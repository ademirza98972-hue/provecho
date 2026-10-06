<?php

namespace App\Support;

use App\Models\Card;

class Brand
{
    public const DEFAULT_NAME  = 'PROVECHO GOOGLE REVIEW';
    public const DEFAULT_COLOR = '#0EA5E9';

    /** Tampilan halaman publik card: milik reseller kalau sudah diatur, selain itu Provecho. */
    public static function forCard(?Card $card): array
    {
        $r = $card?->reseller;

        return self::make($r?->brand_name, $r?->brand_color, $r?->brand_logo);
    }

    public static function make(?string $name, ?string $color, ?string $logo): array
    {
        $color = $color && preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? strtoupper($color) : self::DEFAULT_COLOR;
        [$r, $g, $b] = sscanf($color, '#%02x%02x%02x');
        $mix = fn (int $t, float $p) => sprintf('#%02X%02X%02X', $r + ($t - $r) * $p, $g + ($t - $g) * $p, $b + ($t - $b) * $p);
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return [
            'name'  => $name ?: self::DEFAULT_NAME,
            'logo'  => $logo ?: '/img/logo.png',
            'color' => $color,
            'dark'  => $mix(0, .18),
            'soft'  => $mix(255, .92),
            'on'    => $luminance > .62 ? '#111827' : '#FFFFFF',
        ];
    }
}
