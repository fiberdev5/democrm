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

    public function personelStocks()
    {
       
        return $this->hasMany(PersonelStock::class, 'pid', 'user_id');
    }
    
    // IMPERSONATION İLİŞKİLERİ
    public function impersonations()
    {
        return $this->hasMany(UserImpersonation::class, 'impersonator_id', 'user_id');
    }

    public function impersonatedSessions()
    {
        return $this->hasMany(UserImpersonation::class, 'impersonated_id', 'user_id');
    }

    // IMPERSONATION METODLARI
    public function canImpersonate($user = null)
    {
        // Admin, Patron veya Müdür rolleri impersonate edebilir
        $allowedRoles = ['Admin', 'Patron', 'Müdür'];
        
        if (!$this->hasAnyRole($allowedRoles)) {
            return false;
        }

        // // Eğer spesifik bir user verilmişse, aynı tenant'ta olmalı
        // if ($user) {
        //     return $this->tenant_id === $user->tenant_id;
        // }

        return true;
    }

    public function canBeImpersonated()
    {
        // Bu kullanıcı impersonate edilebilir mi?
        

        // Aktif kullanıcı olmalı
        if ($this->status != 1) {
            return false;
        }

        // Çıkış yapmış personel impersonate edilemez
        if ($this->ayrilmaTarihi && $this->ayrilmaTarihi <= now()) {
            return false;
        }

        return true;
    }

    public function isImpersonating()
    {
        return session()->has('impersonator_id');
    }

    public function isBeingImpersonated()
    {
        return session()->has('impersonated_user_id') && 
               session('impersonated_user_id') == $this->user_id;
    }

    public function getOriginalUser()
    {
        if ($this->isImpersonating()) {
            return User::find(session('impersonator_id'));
        }
        return null;
    }

    // Aynı tenant'taki impersonate edilebilir kullanıcıları getir
    public function getImpersonatableUsers()
    {
        return User::where('user_id', '!=', $currentUser->user_id)
                   ->where('user_id', '!=', $this->user_id)
                   ->where('status', 1)
                   ->whereNull('ayrilmaTarihi')
                //    ->whereHas('roles', function($query) {
                //        $query->whereNotIn('name', ['Admin', 'Patron']);
                //    })
                   ->with('roles')
                   ->orderBy('name')
                   ->get()
                   ->filter(function($user) {
                       return $user->canBeImpersonated();
                   });
    }

    // Aktif impersonation session'ını getir
    public function getActiveImpersonation()
    {
        return UserImpersonation::where('impersonator_id', $this->user_id)
                               ->active()
                               ->first();
    }

    


}
