<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'subscription_id', 'payment_id', 'amount', 'currency',
        'status', 'payment_method', 'transaction_id', 'gateway',
        'gateway_response', 'paid_at', 'failure_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime'
    ];

    // İlişkiler
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription()
    {
        return $this->belongsTo(TenantSubscription::class, 'subscription_id');
    }

    public function getFormattedAmount()
    {
        return number_format($this->amount, 2, ',', '.') . ' ' . $this->currency;
    }

    // Scope'lar
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
