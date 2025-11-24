@extends('frontend.secure.user_master')
@section('user')
<div class="page-content" id="passwords">
    <div class="container-fluid">
        <div class="row pageDetail">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Markalar</h4>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-success markaEkleBtn">Marka Ekle</button>

                        <div class="table-responsive mt-3">
                            <table id="dataTable" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Marka</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($markalar as $marka)
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);" data-id="{{ $marka->id }}" class="markaDuzenleBtn">
                                                @if($marka->resimyol)
                                                <img src="{{ asset('upload/'.$marka->resimyol) }}" width="50" class="mr-2 border">
                                                @endif
                                                <strong>{{ $marka->marka }}</strong>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('super.admin.modeller.index', $marka->id) }}" class="btn btn-info btn-sm">Modeller</a>
                                            <a href="{{ route('super.admin.kodlar.index', ['marka_id'=>$marka->id,'model_id'=>0]) }}" class="btn btn-warning btn-sm">Arıza Kodları</a>
                                            <button class="btn btn-primary btn-sm markaDuzenleBtn" data-id="{{ $marka->id }}">Düzenle</button>
                                            <button class="btn btn-danger btn-sm markaSil" data-id="{{ $marka->id }}">Sil</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
<div class="modal fade" id="markaEkleModal"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body">Yükleniyor...</div></div></div></div>
<div class="modal fade" id="markaDuzenleModal"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body">Yükleniyor...</div></div></div></div>

<script>
$(document).ready(function(){

    /** Marka Ekle Modal **/
    $('.markaEkleBtn').click(function(){
        $.get("{{ route('super.admin.markalar.create') }}", function(data){
            $('#markaEkleModal .modal-body').html(data);
            $('#markaEkleModal').modal('show');
        });
    });

    /** Marka Düzenle Modal **/
    $('.markaDuzenleBtn').click(function(){
        let id = $(this).data('id');
        $.get("/super-admin/marka-duzenle/" + id, function(data){
            $('#markaDuzenleModal .modal-body').html(data);
            $('#markaDuzenleModal').modal('show');
        });
    });

    /** Marka Sil **/
    $('.markaSil').click(function(){
        if(!confirm('Silmek istediğinize emin misiniz?')) return;
        let id = $(this).data('id');

        $.post("/super-admin/marka-sil/" + id, {
            _token: "{{ csrf_token() }}"
        }, function(data){
            alert(data.message);
            location.reload();
        });
    });

});

/** Dinamik load edilen form submit yakalama */
$(document).on("submit", "#markaEkle", function(e){
    e.preventDefault();
    let form = this;

    $.ajax({
        url: "{{ route('super.admin.markalar.store') }}",
        type: "POST",
        data: new FormData(form),
        processData: false,
        contentType: false,
        success: function(res){
            alert(res.message);
            location.reload();
        }
    });
});

</script>
@endsection
