@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .table-modern {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        transform: scale(1.01);
        transition: all 0.3s ease;
    }
    .btn-action {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
        border-radius: 20px;
        padding: 6px 14px;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        color: white;
    }
    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 15px;
    }
    .service-count {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
</style>

<div class="page-content" id="operatorStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="row">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-table me-2"></i>Operatör İstatistikleri</h4>
                        <div>
                            <input type="text" id="daterange" class="form-control form-control-sm" style="width: 250px;">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table id="datatableOperatorStats" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Operatör Adı</th>
                                    <th>Toplam Servis Kaydı</th>
                                    <th style="width: 130px;">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dinamik içerik -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>


$(document).ready(function () {
    var start_date = moment().startOf('month');
    var end_date = moment();

    $('#daterange').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        locale: {
            format: 'DD-MM-YYYY',
            separator: ' - ',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    });

    $('#datatableOperatorStats').DataTable({
        columns: [
            { 
                data: 'name',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar">${data.charAt(0)}</div>
                            <div>
                                <strong>${data}</strong><br>
                                <small class="text-muted">#${row.id}</small>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'toplam',
                render: function(data) {
                    return `<div class="service-count">${data}</div>`;
                }
            },
            { 
                data: 'id',
                render: function(data, type, row) {
                    var from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    var url = "{{ url($tenant_id . '/servisler') }}" + "?operator_id=" + data + "&tarih1=" + from_date + "&tarih2=" + to_date;
                    return `<a href="${url}" class="btn btn-action btn-sm" target="_blank">
                                <i class="fas fa-eye"></i> Gör
                            </a>`;
                }
            }
        ],
        language: {
            sEmptyTable: "Veri yok",
            sInfo: "Toplam _TOTAL_ kayıt",
            sSearch: "Ara:",
            oPaginate: {
                sNext: "Sonraki",
                sPrevious: "Önceki"
            }
        }
    });
});
</script>

@endsection
