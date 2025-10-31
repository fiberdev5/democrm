<link href="{{ asset('frontend/css/add_service.css') }}" rel="stylesheet" type="text/css" />
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Servis Formu</title>
</head>
<body>
    <div class="a4Page">
        <div class="header">
            <div class="col c1">
                @if($logoPath)
                    <img src="{{ public_path((strpos($logoPath, 'upload/') === false) ? 'upload/' . $logoPath : $logoPath) }}" alt="Logo">
                @endif
            </div>

            <div class="col c3">
                <div class="thumb">
                    <ul>
                        <li><span>TELEFON 1</span>{{ str_replace("-", " ", $getUye->tel1 ?? '') }}</li>
                        <li><span>TELEFON 2</span>{{ str_replace("-", " ", $getUye->tel2 ?? '') }}</li>
                        <li><span>E-MAİL</span>{{ $getUye->eposta ?? '' }}</li>
                        <li><span>WEB SİTESİ</span>{{ $webSitesi }}</li>
                    </ul>
                </div>
            </div>
            
            <div class="col c3">
                <div class="thumb">
                    <ul>
                        <li><span>{{ $getUye->vergiDairesi ?? '' }}</span>Vergi No: {{ $getUye->vergiNo ?? '' }}</li>
                        <li><span>IBAN</span>{{ $getUye->iban ?? '' }}</li>
                        <li><span>ADRES</span>{{ ($getUye->adres ?? '') . " " . ($getUye->ilce ?? '') . "/" . ($getUye->il ?? '') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="title clearfix">
            <div class="c1 "><h2>SERVİS FORMU</h2></div>
            <div class="c2">
                <strong>Servis No:</strong> {{ $servis->id }} <br> 
                <strong>Servis Kayıt Tarihi:</strong> {{ $tarih[2]."/".$tarih[1]."/".$tarih[0]." ".$saat[0].":".$saat[1] }}
            </div>
        </div>
        
        <div class="servisBox clearfix">
            <div class="c1">
                <h3>MÜŞTERİ BİLGİLERİ</h3>
                <div class="capt">
                    <div class="text"><strong>Müşteri Adı</strong><span>{{ $musteri->adSoyad ?? '' }}</span></div>
                    <div class="text"><strong>Telefon</strong><span>{{ ($musteri->tel1 ?? '') . ' - ' . ($musteri->tel2 ?? '') }}</span></div>
                    <div class="text"><strong>Adres</strong><span>{{ ($musteri->adres ?? '') . ' ' . ($musteri->ilce ?? '') . '/' . ($musteri->il ?? '') }}</span></div>
                    <div class="text"><strong>T.C. No</strong><span>{{ $musteri->tcNo ?? '' }}</span></div>
                    <div class="text"><strong>Vergi No/Dairesi</strong><span>{{ ($musteri->vergiNo ?? '') . '/' . ($musteri->vergiDairesi ?? '') }}</span></div>
                    <div class="text"><strong>Operatör Notu</strong><span>{{ $servis->opNot ?? '' }}</span></div>
                </div>
            </div>
            
            <div class="c2">
                <h3>CİHAZ BİLGİLERİ</h3>
                <div class="capt">
                    <div class="text"><strong>Cihaz Markası</strong><span>{{ $cihazMarka->marka ?? '' }}</span></div>
                    <div class="text"><strong>Cihaz Türü</strong><span>{{ $cihazTur->cihaz ?? '' }}</span></div>
                    <div class="text"><strong>Cihaz Modeli</strong><span>{{ $servis->cihazModel ?? '' }}</span></div>
                    <div class="text"><strong>Cihaz Seri No</strong><span>{{ $servis->cihazSeriNo ?? '' }}</span></div>
                    <div class="text"><strong>Cihaz Arızası</strong><span>{{ $servis->cihazAriza ?? '' }}</span></div>
                    <div class="text"><strong>Garanti Bitiş T.</strong><span>
                        @if(!empty($garantiBitis))
                            {{ $garantiBitis[0]."/".$garantiBitis[1]."/".$garantiBitis[2] }}
                        @else
                            Garanti Yok
                        @endif
                    </span></div>
                </div>
            </div>
        </div>

        <div class="durumBox"><strong>SERVİS DURUMU: </strong><span style="color: red">{{ $servisDurum->asama ?? '' }}</span></div>

        <div class="islemlerBox">
            <h3>- SERVİSTE YAPILAN SON 5 İŞLEM -</h3>
            <table>
                <tr>
                    <th style="width: 20%">TARİH</th>
                    <th style="width: 30%">İŞLEM ADI</th>
                    <th style="width: 50%">AÇIKLAMA</th>
                </tr>
                @foreach($islemDetaylari as $islem)
                <tr>
                    <td>{{ $islem['tarih'] }}</td>
                    <td><strong>{{ $islem['asama'] }}</strong></td>
                    <td>{!! $islem['aciklama'] !!}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <div class="paraBox">
            <h3>- PARA HAREKETLERİ -</h3>
            <table>
                <tr>
                    <th style="width: 10%">TARİH</th>
                    <th style="width: 25%">TAHSİL EDEN</th>
                    <th style="width: 25%">ÖDEME ŞEKLİ</th>
                    <th style="width: 20%">ÖDEME DURUMU</th>
                    <th style="width: 20%">FİYAT</th>
                </tr>
                @foreach($paraDetaylari as $para)
                <tr>
                    <td>{{ $para['tarih'] }}</td>
                    <td>{{ $para['personel'] }}</td>
                    <td>{{ $para['odemeSekli'] }}</td>
                    <td>{{ $para['odemeDurum'] }}</td>
                    <td>{{ $para['fiyat'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <div class="mesajBox">{!! nl2br(e($mesaj)) !!}</div>
    </div>
</body>
</html>