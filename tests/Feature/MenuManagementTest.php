<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_menu_list(): void
    {
        $admin = User::factory()->admin()->create();
        Menu::factory()->create(['name' => 'Nasi Ayam Geprek', 'selling_price' => 25000]);

        $this->actingAs($admin)
            ->get(route('menus.index'))
            ->assertOk()
            ->assertSee('Katalog Menu')
            ->assertSee('Nasi Ayam Geprek')
            ->assertSee('Rp25.000');
    }

    public function test_pengelola_and_staf_can_view_menu_list(): void
    {
        Menu::factory()->create(['name' => 'Ayam Bakar Madu']);

        foreach ([UserRole::Pengelola, UserRole::Staf] as $role) {
            $actor = User::factory()->state(['role' => $role])->create();

            $this->actingAs($actor)
                ->get(route('menus.index'))
                ->assertOk()
                ->assertSee('Ayam Bakar Madu');
        }
    }

    public function test_admin_can_create_menu_with_selling_price(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('menus.store'), [
                'name' => 'Soto Ayam Kampung',
                'description' => 'Soto ayam dengan kuah bening.',
                'selling_price' => '32000.50',
            ])
            ->assertRedirect(route('menus.index'))
            ->assertSessionHas('success');

        $menu = Menu::where('name', 'Soto Ayam Kampung')->firstOrFail();
        $this->assertSame('32000.50', $menu->selling_price);
        $this->assertNull($menu->image_path);
        $this->assertTrue($menu->is_active);
    }

    public function test_admin_can_upload_menu_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('menus.store'), [
                'name' => 'Rendang Ayam',
                'selling_price' => 28000,
                'image' => UploadedFile::fake()->image('rendang.jpg', 100, 100),
            ])
            ->assertRedirect(route('menus.index'));

        $menu = Menu::where('name', 'Rendang Ayam')->firstOrFail();
        $this->assertNotNull($menu->image_path);
        Storage::disk('public')->assertExists($menu->image_path);
    }

    public function test_menu_creation_validates_input(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('menus.store'), [
                'name' => '',
                'description' => str_repeat('a', 1001),
                'selling_price' => 'bukan-angka',
                'image' => UploadedFile::fake()->create('menu.txt', 10, 'text/plain'),
            ])
            ->assertSessionHasErrors(['name', 'description', 'selling_price', 'image']);

        $this->assertDatabaseCount('menus', 0);
    }

    public function test_selling_price_must_be_non_negative_and_have_at_most_two_decimals(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('menus.store'), ['name' => 'Menu Negatif', 'selling_price' => -1000])
            ->assertSessionHasErrors('selling_price');

        $this->actingAs($admin)
            ->post(route('menus.store'), ['name' => 'Menu Desimal', 'selling_price' => 15000.123])
            ->assertSessionHasErrors('selling_price');

        $this->assertDatabaseCount('menus', 0);
    }

    public function test_admin_can_view_menu_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $menu = Menu::factory()->create(['name' => 'Nasi Goreng Spesial', 'selling_price' => 30000]);

        $this->actingAs($admin)
            ->get(route('menus.show', $menu))
            ->assertOk()
            ->assertSee('Nasi Goreng Spesial')
            ->assertSee('Rp30.000');
    }

    public function test_admin_can_open_create_and_edit_forms(): void
    {
        $admin = User::factory()->admin()->create();
        $menu = Menu::factory()->create(['name' => 'Nasi Goreng Spesial']);

        $this->actingAs($admin)
            ->get(route('menus.create'))
            ->assertOk()
            ->assertSee('Tambah Menu Baru');

        $this->actingAs($admin)
            ->get(route('menus.edit', $menu))
            ->assertOk()
            ->assertSee('Ubah Data Menu')
            ->assertSee('Nasi Goreng Spesial');
    }

    public function test_admin_can_edit_menu_and_replace_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $oldPath = 'menus/foto-lama.jpg';
        Storage::disk('public')->put($oldPath, 'foto lama');

        $menu = Menu::factory()->create([
            'name' => 'Menu Lama',
            'selling_price' => 10000,
            'image_path' => $oldPath,
        ]);

        $this->actingAs($admin)
            ->put(route('menus.update', $menu), [
                'name' => 'Menu Baru',
                'description' => 'Deskripsi diperbarui.',
                'selling_price' => 45000,
                'image' => UploadedFile::fake()->image('foto-baru.png', 100, 100),
            ])
            ->assertRedirect(route('menus.index'));

        $menu->refresh();
        $this->assertSame('Menu Baru', $menu->name);
        $this->assertSame('45000.00', $menu->selling_price);
        $this->assertNotSame($oldPath, $menu->image_path);
        Storage::disk('public')->assertExists($menu->image_path);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_admin_can_deactivate_and_reactivate_menu(): void
    {
        $admin = User::factory()->admin()->create();
        $menu = Menu::factory()->create();

        $this->actingAs($admin)
            ->patch(route('menus.status', $menu), ['is_active' => 1])
            ->assertRedirect(route('menus.index'));
        $this->assertFalse($menu->fresh()->is_active);

        $this->actingAs($admin)
            ->patch(route('menus.status', $menu), ['is_active' => 0])
            ->assertRedirect(route('menus.index'));
        $this->assertTrue($menu->fresh()->is_active);
    }

    public function test_pengelola_and_staf_cannot_mutate_menus(): void
    {
        $menu = Menu::factory()->create();

        foreach ([UserRole::Pengelola, UserRole::Staf] as $role) {
            $actor = User::factory()->state(['role' => $role])->create();

            $this->actingAs($actor)->get(route('menus.create'))->assertForbidden();
            $this->actingAs($actor)->post(route('menus.store'), [])->assertForbidden();
            $this->actingAs($actor)->get(route('menus.edit', $menu))->assertForbidden();
            $this->actingAs($actor)->put(route('menus.update', $menu), [])->assertForbidden();
            $this->actingAs($actor)->patch(route('menus.status', $menu), [])->assertForbidden();
        }
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('menus.index'))->assertRedirect(route('login'));
        $this->get(route('menus.create'))->assertRedirect(route('login'));
    }

    public function test_inactive_admin_cannot_manage_menus(): void
    {
        $admin = User::factory()->admin()->inactive()->create();

        $this->actingAs($admin)
            ->get(route('menus.create'))
            ->assertRedirect(route('login'));
    }

    public function test_editing_menu_does_not_rewrite_transaction_snapshot(): void
    {
        $admin = User::factory()->admin()->create();
        $menu = Menu::factory()->create(['name' => 'Nasi Ayam Geprek', 'selling_price' => 20000]);

        $saleId = DB::table('sales')->insertGetId([
            'transaction_code' => 'TRX-MENU-TEST-0001',
            'type' => 'menu',
            'status' => 'completed',
            'payment_method' => 'cash',
            'total' => 20000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sale_items')->insert([
            'sale_id' => $saleId,
            'menu_id' => $menu->id,
            'menu_name_snapshot' => 'Nasi Ayam Geprek',
            'quantity' => 1,
            'unit_price_snapshot' => 20000,
            'hpp_snapshot' => 8000,
            'subtotal' => 20000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->put(route('menus.update', $menu), [
                'name' => 'Nasi Ayam Geprek Special',
                'selling_price' => 35000,
            ])
            ->assertRedirect(route('menus.index'));

        $this->assertDatabaseHas('sale_items', [
            'menu_id' => $menu->id,
            'menu_name_snapshot' => 'Nasi Ayam Geprek',
            'unit_price_snapshot' => 20000,
            'hpp_snapshot' => 8000,
        ]);
        $this->assertSame('Nasi Ayam Geprek Special', $menu->fresh()->name);
    }

    public function test_menu_index_can_be_filtered_by_search_and_status(): void
    {
        $admin = User::factory()->admin()->create();
        Menu::factory()->create(['name' => 'Nasi Ayam Geprek', 'is_active' => true]);
        Menu::factory()->create(['name' => 'Es Teh Manis', 'is_active' => false]);

        $this->actingAs($admin)
            ->get(route('menus.index', ['search' => 'Geprek']))
            ->assertOk()
            ->assertSee('Nasi Ayam Geprek')
            ->assertDontSee('Es Teh Manis');

        $this->actingAs($admin)
            ->get(route('menus.index', ['status' => 'nonaktif']))
            ->assertOk()
            ->assertSee('Es Teh Manis')
            ->assertDontSee('Nasi Ayam Geprek');
    }

    public function test_menu_index_shows_statistics_from_database(): void
    {
        $admin = User::factory()->admin()->create();
        Menu::factory()->count(2)->create(['is_active' => true]);
        Menu::factory()->create(['is_active' => false]);

        $this->actingAs($admin)
            ->get(route('menus.index'))
            ->assertOk()
            ->assertViewHas('menuStats', fn (array $stats): bool => $stats === [
                'total' => 3,
                'aktif' => 2,
                'nonaktif' => 1,
            ]);
    }

    public function test_modal_fragments_are_rendered_without_full_layout(): void
    {
        $admin = User::factory()->admin()->create();
        $menu = Menu::factory()->create(['name' => 'Ayam Geprek']);

        $this->actingAs($admin)
            ->get(route('menus.create', ['partial' => 1]))
            ->assertOk()
            ->assertViewIs('menus.partials.form')
            ->assertSee('Nama Menu');

        $this->actingAs($admin)
            ->get(route('menus.edit', $menu).'?partial=1')
            ->assertOk()
            ->assertViewIs('menus.partials.form')
            ->assertSee('Ayam Geprek');

        $this->actingAs($admin)
            ->get(route('menus.show', $menu).'?partial=1')
            ->assertOk()
            ->assertViewIs('menus.partials.detail')
            ->assertSee('Harga Jual');
    }

    public function test_create_and_edit_modals_use_distinct_form_and_field_ids(): void
    {
        $admin = User::factory()->admin()->create();
        $menu = Menu::factory()->create(['name' => 'Ayam Geprek']);

        $this->actingAs($admin)
            ->get(route('menus.index'))
            ->assertOk()
            ->assertSee('id="create-menu-form-element"', false)
            ->assertDontSee('id="edit-menu-form-element"', false)
            ->assertSee('form="create-menu-form-element"', false)
            ->assertSee('form="edit-menu-form-element"', false)
            ->assertDontSee('id="menu-form"', false)
            ->assertDontSee('form="menu-form"', false);

        $this->actingAs($admin)
            ->get(route('menus.create', ['partial' => 1]))
            ->assertOk()
            ->assertSee('id="create-menu-form-element"', false)
            ->assertSee('id="create-name"', false)
            ->assertSee('action="'.route('menus.store').'"', false);

        $this->actingAs($admin)
            ->get(route('menus.edit', $menu).'?partial=1')
            ->assertOk()
            ->assertSee('id="edit-menu-form-element"', false)
            ->assertSee('id="edit-name"', false)
            ->assertSee('action="'.route('menus.update', $menu).'"', false)
            ->assertSee('value="PUT"', false);
    }

    public function test_failed_modal_submission_reopens_edit_modal_with_errors(): void
    {
        $admin = User::factory()->admin()->create();
        $menu = Menu::factory()->create(['name' => 'Ayam Bakar']);

        $this->actingAs($admin)
            ->from(route('menus.index'))
            ->put(route('menus.update', $menu), [
                'name' => '',
                'selling_price' => 10000,
                'form_modal' => 'edit-menu-modal',
                'menu_id' => $menu->id,
            ])
            ->assertRedirect(route('menus.index'))
            ->assertSessionHasErrors('name');

        $this->actingAs($admin)
            ->get(route('menus.index'))
            ->assertOk()
            ->assertViewHas('failedMenu', fn ($failed): bool => $failed?->is($menu) === true);
    }

    public function test_modal_hidden_fields_are_not_stored_as_menu_attributes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('menus.store'), [
                'name' => 'Menu Modal',
                'selling_price' => 15000,
                'form_modal' => 'create-menu-modal',
            ])
            ->assertRedirect(route('menus.index'));

        $this->assertDatabaseCount('menus', 1);
        $this->assertDatabaseHas('menus', ['name' => 'Menu Modal']);
    }
}
