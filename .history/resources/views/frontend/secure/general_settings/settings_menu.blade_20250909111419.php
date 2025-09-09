<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İyileştirilmiş Dropdown Menü</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Ana konteyner için temel ayarlar */
        .kasaSubMenu {
            position: relative;
            z-index: 1000;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Dropdown container için güçlendirilmiş z-index */
        .kasaSubMenu .dropdown {
            position: relative !important;
            z-index: 1060 !important; /* Bootstrap modal'ından daha yüksek */
        }

        /* Dropdown menu için geliştirilmiş stil */
        .kasaSubMenu .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            z-index: 1070 !important; /* En yüksek öncelik */
            min-width: 250px;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            background-color: #ffffff;
            transform: translateY(2px);
            margin-top: 0;
        }

        /* Dropdown açık olduğunda ek ayarlar */
        .kasaSubMenu .dropdown.show .dropdown-menu {
            display: block !important;
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Dropdown item'lar için stil */
        .kasaSubMenu .dropdown-item {
            padding: 10px 15px;
            transition: all 0.2s ease;
            white-space: nowrap;
            border-bottom: 1px solid #f8f9fa;
        }

        .kasaSubMenu .dropdown-item:last-child {
            border-bottom: none;
        }

        .kasaSubMenu .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #495057;
        }

        .kasaSubMenu .dropdown-item.active {
            background-color: #007bff;
            color: #fff;
        }

        /* Nav pills için özelleştirilmiş stil */
        .kasaSubMenu .nav-pills .nav-link {
            border-radius: 6px;
            margin: 0 2px;
            padding: 8px 16px;
            transition: all 0.2s ease;
        }

        .kasaSubMenu .nav-pills .nav-link.dropdown-toggle::after {
            margin-left: 8px;
        }

        /* Custom icon boyutu */
        .custom-icon {
            font-size: 14px;
            margin-right: 8px;
        }

        .fa-angle-down {
            display: inline-block !important;
            transition: transform 0.2s ease;
        }

        /* Dropdown açık olduğunda ok döndürme */
        .dropdown.show .fa-angle-down {
            transform: rotate(180deg);
        }

        /* Tab content için stil */
        .tab-content {
            margin-top: 20px;
            min-height: 400px;
            background: #fff;
            border-radius: 6px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .tab-pane {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .tab-pane.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive tasarım için */
        @media (max-width: 768px) {
            .kasaSubMenu .nav-pills {
                flex-direction: column;
            }
            
            .kasaSubMenu .nav-item {
                width: 100%;
                margin-bottom: 5px;
            }
            
            .kasaSubMenu .dropdown-menu {
                position: static !important;
                width: 100%;
                box-shadow: none;
                border: 1px solid #dee2e6;
                margin-top: 5px;
            }
        }

        /* Scroll bar için özel stil */
        .kasaSubMenu .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }

        .kasaSubMenu .dropdown-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .kasaSubMenu .dropdown-menu::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .kasaSubMenu .dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Loading state için */
        .tab-content.loading {
            position: relative;
        }

        .tab-content.loading::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            z-index: 100;
        }

        .tab-content.loading::after {
            content: 'Yükleniyor...';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 101;
            padding: 10px 20px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="kasaSubMenu">
            <ul class="nav nav-pills nav-justified" role="tablist" style="margin-bottom: 5px">
                <li class="nav-item" style="font-size: 14px;">
                    <div class="dropdown">
                        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Firma Ayarları</span> <i class="fa fa-angle-down custom-icon"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item nav1 active" data-bs-toggle="pill" href="#tab1" data-id="" role="tab">
                                <i class="fas fa-building custom-icon"></i>Firma Bilgileri
                            </a>
                            <a class="dropdown-item nav2" data-bs-toggle="pill" href="#tab2" data-id="" role="tab">
                                <i class="fas fa-sms custom-icon"></i>Sms Ayarları
                            </a>
                            <a class="dropdown-item nav24" data-bs-toggle="pill" href="#tab24" data-id="" role="tab">
                                <i class="fas fa-percentage custom-icon"></i>Prim Ayarları
                            </a>
                        </div>
                    </div>
                </li>
                
                <li class="nav-item" style="font-size: 14px;">
                    <div class="dropdown">
                        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Servis Ayarları</span> <i class="fa fa-angle-down custom-icon"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item nav3" data-bs-toggle="pill" href="#tab3" data-id="" role="tab">
                                <i class="fas fa-tags custom-icon"></i>Cihaz Markaları
                            </a>
                            <a class="dropdown-item nav4" data-bs-toggle="pill" href="#tab4" data-id="" role="tab">
                                <i class="fas fa-laptop custom-icon"></i>Cihaz Türleri
                            </a>
                            <a class="dropdown-item nav5" data-bs-toggle="pill" href="#tab5" data-id="" role="tab">
                                <i class="fas fa-shield-alt custom-icon"></i>Garanti Süreleri
                            </a>
                            <a class="dropdown-item nav6" data-bs-toggle="pill" href="#tab6" data-id="" role="tab">
                                <i class="fas fa-tools custom-icon"></i>Servis Araçları
                            </a>
                            <a class="dropdown-item nav7" data-bs-toggle="pill" href="#tab7" data-id="" role="tab">
                                <i class="fas fa-list-ol custom-icon"></i>Servis Aşamaları
                            </a>
                            <a class="dropdown-item nav8" data-bs-toggle="pill" href="#tab8" data-id="" role="tab">
                                <i class="fas fa-question-circle custom-icon"></i>Servis Aşama Soruları
                            </a>
                            <a class="dropdown-item nav9" data-bs-toggle="pill" href="#tab9" data-id="" role="tab">
                                <i class="fas fa-clock custom-icon"></i>Servis Görüntüleme Zamanı
                            </a>
                            <a class="dropdown-item nav10" data-bs-toggle="pill" href="#tab10" data-id="" role="tab">
                                <i class="fas fa-source custom-icon"></i>Servis Kaynakları
                            </a>
                            <a class="dropdown-item nav13" data-bs-toggle="pill" href="#tab13" data-id="" role="tab">
                                <i class="fas fa-trash custom-icon"></i>Silinen Servisler
                            </a>
                        </div>
                    </div>
                </li>
                
                <li class="nav-item" style="font-size: 14px;">
                    <div class="dropdown">
                        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>İzinler ve Roller</span> <i class="fa fa-angle-down custom-icon"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item nav14" data-bs-toggle="pill" href="#tab14" data-id="" role="tab">
                                <i class="fas fa-key custom-icon"></i>İzinler
                            </a>
                            <a class="dropdown-item nav15" data-bs-toggle="pill" href="#tab15" data-id="" role="tab">
                                <i class="fas fa-users custom-icon"></i>Roller
                            </a>
                        </div>
                    </div>
                </li>

                <li class="nav-item" style="font-size: 14px;">
                    <div class="dropdown">
                        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Depo Ayarları</span> <i class="fa fa-angle-down custom-icon"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item nav16" data-bs-toggle="pill" href="#tab16" data-id="" role="tab">
                                <i class="fas fa-boxes custom-icon"></i>Stok Kategorileri
                            </a>
                            <a class="dropdown-item nav17" data-bs-toggle="pill" href="#tab17" data-id="" role="tab">
                                <i class="fas fa-warehouse custom-icon"></i>Stok Rafları
                            </a>
                            <a class="dropdown-item nav18" data-bs-toggle="pill" href="#tab18" data-id="" role="tab">
                                <i class="fas fa-truck custom-icon"></i>Tedarikçiler
                            </a>
                        </div>
                    </div>
                </li>

                <li class="nav-item" style="font-size: 14px;">
                    <div class="dropdown">
                        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Kasa Ayarları</span> <i class="fa fa-angle-down custom-icon"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item nav19" data-bs-toggle="pill" href="#tab19" data-id="" role="tab">
                                <i class="fas fa-credit-card custom-icon"></i>Ödeme Türleri
                            </a>
                            <a class="dropdown-item nav20" data-bs-toggle="pill" href="#tab20" data-id="" role="tab">
                                <i class="fas fa-money-bill custom-icon"></i>Ödeme Şekilleri
                            </a>
                        </div>
                    </div>
                </li>

                <li class="nav-item" style="font-size: 14px;">
                    <div class="dropdown">
                        <a href="#" class="btn btn-secondary dropdown-toggle nav-link" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Yazıcı ve Uygulama Ayarları</span> <i class="fa fa-angle-down custom-icon"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item nav22" data-bs-toggle="pill" href="#tab22" data-id="" role="tab">
                                <i class="fas fa-file-alt custom-icon"></i>Servis Form Ayarları
                            </a>
                            <a class="dropdown-item nav23" data-bs-toggle="pill" href="#tab23" data-id="" role="tab">
                                <i class="fas fa-print custom-icon"></i>Yazıcı Fiş Tasarımı
                            </a>
                        </div>
                    </div>
                </li>
            </ul> 

            <div class="tab-content">
                <div id="tab1" class="tab-pane active" style="padding: 0" role="tabpanel">
                    <h5>Firma Bilgileri</h5>
                    <p>Firma bilgileri içeriği burada görüntülenecek...</p>
                </div>
                <div id="tab2" class="tab-pane fade" style="padding: 0" role="tabpanel">
                    <h5>SMS Ayarları</h5>
                    <p>SMS ayarları içeriği burada görüntülenecek...</p>
                </div>
                <div id="tab3" class="tab-pane fade" style="padding: 0" role="tabpanel">
                    <h5>Cihaz Markaları</h5>
                    <p>Cihaz markaları içeriği burada görüntülenecek...</p>
                </div>
                <!-- Diğer tab'lar buraya eklenecek -->
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Dropdown'ların düzgün kapanması için
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').removeClass('show');
                    $('.dropdown-toggle').attr('aria-expanded', 'false');
                }
            });

            // Dropdown item click olayları
            $('.kasaSubMenu .dropdown-item').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Aktif durumu güncelle
                $('.kasaSubMenu .dropdown-item').removeClass('active');
                $(this).addClass('active');
                
                // Tab'ı aktif yap
                var targetTab = $(this).attr('href');
                $('.tab-pane').removeClass('active show');
                $(targetTab).addClass('active show');
                
                // Dropdown'ı kapat
                $(this).closest('.dropdown-menu').removeClass('show');
                $(this).closest('.dropdown').find('.dropdown-toggle').attr('aria-expanded', 'false');
                
                // Loading state ekle
                $('.tab-content').addClass('loading');
                
                // Simüle edilmiş AJAX çağrısı
                setTimeout(function() {
                    $('.tab-content').removeClass('loading');
                }, 1000);
            });

            // Dropdown toggle için özel event
            $('.dropdown-toggle').on('click', function(e) {
                e.preventDefault();
                var $dropdown = $(this).closest('.dropdown');
                var $menu = $dropdown.find('.dropdown-menu');
                
                // Diğer dropdown'ları kapat
                $('.dropdown-menu').not($menu).removeClass('show');
                $('.dropdown-toggle').not(this).attr('aria-expanded', 'false');
                
                // Mevcut dropdown'ı aç/kapat
                $menu.toggleClass('show');
                $(this).attr('aria-expanded', $menu.hasClass('show'));
            });

            // Dropdown menü içindeki clickleri durdur
            $('.dropdown-menu').on('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
</body>
</html>