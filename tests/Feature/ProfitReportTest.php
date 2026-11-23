<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = ExpenseCategory::create(['name' => 'Listrik/Air', 'is_active' => true]);
        Expense::create([
            'expense_category_id' => $category->id,
            'expense_date' => now()->toDateString(),
            'amount' => 50000,
            'created_by' => null,
        ]);
    }

    private function actingUser(UserRole $role = UserRole::Admin): User
    {
        $user = User::factory()->state(['role' => $role])->create();
        $this->actingAs($user);

        return $user;
    }

    private function completedMenuSale(float $total, float $hppPerPortion, float $qty = 1): Sale
    {
        $sale = Sale::create([
            'transaction_code' => 'TRX-TEST-'.fake()->unique()->numerify('####'),
            'type' => 'menu',
            'status' => SaleStatus::Completed,
            'payment_method' => PaymentMethod::Cash,
            'total' => $total,
            'completed_at' => now(),
        ]);
        SaleItem::create([
            'sale_id' => $sale->id,
            'menu_name_snapshot' => 'Nasi Ayam',
            'quantity' => $qty,
            'unit_price_snapshot' => $total,
            'hpp_snapshot' => $hppPerPortion,
            'subtotal' => $total,
        ]);

        return $sale;
    }

    public function test_admin_can_view_report_and_pengelola_staf_cannot(): void
    {
        $this->actingUser();
        $this->get(route('reports.profit'))->assertOk()->assertSee('Laporan Keuntungan');

        foreach ([UserRole::Pengelola, UserRole::Staf] as $role) {
            $this->actingUser($role);
            $this->get(route('reports.profit'))->assertForbidden();
        }
    }

    public function test_profit_math_uses_hpp_snapshot_and_expenses(): void
    {
        $this->actingUser();
        $this->completedMenuSale(total: 100000, hppPerPortion: 30000, qty: 2);

        $response = $this->get(route('reports.profit'));

        $response->assertOk();
        $response->assertViewHas('revenue', 100000.0);
        $response->assertViewHas('hpp', 60000.0);
        $response->assertViewHas('expenses', 50000.0);
        $response->assertViewHas('grossProfit', 40000.0);
        $response->assertViewHas('netProfit', -10000.0);
    }

    public function test_manual_sale_revenue_counted_but_hpp_marked_unavailable(): void
    {
        $this->actingUser();
        $this->completedMenuSale(total: 100000, hppPerPortion: 30000);
        Sale::create([
            'transaction_code' => 'TRX-MANUAL-TEST',
            'type' => 'manual',
            'status' => SaleStatus::Completed,
            'payment_method' => PaymentMethod::Cash,
            'total' => 25000,
            'completed_at' => now(),
        ]);

        $response = $this->get(route('reports.profit'));

        $response->assertOk()
            ->assertViewHas('manualRevenue', 25000.0)
            ->assertViewHas('revenue', 125000.0)
            ->assertViewHas('hpp', 30000.0)
            ->assertSee('HPP tidak tersedia');
    }

    public function test_pending_and_cancelled_sales_are_excluded(): void
    {
        $this->actingUser();
        $this->completedMenuSale(total: 100000, hppPerPortion: 30000);

        Sale::create([
            'transaction_code' => 'TRX-PENDING-TEST',
            'type' => 'manual',
            'status' => SaleStatus::Pending,
            'payment_method' => PaymentMethod::Qris,
            'total' => 999999,
        ]);
        Sale::create([
            'transaction_code' => 'TRX-CANCELLED-TEST',
            'type' => 'manual',
            'status' => SaleStatus::Cancelled,
            'payment_method' => PaymentMethod::Cash,
            'total' => 888888,
            'completed_at' => now(),
        ]);

        $this->get(route('reports.profit'))
            ->assertOk()
            ->assertViewHas('revenue', 100000.0)
            ->assertViewHas('transactionCount', 1);
    }

    public function test_empty_state_when_no_completed_sales(): void
    {
        $this->actingUser();
        Sale::query()->delete();
        Expense::query()->delete();

        $this->get(route('reports.profit'))
            ->assertOk()
            ->assertSee('Belum ada penjualan')
            ->assertViewHas('netProfit', 0.0);
    }

    public function test_admin_can_export_excel_and_pdf(): void
    {
        $this->actingUser();

        $this->get(route('reports.profit.excel'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->get(route('reports.profit.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_pengelola_cannot_export_report(): void
    {
        $this->actingUser(UserRole::Pengelola);

        $this->get(route('reports.profit.excel'))->assertForbidden();
        $this->get(route('reports.profit.pdf'))->assertForbidden();
    }
}
