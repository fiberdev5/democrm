@extends('frontend.secure.user_master')
@section('user')

<div class="page-content servis-istatistik" id="personelDepoStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern table-responsive">
                    <div class="card-header">
                        Personel Depo İstatistikleri
                    </div>
                    <div class="card-body">
                        <table id="datatablePersonelDepoStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-user me-2"></i>Personel</th>
                                    <th><i class="fas fa-warehouse me-2"></i>Toplam Stok Adedi</th>
                                   <th style="width: 130px;">İşlemler</th>
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

<script>
// URL parametresi almak için yardımcı fonksiyon
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

$(document).ready(function () {
  
    var table = $('#datatablePersonelDepoStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Herhangi bir personel depo hareketi bulunamadı.",
            sInfo: "Personel Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sSearch: "Personel Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı"
        },
        ajax: {
            url: "{{ route('stock.statistics.data', $tenant_id) }}", // Controller'dan gelecek veri
            type: "POST",
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            {
                data: 'personel_name', // Controller'da 'personel_name' olarak tanımlandı
                name: 'name', // Veritabanı sütun adı
                render: function(data, type, row) {
                    // Etiketleri temizle
                    let tempDiv = document.createElement("div");
                    tempDiv.innerHTML = data;
                    let text = tempDiv.textContent || tempDiv.innerText || "";

                    return `
                    <div class="d-flex align-items-center">
                        <div class="avatar">${text.charAt(0)}</div>
                        <div>
                            <div class="fw-bold">${text}</div>
                        </div>
                    </div>`;
                }

            },
            {
                data: 'toplam_adet', 
                render: function(data) {
                     return `<div class="badge bg-primary">${data}</div>`;
                }
            },
            {
                data: null,
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var url = "{{ url($tenant_id . '/stoklar') }}?personel=" + row.user_id;
                    return '<a href="' + url + '" target="_blank"  class="btn btn-action btn-sm" ><i class="fas fa-eye me-1"></i>Parçaları Göster</a>';
                }
            }
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        dom: '<"top">rt<"bottom"ilp><"clear">', // Arama çubuğunu gizledik, sadece filtre dropdown'ı kullanılıyor
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
    });

});
</script>
@endsection