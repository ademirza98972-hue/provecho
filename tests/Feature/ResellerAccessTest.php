<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\CardLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResellerAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $budi;
    private User $sari;
    private Card $budiCard;
    private Card $sariCard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->budi  = User::factory()->create(['role' => 'reseller', 'name' => 'Budi']);
        $this->sari  = User::factory()->create(['role' => 'reseller', 'name' => 'Sari']);

        $this->budiCard = Card::forceCreate(['id' => 'PVBUDI01', 'reseller_id' => $this->budi->id]);
        $this->sariCard = Card::forceCreate(['id' => 'PVSARI01', 'reseller_id' => $this->sari->id]);
        Card::create(['id' => 'PVSTOK01']);

        CardLog::create(['card_id' => 'PVSARI01', 'action' => 'scan']);
    }

    public function test_reseller_only_sees_own_cards_and_logs(): void
    {
        $this->actingAs($this->budi);

        $this->get('/dashboard/cards')->assertOk()->assertSee('PVBUDI01')->assertDontSee('PVSARI01')->assertDontSee('PVSTOK01');
        $this->get('/dashboard/activity')->assertOk()->assertDontSee('PVSARI01');
        $this->get('/dashboard')->assertOk();
        $this->get('/dashboard/stats')->assertOk();
    }

    public function test_reseller_cannot_open_or_change_someone_elses_card(): void
    {
        $this->actingAs($this->budi);

        $this->get('/dashboard/cards/PVSARI01')->assertForbidden();
        $this->get('/dashboard/cards/PVSTOK01')->assertForbidden();
        $this->post('/dashboard/cards/PVSARI01/activate', [
            'owner_name' => 'Curang', 'google_url' => 'https://example.com',
        ])->assertForbidden();

        $this->assertSame('inactive', $this->sariCard->fresh()->status);
    }

    public function test_reseller_can_activate_own_card(): void
    {
        $this->actingAs($this->budi)->post('/dashboard/cards/PVBUDI01/activate', [
            'owner_name' => 'Kopi Budi', 'google_url' => 'https://search.google.com/local/writereview?placeid=x',
        ])->assertRedirect('/dashboard/cards/PVBUDI01');

        $this->assertSame('active', $this->budiCard->fresh()->status);
    }

    public function test_reseller_is_blocked_from_admin_actions(): void
    {
        $this->actingAs($this->budi);

        $this->post('/dashboard/cards/generate', ['count' => 5])->assertForbidden();
        $this->post('/dashboard/cards/export/pdf', ['ids' => ['PVBUDI01']])->assertForbidden();
        $this->post('/dashboard/cards/bulk-delete', ['ids' => ['PVBUDI01']])->assertForbidden();
        $this->post('/dashboard/cards/assign', ['ids' => ['PVSARI01'], 'reseller' => $this->budi->id])->assertForbidden();
        $this->post('/dashboard/cards/PVBUDI01/disable')->assertForbidden();
        $this->delete('/dashboard/cards/PVBUDI01')->assertForbidden();
        $this->get('/dashboard/resellers')->assertForbidden();
        $this->get('/dashboard/export/activity')->assertForbidden();

        $this->assertSame(3, Card::count());
        $this->assertSame($this->sari->id, $this->sariCard->fresh()->reseller_id);
    }

    public function test_admin_assigns_cards_and_deleting_reseller_returns_them_to_stock(): void
    {
        $this->actingAs($this->admin);

        $this->post('/dashboard/cards/assign', ['ids' => ['PVSTOK01'], 'reseller' => $this->budi->id])->assertRedirect();
        $this->assertSame($this->budi->id, Card::find('PVSTOK01')->reseller_id);

        $this->post('/dashboard/cards/assign', ['ids' => ['PVSTOK01'], 'reseller' => $this->admin->id])->assertNotFound();

        $this->delete("/dashboard/resellers/{$this->budi->id}")->assertRedirect();
        $this->assertNull($this->budiCard->fresh()->reseller_id);
        $this->assertNull(Card::find('PVSTOK01')->reseller_id);

        $this->delete("/dashboard/resellers/{$this->admin->id}")->assertNotFound();
    }

    public function test_admin_moves_pasted_ids_and_scanned_links_to_reseller(): void
    {
        Card::create(['id' => 'PVSTOK02']);

        $this->actingAs($this->admin)->post('/dashboard/resellers/assign-ids', [
            'reseller_id' => $this->budi->id,
            'ids_text'    => "pvstok01\n https://provecho.my.id/c/PVSTOK02?utm=x , PVBUDI01",
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame($this->budi->id, Card::find('PVSTOK01')->reseller_id);
        $this->assertSame($this->budi->id, Card::find('PVSTOK02')->reseller_id);
    }

    public function test_pasted_ids_are_all_or_nothing(): void
    {
        Card::forceCreate(['id' => 'PVJUAL01', 'status' => 'active']);

        $this->actingAs($this->admin)->post('/dashboard/resellers/assign-ids', [
            'reseller_id' => $this->budi->id,
            'ids_text'    => "PVSTOK01\nPVSARI01\nPVNOPE99\nPVJUAL01",
        ])->assertRedirect()->assertSessionHasErrors();

        $errors = session('errors')->all();
        $this->assertCount(4, $errors);
        $this->assertStringContainsString('PVSARI01: sudah milik Sari', implode(' ', $errors));
        $this->assertNull(Card::find('PVSTOK01')->reseller_id);
        $this->assertNull(Card::find('PVJUAL01')->reseller_id);

        $this->actingAs($this->budi)->post('/dashboard/resellers/assign-ids', [
            'reseller_id' => $this->budi->id, 'ids_text' => 'PVSTOK01',
        ])->assertForbidden();
    }

    public function test_admin_creates_reseller_who_can_log_in(): void
    {
        $this->actingAs($this->admin)->post('/dashboard/resellers', [
            'name' => 'Andi', 'username' => 'andi', 'password' => 'rahasia123',
        ])->assertRedirect();

        $andi = User::where('username', 'andi')->first();
        $this->assertSame('reseller', $andi->role);

        auth()->logout();
        $this->post('/login', ['username' => 'andi', 'password' => 'rahasia123'])->assertRedirect('/dashboard');
        $this->assertFalse(auth()->user()->isAdmin());
    }
}
