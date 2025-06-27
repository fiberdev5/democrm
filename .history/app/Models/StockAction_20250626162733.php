<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAction extends Model
{
    use HasFactory;

    protected $guarded = [];

public function musteri()
{
    return $this->belongsTo(\App\Models\Musteri::class, 'servisid');
}


}
