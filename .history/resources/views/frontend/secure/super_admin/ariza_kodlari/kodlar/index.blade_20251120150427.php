@extends('frontend.secure.user_master')
@section('user')
<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-header">
            <h4>{{ $titleSec }} Arıza Kodları</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button type="button" class="btn btn-success arizaKoduEkle">Arıza Kodu Ekle</button>
                <a href="{{ $model_id > 0 ? route('modeller.index', $marka_id) : route('markalar.index') }}" 
                   class="btn btn-secondary">Geri</a>
            </div>
            
            <div class="table-responsive">
                <table id="dataTable" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>Başlık</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kodlar as $kod)
                        <tr>
                            <td>
                                <a href="javascript:void(0);" data-id="{{ $kod->id }}" class="arizaKoduDuzenleBtn">
                                    <strong>{{ $kod->kodu }}</strong>
                                </a>
                            </td>
                            <td>
                                <a href="javascript:void(0);" data-id="{{ $kod->id }}" class="arizaKoduDuzenleBtn">
                                    <strong>{{ $kod->baslik }}</strong>
                                </a>
                            </td>
                            <td>
                                <a href="javascript:void(0);" data-id="{{ $kod->id }}" 
                                   class="btn btn-primary btn-sm arizaKoduDuzenleBtn">Düzenle</a>
                                <a href="javascript:void(0);" data-id="{{ $kod->id }}" 
                                   class="btn btn-danger btn-sm kodSil">Sil</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Arıza Kodu Ekle Modal -->
<div class="modal fade" id="arizaKoduEkleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Arıza Kodu Ekle</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Yükleniyor..
            </div>
        </div>
    </div>
</div>

<!-- Arıza Kodu Düzenle Modal -->
<div class="modal fade" id="arizaKoduDuzenleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Arıza Kodu Düzenle</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
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
    // DataTable
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json'
        }
    });
    
    // Arıza Kodu Ekle
    $('.arizaKoduEkle').click(function() {
        $.ajax({
            url: "{{ route('kodlar.create') }}?marka_id={{ $marka_id }}&model_id={{ $model_id }}"
        }).done(function(data) {
            $('#arizaKoduEkleModal .modal-body').html(data);
            $('#arizaKoduEkleModal').modal('show');
        });
    });
    
    // Arıza Kodu Düzenle
    $('.arizaKoduDuzenleBtn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('kodlar.edit', '') }}/" + id
        }).done(function(data) {
            $('#arizaKoduDuzenleModal .modal-body').html(data);
            $('#arizaKoduDuzenleModal').modal('show');
        });
    });
    
    // Kod Sil
    $('.kodSil').click(function() {
        if(confirm("Silmek istediğinizden emin misiniz?")) {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('kodlar.destroy', '') }}/" + id,
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    alert(data.message);
                    location.reload();
                }
            });
        }
    });
});
</script>
@endsection