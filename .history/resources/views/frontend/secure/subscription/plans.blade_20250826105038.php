@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        <div class="row ">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Aboneliklerim</h4>
                    </div>
                    <div class="card-body">
                        
                        {{-- 1. Trial dönemi --}}
                        @if($onTrial)
                            <div class="alert alert-warning">
                                <strong>Deneme Süresi:</strong> {{ $remainingTrialDays }} gün kaldı.
                            </div>
                        
                        {{-- 2. Trial bitmiş ve abonelik yok --}}
                        @elseif(!$currentPlan)
                            <div class="alert alert-danger">
                                Deneme süreniz sona erdi. Devam edebilmek için bir plan seçmeniz gerekiyor.
                            </div>
                            <div class="row">
                                @foreach($plans as $plan)
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header text-center">
                                                <h5>{{ $plan->name }}</h5>
                                                <h3 class="text-primary">
                                                    {{ $plan->getFormattedPrice() }}
                                                    <small>/ {{ $plan->getBillingCycleText() }}</small>
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <p>{{ $plan->description }}</p>
                                            </div>
                                            <div class="card-footer text-center">
                                                <a href="{{ route('subscription.subscribe', [$tenant->id,$plan->slug]) }}" class="btn btn-primary">
                                                    Planı Satın Al
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        
                        {{-- 3. Aktif abonelik var --}}
                        @else
                            <div class="alert alert-success">
                                Şu anda <strong>{{ $currentPlan->name }}</strong> paketini kullanıyorsunuz.
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <p><strong>Paket Adı:</strong> {{ $currentPlan->name }}</p>
                                    <p><strong>Fiyat:</strong> {{ $currentPlan->getFormattedPrice() }} / {{ $currentPlan->getBillingCycleText() }}</p>
                                    <p><strong>Açıklama:</strong> {{ $currentPlan->description }}</p>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="{{ route('subscription.upgrade', [$tenant->id,$currentPlan->id]) }}" class="btn btn-warning">
                                        Paketi Yükselt
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
