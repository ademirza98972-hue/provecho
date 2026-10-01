<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Support\GoogleMapsLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PlacesController extends Controller
{
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2|max:200']);

        $response = Http::withHeaders([
            'X-Goog-Api-Key'   => config('services.google.places_key'),
            'X-Goog-FieldMask' => 'places.id,places.displayName,places.formattedAddress',
        ])->timeout(10)->post('https://places.googleapis.com/v1/places:searchText', [
            'textQuery'      => $request->q,
            'maxResultCount' => 5,
        ]);

        // Dibedakan dari "tidak ada hasil": kalau kuota habis atau key bermasalah,
        // jangan sampai pemilik toko mengira usahanya tidak terdaftar di Google.
        if (!$response->successful()) {
            report(new RuntimeException('Places API gagal: ' . $response->status() . ' ' . $response->body()));

            return response()->json([
                'error' => 'Pencarian Google sedang bermasalah.',
            ], 502);
        }

        $results = collect($response->json('places', []))->map(fn($p) => [
            'place_id' => $p['id'],
            'name'     => $p['displayName']['text'] ?? '',
            'address'  => $p['formattedAddress'] ?? '',
            'url'      => 'https://search.google.com/local/writereview?placeid=' . $p['id'],
        ]);

        return response()->json(['results' => $results]);
    }

    /** Jalur cadangan: tempel link Google Maps, tanpa memakai kuota Places. */
    public function resolveMaps(Request $request)
    {
        $request->validate(['url' => 'required|string|max:2048']);

        try {
            $place = GoogleMapsLink::resolve($request->url);
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'Link tidak bisa dibuka. Coba salin ulang dari Google Maps.'], 422);
        }

        return response()->json([
            'result' => [
                'place_id' => $place['place_id'],
                'name'     => $place['name'],
                'address'  => $place['address'],
                'url'      => $place['review_url'],
            ],
        ]);
    }
}
