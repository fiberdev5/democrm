<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdminInvoice extends Model
{
    
    use HasFactory;

    protected $table = 'super_admin_invoices';
    protected $guarded = [];

    // Firma bilgilerini getir
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'firma_id', 'id');
    }

    // Fatura ürünlerini getir
    public function invoice_products()
    {
        return $this->hasMany(SuperAdminInvoiceProduct::class, 'faturaid');
    }

    // Kaydı oluşturan admin kullanıcısı
    public function admin()
    {
        return $this->belongsTo(User::class, 'kayitAlan', 'id');
    }
}
