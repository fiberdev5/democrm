<div id="accordion" id="serviceReportAccordion">
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading1">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" 
        data-bs-target="#collapse1" aria-expanded="false" 
        aria-controls="collapse1">
        <strong>Operatör Arama</strong>
      </button>
    </h2>
    <div id="collapse1" class="accordion-collapse collapse" 
      aria-labelledby="heading1" data-bs-parent="#serviceReportAccordion">
      <div class="accordion-body">
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