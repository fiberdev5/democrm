<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\StockShelf;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\StockAction;

class Stock extends Model
{
    use HasFactory;

            public function raf() {
                return $this->belongsTo(StockShelf::class, 'urunDepo', 'id'); 
            }

            public function marka() {
                return $this->belongsTo(DeviceBrand::class, 'stok_marka', 'id');
            }

            public function cihaz() {
                return $this->belongsTo(DeviceType::class, 'stok_cihaz', 'id');
            }

            public function sonHareket() {
                return $this->hasOne(StockAction::class, 'stokId', 'id')->latestOfMany();
            }

            public function personel() {
                return $this->belongsTo(User::class, 'pid', 'user_id');
            } 


}
