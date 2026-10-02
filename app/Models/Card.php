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
        'disabled_at', 'printed_at', 'notes',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'disabled_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->hasMany(CardLog::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
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
