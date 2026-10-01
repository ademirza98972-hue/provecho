<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['card_id', 'action', 'ip_address', 'user_agent'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
