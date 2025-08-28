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

        /* --- YENİ EKLENEN STİLLER --- */

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
            margin: 0 -20px 25px -20px; /* İkonların ortasına hizalamak için */
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
    </style>
</head>
<body>  
    <div class="container d-flex align-items-center justify-content-center register-container" style="min-height: 100vh;">
      <div class="row w-100">
        {{-- Sol tarafta form --}}
        <div class="col-md-6 d-flex align-items-center">
          <form id="multiStepForm" class="w-100" method="POST" action="{{ route('kayit.action') }}">
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
                    <div class="step-label">Sms</div>
                </div>
            </div>

            <!-- Adım 1: Kişisel Bilgiler -->
            <div class="form-step active">
              <div class="mb-3">
                <label for="vergiNo" class="form-label">Vergi Numarası</label>
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
                <input type="text" name="firma_adi" id="firma_adi" maxlength="30" class="form-control" placeholder="Firma Adı" required>
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

        // Opsiyonel: Limite yaklaştığında rengi değiştir
        if (currentLength >= maxLength) {
            counter.removeClass('text-muted').addClass('text-danger');
        } else {
            counter.removeClass('text-danger').addClass('text-muted');
        }
    });

        let currentStep = 0;
        const steps = $(".form-step");
        
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
                $('#nextBtn').text('Kayıt Ol ve SMS Doğrula');
                $('#nextBtn').attr('type', 'submit'); // Son adımda butonu submit yap
            } else {
                $('#nextBtn').text('İleri');
                $('#nextBtn').attr('type', 'button'); // Diğer adımlarda normal buton
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

        $('#nextBtn').on('click', function(e) {
            if ($(this).attr('type') === 'submit') {
                if (!validateStep(currentStep)) {
                    e.preventDefault(); // Form geçersizse göndermeyi engelle
                }
                // Form geçerliyse normal şekilde submit olacak
                return;
            }

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
            }
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