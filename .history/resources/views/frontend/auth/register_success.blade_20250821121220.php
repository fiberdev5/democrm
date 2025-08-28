<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Başarılı</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('frontend/custom.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
        <div class="row w-100">
            
            {{-- Sol kısım: mesaj --}}
            <div class="col-md-6 d-flex align-items-center">
                <div class="w-100 text-center p-5 shadow rounded" style="background:#fff;">
                    <h1 class="text-success mb-3">🎉 Teşekkürler!</h1>
                    <p class="fs-5">Kayıt işleminiz başarıyla tamamlandı.</p>
                    <p class="text-muted">Artık hesabınızla giriş yapabilir ve sistemimizi kullanmaya başlayabilirsiniz.</p>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="{{ route('giris') }}" class="btn sbmtButon">Giriş Yap</a>
                        <a href="{{ url('/') }}" class="btn btn-secondary">Anasayfa</a>
                    </div>
                </div>
            </div>

            {{-- Sağ kısım: görsel --}}
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <img src="{{ asset('frontend/img/undraw_welcome_cats_thqn.png') }}" alt="Teşekkürler" class="img-fluid" style="max-height:400px;">
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>
</html>
