<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = CardLog::with('card')
            ->orderByDesc('created_at');

        // Filter berdasarkan card tertentu
        if ($request->filled('card_id')) {
            $query->where('card_id', strtoupper($request->card_id));
        }

        // Filter berdasarkan jenis aksi
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Pencarian berdasarkan card ID atau nama toko
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('card_id', 'like', "%{$s}%")
                  ->orWhereHas('card', function ($q2) use ($s) {
                      $q2->where('owner_name', 'like', "%{$s}%");
                  });
            });
        }

        $logs = $query->paginate(30)->withQueryString();

        // Daftar card aktif untuk dropdown filter
        $activeCards = Card::where('status', 'active')
            ->orderBy('owner_name')
            ->get(['id', 'owner_name']);

        // Daftar aksi unik untuk dropdown filter
        $actions = CardLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('dashboard.activity', compact('logs', 'activeCards', 'actions'));
    }
}
