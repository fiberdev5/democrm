<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\StockShef;
use App\Models\DeviceBrand;
use App\Models\DeviceType;

class Stock extends Model
{
    use HasFactory;

            public function raf() {
                return $this->belongsTo(StockShef::class, 'urunDepo', 'id'); 
            }

            public function marka() {
                return $this->belongsTo(DeviceBrand::class, 'stok_marka', 'id');
            }

            public function cihaz() {
                return $this->belongsTo(DeviceType::class, 'stok_cihaz', 'id');
            }
}
