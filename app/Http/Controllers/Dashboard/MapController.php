<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Support\PlaceLocator;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $mine = fn () => Card::visibleTo($request->user())->whereIn('status', ['active', 'disabled']);

        // Lengkapi koordinat yang belum ada, dibatasi supaya halaman tidak lambat.
        $started = microtime(true);
        $pending = $mine()
            ->whereNotNull('place_id')->whereNull('lat')
            ->limit(15)->get();
        foreach ($pending as $card) {
            if (microtime(true) - $started > 8) {
                break;
            }
            PlaceLocator::locate($card);
        }

        $cards = $mine()->with('reseller:id,name')->get();

        $markers = $cards->whereNotNull('lat')->map(fn (Card $c) => [
            'id'       => $c->id,
            'name'     => $c->owner_name ?? $c->id,
            'address'  => $c->owner_address,
            'active'   => $c->status === 'active',
            'reseller' => $c->reseller?->name,
            'lat'      => $c->lat,
            'lng'      => $c->lng,
            'url'      => route('dashboard.cards.show', $c),
        ])->values();

        // Untuk cek cepat: place_id usaha yang sudah memakai Provecho.
        $registered = $cards->whereNotNull('place_id')->mapWithKeys(fn (Card $c) => [
            $c->place_id => ['id' => $c->id, 'active' => $c->status === 'active', 'lat' => $c->lat, 'lng' => $c->lng],
        ]);

        $missing = $cards->where('status', 'active')->whereNull('lat')->values();

        return view('dashboard.map', compact('markers', 'registered', 'missing'));
    }
}
