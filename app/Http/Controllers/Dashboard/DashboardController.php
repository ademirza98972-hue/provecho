<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Card::count(),
            'active'   => Card::where('status', 'active')->count(),
            'inactive' => Card::where('status', 'inactive')->count(),
            'disabled' => Card::where('status', 'disabled')->count(),
        ];

        $stats['unprinted'] = Card::where('status', 'inactive')->whereNull('printed_at')->count();

        $scans = fn () => CardLog::where('action', 'scan');
        $todayScans     = $scans()->whereDate('created_at', today())->count();
        $yesterdayScans = $scans()->whereDate('created_at', today()->subDay())->count();
        $totalScans     = $scans()->count();
        $prevWeekScans  = $scans()
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->where('created_at', '<', now()->subDays(6)->startOfDay())
            ->count();

        $dailyScans = CardLog::where('action', 'scan')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $chartLabels = collect();
        $chartValues = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels->push($date->translatedFormat('D d/m'));
            $chartValues->push((int) $dailyScans->get($date->format('Y-m-d'), 0));
        }
        $weekScans = $chartValues->sum();

        $recent = Card::where('status', 'active')
            ->orderByDesc('activated_at')
            ->limit(5)
            ->get();

        $topStores = Card::where('status', 'active')
            ->withCount(['logs as scan_count' => fn ($q) => $q->where('action', 'scan')])
            ->orderByDesc('scan_count')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'todayScans', 'yesterdayScans', 'weekScans', 'prevWeekScans', 'totalScans',
            'chartLabels', 'chartValues', 'recent', 'topStores'
        ));
    }
}
