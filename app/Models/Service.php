<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function musteri()
    {
        return $this->belongsTo(Customer::class, 'musteri_id', 'id');
    }

    public function markaCihaz()
    {
        return $this->belongsTo(DeviceBrand::class, 'cihazMarka', 'id');
    }

    public function turCihaz()
    {
        return $this->belongsTo(DeviceType::class, 'cihazTur', 'id');
    }

    public function asamalar()
    {
        return $this->belongsTo(ServiceStage::class, 'servisDurum', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'kayitAlan', 'user_id');
    }

    public function warranty()
    {
        return $this->belongsTo(WarrantyPeriod::class, 'garantiSuresi', 'id');
    }
}
