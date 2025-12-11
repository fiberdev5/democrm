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

class HomeController extends Controller
{
        public function index()
    {
        // İstatistikler
        $stats = [
            [
                'number' => '500+',
                'label' => 'Aktif Firma'
            ],
            [
                'number' => '50K+',
                'label' => 'Tamamlanan Servis'
            ],
            [
                'number' => '99.9%',
                'label' => 'Uptime Garantisi'
            ],
            [
                'number' => '7/24',
                'label' => 'Destek Hizmeti'
            ]
        ];

        // Modüller
        $modules = [
            [
                'icon' => 'fas fa-users',
                'title' => 'Müşteri Yönetimi',
                'description' => 'Müşterilerinizi detaylı kayıt altına alın, geçmiş işlemlerini görüntüleyin ve müşteri memnuniyetini artırın.',
                'color' => 'blue'
            ],
            [
                'icon' => 'fas fa-clipboard-list',
                'title' => 'Servis Takibi',
                'description' => 'Servis süreçlerinizi baştan sona takip edin. Arıza kayıtlarından teslimata kadar her aşamayı yönetin.',
                'color' => 'blue'
            ],
            [
                'icon' => 'fas fa-boxes',
                'title' => 'Stok Yönetimi',
                'description' => 'Yedek parça stoklarınızı takip edin, kritik stok seviyelerinde otomatik uyarı alın.',
                'color' => 'blue'
            ],
            [
                'icon' => 'fas fa-user-tie',
                'title' => 'Personel Yönetimi',
                'description' => 'Teknisyenlerinizi yönetin, performanslarını ölçün ve prim hesaplamalarını otomatikleştirin.',
                'color' => 'orange'
            ],
            [
                'icon' => 'fas fa-file-invoice-dollar',
                'title' => 'Fatura & Kasa',
                'description' => 'E-fatura oluşturun, gelir-gider takibi yapın, finansal raporlarınızı anında görüntüleyin.',
                'color' => 'orange'
            ],
            [
                'icon' => 'fas fa-mobile-alt',
                'title' => 'Mobil Erişim',
                'description' => 'Responsive tasarım sayesinde mobil cihazlardan her yerde işlerinizi yönetin.',
                'color' => 'orange'
            ]
        ];

        // Sektörler
        $sectors = [
            [
                'icon' => 'fas fa-tv',
                'title' => 'Beyaz Eşya',
                'description' => 'Buzdolabı, çamaşır makinesi servisleri'
            ],
            [
                'icon' => 'fas fa-laptop',
                'title' => 'Bilgisayar',
                'description' => 'Bilgisayar, laptop teknik servisleri'
            ],
            [
                'icon' => 'fas fa-mobile-alt',
                'title' => 'Telefon',
                'description' => 'Cep telefonu, tablet onarım servisleri'
            ],
            [
                'icon' => 'fas fa-fan',
                'title' => 'Klima & HVAC',
                'description' => 'Klima, havalandırma servisleri'
            ],
            [
                'icon' => 'fas fa-fire',
                'title' => 'Kombi',
                'description' => 'Kombi, kalorifer bakım ve onarım'
            ],
            [
                'icon' => 'fas fa-heartbeat',
                'title' => 'Medikal',
                'description' => 'Tıbbi cihaz bakım servisleri'
            ],
            [
                'icon' => 'fas fa-camera',
                'title' => 'Elektronik',
                'description' => 'TV, ses sistemleri servisleri'
            ],
            [
                'icon' => 'fas fa-cogs',
                'title' => 'Diğer',
                'description' => 'Tüm teknik servis işletmeleri'
            ]
        ];

        // Entegrasyonlar
        $integrations = [
            [
                'icon' => 'fas fa-file-invoice',
                'title' => 'Paraşüt',
                'description' => 'Muhasebe yazılımı ile entegrasyon',
                'color' => 'blue'
            ],
            [
                'icon' => 'fas fa-phone-volume',
                'title' => 'Hipcall',
                'description' => 'Santral entegrasyonu ile gelen aramalar',
                'color' => 'orange'
            ],
            [
                'icon' => 'fas fa-sms',
                'title' => 'SMS Entegrasyonu',
                'description' => 'Netgsm, Verimor ile SMS gönderimi',
                'color' => 'blue'
            ],
            [
                'icon' => 'fas fa-envelope',
                'title' => 'Email Sistemi',
                'description' => 'SMTP entegrasyonu ile otomatik email',
                'color' => 'orange'
            ],
            [
                'icon' => 'fas fa-credit-card',
                'title' => 'Ödeme Sistemleri',
                'description' => 'Online ödeme alma entegrasyonları',
                'color' => 'blue'
            ],
            [
                'icon' => 'fas fa-plug',
                'title' => 'REST API',
                'description' => 'Kendi sistemlerinizle entegrasyon',
                'color' => 'orange'
            ]
        ];

        // Müşteri Yorumları
        $testimonials = [
            [
                'quote' => 'Serbis sayesinde tüm servis süreçlerimizi dijitalleştirdik. Artık her şey çok daha hızlı ve organize. Müşteri memnuniyetimiz %40 arttı. Kesinlikle tavsiye ediyorum.',
                'name' => 'Ahmet Yılmaz',
                'position' => 'Beyaz Eşya Servisi',
                'initials' => 'AY',
                'color' => 'blue'
            ],
            [
                'quote' => 'Müşteri takibi ve stok yönetimi artık çok kolay. Özellikle mobil erişim sahada işimizi inanılmaz kolaylaştırdı. Kağıt formlardan kurtulduk.',
                'name' => 'Mehmet Kara',
                'position' => 'Elektronik Servisi',
                'initials' => 'MK',
                'color' => 'orange'
            ],
            [
                'quote' => 'Destek ekibi harika! Her sorumuzda hızlıca yardımcı oldular. Sistemi kullanmak gerçekten çok basit ve kullanışlı. 3 yıldır memnuniyetle kullanıyoruz.',
                'name' => 'Fatma Öztürk',
                'position' => 'Klima Servisi',
                'initials' => 'FÖ',
                'color' => 'blue'
            ]
        ];

        // SSS
        $faqs = [
            [
                'question' => 'Serbis\'i kullanmak için teknik bilgiye ihtiyacım var mı?',
                'answer' => 'Hayır, Serbis kullanıcı dostu arayüzü ile herkes tarafından kolayca kullanılabilir. Kurulum sonrası eğitim videolarımız ve destek ekibimiz size yardımcı olacaktır.'
            ],
            [
                'question' => 'Verilerim güvende mi?',
                'answer' => 'Evet, tüm verileriniz SSL şifreleme ile korunur ve düzenli olarak yedeklenir. Türkiye\'de bulunan sunucularımızda KVKK uyumlu olarak verilerinizi saklarız.'
            ],
            [
                'question' => 'Ücretsiz deneme süresi var mı?',
                'answer' => 'Evet, 14 gün boyunca ücretsiz deneyebilirsiniz. Kredi kartı bilgisi gerekmez. Deneme süreniz sonunda istediğiniz paketi seçebilirsiniz.'
            ],
            [
                'question' => 'Mobil cihazlardan kullanabilir miyim?',
                'answer' => 'Evet, Serbis responsive tasarıma sahiptir. Telefon, tablet ve bilgisayardan sorunsuz kullanabilirsiniz. Ayrıca mobil uygulamamız da yakında yayınlanacak.'
            ],
            [
                'question' => 'Mevcut verilerimi aktarabilir miyim?',
                'answer' => 'Evet, mevcut müşteri, stok ve servis verilerinizi Excel dosyası ile sisteme aktarabilirsiniz. Destek ekibimiz bu konuda size yardımcı olacaktır.'
            ],
            [
                'question' => 'Destek hizmeti nasıl çalışıyor?',
                'answer' => 'Telefon, email ve canlı destek kanallarımız üzerinden bize ulaşabilirsiniz. Profesyonel pakette öncelikli destek, Kurumsal pakette 7/24 destek sunuyoruz.'
            ]
        ];

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
            'slug' => 'telekomunikasyon', 
            'icon' => 'fas fa-mobile-alt',
            'title' => 'Telekomünikasyon',
            'short_description' => 'Cep telefonu ve tablet tamir servisi takibi',
            'image' => 'frontend/img/sectors/telekominasyon.jpg',
            'features' => [
                'IMEI kayıt ve takibi',
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
            'short_description' => 'Proje, keşif ve montaj takip çözümü',
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
    'telekominasvon' => [
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

    return view('frontend.frontend_pages.sector_detail', compact('sector'));
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



    public function Pricing() {
        $prices = SubscriptionPlan::active()->ordered()->get();
        return view('frontend.pages.pricing', compact('prices'));
    }

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
