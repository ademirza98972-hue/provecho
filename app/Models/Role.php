<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * Hak akses yang bisa dicentang per role. Kunci = nama Gate.
     * Generate card, berikan card, dan kelola pengguna/role sengaja tidak ada di sini:
     * itu khusus admin utama, supaya user tidak bisa menaikkan aksesnya sendiri.
     */
    public const PERMISSIONS = [
        'invoices'      => ['Nota', 'Buat, lihat, ubah, dan unduh nota dari card miliknya.'],
        'map'           => ['Peta', 'Peta lokasi pelanggan dari card miliknya.'],
        'export'        => ['Export CSV', 'Export data card dan riwayat aktivitas miliknya.'],
        'cards-print'   => ['Cetak QR', 'Cetak QR card miliknya.'],
        'cards-disable' => ['Nonaktifkan card', 'Nonaktifkan dan aktifkan kembali card miliknya.'],
        'cards-reset'   => ['Reset card', 'Kembalikan card miliknya ke Belum aktif. Data usaha dan pembeli terhapus.'],
        'cards-delete'  => ['Hapus card', 'Hapus card miliknya yang belum dicetak.'],
    ];

    protected $fillable = ['name', 'permissions'];

    protected $casts = ['permissions' => 'array'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
