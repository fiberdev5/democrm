<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\UserRegisteredMail;
use Illuminate\Http\Request;
use App\Models\HomeSlide;
use App\Models\Faq;
use App\Models\Misyon;
use App\Models\Category;
use App\Models\Clients;
use App\Models\Feature;
use App\Models\Pricing;
use App\Models\Reference;
use App\Models\ServiceTime;
use App\Models\Settings;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantPrim;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Services\ActivityLogger;
use App\Mail\PasswordResetMail;
use App\Services\TescomService;
use Illuminate\Support\Facades\Log;
use App\Models\FrontendSetting;

class HomeController extends Controller
{
        public function index()
    {
        // // İstatistikler
        // $stats = [
        //     [
        //         'number' => '500+',
        //         'label' => 'Aktif Firma'
        //     ],
        //     [
        //         'number' => '50K+',
        //         'label' => 'Tamamlanan Servis'
        //     ],
        //     [
        //         'number' => '99.9%',
        //         'label' => 'Uptime Garantisi'
        //     ],
        //     [
        //         'number' => '7/24',
        //         'label' => 'Destek Hizmeti'
        //     ]
        // ];

        // Veritabanından çek
        $stats = FrontendSetting::where('section', 'home_stats')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function($item) {
                return $item->data;
            });
        
        // Eğer veritabanında yoksa default değerleri kullan
        if($stats->isEmpty()) {
            $stats = collect([
                ['number' => '500+', 'label' => 'Aktif Firma'],
                ['number' => '50K+', 'label' => 'Tamamlanan Servis'],
                ['number' => '99.9%', 'label' => 'Uptime Garantisi'],
                ['number' => '7/24', 'label' => 'Destek Hizmeti']
            ]);
        }

