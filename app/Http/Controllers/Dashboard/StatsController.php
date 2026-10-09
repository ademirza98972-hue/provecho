<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardLog;
use App\Support\ScanRange;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $days = ScanRange::fromRequest($request);
        $from = ScanRange::start($days);
        $scans = fn () => CardLog::visibleTo($user)->where('action', 'scan');

        [$chartLabels, $chartValues] = ScanRange::daily($scans(), $days);
        $rangeScans  = array_sum($chartValues);
        $activeCards = Card::visibleTo($user)->where('status', 'active')->count();
        $totalScans  = $scans()->count() + (int) Card::visibleTo($user)->sum('archived_scans');

        $query = Card::visibleTo($user)->where('status', 'active')
            ->withCount([
                'logs as range_count' => fn ($q) => $q->where('action', 'scan')->where('created_at', '>=', $from),
                'logs as live_count'  => fn ($q) => $q->where('action', 'scan'),
            ])
            ->withMax(['logs as last_scan_at' => fn ($q) => $q->where('action', 'scan')], 'created_at');

        $sort = in_array($request->sort, ['name', 'range', 'total', 'last_scan', 'activated']) ? $request->sort : 'range';
        $dir  = $request->dir === 'asc' ? 'asc' : 'desc';

        match ($sort) {
            'name'      => $query->orderBy('owner_name', $dir),
            'total'     => $query->orderByRaw("live_count + archived_scans {$dir}"),
            'last_scan' => $query->orderBy('last_scan_at', $dir),
            'activated' => $query->orderBy('activated_at', $dir),
            default     => $query->orderBy('range_count', $dir),
        };

        $stores = $query->paginate(20)->withQueryString();

        return view('dashboard.stats', compact(
            'days', 'chartLabels', 'chartValues', 'rangeScans', 'activeCards', 'totalScans', 'stores', 'sort', 'dir'
        ));
    }
}
