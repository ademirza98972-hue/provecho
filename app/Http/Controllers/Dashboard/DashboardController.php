<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardLog;
use App\Support\ScanRange;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $days = ScanRange::fromRequest($request);
        $cards = fn () => Card::visibleTo($user);
        $scans = fn () => CardLog::visibleTo($user)->where('action', 'scan');

        $stats = [
            'total'     => $cards()->count(),
            'active'    => $cards()->where('status', 'active')->count(),
            'inactive'  => $cards()->where('status', 'inactive')->count(),
            'disabled'  => $cards()->where('status', 'disabled')->count(),
            'unprinted' => $cards()->where('status', 'inactive')->whereNull('printed_at')->count(),
        ];

        $todayScans     = $scans()->whereDate('created_at', today())->count();
        $yesterdayScans = $scans()->whereDate('created_at', today()->subDay())->count();
        $totalScans     = $scans()->count() + (int) $cards()->sum('archived_scans');

        [$chartLabels, $chartValues] = ScanRange::daily($scans(), $days);
        $rangeScans = array_sum($chartValues);

        // Periode pembanding hanya kalau datanya masih tersimpan utuh.
        $prevRangeScans = $days * 2 <= ScanRange::KEEP_DAYS
            ? $scans()->where('created_at', '>=', ScanRange::start($days * 2))->where('created_at', '<', ScanRange::start($days))->count()
            : null;

        $recent = $cards()->where('status', 'active')->orderByDesc('activated_at')->limit(5)->get();

        $topStores = $cards()->where('status', 'active')
            ->withCount(['logs as scan_count' => fn ($q) => $q->where('action', 'scan')->where('created_at', '>=', ScanRange::start($days))])
            ->orderByDesc('scan_count')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'days', 'todayScans', 'yesterdayScans', 'rangeScans', 'prevRangeScans', 'totalScans',
            'chartLabels', 'chartValues', 'recent', 'topStores'
        ));
    }
}
