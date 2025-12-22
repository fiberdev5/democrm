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
        return $this->belongsTo(\App\Models\Customer::class, 'servisid');
    }

    // StockAction tablosundaki 'kid' sütunu, User tablosundaki 'user_id' sütununa bağlanır.
    public function actionPerformer()
    {
        return $this->belongsTo(\App\Models\User::class, 'kid', 'user_id');
    }


}
