<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanRange
{
    /** Detail scan disimpan selama ini; rentang grafik terpanjang sengaja sama. */
    public const KEEP_DAYS = 180;

    public const OPTIONS = [7 => '7 hari', 30 => '30 hari', 90 => '3 bulan', 180 => '6 bulan'];

    public static function fromRequest(Request $request): int
    {
        $days = (int) $request->input('range', 7);

        return array_key_exists($days, self::OPTIONS) ? $days : 7;
    }

    public static function start(int $days)
    {
        return now()->subDays($days - 1)->startOfDay();
    }

    /** Scan per hari untuk rentang ini, hari tanpa scan diisi 0. */
    public static function daily(Builder $scans, int $days): array
    {
        $counts = (clone $scans)
            ->where('created_at', '>=', self::start($days))
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as n'))
            ->groupBy('d')
            ->pluck('n', 'd');

        $labels = [];
        $values = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $i === 0 ? 'Hari ini' : $date->translatedFormat($days <= 7 ? 'D d/m' : 'j M');
            $values[] = (int) $counts->get($date->format('Y-m-d'), 0);
        }

        return [$labels, $values];
    }
}
