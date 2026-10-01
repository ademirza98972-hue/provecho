<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;

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

        $recent = Card::where('status', 'active')
            ->orderByDesc('activated_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recent'));
    }
}
