@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
    <div class="container-fluid">
        <h4>Abonelik Satın Al</h4>

        <p>Seçtiğiniz Plan: <strong>{{ $plan->name }}</strong></p>
        <p>Fiyat: <strong>{{ $plan->getFormattedPrice() }}</strong></p>

        <form action="{{ route('subscription.process', [$tenant->id ,$plan->id]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="payment_method">Ödeme Yöntemi</label>
                <select name="payment_method" class="form-control" required>
                    <option value="credit_card">Kredi Kartı</option>
                </select>
            </div>

            <div class="form-group mt-3">
                <label>
                    <input type="checkbox" name="terms_accepted" required> Kullanım şartlarını kabul ediyorum
                </label>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Satın Al</button>
        </form>
    </div>
</div>
@endsection
