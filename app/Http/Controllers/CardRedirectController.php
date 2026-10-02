<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Dashboard\PlacesController;
use App\Models\Card;
use App\Models\CardLog;
use Illuminate\Http\Request;

class CardRedirectController extends Controller
{
    public function redirect(Request $request, string $id)
    {
        $card = Card::find(strtoupper($id));

        if ($card) {
            $this->log($card, 'scan', $request);
        }

        // Disabled dinonaktifkan operator secara sengaja — jangan tawarkan aktivasi ulang.
        if (!$card || $card->status === 'disabled') {
            return response()->view('card.inactive', ['card' => $card], $card ? 200 : 404);
        }

        if (!$card->isActive()) {
            return response()->view('card.activate', ['card' => $card]);
        }

        // 302 server-side redirect — bukan halaman HTML perantara.
        // Halaman perantara (go.blade.php) berstatus HTTP 200, yang lebih agresif
        // di-cache browser/WebView dari HP, sehingga ganti link setelah reset
        // tidak berlaku sampai cache manual dihapus. HTTP 302 + no-store
        // memastikan browser selalu bertanya ke server untuk URL terbaru.
        return redirect($card->google_url, 302)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Aktivasi mandiri oleh pemilik kartu. Tidak perlu login: ID kartu acak dan
     * hanya diketahui orang yang memegang kartu fisiknya.
     */
    public function activate(Request $request, string $id)
    {
        $card = $this->findActivatable($id);

        $data = $request->validate([
            'google_url'    => 'required|url:http,https|max:2048',
            'owner_name'    => 'required|string|max:255',
            'owner_address' => 'nullable|string|max:500',
            'place_id'      => 'nullable|string|max:255',
        ]);

        $card->update($data + [
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        $this->log($card, 'activated', $request);

        return view('card.activated', compact('card'));
    }

    /**
     * Pencarian Places versi publik — diikat ke kartu yang valid dan belum aktif
     * supaya kuota Google tidak bisa dikuras orang asing.
     */
    public function places(Request $request, string $id)
    {
        $this->findActivatable($id);

        return app(PlacesController::class)->search($request);
    }

    /** Jalur cadangan publik: tempel link Google Maps, tidak memakai kuota Places. */
    public function resolveMaps(Request $request, string $id)
    {
        $this->findActivatable($id);

        return app(PlacesController::class)->resolveMaps($request);
    }

    private function findActivatable(string $id): Card
    {
        $card = Card::find(strtoupper($id));

        abort_unless($card && $card->status === 'inactive', 404);

        return $card;
    }

    private function log(Card $card, string $action, Request $request): void
    {
        CardLog::create([
            'card_id'    => $card->id,
            'action'     => $action,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
