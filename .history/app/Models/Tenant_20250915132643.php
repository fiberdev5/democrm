<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        // Eğer deneme sürecindeyse
        if ($this->isOnTrial()) {
            // Deneme sürecinde bayiSayisi kontrol edilir
            return $this->bayiSayisi > 0;
        }

        // Deneme süreci bittiyse aktif abonelik kontrol edilir
        if ($this->hasActiveSubscription()) {
            $subscription = $this->activeSubscription;
            
            if (!$subscription || !$subscription->plansubs) {
                return false;
            }

            // Abonelik planındaki limits kontrolü
            $limits = $subscription->plansubs->limits;
            
            if (is_string($limits)) {
                $limits = json_decode($limits, true);
            }

            // dealers limiti 0'dan büyükse modül görünür
            return isset($limits['dealers']) && $limits['dealers'] > 0;
        }

        // Hiç aktif abonelik yoksa modül görünmez
        return false;
    }

    /**
     * Bayiler modülü için mevcut limit değerini döndürür
     * 
     * @return int
     */
    public function getDealersLimit()
    {
        // Eğer deneme sürecindeyse bayiSayisi döndür
        if ($this->isOnTrial()) {
            return $this->bayiSayisi ?? 0;
        }

        // Aktif abonelik varsa plan limitini döndür
        if ($this->hasActiveSubscription()) {
            $subscription = $this->activeSubscription;
            
            if (!$subscription || !$subscription->plansubs) {
                return 0;
            }

            $limits = $subscription->plansubs->limits;
            
            if (is_string($limits)) {
                $limits = json_decode($limits, true);
            }

            return $limits['dealers'] ?? 0;
        }

        return 0;
    }

    public function getStorageLimit()
{
    // Deneme sürecindeyse
    if ($this->isOnTrial()) {
        $subscription = $this->subscriptions()->where('status', 'trial')->first();
        if ($subscription && $subscription->plansubs) {
            $limits = $subscription->plansubs->limits;
            if (is_string($limits)) {
                $limits = json_decode($limits, true);
            }
            return $limits['storage_gb'] ?? 0.5; // Trial için default 0.5 GB
        }
        return 0.5; // Trial default
    }

    // Aktif abonelik varsa
    if ($this->hasActiveSubscription()) {
        $subscription = $this->activeSubscription;
        
        if ($subscription && $subscription->plansubs) {
            $limits = $subscription->plansubs->limits;
            
            if (is_string($limits)) {
                $limits = json_decode($limits, true);
            }

            return $limits['storage_gb'] ?? 1; // Default 1GB
        }
    }

    return 0.1; // Aboneliği olmayan firmalar için minimal limit (100MB)
}

/**
 * Firmanın mevcut storage kullanımını hesaplar (GB cinsinden)
 * 
 * @return float
 */
public function getCurrentStorageUsage()
{
    $totalSizeBytes = 0;
    
    // Servis fotoğrafları
    $totalSizeBytes += $this->getServicePhotosSize();
    
    // Stok resimleri (varsa)
    $totalSizeBytes += $this->getStockPhotosSize();
    
    // Diğer dosyalar (belgeler, raporlar vs.)
    $totalSizeBytes += $this->getOtherFilesSize();
    
    // Bytes'ı GB'ye çevir
    return round($totalSizeBytes / (1024 * 1024 * 1024), 4);
}

/**
 * Servis fotoğraflarının toplam boyutunu hesaplar
 * 
 * @return int bytes cinsinden
 */
private function getServicePhotosSize()
{
    $totalSize = 0;
    
    // ServicePhoto modelinden dosya yollarını al
    $servicePhotos = ServicePhoto::where('firma_id', $this->id)->get();
    
    foreach ($servicePhotos as $photo) {
        $filePath = storage_path('app/public/' . $photo->resimyol);
        if (file_exists($filePath)) {
            $totalSize += filesize($filePath);
        }
    }
    
    return $totalSize;
}

/**
 * Stok resimlerinin toplam boyutunu hesaplar
 * 
 * @return int bytes cinsinden
 */
private function getStockPhotosSize()
{
    $totalSize = 0;
    
    // Stok fotoğrafları için model/tablo varsa buraya ekleyin
    // Örnek: StockPhoto, ProductImage vb.
    
    // Örnek implementasyon:
    /*
    $stockPhotos = StockPhoto::where('firma_id', $this->id)->get();
    foreach ($stockPhotos as $photo) {
        $filePath = storage_path('app/public/' . $photo->image_path);
        if (file_exists($filePath)) {
            $totalSize += filesize($filePath);
        }
    }
    */
    
    // Şimdilik klasör bazlı hesaplama
    $stockPath = storage_path("app/public/stock/firma_{$this->firma_slug}");
    if (is_dir($stockPath)) {
        $totalSize += $this->calculateDirectorySize($stockPath);
    }
    
    return $totalSize;
}

