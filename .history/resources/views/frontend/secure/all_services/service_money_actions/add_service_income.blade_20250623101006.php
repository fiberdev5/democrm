<form method="post" id="servisOdemeEkle" class="col-sm-6" style="margin: 0 auto;padding:10px;">
  <div class="row form-group ">
    <div class="col-lg-12 rw1"><label><strong>GELİR EKLE (Servis için alınan ödemeleri kayıt edin. Örn: Müşteriden alınan ödeme.)</strong></label></div>
  </div>

    <div class="row form-group ">
      <div class="col-lg-5 rw1"><label>Tarih <span style="font-weight: bold; color: red;">*</span></label></div>
      <div class="col-lg-7 rw2">
        <input type="text" name="tarih" class="form-control datepicker tarih kayitTarihi" readonly="" value="<?php echo date("d/m/Y"); ?>" style="width: 105px;display: inline-block;background:#fff">
      </div>
    </div>
    <div class="row form-group ">
      <div class="col-lg-5 rw1"><label>Personel <span style="font-weight: bold; color: red;">*</span></label></div>
      <div class="col-lg-7 rw2">
        <select class="form-control personeller" name="personeller">
         
        </select>
      </div>
    </div>

  <div class="row form-group ">
    <div class="col-lg-5 rw1"><label>Ödeme Şekli <span style="font-weight: bold; color: red;">*</span></label></div>
    <div class="col-lg-7 rw2">
      <select class="form-control odemeSekli" name="odemeSekli">
         
      </select>
    </div>
  </div>

  <div class="row form-group">
    <div class="col-lg-5 rw1"><label>Ödeme Durumu <span style="font-weight: bold; color: red;">*</span></label></div>
    <div class="col-lg-7 rw2">
      <select class="form-control odemeDurum" name="odemeDurum">
        <option value="1">Tamamlandı</option>
        <option value="2">Beklemede</option>
      </select>
    </div>
  </div>

  <div class="row form-group">
    <div class="col-lg-5 rw1"><label>Fiyat <span style="font-weight: bold; color: red;">*</span></label></div>
    <div class="col-lg-7 rw2">
      <input type="text" name="fiyat" onkeyup="sayiKontrol(this)" class="form-control fiyat" autocomplete="off" placeholder="0.00">
    </div>
  </div>

  <div class="row form-group">
    <div class="col-lg-5 rw1"><label>Açıklama </label></div>
    <div class="col-lg-7 rw2">
      <input type="text" name="aciklama" class="form-control aciklama" autocomplete="off">
    </div>
  </div>

  <div style="text-align: center;margin-top: 5px;">
    <input type="hidden" name="cihazid" class="cihazid" value=""/>
    <input type="hidden" name="markaid" class="markaid" value=""/>
    <input type="hidden" name="servisid" class="servisid" value=""/>
    <input type="submit" class="btn btn-primary btn-sm" value="Gönder"/>
  </div>
    
</form>