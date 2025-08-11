@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header sayfaBaslik d-flex justify-content-between align-items-center">
        <span>Anket İstatistikleri</span>
        <div class="d-flex gap-2">
          <input type="text" class="form-control form-control-sm me-1 datepicker" id="start_date" value="{{ date('d/m/Y', strtotime('-7 days')) }}">
          <input type="text" class="form-control form-control-sm me-2 datepicker" id="end_date" value="{{ date('d/m/Y') }}">
          <button id="filterBtn" class="btn btn-sm btn-primary">Filtrele</button>
        </div>
      </div>
      <div class="card-body">
        <table id="anketTable" class="table table-bordered">
          <thead>
            <tr>
              <th>Personel</th>
              <th>Yapılan Anket Sayısı</th>
              <th>Servis ID'leri</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
  let table = $('#anketTable').DataTable({
    processing: true,
    serverSide: false,
    paging: false,
    ajax: {
      url: '{{ route("survey.statistics.data", $tenant_id) }}',
      type: 'POST',
      data: function (d) {
        d.start_date = $('#start_date').val();
        d.end_date = $('#end_date').val();
        d._token = '{{ csrf_token() }}';
      }
    },
    columns: [
      { data: 'adsoyad' },
      { data: 'toplam_servis' },
      { data: 'servis_list' }
    ]
  });

  $('#filterBtn').click(function() {
    table.ajax.reload();
  });

  $('.datepicker').datepicker({
    format: 'dd/mm/yyyy',
    language: 'tr',
    autoclose: true,
    endDate: new Date()
  });
});
</script>
@endsection
