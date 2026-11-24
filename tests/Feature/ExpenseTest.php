<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ExpenseCategory::create(['name' => 'Listrik/Air', 'is_active' => true]);
        ExpenseCategory::create(['name' => 'Sewa', 'is_active' => true]);
        ExpenseCategory::create(['name' => 'Gaji', 'is_active' => true]);
    }

    private function actingUser(UserRole $role = UserRole::Admin): User
    {
        $user = User::factory()->state(['role' => $role])->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_admin_can_record_expense_with_precise_amount(): void
    {
        $user = $this->actingUser();
        $category = ExpenseCategory::firstOrFail();

        $this->post(route('expenses.store'), [
            'expense_category_id' => $category->id,
            'expense_date' => now()->toDateString(),
            'amount' => '123456.78',
            'description' => 'Tagihan listrik',
        ])->assertSessionHas('success');

        $expense = Expense::firstOrFail();
        $this->assertSame('123456.78', (string) $expense->amount);
        $this->assertSame($user->id, $expense->created_by);
        $this->assertSame('Tagihan listrik', $expense->description);
    }

    public function test_pengelola_and_staf_cannot_access_expenses(): void
    {
        foreach ([UserRole::Pengelola, UserRole::Staf] as $role) {
            $this->actingUser($role);
            $this->get(route('expenses.index'))->assertForbidden();
            $this->post(route('expenses.store'), [])->assertForbidden();
        }
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $this->actingUser();
        $category = ExpenseCategory::firstOrFail();
        Expense::create([
            'expense_category_id' => $category->id,
            'expense_date' => now()->toDateString(),
            'amount' => 10000,
            'created_by' => User::first()->id,
        ]);

        $this->delete(route('expense-categories.destroy', $category))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('expense_categories', ['id' => $category->id]);
    }

    public function test_category_not_in_use_can_be_deactivated(): void
    {
        $this->actingUser();
        $category = ExpenseCategory::create(['name' => 'Kategori Uji', 'is_active' => true]);

        $this->put(route('expense-categories.update', $category), [
            'name' => $category->name,
            'is_active' => 0,
        ])->assertSessionHas('success');

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_period_filter_harian_bulanan_tahunan_and_date_range(): void
    {
        $this->actingUser();
        $category = ExpenseCategory::firstOrFail();
        $user = User::first();

        Expense::create(['expense_category_id' => $category->id, 'expense_date' => now()->toDateString(), 'amount' => 1000, 'created_by' => $user->id]);
        Expense::create(['expense_category_id' => $category->id, 'expense_date' => now()->subMonth()->toDateString(), 'amount' => 2000, 'created_by' => $user->id]);
        Expense::create(['expense_category_id' => $category->id, 'expense_date' => now()->subYear()->toDateString(), 'amount' => 3000, 'created_by' => $user->id]);

        $this->get(route('expenses.index', ['period' => 'harian']))->assertOk()->assertSee('1.000');
        $this->get(route('expenses.index', ['period' => 'bulanan']))->assertOk()->assertSee('1.000');
        $this->get(route('expenses.index', ['period' => 'tahunan']))->assertOk()->assertSee('3.000');

        $this->get(route('expenses.index', [
            'from' => now()->subDays(2)->toDateString(),
            'to' => now()->addDay()->toDateString(),
        ]))->assertOk()->assertSee('1.000')->assertDontSee('2.000');
    }

    public function test_empty_state_when_no_expenses(): void
    {
        $this->actingUser();
        Expense::query()->delete();

        $this->get(route('expenses.index'))
            ->assertOk()
            ->assertSee('Belum ada biaya');
    }
}
