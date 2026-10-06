<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\User;
use App\Support\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ResellerBrandTest extends TestCase
{
    use RefreshDatabase;

    public function test_reseller_brand_shows_on_their_card_pages_only(): void
    {
        $budi = User::factory()->create(['role' => 'reseller']);
        Card::forceCreate(['id' => 'PVBUDI01', 'reseller_id' => $budi->id]);
        Card::create(['id' => 'PVSTOK01']);

        $this->actingAs($budi)->put('/dashboard/settings/brand', [
            'brand_name'  => 'Kopi Review Budi',
            'brand_color' => '#e11d48',
            'brand_logo'  => UploadedFile::fake()->image('logo.png', 1200, 600),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $budi->refresh();
        $this->assertSame('#E11D48', $budi->brand_color);
        $this->assertStringStartsWith('data:image/png;base64,', $budi->brand_logo);
        [$w, $h] = getimagesizefromstring(base64_decode(substr($budi->brand_logo, 22)));
        $this->assertSame([320, 160], [$w, $h]);

        auth()->logout();
        $this->get('/c/PVBUDI01')->assertOk()
            ->assertSee('Kopi Review Budi')->assertSee('--accent: #E11D48', false)
            ->assertDontSee('PROVECHO GOOGLE REVIEW');

        $this->get('/c/PVSTOK01')->assertOk()
            ->assertSee(Brand::DEFAULT_NAME)->assertSee('/img/logo.png', false);
    }

    public function test_removing_logo_and_rejecting_bad_input(): void
    {
        $budi = User::factory()->create(['role' => 'reseller']);
        $budi->forceFill(['brand_logo' => 'data:image/png;base64,AAA'])->save();

        $this->actingAs($budi)->put('/dashboard/settings/brand', ['remove_logo' => 1, 'brand_color' => '#0EA5E9'])
            ->assertSessionHasNoErrors();
        $this->assertNull($budi->fresh()->brand_logo);

        $this->put('/dashboard/settings/brand', [
            'brand_color' => 'red; background:url(x)',
            'brand_logo'  => UploadedFile::fake()->create('logo.svg', 5, 'image/svg+xml'),
        ])->assertSessionHasErrors(['brand_color', 'brand_logo']);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->put('/dashboard/settings/brand', ['brand_name' => 'X'])->assertForbidden();
    }

    public function test_button_text_stays_readable_on_light_colors(): void
    {
        $this->assertSame('#111827', Brand::make(null, '#FDE047', null)['on']);
        $this->assertSame('#FFFFFF', Brand::make(null, '#1E3A8A', null)['on']);
        $this->assertSame(Brand::DEFAULT_COLOR, Brand::make(null, 'javascript:x', null)['color']);
    }
}
