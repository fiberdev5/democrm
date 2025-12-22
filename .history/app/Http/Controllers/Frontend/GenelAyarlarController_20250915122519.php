<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Il;
use App\Models\ServicePhoto;
use App\Models\Tenant;
use App\Models\TenantPrim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;

class GenelAyarlarController extends Controller
{
    public function GeneralSettings($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        return view('frontend.secure.general_settings.settings', compact('firma'));
    }

    public function CompanySettings($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $countries = Il::orderBy('name', 'ASC')->get();
        return view('frontend.secure.general_settings.company_settings', compact('firma','countries'));
    }

    public function UpdateCompanySet(Request $request, $tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $validateData = $request->validate([
            'logo'=> 'max:2000',
        ]);
        $company_settings_id = $request->id;

        if($request->file('logo')) {
            $image = $request->file('logo');
            $extension = $request->file('logo')->extension();
            if($extension != "jpg" && $extension != "png" && $extension != "jpeg"){
                $notification = array(
                    'message' => ' Dosya uzantısı sadece jpg,png,jpeg olmalı',
                    'alert-type' => 'warning'
                );
                return redirect()->back()->with($notification);
            }

            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            Image::make($image)->save('upload/company_imgs/' . $name_gen);
            $save_url = 'upload/company_imgs/' . $name_gen;
            
            Tenant::findOrFail($company_settings_id)->update([
                'kayitTarihi' => $request->kayitTarihi,
                'firma_adi' => $request->company_name,
                'tel1' => $request->tel1,
                'tel2' => $request->tel2,
                'il' => $request->il,
                'ilce' => $request->ilce,
                'adres' => $request->company_address,
                'eposta' => $request->company_email,
                'webSitesi' => $request->web_sitesi,
                'iban' => $request->iban,
                'vergiNo' => $request->tax_no,
                'vergiDairesi' => $request->tax_office,
                'logo' => $save_url,
            ]);

            $notification = array(
                'message' => 'Firma bilgileri başarıyla güncellendi.',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } else{
            Tenant::findOrFail($company_settings_id)->update([
                'kayitTarihi' => $request->kayitTarihi,
                'firma_adi' => $request->company_name,
                'tel1' => $request->tel1,
                'tel2' => $request->tel2,
                'il' => $request->il,
                'ilce' => $request->ilce,
                'adres' => $request->company_address,
                'eposta' => $request->company_email,
                'webSitesi' => $request->web_sitesi,
                'iban' => $request->iban,
                'vergiNo' => $request->tax_no,
                'vergiDairesi' => $request->tax_office,
            ]);

            $notification = array(
                'message' => 'Firma bilgileri başarıyla güncellendi.',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
        
    }

    public function SmsSettings($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        return view('frontend.secure.general_settings.sms_settings', compact('firma'));
    }

    public function UpdateSms(Request $request,$tenant_id) {
        $sms_settings_id = $request->id;
        Tenant::findOrFail($sms_settings_id)->update([
            'smsKullanici' => $request->smsKullanici,
            'smsSifre' => $request->smsSifre,
            'smsGonderici' => $request->smsGonderici,
            'smsKaraliste' => $request->smsKaraliste,
        ]);

        return response()->json(['success', 'Sms entegrasyon bilgileri güncellendi.']);
    }

    public function PrimSettings($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $prim = TenantPrim::where('firma_id', $tenant_id)->first();
        return view('frontend.secure.general_settings.prim_settings', compact('firma','prim'));
    }

    public function UpdateFirmPrim(Request $request, $tenant_id) {
        $firm_id = $request->id;
        TenantPrim::findOrFail($firm_id)->update([
            'operatorPrim' => $request->operatorPrim,
            'operatorPrimTutari' => $request->operatorPrimTutari,
            'teknisyenPrim' => $request->teknisyenPrim,
            'teknisyenPrimTutari' => $request->teknisyenPrimTutari,
            'atolyePrim' => $request->atolyePrim,
            'atolyePrimTutari' => $request->atolyePrimTutari,
        ]);
        return response()->json(['success', 'Prim sistemi bilgileri güncellendi.']);
    }

    public function getStorageInfo($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $storageInfo = auth()->user()->tenant->getStorageInfo();
        return view('frontend.secure.general_settings.storage_info', compact('firma','storageInfo'));
    }

    public function getStorageDetails($tenant_id)
{
    try {
        $tenant = Tenant::find($tenant_id);
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı.'
            ], 404);
        }

        // Temel storage bilgileri
        $storageInfo = $tenant->getStorageInfo();
        
        // Detaylı dosya breakdown'ı
        $details = [
            'service_photos' => $this->getServicePhotosBreakdown($tenant),
            'stock_photos' => $this->getStockPhotosBreakdown($tenant),
            'other_files' => $this->getOtherFilesBreakdown($tenant),
            'recent_uploads' => $this->getRecentUploads($tenant, 10),
            'largest_files' => $this->getLargestFiles($tenant, 5),
            'monthly_usage' => $this->getMonthlyUsage($tenant)
        ];
        
        return response()->json([
            'success' => true,
            'storage_info' => $storageInfo,
            'details' => $details
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Storage details error', [
            'tenant_id' => $tenant_id,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Storage detayları alınırken hata oluştu.'
        ], 500);
    }
}

/**
 * Servis fotoğrafları breakdown'ı
 */
private function getServicePhotosBreakdown($tenant)
{
    $photos = ServicePhoto::where('firma_id', $tenant->id)
                         ->selectRaw('COUNT(*) as count, SUM(file_size) as total_size, AVG(file_size) as avg_size')
                         ->first();
    
    $recentPhotos = ServicePhoto::where('firma_id', $tenant->id)
                              ->where('created_at', '>=', now()->subDays(30))
                              ->count();
                              
    $photosPerService = ServicePhoto::where('firma_id', $tenant->id)
                                  ->selectRaw('servisid, COUNT(*) as photo_count')
                                  ->groupBy('servisid')
                                  ->orderByDesc('photo_count')
                                  ->take(5)
                                  ->get();

    return [
        'count' => $photos->count ?? 0,
        'total_size' => $photos->total_size ?? 0,
        'total_size_formatted' => $this->formatBytes($photos->total_size ?? 0),
        'average_size' => $photos->avg_size ?? 0,
        'average_size_formatted' => $this->formatBytes($photos->avg_size ?? 0),
        'recent_uploads_30_days' => $recentPhotos,
        'top_services' => $photosPerService->map(function($item) {
            return [
                'service_id' => $item->servisid,
                'photo_count' => $item->photo_count,
                // Servis adını da ekleyebilirsiniz
                // 'service_name' => Service::find($item->servisid)?->name
            ];
        })
    ];
}

/**
 * Stok fotoğrafları breakdown'ı
 */
private function getStockPhotosBreakdown($tenant)
{
    // Eğer StockPhoto modeliniz varsa burayı güncelleyin
    $stockPath = storage_path("app/public/stocks/firma_{$tenant->firma_slug}");
    
    if (!is_dir($stockPath)) {
        return [
            'count' => 0,
            'total_size' => 0,
            'total_size_formatted' => '0 B',
            'average_size' => 0,
            'average_size_formatted' => '0 B'
        ];
    }
    
    $files = [];
    $totalSize = 0;
    
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($stockPath, \RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $size = $file->getSize();
            $files[] = [
                'name' => $file->getFilename(),
                'size' => $size,
                'path' => str_replace(storage_path('app/public/'), '', $file->getPathname()),
                'modified' => $file->getMTime()
            ];
            $totalSize += $size;
        }
    }
    
