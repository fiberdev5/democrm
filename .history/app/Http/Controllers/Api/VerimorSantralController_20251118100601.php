<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerimorSantralController extends Controller
{
    /**
     * Web telefonu sayfasını göster
     */
    public function showWebphone()
    {
        $tenantId = Auth::user()->firma_id;
        $service = new VerimorSantralService($tenantId);
        
        // Token al
        $result = $service->getWebphoneToken();
        
        if (!$result['success']) {
            return view('tenant.integrations.verimor-santral.error', [
                'error' => $result['message']
            ]);
        }
        
        return view('tenant.integrations.verimor-santral.webphone', [
            'iframeUrl' => $service->getIframeUrl($result['token']),
            'token' => $result['token'],
            'expiresAt' => $result['expires_at'],
            'fromCache' => $result['from_cache'] ?? false
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
        
        $result = $service->getWebphoneToken();
        
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
        
        $iframeUrl = $service->getIframeUrl($result['token']);
        
        $html = sprintf(
            '<iframe id="verimorWebphone" style="border: none;" src="%s" width="%spx" height="%spx" allow="microphone"></iframe>',
            $iframeUrl,
            $width,
            $height
        );
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'iframe_url' => $iframeUrl,
            'expires_at' => $result['expires_at']->format('d.m.Y H:i')
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
        VerimorWebphoneToken::where('tenant_id', $tenantId)->delete();
        
        $result = $service->getWebphoneToken();
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Token başarıyla yenilendi',
                'iframe_url' => $service->getIframeUrl($result['token']),
                'expires_at' => $result['expires_at']->format('d.m.Y H:i')
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