        // Özellikler
        // $modules = [
        //     [
        //         'icon' => 'fas fa-users',
        //         'title' => 'Müşteri Yönetimi',
        //         'description' => 'Müşterilerinizi detaylı kayıt altına alın, geçmiş işlemlerini görüntüleyin ve müşteri memnuniyetini artırın.',
        //         'color' => 'blue'
        //     ],
        //     [
        //         'icon' => 'fas fa-clipboard-list',
        //         'title' => 'Servis Takibi',
        //         'description' => 'Servis süreçlerinizi baştan sona takip edin. Arıza kayıtlarından teslimata kadar her aşamayı yönetin.',
        //         'color' => 'blue'
        //     ],
        //     [
        //         'icon' => 'fas fa-boxes',
        //         'title' => 'Stok Yönetimi',
        //         'description' => 'Yedek parça stoklarınızı takip edin, kritik stok seviyelerinde otomatik uyarı alın.',
        //         'color' => 'blue'
        //     ],
        //     [
        //         'icon' => 'fas fa-user-tie',
        //         'title' => 'Personel Yönetimi',
        //         'description' => 'Teknisyenlerinizi yönetin, performanslarını ölçün ve prim hesaplamalarını otomatikleştirin.',
        //         'color' => 'orange'
        //     ],
        //     [
        //         'icon' => 'fas fa-file-invoice-dollar',
        //         'title' => 'Fatura & Kasa',
        //         'description' => 'E-fatura oluşturun, gelir-gider takibi yapın, finansal raporlarınızı anında görüntüleyin.',
        //         'color' => 'orange'
        //     ],
        //     [
        //         'icon' => 'fas fa-mobile-alt',
        //         'title' => 'Mobil Erişim',
        //         'description' => 'Responsive tasarım sayesinde mobil cihazlardan her yerde işlerinizi yönetin.',
        //         'color' => 'orange'
        //     ]
        // ];
        //Özellikler
        $modules = FrontendSetting::where('section', 'home_modules')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function($item) {
                return $item->data;
            });



        // Sektörler (Ana Sayfa İçin)
        // $sectors = [
        //     [
        //         'slug' => 'beyaz-esya',
        //         'image' => 'frontend/img/sectors/beyaz-esya.jpg',
        //         'title' => 'Beyaz Eşya',
        //         'description' => 'Buzdolabı, çamaşır makinesi servis takibi'
        //     ],
        //     [
        //         'slug' => 'bilgisayar-teknoloji',
        //         'image' => 'frontend/img/sectors/bilgisayar-teknoloji.jpg',
        //         'title' => 'Bilgisayar',
        //         'description' => 'Bilgisayar ve donanım teknik servisleri'
        //     ],
        //     [
        //         'slug' => 'telekomünikasyon',
        //         'image' => 'frontend/img/sectors/telekominasyon.jpg',
        //         'title' => 'Telekomünikasyon',
        //         'description' => 'Cep telefonu ve mobil cihaz onarım süreçleri'
        //     ],
        //     [
        //         'slug' => 'klima-sogutma',
        //         'image' => 'frontend/img/sectors/klima.jpg',
        //         'title' => 'Klima & HVAC',
        //         'description' => 'Klima montaj, bakım ve arıza yönetimi'
        //     ],
        //     [
        //         'slug' => 'elektrik-elektronik', // Eğer detayda varsa slug'ı kontrol et
        //         'image' => 'frontend/img/sectors/elektrik-elektronik.jpg',
        //         'title' => 'Elektrik-Elektronik',
        //         'description' => 'TV, ses sistemleri ve elektronik tamiri'
        //     ],
        //     [
        //         'slug' => 'guvenlik-sistemleri',
        //         'image' => 'frontend/img/sectors/guvenlik.jpg',
        //         'title' => 'Güvenlik',
        //         'description' => 'Kamera ve alarm sistemleri kurulum takibi'
        //     ],
        //     [
        //         'slug' => 'medikal-cihazlar',
        //         'image' => 'frontend/img/sectors/medikal.jpg',
        //         'title' => 'Medikal',
        //         'description' => 'Tıbbi cihaz bakım ve kalibrasyon yönetimi'
        //     ],
        //     [
        //         'slug' => 'ofis-ekipmanlari',
        //         'image' => 'frontend/img/sectors/ofis.png',
        //         'title' => 'Ofis Sistemleri',
        //         'description' => 'Yazıcı ve fotokopi kiralama/servis takibi'
        //     ]
        // ];
        
        // Sektörler
        $sectors = FrontendSetting::where('section', 'home_sectors')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function($item) {
                return $item->data;
            });

        // Entegrasyonlar
        // $integrations = [
        //     [
        //         'icon' => 'fas fa-file-invoice',
        //         'title' => 'Paraşüt',
        //         'description' => 'Muhasebe yazılımı ile entegrasyon',
        //         'color' => 'blue'
        //     ],
        //     [
        //         'icon' => 'fas fa-phone-volume',
        //         'title' => 'Hipcall',
        //         'description' => 'Santral entegrasyonu ile gelen aramalar',
        //         'color' => 'orange'
        //     ],
        //     [
        //         'icon' => 'fas fa-sms',
        //         'title' => 'SMS Entegrasyonu',
        //         'description' => 'Netgsm, Verimor ile SMS gönderimi',
        //         'color' => 'blue'
        //     ],
        //     [
        //         'icon' => 'fas fa-envelope',
        //         'title' => 'Email Sistemi',
        //         'description' => 'SMTP entegrasyonu ile otomatik email',
        //         'color' => 'orange'
        //     ],
        //     [
        //         'icon' => 'fas fa-credit-card',
        //         'title' => 'Ödeme Sistemleri',
        //         'description' => 'Online ödeme alma entegrasyonları',
        //         'color' => 'blue'
        //     ],
        //     [
        //         'icon' => 'fas fa-plug',
        //         'title' => 'REST API',
        //         'description' => 'Kendi sistemlerinizle entegrasyon',
        //         'color' => 'orange'
        //     ]
        // ];

        // Entegrasyonlar
        $integrations = FrontendSetting::where('section', 'home_integrations')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function($item) {
                return $item->data;
            });

        // Müşteri Yorumları
        // $testimonials = [
        //     [
        //         'quote' => 'Serbis sayesinde tüm servis süreçlerimizi dijitalleştirdik. Artık her şey çok daha hızlı ve organize. Müşteri memnuniyetimiz %40 arttı. Kesinlikle tavsiye ediyorum.',
        //         'name' => 'Ahmet Yılmaz',
        //         'position' => 'Beyaz Eşya Servisi',
        //         'initials' => 'AY',
        //         'color' => 'blue'
        //     ],
        //     [
        //         'quote' => 'Müşteri takibi ve stok yönetimi artık çok kolay. Özellikle mobil erişim sahada işimizi inanılmaz kolaylaştırdı. Kağıt formlardan kurtulduk.',
        //         'name' => 'Mehmet Kara',
        //         'position' => 'Elektronik Servisi',
        //         'initials' => 'MK',
        //         'color' => 'orange'
        //     ],
        //     [
        //         'quote' => 'Destek ekibi harika! Her sorumuzda hızlıca yardımcı oldular. Sistemi kullanmak gerçekten çok basit ve kullanışlı. 3 yıldır memnuniyetle kullanıyoruz.',
        //         'name' => 'Fatma Öztürk',
        //         'position' => 'Klima Servisi',
        //         'initials' => 'FÖ',
        //         'color' => 'blue'
        //     ]
        // ];

        // Yorumlar
        $testimonials = FrontendSetting::where('section', 'home_testimonials')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function($item) {
                return $item->data;
            });


        // SSS
        // $faqs = [
        //     [
        //         'question' => 'Serbis\'i kullanmak için teknik bilgiye ihtiyacım var mı?',
        //         'answer' => 'Hayır, Serbis kullanıcı dostu arayüzü ile herkes tarafından kolayca kullanılabilir. Kurulum sonrası eğitim videolarımız ve destek ekibimiz size yardımcı olacaktır.'
        //     ],
        //     [
        //         'question' => 'Verilerim güvende mi?',
        //         'answer' => 'Evet, tüm verileriniz SSL şifreleme ile korunur ve düzenli olarak yedeklenir. Türkiye\'de bulunan sunucularımızda KVKK uyumlu olarak verilerinizi saklarız.'
        //     ],
        //     [
        //         'question' => 'Ücretsiz deneme süresi var mı?',
        //         'answer' => 'Evet, 14 gün boyunca ücretsiz deneyebilirsiniz. Kredi kartı bilgisi gerekmez. Deneme süreniz sonunda istediğiniz paketi seçebilirsiniz.'
        //     ],
        //     [
        //         'question' => 'Mobil cihazlardan kullanabilir miyim?',
        //         'answer' => 'Evet, Serbis responsive tasarıma sahiptir. Telefon, tablet ve bilgisayardan sorunsuz kullanabilirsiniz. Ayrıca mobil uygulamamız da yakında yayınlanacak.'
        //     ],
        //     [
        //         'question' => 'Mevcut verilerimi aktarabilir miyim?',
        //         'answer' => 'Evet, mevcut müşteri, stok ve servis verilerinizi Excel dosyası ile sisteme aktarabilirsiniz. Destek ekibimiz bu konuda size yardımcı olacaktır.'
        //     ],
        //     [
        //         'question' => 'Destek hizmeti nasıl çalışıyor?',
        //         'answer' => 'Telefon, email ve canlı destek kanallarımız üzerinden bize ulaşabilirsiniz. Profesyonel pakette öncelikli destek, Kurumsal pakette 7/24 destek sunuyoruz.'
        //     ]
        // ];

        

        return view('frontend.index', compact('stats', 'modules', 'sectors', 'integrations', 'testimonials', 'faqs'));
    }
    public function Sectors()
{
    $sectors = [
        [
            'slug' => 'elektrik-elektronik',
            'icon' => 'fas fa-plug',
            'title' => 'Elektrik-Elektronik',
            'short_description' => 'Elektronik cihaz servis süreçleri yönetimi',
            'image' => 'frontend/img/sectors/elektrik-elektronik.jpg',
            'features' => [
                'Cihaz kabul ve barkodlama',
                'Yedek parça stok takibi',
                'Müşteri SMS bilgilendirme',
                'Garanti süresi kontrolü'
            ]
        ],
        [
            'slug' => 'beyaz-esya',
            'icon' => 'fas fa-tv',
            'title' => 'Beyaz Eşya',
            'short_description' => 'Saha ekibi ve atölye servis takip yönetimi',
            'image' => 'frontend/img/sectors/beyaz-esya.jpg',
            'features' => [
                'Mobil servis yönetimi',
                'Randevu ve rota planlama',
                'Konum bazlı personel takibi',
                'Yerinde fatura yazdırma'
            ]
        ],
        [
            'slug' => 'klima-sogutma',
            'icon' => 'fas fa-fan',
            'title' => 'Klima-Soğutma',
            'short_description' => 'Montaj, bakım ve servis takip yönetimi',
            'image' => 'frontend/img/sectors/klima.jpg',
            'features' => [
                'Periyodik bakım takvimi',
                'Montaj ekibi yönetimi',
                'Bakım sözleşmesi takibi',
                'QR kodlu cihaz geçmişi'
            ]
        ],
        [
            'slug' => 'bilgisayar-teknoloji',
            'icon' => 'fas fa-laptop',
            'title' => 'Bilgisayar-Teknoloji',
            'short_description' => 'Bilişim sektörü teknik servis programı',
            'image' => 'frontend/img/sectors/bilgisayar-teknoloji.jpg',
            'features' => [
                'Detaylı arıza raporlama',
                'Seri no ile cihaz takibi',
                'Hızlı teklif hazırlama',
                'Online durum sorgulama'
            ]
        ],
        [
            'slug' => 'telekomünikasyon', 
            'icon' => 'fas fa-mobile-alt',
            'title' => 'Telekomünikasyon',
            'short_description' => 'Cep telefonu ve tablet tamir servisi takibi',
            'image' => 'frontend/img/sectors/telekominasyon.jpg',
            'features' => [
                'Cihaz kayıt ve takibi',
                'Emanet cihaz yönetimi',
                'Hızlı servis girişi',
                'Toplu kampanya SMS\'leri'
            ]
        ],
        [
            'slug' => 'ofis-ekipmanlari',
            'icon' => 'fas fa-print',
            'title' => 'Ofis Ekipmanları',
            'short_description' => 'Yazıcı kiralama ve sayaç takip sistemi',
            'image' => 'frontend/img/sectors/ofis.png',
            'features' => [
                'Sayaç (Counter) takibi',
                'Toner ve sarf malzeme stoğu',
                'Kiralama sözleşmeleri',
                'Otomatik bakım hatırlatıcı'
            ]
        ],
        [
            'slug' => 'guvenlik-sistemleri',
            'icon' => 'fas fa-shield-alt',
            'title' => 'Güvenlik Sistemleri',
            'short_description' => 'Proje, keşif ve montaj servis takip sistemi',
            'image' => 'frontend/img/sectors/guvenlik.jpg',
            'features' => [
                'Keşif formu oluşturma',
                'Proje bazlı iş takibi',
                'Tekliflendirme modülü',
                'Montaj ekibi planlama'
            ]
        ],
        [
            'slug' => 'medikal-cihazlar',
            'icon' => 'fas fa-heartbeat',
            'title' => 'Medikal Cihazlar',
            'short_description' => 'Biyomedikal cihaz bakım ve kalibrasyon takibi',
            'image' => 'frontend/img/sectors/medikal.jpg',
            'features' => [
                'Kalibrasyon tarihi takibi',
                'Kurumsal envanter yönetimi',
                'Yasal bakım formları',
                'Sözleşmeli servis takibi'
            ]
        ]
    ];

    return view('frontend.frontend_pages.sectors', compact('sectors'));
}
public function SectorDetail($slug)
{
    // Sektör detayları - Servis takip programı satışı odaklı
    $sectorDetails = [
        'elektrik-elektronik' => [
            'title' => 'Elektrik-Elektronik Teknik Servis Programı',
            'icon' => 'fas fa-plug',
            'hero_image' => 'frontend/img/sectors/elektrik-elektronik.jpg',
            'description' => 'Elektrik ve elektronik servis işletmeniz için özel olarak tasarlanmış servis takip programımız ile tüm süreçlerinizi dijitalleştirin. Müşteri kayıtlarından arıza takibine, stok yönetiminden faturalamaya kadar her şeyi tek platformda yönetin.',
            'features' => [
                [
                    'title' => 'Arıza Takip Sistemi',
                    'description' => 'Gelen arızaları kaydedin, teknisyen ataması yapın ve süreçleri anlık takip edin.',
                    'icon' => 'fas fa-tasks'
                ],
                [
                    'title' => 'Yedek Parça Stok Yönetimi',
                    'description' => 'Tüm yedek parçalarınızı takip edin, kritik stok seviyelerinde otomatik uyarı alın.',
                    'icon' => 'fas fa-boxes'
                ],
                [
                    'title' => 'Garanti Takibi',
                    'description' => 'Garanti sürelerini otomatik takip edin, garanti bitim tarihlerinde hatırlatma alın.',
                    'icon' => 'fas fa-shield-alt'
                ],
                [
                    'title' => 'Müşteri CRM',
                    'description' => 'Müşteri bilgilerini, geçmiş işlemlerini ve cihaz bilgilerini detaylı saklayın.',
                    'icon' => 'fas fa-users'
                ]
            ],
            'services' => [
                'Servis Kayıt ve Takip',
                'Müşteri Yönetimi (CRM)',
                'Yedek Parça Stok Kontrolü',
                'Teknisyen Atama ve İş Dağılımı',
                'E-Fatura Entegrasyonu',
                'SMS ve Email Bildirimleri'
            ],
            'benefits' => [
                'Kolay kullanılabilir arayüz',
                'Mobil uyumlu - Her yerden erişim',
                'Bulut tabanlı - Kurulum gerektirmez',
                '14 gün ücretsiz deneme',
                'Sınırsız kullanıcı (üst paketlerde)',
                'Türkçe teknik destek'
            ],
            'stats' => [
                ['number' => '500+', 'label' => 'Aktif İşletme'],
                ['number' => '50K+', 'label' => 'Takip Edilen Servis'],
                ['number' => '99.9%', 'label' => 'Uptime Garantisi'],
                ['number' => '7/24', 'label' => 'Teknik Destek']
            ],
            'faqs' => [
    [
        'question' => 'Elektrik-elektronik servisime özel hangi özellikler var?',
        'answer' => 'Serbis, elektrik-elektronik servisler için özel olarak cihaz seri no takibi, IMEI kaydı, garanti sorgulama ve uyumlu parça eşleştirme gibi özellikler sunar. Ayrıca elektronik cihazlara özel arıza kodları ve tanı raporları oluşturabilirsiniz.'
    ],
    [
        'question' => 'Birden fazla şubem var, hepsini tek programdan yönetebilir miyim?',
        'answer' => 'Evet, Serbis çoklu şube yönetimi destekler. Her şubenin stok, personel ve müşteri bilgileri ayrı tutulur ancak merkezi raporlama ile tüm şubelerinizi tek panelden izleyebilirsiniz. Şubeler arası stok transferi de yapabilirsiniz.'
    ],
    [
        'question' => 'Programı kullanmak için teknik bilgiye ihtiyacım var mı?',
        'answer' => 'Hayır, Serbis kullanıcı dostu arayüzü ile hiçbir teknik bilgi gerektirmez. Temel bilgisayar kullanımı bilen herkes kolayca kullanabilir. Ayrıca video eğitimler ve canlı destek ile her zaman yanınızdayız.'
    ],
    [
        'question' => 'Eski sistemimden veri aktarımı yapabilir miyiz?',
        'answer' => 'Evet, Excel veya CSV formatındaki müşteri, cihaz ve stok verilerinizi toplu olarak sisteme aktarabilirsiniz. Ayrıca farklı programlardan geçiş yapmak istiyorsanız, teknik ekibimiz veri aktarımında size yardımcı olur.'
    ],
    [
        'question' => 'Paraşüt muhasebe programı ile entegrasyon var mı?',
        'answer' => 'Evet, Serbis Paraşüt ile tam entegre çalışır. Kestiğiniz faturalar otomatik olarak Paraşüt hesabınıza aktarılır, e-fatura ve e-arşiv işlemlerinizi tek tıkla tamamlayabilirsiniz.'
    ]
]
        ],
        'beyaz-esya' => [
            'title' => 'Beyaz Eşya Teknik Servis Programı',
            'icon' => 'fas fa-tv',
            'hero_image' => 'frontend/img/sectors/beyaz-esya.jpg',
            'description' => 'Beyaz eşya servis işletmeniz için özel geliştirilmiş servis takip yazılımımız ile günlük operasyonlarınızı kolaylaştırın. Buzdolabı, çamaşır makinesi, bulaşık makinesi ve tüm beyaz eşya cihazlarınızın servisini tek platformdan yönetin.',
            'features' => [
                [
                    'title' => 'Cihaz Bazlı Takip',
                    'description' => 'Her cihaz için marka, model, seri numarası bilgilerini saklayın ve geçmiş servislerini görün.',
                    'icon' => 'fas fa-barcode'
                ],
                [
                    'title' => 'Yerinde Servis Yönetimi',
                    'description' => 'Teknisyen lokasyonunu takip edin, en yakın teknisyeni otomatik atayın.',
                    'icon' => 'fas fa-map-marker-alt'
                ],
                [
                    'title' => 'Parça Siparişi',
                    'description' => 'Eksik parçaları hızlıca tespit edin ve tedarikçiden sipariş verin.',
                    'icon' => 'fas fa-shopping-cart'
                ],
                [
                    'title' => 'Müşteri Bilgilendirme',
                    'description' => 'Otomatik SMS ile müşterileri servis durumu hakkında bilgilendirin.',
                    'icon' => 'fas fa-sms'
                ]
            ],
            'services' => [
                'Servis Randevu Yönetimi',
                'Yerinde Servis Takibi',
                'Marka-Model Bazlı Kayıt',
                'Fotoğraflı Arıza Kaydı',
                'Ödeme ve Fatura Yönetimi',
                'Müşteri Memnuniyet Anketi'
            ],
            'benefits' => [
                'Yerinde servis optimizasyonu',
                'Teknisyen performans raporları',
                'Otomatik müşteri bildirimleri',
                'Kampanya ve hatırlatma sistemi',
                'Paraşüt muhasebe entegrasyonu',
                'Günlük otomatik yedekleme'
            ],
            'stats' => [
                ['number' => '200+', 'label' => 'Beyaz Eşya Servisi'],
                ['number' => '30K+', 'label' => 'Aylık İşlem'],
                ['number' => '%45', 'label' => 'Verimlilik Artışı'],
                ['number' => '2-3', 'label' => 'Gün Kurulum']
            ]
        ],
        'klima-sogutma' => [
            'title' => 'Klima-Soğutma Teknik Servis Programı',
            'icon' => 'fas fa-fan',
            'hero_image' => 'frontend/img/sectors/klima.jpg',
            'description' => 'Klima servisi işletmeniz için tasarlanmış kapsamlı takip programımız ile montaj, bakım, onarım ve tüm servis işlemlerinizi dijital ortamda yönetin. Sezonluk yoğunlukta bile kontrolü kaybetmeyin.',
            'features' => [
                [
                    'title' => 'Montaj ve Bakım Planlaması',
                    'description' => 'Montaj randevularını ve periyodik bakımları takvimde planlayın, otomatik hatırlatın.',
                    'icon' => 'fas fa-calendar-alt'
                ],
                [
                    'title' => 'Gaz Dolum Takibi',
                    'description' => 'Gaz dolum işlemlerini kaydedin, gaz stok durumunu anlık kontrol edin.',
                    'icon' => 'fas fa-tint'
                ],
                [
                    'title' => 'Periyodik Bakım Hatırlatma',
                    'description' => 'Yıllık bakım zamanı gelen müşterilere otomatik SMS/Email gönderin.',
                    'icon' => 'fas fa-bell'
                ],
                [
                    'title' => 'Fotoğraflı Servis Raporu',
                    'description' => 'Montaj öncesi ve sonrası fotoğrafları sistemde saklayın.',
                    'icon' => 'fas fa-camera'
                ]
            ],
            'services' => [
                'Montaj Takip Sistemi',
                'Bakım Planlaması ve Hatırlatma',
                'Gaz Stok Yönetimi',
                'Teknisyen Rota Optimizasyonu',
                'Sözleşme Yönetimi',
                'Müşteri Portföy Analizi'
            ],
            'benefits' => [
                'Sezonluk yoğunlukta hızlı kayıt',
                'Teknisyen mobil uygulaması',
                'Bakım sözleşmesi yönetimi',
                'Otomatik fiyat hesaplama',
                'WhatsApp entegrasyonu',
                'Excel aktarım-içe aktarım'
            ],
            'stats' => [
                ['number' => '150+', 'label' => 'Klima Servisi'],
                ['number' => '25K+', 'label' => 'Montaj Kaydı'],
                ['number' => '%60', 'label' => 'Zaman Tasarrufu'],
                ['number' => '1', 'label' => 'Gün Kurulum']
            ]
        ],
        'bilgisayar-teknoloji' => [
            'title' => 'Bilgisayar-Teknoloji Teknik Servis Programı',
            'icon' => 'fas fa-laptop',
            'hero_image' => 'frontend/img/sectors/bilgisayar-teknoloji.jpg',
            'description' => 'Bilgisayar ve teknoloji servis işletmeniz için geliştirilmiş profesyonel takip yazılımı ile donanım onarımlarından yazılım işlemlerine, veri kurtarmadan network kurulumlarına kadar tüm işlemleri yönetin.',
            'features' => [
                [
                    'title' => 'Detaylı Cihaz Kaydı',
                    'description' => 'Marka, model, seri no, donanım özellikleri ve şifre bilgilerini güvenli saklayın.',
                    'icon' => 'fas fa-laptop-code'
                ],
                [
                    'title' => 'Parça Takibi',
                    'description' => 'RAM, SSD, ekran gibi parçaların stok ve fiyat bilgilerini yönetin.',
                    'icon' => 'fas fa-memory'
                ],
                [
                    'title' => 'İşlem Geçmişi',
                    'description' => 'Yapılan tüm donanım ve yazılım işlemlerinin detaylı geçmişini tutun.',
                    'icon' => 'fas fa-history'
                ],
                [
                    'title' => 'Veri Güvenliği',
                    'description' => 'Müşteri verilerini şifreli ve güvenli ortamda saklayın.',
                    'icon' => 'fas fa-lock'
                ]
            ],
            'services' => [
                'Bilgisayar Servis Takibi',
                'Donanım-Yazılım İşlem Kaydı',
                'Parça ve Lisans Yönetimi',
                'Kurumsal Müşteri Sözleşmeleri',
                'Bakım Anlaşması Takibi',
                'Teknik Ekip Yönetimi'
            ],
            'benefits' => [
                'Hızlı kayıt ve sorgulama',
                'Şifre ve hassas veri koruması',
                'Toplu SMS gönderimi',
                'QR kod ile cihaz takibi',
                'Teknisyen not sistemi',
                'Gelişmiş raporlama araçları'
            ],
            'stats' => [
                ['number' => '300+', 'label' => 'Bilgisayar Servisi'],
                ['number' => '40K+', 'label' => 'Onarım Kaydı'],
                ['number' => '%50', 'label' => 'Daha Hızlı İşlem'],
                ['number' => '24/7', 'label' => 'Bulut Erişim']
            ]
        ],
    'telekomünikasyon' => [
    'title' => 'Telekomünikasyon Teknik Servis Prorgamı',
    'icon' => 'fas fa-mobile-alt',
    'hero_image' => 'frontend/img/sectors/telekominasyon.jpg',
    'description' => 'Cep telefonu ve tablet servis işletmeniz için özel olarak tasarlanmış Serbis takip programı ile tüm operasyonlarınızı dijitalleştirin.Parça stok yönetimi ile günlük iş yükünüzü %70 azaltın.',
    'features' => [
        [
            'title' => 'IMEI Bazlı Müşteri Kaydı',
            'description' => 'IMEI numarası ile otomatik cihaz tanıma, geçmiş servis kayıtlarına anında ulaşım.',
            'icon' => 'fas fa-mobile'
        ],
        [
            'title' => 'Hızlı İşlem Şablonları',
            'description' => 'Ekran, batarya, kamera gibi sık yapılan işlemleri tek tıkla kaydedin.',
            'icon' => 'fas fa-bolt'
        ],
        [
            'title' => 'Akıllı Stok Yönetimi',
            'description' => 'Model bazlı parça stoğu, kritik seviye uyarıları ve tedarikçi takibi.',
            'icon' => 'fas fa-boxes'
        ],
        [
            'title' => 'SMS Bilgilendirme',
            'description' => 'Servis hazır olunca müşterinize otomatik SMS bildirimi gönderin.',
            'icon' => 'fas fa-sms'
        ]
    ],
    'services' => [
        'Müşteri ve Cihaz Kayıt Sistemi',
        'Servis İşlem Takibi',
        'Parça ve Aksesuar Stok Yönetimi',
        'Teknisyen İş Atama Modülü',
        'E-Fatura Entegrasyonu',
        'Otomatik SMS Bildirimleri'
    ],
    'benefits' => [
        'Bulut tabanlı - Kurulum gerektirmez',
        'Mobil uyumlu - Her yerden erişim',
        'IMEI ile otomatik cihaz tanıma',
        'Barkod okuyucu entegrasyonu',
        '14 gün ücretsiz deneme',
        'Sınırsız servis kaydı'
    ],
    'stats' => [
        ['number' => '400+', 'label' => 'GSM Servisi Kullanıyor'],
        ['number' => '100K+', 'label' => 'Aylık İşlem'],
        ['number' => '%70', 'label' => 'Zaman Tasarrufu'],
        ['number' => '15sn', 'label' => 'Ortalama Kayıt Süresi']
    ]
],
'ofis-ekipmanlari' => [
    'title' => 'Ofis Ekipmanları Teknik Servis Programı',
    'icon' => 'fas fa-print',
    'hero_image' => 'frontend/img/sectors/ofis.png',
    'description' => 'Yazıcı, fotokopi ve ofis ekipmanları servisi veren işletmeniz için Serbis takip yazılımı ile sayaç takibinden periyodik bakımlara, toner yönetiminden kurumsal sözleşmelere kadar tüm süreçlerinizi dijital ortamda yönetin.',
    'features' => [
        [
            'title' => 'Sayaç Takip Modülü',
            'description' => 'Cihaz sayaç bilgilerini kaydedin, bakım zamanlarını otomatik hesaplayın.',
            'icon' => 'fas fa-tachometer-alt'
        ],
        [
            'title' => 'Periyodik Bakım Planı',
            'description' => 'Kurumsal müşterilerinizin bakım takvimini oluşturun, otomatik hatırlatmalar alın.',
            'icon' => 'fas fa-calendar-check'
        ],
        [
            'title' => 'Toner-Parça Stoğu',
            'description' => 'Marka-model bazlı toner ve parça stoklarınızı takip edin.',
            'icon' => 'fas fa-fill-drip'
        ],
        [
            'title' => 'Sözleşme Yönetimi',
            'description' => 'Bakım sözleşmelerini sisteme tanımlayın, aylık faturaları otomatik oluşturun.',
            'icon' => 'fas fa-file-contract'
        ]
    ],
    'services' => [
        'Yazıcı-Fotokopi Servis Takibi',
        'Sayaç Bazlı Bakım Yönetimi',
        'Kurumsal Sözleşme Modülü',
        'Toner-Malzeme Stok Kontrolü',
        'Teknisyen Rota Planlaması',
        'Müşteri Portföy Analizi'
    ],
    'benefits' => [
        'Kurumsal müşteri yönetimi',
        'Sayaç bazlı otomatik fiyatlandırma',
        'Periyodik bakım hatırlatmaları',
        'Toplu SMS/Email gönderimi',
        'Paraşüt muhasebe entegrasyonu',
        'Excel içe-dışa aktarım'
    ],
    'stats' => [
        ['number' => '180+', 'label' => 'Ofis Servisi Kullanıyor'],
        ['number' => '15K+', 'label' => 'Takip Edilen Cihaz'],
        ['number' => '%55', 'label' => 'İş Yükü Azalması'],
        ['number' => '2-3', 'label' => 'Saat Kurulum']
    ]
],
'guvenlik-sistemleri' => [
    'title' => 'Güvenlik Sistemleri Teknik Servis Programı',
    'icon' => 'fas fa-shield-alt',
    'hero_image' => 'frontend/img/sectors/guvenlik.jpg',
    'description' => 'Güvenlik kameraları, alarm ve geçiş kontrol sistemleri kuran firmalar için Serbis proje takip yazılımı ile kurulum projelerinizi, bakım anlaşmalarınızı ve teknik destek süreçlerinizi tek platformdan yönetin.',
    'features' => [
        [
            'title' => 'Proje Yönetim Modülü',
            'description' => 'Kamera kurulumlarını proje bazlı takip edin, aşama aşama ilerlemesini izleyin.',
            'icon' => 'fas fa-project-diagram'
        ],
        [
            'title' => 'Cihaz Envanter Sistemi',
            'description' => 'Hangi müşteride hangi cihazlar kurulu, lokasyonu nerede - hepsini sistemde kaydedin.',
            'icon' => 'fas fa-video'
        ],
        [
            'title' => 'Bakım Sözleşmeleri',
            'description' => 'Kurumsal bakım anlaşmalarını sisteme girin, periyodik kontrolleri planlayın.',
            'icon' => 'fas fa-handshake'
        ],
        [
            'title' => 'Malzeme-İşçilik Takibi',
            'description' => 'Proje maliyetlerini detaylı takip edin, kar-zarar analizleri yapın.',
            'icon' => 'fas fa-chart-line'
        ]
    ],
    'services' => [
        'Kamera-Alarm Kurulum Takibi',
        'Proje Bazlı Maliyet Yönetimi',
        'Cihaz Lokasyon Haritalaması',
        'Bakım Sözleşmesi Modülü',
        'Teknik Destek Talep Yönetimi',
        'Fotoğraflı Kurulum Raporları'
    ],
    'benefits' => [
        'Proje bazlı karlılık analizi',
        'Kurulum fotoğraf galerisi',
        'Teknik şema/plan yükleme',
        'Müşteri onay sistemi',
        'Teknisyen performans takibi',
        'Teklif hazırlama modülü'
    ],
    'stats' => [
        ['number' => '120+', 'label' => 'Güvenlik Firması'],
        ['number' => '3K+', 'label' => 'Tamamlanan Proje'],
        ['number' => '%40', 'label' => 'Verimlilik Artışı'],
        ['number' => '1', 'label' => 'Gün Kurulum']
    ]
],
'medikal-cihazlar' => [
    'title' => 'Medikal Cihaz Teknik Servis Programı',
    'icon' => 'fas fa-heartbeat',
    'hero_image' => 'frontend/img/sectors/medikal.jpg',
    'description' => 'Tıbbi cihaz bakım ve kalibrasyon servisi veren işletmeniz için Serbis takip yazılımı ile cihaz kalibrasyonlarını, periyodik bakımları, sertifikaları ve yasal uyumluluk gereksinimlerini profesyonelce yönetin.',
    'features' => [
        [
            'title' => 'Kalibrasyon Takip Sistemi',
            'description' => 'Cihazların kalibrasyon tarihlerini takip edin, süresi dolmadan uyarı alın.',
            'icon' => 'fas fa-certificate'
        ],
        [
            'title' => 'Dijital Sertifika Arşivi',
            'description' => 'Kalibrasyon sertifikalarını, test raporlarını güvenli ortamda saklayın.',
            'icon' => 'fas fa-file-medical'
        ],
        [
            'title' => 'Bakım Hatırlatma Sistemi',
            'description' => 'Periyodik bakım zamanları gelince otomatik SMS/Email bildirimi gönderin.',
            'icon' => 'fas fa-bell'
        ],
        [
            'title' => 'Uyumluluk Raporları',
            'description' => 'Yasal düzenlemelere uygun detaylı raporlar oluşturun, denetim hazırlığını kolaylaştırın.',
            'icon' => 'fas fa-clipboard-check'
        ]
    ],
    'services' => [
        'Medikal Cihaz Envanter Yönetimi',
        'Kalibrasyon Planlama ve Takibi',
        'Periyodik Bakım Hatırlatmaları',
        'Sertifika ve Dokümantasyon',
        'Hastane-Kurum Bazlı Yönetim',
        'Yasal Uyumluluk Raporları'
    ],
    'benefits' => [
        'Yasal gereksinimlere tam uyum',
        'Otomatik kalibrasyon hatırlatma',
        'Dijital belge yönetimi',
        'Hastane bazlı segmentasyon',
        'Kritik cihaz önceliklendirme',
        'Denetim için hazır raporlar'
    ],
    'stats' => [
        ['number' => '80+', 'label' => 'Medikal Servis Firması'],
        ['number' => '12K+', 'label' => 'Takip Edilen Cihaz'],
        ['number' => '%100', 'label' => 'Uyumluluk Oranı'],
        ['number' => '30', 'label' => 'Gün Önceden Uyarı']
    ]
],
    ];

    if (!isset($sectorDetails[$slug])) {
        abort(404);
    }

    $sector = $sectorDetails[$slug];
// Eğer sektörün kendi FAQ'i yoksa, genel FAQ'leri kullan
    if(!isset($sector['faqs'])) {
        $sector['faqs'] = [
            [
                'question' => 'Programı kullanmak için teknik bilgiye ihtiyacım var mı?',
                'answer' => 'Hayır, Serbis kullanıcı dostu arayüzü ile herkes tarafından kolayca kullanılabilir.'
            ],
            [
                'question' => 'Verilerim güvende mi?',
                'answer' => 'Evet, tüm verileriniz şifrelenmiş olarak saklanır ve düzenli yedeklenir.'
            ],
            [
                'question' => 'Ücretsiz deneme süresi var mı?',
                'answer' => 'Evet, 14 gün boyunca ücretsiz deneyebilirsiniz. Kredi kartı bilgisi gerekmez.'
            ],
            [
                'question' => 'Mobil cihazlardan kullanabilir miyim?',
                'answer' => 'Evet, Serbis responsive tasarıma sahiptir. Telefon, tablet ve bilgisayardan kullanabilirsiniz.'
            ],
            [
                'question' => 'Destek hizmeti var mı?',
                'answer' => 'Evet, telefon, email ve canlı destek kanallarımız üzerinden bize ulaşabilirsiniz.'
            ]
        ];
    }
    return view('frontend.frontend_pages.sector_detail', compact('sector'));
}
public function Features()
{
    // Özellikler Kategorileri
    $featureCategories = [
        [
            'title' => 'Müşteri Yönetimi',
            'subtitle' => 'Tüm müşteri bilgilerinizi organize edin ve ilişkilerinizi güçlendirin',
            'slug' => 'musteri-yonetimi',
            'items' => [
                [
                    'icon' => 'fas fa-address-card',
                    'color' => 'blue',
                    'title' => 'Detaylı Müşteri Profilleri',
                    'description' => 'Müşterilerinizin tüm bilgilerini, notlarını, toplantılarını, görevlerini, dosyalarını ve daha fazlasını içeren kapsamlı bir müşteri görünümü edinin.'
                ],
                [
                    'icon' => 'fas fa-database',
                    'color' => 'green',
                    'title' => 'Özel Alanlar',
                    'description' => 'Müşteri kayıtlarına özel alanlarla ek bilgiler saklayın.'
                ],
                [
                    'icon' => 'fas fa-list',
                    'color' => 'orange',
                    'title' => 'Müşteri Listeleri',
                    'description' => 'Daha iyi segmentasyon ve hedefleme için statik veya dinamik müşteri listeleri oluşturun.'
                ],
                [
                    'icon' => 'fas fa-chart-bar',
                    'color' => 'purple',
                    'title' => 'Kontrol Paneli',
                    'description' => 'Aktivite zaman çizelgesi, yaklaşan etkinlikler, aktif müşteriler ve daha fazlasını içeren kapsamlı bir kontrol paneli.'
                ],
                [
                    'icon' => 'fas fa-upload',
                    'color' => 'red',
                    'title' => 'İçe Aktarma Şablonu',
                    'description' => 'Özel alanlar da dahil olmak üzere müşterilerinizi içe aktarmak için düzgün biçimlendirilmiş CSV elektronik tablosunu kullanın.'
                ],
                [
                    'icon' => 'fas fa-mobile-alt',
                    'color' => 'teal',
                    'title' => 'Mobil CRM',
                    'description' => 'Her yerden, her zaman erişim. Tam duyarlı mobil web uygulaması herhangi bir cihaz için otomatik olarak uyarlanır.'
                ]
            ]
        ],
        [
            'title' => 'İş Talep Yönetimi',
            'subtitle' => 'Servis taleplerini kaydedin, teknisyen atayın ve süreçleri takip edin',
            'slug' => 'is-talep-yonetimi',
            'items' => [
                [
                    'icon' => 'fas fa-barcode',
                    'color' => 'blue',
                    'title' => 'Hızlı Servis Kaydı',
                    'description' => 'Müşteri seçimi, cihaz bilgisi, arıza açıklaması işlemlerini tek ekrandan hızlıca tamamlayın. Barkod okuma ile hatasız kayıt oluşturun.'
                ],
                [
                    'icon' => 'fas fa-user-cog',
                    'color' => 'green',
                    'title' => 'Teknisyen Atama',
                    'description' => 'Servisleri uygun teknisyenlere atayın, iş yüklerini dengeleyin. Kimin elinde kaç iş var anlık olarak görün.'
                ],
                [
                    'icon' => 'fas fa-bell',
                    'color' => 'orange',
                    'title' => 'Otomatik Bildirimler',
                    'description' => 'Cihazın durumunu adım adım izleyin. Her aşamada müşteriye otomatik SMS ve email bildirimleri gönderin.'
                ],
                [
                    'icon' => 'fas fa-camera',
                    'color' => 'purple',
                    'title' => 'Fotoğraflı Arıza Kaydı',
                    'description' => 'Cihazın arızalı durumunu fotoğraf ve video ile kaydedin. Müşteri ile anlaşmazlık durumlarında kanıt oluşturun.'
                ],
                [
                    'icon' => 'fas fa-shield-alt',
                    'color' => 'red',
                    'title' => 'Garanti Takibi',
                    'description' => 'Garanti kapsamındaki cihazları otomatik tespit edin. Garanti süresi dolmadan müşterilerinizi bilgilendirin.'
                ],
                [
                    'icon' => 'fas fa-print',
                    'color' => 'teal',
                    'title' => 'Servis Formu Yazdırma',
                    'description' => 'Profesyonel servis formları ve etiketler yazdırın. QR kod ile hızlı sorgulama imkanı.'
                ]
            ]
        ],
        [
            'title' => 'Mobil Saha Yönetimi',
            'subtitle' => 'Teknisyenleriniz sahadan mobil cihazlarla tüm işlemleri yapabilir',
            'slug' => 'mobil-saha-yonetimi',
            'items' => [
                [
                    'icon' => 'fas fa-mobile-alt',
                    'color' => 'blue',
                    'title' => 'Responsive Mobil Arayüz',
                    'description' => 'Uygulama yüklemeden telefon veya tabletten sisteme girin. Her cihaza uyumlu responsive tasarım.'
                ],
                [
                    'icon' => 'fas fa-map-marker-alt',
                    'color' => 'green',
                    'title' => 'GPS ve Navigasyon',
                    'description' => 'Teknisyenler kendilerine atanan işleri görür, adres tarifi alarak müşteriye en kısa yoldan ulaşır.'
                ],
                [
                    'icon' => 'fas fa-pen-nib',
                    'color' => 'orange',
                    'title' => 'Dijital İmza',
                    'description' => 'İş bitiminde müşteri imzasını tablet ekranından dijital olarak alın. Kağıtsız süreç yönetimi.'
                ],
                [
                    'icon' => 'fas fa-camera',
                    'color' => 'purple',
                    'title' => 'Sahadan Fotoğraf',
                    'description' => 'Onarım öncesi ve sonrası fotoğrafları anında sisteme yükleyin. Müşteri memnuniyetini artırın.'
                ],
                [
                    'icon' => 'fas fa-cubes',
                    'color' => 'red',
                    'title' => 'Araç Stoğu',
                    'description' => 'Teknisyen sahada ihtiyaç duyduğu parçayı talep edebilir veya aracındaki stoktan düşebilir.'
                ],
                [
                    'icon' => 'fas fa-receipt',
                    'color' => 'teal',
                    'title' => 'Mobil Tahsilat',
                    'description' => 'Sahadan fatura kesimi ve tahsilat işlemlerini gerçekleştirin. Kasaya gitmeden iş tamamlayın.'
                ]
            ]
        ],
        [
            'title' => 'Stok ve Yedek Parça',
            'subtitle' => 'Parça stoklarınızı takip edin, kritik seviyelerde otomatik uyarı alın',
            'slug' => 'stok-parca',
            'items' => [
                [
                    'icon' => 'fas fa-box-open',
                    'color' => 'blue',
                    'title' => 'Detaylı Stok Kartları',
                    'description' => 'Her parça için alış/satış fiyatı, KDV oranı, raf yeri ve uyumlu cihaz modellerini kaydedin.'
                ],
                [
                    'icon' => 'fas fa-exclamation-circle',
                    'color' => 'green',
                    'title' => 'Kritik Stok Uyarıları',
                    'description' => 'Belirlediğiniz adedin altına düşen ürünler için otomatik uyarı alın. Parça bitmeden sipariş verin.'
                ],
                [
                    'icon' => 'fas fa-exchange-alt',
                    'color' => 'orange',
                    'title' => 'Hareket Geçmişi',
                    'description' => 'Hangi parça hangi serviste kullanıldı, ne zaman alındı? Tüm envanter hareketlerini şeffafça izleyin.'
                ],
                [
                    'icon' => 'fas fa-warehouse',
                    'color' => 'purple',
                    'title' => 'Çoklu Depo Yönetimi',
                    'description' => 'Birden fazla depo ve raf sistemi ile stoklarınızı organize edin. Depo transferleri yapın.'
                ],
                [
                    'icon' => 'fas fa-clipboard-list',
                    'color' => 'red',
                    'title' => 'Stok Sayım',
                    'description' => 'Dönemsel stok sayımları yapın, fireleri kaydedin. Fiziksel ve sistem stoğunu eşitleyin.'
                ],
                [
                    'icon' => 'fas fa-chart-pie',
                    'color' => 'teal',
                    'title' => 'Karlılık Analizi',
                    'description' => 'En çok giden parçaları analiz edin. Alış-satış raporları ile karlılığınızı kontrol edin.'
                ]
            ]
        ],
        [
            'title' => 'Fatura ve Finans Yönetimi',
            'subtitle' => 'E-fatura oluşturun, gelir-gider takibi yapın, muhasebe entegrasyonu sağlayın',
            'slug' => 'fatura-yonetimi',
            'items' => [
                [
                    'icon' => 'fas fa-file-invoice',
                    'color' => 'blue',
                    'title' => 'E-Fatura Entegrasyonu',
                    'description' => 'Paraşüt entegrasyonu ile e-fatura ve e-arşiv fatura oluşturun. GİB onaylı faturalama yapın.'
                ],
                [
                    'icon' => 'fas fa-wallet',
                    'color' => 'green',
                    'title' => 'Cari Hesap Takibi',
                    'description' => 'Müşteri bazlı borç/alacak durumunu takip edin. Vadesi geçen faturaları görün.'
                ],
                [
                    'icon' => 'fas fa-credit-card',
                    'color' => 'orange',
                    'title' => 'Çoklu Ödeme Yöntemi',
                    'description' => 'Nakit, kredi kartı, havale, çek gibi farklı ödeme yöntemlerini kaydedin.'
                ],
                [
                    'icon' => 'fas fa-chart-line',
                    'color' => 'purple',
                    'title' => 'Gelir-Gider Raporları',
                    'description' => 'Dönemsel gelir-gider analizleri yapın. Karlılığınızı grafiklerle görselleştirin.'
                ],
                [
                    'icon' => 'fas fa-receipt',
                    'color' => 'red',
                    'title' => 'Proforma Fatura',
                    'description' => 'Müşterilerinize proforma fatura gönderin. Onay sonrası otomatik olarak e-faturaya çevirin.'
                ],
                [
                    'icon' => 'fas fa-file-excel',
                    'color' => 'teal',
                    'title' => 'Excel Raporlama',
                    'description' => 'Tüm fatura ve tahsilat verilerinizi Excel formatında dışa aktarın.'
                ]
            ]
        ],
        [
            'title' => 'Raporlama ve Analiz',
            'subtitle' => 'İşletmenizin her yönünü detaylı raporlarla analiz edin ve doğru kararlar alın',
            'slug' => 'raporlama-analiz',
            'items' => [
                [
                    'icon' => 'fas fa-chart-pie',
                    'color' => 'blue',
                    'title' => 'Hazır Rapor Şablonları',
                    'description' => 'Standart şablonlardan tek tıkla rapor oluşturun. Excel veya PDF olarak indirin.'
                ],
                [
                    'icon' => 'fas fa-filter',
                    'color' => 'green',
                    'title' => 'Özelleştirilebilir Raporlar',
                    'description' => 'Gelişmiş arama filtreleri ile kendi özel raporlarınızı oluşturun ve kaydedin.'
                ],
                [
                    'icon' => 'fas fa-calendar-check',
                    'color' => 'orange',
                    'title' => 'Otomatik Rapor Gönderimi',
                    'description' => 'Haftalık veya aylık raporları otomatik olarak email ile alın.'
                ],
                [
                    'icon' => 'fas fa-users-cog',
                    'color' => 'purple',
                    'title' => 'Teknisyen Performansı',
                    'description' => 'Teknisyenlerin tamamladığı iş sayısı, ortalama tamamlanma süresi gibi metrikleri görün.'
                ],
                [
                    'icon' => 'fas fa-star',
                    'color' => 'red',
                    'title' => 'Müşteri Memnuniyeti',
                    'description' => 'Müşteri derecelendirmelerini ve geri bildirimlerini analiz edin.'
                ],
                [
                    'icon' => 'fas fa-boxes',
                    'color' => 'teal',
                    'title' => 'Stok Analizi',
                    'description' => 'En çok kullanılan parçalar, stok devir hızı ve karlılık analizleri yapın.'
                ]
            ]
        ],
        [
            'title' => 'Entegrasyonlar',
            'subtitle' => 'Serbis\'i kullandığınız diğer platformlarla entegre edin',
            'slug' => 'entegrasyonlar',
            'items' => [
                [
                    'icon' => 'fas fa-calculator',
                    'color' => 'blue',
                    'title' => 'Paraşüt Muhasebe',
                    'description' => 'Paraşüt ile entegre çalışın. Faturalarınız otomatik olarak muhasebe yazılımınıza aktarılsın.'
                ],
                [
                    'icon' => 'fas fa-sms',
                    'color' => 'green',
                    'title' => 'SMS Entegrasyonu',
                    'description' => 'Netgsm ve Verimor SMS entegrasyonu ile müşterilerinize otomatik bildirimler gönderin.'
                ],
                [
                    'icon' => 'fas fa-envelope',
                    'color' => 'orange',
                    'title' => 'Email Gönderimi',
                    'description' => 'SMTP ayarları ile kendi email sunucunuz üzerinden profesyonel emailler gönderin.'
                ],
                [
                    'icon' => 'fas fa-phone',
                    'color' => 'purple',
                    'title' => 'Santral Entegrasyonu',
                    'description' => 'Verimor Santral ve Hipcall entegrasyonu ile gelen aramaları otomatik olarak müşteri kayıtlarına bağlayın.'
                ],
                [
                    'icon' => 'fas fa-whatsapp',
                    'color' => 'green',
                    'title' => 'WhatsApp Business',
                    'description' => 'WhatsApp Business API ile müşterilerinizle WhatsApp üzerinden iletişim kurun.'
                ],
                [
                    'icon' => 'fas fa-plug',
                    'color' => 'teal',
                    'title' => 'API Erişimi',
                    'description' => 'Kendi yazılımlarınızla Serbis\'i entegre edin. RESTful API dokümantasyonuna erişin.'
                ]
            ]
        ],
        [
            'title' => 'Destek ve Eğitim',
            'subtitle' => '7/24 teknik destek ve kapsamlı eğitim materyalleri',
            'slug' => 'destek-yardim',
            'items' => [
                [
                    'icon' => 'fas fa-headset',
                    'color' => 'blue',
                    'title' => '7/24 Canlı Destek',
                    'description' => 'Telefon, email ve canlı chat üzerinden teknik destek ekibimize her zaman ulaşın.'
                ],
                [
                    'icon' => 'fas fa-video',
                    'color' => 'green',
                    'title' => 'Video Eğitimler',
                    'description' => 'Her özellik için hazırlanmış detaylı video eğitim serileri ile sistemi öğrenin.'
                ],
                [
                    'icon' => 'fas fa-book',
                    'color' => 'orange',
                    'title' => 'Kullanım Kılavuzu',
                    'description' => 'Detaylı dokümantasyon ve adım adım kılavuzlarla sistemi kolayca kullanmaya başlayın.'
                ],
                [
                    'icon' => 'fas fa-chalkboard-teacher',
                    'color' => 'purple',
                    'title' => 'Birebir Eğitim',
                    'description' => 'Ekibiniz için online veya yerinde birebir eğitim seansları düzenleyin.'
                ],
                [
                    'icon' => 'fas fa-sync',
                    'color' => 'red',
                    'title' => 'Ücretsiz Güncellemeler',
                    'description' => 'Tüm yeni özellikler ve güncellemeler ücretsiz olarak hesabınıza eklenir.'
                ],
                [
                    'icon' => 'fas fa-user-shield',
                    'color' => 'teal',
                    'title' => 'Veri Güvenliği',
                    'description' => 'Verileriniz AWS sunucularında şifrelenmiş olarak saklanır. Günlük otomatik yedekleme.'
                ]
            ]
        ]
    ];

    return view('frontend.frontend_pages.features', compact('featureCategories'));
}
public function FeatureDetail($slug)
{
    $featureDetails = [
        'musteri-yonetimi' => [
            'title' => 'Müşteri Yönetimi',
            'subtitle' => 'Tüm müşteri bilgilerinizi tek merkezden yönetin, geçmişi takip edin',
            'hero_image' => 'frontend/img/features/musteri_yonetimi.jpg',
            'description' => 'Serbis Müşteri Yönetimi modülü ile müşterilerinizin tüm bilgilerini, geçmiş işlemlerini, cihaz kayıtlarını ve iletişim geçmişini tek ekranda görüntüleyin. Detaylı müşteri profilleri oluşturun, notlar ekleyin ve müşteri memnuniyetini artırın.',
            'benefits' => [
                [
                    'title' => 'Detaylı Müşteri Profilleri',
                    'description' => 'Her müşteri için ad, soyad, telefon, email, adres gibi temel bilgilerin yanı sıra özel notlar, etiketler ve kategoriler ekleyin.',
                    'mini_features' => [
                        ['icon' => 'fas fa-bolt', 'label' => 'Hızlı Kayıt'],
                        ['icon' => 'fas fa-database', 'label' => 'Detaylı Bilgi'],
                        ['icon' => 'fas fa-tag', 'label' => 'Etiketleme']
                    ]
                ],
                [
                    'title' => 'Geçmiş İşlem Takibi',
                    'description' => 'Müşterinin daha önce yaptırdığı tüm servisleri, satın aldığı parçaları ve ödemelerini kronolojik sırada görün.',
                    'mini_features' => [
                        ['icon' => 'fas fa-history', 'label' => 'Tüm Geçmiş'],
                        ['icon' => 'fas fa-chart-line', 'label' => 'Analiz'],
                        ['icon' => 'fas fa-filter', 'label' => 'Filtreleme']
                    ]
                ],
                [
                    'title' => 'Hızlı Arama ve Filtreleme',
                    'description' => 'Binlerce müşteri arasında isim, telefon, email veya müşteri numarası ile anında arama yapın.',
                    'mini_features' => [
                        ['icon' => 'fas fa-search', 'label' => 'Anında Arama'],
                        ['icon' => 'fas fa-sliders-h', 'label' => 'Gelişmiş Filtre'],
                        ['icon' => 'fas fa-list', 'label' => 'Listeleme']
                    ]
                ],
                [
                    'title' => 'Otomatik SMS/Email',
                    'description' => 'Doğum günü kutlamaları, servis hazır bildirimleri gibi otomatik mesajlar gönderin.',
                    'mini_features' => [
                        ['icon' => 'fas fa-sms', 'label' => 'Toplu SMS'],
                        ['icon' => 'fas fa-envelope', 'label' => 'Email'],
                        ['icon' => 'fas fa-clock', 'label' => 'Zamanlama']
                    ]
                ]
            ],
            'features_list' => [
                'Detaylı müşteri kartları ve profil yönetimi',
                'Müşteri geçmişi ve işlem kronolojisi',
                'Toplu SMS ve Email gönderimi',
                'Müşteri segmentasyonu ve etiketleme',
                'Özel not ve hatırlatma sistemi',
                'Excel içe-dışa aktarım',
                'Müşteri bazlı rapor ve analizler',
                'Cari hesap takibi ve borç/alacak durumu'
            ],
            'stats' => [
                ['number' => '%60', 'label' => 'Daha Hızlı Kayıt'],
                ['number' => '3 sn', 'label' => 'Ortalama Arama Süresi'],
                ['number' => '500+', 'label' => 'Aktif Kullanıcı'],
                ['number' => '%99', 'label' => 'Müşteri Memnuniyeti'],
            ],
            'faqs' => [
                [
                    'question' => 'Müşteri bilgilerimi nasıl içe aktarabilirim?',
                    'answer' => 'Excel veya CSV formatında müşteri listenizi hazırlayıp sisteme toplu olarak yükleyebilirsiniz. Sistem otomatik olarak müşteri kartlarını oluşturur ve gerekli alanları eşleştirir. İçe aktarma sırasında hatalı veya eksik kayıtlar için uyarı alırsınız.'
                ],
                [
                    'question' => 'Müşteri verilerim güvende mi?',
                    'answer' => 'Evet, tüm müşteri verileri AWS sunucularında şifrelenmiş olarak saklanır. Günlük otomatik yedekleme yapılır ve KVKK uyumlu veri koruma politikalarımız bulunur. Verilerinize sadece sizin yetkilendirdiğiniz personel erişebilir.'
                ],
                [
                    'question' => 'Müşterilerimi gruplandırabilir miyim?',
                    'answer' => 'Evet, müşteri segmentasyonu için etiket ve kategori sistemimiz bulunur. VIP müşteriler, kurumsal müşteriler veya özel kampanya grupları gibi istediğiniz kategorileri oluşturabilir ve bu gruplara özel işlemler yapabilirsiniz.'
                ],
                [
                    'question' => 'Müşteri iletişim geçmişi tutulur mu?',
                    'answer' => 'Evet, her müşteri için gönderilen SMS, email, yapılan aramalar ve not kayıtları zaman damgalı olarak tutulur. Hangi personelin ne zaman müşteriyle iletişime geçtiğini detaylı şekilde görebilirsiniz.'
                ],
                [
                    'question' => 'Kaç müşteri kaydı tutabilirim?',
                    'answer' => 'Serbis\'te müşteri sayısı konusunda bir sınırlama yoktur. İster 100, ister 100.000 müşteri olsun, sistem aynı performansla çalışır. Hızlı arama ve filtreleme özellikleri sayesinde büyük müşteri tabanlarını da kolayca yönetebilirsiniz.'
                ]
            ]
        ],
        

        'is-talep-yonetimi' => [
            'title' => 'İş Talep Yönetimi',
            'subtitle' => 'Servis taleplerini kaydedin, teknisyen atayın, süreçleri takip edin',
            'hero_image' => 'frontend/img/features/is-talep.jpg',
            'description' => 'İş Talep Yönetimi modülü ile gelen her servis talebini sistematik bir şekilde kaydedin. Müşteri bilgisi, cihaz bilgisi, arıza açıklaması ve öncelik durumunu belirleyin. Teknisyen ataması yapın ve iş sürecini baştan sona eksiksiz takip edin.',
            'benefits' => [
                [
                    'title' => 'Hızlı Servis Kaydı',
                    'description' => 'Müşteri seçimi, cihaz bilgisi, arıza açıklaması işlemlerini tek ekrandan hızlıca tamamlayın. Seri no veya barkod ile hatasız kayıt oluşturun.',
                    'mini_features' => [
                        ['icon' => 'fas fa-barcode', 'label' => 'Barkod Okuma'],
                        ['icon' => 'fas fa-edit', 'label' => 'Hızlı Giriş'],
                        ['icon' => 'fas fa-camera', 'label' => 'Fotoğraf Ekleme']
                    ]
                ],
                [
                    'title' => 'Teknisyen Atama',
                    'description' => 'Servisleri uygun teknisyenlere atayın, iş yüklerini dengeleyin. Kimin elinde kaç iş var anlık olarak görün.',
                    'mini_features' => [
                        ['icon' => 'fas fa-user-cog', 'label' => 'Personel Atama'],
                        ['icon' => 'fas fa-balance-scale', 'label' => 'İş Yükü'],
                        ['icon' => 'fas fa-tasks', 'label' => 'Görev Takibi']
                    ]
                ],
                [
                    'title' => 'Durum Takibi ve Bildirim',
                    'description' => 'Cihazın durumunu (Beklemede, Onarımda, Hazır) adım adım izleyin. Her aşamada müşteriye otomatik bilgilendirme gitsin.',
                    'mini_features' => [
                        ['icon' => 'fas fa-bell', 'label' => 'Oto Bildirim'],
                        ['icon' => 'fas fa-step-forward', 'label' => 'Süreç Adımları'],
                        ['icon' => 'fas fa-check-circle', 'label' => 'Onay Mekanizması']
                    ]
                ],
                [
                    'title' => 'Öncelik ve Garanti',
                    'description' => 'Acil işleri öne alın, garanti kapsamındaki cihazları otomatik tespit edin. VIP müşterilere özel servis önceliği sağlayın.',
                    'mini_features' => [
                        ['icon' => 'fas fa-star', 'label' => 'VIP Öncelik'],
                        ['icon' => 'fas fa-shield-alt', 'label' => 'Garanti Takibi'],
                        ['icon' => 'fas fa-exclamation-triangle', 'label' => 'Acil İşler']
                    ]
                ]
            ],
            'features_list' => [
                'Hızlı servis kaydı ve barkod desteği',
                'Sürükle bırak teknisyen atama',
                'Otomatik SMS ve WhatsApp bildirimleri',
                'Fotoğraflı ve videolu arıza kaydı',
                'Parça talep ve onay sistemi',
                'Servis formu ve etiket yazdırma',
                'Garanti süresi sorgulama',
                'Cihaz seri no/IMEI takibi'
            ],
            'stats' => [
                ['number' => '%40', 'label' => 'Daha Hızlı Servis'],
                ['number' => '2 Kat', 'label' => 'Müşteri Geri Dönüşü'],
                ['number' => '7/24', 'label' => 'Kesintisiz Takip'],
                ['number' => '%100', 'label' => 'Kayıt Güvenliği'],
            ],
        ],

        'mobil-saha-yonetimi' => [
            'title' => 'Mobil Saha Yönetimi',
            'subtitle' => 'Teknisyenleriniz sahadan mobil cihazlarla işlem yapabilir',
            'hero_image' => 'frontend/img/features/mobil-saha-yonetimi.jpg',
            'description' => 'Mobil uyumlu Serbis arayüzü ile teknisyenleriniz sahada tablet veya telefonlarından tüm işlemleri gerçekleştirebilir. İş listesini görüntüleme, durum güncelleme, fotoğraf yükleme ve müşteri imzası alma işlemleri artık cebinizde.',
            'benefits' => [
                [
                    'title' => 'Her Yerden Erişim',
                    'description' => 'Responsive tasarım sayesinde uygulama yüklemeden telefon veya tabletten sisteme girin. Sahada ofis konforunu yaşayın.',
                    'mini_features' => [
                        ['icon' => 'fas fa-mobile-alt', 'label' => 'Mobil Uyumlu'],
                        ['icon' => 'fas fa-cloud', 'label' => 'Bulut Tabanlı'],
                        ['icon' => 'fas fa-wifi', 'label' => 'Her Yerden']
                    ]
                ],
                [
                    'title' => 'Anlık İş Emri',
                    'description' => 'Teknisyenler kendilerine atanan işleri anında bildirim olarak görür. Adres tarifi alarak müşteriye en kısa yoldan ulaşır.',
                    'mini_features' => [
                        ['icon' => 'fas fa-map-marker-alt', 'label' => 'Navigasyon'],
                        ['icon' => 'fas fa-bolt', 'label' => 'Anlık Bildirim'],
                        ['icon' => 'fas fa-route', 'label' => 'Rota Planı']
                    ]
                ],
                [
                    'title' => 'Fotoğraf ve Dijital İmza',
                    'description' => 'Onarım öncesi ve sonrası fotoğrafları sisteme yükleyin. İş bitiminde müşteri imzasını tablet ekranından dijital olarak alın.',
                    'mini_features' => [
                        ['icon' => 'fas fa-camera', 'label' => 'Fotoğraf Yükle'],
                        ['icon' => 'fas fa-pen-nib', 'label' => 'Dijital İmza'],
                        ['icon' => 'fas fa-file-pdf', 'label' => 'Servis Fişi']
                    ]
                ],
                [
                    'title' => 'Sahadan Parça Talebi',
                    'description' => 'Teknisyen sahada ihtiyaç duyduğu parçayı sistemden talep edebilir veya aracındaki stoktan düşebilir.',
                    'mini_features' => [
                        ['icon' => 'fas fa-cubes', 'label' => 'Stok Kontrol'],
                        ['icon' => 'fas fa-share-square', 'label' => 'Parça İsteme'],
                        ['icon' => 'fas fa-calculator', 'label' => 'Fiyatlandırma']
                    ]
                ]
            ],
            'features_list' => [
                'Mobil uyumlu responsive arayüz',
                'Google Maps entegrasyonu',
                'Sahadan fotoğraf ve video yükleme',
                'Ekranda müşteri imzası alma',
                'Mobil cihazdan fatura/tahsilat girişi',
                'Araç stoğu yönetimi',
                'Konum bazlı teknisyen takibi',
                'QR Kod ile cihaz sorgulama'
            ],
            'stats' => [
                ['number' => '%75', 'label' => 'Kağıt Tasarrufu'],
                ['number' => '15 dk', 'label' => 'Servis Başı Kazanç'],
                ['number' => '%95', 'label' => 'Doğru Konum'],
                ['number' => '0', 'label' => 'Veri Kaybı'],
            ],
        ],

        'stok-parca' => [
            'title' => 'Stok ve Yedek Parça',
            'subtitle' => 'Parça stoklarınızı takip edin, kritik seviyelerde uyarı alın',
            'hero_image' => 'frontend/img/features/stok-yonetimi.jpg',
            'description' => 'Stok Yönetimi modülü ile tüm yedek parça, sarf malzemeleri ve aksesuarlarınızın envanterini profesyonelce yönetin. Giriş-çıkış hareketlerini kaydedin, kritik stok seviyelerinde otomatik uyarılar alın ve maliyetlerinizi kontrol altında tutun.',
            'benefits' => [
                [
                    'title' => 'Akıllı Stok Kartları',
                    'description' => 'Her parça için alış/satış fiyatı, KDV oranı, raf yeri ve uyumlu modelleri içeren detaylı kartlar oluşturun.',
                    'mini_features' => [
                        ['icon' => 'fas fa-box-open', 'label' => 'Ürün Kartı'],
                        ['icon' => 'fas fa-barcode', 'label' => 'Barkodlama'],
                        ['icon' => 'fas fa-tags', 'label' => 'Fiyat Yönetimi']
                    ]
                ],
                [
                    'title' => 'Kritik Stok Uyarıları',
                    'description' => 'Belirlediğiniz adedin altına düşen ürünler için sistem sizi uyarır. Parça bitmeden sipariş vererek iş kaybını önleyin.',
                    'mini_features' => [
                        ['icon' => 'fas fa-exclamation-circle', 'label' => 'Azalan Stok'],
                        ['icon' => 'fas fa-envelope-open-text', 'label' => 'Mail Uyarısı'],
                        ['icon' => 'fas fa-shopping-cart', 'label' => 'Oto Sipariş']
                    ]
                ],
                [
                    'title' => 'Hareket Geçmişi',
                    'description' => 'Hangi parça hangi serviste kullanıldı, ne zaman alındı, kime satıldı? Tüm envanter hareketlerini şeffafça izleyin.',
                    'mini_features' => [
                        ['icon' => 'fas fa-exchange-alt', 'label' => 'Giriş/Çıkış'],
                        ['icon' => 'fas fa-user-check', 'label' => 'Personel Takibi'],
                        ['icon' => 'fas fa-calendar-alt', 'label' => 'Tarihçe']
                    ]
                ],
                [
                    'title' => 'Sayım ve Raporlama',
                    'description' => 'Dönemsel stok sayımları yapın, fireleri kaydedin. En çok giden parçaları analiz ederek karlılığınızı artırın.',
                    'mini_features' => [
                        ['icon' => 'fas fa-clipboard-list', 'label' => 'Sayım Modülü'],
                        ['icon' => 'fas fa-chart-pie', 'label' => 'Karlılık Analizi'],
                        ['icon' => 'fas fa-file-excel', 'label' => 'Excel Çıktı']
                    ]
                ]
            ],
            'features_list' => [
                'Barkod ve QR kod destekli stok takibi',
                'Kritik stok seviyesi bildirimleri',
                'Tedarikçi ve satın alma yönetimi',
                'Çoklu depo ve raf sistemi',
                'Servis bağlantılı otomatik stok düşümü',
                'Sayım ve envanter eşitleme',
                'Alış/Satış raporları',
                'Toplu Excel ile ürün yükleme'
            ],
            'stats' => [
                ['number' => '%30', 'label' => 'Maliyet Avantajı'],
                ['number' => '%100', 'label' => 'Stok Doğruluğu'],
                ['number' => '0', 'label' => 'Parça Bekleme'],
                ['number' => '10k+', 'label' => 'Ürün Kapasitesi'],
            ],
        ],
    ];

    if (!isset($featureDetails[$slug])) {
        abort(404);
    }

    $feature = $featureDetails[$slug];

    return view('frontend.frontend_pages.feature_detail', compact('feature'));
}
public function Integrations()
{
    // Entegrasyonlar - Kategorilere göre
    $integrations = [
        'SMS' => [
            [
                'name' => 'NETGSM',
                'logo' => 'frontend/img/integrations/netgsm.png',
                'description' => 'NETGSM SMS entegrasyonu ile müşterilerinize toplu SMS gönderin',
                'category' => 'SMS',
                'detail' => 'Türkiye\'nin lider SMS sağlayıcısı ile entegre çalışın.',
                'features' => [
                    'Toplu SMS gönderimi',
                    'SMS şablonları',
                    'Gönderim raporları',
                    'API entegrasyonu'
                ]
            ],
            [
                'name' => 'TESCOM',
                'logo' => 'frontend/img/integrations/tescom.png',
                'description' => 'TESCOM SMS servisi entegrasyonu',
                'category' => 'SMS',
                'detail' => 'Tescom altyapısı ile güvenilir SMS iletişimi.',
                'features' => [
                    'Yüksek iletim oranı',
                    'Otomatik bildirimler',
                    'Özelleştirilebilir şablonlar',
                    'Detaylı raporlama'
                ]
            ],
            [
                'name' => 'SOLVELINE',
                'logo' => 'frontend/img/integrations/solveline.png',
                'description' => 'SOLVELINE SMS gönderim sistemi',
                'category' => 'SMS',
                'detail' => 'Solveline entegrasyonu ile SMS ve santral hizmetlerini tek platformda yönetin.',
                'features' => [
                    'SMS gönderimi',
                    'Santral entegrasyonu',
                    'Çift yönlü iletişim',
                    'Anlık bildirimler'
                ]
            ],
            [
                'name' => 'VERIMOR',
                'logo' => 'frontend/img/integrations/verimor.jpeg',
                'description' => 'VERIMOR SMS entegrasyonu',
                'category' => 'SMS',
                'detail' => 'Verimor altyapısı ile hızlı ve güvenilir SMS gönderimi.',
                'features' => [
                    'Ekonomik SMS paketleri',
                    'Hızlı iletim',
                    'OTP desteği',
                    'Kampanya yönetimi'
                ]
            ]
        ],
        'Fatura' => [
            [
                'name' => 'PARAŞÜT',
                'logo' => 'frontend/img/integrations/parasut.png',
                'description' => 'Paraşüt muhasebe yazılımı ile e-fatura entegrasyonu',
                'category' => 'Fatura',
                'detail' => 'Serbis\'ten kestiğiniz faturaları otomatik olarak Paraşüt\'e aktarın. Muhasebe işlemlerinizi kolaylaştırın.',
                'features' => [
                    'Otomatik fatura aktarımı',
                    'E-Fatura / E-Arşiv gönderimi',
                    'Cari hesap senkronizasyonu',
                    'Gelir-gider takibi',
                    'Muhasebe raporları'
                ]
            ]
        ],
        'Diğer' => [
            [
                'name' => 'HIPCALL',
                'logo' => 'frontend/img/integrations/hipcall.jpg',
                'description' => 'Hipcall santral entegrasyonu ile gelen aramaları otomatik kaydedin',
                'category' => 'Santral',
                'detail' => 'Müşteri aradığında bilgileri anında ekranda görün. Arama geçmişini otomatik kaydedin.',
                'features' => [
                    'Arayan numara tanıma',
                    'Müşteri kartı popup',
                    'Arama kaydı oluşturma',
                    'Webhook entegrasyonu',
                    'Çağrı geçmişi'
                ]
            ],
            [
                'name' => 'KOMBİ ARIZA KODLARI',
                'logo' => 'frontend/img/integrations/kombi-ariza-kodlari.png',
                'description' => 'Kombi arıza kodları veritabanı entegrasyonu',
                'category' => 'Araçlar',
                'detail' => 'Tüm kombi markalarının arıza kodlarına anında erişin. Teknisyenlerinizin işini kolaylaştırın.',
                'features' => [
                    '50+ marka desteği',
                    'Anlık kod sorgulama',
                    'Çözüm önerileri',
                    'Sürekli güncelleme'
                ]
            ]
        ],
        'Santral' => [
            [
                'name' => 'VERIMOR - SANTRAL',
                'logo' => 'frontend/img/integrations/verimor.jpeg',
                'description' => 'Verimor santral sistemi entegrasyonu',
                'category' => 'Santral',
                'detail' => 'Verimor bulut santral sistemi ile profesyonel telefon altyapısı. Çağrı yönlendirme ve IVR desteği.',
                'features' => [
                    'Bulut santral',
                    'IVR menü sistemi',
                    'Çağrı yönlendirme',
                    'Sesli karşılama'
                ]
            ]
        ]
    ];


     $faqs = [
        [
            'question' => 'Paraşüt entegrasyonu neleri kapsıyor?',
            'answer' => 'Paraşüt entegrasyonu ile Serbis üzerinden oluşturduğunuz servis fişlerini veya satışları tek tuşla e-fatura/e-arşiv olarak Paraşüt hesabınıza gönderebilirsiniz. Ayrıca cari hesaplarınız otomatik olarak senkronize edilir.'
        ],
        [
            'question' => 'SMS gönderimi için başlık (Originator) almalı mıyım?',
            'answer' => 'Evet, yasal düzenlemeler gereği SMS gönderebilmek için çalıştığınız SMS firmasından (NetGSM, Verimor vb.) firmanıza ait bir SMS başlığı almanız ve bunu onaylatmanız gerekmektedir.'
        ],
        [
            'question' => 'Santral entegrasyonu ne işe yarar?',
            'answer' => 'Santral entegrasyonu sayesinde ofis telefonunuz çaldığında, arayan numara Serbis ekranında "Popup" olarak açılır. Eğer arayan kayıtlı bir müşteriyse ismini ve son servis geçmişini görürsünüz; kayıtlı değilse tek tıkla yeni müşteri kartı oluşturabilirsiniz.'
        ],
        [
            'question' => 'Kendi kullandığım muhasebe programını entegre edebilir misiniz?',
            'answer' => 'Şu an için sadece listede belirtilen firmalarla (Paraşüt vb.) hazır entegrasyonumuz bulunmaktadır. Ancak özel entegrasyon talepleriniz için teknik ekibimizle iletişime geçebilirsiniz.'
        ],
        [
            'question' => 'Entegrasyon ayarlarını nasıl yapabilirim?',
            'answer' => 'Serbis panelinize giriş yaptıktan sonra "Ayarlar > Entegrasyonlar" menüsüne giderek, ilgili hizmet sağlayıcısından aldığınız API anahtarlarını (API Key/Secret) girmeniz yeterlidir.'
        ]
    ];

    return view('frontend.frontend_pages.integrations', compact('integrations', 'faqs'));
}
public function About()
{
    return view('frontend.frontend_pages.about');
}
public function Pricing()
{
    // Fiyatlandırma planları
    $pricing = [
        [
            'name' => 'Başlangıç',
            'icon' => 'fas fa-mobile-alt',
            'price' => 8400,
            'users' => 3,
            'storage' => '2 GB',
            'description' => 'Küçük işletmeler için temel özellikler',
            'features' => [
                'Max. 3 Kullanıcı',
                '2 GB Depolama Alanı',
                'Sınırsız Servis Kaydı',
                'Detaylı Servis Arama',
                'Detaylı Servis Raporlama',
                'Detaylı Prim Hesaplama',
                'Toplu Servis Planlama',
                'Servis Fişi Yazdırma',
                'Detaylı İstatistikler',
                'Sınırsız Kasa Kaydı',
            ]
        ],
        [
            'name' => 'Profesyonel',
            'icon' => 'fas fa-briefcase',
            'price' => 10800,
            'users' => 5,
            'storage' => '4 GB',
            'description' => 'Orta işletmeler için genişletilmiş özellikler',
            'features' => [
                'Max. 5 Kullanıcı',
                '4 GB Depolama Alanı',
                'Sınırsız Servis Kaydı',
                'Detaylı Servis Arama',
                'Detaylı Servis Raporlama',
                'Detaylı Prim Hesaplama',
                'Toplu Servis Planlama',
                'Servis Fişi Yazdırma',
                'Detaylı İstatistikler',
                'Sınırsız Kasa Kaydı',
                'Sınırsız Ürün Kaydı',
            ]
        ],
        [
            'name' => 'Kurumsal',
            'icon' => 'fas fa-gem',
            'price' => 21600,
            'users' => 10,
            'storage' => '8 GB',
            'description' => 'Büyük işletmeler için tam özellik seti',
            'features' => [
                'Max. 10 Kullanıcı',
                '8 GB Depolama Alanı',
                'Sınırsız Servis Kaydı',
                'Detaylı Servis Arama',
                'Detaylı Servis Raporlama',
                'Detaylı Prim Hesaplama',
                'Toplu Servis Planlama',
                'Servis Fişi Yazdırma',
                'Detaylı İstatistikler',
                'Sınırsız Kasa Kaydı',
                'Sınırsız Ürün Kaydı',
                'Gelen Çağrı Kaydı',
            ]
        ]
    ];

    return view('frontend.frontend_pages.pricing', compact('pricing'));
}
public function Contact()
{
    return view('frontend.frontend_pages.contact');
}

