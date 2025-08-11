@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Teknisyen İstatistikleri</h3>
                </div>
                
                <div class="card-body">
                    <!-- Filtre Alanı -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="tarihAraligi">Tarih Aralığı:</label>
                            <input type="text" id="tarihAraligi" class="form-control" placeholder="Tarih seçiniz">
                        </div>
                        <div class="col-md-4">
                            <label for="cihazTur">Cihaz Türü:</label>
                            <select id="cihazTur" class="form-control">
                                <option value="">Tümü</option>
                                @foreach($cihazTurleri as $cihaz)
                                    <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label><br>
                            <button type="button" id="filterBtn" class="btn btn-primary">
                                <i class="fas fa-search"></i> Listele
                            </button>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="loading" class="text-center" style="display: none;">
                        <img src="{{ asset('images/ajax_load.gif') }}" alt="Yükleniyor...">
                        <p>Veriler yükleniyor...</p>
                    </div>

                    <!-- Tablo -->
                    <div id="tableContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="name">
                                            <span class="d-none d-md-inline">Personel</span>
                                            <span class="d-md-none">Personel</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Atanan Servis</span>
                                            <span class="d-md-none">A</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Tamamlanan Servis</span>
                                            <span class="d-md-none">T</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Şikayetçi Servis</span>
                                            <span class="d-md-none">Ş</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">İptal<br>Servis</span>
                                            <span class="d-md-none">İ</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Haber Verecek</span>
                                            <span class="d-md-none">H</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Fiyatta Anlaşılamadı</span>
                                            <span class="d-md-none">F</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Alınan<br>Ücret</span>
                                            <span class="d-md-none">Ü</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Verilen Teklif</span>
                                            <span class="d-md-none">T</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- AJAX ile doldurulacak -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Detay Modal -->
                    <div class="modal fade" id="detayModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Teknisyen Detay İstatistikleri</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id="detayModalBody">
                                    <!-- Detay içeriği buraya gelecek -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
       
    </div>
</div>

