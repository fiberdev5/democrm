@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
  <div class="container-fluid py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Fatura Bilgileri</h2>

    <form action="{{ route('subscription.process', [$tenant_id, $planid]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
      @csrf

      <!-- Sol taraf (Paket Bilgileri) -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Paket Bilgileri</h3>
        <div class="space-y-3 text-gray-600">
          <p><strong>Paket Adı:</strong> {{ $plan->name }}</p>
          <p><strong>Fiyat:</strong> {{ $plan->getFormattedPrice() }} / {{ $plan->getBillingCycleText() }}</p>
          <p><strong>Açıklama:</strong> {!! $plan->description !!}</p>
        </div>
      </div>

      <!-- Sağ taraf (Fatura Bilgileri) -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Fatura Bilgileri</h3>

        <!-- Bireysel / Kurumsal Seçimi -->
        <div class="flex space-x-4 mb-4">
          <label class="flex items-center space-x-2 cursor-pointer">
            <input type="radio" name="billing_type" value="bireysel" id="bireysel" class="form-radio">
            <span>Bireysel</span>
          </label>
          <label class="flex items-center space-x-2 cursor-pointer">
            <input type="radio" name="billing_type" value="kurumsal" id="kurumsal" class="form-radio">
            <span>Kurumsal</span>
          </label>
        </div>

        <!-- Form alanları -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-600">Adınız</label>
            <input type="text" name="first_name" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-400" value="{{ old('first_name', $tenant->name ?? '') }}" required>
          </div>

          <!-- Bireysel Alanları -->
          <div id="bireysel-fields">
            <label class="block text-sm font-medium text-gray-600">TC Kimlik Numaranız</label>
            <input type="text" name="identity_number" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-400" value="{{ old('identity_number', $tenant->tcNo ?? '') }}">
          </div>

          <!-- Kurumsal Alanları -->
          <div id="kurumsal-fields" style="display:none;">
            <label class="block text-sm font-medium text-gray-600">Vergi Dairesi</label>
            <input type="text" name="tax_office" class="w-full border rounded-lg p-2 mb-2 focus:ring-2 focus:ring-orange-400" value="{{ old('tax_office', $tenant->vergiDairesi ?? '') }}">

            <label class="block text-sm font-medium text-gray-600">Vergi Numarası</label>
            <input type="text" name="tax_number" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-400" value="{{ old('tax_number', $tenant->vergiNo ?? '') }}">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600">E-Posta</label>
            <input type="email" name="email" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-400" value="{{ old('email', $tenant->eposta ?? '') }}" required>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600">Telefon</label>
            <input type="text" name="phone" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-400" value="{{ old('phone', $tenant->tel1 ?? '') }}" required>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-600">İl</label>
              <select name="il" id="sehirSelect" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-400">
                <option value="">İl seç</option>
                @foreach($countries as $item)
                  <option value="{{ $item->id }}" {{ $tenant->il == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600">İlçe</label>
              <select name="ilce" id="ilceSelect" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-400">
                <option value="">-Seçiniz-</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600">Adres</label>
            <textarea name="address" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-400">{{ old('address', $tenant->adres ?? '') }}</textarea>
          </div>
        </div>
      </div>

      <!-- Butonlar -->
      <div class="col-span-2 flex justify-end space-x-3">
        <a href="{{ route('subscription.plans', $tenant->id) }}" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700">Geri</a>
        <button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-orange-500 to-[#f9b233] text-white font-semibold hover:opacity-90">Ödeme Sayfasına Geç</button>
      </div>
    </form>
  </div>
</div>
@endsection
<script src="https://cdn.tailwindcss.com"></script>