public function ContactSubmit(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'nullable|string|max:20',
        'message' => 'required|string',
    ]);

    // Mail gönderimi veya DB kayıt
    
    return back()->with('success', 'Mesajınız başarıyla gönderildi!');
}

    // public function Index() {
    //     $slide = HomeSlide::orderBy('id', 'asc')->get();
    //     $home_about = Faq::find(1);
    //     $home_section = Misyon::find(1);
    //     $products = Category::orderBy('id', 'desc')->take(8)->get();
    //     $settings = Settings::find(1);
    //     $pricing = SubscriptionPlan::active()->ordered()->get();
    //     $references = Reference::get();
    //     $faqs = Clients::orderBy('job','asc')->get();
    //     $features = Feature::orderBy('sira','asc')->get();
    //     return view('frontend.index', compact('slide','references','features' ,'faqs','pricing' ,'home_about','settings', 'home_section','products'));
    // }


    // public function Pricing() {
    //     $prices = SubscriptionPlan::active()->ordered()->get();
    //     return view('frontend.pages.pricing', compact('prices'));
    // }

        public function select($planId)
    {
        session(['selected_plan' => $planId]);
        session(['show_register' => true]); 
        return redirect()->route('giris');
    }

    public function Seo($s) {
        $tr = array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç','(',')','/',':',',',"'",'+','_','!','?','.');
        $eng = array('s','s','i','i','i','g','g','u','u','o','o','c','c','','','-','-','','','-','','','','');
        $s = str_replace($tr, $eng, $s);
        $s = mb_strtolower($s, 'UTF-8');
        $s = preg_replace('/&amp;amp;amp;amp;amp;amp;amp;amp;amp;.+?;/', '', $s);
        $s = preg_replace('/\s+/', '-', $s);
        $s = preg_replace('|-+|', '-', $s);
        $s = preg_replace('/#/', '', $s);
        $s = trim($s, '-');
        return $s;
    }

    protected function generateUserEmail($userEmail, $domain)
    {
        $username = explode('@', $userEmail)[0]; // E-postanın kullanıcı adını alır
        return $username . '@' . $domain; // Kullanıcı adı ve firma domainiyle yeni e-posta oluşturur
    }

    public function Register() {
        return view('frontend.auth.register');
    }

        public function validateStep(Request $request) 
    {
        $step = $request->input('step');
        
        try {
            if ($step == 1) {
                // Step 1: Plan selection validation
                $validatedData = $request->validate([
                    'subscription_plan' => 'required|exists:subscription_plans,id',
                ], [
                    'subscription_plan.required' => 'Lütfen bir abonelik planı seçiniz.',
                    'subscription_plan.exists' => 'Seçilen plan geçerli değil.',
                ]);
                
            } elseif ($step == 2) {
                // Step 2: Personal information validation
                $validatedData = $request->validate([
                    'subscription_plan' => 'required|exists:subscription_plans,id',
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:tenants,eposta',
                    'vergiNo' => 'required|max:10|unique:tenants,vergiNo',
                ], [
                    'subscription_plan.required' => 'Lütfen bir abonelik planı seçiniz.',
                    'subscription_plan.exists' => 'Seçilen plan geçerli değil.',
                    'name.required' => 'Ad Soyad alanı zorunludur.',
                    'name.max' => 'Ad Soyad alanı en fazla 255 karakter olmalıdır.',
                    'email.required' => 'E-posta alanı zorunludur.',
                    'email.email' => 'Geçerli bir e-posta adresi giriniz.',
                    'email.max' => 'E-posta alanı en fazla 255 karakter olmalıdır.',
                    'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
                    'vergiNo.required' => 'Vergi numarası alanı zorunludur.',
                    'vergiNo.max' => 'Vergi numarası alanı en fazla 10 karakter olmalıdır.',
                    'vergiNo.unique' => 'Bu vergi numarası zaten kayıtlı.',
                ]);
                
            } elseif ($step == 3) {
                // Step 3: Company information validation
                $validatedData = $request->validate([
                    'subscription_plan' => 'required|exists:subscription_plans,id',
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:tenants,eposta',
                    'vergiNo' => 'required|max:10|unique:tenants,vergiNo',
                    'firma_adi' => 'required|string|max:50',
                    'tel' => 'required|regex:/^[0-9\s]+$/|min:12',
                    'password' => 'required|min:6',
                ], [
                    'subscription_plan.required' => 'Lütfen bir abonelik planı seçiniz.',
                    'subscription_plan.exists' => 'Seçilen plan geçerli değil.',
                    'name.required' => 'Ad Soyad alanı zorunludur.',
                    'name.max' => 'Ad Soyad alanı en fazla 255 karakter olmalıdır.',
                    'email.required' => 'E-posta alanı zorunludur.',
                    'email.email' => 'Geçerli bir e-posta adresi giriniz.',
                    'email.max' => 'E-posta alanı en fazla 255 karakter olmalıdır.',
                    'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
                    'vergiNo.required' => 'Vergi numarası alanı zorunludur.',
                    'vergiNo.max' => 'Vergi numarası alanı en fazla 10 karakter olmalıdır.',
                    'vergiNo.unique' => 'Bu vergi numarası zaten kayıtlı.',
                    'firma_adi.required' => 'Firma Adı alanı zorunludur.',
                    'firma_adi.max' => 'Firma Adı alanı en fazla 50 karakter olmalıdır.',
                    'tel.required' => 'Telefon alanı zorunludur.',
                    'tel.regex' => 'Telefon formatı hatalıdır.',
                    'tel.min' => 'Telefon numarası en az 10 haneli olmalıdır.',
                    'password.required' => 'Şifre alanı zorunludur.',
                    'password.min' => 'Şifre en az 6 karakter olmalıdır.',
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Validasyon başarılı'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }

/**
 * Updated RegisterAction method to handle plan selection
 */
public function RegisterAction(Request $request) 
{
    $request->merge([
        'tel' => preg_replace('/\D/', '', $request->tel),
        'vergiNo' => preg_replace('/\D/', '', $request->vergiNo),
    ]);
    $validatedData = $request->validate([
        'subscription_plan' => 'required|exists:subscription_plans,id',
        'name' => 'required|string|max:255',
        'firma_adi' => 'required|string|max:50',
        'vergiNo' => 'required|digits:10|unique:tenants,vergiNo',
        'tel' => 'required|digits_between:10,11',
        'email' => 'required|email|max:255|unique:tenants,eposta',
        'password' => 'required|min:6',
    ], [
        'subscription_plan.required' => 'Lütfen bir abonelik planı seçiniz.',
        'subscription_plan.exists' => 'Seçilen plan geçerli değil.',
        'name.required' => 'Ad Soyad alanı zorunludur.',
        'name.max' => 'Ad Soyad alanı en fazla 255 karakter olmalıdır.',
        'firma_adi.required' => 'Firma Adı alanı zorunludur.',
        'firma_adi.max' => 'Firma Adı alanı en fazla 50 karakter olmalıdır.',
        'vergiNo.required' => 'Vergi numarası alanı zorunludur.',
        'vergiNo.max' => 'Vergi numarası alanı en fazla 10 karakter olmalıdır.',
        'vergiNo.unique' => 'Bu vergi numarası zaten kayıtlı.',
        'tel.required' => 'Telefon alanı zorunludur.',
        'tel.regex' => 'Telefon formatı hatalıdır.',
        'tel.min' => 'Telefon numarası en az 10 haneli olmalıdır.',
        'email.required' => 'E-posta alanı zorunludur.',
        'email.email' => 'Geçerli bir e-posta adresi giriniz.',
        'email.max' => 'E-posta alanı en fazla 255 karakter olmalıdır.',
        'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
        'password.required' => 'Şifre alanı zorunludur.',
        'password.min' => 'Şifre en az 6 karakter olmalıdır.',
    ]);

    // 6 haneli rastgele bir doğrulama kodu oluştur
    $verificationCode = rand(100000, 999999);

    // Store selected plan in session
    session(['selected_plan' => $validatedData['subscription_plan']]);

    // Kullanıcı verilerini ve kodu session'a kaydet
    $request->session()->put('registration_data', $validatedData);
    $request->session()->put('sms_verification_code', $verificationCode);
    $request->session()->put('sms_code_created_at', now());
    session(['phone_number' => $request->tel]);
    $request->session()->put('verification_attempts', 0);
    
    try {
        Log::info('SMS Gönderimi Başlıyor', [
            'phone' => $validatedData['tel'],
            'code' => $verificationCode
        ]);

        // SMS Servisi ile doğrulama kodu gönder
        $smsService = new TescomService();
        $smsResult = $smsService->sendVerificationCode($validatedData['tel'], $verificationCode);

        Log::info('SMS Servis Sonucu', [
            'result' => $smsResult
        ]);

        if (!$smsResult['success']) {
            Log::error('SMS Başarısız', [
                'result' => $smsResult
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'SMS gönderilemedi: ' . $smsResult['message']
            ], 500);
        }

        Log::info('SMS Başarılı, Response Hazırlanıyor');

        return response()->json([
            'success' => true,
            'message' => 'Doğrulama kodu telefonunuza gönderildi',
            'csrf_token' => csrf_token()
        ]);

    } catch (\Exception $e) {
        Log::error('SMS Gönderim Exception', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'phone' => $validatedData['tel']
        ]);

        return response()->json([
            'success' => false,
            'message' => 'SMS gönderilirken bir hata oluştu: ' . $e->getMessage()
        ], 500);
    }
}

    public function getSubscriptionPlans()
    {
        $plans = SubscriptionPlan::where('is_active', 1)
            ->orderBy('price', 'asc')
            ->get();

        $selectedPlanId = session('selected_plan');

        return response()->json([
            'success' => true,
            'plans' => $plans,
            'selected_plan_id' => $selectedPlanId
        ]);
    }

    public function verifySmsCode(Request $request) 
{
    $request->validate([
        'code' => 'required|numeric|digits:6'
    ], [
        'code.required' => 'Doğrulama kodu alanı zorunludur.',
        'code.numeric' => 'Doğrulama kodu sadece rakamlardan oluşmalıdır.',
        'code.digits' => 'Doğrulama kodu 6 haneli olmalıdır.'
    ]);

    $storedCode = $request->session()->get('sms_verification_code');
    $registrationData = $request->session()->get('registration_data');
    $codeCreatedAt = $request->session()->get('sms_code_created_at');
    $attempts = $request->session()->get('verification_attempts', 0);

    // Session kontrolü
    if (!$storedCode || !$registrationData) {
        return response()->json([
            'success' => false,
            'message' => 'Oturum süresi doldu. Lütfen kayıt işlemini baştan başlatın.'
        ], 400);
    }

    // Maksimum deneme kontrolü
    $maxAttempts = config('sms.verification.max_attempts', 3);
    if ($attempts >= $maxAttempts) {
        // Session'ı temizle
        $request->session()->forget([
            'registration_data', 
            'sms_verification_code', 
            'sms_code_created_at',
            'phone_number',
            'verification_attempts'
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Maksimum deneme hakkınız doldu. Lütfen yeniden kayıt olun.',
            'redirect' => route('giris')
        ], 400);
    }

    // Kod süresi kontrolü (3 dakika)
    $expiryMinutes = config('sms.verification.code_expiry_minutes', 3);
    if (now()->diffInMinutes($codeCreatedAt) >= $expiryMinutes) {
        $request->session()->forget([
            'registration_data', 
            'sms_verification_code', 
            'sms_code_created_at',
            'phone_number',
            'verification_attempts'
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Doğrulama kodu süresi doldu. Lütfen yeniden kayıt olun.',
            'redirect' => route('giris')
        ], 400);
    }

    // Kod kontrolü
    if ($request->code != $storedCode) {
        // Deneme sayısını artır
        $attempts++;
        $request->session()->put('verification_attempts', $attempts);
        
        $remainingAttempts = $maxAttempts - $attempts;
        
        return response()->json([
            'success' => false,
            'message' => "Doğrulama kodu hatalı. Kalan deneme hakkınız: {$remainingAttempts}"
        ], 400);
    }

    // Kod doğru - Kayıt işlemini tamamla
    try {
        $determinedPlanId = $registrationData['subscription_plan'] ?? session('selected_plan');
        $plan = SubscriptionPlan::find($determinedPlanId);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Abonelik planı bulunamadı.'
            ], 400);
        }

        // Tenant ve User oluştur
        $this->createTenantAndUser($registrationData, $plan);

        // Session temizle
        $request->session()->forget([
            'registration_data', 
            'sms_verification_code', 
            'sms_code_created_at',
            'phone_number',
            'verification_attempts',
            'selected_plan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kaydınız başarıyla tamamlandı!',
            'redirect' => route('register.success')
        ],200);

    } catch (\Exception $e) {
        Log::error('Kayıt Tamamlama Hatası', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Kayıt sırasında bir hata oluştu: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * SMS kodunu yeniden gönder
 */
public function resendSmsCode(Request $request)
{
    $registrationData = $request->session()->get('registration_data');
    $phoneNumber = $request->session()->get('phone_number');

    if (!$registrationData || !$phoneNumber) {
        return response()->json([
            'success' => false,
            'message' => 'Oturum süresi doldu. Lütfen kayıt işlemini baştan başlatın.'
        ], 400);
    }

    // Yeni doğrulama kodu oluştur 
    $verificationCode = rand(100000, 999999);

    // Session'ı güncelle
    $request->session()->put('sms_verification_code', $verificationCode);
    $request->session()->put('sms_code_created_at', now());
    $request->session()->put('verification_attempts', 0); // Deneme sayacını sıfırla

    try {
        // SMS gönder
        $smsService = new TescomService();
        $smsResult = $smsService->sendVerificationCode($phoneNumber, $verificationCode);

        if (!$smsResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'SMS gönderilemedi: ' . $smsResult['message']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Doğrulama kodu tekrar gönderildi'
        ]);

    } catch (\Exception $e) {
        Log::error('SMS Yeniden Gönderim Hatası', [
            'error' => $e->getMessage(),
            'phone' => $phoneNumber
        ]);

        return response()->json([
            'success' => false,
            'message' => 'SMS gönderilirken bir hata oluştu'
        ], 500);
    }
}
    
    /**
     * Tenant ve User oluşturma mantığını içeren özel bir metod.
     * Bu, kodu tekrar etmemek için iyi bir yöntemdir.
     */
    private function createTenantAndUser(array $data, $planId = null) {
        $baslik = $data['firma_adi'];
        $username = $this->Seo($baslik); // Seo metodunuzun bu class içinde olduğunu varsayıyorum.
        // 🔹 Kullanıcı adı çakışıyorsa sonuna rakam ekle
        $original = $username;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $original . '-' . $counter;
            $counter++;
        }

        $firmaAdiSlug = Str::slug($data['firma_adi'], '-');
        // 🔹 Tenant username (firma alan adı gibi düşünülen kısım)
        $tenantUsername = $this->Seo($data['firma_adi']) . '.com';
        $originalTenantUsername = $tenantUsername;
        $counterTenant = 1;

        while (Tenant::where('username', $tenantUsername)->exists()) {
            $baseName = str_replace('.com', '', $originalTenantUsername);
            $tenantUsername = $baseName . '-' . $counterTenant . '.com';
            $counterTenant++;
        }

        // Get the selected plan from form data or session
    $determinedPlanId = $data['subscription_plan'] ?? $planId ?? session('selected_plan');
    $plan = SubscriptionPlan::find($determinedPlanId);

    if (!$plan) {
        \Illuminate\Support\Facades\Log::warning("SubscriptionPlan not found for ID: {$determinedPlanId}. Using default plan.");
        // Get the cheapest plan as default
        $plan = SubscriptionPlan::where('is_active', 1)->orderBy('price', 'asc')->first();
        
        if (!$plan) {
            throw new \Exception("No active subscription plan found. Cannot create tenant.");
        }
    }

    // Parse limits and features if they are JSON
    $limits = is_string($plan->limits) ? json_decode($plan->limits, true) : $plan->limits;
    $features = is_string($plan->features) ? json_decode($plan->features, true) : $plan->features;


        $tenant = new Tenant([
            'name' => $data['name'],
            'firma_adi' => $data['firma_adi'],
            'vergiNo' => $data['vergiNo'],
            'firma_slug' => $firmaAdiSlug,
            'tel1' => $data['tel'],
            'eposta' => $data['email'],
            'username' => $tenantUsername,
            'kayitTarihi' => Carbon::now(),
            'bitisTarihi' => Carbon::now()->addDays(14),
            'status' => 0,
            'trial_starts_at' => Carbon::now(),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'subscription_ends_at' => Carbon::now()->addDays(14),
            'trial_used' => 1,
            'personelSayisi' => $limits['users'] ?? '3',
        'bayiSayisi' => $limits['dealers'] ?? '0',
        'stokSayisi' => $limits['stocks'] ?? '10',
        'konsinyeSayisi' => $limits['konsinye'] ?? '1',
        ]);
        $tenant->save();

        $tenant_id = $tenant->id;

        $user = new User([
            'name' => $data['name'],
            'username' => $username,
            'tel' => $data['tel'],
            'eposta' => $this->generateUserEmail($username, $tenant->username), // Bu metodunuzun da burada olduğunu varsayıyorum
            'tenant_id' => $tenant_id,
            'password' => Hash::make($data['password']),
            'status' => '1',
            'baslamaTarihi' => Carbon::now()->format('Y-m-d'),
        ]);
        $user->save();
        $user->syncRoles("Patron");

        TenantPrim::create([
            'firma_id' => $tenant_id,
            'operatorPrim' => 0.00,
            'operatorPrimTutari' => 0,
            'teknisyenPrim' => 0.00,
            'teknisyenPrimTutari' => 0,
            'atolyePrim' => 0.00,
            'atolyePrimTutari' => 0,
        ]);

        ServiceTime::create([
            'firma_id' => $tenant_id,
            'zaman' => '08:30',
            'created_at' => Carbon::now(),
        ]);

        session()->forget('selected_plan');
    }

    public function RegisterSuccess() {
        return view('frontend.auth.register_success');
    }

    public function Login(){
        return view('frontend.auth.login');
    }

    public function LoginAction(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'g-recaptcha-response' => 'required|recaptcha',
        ], [
            'g-recaptcha-response.required' => 'Güvenlik doğrulaması zorunludur.',
            'g-recaptcha-response.recaptcha' => 'Güvenlik doğrulaması doğrulaması başarısız. Lütfen tekrar deneyin.',
        ]);

        // E-posta adresinden domain'i al
        [$username, $domain] = explode('@', $request->email);

        // Domain ile tenant'ı doğrula
        $tenant = Tenant::where('username', $domain)->first();

        if (!$tenant) {
             // Başarısız giriş denemesini logla
            ActivityLogger::log('login_failed', 'Geçersiz firma girişi denemesi: ' . $domain, [
                'module' => 'auth',
                'tenant_id' => null,
                'user_name' => $request->email
            ]);
            $notification = array(
                'message' => 'Geçersiz firma veya kullanıcı!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }

        // Tenant status kontrolü - 0 ise pasif
        if ($tenant->status == 0) {
            // Askıya alınmış hesap giriş denemesi
            ActivityLogger::log('login_blocked', 'Askıya alınmış hesap giriş denemesi: ' . $request->email, [
                'module' => 'auth',
                'tenant_id' => $tenant->id,
                'user_name' => $request->email
            ]);

            $notification = array(
                'message' => 'Bu firma hesabı askıya alınmıştır. Lütfen sistem yöneticisi ile iletişime geçiniz.',
                'alert-type' => 'warning'
            );
            return redirect()->back()->with($notification);
        }

        // Kullanıcıyı doğrula
        $credentials = [
            'eposta' => $request->email,
            'password' => $request->password,
            'tenant_id' => $tenant->id, 
        ];
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Super Admin kontrolü
            if ($user->isSuperAdmin()) {
                ActivityLogger::logLogin($user);
                $notification = [
                    'message' => 'Super Admin olarak giriş yaptınız.',
                    'alert-type' => 'success'
                ];
                return redirect()->route('super.admin.dashboard')->with($notification);
            }
            
            // Kullanıcının tenant'ının tekrar status kontrolü (ekstra güvenlik)
            if ($user->tenant->status == 0) {
                Auth::logout(); // Kullanıcıyı çıkış yaptır
                 // Askıya alınmış hesap giriş denemesi
                ActivityLogger::log('login_blocked_after_auth', 'Askıya alınmış hesap - oturum kapatıldı: ' . $request->email, [
                    'module' => 'auth',
                    'tenant_id' => $tenant->id,
                    'user_name' => $user->name,
                    'user_role' => $user->getRoleNames()->first()
                ]);
                $notification = array(
                    'message' => 'Bu firma hesabı askıya alınmıştır. Lütfen sistem yöneticisi ile iletişime geçiniz.',
                    'alert-type' => 'warning'
                );
                return redirect()->back()->with($notification);
            }
            
            // Başarılı girişi logla
            ActivityLogger::logLogin($user);
            
            $tenantId = $user->tenant->id;
            $notification = array(
                'message' => 'Başarıyla giriş yapıldı.',
                'alert-type' => 'success'
            );
            return redirect()->route('secure.home', ['tenant_id' => $tenantId])->with($notification);
        }
        else{
            // Yanlış şifre denemesi
            ActivityLogger::log('login_failed', 'Yanlış şifre girişi: ' . $request->email, [
                'module' => 'auth',
                'tenant_id' => $tenant->id,
                'user_name' => $request->email
            ]);
            $notification = array(
                'message' => 'Geçersiz giriş bilgileri!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
    }
    // Şifre sıfırlama formu
    public function showForgotPasswordForm()
    {
        return view('frontend.auth.forgot_password');
    }
    // Şifre sıfırlama e-postası gönderme
public function sendResetLinkEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:tenants,eposta',
    ], [
        'email.required' => 'E-posta alanı zorunludur.',
        'email.email' => 'Geçerli bir e-posta adresi giriniz.',
        'email.exists' => 'Bu e-posta adresi sistemde kayıtlı değil.',
    ]);

    // Sadece Tenant (Patron) e-postası ile çalışır
    $tenant = Tenant::where('eposta', $request->email)->first();

    if (!$tenant) {
        return response()->json([
            'success' => false,
            'errors' => [
                'email' => ['Bu e-posta adresi sistemde kayıtlı değil.']
            ]
        ], 422);
    }

    // Eski tokenları temizle
    DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->delete();

    // Yeni token oluştur
    $token = Str::random(64);
    $expiresAt = Carbon::now()->addHours(1);

    DB::table('password_reset_tokens')->insert([
        'email' => $request->email,
        'token' => $token,
        'created_at' => Carbon::now(),
        'expires_at' => $expiresAt,
    ]);

    // Reset URL oluştur
    $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

    // E-posta gönder
    try {
        Mail::to($request->email)->send(new PasswordResetMail(
            $resetUrl,
            $tenant->name,
            $expiresAt->format('d.m.Y H:i')
        ));

        // Activity log
        ActivityLogger::log('password_reset_requested', 'Şifre sıfırlama talebi (Patron): ' . $request->email, [
            'module' => 'auth',
            'tenant_id' => $tenant->id,
            'user_name' => $tenant->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'E-posta gönderilirken bir hata oluştu. Lütfen tekrar deneyin.'
        ], 500);
    }
}
// Şifre sıfırlama formu göster
public function showResetPasswordForm(Request $request, $token)
{
    $email = $request->query('email');
    
    // Token kontrolü
    $resetToken = DB::table('password_reset_tokens')
        ->where('email', $email)
        ->where('token', $token)
        ->where('expires_at', '>', Carbon::now())
        ->first();

    if (!$resetToken) {
        $notification = [
            'message' => 'Şifre sıfırlama bağlantısı geçersiz veya süresi dolmuş.',
            'alert-type' => 'error'
        ];
        return redirect()->route('giris')->with($notification);
    }

    return view('frontend.auth.reset_password', compact('token', 'email'));
}
// Şifreyi sıfırla
// Şifreyi sıfırla
public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email|exists:tenants,eposta',
        'password' => 'required|min:6|confirmed',
    ], [
        'email.required' => 'E-posta alanı zorunludur.',
        'email.email' => 'Geçerli bir e-posta adresi giriniz.',
        'email.exists' => 'Bu e-posta adresi sistemde kayıtlı değil.',
        'password.required' => 'Şifre alanı zorunludur.',
        'password.min' => 'Şifre en az 6 karakter olmalıdır.',
        'password.confirmed' => 'Şifreler eşleşmiyor.',
    ]);

    // Token kontrolü
    $resetToken = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->where('expires_at', '>', Carbon::now())
        ->first();

    if (!$resetToken) {
        return response()->json([
            'success' => false,
            'message' => 'Şifre sıfırlama bağlantısı geçersiz veya süresi dolmuş.'
        ], 400);
    }

    // Tenant'ı bul
    $tenant = Tenant::where('eposta', $request->email)->first();
    
    if (!$tenant) {
        return response()->json([
            'success' => false,
            'message' => 'Firma bulunamadı.'
        ], 404);
    }

    // Patron kullanıcısını bul
    $user = User::where('tenant_id', $tenant->id)
        ->whereHas('roles', function($query) {
            $query->where('name', 'Patron');
        })
        ->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Bu firmaya ait yetkili kullanıcı bulunamadı.'
        ], 404);
    }

    // Patron şifresini güncelle
    $user->password = Hash::make($request->password);
    $user->save();

    // Tokeni sil
    DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->delete();

    // Activity log
    ActivityLogger::log('password_reset_completed', 'Şifre başarıyla sıfırlandı (Patron)', [
        'module' => 'auth',
        'tenant_id' => $tenant->id,
        'tenant_email' => $request->email,
        'user_id' => $user->user_id,
        'user_name' => $user->name,
        'user_email' => $user->eposta,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Şifreniz başarıyla güncellendi. Yeni şifrenizle giriş yapabilirsiniz.'
    ]);
}
    public function Dashboard($tenant_id) {
    $user = Auth::user();
    if ($user->tenant->id != $tenant_id) {
        $notification = array(
            'message' => 'Yetkisiz erişim yapıldı',
            'alert-type' => 'danger'
        );
        return redirect()->back()->with($notification);
    }

    //verileri view'e gönder
    $last_services = $this->getLastServices($tenant_id);
    $stock_alerts = $this->getStockAlerts($tenant_id); // tenant_id parametresini ekleyin

    return view('frontend.secure.index', compact('user', 'last_services', 'stock_alerts'));
}
// Dashboard istatistikleri
public function getStats($tenant_id)
{
    try {
        $today = Carbon::today();
        $thirtyDaysAgo = $today->copy()->subMonth(); // Son 1 Ay

        // AYLIK kasa hesaplama
        $monthly_income = DB::table('cash_transactions')
            ->where('odemeDurum', 1)
            ->where('odemeYonu', 1)
            ->where('firma_id', $tenant_id)
            ->whereBetween('created_at', [$thirtyDaysAgo, $today->copy()->endOfDay()])
            ->sum('fiyat');

        $monthly_expense = DB::table('cash_transactions')
            ->where('odemeDurum', 1)
            ->where('odemeYonu', 2)
            ->where('firma_id', $tenant_id)
            ->whereBetween('created_at', [$thirtyDaysAgo, $today->copy()->endOfDay()])
            ->sum('fiyat');

         // İptal durumlarını ve Yeni Servis durumunu tanımla
        $cancelled_statuses = [244]; // İptal  //[244, 246, 241, 243, 242]; // İptal, Tamir Edilemiyor, Fiyat Anlaşılamadı vb.
        $new_service_status = [235]; // Yeni Servis
        
        // İşlemde olanları bulmak için hariç tutulacak durumlar
        $excluded_statuses = array_merge($cancelled_statuses, $new_service_status);
            
        $stats = [
            // Aylık servis sayısı
            'total_services' => DB::table('services')
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id) 
                ->whereBetween('kayitTarihi', [$thirtyDaysAgo->format('Y-m-d'), $today->format('Y-m-d')])
                ->count(),
            
            // Aylık yeni müşteri sayısı
            'total_customers' => DB::table('customers')
             ->where('firma_id', $tenant_id)
             ->whereBetween('created_at', [$thirtyDaysAgo, $today->copy()->endOfDay()])
             ->count(),
            
            // Aktif Personel sayısı
            'total_personnel' => DB::table('tb_user')
                ->join('model_has_roles', 'tb_user.user_id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['Patron','Teknisyen', 'Teknisyen Yardımcısı', 'Operatör', 'Atölye Çırağı', 'Atölye Ustası', 'Depocu','Müdür'])
                ->where('tb_user.status', 1) // Sadece aktif olanlar
                ->where('tb_user.tenant_id', $tenant_id)
                ->count(),
            
            // AYLIK kasa toplamı
            'monthly_cash' => [
                'net' => $monthly_income - $monthly_expense
            ],
            
            // Günlük servis sayıları 
            'today_services' => DB::table('services')
                ->where('kayitTarihi', $today->format('Y-m-d'))
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id)
                ->count(),
                
            'today_cancelled_services' => DB::table('services')
                // 'updated_at' sütununun tarih kısmının bugüne eşit olup olmadığını kontrol et
                ->whereDate('updated_at', $today) 
                ->whereIn('servisDurum', $cancelled_statuses) // Sadece iptal durumundakileri al
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id)
                ->count(),
                            
            'today_in_process_services' => DB::table('services')
                ->where('kayitTarihi', $today->format('Y-m-d'))
                ->whereNotIn('servisDurum', $excluded_statuses) // Yeni veya İptal olmayanlar
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id)
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Veri alınırken hata oluştu: ' . $e->getMessage()
        ], 500);
    }
}
//Son servis talepleri
private function getLastServices($tenant_id)
{
    //Veritabanından son 4 servis
    $services_query = DB::table('services as s')
        ->join('customers as c', 's.musteri_id', '=', 'c.id')
        ->leftJoin('tb_user as u', 's.kid', '=', 'u.user_id')
        ->select(
            's.id as service_id',
            'c.adSoyad as customer_name',
            's.cihazAriza as service_description',
            'u.name as technician_name',
            's.updated_at as estimated_date',
            's.servisDurum as status_id' 
        )
        ->where('s.firma_id', $tenant_id)
        ->where('s.durum', 1)
        ->orderBy('s.created_at', 'desc')
        ->take(4)
        ->get();

    //Servis durum ID'lerini metne ve CSS sınıfına çeviren harita.
    $statusMap = [
        // Tamamlandı / Teslimat (Yeşil)
        
        252 => ['name' => 'Teslimata Hazır(Tamamlandı)', 'class' => 'status-completed'],
        253 => ['name' => 'Cihaz Teslim Edildi', 'class' => 'status-completed'],
        254 => ['name' => 'Şikayetçi', 'class' => 'status-completed'],
        255 => ['name' => 'Servisi Sonlandır', 'class' => 'status-completed'],
        256 => ['name' => 'Cihaz Satışı Yapıldı', 'class' => 'status-completed'],
        260 => ['name' => 'Cihaz Teslim Edildi (Parça Takıldı)', 'class' => 'status-completed'],
        272 => ['name' => 'Konsinye Cihaz Geri Alındı', 'class' => 'status-completed'],

        // Sorun / Problem / İptal (Kırmızı)
        241 => ['name' => 'Fiyat Anlaşılamadı', 'class' => 'status-high'],
        242 => ['name' => 'Ürün Garantili Çıktı', 'class' => 'status-high'],
        243 => ['name' => 'Müşteriye Ulaşılamadı', 'class' => 'status-high'],
        244 => ['name' => 'Müşteri İptal Etti', 'class' => 'status-high'],
        246 => ['name' => 'Cihaz Tamir Edilemiyor', 'class' => 'status-high'],
        
        // İşlemde / Atölyede (Turuncu)
        236 => ['name' => 'Teknisyen Yönledir', 'class' => 'status-medium'],
        237 => ['name' => 'Cihaz Atölyeye Alındı', 'class' => 'status-medium'],
        238 => ['name' => 'Parça Talep Et', 'class' => 'status-medium'],
        239 => ['name' => 'Yerinde Bakım Yapıldı', 'class' => 'status-medium'],
        245 => ['name' => 'Parçası Atölyeye Alındı', 'class' => 'status-medium'],
        250 => ['name' => 'Atölyede Tamir Ediliyor', 'class' => 'status-medium'],
        240 => ['name' => 'Atölyeye Aldır', 'class' => 'status-medium'],
        258 => ['name' => 'Tahsilata Gönder', 'class' => 'status-medium'],
        259 => ['name' => 'Parça Teslim Et', 'class' => 'status-medium'],
        261 => ['name' => 'Parça Hazır', 'class' => 'status-medium'],
        262 => ['name' => 'Nakliye Gönder', 'class' => 'status-medium'],
        271 => ['name' => 'Konsinye Cihaz Ata', 'class' => 'status-medium'],

        // Beklemede / Bilgi 
        235 => ['name' => 'Yeni Servisler', 'class' => 'status-pending'],
        247 => ['name' => 'Haber Verecek', 'class' => 'status-pending'],
        248 => ['name' => 'Yeniden Teknisyen Yölendir', 'class' => 'status-pending'],
        249 => ['name' => 'Müşteri Atölyeye Getirdi', 'class' => 'status-pending'],
        251 => ['name' => 'Teknisyen Yönlendir(Teslim Edilecek)', 'class' => 'status-pending'],
        257 => ['name' => 'Parça Takmak İçin Teknisyen Yönlendir', 'class' => 'status-pending'],
        263 => ['name' => 'Parça Siparişte', 'class' => 'status-pending'],
        264 => ['name' => 'Bayiye Gönnder', 'class' => 'status-pending'],
        266 => ['name' => 'Müşteri Para İade Edilecek', 'class' => 'status-pending'],
        267 => ['name' => 'Müşteri Para İade Edildi', 'class' => 'status-pending'],
        268 => ['name' => 'Fiyat Yükseltildi', 'class' => 'status-pending'],
        
        
        
    ];

    //Çektiğimiz verilere, yukarıdaki haritaya göre durum metnini ve CSS sınıfını ekleyelim.
    foreach ($services_query as $service) {
        // Eğer services tablosundan gelen status_id, $statusMap'te varsa onu kullan, yoksa default olanı kullan.
        $service->status_info = $statusMap[$service->status_id] ?? $statusMap['default'];
    }

    return $services_query;
}

