<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['card_id', 'price'];

    protected $casts = ['price' => 'integer'];
}
