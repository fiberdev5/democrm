<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory,HasRoles;

    protected $table = 'tb_user';
    protected $primaryKey = 'user_id';

    protected $guarded = [];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function country() 
    {
        return $this->belongsTo(Il::class, 'il', 'id');
    }

    public function state()
    {
        return $this->belongsTo(Ilce::class, 'ilce','id');
    }

    public function yapilanAnketler()
    {
        return $this->hasMany(Survey::class, 'personel', 'user_id');
    }
    
    public function ekledigiAnketler()
    {
        return $this->hasMany(Survey::class, 'ekleyen', 'user_id');
    }
 
    public function assignedServices()
{
    return $this->hasMany(Service::class, 'pid');
}

public function servicePlannings()
{
    return $this->hasMany(ServicePlanning::class, 'pid');
}

public function cashTransactions()
{
    return $this->hasMany(CashTransaction::class, 'personel');
}

public function technicianCashTransactions()
{
    return $this->hasMany(CashTransaction::class, 'pid');
}

// Teknisyen rolündeki kullanıcıları almak için scope
public function scopeTechnicians($query)
{
    return $query->whereHas('roles', function($q) {
        $q->where('name', 'Teknisyen');
    });
}


}
