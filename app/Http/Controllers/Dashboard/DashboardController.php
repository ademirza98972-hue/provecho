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

        $todayScans = CardLog::where('action', 'scan')->whereDate('created_at', today())->count();
        $weekScans  = CardLog::where('action', 'scan')->where('created_at', '>=', now()->subDays(7))->count();
        $totalScans = CardLog::where('action', 'scan')->count();

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
            $chartValues->push($dailyScans->get($date->format('Y-m-d'), 0));
        }

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
            'stats', 'todayScans', 'weekScans', 'totalScans',
            'chartLabels', 'chartValues', 'recent', 'topStores'
        ));
    }
}
