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
    
    <style>
        /* Mevcut login.css stilleri */
        :root {
            --primary-color: #3e546a;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --border-radius: 12px;
            --shadow: 0 4px 16px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
        }

        .logo-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px 40px;
            text-align: center;
        }

        .logo-section h2 {
            margin: 0 0 8px 0;
            font-weight: 700;
            font-size: 2rem;
        }

        .logo-section p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .form-container {
            padding: 40px 40px 30px 40px;
        }

        .form-toggle {
            display: flex;
            background: #f1f3f4;
            border-radius: 8px;
            padding: 4px;
            margin-bottom: 30px;
            position: relative;
        }

        .toggle-slider {
            position: absolute;
            top: 4px;
            left: 4px;
            width: calc(50% - 4px);
            height: calc(100% - 8px);
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .toggle-slider.register {
            transform: translateX(100%);
        }

        .toggle-btn {
            flex: 1;
            background: none;
            border: none;
            padding: 12px;
            font-weight: 600;
            color: var(--secondary-color);
            transition: var(--transition);
            position: relative;
            z-index: 2;
        }

        .toggle-btn.active {
            color: var(--primary-color);
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(62, 84, 106, 0.15);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: #2c3e50;
            transform: translateY(-1px);
        }

        /* Step Indicators */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .step-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e9ecef;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 8px;
            transition: var(--transition);
            z-index: 2;
            position: relative;
        }

        .step.active .step-icon {
            background: var(--primary-color);
            color: white;
        }

        .step.finish .step-icon {
            background: var(--success-color);
            color: white;
        }

        .step-label {
            font-size: 0.8rem;
            color: var(--secondary-color);
            font-weight: 500;
        }

        .step.active .step-label {
            color: var(--primary-color);
        }

        .step-connector {
            position: absolute;
            top: 18px;
            left: 50%;
            right: -50%;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step:last-child .step-connector {
            display: none;
        }

        .step.finish .step-connector {
            background: var(--success-color);
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .character-counter {
            font-size: 0.8rem;
            color: var(--secondary-color);
            text-align: right;
            display: block;
            margin-top: 4px;
        }

        .input-wrapper {
            position: relative;
        }

        .info-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            cursor: help;
        }

        .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px;
            position: absolute;
            z-index: 1000;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.8rem;
        }

        .info-icon:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Kompakt Plan Info Stilleri - Yeni Eklenen */
        .plan-info-compact {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 16px;
            margin-top: 12px;
            border-left: 4px solid var(--primary-color);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .plan-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .plan-name-compact {
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
            font-size: 1rem;
        }

        .plan-price-compact {
            font-weight: 700;
            color: var(--success-color);
            margin: 0;
            font-size: 1.1rem;
        }

        .limits-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 12px;
        }

        .limit-item {
            background: white;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            min-width: fit-content;
        }

        .limit-value {
            background: var(--primary-color);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            margin-right: 6px;
            min-width: 24px;
            text-align: center;
            font-size: 0.8rem;
        }

        .limit-value.unlimited {
            background: var(--success-color);
        }

        .features-compact {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .feature-tag {
            background: var(--success-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .feature-tag::before {
            content: '✓';
            margin-right: 4px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .auth-card {
                max-width: 100%;
                margin: 0;
            }

            .form-container {
                padding: 30px 20px 20px 20px;
            }

            .logo-section {
                padding: 25px 20px;
            }

            .plan-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
            
            .limits-row {
                gap: 8px;
            }
            
            .limit-item {
                font-size: 0.85rem;
                padding: 6px 10px;
            }
        }

        .sms-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .countdown-timer {
            text-align: center;
            margin-bottom: 20px;
        }

        .timer {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--danger-color);
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
                        <input type="hidden" name="_token" value="demo-token">
                        <h4 class="text-center mb-3" style="color: #333; font-weight: 600;">Hoş Geldiniz</h4>
                        <p class="text-center mb-3" style="color: #666; font-size: 0.9rem;">
                            Kullanıcı adı ve parolanız ile güvenli giriş yapabilirsiniz.
                        </p>

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
                            <div class="g-recaptcha" data-sitekey="demo-key" data-action="LOGIN"></div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">Giriş Yap</button>
                        
                        <p class="text-center mb-0" style="color: #666; font-size: 0.9rem;">
                            Hesabınız yok mu? 
                            <a href="#" id="switchToRegister" style="color: #3e546a; font-weight: 600; text-decoration: none;">
                                Kayıt Ol
                            </a>
                        </p>
                    </form>
                </div>

                <!-- Register Form -->
                <div class="form-section" id="registerForm">
                    <!-- Multi-step register form -->
                    <form id="multiStepForm" method="POST">
                        <input type="hidden" name="_token" value="demo-token">
                        <h4 class="text-center mb-4" style="color: #333; font-weight: 600;">Hesap Oluşturun</h4>

                        <!-- Step Indicators -->
                        <div class="step-indicator">
                            <div class="step active" data-step="1">
                                <div class="step-icon">1</div>
                                <div class="step-label">Plan</div>
                                <div class="step-connector"></div>
                            </div>
                            <div class="step" data-step="2">
                                <div class="step-icon">2</div>
                                <div class="step-label">Kişisel</div>
                                <div class="step-connector"></div>
                            </div>
                            <div class="step" data-step="3">
                                <div class="step-icon">3</div>
                                <div class="step-label">Firma</div>
                                <div class="step-connector"></div>
                            </div>
                            <div class="step" data-step="4">
                                <div class="step-icon">4</div>
                                <div class="step-label">SMS</div>
                            </div>
                        </div>

                        <!-- Step 1: Plan Selection -->
                        <div class="form-step active">
                            <div class="mb-3">
                                <label for="subscription_plan" class="form-label" style="color: #333; font-weight: 500;">
                                    Abonelik Planı Seçin <span style="color: #dc3545;">*</span>
                                </label>
                                <select name="subscription_plan" id="subscription_plan" class="form-select" required>
                                    <option value="">Plan Seçiniz...</option>
                                    <!-- Plans will be loaded via JavaScript -->
                                </select>
                                <div id="planInfo" class="plan-info" style="display: none;">
                                    <div class="plan-features"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Personal Information -->
                        <div class="form-step">
                            <div class="mb-3">
                                <label for="vergiNo" class="form-label" style="color: #333; font-weight: 500;">
                                    Vergi Numarası <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" name="vergiNo" id="vergiNo" class="form-control vergiNo" 
                                       placeholder="Vergi Numarası" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label" style="color: #333; font-weight: 500;">
                                    Ad Soyad <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control" 
                                       placeholder="Ad Soyad" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label" style="color: #333; font-weight: 500;">
                                    E-posta Adresiniz <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="email" name="email" id="registerEmail" class="form-control" 
                                       placeholder="E-posta" required>
                            </div>
                        </div>

                        <!-- Step 3: Company Information -->
                        <div class="form-step">
                            <div class="mb-3">
                                <label for="firma_adi" class="form-label" style="color: #333; font-weight: 500;">
                                    Firma Adı <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" name="firma_adi" id="firma_adi" maxlength="50" 
                                       class="form-control" placeholder="Firma Adı" required>
                                <small id="firmaAdiCounter" class="character-counter">0 / 50</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tel" class="form-label" style="color: #333; font-weight: 500;">
                                    Firma Telefon Numarası <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" name="tel" id="tel" class="form-control tel" 
                                       placeholder="5xx xxx xx xx" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label" style="color: #333; font-weight: 500;">
                                    Şifre <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="password" name="password" id="registerPassword" class="form-control" 
                                       placeholder="Şifre" required>
                                <small class="form-text" style="color: #6c757d;">Şifre en az 6 karakter olmalıdır.</small>
                            </div>
                        </div>

                        <!-- Step 4: SMS Verification -->
                        <div class="form-step">
                            <div class="sms-info">
                                <h5>SMS Doğrulama</h5>
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
                            <button type="button" class="btn btn-secondary btn-sm" id="prevBtn" style="display: none;">
                                ← Geri
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="nextBtn" style="flex: 1;">
                                İleri →
                            </button>
                        </div>

                        <p class="text-center mt-3 mb-0" style="color: #666; font-size: 0.9rem;">
                            Zaten hesabın var mı? 
                            <a href="#" id="switchToLogin" style="color: #3e546a; font-weight: 600; text-decoration: none;">
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
            // Load subscription plans on page load
            loadSubscriptionPlans();

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

            // Plan selection change event
            $('#subscription_plan').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    const planData = selectedOption.data();
                    showPlanInfo(planData);
                } else {
                    hidePlanInfo();
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
                hidePlanInfo();
            }

            // Load subscription plans from backend
            function loadSubscriptionPlans() {
                // Demo data - gerçek implementasyonda AJAX ile çekeceksiniz
                const demoPlans = [
                    {
                        id: 1,
                        name: "Başlangıç",
                        price: "99.00",
                        billing_cycle: "monthly",
                        limits: { users: 3, dealers: 10, stocks: 100, konsinye: 0 },
                        features: { tickets: true, basic_reports: true, inventory: false }
                    },
                    {
                        id: 2,
                        name: "Profesyonel",
                        price: "199.00",
                        billing_cycle: "monthly",
                        limits: { users: 10, dealers: 50, stocks: 500, konsinye: 200 },
                        features: { tickets: true, basic_reports: true, inventory: true }
                    },
                    {
                        id: 3,
                        name: "Kurumsal",
                        price: "399.00",
                        billing_cycle: "monthly",
                        limits: { users: -1, dealers: -1, stocks: -1, konsinye: -1 },
                        features: { tickets: true, basic_reports: true, inventory: true }
                    }
                ];

                const select = $('#subscription_plan');
                select.empty().append('<option value="">Plan Seçiniz...</option>');
                
                demoPlans.forEach(function(plan) {
                    const option = $('<option></option>')
                        .attr('value', plan.id)
                        .text(`${plan.name} - ₺${parseFloat(plan.price).toFixed(2)}/${plan.billing_cycle === 'yearly' ? 'yıl' : 'ay'}`)
                        .data('name', plan.name)
                        .data('price', plan.price)
                        .data('billing_cycle', plan.billing_cycle)
                        .data('users', plan.limits.users)
                        .data('dealers', plan.limits.dealers)
                        .data('stocks', plan.limits.stocks)
                        .data('konsinye', plan.limits.konsinye)
                        .data('tickets', plan.features.tickets)
                        .data('basic_reports', plan.features.basic_reports)
                        .data('inventory', plan.features.inventory);
                    
                    select.append(option);
                });

                // Default olarak Profesyonel planı seç
                setTimeout(() => {
                    select.val('2').trigger('change');
                }, 500);
            }

            // Güncellenmiş showPlanInfo fonksiyonu
            function showPlanInfo(planData) {
                const planInfo = $('#planInfo');
                const featuresDiv = planInfo.find('.plan-features');
                
                // Format functions
                function formatLimitValue(value) {
                    if (value == -1) return 'Sınırsız';
                    if (value == 0) return null; // 0 ise gösterme
                    return value.toLocaleString('tr-TR');
                }

                function formatPrice(price, cycle) {
                    const formattedPrice = parseFloat(price).toLocaleString('tr-TR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    const cycleName = cycle === 'yearly' ? 'yıl' : 'ay';
                    return `₺${formattedPrice}/${cycleName}`;
                }

                // Sadece 0'dan büyük limit değerlerini göster
                let limitItems = [];
                
                const limits = [
                    { key: 'users', label: 'Kullanıcı', value: planData.users },
                    { key: 'dealers', label: 'Bayi', value: planData.dealers },
                    { key: 'stocks', label: 'Stok', value: planData.stocks },
                    { key: 'konsinye', label: 'Konsinye', value: planData.konsinye }
                ];

                limits.forEach(limit => {
                    const formattedValue = formatLimitValue(limit.value);
                    if (formattedValue !== null) { // 0 olanları atla
                        const isUnlimited = limit.value == -1;
                        limitItems.push(`
                            <div class="limit-item">
                                <span class="limit-value ${isUnlimited ? 'unlimited' : ''}">${isUnlimited ? '∞' : limit.value}</span>
                                <span>${limit.label}</span>
                            </div>
                        `);
                    }
                });

                // Feature tags
                let featureTags = [];
                if (planData.tickets === 'true' || planData.tickets === true) {
                    featureTags.push('<span class="feature-tag">Ticket</span>');
                }
                if (planData.basic_reports === 'true' || planData.basic_reports === true) {
                    featureTags.push('<span class="feature-tag">Raporlar</span>');
                }
                if (planData.inventory === 'true' || planData.inventory === true) {
                    featureTags.push('<span class="feature-tag">Stok Yönetimi</span>');
                }

                const compactHTML = `
                    <div class="plan-info-compact">
                        <div class="plan-summary">
                            <h6 class="plan-name-compact">${planData.name} Planı</h6>
                            <div class="plan-price-compact">${formatPrice(planData.price, planData.billing_cycle)}</div>
                        </div>
                        
                        ${limitItems.length > 0 ? `
                            <div class="limits-row">
                                ${limitItems.join('')}
                            </div>
                        ` : ''}
                        
                        ${featureTags.length > 0 ? `
                            <div class="features-compact">
                                ${featureTags.join('')}
                            </div>
                        ` : ''}
                    </div>
                `;
                
                featuresDiv.html(compactHTML);
                planInfo.show();
            }

            // Hide plan information
            function hidePlanInfo() {
                $('#planInfo').hide();
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
                if (stepIndex === 3) {
                    const phoneNumber = $('#tel').val();
                    if (phoneNumber) {
                        $('#phoneDisplay').text('+90 ' + phoneNumber);
                    }
                }
            }

            // Frontend validation
            function validateStepFields(stepIndex) {
                let isValid = true;
                $(steps[stepIndex]).find('input[required], select[required]').each(function() {
                    let value = $(this).val();
                    if (!value || value.trim() === '') {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        
                        // Show error message
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

            // Backend validation (AJAX) - Demo version
            function validateStepOnServer(stepIndex) {
                return new Promise((resolve, reject) => {
                    // Demo validation - gerçekte AJAX call yapacaksınız
                    setTimeout(() => {
                        resolve({ success: true });
                    }, 500);
                });
            }

            // Display errors
            function displayErrors(errors) {
                $('.text-danger').remove();
                $('.form-control, .form-select').removeClass('is-invalid');

                Object.keys(errors).forEach(function(field) {
                    const input = $(`[name="${field}"], #${field}`);
                    if (input.length > 0) {
                        input.addClass('is-invalid');
                        input.after(`<small class="text-danger">${errors[field][0]}</small>`);
                    }
                });
            }

            // Next button click handler
            $('#nextBtn').on('click', async function(e) {
                e.preventDefault();
                
                if (currentStep < 3) {
                    // Frontend validation
                    if (!validateStepFields(currentStep)) {
                        return;
                    }

                    try {
                        // Backend validation
                        await validateStepOnServer(currentStep);
                        
                        // Validation successful, go to next step
                        currentStep++;
                        showStep(currentStep);
                        
                        // If moving to SMS step (step 4), auto-send SMS
                        if (currentStep === 3 && !smsSent) {
                            sendSMS();
                        }
                        
                    } catch (error) {
                        // Backend validation error
                        if (error.errors) {
                            displayErrors(error.errors);
                        } else {
                            console.error('Validation error:', error);
                        }
                    }
                } else if (currentStep === 3) {
                    // SMS step
                    if (!smsSent) {
                        sendSMS();
                    } else {
                        verifySMSAndComplete();
                    }
                }
            });

            function sendSMS() {
                // Demo SMS send
                smsSent = true;
                $('#nextBtn').text('Doğrula ve Kaydı Tamamla');
                $('#countdownTimer').show();
                startCountdown();
                
                // Demo notification
                if (typeof toastr !== 'undefined') {
                    toastr.success('SMS başarıyla gönderildi!');
                }
            }

            // Input change events to clear error messages
            $('input, select').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('.text-danger').remove();
            });

            function verifySMSAndComplete() {
                const smsCode = $('#smsCode').val();
                
                if (!smsCode || smsCode.length !== 6) {
                    $('#smsCodeError').text('Lütfen 6 haneli doğrulama kodunu giriniz.').show();
                    return;
                }

                // Demo verification
                if (typeof toastr !== 'undefined') {
                    toastr.success('Hesabınız başarıyla oluşturuldu!');
                }
                
                setTimeout(function() {
                    alert('Kayıt tamamlandı! Gerçek uygulamada yönlendirme yapılacak.');
                }, 1000);
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
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.warning('SMS doğrulama süresi doldu. Lütfen yeniden deneyin.');
                        }
                    }
                    
                    duration--;
                }, 1000);
            }

            // Previous button
            $('#prevBtn').on('click', function() {
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                    
                    if (currentStep < 3) {
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

            // Demo toastr notification
            setTimeout(() => {
                if (typeof toastr !== 'undefined') {
                    toastr.options.positionClass = "toast-top-right";
                    toastr.options.timeOut = 3000;
                    toastr.info("Demo modunda çalışıyorsunuz. Plan seçimi otomatik olarak yapıldı.");
                }
            }, 1000);
        });
    </script>
</body>
</html>