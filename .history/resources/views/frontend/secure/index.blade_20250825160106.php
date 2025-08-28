@extends('frontend.secure.user_master')

@section('user')

{{-- CSRF Token Meta Tag --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

 <style>
        body {
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: visible !important;
            
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
            border-radius: 16px;
            padding: 25px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            margin-bottom: 20px;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .stat-card.blue {
      --card-bg-1: #495057; 
      --card-bg-2: #6c757d; 
        }

        .stat-card.green {
            --card-bg-1: #495057; 
            --card-bg-2: #6c757d; 
        }

        .stat-card.red {
            --card-bg-1: #495057; 
            --card-bg-2: #6c757d; 
        }

        .stat-card.teal {
            --card-bg-1: #495057;
            --card-bg-2: #6c757d; 
            
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            float: right;
            margin-top: -10px;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: bold;
            margin-bottom: 5px;
            position: relative;
            z-index: 2;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .service-summary {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .service-summary h5 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .service-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .service-item:hover {
            transform: translateY(-2px);
            border-color: #fff;
            background: #fff;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
            text-decoration: none;
            color: inherit;
        }

        .service-count {
            font-size: 2.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .service-item.today .service-count {
            color: #007bff; /* parlak mavi - bootstrap primary */
        }

        .service-item.yesterday .service-count {
            color: #dc3545; /* canlı kırmızı - bootstrap danger */
        }

        .service-item.previous .service-count {
            color: #6c757d; /* orta koyulukta gri */
        }

        .chart-container {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            position: relative;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f1f1;
        }

        .chart-header h5 {
            color: #333;
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

       /* Nav menü kapsayıcısı */
        .time-filter {
            display: flex;
            gap: 10px;
            background: white;
            border-radius: 30px;
            padding: 5px 10px;
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.15);
        }

        /* Nav item olarak butonlar */
        .filter-btn {
            all: unset; /* buton varsayılanlarını sıfırlar */
            padding: 8px 20px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
            user-select: none;
        }

        /* Hover ve odak durumları */
        .filter-btn:hover,
        .filter-btn:focus {
            background-color: #6c757d; 
            color: #495057;
            outline: none;
        }

        /* Aktif buton */
        .filter-btn.active {
            background-color: #495057; 
            color: white;
            box-shadow: 0 4px 8px #495057;
        }

        /* Responsive küçülünce biraz küçült */
        @media (max-width: 768px) {
            .filter-btn {
                padding: 6px 14px;
                font-size: 0.85rem;
            }
        }

        .chart-canvas {
            position: relative;
            height: 300px;
        }

        .loading {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #667eea;
        }

        .page-title {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
            font-weight: 300;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
            display: none;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                margin: 10px;
                padding: 20px;
            }
            
            .stat-card {
                margin-bottom: 15px;
            }
            
            .time-filter {
                flex-wrap: wrap;
            }
            
            .filter-btn {
                font-size: 0.8rem;
                padding: 6px 12px;
            }
        }
        .stat-card {
            padding: 15px 20px !important;
        }

        .stat-icon {
            font-size: 1.8rem !important;
            margin-top: -5px !important;
        }

        .stat-value {
            font-size: 1.8rem !important;
        }

        .stat-label {
            font-size: 0.85rem !important;
        }
        .service-summary {
            padding: 15px 20px;  /* Daha az iç boşluk */
            margin: 10px 0;      /* Daha az üst-alt boşluk */
        }

        .service-summary h5 {
            font-size: 1.1rem;   /* Başlık biraz küçüldü */
            margin-bottom: 15px;
        }

        .service-item {
            padding: 15px 10px;  /* Daha küçük kutu iç boşluğu */
            border-radius: 10px;
            font-size: 0.9rem;   /* İçerik genel font küçüldü */
        }

        .service-count {
            font-size: 1.8rem;   /* Sayı boyutu küçüldü */
            font-weight: 700;
            margin-bottom: 6px;
        }

        .service-item div:not(.service-count) {
            font-size: 0.85rem;  /* Alt açıklama metni daha küçük */
        }
           .dashboard-card {
        background: #2a2f3b; /* Koyu tema ana rengi */
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        color: #f8f9fa; /* Açık renk metin */
    }

    .dashboard-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #495057; /* Ayırıcı çizgi */
    }

    .dashboard-card-header h5 {
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;
    }

    .view-all-btn {
        color: #adb5bd;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .view-all-btn:hover {
        color: #fff;
    }

    /* Kritik Stok Uyarıları */
    .stock-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #3e4451;
    }
    .stock-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .stock-icon-wrapper {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.2rem;
    }
    .stock-icon-wrapper.critical { background-color: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .stock-icon-wrapper.low { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; }

    .stock-details {
        flex-grow: 1;
    }
    .stock-details h6 { margin: 0; font-size: 1rem; color: #fff; }
    .stock-details p { margin: 0; font-size: 0.85rem; color: #adb5bd; }

    .stock-level {
        text-align: right;
    }
    .stock-level .level-text { font-size: 1.1rem; font-weight: bold; }
    .stock-level .level-label { font-size: 0.8rem; color: #adb5bd; }
    .stock-level.critical .level-text { color: #dc3545; }
    .stock-level.low .level-text { color: #ffc107; }
    
    .stock-alert-footer {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #495057;
        color: #dc3545;
        font-weight: 500;
        display:flex;
        align-items:center;
        gap: 8px;
    }

    /* Son Servis Talepleri */
    .service-request-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 0;
        border-bottom: 1px solid #3e4451;
    }
    .service-request-item:last-child { border-bottom: none; }

    .service-customer-info { flex-grow: 1; }
    .service-customer-info h6 { margin: 0; font-size: 1rem; color: #fff; }
    .service-customer-info p { margin: 0; font-size: 0.85rem; color: #adb5bd; }

    .service-status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #fff;
    }

    /* Örnek durum renkleri - Kendi durumlarınıza göre ayarlayın */
    .status-high { background-color: #dc3545; } /* Yüksek Öncelik */
    .status-medium { background-color: #ffc107; color: #212529;} /* Orta Öncelik */
    .status-completed { background-color: #28a745; } /* Tamamlandı */
    .status-pending { background-color: #6c757d; } /* Beklemede */

    /* Beyaz Arka Planlı Kart Stili */
.dashboard-card-light {
    background: #ffffff; /* Arka planı beyaz yap */
    box-shadow: 0 10px 25px rgba(0,0,0,0.08); /* Hafif bir gölge ver */
}

/* Beyaz kart içindeki başlık metnini koyu yap */
.dashboard-card-light .dashboard-card-header h5 {
    color: #343a40;
}

/* Beyaz kart içindeki "Tümünü Gör" linkini koyu yap */
.dashboard-card-light .view-all-btn {
    color: #6c757d;
}
.dashboard-card-light .view-all-btn:hover {
    color: #343a40;
}

/* Beyaz kart içindeki ayırıcı çizgileri açık gri yap */
.dashboard-card-light .dashboard-card-header,
.dashboard-card-light .stock-item,
.dashboard-card-light .service-request-item {
    border-bottom-color: #e9ecef;
}

/* Beyaz kart içindeki metinleri koyu yap (önemli) */
.dashboard-card-light .stock-details h6,
.dashboard-card-light .service-customer-info h6 {
    color: #212529; /* Ana başlık rengi */
}
.dashboard-card-light .stock-details p,
.dashboard-card-light .service-customer-info p {
    color: #6c757d; /* İkincil metin rengi */
}

/* Alt uyarı footer'ının rengini de beyaz temaya uyarla */
.dashboard-card-light .stock-alert-footer {
    border-top-color: #e9ecef;
}
/* Kart başlığındaki ikon ve linki mavi yapalım */
.dashboard-card-light .dashboard-card-header h5 {
    color: #0d6efd; /* Bootstrap Mavi */
}
.dashboard-card-light .dashboard-card-header .view-all-btn {
    color: #0d6efd;
    font-weight: 600;
}
.dashboard-card-light .dashboard-card-header .view-all-btn:hover {
    color: #0b5ed7;
}

/* Her bir servis talebi satırı */
.service-request-item {
    display: flex;
    align-items: center; /* Dikeyde ortala */
    gap: 1.25rem; /* İkon ve metin arası boşluk */
    padding: 1rem 0.5rem;
    border-bottom: 1px solid #e9ecef;
}
.service-request-item:last-child {
    border-bottom: none;
}

/* Soldaki kullanıcı ikonu */
.service-user-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 50%; /* Tam yuvarlak */
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e7f1ff; /* Açık mavi arka plan */
    color: #0d6efd; /* Mavi ikon rengi */
    font-size: 1rem;
}

/* Müşteri ve servis detaylarını içeren orta bölüm */
.service-customer-info { flex-grow: 1; }

.service-customer-info h6 {
    font-weight: 600;
    margin-bottom: 0.25rem;
}
.service-customer-info h6 small {
    font-weight: 400;
    color: #6c757d;
}
.service-customer-info .service-description {
    font-size: 0.9rem;
    color: #495057;
    margin-bottom: 0.5rem;
}

/* Teknisyen ve tarih bilgisini içeren meta satırı */
.service-meta-info {
    font-size: 0.85rem;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 1rem; /* İkonlar ve metinler arası boşluk */
    margin: 0;
}
.service-meta-info span {
    display: flex;
    align-items: center;
    gap: 0.4rem; /* İkon ve metin arası boşluk */
}
.service-meta-info .fa-user-cog, .service-meta-info .fa-calendar-alt {
    font-size: 0.8rem;
}


/* Durum etiketleri (Badge) */
.service-status-badge {
    padding: 0.35em 0.8em;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #fff;
    white-space: nowrap; /* Etiketin bölünmesini engelle */
}

/* GÖRSELE UYGUN YENİ DURUM RENKLERİ */
.status-high { background-color: #dc3545; } /* Yüksek Öncelik - Kırmızı */
.status-medium { background-color: #fd7e14; color: #fff !important;} /* Orta Öncelik - Turuncu */
.status-completed { background-color: #198754; } /* Tamamlandı - Yeşil */
.status-pending { background-color: #6c757d; } /* Beklemede - Gri */
.status-cancelled { background-color: #495057; } /* İptal (Örnek) - Koyu Gri */
    </style>

        <div class="page-title" style="height:40px;"></div>
        <div class="dashboard-container">
            <!-- Hata mesajı -->
            <div id="errorMessage" class="error-message"></div>
            
            <!-- Üst İstatistik Kartları -->
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="#" class="stat-card blue" id="totalServicesCard">
                        <i class="fas fa-tools stat-icon"></i>
                        <div class="stat-value" id="totalServices">-</div>
                        <div class="stat-label">Aylık Servis Sayısı</div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="{{ route('customers', ['tenant_id' => request()->route('tenant_id')]) }}" class="stat-card green" id="totalCustomersCard">
                        <i class="fas fa-users stat-icon"></i>
                        <div class="stat-value" id="totalCustomers">-</div>
                        <div class="stat-label">Aylık Müşteri Sayısı</div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="{{ route('staffs', ['tenant_id' => request()->route('tenant_id')]) }}" class="stat-card red" id="totalPersonnelCard">
                        <i class="fas fa-user-tie stat-icon"></i>
                        <div class="stat-value" id="totalPersonnel">-</div>
                        <div class="stat-label">Aktif Personel Sayısı</div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                       <a href="#" onclick="window.location.href = buildCashUrl('total')"
                       class="stat-card teal" id="totalCashCard">
                        <i class="fas fa-lira-sign stat-icon"></i>
                        <div class="stat-value" id="totalCash">0,00 TL</div>
                        <div class="stat-label">Aylık Kasa</div>
                    </a>
                </div>
            </div>
             
            <!-- Servis Özeti -->
            <div class="service-summary">
                <h5><i class="fas fa-chart-bar"></i> Servis Sayıları</h5>
                <div class="row">
                    <div class="col-md-4">
                        <a href="#" class="service-item today" id="todayServicesCard">
                            <div class="service-count" id="todayServices">-</div>
                            <div>BUGÜN Alınan Servis Sayısı</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="service-item yesterday" id="yesterdayServicesCard">
                            <div class="service-count" id="yesterdayServices">-</div>
                            <div>DÜN Alınan Servis Sayısı</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="service-item previous" id="previousServicesCard">
                            <div class="service-count" id="previousServices">-</div>
                            <div>ÖNCEKİ GÜN Alınan Servis Sayısı</div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
            <!-- Son Servis Talepleri -->
             <div class="col-lg-7">
        <div class="dashboard-card dashboard-card-light">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-clipboard-list"></i> Son Servis Talepleri</h5>
                <a href="{{-- Buraya servisler listenizin rotasını ekleyin --}}" class="view-all-btn">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="dashboard-card-body">
                @forelse ($last_services as $service)
                <div class="service-request-item">
                    <div class="service-customer-info">
                        {{-- Müşteri adı ve servis ID'si --}}
                        <h6>{{ $service->customer_name }} <small class="text-muted">#{{ $service->service_id }}</small></h6>
                        {{-- Servis açıklaması --}}
                        <p>{{ $service->service_description }}</p>
                        <p style="font-size: 0.8rem; color: #8892b0;">
                            {{-- Teknisyen ve Tahmini Tarih --}}
                            <i class="fas fa-user-cog"></i> {{ $service->technician_name ?? 'Atanmadı' }} | 
                            <i class="fas fa-calendar-alt"></i> Bitiş: {{ $service->estimated_date ? \Carbon\Carbon::parse($service->estimated_date)->format('d.m.Y') : 'Belirsiz' }}
                        </p>
                    </div>
                    {{-- Servis durumu (HomeController'da oluşturulan haritadan geliyor) --}}
                    <span class="service-status-badge {{ $service->status_info['class'] }}">{{ $service->status_info['name'] }}</span>
                </div>
                @empty
                <p class="text-center text-muted">Gösterilecek servis talebi bulunamadı.</p>
                @endforelse
            </div>
        </div>
    </div>

<!-- Kritik Stok Uyarıları (Güncellenmiş Kod) -->
<div class="col-lg-5">
    <div class="dashboard-card dashboard-card-light">
        <div class="dashboard-card-header">
            <h5><i class="fas fa-exclamation-triangle"></i> Kritik Stok Uyarıları</h5>
            <a href="{{-- Buraya stok listenizin rotasını ekleyin --}}" class="view-all-btn">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="dashboard-card-body">
            @php $criticalCount = count($stock_alerts['critical']); @endphp
            
            {{-- Kritik Seviyedeki Ürünler --}}
            @foreach ($stock_alerts['critical'] as $item)
            <div class="stock-item">
                <div class="stock-icon-wrapper critical"><i class="fas fa-box-open"></i></div>
                <div class="stock-details">
                    <h6>{{ $item->urunAdi }}</h6>
                    {{-- Not: urunKategori bir ID'dir. İsterseniz kategori tablosuyla join yapıp adını getirebilirsiniz. --}}
                    <p>Kategori ID: {{ $item->urunKategori }}</p>
                </div>
                <div class="stock-level critical">
                    <div class="level-text">{{ $item->current_stock }} / {{ $item->threshold }}</div>
                    <div class="level-label">Kritik Seviye</div>
                </div>
            </div>
            @endforeach

            {{-- Düşük Stoktaki Ürünler --}}
            @foreach ($stock_alerts['low'] as $item)
            <div class="stock-item">
                <div class="stock-icon-wrapper low"><i class="fas fa-box-open"></i></div>
                <div class="stock-details">
                    <h6>{{ $item->urunAdi }}</h6>
                    <p>Kategori ID: {{ $item->urunKategori }}</p>
                </div>
                <div class="stock-level low">
                    <div class="level-text">{{ $item->current_stock }} / {{ $item->threshold }}</div>
                    <div class="level-label">Düşük Stok</div>
                </div>
            </div>
            @endforeach
            
            {{-- Eğer kritik ürün varsa alt uyarı mesajı --}}
            @if ($criticalCount > 0)
            <div class="stock-alert-footer">
               <i class="fas fa-info-circle"></i> {{ $criticalCount }} ürün kritik stok seviyesinde! Acilen tedarik yapılması gerekiyor.
            </div>
            @endif

            {{-- Eğer hiç uyarı yoksa --}}
            @if(empty($stock_alerts['critical']) && empty($stock_alerts['low']))
            <p class="text-center text-muted">Kritik seviyede ürün bulunmamaktadır.</p>
            @endif
        </div>
    </div>
</div>



            <!-- Grafikler -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5><i class="fas fa-chart-line"></i> Günlük Servis Trendi</h5>
                            <div class="time-filter">
                                <button class="filter-btn active" data-period="7" data-chart="daily">7 Gün</button>
                                <button class="filter-btn" data-period="15" data-chart="daily">15 Gün</button>
                                <button class="filter-btn" data-period="30" data-chart="daily">30 Gün</button>
                            </div>
                        </div>
                        <div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>
                        <div class="chart-canvas">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h5><i class="fas fa-clock"></i> Saatlik Servis Dağılımı</h5>
                            <div class="time-filter">
                                <button class="filter-btn active" data-period="7" data-chart="hourly">7 Gün</button>
                                <button class="filter-btn" data-period="15" data-chart="hourly">15 Gün</button>
                                <button class="filter-btn" data-period="30" data-chart="hourly">30 Gün</button>
                            </div>
                        </div>
                        <div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>
                        <div class="chart-canvas">
                            <canvas id="hourlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script>
        // Global değişkenler
        let dailyChart, hourlyChart;
        let currentDailyPeriod = 7;
        let currentHourlyPeriod = 7;

        // CSRF token al
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Hata gösterme fonksiyonu
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }

        // Tarih formatı fonksiyonu
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // URL oluşturma fonksiyonu
        function buildServiceUrl(type) {
            const tenant_id = {{ $user->tenant->id }};
            const baseUrl = `/${tenant_id}/servisler`;
            const today = new Date();
            let startDate, endDate;

            switch(type) {
                case 'today':
                    startDate = formatDate(today);
                    endDate = formatDate(today);
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(today.getDate() - 1);
                    startDate = formatDate(yesterday);
                    endDate = formatDate(yesterday);
                    break;
                case 'previous':
                    const previousDay = new Date(today);
                    previousDay.setDate(today.getDate() - 2);
                    startDate = formatDate(previousDay);
                    endDate = formatDate(previousDay);
                    break;
                case 'total':
                    // Son 30 gün
                    const lastMonth = new Date(today);
                    lastMonth.setMonth(today.getMonth() - 1);
                    startDate = formatDate(lastMonth);
                    endDate = formatDate(today);
                    break;
                default:
                    startDate = formatDate(today);
                    endDate = formatDate(today);
            }

            return `${baseUrl}?dashboard_filter=1&dashboard_istatistik_tarih1=${startDate}&dashboard_istatistik_tarih2=${endDate}`;
        }

        function buildCashUrl(type) {
            const tenant_id = {{ $user->tenant->id }};
            const baseUrl = `/${tenant_id}/kasa-filtrele`;
            const today = new Date();
            let startDate, endDate;

            switch(type) {
                case 'today':
                    startDate = formatDate(today);
                    endDate = formatDate(today);
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(today.getDate() - 1);
                    startDate = formatDate(yesterday);
                    endDate = formatDate(yesterday);
                    break;
                case 'previous':
                    const previousDay = new Date(today);
                    previousDay.setDate(today.getDate() - 2);
                    startDate = formatDate(previousDay);
                    endDate = formatDate(previousDay);
                    break;
                case 'total':
                    startDate = '2025-01-01';
                    endDate = formatDate(today);
                    break;
                default:
                    startDate = formatDate(today);
                    endDate = formatDate(today);
            }

            return `${baseUrl}?dashboard_filter=1&dashboard_istatistik_tarih1=${startDate}&dashboard_istatistik_tarih2=${endDate}`;
        }

        // İstatistikleri yükle - YENİ ROUTE YAPISI
        async function loadStats() {
            try {
                const tenant_id = {{ $user->tenant->id }};
                const url = `/${tenant_id}/dashboard/stats`;
                
                console.log('Stats URL:', url); // Debug için
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Stats Response Status:', response.status); // Debug için
                
                const result = await response.json();
                
                console.log('Stats Result:', result); // Debug için
                
                if (result.success) {
                    const data = result.data;
                    
                    // Sayaçları direkt güncelle (animasyon yok)
                    updateCounter('totalServices', data.total_services);
                    updateCounter('totalCustomers', data.total_customers);
                    updateCounter('totalPersonnel', data.total_personnel);
                    updateCounter('todayServices', data.today_services);
                    updateCounter('yesterdayServices', data.yesterday_services);
                    updateCounter('previousServices', data.previous_services);
                    
                    // Günlük kasa bilgisini güncelle
                    document.getElementById('totalCash').textContent = 
                        new Intl.NumberFormat('tr-TR', {
                            style: 'currency',
                            currency: 'TRY',
                            minimumFractionDigits: 2
                        }).format(data.monthly_cash.net);
                        
                } else {
                    console.error('Stats yüklenirken hata:', result.message);
                    showError('İstatistikler yüklenirken hata oluştu: ' + result.message);
                }
            } catch (error) {
                console.error('AJAX Stats hatası:', error);
                showError('Sunucuya bağlanırken hata oluştu. Lütfen daha sonra tekrar deneyin.');
            }
        }

        // Grafik verilerini yükle - YENİ ROUTE YAPISI
        async function loadChartData(period, chartType) {
            try {
                const tenant_id = {{ $user->tenant->id }};
                const url = `/${tenant_id}/dashboard/chart-data?period=${period}&type=${chartType}`;
                
                console.log('Chart URL:', url); // Debug için
                console.log('Chart Params:', { period, chartType }); // Debug için
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Chart Response Status:', response.status); // Debug için
                
                const result = await response.json();
                
                console.log('Chart Result:', result); // Debug için
                
                if (result.success) {
                    return result.data;
                } else {
                    console.error('Chart data yüklenirken hata:', result.message);
                    showError('Grafik verisi yüklenirken hata oluştu: ' + result.message);
                    return null;
                }
            } catch (error) {
                console.error('AJAX Chart hatası:', error);
                showError('Grafik verisi yüklenirken hata oluştu.');
                return null;
            }
        }

        // Grafik başlatma
        async function initCharts() {
            const dailyCtx = document.getElementById('dailyChart').getContext('2d');
            const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');

            // İlk veriyi yükle
            const dailyData = await loadChartData(7, 'daily');
            const hourlyData = await loadChartData(7, 'hourly');

            // Günlük grafik - MAVİ SÜTUN GRAFİK
            dailyChart = new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: dailyData?.labels || [],
                    datasets: [{
                        label: 'Günlük Servis',
                        data: dailyData?.data || [],
                        backgroundColor: 'rgba(79, 172, 254, 0.8)',
                        borderColor: '#4facfe',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f1f1'
                            },
                            ticks: {
                                color: '#6c757d'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6c757d'
                            },
                            categoryPercentage: 0.8, // Kategoriler arası boşluk az
                            barPercentage: 0.6 // Sütun kalınlığı fazla
                        }
                    }
                }
            });

            // Saatlik grafik - SARI NOKTA GRAFİK (küçük noktalar, dolu)
            hourlyChart = new Chart(hourlyCtx, {
                type: 'line',
                data: {
                    labels: hourlyData?.labels || [],
                    datasets: [{
                        label: 'Saatlik Servis',
                        data: hourlyData?.data || [],
                        borderColor: '#ffc107', // Sarı çizgi
                        backgroundColor: 'rgba(255,193,7,0.2)', // Hafif sarı arkaplan
                        borderWidth: 3,
                        fill: true, // İçi dolu olsun
                        tension: 0.4,
                        pointBackgroundColor: '#ffc107', // Sarı noktalar
                        pointBorderColor: '#ffc107',
                        pointBorderWidth: 2,
                        pointRadius: 5, // Küçük noktalar
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f1f1'
                            },
                            ticks: {
                                color: '#6c757d'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6c757d'
                            }
                        }
                    }
                }
            });
        }

        // Grafik güncelleme fonksiyonu
        async function updateChart(chartType, period) {
            const loadingEl = document.querySelector(`.chart-container:has(#${chartType}Chart) .loading`);
            loadingEl.style.display = 'block';
            
            try {
                const data = await loadChartData(period, chartType);
                
                if (data) {
                    if (chartType === 'daily') {
                        dailyChart.data.labels = data.labels;
                        dailyChart.data.datasets[0].data = data.data;
                        dailyChart.update('active');
                        currentDailyPeriod = period;
                    } else {
                        hourlyChart.data.labels = data.labels;
                        hourlyChart.data.datasets[0].data = data.data;
                        hourlyChart.update('active');
                        currentHourlyPeriod = period;
                    }
                }
            } catch (error) {
                console.error('Grafik güncellenirken hata:', error);
                showError('Grafik güncellenirken hata oluştu.');
            } finally {
                loadingEl.style.display = 'none';
            }
        }

        // Sayaçları güncelleme
        function updateCounter(elementId, targetValue) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            // Animasyon kaldırıldı, direkt değer atanıyor
            element.textContent = targetValue;
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // İstatistikleri yükle
            loadStats();
            
            // Grafikleri başlat
            initCharts();
            
            // Filter button event listeners
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const period = parseInt(this.dataset.period);
                    const chartType = this.dataset.chart;
                    
                    // Active button güncelle
                    this.parentElement.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Grafiği güncelle
                    updateChart(chartType, period);
                });
            });

            // İstatistik kartları için click event listeners
            document.getElementById('totalServicesCard').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = buildServiceUrl('total');
            });

            document.getElementById('todayServicesCard').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = buildServiceUrl('today');
            });

            document.getElementById('yesterdayServicesCard').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = buildServiceUrl('yesterday');
            });

            document.getElementById('previousServicesCard').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = buildServiceUrl('previous');
            });
        });

        // Periyodik güncelleme (opsiyonel - her 5 dakikada bir)
        setInterval(() => {
            loadStats();
        }, 5 * 60 * 1000); // 5 dakika
    </script>
@endsection