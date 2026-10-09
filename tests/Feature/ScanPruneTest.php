<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\CardLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanPruneTest extends TestCase
{
    use RefreshDatabase;

    private function log(string $card, string $action, int $daysAgo): void
    {
        CardLog::forceCreate(['card_id' => $card, 'action' => $action, 'created_at' => now()->subDays($daysAgo)]);
    }

    public function test_prune_keeps_every_card_and_total_scans(): void
    {
        Card::forceCreate(['id' => 'PVAAAA01', 'status' => 'active', 'printed_at' => now(),
            'google_url' => 'https://search.google.com/local/writereview?placeid=a']);
        Card::forceCreate(['id' => 'PVBBBB01', 'status' => 'active', 'google_url' => 'https://example.com/b']);
        Card::create(['id' => 'PVCCCC01']);

        foreach ([200, 190, 185] as $d) $this->log('PVAAAA01', 'scan', $d);
        foreach ([10, 1] as $d)        $this->log('PVAAAA01', 'scan', $d);
        $this->log('PVAAAA01', 'activated', 300);
        $this->log('PVBBBB01', 'scan', 250);

        $total = fn (string $id) => Card::withCount(['logs as scan_count' => fn ($q) => $q->where('action', 'scan')])->find($id)->total_scans;
        $this->assertSame(5, $total('PVAAAA01'));

        $this->artisan('scans:prune')->assertSuccessful();
        $this->artisan('scans:prune')->assertSuccessful();

        $this->assertSame(['PVAAAA01', 'PVBBBB01', 'PVCCCC01'], Card::orderBy('id')->pluck('id')->all());
        $this->assertSame(5, $total('PVAAAA01'));
        $this->assertSame(1, $total('PVBBBB01'));
        $this->assertSame(3, Card::find('PVAAAA01')->archived_scans);

        $this->assertSame(2, CardLog::where('card_id', 'PVAAAA01')->where('action', 'scan')->count());
        $this->assertSame(1, CardLog::where('action', 'activated')->count());

        $this->get('/c/PVAAAA01')->assertRedirect('https://search.google.com/local/writereview?placeid=a');
    }

    public function test_range_selector_and_totals_on_dashboard_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Card::forceCreate(['id' => 'PVAAAA01', 'status' => 'active', 'owner_name' => 'Kopi A', 'archived_scans' => 40]);
        $this->log('PVAAAA01', 'scan', 0);
        $this->log('PVAAAA01', 'scan', 20);

        $this->actingAs($admin);
        $this->get('/dashboard?range=7')->assertOk()->assertSee('Scan 7 hari')->assertSee('42');
        $this->get('/dashboard?range=30')->assertOk()->assertSee('Scan 30 hari');
        $this->get('/dashboard?range=180')->assertOk()->assertSee('Rentang terpanjang yang disimpan');
        $this->get('/dashboard?range=999')->assertOk()->assertSee('Scan 7 hari');
        $this->get('/dashboard/stats?range=90&sort=total&dir=asc')->assertOk()->assertSee('Scan 3 bulan')->assertSee('42');
        $this->get('/dashboard/stats?sort=evil;drop&dir=sideways')->assertOk();
        $this->get('/dashboard/cards')->assertOk()->assertSee('42');
        $this->get('/dashboard/cards/PVAAAA01')->assertOk()->assertSee('42');
    }
}
