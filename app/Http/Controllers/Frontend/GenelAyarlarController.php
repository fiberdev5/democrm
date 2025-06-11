<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Il;
use App\Models\Tenant;
use Illuminate\Http\Request;
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
                'personelSayisi' => $request->personel_sayisi,
                'bayiSayisi' => $request->bayiSayisi,
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
                'personelSayisi' => $request->personel_sayisi,
                'bayiSayisi' => $request->bayiSayisi,
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
}
