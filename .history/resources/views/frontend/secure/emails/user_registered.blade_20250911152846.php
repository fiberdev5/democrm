<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesabınız Oluşturuldu</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .package-info { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #17a2b8; }
        .feature-list { list-style: none; padding: 0; }
        .feature-list li { padding: 5px 0; }
        .login-button { background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Hoş Geldiniz!</h2>
            <p>{{ $mailData['tenant']->firma_adi }}</p>
        </div>
        
        <div class="content">
            <h3>Merhaba,</h3>
            <p>Hesabınız başarıyla oluşturuldu. Sisteme giriş yaparak hizmetlerimizden faydalanmaya başlayabilirsiniz.</p>
            
            <div class="package-info">
                <h4>📦 Paket Bilgileriniz</h4>
                
                @if($mailData['isTrialActive'])
                    <p><strong>Paket Türü:</strong> <span style="color: #17a2b8;">Deneme Süresi (Ücretsiz)</span></p>
                    <p><strong>Kalan Süre:</strong> {{ $mailData['trialDaysRemaining'] }} gün</p>
                    <p><strong>Bitiş Tarihi:</strong> {{ $mailData['tenant']->trial_ends_at->format('d.m.Y') }}</p>
                    
                    <h5>Deneme Süresi Özellikleri:</h5>
                    <ul class="feature-list">
                        <li>✓ <strong>Personel Sayısı:</strong> 
                            @if($mailData['tenant']->personelSayisi == -1)
                                Sınırsız
                            @elseif($mailData['tenant']->personelSayisi)
                                {{ $mailData['tenant']->personelSayisi }}
                            @else
                                Belirsiz
                            @endif
                        </li>
                        <li>✓ <strong>Bayi Sayısı:</strong> 
                            @if($mailData['tenant']->bayiSayisi == -1)
                                Sınırsız
                            @elseif($mailData['tenant']->bayiSayisi)
                                {{ $mailData['tenant']->bayiSayisi }}
                            @else
                                Belirsiz
                            @endif
                        </li>
                        <li>✓ <strong>Stok Sayısı:</strong> 
                            @if($mailData['tenant']->stokSayisi == -1)
                                Sınırsız
                            @elseif($mailData['tenant']->stokSayisi)
                                {{ $mailData['tenant']->stokSayisi }}
                            @else
                                Belirsiz
                            @endif
                        </li>
                        <li>✓ <strong>Konsinye Sayısı:</strong> 
                            @if($mailData['tenant']->konsinyeSayisi == -1)
                                Sınırsız
                            @elseif($mailData['tenant']->konsinyeSayisi)
                                {{ $mailData['tenant']->konsinyeSayisi }}
                            @else
                                Belirsiz
                            @endif
                        </li>
                    </ul>
                @else
                    <p><strong>Paket Türü:</strong> Aktif Abonelik</p>
                    <p>Abonelik detaylarınızı sistem üzerinden görüntüleyebilirsiniz.</p>
                @endif
            </div>
            
            <h4>🔐 Giriş Bilgileriniz</h4>
            <p><strong>Kullanıcı Adınız:</strong> {{ $mailData['username'] }}</p>
            <p><strong>Şifreniz:</strong> Kaydolurken girdiğiniz şifredir.</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <a href="http://127.0.0.1:8000/kullanici-girisi" class="login-button">Sisteme Giriş Yap</a>
            </div>
            
            <p><small>Bu e-posta otomatik olarak gönderilmiştir. Herhangi bir sorunuz varsa destek ekibimizle iletişime geçebilirsiniz.</small></p>
        </div>
    </div>
</body>
</html>