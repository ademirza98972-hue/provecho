<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_map_fills_coordinates_and_lists_cards_without_location(): void
    {
        Http::fake([
            'places.googleapis.com/v1/places/ChIJkopi*' => Http::response(['location' => ['latitude' => -3.4442, 'longitude' => 114.8411]]),
            'places.googleapis.com/*'                   => Http::response(['error' => 'quota'], 429),
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        Card::forceCreate(['id' => 'PVKOPI01', 'status' => 'active', 'owner_name' => 'Kopi <script>x</script>', 'place_id' => 'ChIJkopi123',
            'google_url' => 'https://search.google.com/local/writereview?placeid=ChIJkopi123']);
        Card::forceCreate(['id' => 'PVGAGAL1', 'status' => 'active', 'owner_name' => 'Google Lagi Error', 'place_id' => 'ChIJgagal']);
        Card::forceCreate(['id' => 'PVMANUAL', 'status' => 'active', 'owner_name' => 'Link Manual', 'google_url' => 'https://instagram.com/x']);
        Card::create(['id' => 'PVSTOK01']);

        $res = $this->actingAs($admin)->get('/dashboard/peta')->assertOk();

        $this->assertEqualsWithDelta(-3.4442, Card::find('PVKOPI01')->lat, 0.0001);
        $this->assertNull(Card::find('PVGAGAL1')->lat);
        $res->assertSee('PVKOPI01')->assertSee('Belum ada di peta · 2')->assertSee('Link Manual')
            ->assertDontSee('<script>x</script>', false);

        $this->get('/c/PVKOPI01')->assertRedirect('https://search.google.com/local/writereview?placeid=ChIJkopi123');
    }

    public function test_map_is_admin_only_and_changing_location_clears_old_coordinates(): void
    {
        $budi = User::factory()->create(['role' => 'reseller']);
        $this->actingAs($budi)->get('/dashboard/peta')->assertForbidden();

        $card = Card::forceCreate(['id' => 'PVKOPI01', 'status' => 'active', 'place_id' => 'ChIJlama', 'lat' => -3.4, 'lng' => 114.8]);
        $card->update(['owner_name' => 'Ganti nama saja']);
        $this->assertNotNull($card->fresh()->lat);

        $card->update(['place_id' => 'ChIJbaru']);
        $this->assertNull($card->fresh()->lat);
        $this->assertNull($card->fresh()->lng);
    }

    public function test_locate_command_backfills_all_cards(): void
    {
        Http::fake(['places.googleapis.com/*' => Http::response(['location' => ['latitude' => -3.3, 'longitude' => 114.6]])]);
        Card::forceCreate(['id' => 'PVA00001', 'status' => 'active', 'place_id' => 'ChIJa']);
        Card::forceCreate(['id' => 'PVB00001', 'status' => 'disabled', 'place_id' => 'ChIJb']);

        $this->artisan('cards:locate')->expectsOutput('2 dari 2 card mendapat koordinat.')->assertSuccessful();
        $this->assertSame(0, Card::whereNull('lat')->count());
    }
}
