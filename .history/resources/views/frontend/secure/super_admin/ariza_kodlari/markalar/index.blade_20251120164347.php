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
            <div >
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
        // DataTable Başlatma
        var table = $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json'
            }
        });

        // 1. MARKA EKLE (Body üzerinden dinleme yapıyoruz)
        $('body').on('click', '.markaEkleBtn', function() {
            // Önce modal içeriğini temizleyelim ki yükleniyor yazısı görünsün
            $('#markaEkleModal .modal-body').html('Yükleniyor...'); 
            
            $.ajax({
                url: "{{ route('super.admin.markalar.create') }}",
                type: "GET", // Method belirtmek iyidir
                success: function(data) {
                    $('#markaEkleModal .modal-body').html(data);
                    $('#markaEkleModal').modal('show');
                },
                error: function(xhr) {
                    console.log(xhr); // Hatayı konsola yaz
                    alert("Hata oluştu: " + xhr.status + " " + xhr.statusText);
                }
            });
        });

        // 2. MARKA DÜZENLE (DataTable içinde olduğu için 'on' metodu ŞARTTIR)
        $('body').on('click', '.markaDuzenleBtn', function() {
            var id = $(this).data('id');
            
            // Modal içeriğini temizle
            $('#markaDuzenleModal .modal-body').html('Yükleniyor...');

            $.ajax({
                url: "{{ route('super.admin.markalar.edit', ':id') }}".replace(':id', id),
                type: "GET",
                success: function(data) {
                    $('#markaDuzenleModal .modal-body').html(data);
                    $('#markaDuzenleModal').modal('show');
                },
                error: function(xhr) {
                    console.log(xhr);
                    alert("Veri getirilemedi: " + xhr.status + " " + xhr.statusText);
                }
            });
        });

        // 3. MARKA SİL
        $('body').on('click', '.markaSil', function() {
            var id = $(this).data('id');
            if (confirm("Silmek istediğinizden emin misiniz?")) {
                $.ajax({
                    url: "{{ route('super.admin.markalar.destroy', ':id') }}".replace(':id', id),
                    method: "POST", // Laravel'de DELETE methodu için _method field gerekir veya POST atıp backendde silersiniz
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "DELETE" // Resource route kullanıyorsanız bu gereklidir
                    },
                    success: function(data) {
                        alert(data.message || "Silme işlemi başarılı");
                        location.reload();
                    },
                    error: function(xhr) {
                        alert("Silinemedi: " + xhr.responseText);
                    }
                });
            }
        });
    });
</script>
@endsection