    $count = count($files);
    $avgSize = $count > 0 ? $totalSize / $count : 0;
    
    return [
        'count' => $count,
        'total_size' => $totalSize,
        'total_size_formatted' => $this->formatBytes($totalSize),
        'average_size' => $avgSize,
        'average_size_formatted' => $this->formatBytes($avgSize),
        'files' => collect($files)->sortByDesc('size')->take(5)->values()
    ];
}

/**
 * Diğer dosyalar breakdown'ı - Ticket resimleri dahil
 */
private function getOtherFilesBreakdown($tenant)
{
    try {
        $firmSlug = $tenant->firma_slug ?? $tenant->id;
        $otherPaths = [
            'documents' => storage_path("app/public/documents/firma_{$firmSlug}"),
            'reports' => storage_path("app/public/reports/firma_{$firmSlug}"),
            'attachments' => storage_path("app/public/attachments/firma_{$firmSlug}"),
            'support_attachments' => storage_path("app/public/support_attachments/firma_{$firmSlug}"), // Ticket resimleri
        ];
        
        $breakdown = [];
        $totalSize = 0;
        $totalCount = 0;
        
        foreach ($otherPaths as $type => $path) {
            try {
                if (is_dir($path)) {
                    $size = $this->safeCalculateDirectorySize($path);
                    $count = $this->safeCountFilesInDirectory($path);
                    
                    // Özel olarak ticket resimleri için detay
                    if ($type === 'support_attachments') {
                        $ticketDetails = $this->getTicketAttachmentsDetails($path);
                        $breakdown[$type] = [
                            'count' => $count,
                            'size' => $size,
                            'size_formatted' => $this->formatBytes($size),
                            'ticket_count' => $ticketDetails['ticket_count'],
                            'recent_tickets' => $ticketDetails['recent_tickets']
                        ];
                    } else {
                        $breakdown[$type] = [
                            'count' => $count,
                            'size' => $size,
                            'size_formatted' => $this->formatBytes($size)
                        ];
                    }
                    
                    $totalSize += $size;
                    $totalCount += $count;
                } else {
                    $breakdown[$type] = [
                        'count' => 0,
                        'size' => 0,
                        'size_formatted' => '0 B'
                    ];
                    
                    if ($type === 'support_attachments') {
                        $breakdown[$type]['ticket_count'] = 0;
                        $breakdown[$type]['recent_tickets'] = [];
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Other files breakdown error for {$type}", ['error' => $e->getMessage()]);
                $breakdown[$type] = [
                    'count' => 0,
                    'size' => 0,
                    'size_formatted' => '0 B'
                ];
                
                if ($type === 'support_attachments') {
                    $breakdown[$type]['ticket_count'] = 0;
                    $breakdown[$type]['recent_tickets'] = [];
                }
            }
        }
        
        return [
            'total_count' => $totalCount,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'breakdown' => $breakdown
        ];
        
    } catch (\Exception $e) {
        \Log::error('Other files breakdown error', ['error' => $e->getMessage()]);
        return [
            'total_count' => 0,
            'total_size' => 0,
            'total_size_formatted' => '0 B',
            'breakdown' => []
        ];
    }
}

/**
 * Ticket attachments detaylarını getir
 */
private function getTicketAttachmentsDetails($supportAttachmentsPath)
{
    $ticketFolders = [];
    $ticketCount = 0;
    
    try {
        if (!is_dir($supportAttachmentsPath)) {
            return [
                'ticket_count' => 0,
                'recent_tickets' => []
            ];
        }
        
        // Ticket klasörlerini tara
        $folders = glob($supportAttachmentsPath . '/ticket_*', GLOB_ONLYDIR);
        
        foreach ($folders as $folder) {
            $ticketNumber = str_replace($supportAttachmentsPath . '/ticket_', '', $folder);
            $folderSize = $this->safeCalculateDirectorySize($folder);
            $fileCount = $this->safeCountFilesInDirectory($folder);
            
            if ($fileCount > 0) {
                $ticketFolders[] = [
                    'ticket_number' => $ticketNumber,
                    'file_count' => $fileCount,
                    'folder_size' => $folderSize,
                    'folder_size_formatted' => $this->formatBytes($folderSize),
                    'last_modified' => filemtime($folder)
                ];
                $ticketCount++;
            }
        }
        
        // Son değiştirilen ticket'ları sırala
        usort($ticketFolders, function($a, $b) {
            return $b['last_modified'] - $a['last_modified'];
        });
        
        // Son 5 ticket'ı al
        $recentTickets = array_slice($ticketFolders, 0, 5);
        
        // Tarihleri formatla
        foreach ($recentTickets as &$ticket) {
            $ticket['last_modified_formatted'] = date('d.m.Y H:i', $ticket['last_modified']);
            unset($ticket['last_modified']); // Raw timestamp'i kaldır
        }
        
        return [
            'ticket_count' => $ticketCount,
            'recent_tickets' => $recentTickets
        ];
        
    } catch (\Exception $e) {
        \Log::warning('Ticket attachments details error', ['error' => $e->getMessage()]);
        return [
            'ticket_count' => 0,
            'recent_tickets' => []
        ];
    }
}

private function formatBytes($bytes, $precision = 2)
{
    if ($bytes === null || $bytes < 0) {
        return '0 B';
    }
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
/**
 * Son yüklenen dosyaları getir
 */
private function getRecentUploads($tenant, $limit = 10)
{
    $recentFiles = [];
    
    // Servis fotoğrafları
    $recentServicePhotos = ServicePhoto::where('firma_id', $tenant->id)
                                     ->orderByDesc('created_at')
                                     ->take($limit)
                                     ->get()
                                     ->map(function($photo) {
                                         return [
                                             'id' => $photo->id,
                                             'type' => 'service_photo',
                                             'name' => $photo->original_name ?? 'Servis Fotoğrafı',
                                             'size' => $photo->file_size,
                                             'size_formatted' => $this->formatBytes($photo->file_size),
                                             'uploaded_at' => $photo->created_at,
                                             'url' => Storage::url($photo->resimyol),
                                             'service_id' => $photo->servisid
                                         ];
                                     });
    
    // Diğer dosya türleri buraya eklenebilir
    // $recentStockPhotos = ...
    
    $recentFiles = $recentServicePhotos;
    
    return $recentFiles->sortByDesc('uploaded_at')->take($limit)->values();
}

/**
 * En büyük dosyaları getir
 */
private function getLargestFiles($tenant, $limit = 5)
{
    $largestFiles = [];
    
    // Servis fotoğrafları
    $largestServicePhotos = ServicePhoto::where('firma_id', $tenant->id)
                                      ->whereNotNull('file_size')
                                      ->orderByDesc('file_size')
                                      ->take($limit)
                                      ->get()
                                      ->map(function($photo) {
                                          return [
                                              'id' => $photo->id,
                                              'type' => 'service_photo',
                                              'name' => $photo->original_name ?? 'Servis Fotoğrafı',
                                              'size' => $photo->file_size,
                                              'size_formatted' => $this->formatBytes($photo->file_size),
                                              'uploaded_at' => $photo->created_at,
                                              'url' => Storage::url($photo->resimyol)
                                          ];
                                      });
    
    return $largestServicePhotos->take($limit)->values();
}

/**
 * Aylık kullanım istatistikleri
 */
private function getMonthlyUsage($tenant)
{
    $monthlyData = [];
    
    // Son 12 ayın verilerini al
    for ($i = 11; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // O aydaki yüklenen dosya sayısı ve boyutu
        $uploadCount = ServicePhoto::where('firma_id', $tenant->id)
                                 ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                 ->count();
                                 
        $uploadSize = ServicePhoto::where('firma_id', $tenant->id)
                                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                ->sum('file_size') ?? 0;
        
        $monthlyData[] = [
            'month' => $date->format('Y-m'),
            'month_name' => $date->format('M Y'),
            'upload_count' => $uploadCount,
            'upload_size' => $uploadSize,
            'upload_size_formatted' => $this->formatBytes($uploadSize)
        ];
    }
    
    return $monthlyData;
}

/**
 * Klasördeki dosya sayısını hesapla
 */
private function countFilesInDirectory($directory)
{
    if (!is_dir($directory)) {
        return 0;
    }
    
    $count = 0;
    
    try {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $count++;
            }
        }
    } catch (\Exception $e) {
        \Log::warning("File count error for directory: {$directory}", ['error' => $e->getMessage()]);
    }
    
    return $count;
}

/**
 * Klasörün boyutunu hesapla (private method zaten var ama yeniden tanımlayalım)
 */
private function calculateDirectorySize($directory)
{
    if (!is_dir($directory)) {
        return 0;
    }
    
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
        \Log::warning("Directory size calculation error: {$directory}", ['error' => $e->getMessage()]);
    }
    
    return $totalSize;
}

