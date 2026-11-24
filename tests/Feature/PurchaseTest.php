<?php

namespace Tests\Feature;

use App\Enums\PurchaseStatus;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\User;
use App\Services\PurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_purchase_with_items_and_receipt(): void
    {
        Storage::fake('local');
        $staff = User::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'name' => 'Daging Ayam',
            'base_unit' => 'kg',
            'current_stock' => '5.000',
            'average_cost' => '10000.00',
        ]);

        $this->actingAs($staff)->post(route('purchases.store'), [
            'purchase_date' => '2026-11-24',
            'supplier_name' => 'Toko Berkah',
            'items' => [[
                'ingredient_id' => $ingredient->id,
                'quantity' => '2.000',
                'unit' => 'kg',
                'unit_cost' => '15000.00',
            ]],
            'receipt' => UploadedFile::fake()->image('nota.jpg', 120, 120),
        ])->assertRedirect(route('dashboard'));

        $purchase = Purchase::query()->firstOrFail();

        $this->assertSame(PurchaseStatus::Pending->value, $purchase->status->value);
        $this->assertSame('30000.00', $purchase->total);
        $this->assertSame($staff->id, $purchase->created_by);
        $this->assertNotNull($purchase->receipt_path);
        Storage::disk('local')->assertExists($purchase->receipt_path);
        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => '2.000',
            'subtotal' => '30000.00',
        ]);
        $this->assertSame('5.000', $ingredient->fresh()->current_stock);
    }

    public function test_rekap_and_receipt_are_for_admin_and_pengelola_only(): void
    {
        Storage::fake('local');
        $purchase = Purchase::factory()->create(['receipt_path' => 'purchases/receipts/nota.jpg']);
        Storage::disk('local')->put($purchase->receipt_path, 'dummy');

        foreach ([User::factory()->admin()->create(), User::factory()->pengelola()->create()] as $user) {
            $this->actingAs($user)
                ->get(route('purchases.index'))
                ->assertOk()
                ->assertSee($purchase->purchase_code);
            $this->actingAs($user)->get(route('purchases.show', $purchase))->assertOk();
            $this->actingAs($user)->get(route('purchases.receipt', $purchase))->assertOk();
        }

        $staf = User::factory()->create();

        $this->actingAs($staf)->get(route('purchases.index'))->assertForbidden();
        $this->actingAs($staf)->get(route('purchases.show', $purchase))->assertForbidden();
        $this->actingAs($staf)->get(route('purchases.receipt', $purchase))->assertForbidden();
    }

    public function test_approval_adds_stock_ledger_and_updates_average_cost(): void
    {
        $admin = User::factory()->admin()->create();
        $pengelola = User::factory()->pengelola()->create();
        $ingredient = Ingredient::factory()->create([
            'base_unit' => 'kg',
            'current_stock' => '10.000',
            'average_cost' => '10000.00',
        ]);
        $purchase = app(PurchaseService::class)->create([
            'purchase_date' => '2026-11-24',
            'items' => [[
                'ingredient_id' => $ingredient->id,
                'quantity' => '2.000',
                'unit' => 'kg',
                'unit_cost' => '15000.00',
            ]],
        ], $admin);

        $this->actingAs($pengelola)
            ->post(route('purchases.verify', $purchase), ['decision' => 'approved'])
            ->assertRedirect(route('purchases.index', [
                'modal' => 'detail-purchase-modal',
                'id' => $purchase->id,
            ]));

        $purchase->refresh();
        $ingredient->refresh();

        $this->assertSame(PurchaseStatus::Approved->value, $purchase->status->value);
        $this->assertSame($pengelola->id, (int) $purchase->verified_by);
        $this->assertNotNull($purchase->verified_at);
        $this->assertSame('12.000', $ingredient->current_stock);
        $this->assertSame('10833.33', $ingredient->average_cost);
        $this->assertDatabaseHas('stock_movements', [
            'ingredient_id' => $ingredient->id,
            'reference_type' => 'Purchase',
            'reference_id' => $purchase->id,
            'movement_type' => 'purchase_in',
            'quantity_in' => '2.000',
            'balance_before' => '10.000',
            'balance_after' => '12.000',
        ]);
    }

    public function test_rejection_requires_reason_and_does_not_change_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create([
            'base_unit' => 'kg',
            'current_stock' => '10.000',
        ]);
        $purchase = app(PurchaseService::class)->create([
            'purchase_date' => '2026-11-24',
            'items' => [[
                'ingredient_id' => $ingredient->id,
                'quantity' => '2.000',
                'unit' => 'kg',
                'unit_cost' => '15000.00',
            ]],
        ], $admin);

        $this->actingAs($admin)
            ->post(route('purchases.verify', $purchase), ['decision' => 'rejected'])
            ->assertSessionHasErrors('rejection_note');
        $this->assertSame(PurchaseStatus::Pending->value, $purchase->fresh()->status->value);

        $this->actingAs($admin)
            ->post(route('purchases.verify', $purchase), [
                'decision' => 'rejected',
                'rejection_note' => 'Harga tidak sesuai nota.',
            ])->assertRedirect();

        $purchase->refresh();

        $this->assertSame(PurchaseStatus::Rejected->value, $purchase->status->value);
        $this->assertSame('Harga tidak sesuai nota.', $purchase->rejection_note);
        $this->assertSame('10.000', $ingredient->fresh()->current_stock);
        $this->assertSame(0, $ingredient->stockMovements()->where('reference_type', 'Purchase')->count());
    }

    public function test_second_verification_cannot_add_stock_twice(): void
    {
        $admin = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create([
            'base_unit' => 'kg',
            'current_stock' => '10.000',
        ]);
        $purchase = app(PurchaseService::class)->create([
            'purchase_date' => '2026-11-24',
            'items' => [[
                'ingredient_id' => $ingredient->id,
                'quantity' => '2.000',
                'unit' => 'kg',
                'unit_cost' => '15000.00',
            ]],
        ], $admin);

        $this->actingAs($admin)->post(route('purchases.verify', $purchase), ['decision' => 'approved'])->assertRedirect();
        $this->actingAs($admin)->post(route('purchases.verify', $purchase), ['decision' => 'approved'])->assertSessionHas('error');

        $this->assertSame(PurchaseStatus::Approved->value, $purchase->fresh()->status->value);
        $this->assertSame('12.000', $ingredient->fresh()->current_stock);
        $this->assertSame(1, $ingredient->stockMovements()->where('reference_type', 'Purchase')->count());
    }

    public function test_staff_cannot_verify_and_pengelola_cannot_input_purchase(): void
    {
        $admin = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create(['base_unit' => 'kg']);
        $purchase = app(PurchaseService::class)->create([
            'purchase_date' => '2026-11-24',
            'items' => [[
                'ingredient_id' => $ingredient->id,
                'quantity' => '1.000',
                'unit' => 'kg',
                'unit_cost' => '10000.00',
            ]],
        ], $admin);

        $payload = [
            'purchase_date' => '2026-11-24',
            'items' => [[
                'ingredient_id' => $ingredient->id,
                'quantity' => '1.000',
                'unit' => 'kg',
                'unit_cost' => '10000.00',
            ]],
        ];

        $this->actingAs(User::factory()->create())
            ->post(route('purchases.verify', $purchase), ['decision' => 'approved'])
            ->assertForbidden();
        $this->actingAs(User::factory()->pengelola()->create())
            ->post(route('purchases.store'), $payload)
            ->assertForbidden();

        $this->assertSame(PurchaseStatus::Pending->value, $purchase->fresh()->status->value);
        $this->assertSame(1, Purchase::query()->count());
    }

    public function test_purchase_codes_are_unique_and_sequential(): void
    {
        $admin = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create(['base_unit' => 'kg']);
        $data = [
            'purchase_date' => '2026-11-24',
            'items' => [[
                'ingredient_id' => $ingredient->id,
                'quantity' => '1.000',
                'unit' => 'kg',
                'unit_cost' => '10000.00',
            ]],
        ];

        $first = app(PurchaseService::class)->create($data, $admin);
        $second = app(PurchaseService::class)->create($data, $admin);
        $prefix = 'PUR-'.now()->timezone('Asia/Jakarta')->format('Ymd').'-';

        $this->assertSame($prefix.'0001', $first->purchase_code);
        $this->assertSame($prefix.'0002', $second->purchase_code);
    }
}
