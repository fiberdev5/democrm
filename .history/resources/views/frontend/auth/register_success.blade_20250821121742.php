<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('frontend/custom.css')}}" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
    <style>
        body {
            background-color: #f0f0f0; /* Gri arka plan */
        }

        .container {
            max-width: 400px;
            margin-top: 50px;
        }

        .login-box {
            background-color: #ffffff; /* Beyaz kutu arka planı */
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #ddd; /* Kutu çevresine ince bir çizgi */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Hafif gölge */
            text-align: center;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 50px; /* Form ile logo arasında boşluk */
        }

        .logo-container img {
            width: 150px; /* Logonun genişliği */
            height: auto; /* Oranlı yüksekliği */
        }

        h5 {
            font-size: 15px;
            color: #8f8383;
        }
        .sbmtButon{background-color: #f27c22;color: #fff;}
        .sbmtButon:hover{background-color: #f58733;color: #fff;}
        a{text-decoration: none;}

         .input-wrapper {
        display: flex;
        align-items: center;
        position: relative; /* Konumlandırma için gereklidir */
    }

    .info-icon {
        margin-left: 10px;
        cursor: pointer;
        position: relative;
        display: inline-block;
    }

    /* Bilgi balonu (tooltip) */
    .info-icon .tooltip-text {
        visibility: hidden; /* Başlangıçta gizli */
        width: 220px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px;
        position: absolute;
        z-index: 1;
        bottom: 125%; /* İkonun üstünde konumlandır */
        left: 50%;
        margin-left: -110px; /* Genişliğin yarısı kadar sola çekerek ortala */
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    /* Balonun altındaki ok */
    .info-icon .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    /* İkonun üzerine gelindiğinde balonu görünür yap */
    .info-icon:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
    </style>
</head>
<body>
    <div class="container">
        <!-- Firma Logosu -->
        <div class="logo-container">
            <img src="{{ asset('frontend/img/serbis-logo.png') }}" alt="Firma Logosu">
        </div>

        <div class="">


            <div class="container d-flex flex-column align-items-center justify-content-center" style="min-height:70vh;">
                <div class="card shadow-lg p-5 text-center" style="border-radius: 1rem;">
                    <h1 class="text-success mb-4">🎉 Teşekkürler!</h1>
                    <p class="fs-5">Kayıt işleminiz başarıyla tamamlandı.</p>
                    <p class="text-muted">Artık sistemimizi kullanmaya başlayabilirsiniz.</p>
                    
                    <a href="{{ url('/') }}" class="btn btn-gradient mt-4">Anasayfaya Dön</a>
                </div>
            </div>


        </div>
    </div>

    <!-- Bootstrap JS -->
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
