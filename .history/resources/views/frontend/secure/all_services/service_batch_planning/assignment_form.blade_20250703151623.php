{{-- resources/views/frontend/secure/all_services/service_batch_planning/assignment_form.blade.php --}}

<form method="post" id="topluPlanKaydet" action="{{route('service.assign', $tenant_id)}}" style="padding:0 10px;">
    @csrf {{-- Laravel CSRF koruması --}}

    @foreach ($questions as $question)
        <div class="row form-group">
            <div class="col-lg-4"><label>{{ $question->soru }}</label></div>
            <div class="col-lg-8">
                @if ($question->cevapTuru == "[Aciklama]")
                    <input type="text" name="soru{{ $question->id }}" class="form-control" />
                @elseif (Str::contains($question->cevapTuru, "Grup")) {{-- Str::contains kullanıldı --}}
                    <select class="form-control" name="soru{{ $question->id }}">
                        <option value="">-Seçiniz-</option>
                        @php
                            $filteredPersonnel = collect();
                            if ($question->cevapTuru == "Grup-0") {
                                $filteredPersonnel = $personnel; // Tüm yetkili personeller
                            } else {
                                $filteredPersonnel = App\Models\User::where('tenant_id', $tenant_id)
                                ->where('status', '1')
                                ->whereHas('roles', function($query) {
                                    $query->whereIn('name', ['Teknisyen', 'Teknisyen Yardımcısı', 'Atölye Çırak', 'Atölye Ustası']);
                                })
                                ->with('roles') // roles ilişkisini önceden yükle
                                ->orderBy('name', 'asc')
                                ->get();
                            }
                        @endphp
                        @php
                            $filteredPersonnel = App\Models\User::where('tenant_id', $tenant_id)
                                ->where('status', '1')
                                ->whereHas('roles', function($query) {
                                    $query->whereIn('name', ['Teknisyen', 'Teknisyen Yardımcısı', 'Atölye Çırak', 'Atölye Ustası']);
                                })
                                ->with('roles') // roles ilişkisini önceden yükle
                                ->orderBy('name', 'asc')
                                ->get();
                        @endphp

                        @foreach ($filteredPersonnel as $person)
                            <option value="{{ $person->user_id }}">{{ $person->name }}</option>
                        @endforeach
                    </select>
                @elseif ($question->cevapTuru == "[Tarih]")
                    <input type="date" name="soru{{ $question->id }}" class="form-control datepicker" value="{{ $defaultDateFormatted }}" style="background:#fff;">
                @elseif ($question->cevapTuru == "[Saat]")
                    <select class="form-control" name="soru{{ $question->id }}">
                        <option value="">-Seçiniz-</option>
                        <option value="08:00-10:00">08:00-10:00</option>
                        <option value="09:00-11:00">09:00-11:00</option>
                        <option value="10:00-12:00">10:00-12:00</option>
                        <option value="11:00-13:00">11:00-13:00</option>
                        <option value="12:00-14:00">12:00-14:00</option>
                        <option value="13:00-15:00">13:00-15:00</option>
                        <option value="14:00-16:00">14:00-16:00</option>
                        <option value="15:00-17:00">15:00-17:00</option>
                        <option value="16:00-18:00">16:00-18:00</option>
                        <option value="17:00-19:00">17:00-19:00</option>
                        <option value="18:00-20:00">18:00-20:00</option>
                        <option value="19:00-21:00">19:00-21:00</option>
                        <option value="20:00-22:00">20:00-22:00</option>
                        <option value="21:00-23:00">21:00-23:00</option>
                    </select>
                @elseif ($question->cevapTuru == "[Arac]")
                    <select class="form-control" name="soru{{ $question->id }}">
                        <option value="">-Seçiniz-</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->arac }}</option>
                        @endforeach
                    </select>
                @elseif ($question->cevapTuru == "[Parca]")
                    <span style="font-size: 12px; color: red; line-height: initial; display: block;">Parçalar bu modülde sorulamamaktır. Parçalar Cihaz ve Modele göre değişiklik gösterir.</span>
                @elseif ($question->cevapTuru == "[Fiyat]")
                    <input type="number" name="soru{{ $question->id }}" class="form-control" />
                @elseif ($question->cevapTuru == "[Teklif]")
                    <input type="number" name="soru{{ $question->id }}" class="form-control" />
                @elseif ($question->cevapTuru == "[Bayi]")
                    <select class="form-control" name="soru{{ $question->id }}">
                        <option value="">-Seçiniz-</option>
                        @foreach ($dealers as $dealer)
                            <option value="{{ $dealer->user_id }}">{{ $dealer->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
    @endforeach

    <div class="row">
        <div class="col-lg-12" style="text-align: center;margin-top: 2px;">
            <input type="hidden" name="servisidler" class="servisidler" value="{{ $servisIds }}"/>
            <input type="hidden" name="gelenIslem" value="{{ $gelenDurum }}"/>
            <input type="hidden" name="gidenIslem" value="{{ $gidenDurum }}"/>
            <input type="submit" class="btn btn-primary btn-sm" value="Kaydet"/>
        </div>
    </div>
</form>

<script type="text/javascript">
 // jQuery yüklendiğinden emin olun
$(document).ready(function() {
    // Datepicker'ı başlat
    $('.datepicker').datepicker({
        language: 'tr', // Türkçe dil paketi kullandığınızdan emin olun
        autoclose: true,
        format: 'dd/mm/yyyy' // Laravel'deki Carbon::format('d/m/Y') ile uyumlu
    });

    $("#topluPlanKaydet").on('submit', function(e) {
        e.preventDefault(); // Formun varsayılan gönderme davranışını engelle

        var $form = $(this); // Form elementini önbelleğe al
        var $submitButton = $form.find('input[type="submit"]');

        // Birden fazla göndermeyi önlemek için gönder düğmesini devre dışı bırak
        $submitButton.prop('disabled', true).val('Kaydediliyor...'); // Buton metnini değiştir

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: new FormData(this),
            contentType: false, // FormData için gerekli
            processData: false, // FormData için gerekli

            success: function(response) {
                // Sunucudan gelen yanıtı kontrol et
                if (response.success) {
                    alert(response.success); // Başarı mesajını göster

                    // Modalı kapat
                    $('#servisPersonelAtamaModal').modal('hide');

                    // Modal içeriğini temizle (isteğe bağlı, yeniden kullanım için iyi bir pratik)
                    $('#servisPersonelAtamaModal .modal-body').html("");

                    // Arka plandaki listeyi yenilemek için tetikleyiciyi çalıştır.
                    // ".servisPlanListele" seçicisinin listenizi yeniden yükleyen doğru element olduğundan emin olun.

                } else if (response.error) {
                    alert('Hata: ' + response.error); // Sunucudan gelen hata mesajını göster
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Hatası:', xhr.responseText); // Hata yanıtını konsola yazdır

                try {
                    // Eğer hata yanıtı JSON ise, daha spesifik bir mesaj göster
                    var errorResponse = JSON.parse(xhr.responseText);
                    alert('Bir hata oluştu: ' + (errorResponse.error || 'Bilinmeyen bir hata.'));
                } catch (e) {
                    // JSON değilse genel bir hata mesajı göster
                    alert('Bir hata oluştu. Lütfen konsolu kontrol edin.');
                }
            },
            complete: function() {
                // İşlem tamamlandıktan sonra gönder düğmesini tekrar etkinleştir ve orijinal metnini geri yükle
                $submitButton.prop('disabled', false).val('Kaydet');
            }
        });
    });
});
</script>