<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardLog;
use App\Models\User;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Card::visibleTo($user)->with('reseller:id,name');

        if ($user->isAdmin() && $request->filled('reseller')) {
            $request->reseller === 'none'
                ? $query->whereNull('reseller_id')
                : $query->where('reseller_id', (int) $request->reseller);
        }

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
                  ->orWhere('owner_name', 'like', "%{$s}%")
                  ->orWhere('owner_address', 'like', "%{$s}%");
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

        $mine = fn () => Card::visibleTo($user);
        $counts = [
            'total'     => $mine()->count(),
            'active'    => $mine()->where('status', 'active')->count(),
            'inactive'  => $mine()->where('status', 'inactive')->count(),
            'disabled'  => $mine()->where('status', 'disabled')->count(),
            'unprinted' => $mine()->where('status', 'inactive')->whereNull('printed_at')->count(),
        ];

        $resellers = $user->isAdmin() ? User::where('role', 'reseller')->orderBy('name')->get(['id', 'name']) : collect();

        return view('dashboard.cards.index', compact('cards', 'counts', 'resellers'));
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'ids'      => 'required|array|min:1',
            'ids.*'    => 'string',
            'reseller' => 'required',
        ]);

        $resellerId = null;
        if ($data['reseller'] !== 'none') {
            $resellerId = User::where('role', 'reseller')->findOrFail((int) $data['reseller'])->id;
        }

        $n = Card::whereIn('id', $data['ids'])->update(['reseller_id' => $resellerId]);

        return back()->with('success', $resellerId
            ? "{$n} card diberikan ke " . User::find($resellerId)->name . '.'
            : "{$n} card ditarik kembali ke stok admin.");
    }

    public function show(Request $request, Card $card)
    {
        $resellers = $request->user()->isAdmin()
            ? User::where('role', 'reseller')->orderBy('name')->get(['id', 'name'])
            : collect();
        $logs = $card->logs()->orderByDesc('created_at')->limit(20)->get();
        $qr = QrCode::size(180)->generate($card->url);

        $scans = $card->logs()->where('action', 'scan');
        $scanStats = [
            'total' => (clone $scans)->count(),
            'week'  => (clone $scans)->where('created_at', '>=', now()->subDays(6)->startOfDay())->count(),
            'last'  => (clone $scans)->max('created_at'),
        ];

        return view('dashboard.cards.show', compact('card', 'logs', 'qr', 'scanStats', 'resellers'));
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