private function getTicketAttachmentsSize()
{
    $totalSize = 0;
    
    try {
        // support_ticket_replies tablosundan bu tenant'a ait ticket'ları al
        $ticketReplies = DB::table('support_ticket_replies')
                          ->whereJsonLength('attachments', '>', 0) // attachments boş olmayan
                          ->get();
        
        foreach ($ticketReplies as $reply) {
            if (!empty($reply->attachments)) {
                $attachments = json_decode($reply->attachments, true);
                
                if (is_array($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (isset($attachment['path'])) {
                            // Path'in bu tenant'a ait olup olmadığını kontrol et
                            $firmSlug = $this->firma_slug ?? $this->id;
                            if (str_contains($attachment['path'], "firma_{$firmSlug}")) {
                                $filePath = storage_path('app/public/' . $attachment['path']);
                                if (file_exists($filePath)) {
                                    $totalSize += filesize($filePath);
                                }
                            }
                        }
                    }
                }
            }
        }
        
    } catch (\Exception $e) {
        \Log::warning('Ticket attachments size calculation error', ['error' => $e->getMessage()]);
    }
    
    return $totalSize;
}

/**
 * Diğer dosyaların toplam boyutunu hesaplar
 * 
 * @return int bytes cinsinden
 */
private function getOtherFilesSize()
{
    $totalSize = 0;
    
    // Firm slug kontrolü
    $firmSlug = $this->firma_slug ?? $this->id;
    
    // Klasör bazlı dosyalar
    $otherPaths = [
        storage_path("app/public/documents/firma_{$firmSlug}"),
        storage_path("app/public/reports/firma_{$firmSlug}"),
        storage_path("app/public/attachments/firma_{$firmSlug}"),
    ];
    
    foreach ($otherPaths as $path) {
        if (is_dir($path)) {
            $totalSize += $this->calculateDirectorySize($path);
        }
    }
    
    // Ticket attachments'ları JSON'dan hesapla
    $totalSize += $this->getTicketAttachmentsSize();
    
    return $totalSize;
}

/**
 * Klasörün toplam boyutunu hesaplar
 * 
 * @param string $directory
 * @return int bytes
 */
private function calculateDirectorySize($directory)
{
    $totalSize = 0;
    
    try {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $totalSize += $file->getSize();
            }
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::warning("Storage calculation error for directory: {$directory}", ['error' => $e->getMessage()]);
    }
    
    return $totalSize;
}

/**
 * Kalan storage alanını döndürür (GB)
 * 
 * @return float
 */
public function getRemainingStorage()
{
    $limit = $this->getStorageLimit();
    $used = $this->getCurrentStorageUsage();
    
    return max(0, round($limit - $used, 4));
}

/**
 * Storage limitine ulaşılıp ulaşılmadığını kontrol eder
 * 
 * @return bool
 */
public function hasReachedStorageLimit()
{
    return $this->getCurrentStorageUsage() >= $this->getStorageLimit();
}

/**
 * Belirli boyutta dosya yüklenebilir mi kontrolü
 * 
 * @param int $fileSizeInBytes
 * @return bool
 */
public function canUploadFile($fileSizeInBytes)
{
    $fileSizeInGB = $fileSizeInBytes / (1024 * 1024 * 1024);
    $currentUsage = $this->getCurrentStorageUsage();
    $limit = $this->getStorageLimit();
    
    return ($currentUsage + $fileSizeInGB) <= $limit;
}

/**
 * Storage kullanım yüzdesini döndürür
 * 
 * @return float
 */
public function getStorageUsagePercentage()
{
    $limit = $this->getStorageLimit();
    if ($limit == 0) return 100;
    
    $used = $this->getCurrentStorageUsage();
    return round(($used / $limit) * 100, 1);
}

/**
 * Storage bilgilerini detaylı olarak döndürür
 * 
 * @return array
 */
public function getStorageInfo()
{
    $currentUsage = $this->getCurrentStorageUsage();
    $limit = $this->getStorageLimit();
    $remaining = $this->getRemainingStorage();
    $percentage = $this->getStorageUsagePercentage();
    
    return [
        'current_usage_gb' => $currentUsage,
        'current_usage_formatted' => $this->formatBytes($currentUsage * 1024 * 1024 * 1024),
        'limit_gb' => $limit,
        'limit_formatted' => $this->formatBytes($limit * 1024 * 1024 * 1024),
        'remaining_gb' => $remaining,
        'remaining_formatted' => $this->formatBytes($remaining * 1024 * 1024 * 1024),
        'usage_percentage' => $percentage,
        'is_limit_reached' => $this->hasReachedStorageLimit(),
        'warning_threshold' => $percentage >= 80, // %80'e ulaştığında uyarı
        'danger_threshold' => $percentage >= 95,  // %95'e ulaştığında tehlikeli
    ];
}

/**
 * Byte'ları okunabilir formata çevirir
 * 
 * @param int $bytes
 * @param int $precision
 * @return string
 */
private function formatBytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
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
