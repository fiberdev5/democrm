@extends('frontend.secure.user_master')
@section('user')
 <div class="page-content" id="passwords">
    <div class="container-fluid">
      <div class="row pageDetail">
        <div class="col-12">
    <div class="card mb-3">
        <div class="card-header">
            <h4>Markalar</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button type="button" class="btn btn-success markaEkleBtn">Marka Ekle</button>
            </div>
            
            <div class="table-responsive">
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
                                    <img style="width:50px;height:50px;object-fit:contain;border:1px solid #ddd;margin-right:5px" 
                                         src="{{ asset('upload/'.$marka->resimyol) }}" />
                                    @endif
                                    <strong>{{ $marka->marka }}</strong>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('super.admin.modeller.index', $marka->id) }}" class="btn btn-info btn-sm">Modeller</a>
                                <a href="{{ route('super.admin.kodlar.index', ['marka_id' => $marka->id, 'model_id' => 0]) }}" 
                                   class="btn btn-warning btn-sm">Arıza Kodları</a>
                                <a href="javascript:void(0);" data-id="{{ $marka->id }}" 
                                   class="btn btn-primary btn-sm markaDuzenleBtn">Düzenle</a>
                                <a href="javascript:void(0);" data-id="{{ $marka->id }}" 
                                   class="btn btn-danger btn-sm markaSil">Sil</a>
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
<!-- Marka Ekle Modal -->
<div class="modal fade" id="markaEkleModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Marka Ekle</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Yükleniyor..
            </div>
        </div>
    </div>
</div>

<!-- Marka Düzenle Modal -->
<div class="modal fade" id="markaDuzenleModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Marka Düzenle</h6>
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
    
    // Marka Ekle
    $('.markaEkleBtn').click(function() {
        $.ajax({
            url: "{{ route('super.admin.markalar.create') }}"
        }).done(function(data) {
            $('#markaEkleModal .modal-body').html(data);
            $('#markaEkleModal').modal('show');
        });
    });
    
    // Marka Düzenle
    $('.markaDuzenleBtn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('super.admin.markalar.edit', '') }}/" + id
        }).done(function(data) {
            $('#markaDuzenleModal .modal-body').html(data);
            $('#markaDuzenleModal').modal('show');
        });
    });
    
    // Marka Sil
    $('.markaSil').click(function() {
        if(confirm("Silmek istediğinizden emin misiniz?")) {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('super.admin.markalar.destroy', '') }}/" + id,
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