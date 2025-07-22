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
}
