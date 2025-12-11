<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontendHomeController extends Controller
{
    /**
     * Ana sayfa görünümünü gösterir
     */
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

        return view('frontend.home_frontend.home.home', compact('stats', 'modules', 'sectors', 'integrations', 'testimonials', 'faqs'));
    }
}
