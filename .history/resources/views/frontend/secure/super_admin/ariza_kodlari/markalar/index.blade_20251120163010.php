@extends('frontend.secure.user_master')
@section('user')
<!-- Gerekli CSS -->
<link href="{{ asset('frontend/css/super_admin/integrations/all_integrations.css') }}" rel="stylesheet" type="text/css" />

<div class="page-content" id="markalarPage">
    <div class="container-fluid">
        <div class="row pageDetail">
            <div class="col-12">
                <div class="card card-invocies">
                    <div class="card-header card-invocies-header sayfaBaslik">
                        Markalar
                    </div>
                    <div class="card-body card-invocies-body">
                        <!-- Tablo -->
                        <table id="datatableMarkalar" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            
                            <!-- Ekle Butonu (Tablo ile entegre görünecek) -->
                            <a class="btn btn-success btn-sm markaEkleBtn" href="javascript:void(0);" style="margin-bottom: 10px;">
                                <i class="fas fa-plus"></i><span>Marka Ekle</span>
                            </a>

                            <thead class="title">
                                <tr>
                                    <th style="display:none;">ID</th> <!-- Gizli ID -->
                                    <th>Marka</th>
                                    <th style="width: 350px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> 
        </div> 
    </div>
</div>

<!-- Modallar -->
<div class="modal fade" id="markaEkleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Marka Ekle</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Yükleniyor..</div>
        </div>
    </div>
</div>

<div class="modal fade" id="markaDuzenleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Marka Düzenle</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Yükleniyor..</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    
    // --- DATATABLE AYARLARI ---
    var table = $('#datatableMarkalar').DataTable({
        processing: true,
        serverSide: false, // ÖNEMLİ: Bu false olmalı ki veriler kesin görünsün
        ajax: {
            url: "{{ route('super.admin.markalar.index') }}",
            type: "GET"
        },
        columns: [
            { data: 'id', visible: false }, // ID gizli
            { data: 'marka_html' },         // Controller'dan gelen marka HTML
            { data: 'action_html', orderable: false, searchable: false } // İşlem butonları
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json',
            paginate: { previous: "<", next: ">" }
        },
        dom: '<"top"f>rt<"bottom"i<"float-end invoices-filtre"lp>><"clear">',
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tümü"]],
        initComplete: function () {
            // Arama kutusunu sağa yaslama ve stil ayarı
            var searchContainer = $('#datatableMarkalar_filter');
            var searchInput = searchContainer.find('input');
            var wrapper = $('#datatableMarkalar_wrapper .top');
            
            searchContainer.addClass('flex-grow-1 text-end');
            searchInput.addClass('form-control-sm').css('display', 'inline-block').attr('placeholder', 'Marka Ara...');
            
            // Label textini temizle
            searchContainer.contents().filter(function(){ return this.nodeType === 3; }).remove();
            searchContainer.prepend(searchInput);
        }
    });

    // --- İŞLEMLER ---

    // Ekleme
    $('.markaEkleBtn').click(function() {
        $.get("{{ route('super.admin.markalar.create') }}", function(data) {
            $('#markaEkleModal .modal-body').html(data);
            $('#markaEkleModal').modal('show');
        });
    });

    // Düzenleme
    $('#datatableMarkalar').on('click', '.markaDuzenleBtn', function() {
        var id = $(this).data('id');
        $.get("{{ route('super.admin.markalar.edit', '') }}/" + id, function(data) {
            $('#markaDuzenleModal .modal-body').html(data);
            $('#markaDuzenleModal').modal('show');
        });
    });

    // Silme
    $('#datatableMarkalar').on('click', '.markaSil', function() {
        if(confirm("Bu markayı silmek istediğinizden emin misiniz?")) {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('super.admin.markalar.destroy', '') }}/" + id,
                method: "POST", // Laravel delete route'u genelde DELETE methodu ister
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE" // Method spoofing
                },
                success: function(res) {
                    alert(res.message);
                    table.ajax.reload(null, false); // Tabloyu yenile
                },
                error: function() {
                    alert("Silme işlemi sırasında bir hata oluştu.");
                }
            });
        }
    });
});
</script>
@endsection