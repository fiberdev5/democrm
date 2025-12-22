<div class="p-3 bg-light">
    <div class="row">
        <div class="col-md-12">
            <h6 class="mb-3">
                <i class="fas fa-user-cog me-2"></i>
                Teknisyen Detay Bilgileri
            </h6>
            
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-tasks text-primary mb-2" style="font-size: 24px;"></i>
                            <h6 class="mb-1">Toplam Görev</h6>
                            <span class="badge bg-primary">{{ $detailData['total_tasks'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle text-success mb-2" style="font-size: 24px;"></i>
                            <h6 class="mb-1">Tamamlanan</h6>
                            <span class="badge bg-success">{{ $detailData['completed_tasks'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 24px;"></i>
                            <h6 class="mb-1">Şikayetli</h6>
                            <span class="badge bg-warning">{{ $detailData['complaint_tasks'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-money-bill text-info mb-2" style="font-size: 24px;"></i>
                            <h6 class="mb-1">Toplam Gelir</h6>
                            <span class="badge bg-info">{{ number_format($detailData['total_income'] ?? 0, 2) }} TL</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h6 class="mb-3">
                    <i class="fas fa-chart-line me-2"></i>
                    Performans Özeti
                </h6>
                
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <tbody>
                            <tr>
                                <td><i class="fas fa-clipboard-list text-primary me-2"></i>Atanan Servisler</td>
                                <td class="text-end"><strong>{{ $detailData['assigned_services'] ?? 0 }}</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-check-circle text-success me-2"></i>Tamamlanan Servisler</td>
                                <td class="text-end"><strong>{{ $detailData['completed_services'] ?? 0 }}</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-frown text-danger me-2"></i>Şikayetçi Servisler</td>
                                <td class="text-end"><strong>{{ $detailData['complaint_services'] ?? 0 }}</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-times-circle text-secondary me-2"></i>İptal Edilen Servisler</td>
                                <td class="text-end"><strong>{{ $detailData['cancelled_services'] ?? 0 }}</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-phone text-info me-2"></i>Haber Verecek Servisler</td>
                                <td class="text-end"><strong>{{ $detailData['callback_services'] ?? 0 }}</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-hand-holding-usd text-warning me-2"></i>Fiyat Anlaşmazlığı</td>
                                <td class="text-end"><strong>{{ $detailData['price_disagreement'] ?? 0 }}</strong></td>
                            </tr>
                            <tr class="table-primary">
                                <td><i class="fas fa-money-bill-wave text-success me-2"></i>Toplam Alınan Ücret</td>
                                <td class="text-end"><strong>{{ number_format($detailData['collected_amount'] ?? 0, 2) }} TL</strong></td>
                            </tr>
                            <tr class="table-info">
                                <td><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Toplam Verilen Teklif</td>
                                <td class="text-end"><strong>{{ number_format($detailData['given_offers'] ?? 0, 2) }} TL</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                @if(isset($detailData['recent_services']) && count($detailData['recent_services']) > 0)
                <div class="mt-4">
                    <h6 class="mb-3">
                        <i class="fas fa-clock me-2"></i>
                        Son Servisler
                    </h6>
                    
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Servis No</th>
                                    <th>Müşteri</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailData['recent_services'] as $service)
                                <tr>
                                    <td>#{{ $service['id'] ?? '' }}</td>
                                    <td>{{ $service['customer_name'] ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $service['status_color'] ?? 'secondary' }}">
                                            {{ $service['status_text'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $service['date'] ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>