{{-- resources/views/servis/show.blade.php --}}

<div class="servisModal teknisyenServisModal">
    <form method="POST" id="servisDuzenle" action="">
        @csrf
        @method('PUT')
        
        <div class="card" style="margin-bottom: 3px">
            <div class="card-header ch1" style="padding: 3px 5px!important;">
                <div class="row">
                    <div class="col-sm-12" style="text-align: left;">
                        <label style="text-align: left;width: auto;display: inline-block;margin: 0;">
                            Servis Kaynağı: 
                            <span style="background: #ec0000;border: 1px solid #ce0000;color: #fff;padding: 0px 5px;border-radius: 3px;margin-left: 5px;max-width: 215px">
                                {{ $servis->skaynak->kaynak ?? 'Belirtilmemiş' }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row1">
            <div class="col-sm-6 c1">
                <div class="card">
                    <div class="card-header">MÜŞTERİ BİLGİSİ</div>
                    <div class="card-body">
                        <span class="musName">
                            {{ $servis->musteri->adSoyad }}
                            @if($servis->musteri->musteriTipi == 1)
                                (BİREYSEL)
                            @elseif($servis->musteri->musteriTipi == 2)
                                (KURUMSAL)
                            @endif
                        </span>
                        
                        <span>
                            <a href="tel:0{{ $servis->musteri->tel1 }}" style="color:red">{{ $servis->musteri->tel1 }}</a>
                            @if($servis->musteri->tel2)
                                - <a href="tel:0{{ $servis->musteri->tel2 }}" style="color:red">{{ $servis->musteri->tel2 }}</a>
                            @endif
                        </span>
                        
                        <span>
                            <a href="https://www.google.com/maps?daddr={{ $servis->musteri->adres }} {{ $servis->musteri->ilce }}/{{ $servis->musteri->il }}" style="color:red">
                                {{ $servis->musteri->adres }} {{ $servis->musteri->state->ilceName }}/{{ $servis->musteri->country->name }}
                            </a>
                        </span>
                        
                        @if($servis->musteri->musteriTipi == 1 && $servis->musteri->tcNo)
                            <span>T.C. {{ $servis->musteri->tcNo }}</span>
                        @elseif($servis->musteri->musteriTipi == 2 && $servis->musteri->vergiNo)
                            <span>Vergi No: {{ $servis->musteri->vergiNo }}-{{ $servis->musteri->vergiDairesi }}</span>
                        @endif
                        
                        <span>
                            <label>Müsait Olma Zamanı: </label>
                            {{ $musaitTarih[2] }}/{{ $musaitTarih[1] }}/{{ $musaitTarih[0] }} {{ $servis->musaitSaat1 }}-{{ $servis->musaitSaat2 }}
                        </span>
                        
                        
                    </div>
                </div>
            </div>

            <div class="col-sm-6 c2">
                <div class="card">
                    <div class="card-header">CİHAZ BİLGİSİ</div>
                    <div class="card-body">
                        <span class="cihazName">
                            {{ strtoupper($servis->markaCihaz->marka . ' - ' . $servis->turCihaz->cihaz . ' - ' . $servis->cihazAriza) }}
                        </span>
                        
                        <span>
                            <label>Operatör Notu: </label>
                            {{ $servis->opNot }}
                        </span>
                        
                        <span>
                            <label>Garanti Süresi: </label>
                            @if($servis->garantiSuresi && $garantiBitis)
                                {{ \Carbon\Carbon::parse($garantiBitis)->format('d/m/Y') }} ({{ $kalanGun }} Gün Kaldı)
                            @else
                                Garanti Yok
                            @endif
                        </span>
                        
                        <span style="margin:0">
                            <label>Cihaz Modeli: </label>
                            <input type="text" name="cihazModel" class="form-control cihazModel" 
                                   value="{{ $servis->cihazModel }}" 
                                   style="display:inline-block;width:calc(100% - 147px);padding:3px 5px!important;">
                            <button type="button" class="btn btn-primary btn-sm servisGuncelleBtn" 
                                    style="font-size:12px;padding:2px 5px;position:relative;top:-3px;left:3px;">
                                Kaydet
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="acil" class="acil" value="{{ $servis->acil }}"/>
        <input type="hidden" name="markaid" class="markaid" value="{{ $servis->cihazMarka }}"/>
        <input type="hidden" name="servisid" class="servisid" value="{{ $servis->id }}"/>
    </form>

    <div class="servisAsamalari">
        @if($kalanGun >= 0)
            @if(!$eskiIslemler2 || $eskiIslemler2->pid != auth()->user()->id)
                <div class="card" style="margin-top: 5px;">
                    <div class="card-header ch1" style="padding: 3px 5px;">
                        <div class="row">
                            <div class="col-md-6 col1">
                                <label class="servisAcilLabel servisAcilBtn">
                                    <span>Acil</span>
                                    <input type="checkbox" {{ $servis->acil ? 'checked' : '' }} style="display: none;">
                                    <div class="checkmark"><i class="fas fa-check"></i></div>
                                </label>
                                <input type="hidden" class="servisDurum" value="{{ $servis->servisDurum }}">
                            </div>
                            <div class="col-md-6 col2">
                                <label style="margin: 0">Yapılacak İşlemi Seçiniz: </label>
                                <select class="form-control altAsamalar" name="altAsamalar" 
                                        style="display: inline-block;width: 169px;margin-left: 2px">
                                    <option value="">Seçiniz</option>
                                    @foreach($altAsamalar as $asama)
                                        @if($asama->id != 244 && $asama->id != 247)
                                            <option value="{{ $asama->id }}">{{ $asama->asama }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body altSecenekler" style="padding: 0"></div>
                </div>
            @endif
        @endif

        <div class="card" style="margin-top: 5px;">
            <div class="card-body" style="padding: 0">
                <div id="no-more-tables">
                    <div class="table-responsive" style="margin: 0">
                        <table class="table table-hover table-striped servisAsamaTable" width="100%" cellspacing="0" style="margin: 0">
                            <thead class="title">
                                <tr>
                                    <th style="padding: 5px 10px;font-size: 12px;">Tarih</th>
                                    <th style="padding: 5px 10px;font-size: 12px;">İşlemi Yapan</th>
                                    <th style="padding: 5px 10px;font-size: 12px;">İşlem Adı</th>
                                    <th style="padding: 5px 10px;font-size: 12px;">Açıklama</th>
                                    @if($kalanGun >= 0)
                                        <th style="padding: 5px 10px;font-size: 12px;"></th>
                                        <th style="padding: 5px 10px;font-size: 12px;"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Acil işlem satırı --}}
                                @if($servis->acil && $acilIslem)
                                    <tr style="background: #ffc107;">
                                        <td class="kayitTarihiCS" style="vertical-align: middle;width: 100px; font-size: 11px; padding: 5px;">
                                            {{ $acilIslem->tarih->format('d/m/Y') }}
                                        </td>
                                        <td style="vertical-align: middle;font-size: 11px; padding: 5px;">
                                            {{ $acilIslem->personel->adsoyad }}
                                        </td>
                                        <td style="vertical-align: middle;font-size: 11px; padding: 5px;">
                                            <strong>Servis Acil Aşamasındadır.</strong>
                                        </td>
                                        <td style="vertical-align: middle;font-size: 11px; padding: 5px;" colspan="3">
                                            Servis işlemi bittiğinde acil işaretini kaldırın.
                                        </td>
                                    </tr>
                                @endif

                                {{-- Servis notları --}}
                                @foreach($servisNotlari as $not)
                                    <tr>
                                        <td class="kayitTarihiCS" style="vertical-align: middle;width: 100px; font-size: 11px; padding: 5px;">
                                            {{ $not->kayitTarihi->format('d/m/Y') }}<br>
                                            {{ $not->kayitTarihi->format('H:i') }}
                                        </td>
                                        <td style="vertical-align: middle;font-size: 11px; padding: 5px;">
                                            {{ $not->personel->adsoyad }}
                                        </td>
                                        <td style="vertical-align: middle;font-size: 11px; padding: 5px;color:#ec0000;">
                                            <strong>Operatör Notu</strong>
                                        </td>
                                        <td style="vertical-align: middle;font-size: 11px; padding: 5px;" colspan="3">
                                            <strong>{{ $not->aciklama }}</strong>
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Eski işlemler --}}
                                @foreach($eskiIslemler as $eskiIslem)
                                    <tr>
                                        <td class="kayitTarihiCS" style="vertical-align: middle;width: 100px; font-size: 11px; padding: 0 5px;">
                                            {{ $eskiIslem->created_at->format('d/m/Y') }}<br>
                                            {{ $eskiIslem->created_at->format('H:i') }}
                                        </td>
                                        <td style="vertical-align: middle;font-size: 11px; padding: 0 5px;">
                                            {{ $eskiIslem->user->name }}
                                        </td>
                                        <td class="islemAsamaCS" style="vertical-align: middle;font-size: 11px; padding: 0 5px;">
                                            <strong>{{ $eskiIslem->asama->asama ?? 'Bilinmeyen Aşama' }}</strong>
                                        </td>
                                        <td class="islemAciklamaCS" style="vertical-align: middle;font-size: 11px;padding: 0 5px;width: 300px;text-transform: capitalize;">
                                            {{-- Burada servis_asama_cevaplari tablosundan gelen cevaplar işlenecek --}}
                                            {{-- Bu kısım daha karmaşık olduğu için ayrı bir component olarak yapılabilir --}}
                                            İşlem detayları burada gösterilecek
                                        </td>
                                        
                                        @if($kalanGun >= 0)
                                            @can('deletePlan', $servis)
                                                @if($servis->planDurum == $eskiIslem->id && $eskiIslem->pid == auth()->user()->id)
                                                    <td class="btnCS" style="vertical-align: middle;width: 25px;padding: 0 5px;">
                                                        <button type="button" class="btn btn-danger btn-sm servisPlanSil" 
                                                                data-id="{{ $eskiIslem->id }}" style="font-size: 11px;">
                                                            Sil
                                                        </button>
                                                    </td>
                                                    <td class="btnCS" style="vertical-align: middle;width: 70px;padding: 0 5px;">
                                                        <button type="button" class="btn btn-primary btn-sm servisPlanDuzenleBtn" 
                                                                data-id="{{ $eskiIslem->id }}" style="font-size: 11px;">
                                                            Düzenle
                                                        </button>
                                                    </td>
                                                @else
                                                    <td class="btnCS" style="vertical-align: middle;width: 25px;padding: 0 5px;font-size:11px;">
                                                        Yetkiniz Yok
                                                    </td>
                                                    <td class="btnCS" style="vertical-align: middle;width: 25px;padding: 0 5px;font-size:11px;">
                                                        Yetkiniz Yok
                                                    </td>
                                                @endif
                                            @else
                                                <td class="btnCS" style="vertical-align: middle;width: 25px;padding: 0 5px;font-size:11px;">
                                                    Yetkiniz Yok
                                                </td>
                                                <td class="btnCS" style="vertical-align: middle;width: 25px;padding: 0 5px;font-size:11px;">
                                                    Yetkiniz Yok
                                                </td>
                                            @endcan
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($kalanGun >= 0)
        <div class="card cf1" style="margin-top: 5px;">
            <div class="card-header" style="padding: 3px 5px;">
                <div class="row">
                    <div class="col-sm-12" style="text-align: right;">
                        
                        <button type="button" class="btn btn-primary btn-sm servisGuncelleBtn">
                            Servis Güncelle
                        </button>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Servis güncelleme
    $('.servisGuncelleBtn').click(function() {
        var formData = {
            cihazModel: $('.cihazModel').val(),
            acil: $('.acil').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: '',
            type: 'PUT',
            data: formData,
            success: function(response) {
                alert('Servis güncellendi');
                location.reload();
            },
            error: function(xhr) {
                alert('Hata: ' + xhr.responseJSON.message);
            }
        });
    });

    // Acil durumu toggle
    $('.servisAcilBtn').click(function() {
        var checkbox = $(this).find('input[type="checkbox"]');
        var isChecked = checkbox.is(':checked');
        checkbox.prop('checked', !isChecked);
        $('.acil').val(isChecked ? 0 : 1);
    });

    // Yeni işlem ekleme
    $('.altAsamalar').change(function() {
        var selectedValue = $(this).val();
        if (selectedValue) {
            $.ajax({
                url: '',
                type: 'POST',
                data: {
                    altAsamalar: selectedValue,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('İşlem eklendi');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Hata: ' + xhr.responseJSON.message);
                }
            });
        }
    });

    // Plan silme
    $('.servisPlanSil').click(function() {
        var planId = $(this).data('id');
        if (confirm('Bu planı silmek istediğinizden emin misiniz?')) {
            $.ajax({
                url: '/servis/plan/' + planId,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('Plan silindi');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Hata: ' + xhr.responseJSON.message);
                }
            });
        }
    });
});
</script>
@endpush