/**
 * File cleanup metodları
 */
public function cleanupStorageFiles($tenant_id, Request $request)
{
    try {
        $tenant = Tenant::findOrFail($tenant_id);
        $cleanupType = $request->get('type', 'orphaned'); // orphaned, old, large
        $daysOld = $request->get('days_old', 30);
        $minSizeKB = $request->get('min_size_kb', 1000); // 1MB
        
        $cleaned = 0;
        $freedSpace = 0;
        
        switch ($cleanupType) {
            case 'orphaned':
                [$cleaned, $freedSpace] = $this->cleanOrphanedFiles($tenant);
                break;
                
            case 'old':
                [$cleaned, $freedSpace] = $this->cleanOldFiles($tenant, $daysOld);
                break;
                
            case 'large':
                [$cleaned, $freedSpace] = $this->cleanLargeFiles($tenant, $minSizeKB * 1024);
                break;
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Temizlik işlemi tamamlandı',
            'cleaned_files' => $cleaned,
            'freed_space' => $this->formatBytes($freedSpace),
            'storage_info' => $tenant->getStorageInfo()
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Storage cleanup error', [
            'tenant_id' => $tenant_id,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Temizlik sırasında hata oluştu'
        ], 500);
    }
}

/**
 * Orphaned dosyaları temizle
 */
