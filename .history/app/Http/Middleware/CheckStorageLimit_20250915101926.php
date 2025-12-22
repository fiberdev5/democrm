<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStorageLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Sadece dosya yükleme işlemleri için çalışsın
        if (!$this->hasFileUploads($request)) {
            return $next($request);
        }

        $user = auth()->user();
        
        if (!$user || !$user->tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkilendirme hatası'
            ], 403);
        }

        $tenant = $user->tenant;
        
        // Yüklenen tüm dosyaların toplam boyutunu hesapla
        $totalUploadSize = $this->calculateTotalUploadSize($request);
        
        // Storage kontrolü
        if (!$tenant->canUploadFile($totalUploadSize)) {
            $storageInfo = $tenant->getStorageInfo();
            
            return response()->json([
                'success' => false,
                'message' => 'Storage limiti aşıldı! Dosya yükleyemezsiniz.',
                'error_type' => 'storage_limit_exceeded',
                'storage_info' => $storageInfo
            ], 422);
        }
        
        // Storage kullanımı %80'i geçtiyse uyarı ver (ama devam et)
        if ($tenant->getStorageUsagePercentage() >= 80) {
            // Response'a uyarı bilgisi eklemek için request'e ekleyelim
            $request->attributes->set('storage_warning', true);
            $request->attributes->set('storage_info', $tenant->getStorageInfo());
        }

        return $next($request);
    }
    
    /**
     * Request'te dosya yüklemesi var mı kontrol et
     * 
     * @param Request $request
     * @return bool
     */
    private function hasFileUploads(Request $request)
    {
        // Yaygın dosya field isimleri
        $fileFields = ['belge', 'image', 'file', 'photo', 'attachment', 'document'];
        
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                return true;
            }
        }
        
        // Çoklu dosya yüklemeleri için
        foreach ($request->allFiles() as $files) {
            if (!empty($files)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Yüklenen tüm dosyaların toplam boyutunu hesapla
     * 
     * @param Request $request
     * @return int bytes
     */
    private function calculateTotalUploadSize(Request $request)
    {
        $totalSize = 0;
        
        foreach ($request->allFiles() as $files) {
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $totalSize += $file->getSize();
                }
            }
        }
        
        return $totalSize;
    }
}
