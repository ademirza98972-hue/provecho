<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Invoice extends Model
{
    public const PAYMENT_METHODS = ['tunai' => 'Tunai', 'transfer' => 'Transfer bank', 'qris' => 'QRIS', 'lainnya' => 'Lainnya'];

    protected $fillable = ['number', 'user_id', 'buyer_name', 'buyer_phone', 'payment_method', 'discount', 'notes'];

    protected $casts = ['discount' => 'integer'];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $user->isAdmin() ? $query : $query->where('user_id', $user->id);
    }

    public function getSubtotalAttribute(): int
    {
        return (int) $this->items->sum('price');
    }

    public function getTotalAttribute(): int
    {
        return max(0, $this->subtotal - $this->discount);
    }

    public const PREFIX = 'PV-';

    /** Dicetak di nota untuk klaim garansi dan pertanyaan pembeli. */
    public const CONTACT_PHONE = '0895400842721';

    /** PV-20261012-001: urut per hari. Tanda strip membedakannya dari ID card (PVXXXXXX). */
    public static function nextNumber(): string
    {
        $prefix = self::PREFIX . now()->format('Ymd') . '-';
        $last = static::where('number', 'like', $prefix . '%')->max('number');

        return $prefix . str_pad((int) substr((string) $last, -3) + 1, 3, '0', STR_PAD_LEFT);
    }

    /** Link unduh untuk pembeli tanpa login; bertanda tangan agar nota orang lain tidak bisa ditebak. */
    public function getPublicUrlAttribute(): string
    {
        return URL::signedRoute('invoice.public', $this);
    }

    public function getDownloadUrlAttribute(): string
    {
        return URL::signedRoute('invoice.download', $this);
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        $wa = Card::whatsappUrl($this->buyer_phone);
        if (! $wa) {
            return null;
        }

        $text = "Halo {$this->buyer_name}, terima kasih sudah berbelanja. Berikut nota pembelian {$this->number}:\n{$this->public_url}";

        return $wa . '?text=' . rawurlencode($text);
    }

    public static function rupiah(int $n): string
    {
        return 'Rp' . number_format($n, 0, ',', '.');
    }
}
