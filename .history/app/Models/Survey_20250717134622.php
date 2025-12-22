<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Survey extends Model
{
    use HasFactory;
    protected $guarded = [];

    //Anketi ekleyen kullanıcı 
    public function ekleyenUser()
    {
        return $this->belongsTo(User::class, 'ekleyen', 'user_id');
    }
  
    //Anketi yapılan personel
    public function personelUser()
    {
        return $this->belongsTo(User::class, 'personel', 'user_id');
    }
 
    //Anketin bağlı olduğu servis 
    public function servis()
    {
        return $this->belongsTo(Service::class, 'servisid', 'id');
    }

     // Soru cevaplarını human-readable formatta döndüren accessor'lar
    public function getSoru1TextAttribute($value)
    {
        return $value;
    }

    public function getSoru1LabelAttribute()
    {
        return match($this->soru1) {
            0 => 'Belli Değil',
            1 => 'Evet',
            2 => 'Hayır',
            default => 'Bilinmiyor'
        };
    }

    public function getSoru2LabelAttribute()
    {
        return match($this->soru2) {
            0 => 'Belli Değil',
            1 => 'Evet',
            2 => 'Hayır',
            default => 'Bilinmiyor'
        };
    }

    public function getSoru3LabelAttribute()
    {
        return match($this->soru3) {
            0 => 'Belli Değil',
            1 => 'Evet',
            2 => 'Hayır',
            default => 'Bilinmiyor'
        };
    }

    public function getSoru5LabelAttribute()
    {
        return match($this->soru5) {
            0 => 'Belli Değil',
            1 => 'Evet',
            2 => 'Hayır',
            default => 'Bilinmiyor'
        };
    }

    // Scope'lar
    public function scopeByPersonel($query, $personelId)
    {
        return $query->where('personel', $personelId);
    }

    public function scopeByServis($query, $servisId)
    {
        return $query->where('servisid', $servisId);
    }



}
