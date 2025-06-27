<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function firma()
    {
    return $this->belongsTo(\App\Models\Tenant::class, 'firma_id');
    }

}