private function cleanOrphanedFiles($tenant)
{
    $cleaned = 0;
    $freedSpace = 0;
    
    // Veritabanında kaydı olmayan dosyaları bul
    $servicePhotosPath = storage_path("app/public/service_photos/firma_{$tenant->firma_slug}");
    
    if (is_dir($servicePhotosPath)) {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($servicePhotosPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace(storage_path('app/public/'), '', $file->getPathname());
                
                // Bu dosyanın veritabanında kaydı var mı?
                $exists = ServicePhoto::where('firma_id', $tenant->id)
                                    ->where('resimyol', $relativePath)
                                    ->exists();
                
                if (!$exists) {
                    $fileSize = $file->getSize();
                    if (unlink($file->getPathname())) {
                        $cleaned++;
                        $freedSpace += $fileSize;
                    }
                }
            }
        }
    }
    
    return [$cleaned, $freedSpace];
}

/**
 * Eski dosyaları temizle
 */
private function cleanOldFiles($tenant, $daysOld)
{
    $cleaned = 0;
    $freedSpace = 0;
    $cutoffDate = now()->subDays($daysOld);
    
    $oldPhotos = ServicePhoto::where('firma_id', $tenant->id)
                            ->where('created_at', '<', $cutoffDate)
                            ->get();
    
    foreach ($oldPhotos as $photo) {
        $filePath = storage_path('app/public/' . $photo->resimyol);
        
        if (file_exists($filePath)) {
            $fileSize = filesize($filePath);
            if (unlink($filePath)) {
                $freedSpace += $fileSize;
                $cleaned++;
            }
        }
        
        $photo->delete();
    }
    
    return [$cleaned, $freedSpace];
}

/**
 * Büyük dosyaları temizle
 */
private function cleanLargeFiles($tenant, $minSize)
{
    $cleaned = 0;
    $freedSpace = 0;
    
    $largePhotos = ServicePhoto::where('firma_id', $tenant->id)
                              ->where('file_size', '>', $minSize)
                              ->orderByDesc('file_size')
                              ->get();
    
    foreach ($largePhotos as $photo) {
        $filePath = storage_path('app/public/' . $photo->resimyol);
        
        if (file_exists($filePath)) {
            $fileSize = filesize($filePath);
            if (unlink($filePath)) {
                $freedSpace += $fileSize;
                $cleaned++;
            }
        }
        
        $photo->delete();
    }
    
    return [$cleaned, $freedSpace];
}
}
