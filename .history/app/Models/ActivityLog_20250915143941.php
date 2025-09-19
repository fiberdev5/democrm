<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'user_name',
        'user_role',
        'ip_address',
        'action',
        'module',
        'description',
        'old_values',
        'new_values',
        'reference_table',
        'reference_id',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // İlişkiler
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    // Scope'lar
    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    // Yardımcı metodlar
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d.m.Y H:i:s');
    }

    
    public function getFormattedDescriptionAttribute()
    {
        // user_name alanını kullan, eğer yoksa user ilişkisinden name'i çek
        $userName = $this->user_name ?? ($this->user ? $this->user->name : '');
        
        $userInfo = $userName ? 
            "{$this->ip_address} - {$this->user_id} - {$userName} - {$this->formatted_date} - " : 
            "{$this->ip_address} - - {$this->formatted_date} - ";
        
        return $userInfo . $this->description;
    }

        // Müşteri Log Metodları
    public static function logCustomerCreated($customerId, $customerName = null)
    {
        $description = $customerName 
            ? "MusteriID: {$customerId} Müşteri Kaydı Oluşturuldu: {$customerName}"
            : "MusteriID: {$customerId} Müşteri Kaydı Oluşturuldu";
            
        self::log('customer_created', $description, [
            'module' => 'customer',
            'reference_table' => 'customers',
            'reference_id' => $customerId
        ]);
    }

    public static function logCustomerUpdated($customerId, $customerName = null)
    {
        $description = $customerName 
            ? "MusteriID: {$customerId} Müşteri Bilgileri Güncellendi: {$customerName}"
            : "MusteriID: {$customerId} Müşteri Bilgileri Güncellendi";
            
        self::log('customer_updated', $description, [
            'module' => 'customer',
            'reference_table' => 'customers',
            'reference_id' => $customerId
        ]);
    }

    public static function logCustomerDeleted($customerId, $customerName = null)
    {
        $description = $customerName 
            ? "MusteriID: {$customerId} Müşteri Kaydı Silindi: {$customerName}"
            : "MusteriID: {$customerId} Müşteri Kaydı Silindi";
            
        self::log('customer_deleted', $description, [
            'module' => 'customer',
            'reference_table' => 'customers',
            'reference_id' => $customerId
        ]);
    }
    
}