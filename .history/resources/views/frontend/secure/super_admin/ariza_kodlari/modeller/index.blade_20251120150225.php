// resources/views/frontend/secure/super_admin/ariza_kodlari/modeller/index.blade.php

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-header">
            <h4>{{ $markaSec->marka }} Modeller</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button type="button" class="btn btn-success modelEkleBtn">Model Ekle</button>
                <a href="{{ route('markalar.index') }}" class="btn btn-secondary">Geri</a>
            </div>
            
            <div class="table-responsive">
                <table id="dataTable" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modeller as $model)
                        <tr>
                            <td>
                                <a href="javascript:void(0);" data-id="{{ $model->id }}" class="modelDuzenleBtn">
                                    @if($model->resimyol)
                                    <img style="width:50px;height:50px;object-fit:contain;border:1px solid #ddd;margin-right:5px" 
                                         src="{{ asset('upload/'.$model->resimyol) }}" />
                                    @endif
                                    <strong>{{ $model->model }}</strong>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('kodlar.index', ['model_id' => $model->id, 'marka_id' => $model->mid]) }}" 
                                   class="btn btn-warning btn-sm">Arıza Kodları</a>
                                <a href="javascript:void(0);" data-id="{{ $model->id }}" 
                                   class="btn btn-primary btn-sm modelDuzenleBtn">Düzenle</a>
                                <a href="javascript:void(0);" data-id="{{ $model->id }}" 
                                   class="btn btn-danger btn-sm modelSil">Sil</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Model Ekle Modal -->
<div class="modal fade" id="modelEkleModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ $markaSec->marka }} Model Ekle</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Yükleniyor..
            </div>
        </div>
    </div>
</div>

<!-- Model Düzenle Modal -->
<div class="modal fade" id="modelDuzenleModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Model Düzenle</h6>
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
    
    // Model Ekle
    $('.modelEkleBtn').click(function() {
        $.ajax({
            url: "{{ route('modeller.create', $markaSec->id) }}"
        }).done(function(data) {
            $('#modelEkleModal .modal-body').html(data);
            $('#modelEkleModal').modal('show');
        });
    });
    
    // Model Düzenle
    $('.modelDuzenleBtn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('modeller.edit', '') }}/" + id
        }).done(function(data) {
            $('#modelDuzenleModal .modal-body').html(data);
            $('#modelDuzenleModal').modal('show');
        });
    });
    
    // Model Sil
    $('.modelSil').click(function() {
        if(confirm("Silmek istediğinizden emin misiniz?")) {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('modeller.destroy', '') }}/" + id,
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