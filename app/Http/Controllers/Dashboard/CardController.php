<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardLog;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $query = Card::query();

        if ($request->filled('status')) {
            if ($request->status === 'printed') {
                $query->whereNotNull('printed_at');
            } elseif ($request->status === 'unprinted') {
                $query->where('status', 'inactive')->whereNull('printed_at');
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('id', 'like', "%{$s}%")
                  ->orWhere('owner_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('generated')) {
            $query->where('created_at', '>=', match ($request->generated) {
                'today' => now()->startOfDay(),
                'week'  => now()->subDays(7)->startOfDay(),
                'month' => now()->subDays(30)->startOfDay(),
                default => now()->startOfDay(),
            });
        }

        $query->withCount(['logs as scan_count' => fn ($q) => $q->where('action', 'scan')]);

        $perPage = in_array((int) $request->input('per_page'), [20, 50, 100]) ? (int) $request->per_page : 20;
        $cards = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $counts = [
            'total'    => Card::count(),
            'active'   => Card::where('status', 'active')->count(),
            'inactive' => Card::where('status', 'inactive')->count(),
            'disabled' => Card::where('status', 'disabled')->count(),
        ];

        return view('dashboard.cards.index', compact('cards', 'counts'));
    }

    public function show(Card $card)
    {
        $logs = $card->logs()->orderByDesc('created_at')->limit(20)->get();
        $qr = QrCode::size(180)->generate($card->url);

        return view('dashboard.cards.show', compact('card', 'logs', 'qr'));
    }

    public function activate(Request $request, Card $card)
    {
        $request->validate([
            'google_url'    => 'required|url:http,https',
            'owner_name'    => 'required|string|max:255',
            'owner_address' => 'nullable|string',
            'place_id'      => 'nullable|string|max:255',
        ]);

        $card->update([
            'status'        => 'active',
            'google_url'    => $request->google_url,
            'owner_name'    => $request->owner_name,
            'owner_address' => $request->owner_address,
            'place_id'      => $request->place_id,
            'activated_at'  => now(),
        ]);

        CardLog::create([
            'card_id'    => $card->id,
            'action'     => 'activated',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard.cards.show', $card)
            ->with('success', "Card {$card->id} berhasil diaktifkan.");
    }

    public function update(Request $request, Card $card)
    {
        $request->validate([
            'google_url'  => 'required|url:http,https',
            'owner_name'  => 'required|string|max:255',
            'owner_address' => 'nullable|string',
            'place_id'    => 'nullable|string|max:255',
        ]);

        $card->update([
            'google_url'    => $request->google_url,
            'owner_name'    => $request->owner_name,
            'owner_address' => $request->owner_address,
            'place_id'      => $request->place_id,
        ]);

        CardLog::create([
            'card_id'    => $card->id,
            'action'     => 'updated',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard.cards.show', $card)
            ->with('success', 'Link berhasil diupdate.');
    }

    public function disable(Request $request, Card $card)
    {
        $card->update([
            'status'      => 'disabled',
            'disabled_at' => now(),
        ]);

        CardLog::create([
            'card_id'    => $card->id,
            'action'     => 'disabled',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard.cards.show', $card)
            ->with('success', "Card {$card->id} dinonaktifkan.");
    }

    public function reactivate(Request $request, Card $card)
    {
        $card->update([
            'status'      => 'active',
            'disabled_at' => null,
        ]);

        CardLog::create([
            'card_id'    => $card->id,
            'action'     => 'reactivated',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard.cards.show', $card)
            ->with('success', "Card {$card->id} diaktifkan kembali.");
    }

    public function reset(Request $request, Card $card)
    {
        $card->update([
            'status'        => 'inactive',
            'google_url'    => null,
            'place_id'      => null,
            'owner_name'    => null,
            'owner_address' => null,
            'activated_at'  => null,
            'disabled_at'   => null,
        ]);

        CardLog::create([
            'card_id'    => $card->id,
            'action'     => 'reset',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard.cards.show', $card)
            ->with('success', "Card {$card->id} direset ke Belum Aktif.");
    }

    public function destroy(Card $card)
    {
        $id = $card->id;
        $card->logs()->delete();
        $card->delete();

        return redirect()->route('dashboard.cards.index')
            ->with('success', "Card {$id} dihapus.");
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        if ($ids) {
            CardLog::whereIn('card_id', $ids)->delete();
            Card::whereIn('id', $ids)->delete();
        }

        return redirect()->route('dashboard.cards.index')
            ->with('success', count($ids) . ' card dihapus.');
    }

    public function generate(Request $request)
    {
        $request->validate(['count' => 'required|integer|min:1|max:500']);

        $ids = [];
        for ($i = 0; $i < $request->count; $i++) {
            $ids[] = Card::create(['id' => Card::newId()])->id;
        }

        return redirect()->route('dashboard.cards.index')
            ->with('success', count($ids) . ' card berhasil digenerate.');
    }

    public function exportPdf(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));

        $cards = $ids
            ? Card::whereIn('id', $ids)->orderBy('id')->get()
            : Card::where('status', 'inactive')->orderBy('id')->limit(100)->get();

        $mode = $request->input('mode', 'a4');

        $qrSize = $mode === 'sticker' ? 120 : 160;
        $qrCodes = $cards->mapWithKeys(fn($card) => [
            $card->id => QrCode::size($qrSize)->generate($card->url),
        ]);

        $view = match ($mode) {
            'single'  => 'dashboard.cards.print-single',
            'sticker' => 'dashboard.cards.print-sticker',
            'a3'      => 'dashboard.cards.print-a3',
            default   => 'dashboard.cards.print',
        };

        $unprinted = $cards->whereNull('printed_at');
        if ($unprinted->isNotEmpty()) {
            Card::whereIn('id', $unprinted->pluck('id'))->update(['printed_at' => now()]);
            foreach ($unprinted as $card) {
                CardLog::create([
                    'card_id'    => $card->id,
                    'action'     => 'printed',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return view($view, compact('cards', 'qrCodes'));
    }
}
