<?php

use App\Models\Card;
use App\Models\CardLog;
use App\Support\ScanRange;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Dijalankan sebulan sekali lewat Cron Jobs cPanel. Hanya menghapus baris riwayat scan
// yang lebih tua dari ScanRange::KEEP_DAYS; card tidak pernah dihapus, jumlah scannya
// dipindah ke cards.archived_scans supaya total scan tidak berkurang.
Artisan::command('scans:prune', function () {
    $cutoff = now()->subDays(ScanRange::KEEP_DAYS)->startOfDay();
    $old = fn () => CardLog::where('action', 'scan')->where('created_at', '<', $cutoff);

    $perCard = $old()->selectRaw('card_id, COUNT(*) as n')->groupBy('card_id')->pluck('n', 'card_id');
    if ($perCard->isEmpty()) {
        $this->info('Tidak ada riwayat scan yang lebih tua dari ' . $cutoff->toDateString() . '.');
        return;
    }

    DB::transaction(function () use ($perCard, $old) {
        foreach ($perCard as $cardId => $n) {
            Card::whereKey($cardId)->increment('archived_scans', $n);
        }
        $old()->delete();
    });

    $this->info("{$perCard->sum()} riwayat scan dari {$perCard->count()} card dibersihkan (sebelum {$cutoff->toDateString()}). Total scan tidak berubah.");
})->purpose('Bersihkan detail riwayat scan yang lebih tua dari 6 bulan');

// Lengkapi koordinat semua card sekaligus (biasanya cukup sekali setelah fitur peta dipasang).
Artisan::command('cards:locate', function () {
    $cards = Card::whereIn('status', ['active', 'disabled'])->whereNotNull('place_id')->whereNull('lat')->get();
    $ok = $cards->filter(fn (Card $c) => App\Support\PlaceLocator::locate($c))->count();
    $this->info("{$ok} dari {$cards->count()} card mendapat koordinat.");
})->purpose('Ambil koordinat peta untuk card yang belum punya');
