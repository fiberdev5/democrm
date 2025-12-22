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

                        <!-- Tablo yüklenirken sıçramayı engellemek için stil eklendi -->
                        <div class="table-responsive mt-3">
                            <table id="dataTable" class="table table-hover table-striped" style="width:100%">
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
            <!-- d-flex: Elemanları yan yana dizer ve ortalar -->
            <a href="javascript:void(0);" data-id="{{ $marka->id }}" class="markaDuzenleBtn d-flex align-items-center">
                
                @if($marka->resimyol)
                    {{-- Resim Varsa --}}
                    <img src="{{ asset('upload/'.$marka->resimyol) }}" 
                         width="50" 
                         height="50" 
                         style="object-fit: cover; min-width: 50px;" 
                         class="mr-2 border rounded"
                         loading="lazy">
                @else
                    {{-- Resim Yoksa (Placeholder) --}}
                    <div class="mr-2 border rounded bg-light d-flex align-items-center justify-content-center" 
                         style="width: 50px; height: 50px; min-width: 50px;">
                        <i class="fa fa-image text-muted"></i> <!-- İkon veya boşluk -->
                    </div>
                @endif

                <strong>{{ $marka->marka }}</strong>
            </a>
        </td>
        <td class="align-middle"> <!-- Butonları dikey ortalamak için -->
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
<div class="modal fade" id="markaEkleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body">Yükleniyor...</div></div></div>
</div>
<div class="modal fade" id="markaDuzenleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body">Yükleniyor...</div></div></div>
</div>

<script>
$(document).ready(function(){
    
    // DataTables kullanıyorsanız, sayfa geçişlerinde eventlerin kopmaması için
    // .click() yerine $(document).on('click') kullanmalısınız.

    /** Marka Ekle Modal **/
    $(document).on('click', '.markaEkleBtn', function(){
        $('#markaEkleModal .modal-body').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Yükleniyor...</div>');
        $('#markaEkleModal').modal('show');
        
        $.get("{{ route('super.admin.markalar.create') }}", function(data){
            $('#markaEkleModal .modal-body').html(data);
        });
    });

    /** Marka Düzenle Modal **/
    $(document).on('click', '.markaDuzenleBtn', function(){
        let id = $(this).data('id');
        $('#markaDuzenleModal .modal-body').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Yükleniyor...</div>');
        $('#markaDuzenleModal').modal('show');

        $.get("/super-admin/marka-duzenle/" + id, function(data){
            $('#markaDuzenleModal .modal-body').html(data);
        });
    });

    /** Marka Sil **/
    $(document).on('click', '.markaSil', function(){
        if(!confirm('Silmek istediğinize emin misiniz?')) return;
        let id = $(this).data('id');
        let btn = $(this); // Buton referansını tut

        $.post("/super-admin/marka-sil/" + id, {
            _token: "{{ csrf_token() }}"
        }, function(data){
            // Tüm sayfayı yenilemek yerine sadece satırı silmek daha hızlı hissettirir
            // Eğer DataTables kullanıyorsanız:
            // var table = $('#dataTable').DataTable();
            // table.row(btn.parents('tr')).remove().draw();
            
            alert(data.message);
            location.reload(); 
        });
    });

    /** Dinamik load edilen form submit yakalama */
    $(document).on("submit", "#markaEkle", function(e){
        e.preventDefault();
        let form = this;
        
        // Butonu disable et ki çift tıklama olmasın
        let submitBtn = $(form).find('[type="submit"]');
        submitBtn.prop('disabled', true).html('Kaydediliyor...');

        $.ajax({
            url: "{{ route('super.admin.markalar.store') }}",
            type: "POST",
            data: new FormData(form),
            processData: false,
            contentType: false,
            success: function(res){
                alert(res.message);
                location.reload();
            },
            error: function(err){
                alert("Bir hata oluştu.");
                submitBtn.prop('disabled', false).html('Kaydet');
            }
        });
    });

});
</script>
@endsection