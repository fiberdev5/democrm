<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LegalContent;
use Illuminate\Support\Facades\Log;


class LegalContentController extends Controller
{
    public function legalSettings()
    {
        $termsContent = LegalContent::where('type', 'terms')->first();
        $privacyContent = LegalContent::where('type', 'privacy')->first();
        
        return view('frontend.secure.general_settings.legal_settings', compact('termsContent', 'privacyContent'));
    }
    
    public function updateLegalSettings(Request $request)
    {
        try {
            $request->validate([
                'terms_content' => 'nullable|string',
                'privacy_content' => 'nullable|string',
            ]);
            
            // Kullanım Koşulları
            LegalContent::updateOrCreate(
                ['type' => 'terms'],
                ['content' => $request->terms_content ?? '']
            );
            
            // Gizlilik Politikası
            LegalContent::updateOrCreate(
                ['type' => 'privacy'],
                ['content' => $request->privacy_content ?? '']
            );
            
            $notification = [
                'message' => 'Yasal metinler başarıyla güncellendi!',
                'alert-type' => 'success'
            ];
            
            return redirect()->back()->with($notification);
            
        } catch (\Exception $e) {
            Log::error('Legal content update error: ' . $e->getMessage());
            
            $notification = [
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
                'alert-type' => 'error'
            ];
            
            return redirect()->back()->with($notification);
        }
    }
    
    // Popup için API endpoint
    public function getTermsContent()
    {
        $content = LegalContent::getTerms();
        return response()->json(['content' => $content]);
    }
    
    public function getPrivacyContent()
    {
        $content = LegalContent::getPrivacy();
        return response()->json(['content' => $content]);
    }
}
