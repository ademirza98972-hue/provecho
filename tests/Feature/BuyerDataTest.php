<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_activation_leads_to_buyer_step_and_qr_link_stays_the_same(): void
    {
        $budi = User::factory()->create(['role' => 'reseller']);
        Card::forceCreate(['id' => 'PVBUDI01', 'reseller_id' => $budi->id, 'printed_at' => now()]);
        $google = 'https://search.google.com/local/writereview?placeid=abc';

        $this->actingAs($budi)->post('/dashboard/cards/PVBUDI01/activate', ['owner_name' => 'Kopi Budi', 'google_url' => $google])
            ->assertRedirect('/dashboard/cards/PVBUDI01/buyer');
        $this->followingRedirects()->get('/dashboard/cards/PVBUDI01/buyer')->assertOk();
        $this->withSession(['activated' => true])->get('/dashboard/cards/PVBUDI01/buyer')
            ->assertSee('Hubungkan ke Google')->assertSee('Lewati, isi nanti');

        $this->put('/dashboard/cards/PVBUDI01/buyer', [
            'order_number' => ' 2410098KQ7XJ3P ', 'buyer_name' => 'Andi', 'buyer_phone' => '0812-3456 7890',
        ])->assertRedirect('/dashboard/cards/PVBUDI01')->assertSessionHasNoErrors();

        $card = Card::find('PVBUDI01');
        $this->assertSame('2410098KQ7XJ3P', $card->order_number);
        $this->assertSame('https://wa.me/6281234567890', $card->buyer_whatsapp);

        $this->get('/dashboard/cards?search=2410098KQ')->assertSee('PVBUDI01');
        $this->get('/dashboard/cards/PVBUDI01')->assertSee('Andi')->assertSee('Chat WhatsApp');

        auth()->logout();
        $this->get('/c/PVBUDI01')->assertRedirect($google);
    }

    public function test_buyer_data_is_private_validated_and_cleared_on_reset(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $budi  = User::factory()->create(['role' => 'reseller']);
        $sari  = User::factory()->create(['role' => 'reseller']);
        Card::forceCreate(['id' => 'PVBUDI01', 'reseller_id' => $budi->id, 'status' => 'disabled',
            'order_number' => 'A1', 'buyer_name' => 'Andi', 'buyer_phone' => '0812']);

        $this->actingAs($sari)->get('/dashboard/cards/PVBUDI01/buyer')->assertForbidden();
        $this->put('/dashboard/cards/PVBUDI01/buyer', ['buyer_name' => 'Diambil'])->assertForbidden();
        $this->get('/dashboard/cards?search=Andi')->assertDontSee('PVBUDI01');

        $this->actingAs($budi)->put('/dashboard/cards/PVBUDI01/buyer', ['buyer_phone' => 'telepon saya'])
            ->assertSessionHasErrors('buyer_phone');
        $this->assertSame('Andi', Card::find('PVBUDI01')->buyer_name);

        $this->actingAs($admin)->post('/dashboard/cards/PVBUDI01/reset')->assertRedirect();
        $card = Card::find('PVBUDI01');
        $this->assertNull($card->order_number);
        $this->assertNull($card->buyer_name);
        $this->assertNull($card->buyer_phone);
    }

    public function test_csv_export_neutralises_formulas_in_buyer_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Card::forceCreate(['id' => 'PVAAAA01', 'status' => 'active', 'buyer_name' => '=HYPERLINK("x")', 'buyer_phone' => '+62812']);

        $csv = $this->actingAs($admin)->get('/dashboard/export/cards')->streamedContent();

        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringContainsString("'+62812", $csv);
    }
}
