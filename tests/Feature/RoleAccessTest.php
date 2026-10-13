<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $sales;
    private User $other;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->sales = User::factory()->create(['role' => 'reseller']);
        $this->other = User::factory()->create(['role' => 'reseller']);

        Card::forceCreate(['id' => 'PVSALE01', 'reseller_id' => $this->sales->id, 'status' => 'active', 'owner_name' => 'Kopi Sales', 'place_id' => 'p1', 'lat' => -3.3, 'lng' => 114.6]);
        Card::forceCreate(['id' => 'PVSALE02', 'reseller_id' => $this->sales->id]);
        Card::forceCreate(['id' => 'PVOTHR01', 'reseller_id' => $this->other->id, 'status' => 'active', 'owner_name' => 'Toko Lain', 'place_id' => 'p2', 'lat' => -3.4, 'lng' => 114.7]);
    }

    public function test_admin_creates_role_and_user_gets_exactly_those_features(): void
    {
        $this->actingAs($this->admin)->post('/dashboard/roles', ['name' => 'Sales', 'permissions' => ['invoices', 'map']])
            ->assertSessionHasNoErrors();
        $role = Role::where('name', 'Sales')->sole();
        $this->put("/dashboard/resellers/{$this->sales->id}/role", ['role_id' => $role->id])->assertSessionHasNoErrors();
        $this->get('/dashboard/resellers')->assertOk()->assertSee('Sales')->assertDontSee('Fitur tambahan yang boleh diakses');
        $this->get('/dashboard/roles')->assertOk()->assertSee('Sales')->assertSee('Fitur tambahan yang boleh diakses');

        $this->actingAs($this->sales->fresh());
        $this->get('/dashboard/cards')->assertOk()->assertSee('/dashboard/nota', false)->assertSee('Buat nota')->assertDontSee('Cetak QR');
        $this->get('/dashboard')->assertOk()->assertSee('Sales');

        // Nota: hanya dari card miliknya.
        $this->post('/dashboard/nota', ['card_ids' => 'PVOTHR01', 'buyer_name' => 'A', 'payment_method' => 'tunai', 'price' => 1000])
            ->assertSessionHasErrors('card_ids');
        $this->post('/dashboard/nota', ['card_ids' => 'PVSALE01', 'buyer_name' => 'A', 'payment_method' => 'tunai', 'price' => 1000])
            ->assertSessionHasNoErrors();
        $this->assertSame($this->sales->id, Invoice::sole()->user_id);

        // Peta: hanya pelanggannya.
        $this->get('/dashboard/peta')->assertOk()->assertSee('Kopi Sales')->assertDontSee('Toko Lain');

        // Fitur yang tidak dicentang tetap tertutup.
        $this->get('/dashboard/export/cards')->assertForbidden();
        $this->post('/dashboard/cards/PVSALE01/reset')->assertForbidden();
        $this->post('/dashboard/cards/generate', ['count' => 1])->assertForbidden();
        $this->get('/dashboard/resellers')->assertForbidden();
        $this->get('/dashboard/roles')->assertForbidden();

        // Hak akses dicabut: langsung berlaku.
        // (fresh(): di aplikasi asli user dimuat ulang tiap request; di test objeknya dipakai ulang.)
        $role->update(['permissions' => ['map']]);
        $this->actingAs($this->sales->fresh())->get('/dashboard/nota')->assertForbidden();
        $this->get('/dashboard/cards')->assertDontSee('/dashboard/nota', false);
    }

    public function test_admin_edits_existing_role(): void
    {
        $role = Role::where('name', 'Reseller')->sole();

        $this->actingAs($this->admin)->put("/dashboard/roles/{$role->id}", ['name' => 'Reseller', 'permissions' => ['cards-reset']])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame(['cards-reset'], $role->fresh()->permissions);
    }

    public function test_card_permissions_only_touch_own_cards(): void
    {
        $role = Role::create(['name' => 'Staf', 'permissions' => ['cards-reset', 'cards-disable', 'cards-delete', 'cards-print', 'export']]);
        $this->sales->update(['role_id' => $role->id]);
        $this->actingAs($this->sales->fresh());

        $this->get('/dashboard/cards/PVSALE01')->assertOk()->assertSee('Reset card')->assertSee('Nonaktifkan card');
        $this->post('/dashboard/cards/PVOTHR01/reset')->assertForbidden();
        $this->post('/dashboard/cards/PVOTHR01/disable')->assertForbidden();
        $this->assertSame('active', Card::find('PVOTHR01')->status);

        $this->post('/dashboard/cards/PVSALE01/disable')->assertRedirect();
        $this->assertSame('disabled', Card::find('PVSALE01')->status);
        $this->post('/dashboard/cards/PVSALE01/reset')->assertRedirect();
        $this->assertSame('inactive', Card::find('PVSALE01')->status);

        // Hapus massal dan cetak hanya mengenai card miliknya.
        $this->post('/dashboard/cards/bulk-delete', ['ids' => ['PVSALE02', 'PVOTHR01']]);
        $this->assertNull(Card::find('PVSALE02'));
        $this->assertNotNull(Card::find('PVOTHR01'));
        $this->post('/dashboard/cards/export/pdf', ['ids' => ['PVOTHR01']])->assertOk()->assertDontSee('PVOTHR01');

        $csv = $this->get('/dashboard/export/cards')->streamedContent();
        $this->assertStringNotContainsString('PVOTHR01', $csv);
    }

    public function test_only_admin_manages_roles_and_permissions_are_validated(): void
    {
        $role = Role::create(['name' => 'Staf', 'permissions' => []]);
        $this->sales->update(['role_id' => $role->id]);

        $this->actingAs($this->sales);
        $this->post('/dashboard/roles', ['name' => 'Bos', 'permissions' => ['invoices']])->assertForbidden();
        $this->put("/dashboard/roles/{$role->id}", ['name' => 'Staf', 'permissions' => ['invoices']])->assertForbidden();
        $this->put("/dashboard/resellers/{$this->sales->id}/role", ['role_id' => $role->id])->assertForbidden();
        $this->assertSame([], $role->fresh()->permissions);

        $this->actingAs($this->admin);
        $this->post('/dashboard/roles', ['name' => 'Licik', 'permissions' => ['admin']])->assertSessionHasErrors('permissions.0');
        $this->post('/dashboard/roles', ['name' => 'staf'])->assertSessionHasErrors('name');

        // Role yang masih dipakai tidak bisa dihapus; yang kosong bisa.
        $this->delete("/dashboard/roles/{$role->id}")->assertSessionHasErrors('role');
        $this->assertNotNull($role->fresh());
        $empty = Role::create(['name' => 'Kosong', 'permissions' => []]);
        $this->delete("/dashboard/roles/{$empty->id}")->assertSessionHasNoErrors();
        $this->assertNull($empty->fresh());
    }
}
