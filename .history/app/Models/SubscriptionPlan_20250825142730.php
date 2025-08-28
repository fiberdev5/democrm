<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'billing_cycle',
        'features', 'limits', 'trial_days', 'is_active', 'is_popular', 'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_popular' => 'boolean'
    ];

    // İlişkiler
    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class, 'plan_id');
    }

    // Özellik Kontrolleri
    public function hasFeature($feature)
    {
        $features = $this->features ?? [];
        return in_array($feature, $features);
    }

    public function getFeatureLimit($feature)
    {
        $limits = $this->limits ?? [];
        return $limits[$feature] ?? 0;
    }

    public function getFormattedPrice()
    {
        return number_format($this->price, 2, ',', '.') . ' ₺';
    }

    public function getBillingCycleText()
    {
        return match($this->billing_cycle) {
            'monthly' => 'Aylık',
            'quarterly' => '3 Aylık',
            'yearly' => 'Yıllık',
            default => $this->billing_cycle
        };
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }
}
