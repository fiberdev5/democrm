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

        /* Adım göstergeleri için stiller */
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

        /* Form adımlarını gizlemek için */
        .form-step {
            display: none;
            animation: fadeIn 0.5s;
        }
        .form-step.active {
            display: block;
        }

        /* Buton grupları için */
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Doğrulama hatası için */
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .form-step label[for]::after {
            content: " *";
            color: red;
            font-size: 0.8rem;
        }

        /* SMS Kod Input Styling */
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
            color: #999;
            cursor: not-allowed;
            text-decoration: none;
            pointer-events: none;
        }

        .countdown-timer {
            font-size: 0.9rem;
            color: #666;
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
        {{-- Sol tarafta form --}}
        <div class="col-md-6 d-flex align-items-center">
          <form id="multiStepForm" class="w-100">
            @csrf
            <h2 class="mb-4"><label style="color:#f27c22;">Serbis</label>'e Kayıt Olun.</h2>

            <!-- Adım Göstergeleri -->
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
                @error('vergiNo')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
              <div class="mb-3">
                <label for="name" class="form-label">Ad Soyad</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Ad Soyad" required>
                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">E-posta Adresiniz</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="E-posta" required>
                @error('email')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>

            <!-- Adım 2: Firma Bilgileri -->
            <div class="form-step">
              <div class="mb-3">
                <label for="firma_adi" class="form-label">Firma Adı</label>
                <input type="text" name="firma_adi" id="firma_adi" maxlength="50" class="form-control" placeholder="Firma Adı" required>
                <small id="firmaAdiCounter" class="form-text text-muted float-end">0 / 50</small>
                @error('firma_adi')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
              <div class="mb-3">
                <label for="tel" class="form-label">Firma Telefon Numarası</label>
                <input type="text" name="tel" id="tel" class="form-control tel" placeholder="5xx xxx xx xx" required>
                @error('tel')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Şifre" required>
                <small class="form-text text-muted">Şifre en az 6 karakter olmalıdır.</small>
                @error('password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>

            <!-- Adım 3: SMS Doğrulama -->
            <div class="form-step">
                <div class="text-center mb-4">
                    <h5>SMS Doğrulama</h5>
                    <p class="text-muted">Telefonunuza gönderilen 6 haneli kodu giriniz</p>
                    <p class="text-muted" id="phoneDisplay"></p>
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
                    <p class="countdown-timer" id="countdown"></p>
                    <p class="mt-3">
                        Kod gelmedi mi? 
                        <a class="resend-link disabled" id="resendLink">Tekrar Gönder</a>
                    </p>
                </div>
            </div>

            <!-- Navigasyon Butonları -->
            <div class="button-group">
                <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">Geri</button>
                <button type="button" class="btn sbmtButon" id="nextBtn">İleri</button>
            </div>

            <p class="mt-3 text-center">Zaten hesabın var mı? <a href="{{route('giris')}}" style="color: #f27c22;"> Giriş Yap </a> </p>
          </form>
        </div>

        {{-- Sağ tarafta resim --}}
        <div class="col-md-6 d-flex align-items-center justify-content-center">
          <img src="{{ asset('frontend/img/undraw_website_27ju.png') }}" alt="Kayıt Resmi" class="img-fluid" style="max-height: 400px;">
        </div>
      </div>
    </div>

    <script>
    $(document).ready(function () {
        // Telefon numarasını maskele
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
        let countdownInterval = null;
        
        function showStep(stepIndex) {
            steps.removeClass('active');
            $(steps[stepIndex]).addClass('active');

            // Butonların görünürlüğünü ayarla
            if (stepIndex === 0) {
                $('#prevBtn').hide();
            } else {
                $('#prevBtn').show();
            }

            if (stepIndex === steps.length - 1) {
                $('#nextBtn').text('Doğrula ve Kayıt Ol');
                
                // Telefon numarasını göster
                const phone = $('#tel').val();
                $('#phoneDisplay').text('Telefon: ' + phone);
                
                // İlk input'a focus
                setTimeout(() => {
                    $('#code1').focus();
                }, 100);
            } else if (stepIndex === 1) {
                $('#nextBtn').text('Kayıt Ol ve SMS Doğrula');
            } else {
                $('#nextBtn').text('İleri');
            }

            // Adım göstergelerini güncelle
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

        function startCountdown(seconds) {
            // Önceki interval'i temizle
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            let timeLeft = seconds;
            
            // Resend link'i disable et
            $('#resendLink').addClass('disabled');
            
            // İlk değeri hemen göster
            updateCountdownDisplay(timeLeft);
            
            countdownInterval = setInterval(function() {
                timeLeft--;
                updateCountdownDisplay(timeLeft);
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                    $('#countdown').text('Kodun süresi doldu');
                    $('#resendLink').removeClass('disabled');
                }
            }, 1000);
        }
        
        function updateCountdownDisplay(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            $('#countdown').text(`Kalan süre: ${minutes}:${secs.toString().padStart(2, '0')}`);
        }

        // SMS kod inputları için otomatik geçiş
        $('.verification-code-input').on('input', function() {
            const $this = $(this);
            const value = $this.val();
            
            // Sadece rakam kabul et
            if (!/^\d$/.test(value)) {
                $this.val('');
                return;
            }
            
            // Sonraki input'a geç
            if (value.length === 1) {
                const nextInput = $this.next('.verification-code-input');
                if (nextInput.length) {
                    nextInput.focus();
                }
            }
        });

        // Backspace ile geri gitme
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
            
            // Zaten işlem yapılıyorsa çık
            if ($btn.prop('disabled')) {
                return;
            }

            if (currentStep === steps.length - 1) {
                // SMS Doğrulama Adımı
                const code = $('#code1').val() + $('#code2').val() + $('#code3').val() + 
                             $('#code4').val() + $('#code5').val() + $('#code6').val();
                
                if (code.length !== 6) {
                    toastr.error('Lütfen 6 haneli kodu tam olarak giriniz');
                    return;
                }

                // Butonu devre dışı bırak ve loading ekle
                $btn.prop('disabled', true);
                $btn.html('Doğrulanıyor... <div class="loading-spinner"></div>');

                // SMS kodunu doğrula
                $.ajax({
                    url: '{{ route("verify.sms") }}',
                    method: 'POST',
                    data: {
                        code: code,
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            
                            // Countdown'u durdur
                            if (countdownInterval) {
                                clearInterval(countdownInterval);
                                countdownInterval = null;
                            }
                            
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1500);
                        } else {
                            toastr.error(response.message);
                            // Kod inputlarını temizle
                            $('.verification-code-input').val('').removeClass('is-invalid');
                            $('#code1').focus();
                            
                            // Butonu tekrar aktif et
                            $btn.prop('disabled', false);
                            $btn.html('Doğrula ve Kayıt Ol');
                            
                            // Redirect varsa yönlendir
                            if (response.redirect) {
                                setTimeout(function() {
                                    window.location.href = response.redirect;
                                }, 2000);
                            }
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response?.message || 'Bir hata oluştu');
                        
                        // Kod inputlarını temizle ve hata göster
                        $('.verification-code-input').val('').addClass('is-invalid');
                        $('#code1').focus();
                        
                        // Butonu tekrar aktif et
                        $btn.prop('disabled', false);
                        $btn.html('Doğrula ve Kayıt Ol');
                        
                        // Redirect varsa yönlendir
                        if (response?.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 2000);
                        }
                    }
                });
                
                return;
            } else if (currentStep === 1) {
                // Firma bilgileri adımı - SMS gönder
                if (!validateStep(currentStep)) {
                    return;
                }

                // Butonu devre dışı bırak
                $btn.prop('disabled', true);
                $btn.html('SMS Gönderiliyor... <div class="loading-spinner"></div>');

                // Form verilerini topla
                const formData = {
                    subscription_plan: 1, // Default plan
                    vergiNo: $('#vergiNo').val(),
                    name: $('#name').val(),
                    email: $('#email').val(),
                    firma_adi: $('#firma_adi').val(),
                    tel: $('#tel').val(),
                    password: $('#password').val(),
                    _token: $('input[name="_token"]').val()
                };

                // SMS gönder
                $.ajax({
                    url: '{{ route("kayit.action") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            
                            // CSRF token'ı güncelle
                            if (response.csrf_token) {
                                $('input[name="_token"]').val(response.csrf_token);
                            }
                            
                            // Sonraki adıma geç
                            currentStep++;
                            showStep(currentStep);
                            
                            // Countdown'u BURDA başlat - 3. adıma geçtikten SONRA
                            startCountdown(180);
                        } else {
                            toastr.error(response.message);
                        }
                        
                        // Butonu tekrar aktif et
                        $btn.prop('disabled', false);
                        $btn.html('Kayıt Ol ve SMS Doğrula');
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        
                        if (response.errors) {
                            // Validasyon hataları
                            $.each(response.errors, function(field, messages) {
                                toastr.error(messages[0]);
                            });
                        } else {
                            toastr.error(response?.message || 'Bir hata oluştu');
                        }
                        
                        // Butonu tekrar aktif et
                        $btn.prop('disabled', false);
                        $btn.html('Kayıt Ol ve SMS Doğrula');
                    }
                });
                
                return;
            }

            // Diğer adımlar için normal validasyon
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
                
                // Geri sayımı durdur
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                }
            }
        });

        // Kod tekrar gönderme
        $('#resendLink').on('click', function(e) {
            e.preventDefault();
            
            const $link = $(this);
            
            if ($link.hasClass('disabled')) {
                return;
            }
            
            $link.addClass('disabled');
            
            $.ajax({
                url: '{{ route("resend.sms") }}',
                method: 'POST',
                data: {
                    _token: $('input[name="_token"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        
                        // Inputları temizle
                        $('.verification-code-input').val('').removeClass('is-invalid');
                        $('#code1').focus();
                        
                        // Geri sayımı TEKRAR başlat
                        startCountdown(180);
                    } else {
                        toastr.error(response.message);
                        $link.removeClass('disabled');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Bir hata oluştu');
                    $link.removeClass('disabled');
                }
            });
        });

        // Sayfa yüklendiğinde ilk adımı göster
        showStep(currentStep);
    });
    </script>
    
    <!-- Optional JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
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