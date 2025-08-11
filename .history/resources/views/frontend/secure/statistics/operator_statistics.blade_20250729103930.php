@extends('frontend.secure.user_master')
@section('user')
<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
    </div>
    <div class="container">
    <h4 class="mb-3">Operatör Bazlı Servis Giriş Sayısı</h4>

    <form method="GET" class="row mb-4">
        <div class="col-md-4">
            <label>Tarih Başlangıç</label>
            <input type="text" name="tarih1" class="form-control datepicker" 
                value="{{ request('tarih1') ?? $tarih1->format('d/m/Y') }}">
        </div>
        <div class="col-md-4">
            <label>Tarih Bitiş</label>
            <input type="text" name="tarih2" class="form-control datepicker" 
                value="{{ request('tarih2') ?? $tarih2->format('d/m/Y') }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrele</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Operatör Adı</th>
                <th>Toplam Servis Kaydı</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($istatistikler as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->toplam }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Kayıt bulunamadı.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
@endsection