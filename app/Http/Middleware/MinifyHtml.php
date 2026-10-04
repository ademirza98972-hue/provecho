<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || $html === '') {
            return $response;
        }

        // regex gagal (mis. backtrack limit) → kirim halaman asli, jangan halaman kosong
        $min = rescue(fn () => self::minify($html), '', false);
        if ($min !== '' && preg_last_error() === PREG_NO_ERROR) {
            $response->setContent($min);
        }

        return $response;
    }

    public static function minify(string $html): string
    {
        $kept = [];
        $keep = function (string $block) use (&$kept): string {
            $kept[] = $block;

            return "\x1A" . (count($kept) - 1) . "\x1A";
        };

        // <pre>/<textarea>: spasi bermakna, simpan utuh
        $html = preg_replace_callback('#<(pre|textarea)\b.*?</\1>#is', fn ($m) => $keep($m[0]), $html);

        // <script>: jangan digabung jadi satu baris (komentar // akan menelan kode), cukup buang indentasi
        $html = preg_replace_callback('#<script\b[^>]*>.*?</script>#is',
            fn ($m) => $keep(preg_replace('/^[ \t]+|[ \t]+$/m', '', preg_replace("/\n\s*\n/", "\n", $m[0]))), $html);

        // <style>: buang komentar & spasi; jangan sentuh +/- (dipakai calc())
        $html = preg_replace_callback('#(<style\b[^>]*>)(.*?)(</style>)#is', function ($m) use ($keep) {
            $css = preg_replace('#/\*.*?\*/#s', '', $m[2]);
            $css = preg_replace('/\s+/', ' ', $css);
            $css = preg_replace('/\s*([{};,>])\s*/', '$1', $css);

            return $keep($m[1] . trim(str_replace(';}', '}', $css)) . $m[3]);
        }, $html);

        $html = preg_replace('/<!--(?!\[if).*?-->/s', '', $html);
        // sisakan satu spasi agar jarak antar elemen inline tidak hilang
        $html = preg_replace('/\s+/', ' ', $html);

        return trim(preg_replace_callback("/\x1A(\d+)\x1A/", fn ($m) => $kept[(int) $m[1]], $html));
    }
}
