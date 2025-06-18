<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

            public function raf() {
                return $this->belongsTo(Raf::class, 'urunDepo', 'id');  // urunDepo stokta depo/raf id olabilir
            }

            public function marka() {
                return $this->belongsTo(Marka::class, 'stok_marka', 'id');
            }

            public function cihaz() {
                return $this->belongsTo(Cihaz::class, 'stok_cihaz', 'id');
            }
}
