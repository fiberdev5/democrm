namespace App\Services;

use App\Models\Service;
use App\Models\ServiceStageAnswer;
use App\Models\ServicePlan;
use App\Models\StageQuestion;
use App\Models\User;
use Illuminate\Support\Carbon;

class ServiceFilter
{
    public static function getYetkiliServisIDleri($user)
    {
        $yetkiliServisIDler = [];

        // Kullanıcının cevap verdiği tüm cevap kayıtları (tekil servisid'lere göre gruplanmış)
        $cevaplar = ServiceStageAnswer::where('cevap', $user->user_id)
            ->select('id', 'servisid', 'planid', 'soruid')
            ->get()
            ->groupBy('servisid');

        foreach ($cevaplar as $servisID => $cevapListesi) {
            $cevapRow = $cevapListesi->last(); // En güncel olanı alalım (gerekirse sıralanabilir)
            $plan = ServicePlan::find($cevapRow->planid);
            $soru = StageQuestion::find($cevapRow->soruid);

            if (!$plan || !$soru) continue;

            // Grup kontrolü
            if (str_contains($soru->cevapTuru, 'Grup')) {
                if ($plan->tarihDurum) {
                    // Plan tarihli ise "[Tarih]" olan cevapları bul
                    $tarihCevap = ServiceStageAnswer::where('planid', $plan->id)
                        ->where('cevapText', '[Tarih]')
                        ->first();

                    if ($tarihCevap) {
                        $bugun = strtotime(now()->format('d.m.Y'));
                        $tarih = strtotime(str_replace("/", ".", $tarihCevap->cevap));

                        if ($bugun == $tarih) {
                            $yetkiliServisIDler[] = $servisID;
                        }
                    }
                } else {
                    // Tarih yoksa, servis planı son durumla eşleşiyorsa göster
                    $servis = Service::find($servisID);
                    if ($servis && $servis->planDurum == $plan->id) {
                        $yetkiliServisIDler[] = $servisID;
                    } else {
                        // Bugünkü plan onunsa ve grubu depo değilse yine göster
                        if ($plan->pid == $user->user_id && $user->grup != 249) {
                            $planTarih = Carbon::parse($plan->tarih)->format('Y-m-d');
                            if ($planTarih == now()->format('Y-m-d')) {
                                $yetkiliServisIDler[] = $servisID;
                            }
                        }
                    }
                }
            }

            // Bayi kontrolü (istersen burada `Bayi` için de ek filtre yazabiliriz)
            if (str_contains($soru->cevapTuru, '[Bayi]')) {
                $yetkiliServisIDler[] = $servisID;
            }
        }

        return array_unique($yetkiliServisIDler);
    }
}
