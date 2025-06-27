<div id="accordion">
  <div class="card">
    <div class="card-header">
      <a class="card-link collapsed" data-bs-toggle="collapse" href="#collapse1" aria-expanded="true"><span data-toggle="tooltip" title="Seçilen personelin kayıt aldığı servisleri listeler." class="fas fa-info-circle"></span> Oparatör Arama <i class="icon-action fa fa-chevron-down"></i></a></div>
    <div id="collapse1" class="collapse" data-parent="#accordion">
      <div class="card-body">
        <form method="post" id="oparatorArama">
          <div class="row form-group">
            <div class="col-lg-5 rw1"><label>Personel</label></div>
            <div class="col-lg-7 rw2">
              <select name="personeller" class="form-control personeller">
                <option value="0">Tüm Personeller</option>                           
              </select>
            </div>
          </div>

          <div class="row form-group">
            <div class="col-lg-5 rw1"><label>Tarih Aralığı</label></div>
            <div class="col-lg-7 rw2">
              <input type="text" name="tarih1" class="form-control datepicker tarih1 " readonly="" value="" style="background:#fff;margin-bottom: 3px;">
              <input type="text" name="tarih2" class="form-control datepicker tarih2 " readonly="" value=""  style="background:#fff;margin-bottom: 2px;">
            </div>
          </div>

          <div class="row">
            <div class="col-lg-7 offset-lg-5">
              <input type="hidden" name="oparatorArama" value="Ara"/>
              <button type="submit" class="btn btn-primary btn-sm inBtn btn-block btnFilter">ARA</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>