<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Ringkasan global
        $totalScans     = CardLog::visibleTo($user)->where('action', 'scan')->count();
        $todayScans     = CardLog::visibleTo($user)->where('action', 'scan')->whereDate('created_at', today())->count();
        $weekScans      = CardLog::visibleTo($user)->where('action', 'scan')->where('created_at', '>=', now()->subDays(7))->count();
        $activeCards    = Card::visibleTo($user)->where('status', 'active')->count();

        // Scan per toko (card aktif)
        $query = Card::visibleTo($user)->where('status', 'active')
            ->withCount(['logs as scan_count' => function ($q) {
                $q->where('action', 'scan');
            }])
            ->withMax('logs as last_scan_at', 'created_at');

        // Sorting
        $sort = $request->input('sort', 'scan_count');
        $dir  = $request->input('dir', 'desc');

        if ($sort === 'name') {
            $query->orderBy('owner_name', $dir);
        } elseif ($sort === 'last_scan') {
            $query->orderBy('last_scan_at', $dir);
        } elseif ($sort === 'activated') {
            $query->orderBy('activated_at', $dir);
        } else {
            $query->orderBy('scan_count', $dir);
        }

        $stores = $query->paginate(20)->withQueryString();

        // Scan per hari (7 hari terakhir) untuk chart sederhana
        $dailyScans = CardLog::visibleTo($user)->where('action', 'scan')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Lengkapi 7 hari (isi 0 untuk hari tanpa scan)
        $chartData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData->put($date, $dailyScans->get($date, 0));
        }

        return view('dashboard.stats', compact(
            'totalScans', 'todayScans', 'weekScans', 'activeCards',
            'stores', 'chartData', 'sort', 'dir'
        ));
    }
}
