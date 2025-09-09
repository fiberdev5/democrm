<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serbis - Giriş / Kayıt</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: 
            linear-gradient(135deg, rgb(0 0 0 / 27%) 0%, rgb(52 73 94 / 67%) 100%),
                url('{{ asset("frontend/img/14624.jpg") }}');
            background-size: cover;
            background-position: center;
        }
        h4 {
            font-size: 1.2rem;
        }
        p {
            font-size: 0.85rem;
        }
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
            min-height: auto;
            padding: 20px;
        }

        .logo-section {
            text-align: center;
            padding: 10px 0 10px;
            background: linear-gradient(135deg, #f27c22 0%, #f58733 100%);
            color: white;
        }

        .logo-section img {
            width: 120px;
            height: auto;
            filter: brightness(0) invert(1);
        }

        .logo-section h2 {
            font-weight: 700;
        }

        .logo-section p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .form-container {
            padding: 25px 20px;
            position: relative;
        }

        .form-toggle {
            display: flex;
            background: #f8f9fa;
            border-radius: 50px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .toggle-btn {
            flex: 1;
            padding: 12px 0;
            text-align: center;
            background: none;
            border: 1px solid #f27c22;
            border-radius: 50px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .toggle-btn.active {
            color: white;
        }

        .toggle-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: linear-gradient(135deg, #f27c22 0%, #f58733 100%);
            border-radius: 50px;
            transition: transform 0.3s ease;
            z-index: 1;
        }

        .toggle-slider.register {
            transform: translateX(100%);
        }

        .form-section {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .form-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #f27c22;
            box-shadow: 0 0 0 0.2rem rgba(242, 124, 34, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #f27c22 0%, #f58733 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #e66b14 0%, #e47424 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(242, 124, 34, 0.3);
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            margin: 0 10px;
        }

        .step-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #e0e0e0;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .step.active .step-icon {
            background: linear-gradient(135deg, #f27c22 0%, #f58733 100%);
            color: white;
            transform: scale(1.1);
        }

        .step.finish .step-icon {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
        }

        .step-label {
            margin-top: 8px;
            font-size: 0.75rem;
            color: #888;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #f27c22;
            font-weight: 700;
        }

        .step-connector {
            position: absolute;
            top: 17px;
            left: 60%;
            width: 80px;
            height: 2px;
            background-color: #e0e0e0;
        }

        .step:last-child .step-connector {
            display: none;
        }

        .form-step {
            display: none;
            animation: slideIn 0.5s ease;
        }

        .form-step.active {
            display: block;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            gap: 15px;
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
        }

        .info-icon {
            margin-left: 10px;
            cursor: pointer;
            position: relative;
            display: inline-block;
            color: #f27c22;
        }

        .info-icon .tooltip-text {
            visibility: hidden;
            width: 250px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 10px;
            position: absolute;
            z-index: 1000;
            bottom: 125%;
            left: 50%;
            margin-left: -125px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.85rem;
        }

        .info-icon .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        .info-icon:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .text-danger {
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .character-counter {
            float: right;
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }

        /* SMS Verification specific styles */
        .countdown-timer {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #f27c22;
        }

        .countdown-timer .timer {
            font-size: 1.2rem;
            font-weight: bold;
            color: #dc3545;
        }

        .sms-info {
            background: #e8f4fd;
            border: 1px solid #b8daff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .auth-card {
                margin: 10px;
                min-height: auto;
            }
            
            .form-container {
                padding: 30px 20px;
            }
            
            .step-connector {
                width: 40px;
                left: 45px;
            }
        }

        @media (max-width: 1366px) {
            .auth-card {
                max-width: 500px;
            }
            .form-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="auth-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <h2>Serbis</h2>
                <p>Teknik Servis Yönetim Sistemi</p>
            </div>

            <!-- Form Container -->
            <div class="form-container">
                <!-- Form Toggle -->
                <div class="form-toggle">
                    <div class="toggle-slider" id="toggleSlider"></div>
                    <button class="toggle-btn active" id="loginToggle">Giriş Yap</button>
                    <button class="toggle-btn" id="registerToggle">Kayıt Ol</button>
                </div>

                <!-- Login Form -->
                <div class="form-section active" id="loginForm">
                    <form method="POST" action="{{ route('giris.action') }}">
                        @csrf
                        <h4 class="text-center mb-4" style="color: #333; font-weight: 600;">Hoş Geldiniz</h4>
                        <p class="text-center mb-4" style="color: #666; font-size: 0.9rem;">
                            Kullanıcı adı ve parolanız ile güvenli giriş yapabilirsiniz.
                        </p>

                        <!-- Success/Error Messages -->
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @error('email')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="mb-3">
                            <div class="input-wrapper">
                                <input type="email" name="email" id="loginEmail" class="form-control" 
                                       placeholder="kullaniciadi@firmaadi.com" required>
                                <div class="info-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                                         class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                    </svg>
                                    <span class="tooltip-text">
                                        Lütfen kurumsal e-posta adresinizi giriniz. Bu adres, kullaniciadiniz@firmaadi.com şeklinde olmalıdır.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <input type="password" name="password" id="loginPassword" class="form-control" 
                                   placeholder="Şifre" required>
                        </div>

                        <div class="mb-4">
                            <div class="g-recaptcha" data-sitekey="6Ldl86UrAAAAAIo9asM85k5ajB363yYtf8FuKQgu" data-action="LOGIN"></div>
                            @if($errors->has('g-recaptcha-response'))
                                <div class="text-danger mt-2">
                                    <small>{{$errors->first('g-recaptcha-response')}}</small>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">Giriş Yap</button>
                        
                        <p class="text-center mb-0" style="color: #666; font-size: 0.9rem;">
                            Hesabınız yok mu? 
                            <a href="#" id="switchToRegister" style="color: #f27c22; font-weight: 600; text-decoration: none;">
                                Kayıt Ol
                            </a>
                        </p>
                    </form>
                </div>

                <!-- Register Form -->
                <div class="form-section" id="registerForm">
                    <!-- Multi-step register form -->
                    <form id="multiStepForm" method="POST">
                        @csrf
                        <h4 class="text-center mb-4" style="color: #333; font-weight: 600;">Hesap Oluşturun</h4>

                        <!-- Step Indicators -->
                        <div class="step-indicator">
                            <div class="step active" data-step="1">
                                <div class="step-icon">1</div>
                                <div class="step-label">Kişisel</div>
                                <div class="step-connector"></div>
                            </div>
                            <div class="step" data-step="2">
                                <div class="step-icon">2</div>
                                <div class="step-label">Firma</div>
                                <div class="step-connector"></div>
                            </div>
                            <div class="step" data-step="3">
                                <div class="step-icon">3</div>
                                <div class="step-label">SMS</div>
                            </div>
                        </div>

                        <!-- Step 1: Personal Information -->
                        <div class="form-step active">
                            <div class="mb-3">
                                <label for="vergiNo" class="form-label" style="color: #333; font-weight: 500;">
                                    Vergi Numarası <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" name="vergiNo" id="vergiNo" class="form-control vergiNo" 
                                       placeholder="Vergi Numarası" required>
                                @error('vergiNo')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label" style="color: #333; font-weight: 500;">
                                    Ad Soyad <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control" 
                                       placeholder="Ad Soyad" required>
                                @error('name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label" style="color: #333; font-weight: 500;">
                                    E-posta Adresiniz <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="email" name="email" id="registerEmail" class="form-control" 
                                       placeholder="E-posta" required>
                                @error('email')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Step 2: Company Information -->
                        <div class="form-step">
                            <div class="mb-3">
                                <label for="firma_adi" class="form-label" style="color: #333; font-weight: 500;">
                                    Firma Adı <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" name="firma_adi" id="firma_adi" maxlength="50" 
                                       class="form-control" placeholder="Firma Adı" required>
                                <small id="firmaAdiCounter" class="character-counter">0 / 50</small>
                                @error('firma_adi')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="tel" class="form-label" style="color: #333; font-weight: 500;">
                                    Firma Telefon Numarası <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" name="tel" id="tel" class="form-control tel" 
                                       placeholder="5xx xxx xx xx" required>
                                @error('tel')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label" style="color: #333; font-weight: 500;">
                                    Şifre <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="password" name="password" id="registerPassword" class="form-control" 
                                       placeholder="Şifre" required>
                                <small class="form-text" style="color: #6c757d;">Şifre en az 6 karakter olmalıdır.</small>
                                @error('password')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Step 3: SMS Verification -->
                        <div class="form-step">
                            <div class="sms-info">
                                <h5 style="color: #f27c22; margin-bottom: 10px;">SMS Doğrulama</h5>
                                <p id="smsInfoText" style="margin: 0; color: #666;">
                                    Lütfen <strong id="phoneDisplay">+90 --- --- -- --</strong> numaralı telefona gönderilen 6 haneli doğrulama kodunu giriniz.
                                </p>
                            </div>

                            <div class="countdown-timer" id="countdownTimer" style="display: none;">
                                <p style="margin: 0; color: #666;">Kalan süre:</p>
                                <span class="timer" id="countdown">3:00</span>
                            </div>

                            <div class="mb-3">
                                <label for="smsCode" class="form-label" style="color: #333; font-weight: 500;">
                                    Doğrulama Kodu <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" name="smsCode" id="smsCode" class="form-control" 
                                       placeholder="6 haneli kod" required maxlength="6">
                                <div id="smsCodeError" class="text-danger mt-2" style="display: none;"></div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="button-group">
                            <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                                ← Geri
                            </button>
                            <button type="button" class="btn btn-primary" id="nextBtn" style="flex: 1;">
                                İleri →
                            </button>
                        </div>

                        <p class="text-center mt-3 mb-0" style="color: #666; font-size: 0.9rem;">
                            Zaten hesabın var mı? 
                            <a href="#" id="switchToLogin" style="color: #f27c22; font-weight: 600; text-decoration: none;">
                                Giriş Yap
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Input masks
            $(".tel").mask("999 999 99 99");
            $(".vergiNo").mask("0000000000");
            $("#smsCode").mask("000000");

            // Character counter for company name
            $('#firma_adi').on('input', function() {
                var currentLength = $(this).val().length;
                var maxLength = $(this).attr('maxlength');
                $('#firmaAdiCounter').text(currentLength + " / " + maxLength);
                
                if (currentLength >= maxLength) {
                    $('#firmaAdiCounter').removeClass('text-muted').addClass('text-danger');
                } else {
                    $('#firmaAdiCounter').removeClass('text-danger').addClass('text-muted');
                }
            });

            // Form toggle functionality
            let isLoginMode = true;
            
            $('#loginToggle, #switchToLogin').on('click', function(e) {
                e.preventDefault();
                if (!isLoginMode) {
                    switchToLogin();
                }
            });
            
            $('#registerToggle, #switchToRegister').on('click', function(e) {
                e.preventDefault();
                if (isLoginMode) {
                    switchToRegister();
                }
            });

            function switchToLogin() {
                isLoginMode = true;
                $('#toggleSlider').removeClass('register');
                $('#loginToggle').addClass('active');
                $('#registerToggle').removeClass('active');
                $('#loginForm').addClass('active');
                $('#registerForm').removeClass('active');
                
                // Reset register form
                resetRegisterForm();
            }

            function switchToRegister() {
                isLoginMode = false;
                $('#toggleSlider').addClass('register');
                $('#registerToggle').addClass('active');
                $('#loginToggle').removeClass('active');
                $('#registerForm').addClass('active');
                $('#loginForm').removeClass('active');
            }

            function resetRegisterForm() {
                currentStep = 0;
                showStep(currentStep);
                $('#multiStepForm')[0].reset();
                clearInterval(countdownInterval);
                $('#countdownTimer').hide();
                smsSent = false;
            }

            // Multi-step form functionality
            let currentStep = 0;
    let smsSent = false;
    let countdownInterval = null;
    const steps = $(".form-step");
    
    function showStep(stepIndex) {
        steps.removeClass('active');
        $(steps[stepIndex]).addClass('active');

        // Update button visibility and text
        if (stepIndex === 0) {
            $('#prevBtn').hide();
        } else {
            $('#prevBtn').show();
        }

        if (stepIndex === steps.length - 1) {
            if (smsSent) {
                $('#nextBtn').text('Doğrula ve Kaydı Tamamla');
            } else {
                $('#nextBtn').text('SMS Gönder');
            }
        } else {
            $('#nextBtn').text('İleri →');
        }

        // Update step indicators
        $('.step').removeClass('active finish');
        $('.step').each(function(index) {
            if (index < stepIndex) {
                $(this).addClass('finish');
            } else if (index === stepIndex) {
                $(this).addClass('active');
            }
        });

        // Update phone display for SMS step
        if (stepIndex === 2) {
            const phoneNumber = $('#tel').val();
            if (phoneNumber) {
                $('#phoneDisplay').text('+90 ' + phoneNumber);
            }
        }
    }

    // Frontend validasyon
    function validateStepFields(stepIndex) {
        let isValid = true;
        $(steps[stepIndex]).find('input[required]').each(function() {
            if ($(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
                
                // Hata mesajı göster
                let errorDiv = $(this).siblings('.text-danger');
                if (errorDiv.length === 0) {
                    $(this).after('<small class="text-danger">Bu alan zorunludur.</small>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.text-danger').remove();
            }
        });
        return isValid;
    }

    // Backend validasyon (AJAX ile)
    function validateStepOnServer(stepIndex) {
        return new Promise((resolve, reject) => {
            let formData = {};
            
            // Hangi adımda olduğumuza göre veri topla
            if (stepIndex === 0) {
                // 1. Adım: Kişisel bilgiler
                formData = {
                    name: $('#name').val(),
                    email: $('#registerEmail').val(),
                    vergiNo: $('#vergiNo').val(),
                    step: 1,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
            } else if (stepIndex === 1) {
                // 2. Adım: Firma bilgileri
                formData = {
                    name: $('#name').val(),
                    email: $('#registerEmail').val(),
                    vergiNo: $('#vergiNo').val(),
                    firma_adi: $('#firma_adi').val(),
                    tel: $('#tel').val(),
                    password: $('#registerPassword').val(),
                    step: 2,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
            }

            $.ajax({
                url: '{{ route("validate.step") }}', // Bu route'u oluşturacağız
                method: 'POST',
                data: formData,
                success: function(response) {
                    resolve(response);
                },
                error: function(xhr) {
                    reject(xhr.responseJSON);
                }
            });
        });
    }

    // Hata mesajlarını göster
    function displayErrors(errors) {
        // Önce tüm hata mesajlarını temizle
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid');

        // Her bir hata için mesaj göster
        Object.keys(errors).forEach(function(field) {
            const input = $(`[name="${field}"], #${field}`);
            if (input.length > 0) {
                input.addClass('is-invalid');
                input.after(`<small class="text-danger">${errors[field][0]}</small>`);
            }
        });
    }

    // İleri butonuna tıklandığında
    $('#nextBtn').on('click', async function(e) {
        e.preventDefault();
        
        if (currentStep < 2) {
            // Frontend validasyonu
            if (!validateStepFields(currentStep)) {
                return;
            }

            try {
                // Backend validasyonu
                await validateStepOnServer(currentStep);
                
                // Validasyon başarılı, sonraki adıma geç
                currentStep++;
                showStep(currentStep);
                
                // Eğer 3. adıma (SMS) geçtiyse, SMS'i otomatik gönder
                if (currentStep === 2 && !smsSent) {
                    sendSMS();
                }
                
            } catch (error) {
                // Backend validasyon hatası
                if (error.errors) {
                    displayErrors(error.errors);
                } else {
                    toastr.error(error.message || 'Bir hata oluştu.');
                }
            }
        } else if (currentStep === 2) {
            // SMS adımı
            if (!smsSent) {
                sendSMS();
            } else {
                verifySMSAndComplete();
            }
        }
    });

    // Diğer fonksiyonlar (sendSMS, verifySMSAndComplete, vb.) aynı kalacak...
    function sendSMS() {
        const formData = {
            vergiNo: $('#vergiNo').val(),
            name: $('#name').val(),
            email: $('#registerEmail').val(),
            firma_adi: $('#firma_adi').val(),
            tel: $('#tel').val(),
            password: $('#registerPassword').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: '{{ route("kayit.action") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                smsSent = true;
                $('#nextBtn').text('Doğrula ve Kaydı Tamamla');
                $('#countdownTimer').show();
                startCountdown();
                toastr.success('SMS başarıyla gönderildi!');
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                if (errors) {
                    displayErrors(errors);
                } else {
                    toastr.error('SMS gönderilirken bir hata oluştu.');
                }
            }
        });
    }

    // Input değişikliklerinde hata mesajlarını temizle
    $('input').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.text-danger').remove();
    });

    // Diğer fonksiyonlar...
    function verifySMSAndComplete() {
        const smsCode = $('#smsCode').val();
        
        if (!smsCode || smsCode.length !== 6) {
            $('#smsCodeError').text('Lütfen 6 haneli doğrulama kodunu giriniz.').show();
            return;
        }

        $.ajax({
            url: '{{ route("sms.verification.verify") }}',
            method: 'POST',
            data: {
                code: smsCode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                toastr.success('Hesabınız başarıyla oluşturuldu!');
                setTimeout(function() {
                    window.location.href = '{{ route("register.success") }}';
                }, 2000);
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Doğrulama kodu hatalı veya süresi dolmuş.';
                $('#smsCodeError').text(errorMsg).show();
            }
        });
    }

    function startCountdown() {
        let duration = 180;
        
        countdownInterval = setInterval(function() {
            const minutes = Math.floor(duration / 60);
            const seconds = duration % 60;
            
            $('#countdown').text(
                minutes + ':' + (seconds < 10 ? '0' : '') + seconds
            );
            
            if (duration <= 0) {
                clearInterval(countdownInterval);
                $('#countdownTimer').hide();
                smsSent = false;
                $('#nextBtn').text('SMS Gönder');
                toastr.warning('SMS doğrulama süresi doldu. Lütfen yeniden deneyin.');
            }
            
            duration--;
        }, 1000);
    }

    // Geri buton
    $('#prevBtn').on('click', function() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
            
            if (currentStep < 2) {
                clearInterval(countdownInterval);
                $('#countdownTimer').hide();
                smsSent = false;
                $('#smsCodeError').hide();
            }
        }
    });

    // Initialize
    showStep(currentStep);

            

            // Clear SMS code error on input
            $('#smsCode').on('input', function() {
                $('#smsCodeError').hide();
            });

            // Toastr notifications
            @if (Session::has('message'))
                var type = "{{ Session::get('alert-type', 'info') }}"
                toastr.options.positionClass = "toast-top-right";
                toastr.options.timeOut = 5000;
                toastr.options.extendedTimeOut = 1000;
                
                switch (type) {
                    case 'info':
                        toastr.info("{{ Session::get('message') }}");
                        break;
                    case 'success':
                        toastr.success("{{ Session::get('message') }}");
                        break;
                    case 'warning':
                        toastr.warning("{{ Session::get('message') }}");
                        break;
                    case 'error':
                        toastr.error("{{ Session::get('message') }}");
                        break;
                }
            @endif
        });
    </script>
</body>
</html>