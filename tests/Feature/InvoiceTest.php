<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creates_invoice_and_buyer_gets_pdf_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $budi = User::factory()->create(['role' => 'reseller']);
        Card::forceCreate(['id' => 'PVBUDI01', 'reseller_id' => $budi->id]);
        Card::forceCreate(['id' => 'PVBUDI02']);

        $this->actingAs($admin)->post('/dashboard/nota/buat', ['ids' => ['PVBUDI01', 'PVBUDI02']])
            ->assertOk()->assertSee('PVBUDI01\nPVBUDI02', false);

        $this->post('/dashboard/nota', [
            'card_ids' => "pvbudi01, PVBUDI02\nPVBUDI01", 'buyer_name' => 'Andi', 'buyer_phone' => '0812 3456 7890',
            'payment_method' => 'tunai', 'price' => 75000, 'discount' => 10000,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $invoice = Invoice::with('items')->sole();
        $this->assertSame('PV-' . now()->format('Ymd') . '-001', $invoice->number);
        $this->assertSame(140000, $invoice->total);
        $this->assertSame($invoice->number, Card::find('PVBUDI02')->order_number);
        $this->assertSame('Andi', Card::find('PVBUDI01')->buyer_name);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $invoice->whatsapp_url);

        // Preview langsung di halaman, unduhan lewat link bertanda tangan (tanpa butuh sesi login).
        $this->get("/dashboard/nota/{$invoice->id}")->assertOk()->assertSee('Rp140.000')->assertSee('Card Google Review')
            ->assertSee(e($invoice->download_url), false);
        $this->get('/dashboard/nota?search=Andi')->assertOk()->assertSee($invoice->number);
        $this->get('/dashboard/cards/PVBUDI01')->assertOk()->assertSee("/dashboard/nota/{$invoice->id}", false);

        auth()->logout();
        $this->get($invoice->public_url)->assertOk()->assertSee('Rp140.000')->assertSee('Unduh PDF')
            ->assertSee(\App\Models\Invoice::CONTACT_PHONE);
        $this->get($invoice->download_url)->assertOk()->assertHeader('content-type', 'application/pdf')
            ->assertDownload($invoice->number . '.pdf');
        $this->get("/nota/{$invoice->id}")->assertForbidden();
        $this->get("/nota/{$invoice->id}/unduh")->assertForbidden();
    }

    public function test_editing_invoice_moves_buyer_data_between_cards(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        foreach (['PVAAAA01', 'PVAAAA02', 'PVAAAA03'] as $id) {
            Card::forceCreate(['id' => $id]);
        }
        $form = ['buyer_name' => 'Andi', 'buyer_phone' => '081234567890', 'payment_method' => 'tunai', 'price' => 50000];

        $this->actingAs($admin)->post('/dashboard/nota', $form + ['card_ids' => 'PVAAAA01 PVAAAA02']);
        $invoice = Invoice::sole();

        $this->get("/dashboard/nota/{$invoice->id}/ubah")->assertOk()->assertSee('Simpan perubahan')->assertSee('PVAAAA02');
        $this->put("/dashboard/nota/{$invoice->id}", ['buyer_name' => 'Andika', 'payment_method' => 'qris', 'price' => 60000, 'discount' => 5000,
            'card_ids' => 'PVAAAA02 PVAAAA03'])->assertSessionHasNoErrors()->assertRedirect("/dashboard/nota/{$invoice->id}");

        $invoice->refresh()->load('items');
        $this->assertSame(115000, $invoice->total);
        $this->assertSame('qris', $invoice->payment_method);
        $this->assertSame(['PVAAAA02', 'PVAAAA03'], $invoice->items->pluck('card_id')->sort()->values()->all());
        $this->assertNull(Card::find('PVAAAA01')->order_number);
        $this->assertNull(Card::find('PVAAAA01')->buyer_name);
        $this->assertSame('Andika', Card::find('PVAAAA03')->buyer_name);
        $this->assertSame($invoice->number, Card::find('PVAAAA03')->order_number);
        $this->assertSame(1, Invoice::count());
    }

    public function test_nota_is_admin_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $budi = User::factory()->create(['role' => 'reseller']);
        Card::forceCreate(['id' => 'PVBUDI01', 'reseller_id' => $budi->id, 'order_number' => 'PV-X']);
        $invoice = Invoice::create(['number' => 'PV-X', 'user_id' => $admin->id, 'buyer_name' => 'R', 'payment_method' => 'tunai']);

        $this->actingAs($budi);
        $this->get('/dashboard/nota')->assertForbidden();
        $this->get('/dashboard/nota/buat')->assertForbidden();
        $this->post('/dashboard/nota', ['card_ids' => 'PVBUDI01', 'buyer_name' => 'A', 'payment_method' => 'tunai', 'price' => 1])->assertForbidden();
        $this->get("/dashboard/nota/{$invoice->id}")->assertForbidden();
        $this->get("/dashboard/nota/{$invoice->id}/ubah")->assertForbidden();
        $this->put("/dashboard/nota/{$invoice->id}", ['buyer_name' => 'Diambil'])->assertForbidden();
        $this->assertSame(1, Invoice::count());

        // Menu dan link nota tidak tampil untuk reseller.
        $this->get('/dashboard/cards/PVBUDI01')->assertOk()->assertDontSee('/dashboard/nota', false);
    }

    public function test_discount_cannot_exceed_subtotal(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Card::forceCreate(['id' => 'PVAAAA01']);

        $this->actingAs($admin)->post('/dashboard/nota', [
            'card_ids' => 'PVAAAA01', 'buyer_name' => 'Andi', 'payment_method' => 'qris', 'price' => 5000, 'discount' => 6000,
        ])->assertSessionHasErrors('discount');
    }
}
