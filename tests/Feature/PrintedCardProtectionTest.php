<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\CardLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintedCardProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_printed_cards_cannot_be_deleted_one_by_one_or_in_bulk(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Card::forceCreate(['id' => 'PVCETAK1', 'printed_at' => now()]);
        Card::forceCreate(['id' => 'PVCETAK2', 'printed_at' => now(), 'status' => 'disabled']);
        Card::create(['id' => 'PVBARU01']);
        CardLog::create(['card_id' => 'PVCETAK1', 'action' => 'scan']);

        $this->actingAs($admin)->delete('/dashboard/cards/PVCETAK1')->assertSessionHasErrors('card');
        $this->assertNotNull(Card::find('PVCETAK1'));
        $this->assertSame(1, CardLog::where('card_id', 'PVCETAK1')->count());

        $this->post('/dashboard/cards/bulk-delete', ['ids' => ['PVCETAK1', 'PVCETAK2', 'PVBARU01']])
            ->assertSessionHasErrors('card');
        $this->assertNotNull(Card::find('PVCETAK1'));
        $this->assertNotNull(Card::find('PVCETAK2'));
        $this->assertNull(Card::find('PVBARU01'));
    }

    public function test_printed_card_link_keeps_working(): void
    {
        Card::forceCreate([
            'id' => 'PVCETAK1', 'printed_at' => now(), 'status' => 'active',
            'google_url' => 'https://search.google.com/local/writereview?placeid=abc',
        ]);

        $this->get('/c/PVCETAK1')->assertRedirect('https://search.google.com/local/writereview?placeid=abc');
        $this->get('/c/pvcetak1')->assertRedirect('https://search.google.com/local/writereview?placeid=abc');
    }
}
