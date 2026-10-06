<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardLog extends Model
{
    public $timestamps = false;

    public const ACTIONS = [
        'scan'        => ['Scan',               'act-scan'],
        'activated'   => ['Diaktifkan',         'act-ok'],
        'reactivated' => ['Diaktifkan kembali', 'act-ok'],
        'updated'     => ['Data diubah',        'act-info'],
        'printed'     => ['QR dicetak',         'act-info'],
        'disabled'    => ['Dinonaktifkan',      'act-bad'],
        'reset'       => ['Direset',            'act-warn'],
    ];

    public function getActionLabelAttribute(): string
    {
        return self::ACTIONS[$this->action][0] ?? ucfirst($this->action);
    }

    public function getActionClassAttribute(): string
    {
        return self::ACTIONS[$this->action][1] ?? 'act-scan';
    }

    public function getDeviceAttribute(): string
    {
        $ua = (string) $this->user_agent;

        return match (true) {
            $ua === ''                                                   => '—',
            (bool) preg_match('/WhatsApp|facebookexternalhit|bot|crawl|spider|preview/i', $ua) => 'Preview link',
            (bool) preg_match('/iPhone|iPad/', $ua)                      => 'iPhone',
            str_contains($ua, 'Android')                                 => 'Android',
            str_contains($ua, 'Windows')                                 => 'Windows',
            str_contains($ua, 'Macintosh')                               => 'Mac',
            default                                                      => 'Lainnya',
        };
    }

    protected $fillable = ['card_id', 'action', 'ip_address', 'user_agent'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $user->isAdmin()
            ? $query
            : $query->whereIn('card_id', Card::where('reseller_id', $user->id)->select('id'));
    }
}
