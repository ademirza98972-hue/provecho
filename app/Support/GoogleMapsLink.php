<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Mengubah link Google Maps jadi link tulis-ulasan, tanpa memanggil Places API.
 *
 * Place ID (ChIJ…) ternyata hanya penyandian ulang dari feature ID yang sudah
 * tercantum di URL Maps sebagai `!1s0x<hi>:0x<lo>`, jadi datanya tidak perlu
 * ditanyakan ke Google lagi. Ini jalur cadangan saat kuota Places habis.
 */
class GoogleMapsLink
{
    private const MAX_REDIRECTS = 3;

    /**
     * Feature ID muncul dalam dua bentuk, tergantung asal link:
     *   `!1s0x..:0x..`  — URL panjang dari Maps versi browser
     *   `ftid=0x..:0x..` — tujuan redirect dari Share aplikasi HP
     */
    private const FID_PATTERN = '/(?:!1s|ftid=)0x([0-9a-f]+)(?::|%3A)0x([0-9a-f]+)/i';

    public static function resolve(string $url): array
    {
        $url = trim($url);

        if (!self::isGoogleUrl($url)) {
            throw new RuntimeException('Link ini bukan link Google Maps.');
        }

        $long = self::expand($url);

        if (!preg_match(self::FID_PATTERN, $long, $m)) {
            throw new RuntimeException(
                'Ini sepertinya link hasil pencarian, bukan halaman toko. ' .
                'Buka dulu halaman toko di Google Maps, lalu tekan Share.'
            );
        }

        $placeId = self::toPlaceId($m[1], $m[2]);

        return self::extractPlace($long) + [
            'place_id'   => $placeId,
            'review_url' => 'https://search.google.com/local/writereview?placeid=' . $placeId,
        ];
    }

    /**
     * Feature ID (sepasang hex 64-bit) -> Place ID.
     *
     * Place ID = base64url dari protobuf: field 1 berisi dua fixed64 little-endian.
     * Hex-nya diolah sebagai byte, bukan angka: 0x... melebihi PHP_INT_MAX sehingga
     * hexdec() diam-diam mengubahnya jadi float dan presisinya hilang.
     */
    public static function toPlaceId(string $hi, string $lo): string
    {
        $le = fn(string $hex) => strrev(hex2bin(str_pad(strtolower($hex), 16, '0', STR_PAD_LEFT)));

        $inner = "\x09" . $le($hi) . "\x11" . $le($lo);
        $msg   = "\x0a" . chr(strlen($inner)) . $inner;

        return rtrim(strtr(base64_encode($msg), '+/', '-_'), '=');
    }

    /**
     * Link pendek (hasil Share dari aplikasi HP) tidak memuat data tempat,
     * hanya melempar ke link panjang — jadi perlu diikuti sekali.
     */
    private static function expand(string $url): string
    {
        for ($i = 0; $i < self::MAX_REDIRECTS; $i++) {
            // Berhenti begitu feature ID didapat — bentuk URL-nya tidak penting.
            // Yang perlu dibuka hanya link pendek; URL Maps panjang tanpa feature ID
            // memang link pencarian, dan menembaknya ke jaringan cuma buang waktu.
            if (preg_match(self::FID_PATTERN, $url) || !self::isShortener($url)) {
                return $url;
            }

            $res  = Http::withoutRedirecting()->timeout(8)->get($url);
            $next = $res->header('Location');

            if (!$next && preg_match('#https?://[^"\'\s\\\\]*/maps/place/[^"\'\s\\\\]*#', $res->body(), $m)) {
                $next = html_entity_decode($m[0]);
            }

            if (!$next) {
                return $url;
            }

            // Setiap lompatan divalidasi ulang: tanpa ini, link Google bisa
            // dipakai untuk memaksa server menembak alamat internal (SSRF).
            if (!self::isGoogleUrl($next)) {
                throw new RuntimeException('Link mengarah ke alamat di luar Google Maps.');
            }

            $url = $next;
        }

        return $url;
    }

    /** @return array{name: ?string, address: ?string} */
    private static function extractPlace(string $url): array
    {
        // URL browser: nama ada di jalur, alamat tidak tersedia.
        if (preg_match('#/maps/place/([^/@?]+)#', $url, $m)) {
            return ['name' => self::clean(rawurldecode(str_replace('+', ' ', $m[1]))), 'address' => null];
        }

        // Redirect dari Share HP: q berisi "Nama, Alamat lengkap".
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (!empty($query['q']) && is_string($query['q'])) {
            $parts = explode(',', $query['q'], 2);

            return [
                'name'    => self::clean($parts[0]),
                'address' => self::clean($parts[1] ?? ''),
            ];
        }

        return ['name' => null, 'address' => null];
    }

    private static function clean(string $value): ?string
    {
        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    private static function isShortener(string $url): bool
    {
        return in_array(
            strtolower((string) parse_url($url, PHP_URL_HOST)),
            ['maps.app.goo.gl', 'goo.gl'],
            true
        );
    }

    private static function isGoogleUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $schemeOk = in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);

        return $schemeOk && (
            in_array($host, ['maps.app.goo.gl', 'goo.gl', 'maps.google.com'], true)
            || preg_match('/^((www|maps)\.)?google(\.[a-z]{2,3}){1,2}$/', $host) === 1
        );
    }
}
