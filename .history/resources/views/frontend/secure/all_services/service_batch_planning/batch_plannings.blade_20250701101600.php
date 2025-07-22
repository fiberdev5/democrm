<div class="row wrap">
    <div class="col-lg-4 sol">
        {{-- <button data-toggle="collapse" data-target="#planlamaSearch" 
                class="btn btn-primary btn-block btn-sm planlamaSearchBtn">
            Filtrele <i class="fas fa-chevron-down"></i>
        </button> --}}

        <div id="planlamaSearch" class="collapse show">
            <div class="card">
                <div class="card-header" style="padding: 5px;">
                    <form id="filterForm">
                        <div class="row form-group">
                            <div class="col-md-4 rw1"><label>Tarih</label></div>
                            <div class="col-md-8 rw2">
                                <input type="date" class="form-control datepicker planTarih" 
                                       readonly value="{{ $tomorrow }}" style="background:#fff">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4 rw1"><label>İl Seç</label></div>
                            <div class="col-md-8 rw2">
                                <select class="form-control il" id="il">
                                    <option value="İSTANBUL" selected>İSTANBUL</option>
                                    <option value="ANKARA">ANKARA</option>
                                    <option value="SAKARYA">SAKARYA</option>
                                    <option value="KOCAELİ">KOCAELİ</option>
                                    <option value="TEKİRDAĞ">TEKİRDAĞ</option>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4 rw1"><label>Bölgeler</label></div>
                            <div class="col-md-8 rw2">
                                <select class="form-control bolgeler" id="ilce" multiple style="height: 170px">
                                    <option value="0" selected>HEPSİ</option>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4 rw1"><label>Cihazlar</label></div>
                            <div class="col-md-8 rw2">
                                <select class="form-control cihazlar" multiple style="height: 170px">
                                    <option value="0" selected>HEPSİ</option>
                                    @foreach($deviceTypes as $device)
                                        <option value="{{ $device->id }}">{{ $device->cihaz }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4 rw1"><label>Kaynaklar</label></div>
                            <div class="col-md-8 rw2">
                                <select class="form-control kaynaklar" multiple style="height: 100px">
                                    <option value="0" selected>HEPSİ</option>
                                    @foreach($serviceSources as $source)
                                        <option value="{{ $source->id }}">{{ $source->kaynak }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4 rw1"><label>Durumlar</label></div>
                            <div class="col-md-8 rw2">
                                <select class="form-control durumlar">
                                    <option value="240">Atölyeye Aldır (Nakliye Gönder)</option>
                                    <option value="264">Bayiye Gönder</option>
                                    <option value="237">Cihaz Atölyeye Alındı</option>
                                    <option value="246">Cihaz Tamir Edilemiyor</option>
                                    <option value="261">Parça Hazır</option>
                                    <option value="254">Şikayetçi</option>
                                    <option value="252">Teslimata Hazır (Tamamlandı)</option>
                                    <option value="235" selected>Yeni Servisler</option>
                                    <option value="235-2">Yeni Servisler (Bayiye Gönder)</option>
                                    <option value="248">Yeniden Teknisyen Yönlendir</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" style="padding: 0 5px">
                            <input type="submit" class="btn btn-block btn-primary btn-sm servisPlanListele" value="Listele">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 sag">
        <div class="card">
            <div class="servisListe" style="padding: 0">
                <!-- Service list will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="servisPersonelAtamaModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Servis Atama</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="personelServisDuzenleModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Servis Düzenle</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize datepicker
    $('.datepicker').datepicker({
        language: 'tr',
        autoclose: true,
    });

    // Load districts on city change
    $("#il").on("change", function() {
        loadDistricts($(this).val());
    });

    // Load initial districts
    loadDistricts($("#il").val());

    // Load initial service list
    loadServiceList();

    // Filter form submission
    $("#filterForm").on("submit", function(e) {
        e.preventDefault();
        loadServiceList();
    });

    function loadDistricts(city) {
        $("#ilce").html('<option value="0" selected>HEPSİ</option>');
        
        $.ajax({
            url: "{{ route('service.districts', $firma->id) }}",
            type: "GET",
            data: { ilceSecimId: city },
            success: function(districts) {
                $.each(districts, function(index, district) {
                    $("#ilce").append('<option value="' + district + '">' + district + '</option>');
                });
            }
        });
    }

    function loadServiceList() {
        var formData = {
            planTarih: $(".planTarih").val().replace(/\//g, '-'),
            il: $(".il").val(),
            bolgeler: $(".bolgeler").val(),
            cihazlar: $(".cihazlar").val(),
            kaynaklar: $(".kaynaklar").val(),
            durumlar: $(".durumlar").val()
        };

        $(".servisListe").html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>');

        $.ajax({
            url: "{{ route('service.list', $firma->id) }}",
            type: "GET",
            data: formData,
            success: function(data) {
                $(".servisListe").html(data);
            },
            error: function() {
                $(".servisListe").html('<div class="alert alert-danger">Bir hata oluştu!</div>');
            }
        });
    }

    // Personnel service list
    $(document).on('click', '.personelServisListele', function() {
        var persID = $(".personelList").val();
        $(".servisListe").html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>');
        
        $.ajax({
            url: "{{ route('service.list', $firma->id) }}",
            type: "GET",
            data: { persID: persID },
            success: function(data) {
                $(".servisListe").html(data);
            }
        });
    });

    // Service assignment
    $(document).on('click', '.servisAtaBtn', function() {
        var serviceIds = [];
        $('input[name="servisCheckList"]:checked').each(function() {
            serviceIds.push(this.value);
        });

        if (serviceIds.length === 0) {
            alert('Lütfen en az bir servis seçin!');
            return;
        }

        var data = {
            servisidler: serviceIds.join(', '),
            gidenDurum: $(this).data('id'),
            personel: $(this).data('pers')
        };

        $.ajax({
            url: "{{ route('service.assign', $firma->id) }}",
            type: "POST",
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Atama başarılı!');
                    loadServiceList();
                }
            }
        });
    });

    // Edit service
    $(document).on('click', '.personelServisDuzenle', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var firma_id = {{$firma->id}};
        $.ajax({
            url: "" + id,
            success: function(data) {
                $('#personelServisDuzenleModal .modal-title').html(name + " (" + id + ")");
                $('#personelServisDuzenleModal .modal-body').html(data);
                $('#personelServisDuzenleModal').modal('show');
            }
        });
    });

    // Responsive collapse
    if ($(window).width() < 992) {
        $("#planlamaSearch").removeClass("show");
    }
});
</script>