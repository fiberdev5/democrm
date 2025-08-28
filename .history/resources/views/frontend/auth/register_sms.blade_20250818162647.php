<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Kaydı</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('frontend/custom.css')}}" rel="stylesheet">
  
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

                <div class="">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>Lütfen <strong> +90 {{ session('phone_number') }}</strong> numaralı telefona gönderilen 6 haneli doğrulama kodunu giriniz.</p>

                    <form method="POST" action="{{ route('sms.verification.verify') }}">
                        @csrf
                        <!-- Adım Göstergeleri -->
            <div class="step-indicator">
                <div class="step " data-step="1">
                    <div class="step-icon" style="background-color: #4caf50">1</div>
                    <div class="step-label">Kişisel</div>
                </div>
                <div class="step-connector"></div>
                <div class="step" data-step="2">
                    <div class="step-icon" style="background-color: #4caf50;">2</div>
                    <div class="step-label">Firma</div>
                </div>
                <div class="step-connector"></div>
                <div class="step active" data-step="3">
                    <div class="step-icon">3</div>
                    <div class="step-label">Sms</div>
                </div>
            </div>
                        <div class="form-group row">
                            <label for="code" class="col-md-4 text-md-right">Doğrulama Kodu</label>

                            <div class="col-md-6">
                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" required autocomplete="one-time-code">

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <button type="button" class="btn sbmtButon" id="nextBtn">Doğrula ve Kaydı Tamamla</button>
                    </form>
                    
                    {{-- İsteğe bağlı: Kodu yeniden gönderme linki eklenebilir --}}
                    {{-- <div class="mt-3 text-center">
                        <a href="#">Kodu yeniden gönder</a>
                    </div> --}}
                </div>
            
        </div>

        {{-- Sağ tarafta resim --}}
        <div class="col-md-6 d-flex align-items-center justify-content-center">
          <img src="{{ asset('frontend/img/undraw_website_27ju.png') }}" alt="Kayıt Resmi" class="img-fluid" style="max-height: 400px;">
        </div>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
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
    </script>
</body>
</html>