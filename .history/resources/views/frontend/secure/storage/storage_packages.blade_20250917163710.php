{{-- resources/views/frontend/secure/storage/packages.blade.php --}}
@extends('frontend.secure.user_master')
@section('user')

<style>
  .btn-gradient {
    background: linear-gradient(135deg, #fb923c 0%, #f9b233 100%);
    color: #fff;
    border: none;
    transition: all 0.3s ease;
  }

  .btn-gradient:hover {
    opacity: 0.9;
    transform: translateY(-2px);
  }

  .pricing-card {
    border-radius: 1.2rem;
    transition: all 0.3s ease;
  }

  .pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  }
  
  .price-description ul {
    list-style: none;
    padding: 0;
    text-align: left;
    margin: 0 auto;
    display: inline-block;
  }

  .price-description ul li {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
  }

  .price-description ul li::before {
    content: '';
    display: inline-block;
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.5rem;
    background-color: #027a48;
    -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'%3E%3Cpath fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd' /%3E%3C/svg%3E") no-repeat center;
    mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'%3E%3Cpath fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd' /%3E%3C/svg%3E") no-repeat center;
    background-size: contain;
  }
  
  .text-gray-900 {
    --tw-text-opacity: 1;
    color: #212529;
  }
  
  .bg-bls-teal-50 {
    background-color: #cff3fa;
  }
  
  .text-bls-success-700 {
    color: #027a48;
  }
</style>

<script src="https://cdn.tailwindcss.com"></script>  

<div class="page-content">
  <div class="container-fluid">
    <!-- Başlık -->
    <div class="row ">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0">
            <i class="fas fa-hdd text-primary me-2"></i>
            Ek Storage Paketleri
          </h4>
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="{{ route('secure.home', $firma->id) }}">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Storage Paketleri</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Mevcut Storage Durumu -->
    <div class="row ">
      <div class="col-12">
        <div class="card ">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-8">
                <h6 class="mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Mevcut Storage Durumunuz</h6>
                <div class="d-flex justify-content-between mb-2">
                  <span>Kullanım: {{ $storageInfo['current_usage_formatted'] }} / {{ $storageInfo['limit_formatted'] }}</span>
                  <span class="text-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}">
                    %{{ $storageInfo['usage_percentage'] }}
                  </span>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}" 
                       style="width: {{ $storageInfo['usage_percentage'] }}%"></div>
                </div>
                @if($storageInfo['has_extra_storage'])
                  <small class="text-success mt-1 d-block">
                    <i class="fas fa-plus-circle me-1"></i>
                    Ek Storage: {{ $storageInfo['extra_storage_gb'] }} GB aktif
                  </small>
                @endif
              </div>
              <div class="col-md-4 text-end">
                <div class="text-muted">Kalan Alan</div>
                <h4 class="mb-0 text-{{ $storageInfo['danger_threshold'] ? 'danger' : 'primary' }}">{{ $storageInfo['remaining_formatted'] }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <section class="py-8 bg-gray-50">
          <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:items-start">
              @foreach($packages as $i => $package)
                @php
                  // 2. kartı "önerilen" yap
                  $isPopular = ($i === 1);
                @endphp

                <div class="relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2 p-8 flex flex-col text-center pricing-card">                                     
                  @if($isPopular)
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-orange-500 to-[#f9b233] text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
                      Önerilen
                    </span>
                  @endif

                  <div class="flex justify-center mb-6">
                    <i class="fas fa-database text-5xl text-[#f9b233]"></i>
                  </div>
                  
                  <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $package->name }}</h3>
                  <p class="text-gray-500 font-normal mb-2" style="font-size: 14px;">{{ $package->description }}</p>

                  <div class="text-4xl font-extrabold text-gray-900 mb-4">
                    ₺ {{ number_format($package->price, 2) }}
                    <span class="text-base text-gray-500 font-normal">
                      / tek seferlik
                    </span>
                  </div>

                  <div class="flex justify-between items-center my-4 text-sm text-gray-600">
                    <!-- Sol Taraf: Storage İkonu -->
                    <div class="flex items-center space-x-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                      </svg>
                      <span class="text-gray-400">Kalıcı</span>
                    </div>
                    
                    <!-- Sağ Taraf: Storage Miktarı -->
                    <div class="flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 text-gray-400" style="width: 1rem;height: 0.9rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                      </svg>
                      <span class="font-medium">
                        +{{ $package->storage_gb }} GB
                      </span>
                    </div>
                  </div>

                  <form action="{{ route('storage.purchase', $firma->id) }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    <button type="submit" class="inline-block w-full py-2 rounded-full bg-gradient-to-r from-orange-500 to-[#f9b233] text-white font-semibold transition hover:opacity-90">
                      Satın Al
                    </button>
                  </form>

                  <hr class="mt-4" style="--tw-border-opacity: 1;border-color: rgb(132 145 173);">

                  <!-- Özellikler - Her Zaman Görünür -->
                  <div class="price-description text-gray-600 mb-2 mt-4 text-center">
                    <h6 class="text-gray-700 font-semibold mb-3">Bu Pakette</h6>
                    <ul>
                      <li>+{{ $package->storage_gb }} GB kalıcı depolama alanı</li>
                      <li>Tüm dosya türleri desteklenir</li>
                      <li>Anında aktifleşir</li>
                      <li>Süre sınırı yoktur</li>
                      <li>Mevcut limitinize eklenir</li>
                      <li>7/24 teknik destek</li>
                    </ul>
                    
                    {{-- @if($package->storage_gb >= 15)
                      <div class="mt-3 p-2 bg-green-50 rounded-lg">
                        <small class="text-green-600 font-medium">
                          <i class="fas fa-gift me-1"></i>
                          Bonus: Öncelikli müşteri desteği
                        </small>
                      </div>
                    @endif --}}
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </section>
      </div>
    </div>

    <!-- Alt Bilgilendirme -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="mb-3"><i class="fas fa-question-circle text-primary me-2"></i>Sıkça Sorulan Sorular</h5>
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <h6 class="text-primary">Ek storage kalıcı mı?</h6>
                  <p class="small text-muted mb-0">Evet, satın aldığınız ek storage kalıcıdır ve süre sınırı yoktur.</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <h6 class="text-primary">Ne zaman aktif olur?</h6>
                  <p class="small text-muted mb-0">Ödeme onaylandıktan hemen sonra hesabınıza otomatik olarak eklenir.</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <h6 class="text-primary">Güvenli mi?</h6>
                  <p class="small text-muted mb-0">PayTR güvenli ödeme sistemi kullanılır. Kredi kartı bilgileriniz saklanmaz.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
@if(request('payment_check'))
  $(document).ready(function() {
    checkStoragePaymentStatus();
  });

  function checkStoragePaymentStatus() {
    let attempts = 0;
    const maxAttempts = 12;
    
    const checkInterval = setInterval(function() {
      $.get('/{{ $firma->id }}/storage-odeme-durum')
        .done(function(response) {
          if (response.payment_completed) {
            clearInterval(checkInterval);
            alert('Ödeme başarılı! Ek storage alanınız hesabınıza eklendi.');
            location.reload();
          } else if (response.payment_failed) {
            clearInterval(checkInterval);
            alert('Ödeme işlemi başarısız.');
          }
          
          attempts++;
          if (attempts >= maxAttempts) {
            clearInterval(checkInterval);
            alert('Ödeme durumu kontrol edilemiyor. Sayfa yenileniyor...');
            location.reload();
          }
        });
    }, 5000);
  }
@endif
</script>

@endsection