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
    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');
    }
    public function canImpersonate($user = null)

    {
         // Super Admin herkeşi impersonate edebilir
        if ($this->isSuperAdmin()) {
            return true;
        }
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

    //impersonate edilebilir kullanıcıları getir
    public function getImpersonatableUsers(?int $specificTenantId = null) // Yeni parametre
    {
        $query = User::query();

        // Eğer specificTenantId verilmişse ona göre filtrele, yoksa kendi tenant'ına göre filtrele
        if ($specificTenantId) {
            $query->where('tenant_id', $specificTenantId);
        } else {
            // Eğer hiçbir tenantId belirtilmezse, default olarak çağıran kullanıcının tenant_id'si
            // ya da bu satırı kaldırıp tüm tenantları getirebilirsiniz.
            // Mevcut isteğiniz için, bu else bloğunu yorum satırı yapabiliriz.
            // $query->where('tenant_id', $this->tenant_id); 
        }
        
        // Kendisi dışındaki kullanıcılar
        $query->where('user_id', '!=', $this->user_id)
            ->where('status', 1)
            ->whereNull('ayrilmaTarihi')
            //    ->whereHas('roles', function($q) {
            //        $q->whereNotIn('name', ['Admin', 'Patron']);
            //    })
            ->with('roles')
            ->orderBy('name');

        return $query->get()->filter(function($user) {
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
