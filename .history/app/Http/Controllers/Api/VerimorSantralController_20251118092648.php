<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerimorSantralController extends Controller
{
    /**
     * Web telefonu sayfasını göster
     */
    public function index()
    {
        $tenantId = Auth::user()->firma_id;
        $service = new VerimorSantralService($tenantId);
        
        // Token al
        $result = $service->getWebphoneToken();
        
        if (!$result['success']) {
            return view('integrations.verimor-santral.error', [
                'error' => $result['message']
            ]);
        }
        
        return view('integrations.verimor-santral.index', [
            'iframeUrl' => $service->getIframeUrl($result['token']),
            'token' => $result['token'],
            'expiresAt' => $result['expires_at']
        ]);
    }
    
    /**
     * AJAX ile iframe HTML döndür (modal için)
     */
    public function getIframe(Request $request)
    {
        $tenantId = Auth::user()->firma_id;
        $service = new VerimorSantralService($tenantId);
        
        $width = $request->input('width', 275);
        $height = $request->input('height', 700);
        
        $html = $service->getIframeHtml($width, $height);
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
    
    /**
     * Token yenileme
     */
    public function refreshToken(Request $request)
    {
        $tenantId = Auth::user()->firma_id;
        $service = new VerimorSantralService($tenantId);
        
        // Mevcut token'ı sil ve yeni al
        \App\Models\VerimorWebphoneToken::where('tenant_id', $tenantId)->delete();
        
        $result = $service->getWebphoneToken();
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Token yenilendi',
                'iframe_url' => $service->getIframeUrl($result['token'])
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }
    
    /**
     * Bağlantı testi
     */
    public function testConnection()
    {
        $tenantId = Auth::user()->firma_id;
        $service = new VerimorSantralService($tenantId);
        
        $result = $service->testConnection();
        
        return response()->json($result);
    }
}
