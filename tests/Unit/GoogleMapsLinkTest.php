<?php

namespace Tests\Unit;

use App\Support\GoogleMapsLink;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class GoogleMapsLinkTest extends TestCase
{
    /** Patokan: FID dari URL Maps Kopi Kenangan vs place_id hasil Places API untuk toko yang sama. */
    public function test_feature_id_converts_to_the_place_id_google_returns(): void
    {
        $this->assertSame(
            'ChIJ-dkoHGaB5i0RBaT2yAWcc6g',
            GoogleMapsLink::toPlaceId('2de681661c28d9f9', 'a8739c05c8f6a405')
        );
    }

    /** hexdec() mengubah nilai > PHP_INT_MAX jadi float; hasilnya mirip tapi salah. */
    public function test_high_bit_values_keep_full_precision(): void
    {
        $this->assertSame(
            GoogleMapsLink::toPlaceId('ffffffffffffffff', 'ffffffffffffffff'),
            GoogleMapsLink::toPlaceId('FFFFFFFFFFFFFFFF', 'FFFFFFFFFFFFFFFF'),
            'Huruf besar/kecil tidak boleh mengubah hasil'
        );

        $this->assertNotSame(
            GoogleMapsLink::toPlaceId('a8739c05c8f6a405', 'a8739c05c8f6a405'),
            GoogleMapsLink::toPlaceId('a8739c05c8f6a404', 'a8739c05c8f6a405'),
            'Beda 1 bit terakhir harus menghasilkan Place ID berbeda'
        );
    }

    public function test_long_url_resolves_without_any_network_call(): void
    {
        $url = 'https://www.google.com/maps/place/Kopi+Kenangan+-+Ruko+Ahmad+Yani/@-3.4459436,114.8077579,17z'
             . '/data=!4m8!3m7!1s0x2de681661c28d9f9:0xa8739c05c8f6a405!8m2!3d-3.4459436!4d114.8103328';

        $result = GoogleMapsLink::resolve($url);

        $this->assertSame('Kopi Kenangan - Ruko Ahmad Yani', $result['name']);
        $this->assertSame('ChIJ-dkoHGaB5i0RBaT2yAWcc6g', $result['place_id']);
        $this->assertSame(
            'https://search.google.com/local/writereview?placeid=ChIJ-dkoHGaB5i0RBaT2yAWcc6g',
            $result['review_url']
        );
    }

    /**
     * Share dari aplikasi HP tidak melempar ke /maps/place/, melainkan ke
     * maps.google.com?q=<nama, alamat>&ftid=0x..:0x.. — bentuk kedua ini
     * sempat terlewat dan membuat semua link dari HP ditolak.
     */
    public function test_phone_share_redirect_target_resolves(): void
    {
        $url = 'https://maps.google.com?q=Gemilang+Pusat+Bahan+Bangunan+Banjarbaru,+Jl.+A.+Yani+No.Km.33,'
             . '+Guntung+Payung,+Kec.+Landasan+Ulin,+Kota+Banjar+Baru,+Kalimantan+Selatan+70714'
             . '&ftid=0x2de68163309870b9:0x73bc6400cc369536&entry=gps';

        $result = GoogleMapsLink::resolve($url);

        // Dicocokkan dengan place_id yang dikembalikan Places API untuk toko yang sama.
        $this->assertSame('ChIJuXCYMGOB5i0RNpU2zABkvHM', $result['place_id']);
        $this->assertSame('Gemilang Pusat Bahan Bangunan Banjarbaru', $result['name']);
        $this->assertStringContainsString('Landasan Ulin', $result['address']);
    }

    public function test_url_encoded_ftid_separator_is_handled(): void
    {
        $url = 'https://maps.google.com?q=Toko&ftid=0x2de68163309870b9%3A0x73bc6400cc369536';

        $this->assertSame('ChIJuXCYMGOB5i0RNpU2zABkvHM', GoogleMapsLink::resolve($url)['place_id']);
    }

    public function test_non_google_link_is_rejected(): void
    {
        $this->expectExceptionMessage('bukan link Google Maps');
        GoogleMapsLink::resolve('https://evil.example.com/maps/place/Toko/!1s0x1:0x2');
    }

    public function test_internal_address_is_rejected(): void
    {
        $this->expectExceptionMessage('bukan link Google Maps');
        GoogleMapsLink::resolve('http://169.254.169.254/latest/meta-data/');
    }

    public function test_search_link_without_place_gives_actionable_message(): void
    {
        $this->expectExceptionMessage('link hasil pencarian');
        GoogleMapsLink::resolve('https://www.google.com/maps/place/Kopi+Kenangan/@-3.44,114.80,17z');
    }
}
