<div class="table-responsive" style="margin: 0">
  @if(!is_null($customer_invoices) && count($customer_invoices) > 0)
  <table class="table table-hover table-striped" width="100%" cellspacing="0" style="margin: 0">
    <thead class="title">
      <tr>
        <th style="padding: 5px 10px;font-size: 12px;width: 70px">Tarih</th>
        <th style="padding: 5px 10px;font-size: 12px;">Genel Toplam</th>
        <th style="padding: 5px 10px;font-size: 12px;">Durum</th>
        <th style="padding: 5px 10px;font-size: 12px;width: 50px"></th>
      </tr>
    </thead>
    <tbody>
     @foreach($customer_invoices as $invoice)  
        @php 
          $sontarih = \Carbon\Carbon::parse($invoice->faturaTarihi)->format('d/m/Y');
        @endphp  	 
      <tr>
        <td style="vertical-align: middle;font-size: 11px; padding: 0 10px;">{{$sontarih}}</td>
        <td style="vertical-align: middle;font-size: 11px; padding: 0 10px;"><strong>{{$invoice->genelToplam}}</strong></td>
      

      <!-- Faturanın ürünlerini göster -->
      @if($invoice->invoice_products->count() > 0)
       @foreach($invoice->invoice_products as $product)
          <td colspan="4" style="padding:0 10px;">
            
               {{ $product->urunAdi }}
                   
                  
               
          </td>
           @endforeach
       
      @endif
      <td style="vertical-align: middle;font-size: 11px; padding: 0 10px;"><strong><a href="{{ route('all.invoices', [$firma->id, 'did' => $invoice->id]) }}" class="btn btn-danger btn-sm editDomain" style="font-size:11px" target="_blank">Detaylar</a></strong></td>
        </tr>
      @endforeach
        
    </tbody>
  </table>
  @else
    <div style="color: black;text-align:center;">Fatura bulunmamaktadır</div>
  @endif
</div>
