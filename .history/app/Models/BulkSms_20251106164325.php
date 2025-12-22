<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkSms extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function servisModel()
    {
        return $this->belongsTo(Service::class, 'servis');
    }

    public function musteriModel()
    {
        return $this->belongsTo(Customer::class, 'musteri');
    }
}
