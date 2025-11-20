
<div class="page-content">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">API Token Yönetimi</h4>
                </div>
            </div>
        </div>

        @if(session('new_token'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Token Oluşturuldu!</strong> Aşağıdaki token'ı kopyalayın. Bu token tekrar gösterilmeyecektir!
                    <div class="mt-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="newToken" value="{{ session('new_token') }}" readonly>
                            <button class="btn btn-primary" onclick="copyToken()">
                                <i class="mdi mdi-content-copy"></i> Kopyala
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">API Token'larım</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                                <i class="mdi mdi-plus"></i> Yeni Token Oluştur
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Token İsmi</th>
                                        <th>Son Kullanım</th>
                                        <th>Durum</th>
                                        <th>Oluşturulma</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tokens as $token)
                                    <tr>
                                        <td>{{ $token->name }}</td>
                                        <td>
                                            @if($token->last_used_at)
                                                {{ $token->last_used_at->diffForHumans() }}
                                            @else
                                                <span class="text-muted">Hiç kullanılmadı</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($token->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Pasif</span>
                                            @endif
                                        </td>
                                        <td>{{ $token->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <form action="{{ route('api.tokens.toggle', $token->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-{{ $token->is_active ? 'warning' : 'success' }}">
                                                    {{ $token->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('api.tokens.destroy', $token->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bu token\'ı silmek istediğinize emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="mdi mdi-delete"></i> Sil
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Henüz token oluşturulmamış</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3">
                            <strong>Önemli:</strong> API token'larınızı güvenli tutun. Token'larınız API'nize tam erişim sağlar.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Token Oluşturma Modal -->
<div class="modal fade" id="createTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('api.tokens.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yeni API Token</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Token İsmi</label>
                        <input type="text" name="name" class="form-control" placeholder="Örn: Web Sitesi, Mobil App" required>
                        <small class="text-muted">Bu token'ı tanımlamak için bir isim verin</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Token Oluştur</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyToken() {
    var tokenInput = document.getElementById('newToken');
    tokenInput.select();
    document.execCommand('copy');
    
    alert('Token kopyalandı!');
}
</script>