//Kritik stoklar
//Kritik stoklar
private function getStockAlerts($tenant_id) 
{     
    $critical_level = 3; // Bu seviye ve altı KRİTİK     
    $low_level = 5;      // Bu seviye ve altı DÜŞÜK (ama kritik değil)     

    // Operatör rolüne sahipse boş array döndür
    if (Auth::user()->hasRole('Operatör')) {
        return ['critical' => [], 'low' => []];
    }

    try {
        // Önce tüm aktif ürünleri getir
        $allProducts = DB::table('stocks')
            ->where('firma_id', $tenant_id)
            ->where('durum', '1') // Aktif ürünler
            ->where('urunKategori', '!=', 3) // Konsinye kategorisini hariç tut
            ->select('id', 'urunAdi', 'urunKodu', 'urunKategori')
            ->get();

        \Log::info('All active products count: ' . count($allProducts));

        $alerts = [
            'critical' => [],
            'low' => []
        ];

        foreach ($allProducts as $product) {
            // Her ürün için güncel stok hesapla
            $currentStockData = DB::table('stock_actions')
                ->where('stokId', $product->id)
                ->where('firma_id', $tenant_id)
                ->selectRaw('
                    SUM(CASE 
                        WHEN islem = 1 THEN adet 
                        WHEN islem = 2 THEN -adet 
                        ELSE 0 
                    END) as current_stock,
                    COUNT(*) as total_actions,
                    MAX(created_at) as last_action_date
                ')
                ->first();

            $currentStock = $currentStockData ? (int)$currentStockData->current_stock : 0;

            \Log::info("Product ID {$product->id} ({$product->urunAdi}): Current stock = {$currentStock}");

            // Stok 0'dan büyük olmalı (negatif stokları gösterme)
            if ($currentStock > 0) {
                if ($currentStock <= $critical_level) {
                    $product->current_stock = $currentStock;
                    $product->threshold = $critical_level;
                    $product->alert_type = 'critical';
                    $product->total_actions = $currentStockData->total_actions ?? 0;
                    $product->last_action_date = $currentStockData->last_action_date ?? null;
                    
                    $alerts['critical'][] = $product;
                    \Log::info("Added to critical: {$product->urunAdi} (Stock: {$currentStock})");
                    
                } elseif ($currentStock <= $low_level) {
                    $product->current_stock = $currentStock;
                    $product->threshold = $low_level;
                    $product->alert_type = 'low';
                    $product->total_actions = $currentStockData->total_actions ?? 0;
                    $product->last_action_date = $currentStockData->last_action_date ?? null;
                    
                    $alerts['low'][] = $product;
                    \Log::info("Added to low: {$product->urunAdi} (Stock: {$currentStock})");
                }
            } else {
                \Log::info("Product {$product->urunAdi} has zero or negative stock: {$currentStock}");
            }
        }

        // Stok seviyesine göre sırala (en düşük önce)
        usort($alerts['critical'], function($a, $b) {
            return $a->current_stock <=> $b->current_stock;
        });
        
        usort($alerts['low'], function($a, $b) {
            return $a->current_stock <=> $b->current_stock;
        });

        // Her listeden en fazla 2'şer tane göster (dashboard'da daha fazla görünür olması için)
        $alerts['critical'] = array_slice($alerts['critical'], 0, 2);
        $alerts['low'] = array_slice($alerts['low'], 0, 2);

        \Log::info('Final Alerts Result:', [
            'critical_count' => count($alerts['critical']),
            'low_count' => count($alerts['low']),
            'total_products_checked' => count($allProducts)
        ]);

        return $alerts;

    } catch (\Exception $e) {
        \Log::error('Stock alerts error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return ['critical' => [], 'low' => []];
    }
}
    // Dashboard grafik verileri
    public function getChartData(Request $request,$tenant_id)
    {
         // Kullanıcı yetkisi kontrolü
        $user = Auth::user();
        if ($user->tenant->id != $tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkisiz erişim'
            ], 403);
        }
        try {
            $period = $request->get('period', 7);
            $type = $request->get('type', 'daily');

           if ($type === 'daily') {
                return $this->getDailyChartData($period, $tenant_id);
            } else {
                return $this->getHourlyChartData($period, $tenant_id);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Grafik verisi alınırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getDailyChartData($period,$tenant_id)
    {
        $startDate = Carbon::now()->subDays($period - 1);
        $endDate = Carbon::now();

        $services = DB::table('services')
            ->select(
                'kayitTarihi as date',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('kayitTarihi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('durum', '!=', 0) // Silinmeyenler
            ->where('firma_id', $tenant_id)
            ->groupBy('kayitTarihi')
            ->orderBy('kayitTarihi')
            ->get();

        // Tüm günleri içeren array oluştur
        $labels = [];
        $data = [];
        
        for ($i = 0; $i < $period; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $labels[] = $currentDate->format('d/m');
            
            // Bu günde servis var mı kontrol et
            $serviceCount = $services->where('date', $currentDate->format('Y-m-d'))->first();
            $data[] = $serviceCount ? $serviceCount->count : 0;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'data' => $data
            ]
        ]);
    }

    private function getHourlyChartData($period,$tenant_id)
    {
        $startDate = Carbon::now()->subDays($period - 1);
        $endDate = Carbon::now();

        // created_at sütunu var, saatlik dağılım için kullanabiliriz
        $services = DB::table('services')
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('durum', '!=', 0)
            ->where('firma_id', $tenant_id)
            ->whereNotNull('created_at')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get();

        // 8-18 saatleri arası
        $labels = [];
        $data = [];
        
        for ($hour = 8; $hour <= 18; $hour++) {
            $labels[] = sprintf('%02d:00', $hour);
            
            $serviceCount = $services->where('hour', $hour)->first();
            $data[] = $serviceCount ? $serviceCount->count : 0;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'data' => $data
            ]
        ]);
    }

    public function logout(Request $request)
    {
        
        $user = Auth::user();
        if ($user) {
            ActivityLogger::logLogout($user);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $notification = array(
            'message' => 'Başarıyla çıkış yapıldı!',
            'alert-type' => 'success'
        );
        return redirect()->route('giris')->with($notification);
    }

    public function getStatesByCountry($countryId)
    {   $cities = DB::table('ilces')->where('sehir_id', $countryId)->orderBy('ilceName','asc')->get();
        return response()->json($cities);
    }

    
}
