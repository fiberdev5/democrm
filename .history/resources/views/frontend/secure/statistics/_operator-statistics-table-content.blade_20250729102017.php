<div class="table-responsive mailTable">
    <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead class="title">
            <tr>
                <th class="name"><span class="desktop">Operatör</span><span class="mobile">Operatör</span></th>
                <th style="width: 15%"><span class="desktop">Toplam</span><span class="mobile">Toplam</span></th>
                <th style="width: 80px"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($getTable as $row)
                <tr>
                    <td><strong>{{ $row->kayitAlanUser->name ?? 'Bilinmiyor' }}</strong></td>
                    <td><strong>{{ $row->toplam }}</strong></td>
                    <td>
                        <a href="{{ route('services.opt_search', [
                            'tenant_id' => request()->route('tenant_id'),
                            'optArama' => $row->kayitAlan,
                            'tarih1' => $tarih1Str,
                            'tarih2' => $tarih2Str
                        ]) }}" target="_blank" class="btn btn-primary btn-sm btn-block" style="font-size:13px;padding:1px">Servisleri Göster </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Herhangi bir servis bulunamadı.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>