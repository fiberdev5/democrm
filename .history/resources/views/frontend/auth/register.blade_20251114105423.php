<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Kaydı</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('frontend/custom.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <style>
        .register-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        form {
            max-width: 400px;
            width: 100%;
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .sbmtButon{background-color: #f27c22;color: #fff;}
        .sbmtButon:hover{background-color: #f58733;color: #fff;}
        a{text-decoration: none;}

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .step-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e0e0e0;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .step.active .step-icon {
            background-color: #f27c22;
        }
        .step.finish .step-icon {
            background-color: #4caf50;
        }
        .step-label {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #888;
        }
        .step.active .step-label {
            color: #f27c22;
            font-weight: bold;
        }
        .step-connector {
            flex-grow: 1;
            height: 2px;
            background-color: #e0e0e0;
            margin: 0 -20px 25px -20px;
            align-self: center;
        }

        .form-step {
            display: none;
            animation: fadeIn 0.5s;
        }
        .form-step.active {
            display: block;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .form-step label[for]::after {
            content: " *";
            color: red;
            font-size: 0.8rem;
        }

        .verification-code-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        .verification-code-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s;
        }
        .verification-code-input:focus {
            border-color: #f27c22;
            outline: none;
        }
        .verification-code-input.is-invalid {
            border-color: #dc3545;
        }

        .resend-link {
            color: #f27c22;
            cursor: pointer;
            text-decoration: underline;
        }
        .resend-link:hover {
            color: #f58733;
        }
        .resend-link.disabled {
            color: #999 !important;
            cursor: not-allowed !important;
            text-decoration: none !important;
            pointer-events: none !important;
        }

        .countdown-timer {
            font-size: 0.9rem;
            color: #666;
            font-weight: bold;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #f27c22;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>  
    <div class="container d-flex align-items-center justify-content-center register-container" style="min-height: 100vh;">
      <div class="row w-100">
        <div class="col-md-6 d-flex align-items-center">
          <form id="multiStepForm" class="w-100">
            @csrf
            <h2 class="mb-4"><label style="color:#f27c22;">Serbis</label>'e Kayıt Olun.</h2>

            <div class="step-indicator">
                <div class="step active" data-step="1">
                    <div class="step-icon">1</div>
                    <div class="step-label">Kişisel</div>
                </div>
                <div class="step-connector"></div>
                <div class="step" data-step="2">
                    <div class="step-icon">2</div>
                    <div class="step-label">Firma</div>
                </div>
                <div class="step-connector"></div>
                <div class="step" data-step="3">
                    <div class="step-icon">3</div>
                    <div class="step-label">SMS Doğrulama</div>
                </div>
            </div>

            <!-- Adım 1: Kişisel Bilgiler -->
            <div class="form-step active">
              <div class="mb-3">
                <label for="vergiNo" class="form-label">Vergi Numarası </label>
                <input type="text" name="vergiNo" id="vergiNo" class="form-control vergiNo" placeholder="Vergi Numarası" required>
              </div>
              <div class="mb-3">
                <label for="name" class="form-label">Ad Soyad</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Ad Soyad" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">E-posta Adresiniz</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="E-posta" required>
              </div>
            </div>

            <!-- Adım 2: Firma Bilgileri -->
            <div class="form-step">
              <div class="mb-3">
                <label for="firma_adi" class="form-label">Firma Adı</label>
                <input type="text" name="firma_adi" id="firma_adi" maxlength="50" class="form-control" placeholder="Firma Adı" required>
                <small id="firmaAdiCounter" class="form-text text-muted float-end">0 / 50</small>
              </div>
              <div class="mb-3">
                <label for="tel" class="form-label">Firma Telefon Numarası</label>
                <input type="text" name="tel" id="tel" class="form-control tel" placeholder="5xx xxx xx xx" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Şifre" required>
                <small class="form-text text-muted">Şifre en az 6 karakter olmalıdır.</small>
              </div>
            </div>

            <!-- Adım 3: SMS Doğrulama -->
            <div class="form-step">
                <div class="text-center mb-4">
                    <h5>SMS Doğrulama</h5>
                    <p class="text-muted">Telefonunuza gönderilen 6 haneli kodu giriniz</p>
                    <p class="text-muted fw-bold" id="phoneDisplay"></p>
                </div>

                <div class="verification-code-container">
                    <input type="text" maxlength="1" class="form-control verification-code-input" id="code1" autocomplete="off">
                    <input type="text" maxlength="1" class="form-control verification-code-input" id="code2" autocomplete="off">
                    <input type="text" maxlength="1" class="form-control verification-code-input" id="code3" autocomplete="off">
                    <input type="text" maxlength="1" class="form-control verification-code-input" id="code4" autocomplete="off">
                    <input type="text" maxlength="1" class="form-control verification-code-input" id="code5" autocomplete="off">
                    <input type="text" maxlength="1" class="form-control verification-code-input" id="code6" autocomplete="off">
                </div>

                <div class="text-center">
                    <p class="countdown-timer" id="countdown">Kalan süre: 3:00</p>
                    <p class="mt-3">
                        Kod gelmedi mi? 
                        <a class="resend-link disabled" id="resendLink">Tekrar Gönder</a>
                    </p>
                </div>
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">Geri</button>
                <button type="button" class="btn sbmtButon" id="nextBtn">İleri</button>
            </div>

            <p class="mt-3 text-center">Zaten hesabın var mı? <a href="{{route('giris')}}" style="color: #f27c22;"> Giriş Yap </a> </p>
          </form>
        </div>

        <div class="col-md-6 d-flex align-items-center justify-content-center">
          <img src="{{ asset('frontend/img/undraw_website_27ju.png') }}" alt="Kayıt Resmi" class="img-fluid" style="max-height: 400px;">
        </div>
      </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
$(document).ready(function () {
    $(".tel").mask("999 999 99 99");
    $(".vergiNo").mask("0000000000");

    $('#firma_adi').on('keyup', function() {
        var currentLength = $(this).val().length;
        var maxLength = $(this).attr('maxlength');
        var counter = $('#firmaAdiCounter');
        counter.text(currentLength + " / " + maxLength);
        if (currentLength >= maxLength) {
            counter.removeClass('text-muted').addClass('text-danger');
        } else {
            counter.removeClass('text-danger').addClass('text-muted');
        }
    });

    let currentStep = 0;
    const steps = $(".form-step");
    let countdownTimer = null;
    
    function showStep(stepIndex) {
        steps.removeClass('active');
        $(steps[stepIndex]).addClass('active');

        if (stepIndex === 0) {
            $('#prevBtn').hide();
        } else {
            $('#prevBtn').show();
        }

        if (stepIndex === 2) {
            $('#nextBtn').text('Doğrula ve Kayıt Ol');
            const phone = $('#tel').val();
            $('#phoneDisplay').text('Telefon: ' + phone);
            setTimeout(() => $('#code1').focus(), 100);
        } else if (stepIndex === 1) {
            $('#nextBtn').text('SMS Gönder');
        } else {
            $('#nextBtn').text('İleri');
        }

        $('.step').removeClass('active finish');
        $('.step').each(function(index) {
            if (index < stepIndex) {
                $(this).addClass('finish');
            } else if (index === stepIndex) {
                $(this).addClass('active');
            }
        });
    }

    function validateStep(stepIndex) {
        let isValid = true;
        $(steps[stepIndex]).find('input[required]').each(function() {
            if ($(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return isValid;
    }

    function startTimer() {
        if (countdownTimer) {
            clearInterval(countdownTimer);
        }
        
        let timeLeft = 180;
        $('#resendLink').addClass('disabled');
        
        countdownTimer = setInterval(function() {
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            $('#countdown').text('Kalan süre: ' + minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
            
            timeLeft--;
            
            if (timeLeft < 0) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                $('#countdown').text('Süre doldu!');
                $('#resendLink').removeClass('disabled');
            }
        }, 1000);
    }

    $('.verification-code-input').on('input', function() {
        const $this = $(this);
        const value = $this.val();
        
        if (!/^\d$/.test(value)) {
            $this.val('');
            return;
        }
        
        if (value.length === 1) {
            const nextInput = $this.next('.verification-code-input');
            if (nextInput.length) {
                nextInput.focus();
            }
        }
    });

    $('.verification-code-input').on('keydown', function(e) {
        if (e.key === 'Backspace' && $(this).val() === '') {
            const prevInput = $(this).prev('.verification-code-input');
            if (prevInput.length) {
                prevInput.focus();
            }
        }
    });

    $('#nextBtn').on('click', function(e) {
        e.preventDefault();
        
        const $btn = $(this);
        
        if ($btn.prop('disabled')) {
            return;
        }

        // SMS Doğrulama adımı
        if (currentStep === 2) {
            const code = $('#code1').val() + $('#code2').val() + $('#code3').val() + 
                         $('#code4').val() + $('#code5').val() + $('#code6').val();
            
            if (code.length !== 6) {
                toastr.error('Lütfen 6 haneli kodu giriniz');
                return;
            }

            $btn.prop('disabled', true);
            $btn.html('Doğrulanıyor... <div class="loading-spinner"></div>');

            $.ajax({
                url: '{{ route("verify.sms") }}',
                method: 'POST',
                data: {
                    code: code,
                    _token: $('input[name="_token"]').val()
                },
                success: function(response) {
                    // Bu kısım sadece backend 200 OK ve {success: true} döndürdüğünde çalışır
                    if (response.success) {
                        toastr.success(response.message);
                        if (countdownTimer) clearInterval(countdownTimer);
                        setTimeout(() => window.location.href = response.redirect, 1500);
                    } else {
                        // Backend 200 OK ama {success: false} döndüğünde (nadiren olur, ama olabilir)
                        // Örneğin bazı doğrulama hatalarını 200 ile döndüren backendler olabilir.
                        // Sizin PHP'niz 400 ile döndüğü için bu blok genellikle tetiklenmez.
                        toastr.error(response.message);
                        $('.verification-code-input').val('');
                        $('#code1').focus();
                        $btn.prop('disabled', false);
                        $btn.html('Doğrula ve Kayıt Ol');
                        if (response.redirect) {
                            setTimeout(() => window.location.href = response.redirect, 2000);
                        }
                    }
                },
                error: function(xhr) {
                    // Bu kısım backend 4xx veya 5xx HTTP durum kodu döndürdüğünde çalışır.
                    // Sizin PHP kodunuz 400 döndürdüğü için hatalı kodda burası tetiklenir.
                    let errorMessage = 'Bilinmeyen bir hata oluştu.';
                    let redirectUrl = null;

                    if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || errorMessage;
                        redirectUrl = xhr.responseJSON.redirect || null;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                            redirectUrl = response.redirect || null;
                        } catch (e) {
                            console.error('JSON parse hatası:', e);
                        }
                    }
                    
                    console.log('Hata Yanıtı:', xhr.responseJSON); // Debug için
                    toastr.error(errorMessage);
                    
                    // Doğrulama kodu alanlarını temizle ve ilk alana odaklan
                    $('.verification-code-input').val('');
                    $('#code1').focus();
                    
                    // Butonu eski haline getir
                    $btn.prop('disabled', false);
                    $btn.html('Doğrula ve Kayıt Ol');
                    
                    // Eğer bir yönlendirme URL'si varsa, yönlendir
                    if (redirectUrl) {
                        setTimeout(() => window.location.href = redirectUrl, 2000);
                    }
                }
            });
            return;
        }

        // SMS gönderme adımı
        if (currentStep === 1) {
            if (!validateStep(currentStep)) {
                return;
            }

            $btn.prop('disabled', true);
            $btn.html('SMS Gönderiliyor... <div class="loading-spinner"></div>');

            const formData = {
                subscription_plan: 1,
                vergiNo: $('#vergiNo').val(),
                name: $('#name').val(),
                email: $('#email').val(),
                firma_adi: $('#firma_adi').val(),
                tel: $('#tel').val(),
                password: $('#password').val(),
                _token: $('input[name="_token"]').val()
            };

            $.ajax({
                url: '{{ route("kayit.action") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        if (response.csrf_token) {
                            $('input[name="_token"]').val(response.csrf_token);
                        }
                        
                        currentStep++;
                        showStep(currentStep);
                        startTimer();
                    } else {
                        toastr.error(response.message);
                    }
                    $btn.prop('disabled', false);
                    $btn.html('SMS Gönder');
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        $.each(response.errors, function(field, messages) {
                            toastr.error(messages[0]);
                        });
                    } else if (response && response.message) {
                        toastr.error(response.message);
                    } else {
                        toastr.error('Bir hata oluştu');
                    }
                    $btn.prop('disabled', false);
                    $btn.html('SMS Gönder');
                }
            });
            return;
        }

        // Normal adım geçişi
        if (validateStep(currentStep)) {
            currentStep++;
            if (currentStep < steps.length) {
                showStep(currentStep);
            }
        }
    });

    $('#prevBtn').on('click', function() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
            if (countdownTimer) {
                clearInterval(countdownTimer);
                countdownTimer = null;
            }
        }
    });

    // Tekrar gönder
    $('#resendLink').on('click', function(e) {
        e.preventDefault();
        
        if ($(this).hasClass('disabled')) {
            return;
        }
        
        $.ajax({
            url: '{{ route("resend.sms") }}',
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('.verification-code-input').val('');
                    $('#code1').focus();
                    startTimer();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Bir hata oluştu';
                toastr.error(errorMessage);
            }
        });
    });

    showStep(currentStep);
});
</script>


        @if(session('message'))
            <script>
                var type = "{{ session('alert-type') }}";
                toastr.options.positionClass = "toast-top-right";
                switch(type){
                    case 'info':
                        toastr.info("{{ session('message') }}");
                        break;
                    case 'success':
                        toastr.success("{{ session('message') }}");
                        break;
                    case 'warning':
                        toastr.warning("{{ session('message') }}");
                        break;
                    case 'error':
                        toastr.error("{{ session('message') }}");
                        break;
                }
            </script>
    @endif
</body>
</html>