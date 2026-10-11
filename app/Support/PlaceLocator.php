<?php

namespace App\Support;

use App\Models\Card;
use Illuminate\Support\Facades\Http;
use Throwable;

class PlaceLocator
{
    /**
     * Ambil koordinat card dari Google berdasarkan place_id dan simpan.
     * Gagal (kuota, jaringan, place tidak ada) tidak melempar error: card cukup belum tampil di peta.
     */
    public static function locate(Card $card): bool
    {
        if (! $card->place_id) {
            return false;
        }

        try {
            $res = Http::withHeaders([
                'X-Goog-Api-Key'   => config('services.google.places_key'),
                'X-Goog-FieldMask' => 'location',
            ])->timeout(6)->get('https://places.googleapis.com/v1/places/' . rawurlencode($card->place_id));

            $lat = $res->json('location.latitude');
            $lng = $res->json('location.longitude');
            if (! $res->successful() || ! is_numeric($lat) || ! is_numeric($lng)) {
                return false;
            }
        } catch (Throwable $e) {
            report($e);

            return false;
        }

        $card->forceFill(['lat' => $lat, 'lng' => $lng])->saveQuietly();

        return true;
    }
}
