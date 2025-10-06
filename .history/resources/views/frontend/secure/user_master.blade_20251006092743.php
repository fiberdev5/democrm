<!doctype html>
<html lang="tr">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    @php 
        $user = Auth::user();
    @endphp
    <title>{{ $user && $user->tenant && $user->tenant->firma_adi ? $user->tenant->firma_adi : 'Yönetim Paneli' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">
 
    <!-- CSS Files -->
    <link href="{{ asset('backend/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <link rel="stylesheet" href="{{asset('backend/assets/libs/select2/css/select2.min.css')}}" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="{{ asset('backend/assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('frontend/css/secure.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{asset('backend/assets/libs/dropzone/min/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="{{asset('backend/assets/libs/spectrum-colorpicker2/spectrum.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="preload" as="image" href="{{ asset('frontend/img/alarm.gif') }}">

    <!-- jQuery - SADECE BİR KEZ -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>

<body data-topbar="dark">
    <div id="layout-wrapper">
        @include('frontend.secure.body.header')
        @include('frontend.secure.body.sidebar')
        
        <div class="main-content">
            <!-- Deneme süresi uyarısı -->
            @if (session('warning'))
                <div class="fullwidth-app-alert warning-app-alert">
                    <div class="alert-left">
                        <span class="alert-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.706c.89 0 1.438-.99.982-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1-2 .001 1 1 0 0 1 2-.001z"/>
                            </svg>
                        </span>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button type="button" class="close-app-alert-button" onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
            @endif

            <!-- Impersonation Banner -->
            <div id="impersonationBanner" class="d-none">
                <div class="alert alert-warning m-0" style="border-radius: 0;padding:10px 0px; border: none; border-bottom: 3px solid #ffc107;">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div style="padding-right:3px" class="col-auto col-md-1 col-2 ms-auto text-center">
                                <i class="fas fa-user-secret fa-2x text-warning"></i>
                            </div>
                            <div style="padding-left:3px;padding-right:3px" class="col col-md-9 col-10">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <strong class="font-md">Kimliğe Bürünme Aktif</strong>
                                        <div id="impersonationDetails" class="small"></div>
                                    </div>
                                    <div class="me-3">
                                        <small class="text-muted">Başlangıç:</small>
                                        <div id="impersonationTime" class="small fw-bold"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto back-button col-md-2">
                                <button type="button" class="btn btn-outline-dark btn-sm" id="exitImpersonation">
                                    <i class="fas fa-sign-out-alt me-1"></i>
                                    Süper Admin Hesabıma Dön
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @yield('user')

            @include('frontend.secure.body.footer')
        </div>
    </div>

    <!-- JavaScript Files - Optimized Loading -->
    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('backend/assets/libs/metismenu/metisMenu.min.js') }}" defer></script>
    <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}" defer></script>
    <script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}" defer></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}" defer></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js" defer></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js" defer></script>
    <script src="{{ asset('backend/assets/js/app.js') }}" defer></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js" defer></script>
    <script src="{{asset('backend/assets/libs/spectrum-colorpicker2/spectrum.min.js')}}" defer></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" defer></script>

    <!-- Dropzone - SADECE BİR KEZ -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.0/dropzone.min.js" defer></script>

    <!-- Toastr Messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.options = {
                "positionClass": "toast-top-center"
            };
            @if (Session::has('message'))
                var type = "{{ Session::get('alert-type', 'info') }}"
                switch (type) {
                    case 'info':
                        toastr.info(" {{ Session::get('message') }} ");
                        break;
                    case 'success':
                        toastr.success(" {{ Session::get('message') }} ");
                        break;
                    case 'warning':
                        toastr.warning(" {{ Session::get('message') }} ");
                        break;
                    case 'error':
                        toastr.error(" {{ Session::get('message') }} ");
                        break;
                }
            @endif
        });
    </script>

    <!-- Datepicker Initialization -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeDatePickers();
            
            $(document).on('shown.bs.modal', '.modal', function() {
                initializeDatePickers();
            });
            
            $(document).ajaxComplete(function() {
                setTimeout(function() {
                    initializeDatePickers();
                }, 100);
            });
        });

        function initializeDatePickers() {
            $('.datepicker:not(.flatpickr-input), .kayitTarihi:not(.flatpickr-input)').each(function() {
                const currentValue = this.value;
                flatpickr(this, {
                    dateFormat: "Y-m-d",
                    altInput: false,
                    locale: "tr",
                    allowInput: true,
                    defaultDate: currentValue || "today",
                    onChange: function(selectedDates, dateStr, instance) {
                        this.value = dateStr;
                    }
                });
            });
        }
    </script>

    <!-- Impersonation Script -->
    <script>
        $(document).ready(function() {
            checkImpersonationStatus();
            $('#exitImpersonation').click(function() {
                exitImpersonation();
            });

            function checkImpersonationStatus() {
                $.get('/impersonation/status')
                    .done(function(response) {
                        if (response.is_impersonating) {
                            showImpersonationBanner(response);
                        } else {
                            hideImpersonationBanner();
                        }
                    })
                    .fail(function() {
                        hideImpersonationBanner();
                    });
            }

            function showImpersonationBanner(data) {
                var details = `${data.impersonated.name} olarak giriş yapmışsınız`;
                var startTime = new Date(data.started_at).toLocaleString('tr-TR');
                
                $('#impersonationDetails').text(details);
                $('#impersonationTime').text(startTime);
                $('#impersonationBanner').removeClass('d-none');
                $('body').addClass('impersonating');
            }

            function hideImpersonationBanner() {
                $('#impersonationBanner').addClass('d-none');
                $('body').removeClass('impersonating');
            }

            function exitImpersonation() {
                $('#exitImpersonation').prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-1"></span>Çıkış yapılıyor...
                `);

                $.post('/impersonation/stop', {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function(response) {
                    if (response.success && response.redirect_url) {
                        window.location.href = response.redirect_url;
                    }
                })
                .fail(function(xhr) {
                    var error = xhr.responseJSON?.message || 'Çıkış yapılamadı';
                    showNotification(error, 'danger');
                })
                .always(function() {
                    $('#exitImpersonation').prop('disabled', false).html(`
                        <i class="fas fa-sign-out-alt me-1"></i>Kendi Hesabıma Dön
                    `);
                });
            }

            window.showNotification = function(message, type) {
                var alertClass = `alert-${type}`;
                var icon = type === 'success' ? 'check' : 
                          type === 'danger' ? 'exclamation-triangle' : 
                          type === 'warning' ? 'exclamation-triangle' : 'info';
                
                var notification = `
                    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                         style="top: 80px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <i class="fas fa-${icon} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                $('body').append(notification);
                
                setTimeout(() => {
                    $('.alert').fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
            };
        });
    </script>

    <style>
        .impersonating {
            position: relative;
        }
        .impersonating .navbar {
            border-top: 3px solid #ffc107;
        }
        .impersonation-mode {
            opacity: 0.95;
        }
        #impersonationBanner {
            position: sticky;
            top: 0;
            z-index: 1040;
            height: 0px;
            margin-left: 6px;
        }
        @media (max-width: 767px) {
           #impersonationBanner {
                margin-left: 0px;
                height: 54px;
            } 
            .font-md{
                font-size:13px;
            }
            .back-button{
                margin-top:10px;
            }
        }
    </style>

    <!-- TinyMCE - Sadece gerekli sayfalarda yükleyin -->
    @if(isset($needsEditor) && $needsEditor)
        <script src="{{ asset('backend/assets/libs/tinymce/tinymce.min.js') }}" defer></script>
        <script src="{{ asset('backend/assets/js/pages/form-editor.init.js') }}" defer></script>
    @endif

    <script src="{{asset('backend/assets/js/pages/form-validation.init.js')}}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <script src="{{asset('backend/assets/js/pages/form-advanced.init.js')}}" defer></script>
    <script src="{{ asset('backend/assets/js/pages/datatables.init.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10" defer></script>
    <script src="{{ asset('backend/assets/js/code.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" defer></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" defer></script>
</body>
</html>