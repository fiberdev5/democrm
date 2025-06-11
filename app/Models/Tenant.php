<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ils() 
    {
        return $this->belongsTo(Il::class, 'il', 'id');
    }

    public function ilces()
    {
        return $this->belongsTo(Ilce::class, 'ilce','id');
    }

    // Firmanın aşamaları
    public function serviceStages()
    {
        return $this->hasMany(ServiceStage::class, 'firma_id', 'id');
    }

    // Varsayılan aşamaları getir
    public static function defaultStages()
    {
        return ServiceStage::whereNull('firma_id')->get();
    }
}
