{{-- resources/views/subscription/plans.blade.php --}}
@extends('layouts.app')

@section('title', 'Abonelik Paketleri')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Abonelik Paketleri</h4>
                    @if($tenant->isOnTrial())
                        <div class="alert alert-warning mt-3">
                            <strong>Deneme Süresi:</strong> {{ $tenant->getRemainingTrialDays() }} gün kaldı
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($plans as $plan)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 {{ $plan->is_popular ? 'border-primary' : '' }} {{ $currentPlan && $currentPlan->id == $plan->id ? 'bg-light' : '' }}">
                                @if($plan->is_popular)
                                    <div class="badge badge-primary position-absolute" style="top: -10px; right: 15px;">
                                        Popüler
                                    </div>
                                @endif
                                
                                <div class="card-header text-center">
                                    <h5>{{ $plan->name }}</h5>
                                    <h3 class="text-primary">
                                        {{ $plan->getFormattedPrice() }}
                                        <small class="text-muted">/ {{ $plan->getBillingCycleText() }}</small>
                                    </h3>
                                    @if($plan->description)
                                        <p class="text-muted small">{{ $plan->description }}</p>
                                    @endif
                                </div>
                                
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        @if($plan->features)
                                            @foreach($plan->features as $feature)
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                {{ ucfirst(str_replace('_', ' ', $feature)) }}
                                            </li>
                                            @endforeach
                                        @endif
                                        
                                        @if($plan->limits)
                                            @foreach($plan->limits as $limit => $value)
                                            <li class="mb-2">
                                                <i class="fas fa-info-circle text-info me-2"></i>
                                                {{ $value == -1 ? 'Sınırsız' : $value }} {{ ucfirst(str_replace('_', ' ', $limit)) }}
                                            </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                                
                                <div class="card-footer text-center">
                                    @if($currentPlan && $currentPlan->id == $plan->id)
                                        <button class="btn btn-secondary" disabled>
                                            Mevcut Paketiniz
                                        </button>
                                    @else
                                        <a href="{{ route('subscription.subscribe', $plan) }}" class="btn btn-primary btn-block">
                                            @if($tenant->isOnTrial())
                                                Paketi Seç
                                            @else
                                                Yükselt
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection