<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'status', 'google_url', 'place_id',
        'owner_name', 'owner_address', 'activated_at',
        'order_number', 'buyer_name', 'buyer_phone',
        'disabled_at', 'printed_at', 'notes',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'disabled_at' => 'datetime',
        'printed_at' => 'datetime',
        'reseller_id' => 'integer',
        'archived_scans' => 'integer',
        'lat' => 'float',
        'lng' => 'float',
    ];

    protected static function booted(): void
    {
        // Lokasi diganti atau dikosongkan: koordinat lama tidak berlaku, diambil ulang saat peta dibuka.
        static::saving(function (Card $card) {
            if ($card->isDirty('place_id') && ! $card->isDirty('lat')) {
                $card->lat = null;
                $card->lng = null;
            }
        });
    }

    public function logs()
    {
        return $this->hasMany(CardLog::class);
    }

    public function reseller()
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }

    /** Scan yang masih punya detail (scan_count dari withCount) + scan lama yang sudah dibersihkan. */
    public function getTotalScansAttribute(): int
    {
        return (int) ($this->scan_count ?? 0) + (int) $this->archived_scans;
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $user->isAdmin() ? $query : $query->where('reseller_id', $user->id);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public const BUYER_RULES = [
        'order_number' => 'nullable|string|max:50',
        'buyer_name'   => 'nullable|string|max:100',
        'buyer_phone'  => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{8,20}$/'],
    ];

    public const BUYER_MESSAGES = [
        'buyer_phone.regex' => 'Nomor HP hanya boleh angka, spasi, strip, atau diawali +.',
        'order_number.max'  => 'Nomor pesanan maksimal 50 karakter.',
        'buyer_name.max'    => 'Nama pembeli maksimal 100 karakter.',
    ];

    /** Kosong/spasi disimpan sebagai null. */
    public static function cleanBuyer(array $data): array
    {
        return array_map(fn ($v) => is_string($v) ? (trim($v) ?: null) : $v, $data);
    }

    /** Link chat WhatsApp ke pembeli; 08xx dan +62xx sama-sama jadi 62xx. */
    public function getBuyerWhatsappAttribute(): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $this->buyer_phone);
        if ($digits === '') {
            return null;
        }

        return 'https://wa.me/' . (str_starts_with($digits, '0') ? '62' . substr($digits, 1) : $digits);
    }

    public function getUrlAttribute(): string
    {
        return rtrim(config('app.url'), '/') . '/c/' . $this->id;
    }

    // Tanpa 0/O/1/I agar tidak salah baca saat diketik manual dari kartu fisik.
    private const ID_ALPHABET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    /**
     * ID acak, bukan berurutan: ID tercetak di kartu sekaligus jadi rahasia
     * yang mengizinkan aktivasi mandiri, jadi tidak boleh bisa ditebak.
     */
    public static function newId(): string
    {
        $max = strlen(self::ID_ALPHABET) - 1;

        do {
            $id = 'PV';
            for ($i = 0; $i < 6; $i++) {
                $id .= self::ID_ALPHABET[random_int(0, $max)];
            }
        } while (static::whereKey($id)->exists());

        return $id;
    }
}
