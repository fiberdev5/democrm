@extends('frontend.main_master')

@section('title', 'Fiyatlar - Serbis')

@section('main')

<!-- Hero Section -->
<section class="pricing-hero">
    <div class="container">
        <div class="pricing-hero-content">
            <div class="pricing-badge">
                <i class="fas fa-tag"></i>
                14 Gün Ücretsiz Deneme
            </div>
            <h1 class="pricing-hero-title">Size Uygun <span>Planı</span> Seçin</h1>
            <p class="pricing-hero-description">
                Her ölçekteki teknik servis için uygun fiyatlı çözümler. Kredi kartı gerektirmeden hemen başlayın, işinizi büyütün.
            </p>
            <div class="pricing-hero-features">
                <div class="hero-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>Kurulum ücretsiz</span>
                </div>
                <div class="hero-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>İstediğiniz zaman iptal</span>
                </div>
                <div class="hero-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>7/24 Destek</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="pricing-section">
    <div class="container">
        <div class="row g-4 justify-content-center">
            
            @foreach($pricing as $index => $plan)
            @php
                $isPopular = ($index === 1);
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card">
                    @if($isPopular)
                    <span class="popular-badge">Önerilen</span>
                    @endif
                    
                    <!-- Plan Icon -->
                    <div class="plan-icon">
                        <i class="{{ $plan['icon'] }}"></i>
                    </div>
                    
                    <!-- Plan Name -->
                    <h3 class="plan-name">{{ $plan['name'] }}</h3>
                    
                    <!-- Short Description -->
                    <p class="plan-short-desc">Teknik servis süreçlerinizi dijitalleştirin, müşteri memnuniyetini artırın.</p>
                    
                    <!-- Price -->
                    <div class="plan-price">
                        <span class="currency">₺</span>
                        <span class="amount">{{ number_format($plan['price'], 0, ',', '.') }}</span>
                        <span class="period">/yıllık</span>
                        <span class="discount-badge">%30 Kazanın</span>
                    </div>
                    
                    <!-- Device & User Meta -->
                    <div class="plan-meta">
                        <div class="plan-devices">
                            <!-- Mobile -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <!-- Tablet -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 1v22m-4-18h8a2 2 0 012 2v14a2 2 0 01-2 2h-8a2 2 0 01-2-2V5a2 2 0 012-2z" />
                            </svg>
                            <!-- Desktop -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="plan-users">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ $plan['users'] }} Kullanıcı</span>
                        </div>
                    </div>
                    
                    <!-- Buy Button -->
                    <a href="{{ url('/kullanici-girisi') }}" class="btn-buy" target="_blank">Satın Al</a>
                    
                    <!-- Divider -->
                    <hr class="features-divider">
                    
                    <!-- Features Header -->
                    <p class="features-header-text">{{ $plan['description'] }}</p>
                    
                    <!-- Features List - Always Visible, No Scroll -->
                    <ul class="features-list">
                        @foreach($plan['features'] as $feature)
                        <li>
                            <span class="check-icon"></span>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
            
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="pricing-faq">
    <div class="container">
        <h2 class="faq-title">Sıkça Sorulan Sorular</h2>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="faq-item active">
                    <div class="faq-question">
                        <span>Ücretsiz deneme süresi var mı?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            Evet, tüm paketlerimizde 14 gün ücretsiz deneme süresi sunuyoruz. Kredi kartı bilgisi gerekmeden hemen başlayabilirsiniz.
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Paket değişikliği yapabilir miyim?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            Evet, istediğiniz zaman paketinizi yükseltebilir veya düşürebilirsiniz. Değişiklikler bir sonraki fatura döneminde geçerli olur.
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Ödeme yöntemleri nelerdir?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            Kredi kartı, banka kartı ve havale/EFT ile ödeme yapabilirsiniz. Kurumsal fatura kesimi de mevcuttur.
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Verilerim güvende mi?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            Tüm verileriniz SSL şifreleme ile korunur ve Türkiye'deki güvenli sunucularda saklanır. Günlük yedekleme yapılmaktadır.
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>İptal edebilir miyim?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            Evet, istediğiniz zaman aboneliğinizi iptal edebilirsiniz. İptal sonrası mevcut dönemin sonuna kadar kullanmaya devam edebilirsiniz.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="pricing-cta">
    <div class="container">
        <h2>Hala kararsız mısınız?</h2>
        <p>Ücretsiz demo talep edin, size özel sunum yapalım.</p>
        <a href="{{ url('/#iletisim') }}" class="btn-cta">
            <i class="fas fa-phone-alt"></i> Bize Ulaşın
        </a>
    </div>
</section>

@endsection


<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', function() {
            const isActive = item.classList.contains('active');
            
            // Close all items
            faqItems.forEach(i => i.classList.remove('active'));
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
});
</script>
