<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NotificationService;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_pending_purchase_sales_and_low_stock(): void
    {
        $this->createPendingPurchase();
        $this->createPendingSale();
        $this->createLowStockIngredient();

        $labels = app(NotificationService::class)
            ->forUser($this->user(UserRole::Admin))
            ->pluck('label')
            ->all();

        $this->assertContains('1 pembelian menunggu verifikasi', $labels);
        $this->assertContains('1 pesanan perlu ditindaklanjuti', $labels);
        $this->assertContains('1 bahan stok menipis', $labels);
    }

    public function test_staff_does_not_see_purchase_verification_notification(): void
    {
        $this->createPendingPurchase();

        $labels = app(NotificationService::class)
            ->forUser($this->user(UserRole::Staf))
            ->pluck('label')
            ->all();

        $this->assertNotContains('1 pembelian menunggu verifikasi', $labels);
    }

    public function test_no_notification_when_everything_is_handled(): void
    {
        $items = app(NotificationService::class)
            ->forUser($this->user(UserRole::Admin));

        $this->assertTrue($items->isEmpty());
    }

    public function test_navbar_shows_badge_with_total_count(): void
    {
        $this->createPendingPurchase();

        $this->actingAs($this->user(UserRole::Admin))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('1 item');
    }

    private function user(UserRole $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function createPendingPurchase(): void
    {
        DB::table('purchases')->insert([
            'purchase_code' => 'PUR-NOTIF-0001',
            'purchase_date' => now()->toDateString(),
            'status' => 'pending',
            'total' => 10000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPendingSale(): void
    {
        DB::table('sales')->insert([
            'transaction_code' => 'TRX-NOTIF-0001',
            'type' => 'menu',
            'status' => 'pending',
            'payment_method' => 'cash',
            'total' => 10000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createLowStockIngredient(): void
    {
        DB::table('ingredients')->insert([
            'name' => 'Bahan Menipis',
            'base_unit' => 'gram',
            'current_stock' => 5,
            'minimum_stock' => 10,
            'average_cost' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
