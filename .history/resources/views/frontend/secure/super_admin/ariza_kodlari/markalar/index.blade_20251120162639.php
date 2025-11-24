@extends('frontend.secure.user_master')
@section('user')
<!-- Gerekli Stil Dosyası (Diğer sayfadaki ile aynı) -->
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
                        <table id="datatableMarkalar" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            
                            <!-- Ekle Butonu (Tablo Üstü) -->
                            <a class="btn btn-success btn-sm markaEkleBtn" href="javascript:void(0);">
                                <i class="fas fa-plus"></i><span>Marka Ekle</span>
                            </a>

                            <!-- Tablo Başlıkları -->
                            <thead class="title">
                                <tr>
                                    <th style="width: 10px">ID</th>
                                    <th>Marka</th>
                                    <th data-priority="1" style="width: 300px; text-align:center;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Veriler AJAX ile Controller'dan gelecek -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div>
</div>

<!-- Marka Ekle Modal -->
<div class="modal fade" id="markaEkleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Marka Ekle</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Yükleniyor..
            </div>
        </div>
    </div>
</div>

<!-- Marka Düzenle Modal -->
<div class="modal fade" id="markaDuzenleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Marka Düzenle</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Yükleniyor..
            </div>
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
        serverSide: true, // Sunucu taraflı işlem (AJAX)
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json',
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            search: "", 
            searchPlaceholder: "Marka ara..."
        },
        ajax: {
            url: "{{ route('super.admin.markalar.index') }}", // Controller'daki index rotası
            data: function (data) {
                // Ekstra bir veri göndermeniz gerekirse buraya ekleyebilirsiniz
                // Şu an sadece DataTables'ın kendi arama verisi gidiyor
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'marka_info', name: 'marka' }, // Controller'da oluşturduğumuz resim+isim sütunu
            { data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center" } // İşlemler butonları
        ],
        order: [[0, 'desc']], // Varsayılan olarak ID'ye göre tersten sırala
        columnDefs: [
            {
                "targets": 0,
                "visible": false, // ID sütununu gizle (sıralama için kullanılır ama görünmez)
                "searchable": false
            }
        ],
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        // Diğer sayfadaki tasarım yapısını kopyalıyoruz:
        dom: '<"top"f>rt<"bottom"i<"float-end invoices-filtre"lp>><"clear">',
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tümü"]],
        initComplete: function (settings, json) {
            // Arama kutusunu sağ üst köşeye taşıma işlemi
            var searchContainer = $('#datatableMarkalar_filter');
            var searchInput = searchContainer.find('input');
            var flexContainer = $('<div class="d-flex justify-content-end w-100"></div>');
            
            // Label text'ini temizle
            searchContainer.find('label').contents().filter(function () {
              return this.nodeType == 3;
            }).remove();
            
            searchContainer.addClass('flex-grow-1 me-2');
            searchInput.addClass('w-100');
            
            flexContainer.append(searchContainer);
            // Filtre dropdown'ı olmadığı için sadece searchContainer'ı ekliyoruz
            
            $('#datatableMarkalar_wrapper .top').append(flexContainer);
        }
    });

    // --- BUTON İŞLEMLERİ ---

    // Marka Ekle Butonu
    $('.markaEkleBtn').click(function() {
        $.ajax({
            url: "{{ route('super.admin.markalar.create') }}"
        }).done(function(data) {
            $('#markaEkleModal .modal-body').html(data);
            $('#markaEkleModal').modal('show');
        });
    });

    // Marka Düzenle Butonu (Delegation kullanarak - AJAX sonrası gelen elemanlar için)
    $('#datatableMarkalar').on('click', '.markaDuzenleBtn', function() {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('super.admin.markalar.edit', '') }}/" + id
        }).done(function(data) {
            $('#markaDuzenleModal .modal-body').html(data);
            $('#markaDuzenleModal').modal('show');
        });
    });

    // Marka Sil Butonu
    $('#datatableMarkalar').on('click', '.markaSil', function() {
        if(confirm("Silmek istediğinizden emin misiniz?")) {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('super.admin.markalar.destroy', '') }}/" + id,
                method: "POST", // Veya DELETE methodu kullanıyorsanız ona göre ayarlayın
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE" // Laravel'de delete route'u için
                },
                success: function(data) {
                    // Alert yerine Toast kullanılabilir ama isteğe bağlı basit alert:
                    // alert(data.message); 
                    table.ajax.reload(null, false); // Sayfayı yenilemeden tabloyu güncelle
                },
                error: function() {
                    alert("Bir hata oluştu.");
                }
            });
        }
    });

    // Modal kapandığında içeriği temizle (Opsiyonel)
    $("#markaEkleModal, #markaDuzenleModal").on("hidden.bs.modal", function () {
        $(this).find('.modal-body').html("");
    });

});
</script>
@endsection