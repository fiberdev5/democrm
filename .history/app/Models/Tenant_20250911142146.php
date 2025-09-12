<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ils() 
    {
        return $this->belongsTo(Il::class, 'il', 'id');
    }

    public function ilces()
    {
        return $this->belongsTo(Ilce::class, 'ilce','id');
    }

    // Firmanın aşamaları
    public function serviceStages()
    {
        return $this->hasMany(ServiceStage::class, 'firma_id', 'id');
    }

    // Varsayılan aşamaları getir
    public static function defaultStages()
    {
        return ServiceStage::whereNull('firma_id')->get();
    }

    protected $casts = [
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'trial_used' => 'boolean'
    ];

    // İlişkiler
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(TenantSubscription::class)->latest();
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(TenantSubscription::class)
                    ->where('status', 'active')
                    ->latestOfMany();
    }

    public function plan()
    {
        return $this->activeSubscription?->plansubs;
    }

    public function hasFeature($key)
    {
        return !empty($this->plan()?->features[$key]) && $this->plan()->features[$key] === true;
    }

    // Abonelik Durumu Kontrolleri
    public function isOnTrial()
    {
        return $this->subscription_status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription()
    {
        return in_array($this->subscription_status, ['trial', 'active']) && 
               $this->subscription_ends_at &&
               $this->subscription_ends_at->isFuture();
    }

    public function isExpired()
    {
        return $this->subscription_status === 'expired' || 
               ($this->subscription_ends_at && $this->subscription_ends_at->isPast());
    }

    public function canAccessFeature($feature)
    {
        if (!$this->hasActiveSubscription()) {
            return false;
        }

        $subscription = $this->currentSubscription;
        if (!$subscription || !$subscription->plan) {
            return false;
        }

        return $subscription->plan->hasFeature($feature);
    }

    public function getFeatureLimit($feature)
    {
        $subscription = $this->currentSubscription;
        if (!$subscription || !$subscription->plan) {
            return 0;
        }

        return $subscription->plan->getFeatureLimit($feature);
    }

    public function getRemainingTrialDays()
    {
        if (!$this->isOnTrial()) {
            return 0;
        }

        return $this->trial_ends_at->diffInDays(now());
    }

    public function startTrial()
    {
        $this->update([
            'trial_starts_at' => now(),
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trial',
            'subscription_ends_at' => now()->addDays(14),
            'trial_used' => true
        ]);

        // Varsayılan trial planı ile subscription oluştur
        $trialPlan = SubscriptionPlan::where('slug', 'trial')->first();
        if ($trialPlan) {
            $this->subscriptions()->create([
                'plan_id' => $trialPlan->id,
                'status' => 'trial',
                'starts_at' => now(),
                'ends_at' => now()->addDays(14),
                'trial_ends_at' => now()->addDays(14)
            ]);
        }
    }

     public function canAccessDealersModule()
    {
        // 1. Check if the tenant is on trial
        if ($this->isOnTrial()) {
            // During trial, check the 'dealers' limit directly from the 'limits' column of the trial plan
            // Assuming 'trial' plan slug exists and has limits defined.
            $trialPlan = $this->subscriptions()->where('status', 'trial')->first()?->plansubs;

            if ($trialPlan && isset($trialPlan->limits['dealers'])) {
                return (int) $trialPlan->limits['dealers'] > 0;
            }
            // If no trial plan or limits defined for trial, default to false
            return false;
        }

        // 2. Check for active subscription
        $activeSubscription = $this->activeSubscription;

        if ($activeSubscription && $activeSubscription->plansubs) {
            $plan = $activeSubscription->plansubs;

            // Ensure 'limits' is an array and 'dealers' key exists
            if (isset($plan->limits['dealers'])) {
                return (int) $plan->limits['dealers'] > 0;
            }
        }

        // If no active subscription or no plan/limits, or dealers limit is 0
        return false;
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOnTrial($query)
    {
        return $query->where('subscription_status', 'trial')
                    ->where('trial_ends_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('subscription_status', 'expired')
                    ->orWhere('subscription_ends_at', '<', now());
    }
}
