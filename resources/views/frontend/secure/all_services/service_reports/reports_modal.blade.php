<div id="accordion" id="serviceReportAccordion">
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading1">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" 
        data-bs-target="#collapse1" aria-expanded="true" 
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
              <input type="date" name="tarih1" class="form-control datepicker tarih1 " readonly="" value="" style="background:#fff;margin-bottom: 3px;">
              <input type="date" name="tarih2" class="form-control datepicker tarih2 " readonly="" value=""  style="background:#fff;margin-bottom: 2px;">
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

  <div class="accordion-item">
    <h2 class="accordion-header" id="heading2">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" 
        data-bs-target="#collapse2" aria-expanded="true" 
        aria-controls="collapse2">
        <strong>Teknisyen Arama</strong>
      </button>
    </h2>
    <div id="collapse2" class="accordion-collapse collapse" 
      aria-labelledby="heading2" data-bs-parent="#serviceReportAccordion">
      <div class="accordion-body">
        <form method="post" id="teknisyenArama">
          <div class="row form-group">
            <div class="col-lg-5 rw1"><label>Teknisyen</label></div>
            <div class="col-lg-7 rw2">
              <select name="personeller" class="form-control personeller">
                <option value="0">Tüm Personeller</option>
              </select>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-lg-5 rw1"><label>Yardımcı Teknisyen</label></div>
            <div class="col-lg-7 rw2">
              <select name="yarPersoneller" class="form-control yarPersoneller">
                <option value="0">Tüm Personeller</option>                       
              </select>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-lg-5 rw1"><label>Araç</label></div>
            <div class="col-lg-7 rw2">
              <select name="tekArac" class="form-control tekArac">
                <option value="0">Tüm Araçlar</option>                    
              </select>
            </div>
          </div>

          <div class="row form-group">
            <div class="col-lg-5 rw1"><label>Tarih</label></div>
            <div class="col-lg-7 rw2">
              <input type="date" name="tarih1" class="form-control datepicker tarih1" readonly="" value="" style="background:#fff;">
            </div>
          </div>

          <div class="row form-group">
            <div class="col-lg-5 rw1"><label>Sıralama</label></div>
            <div class="col-lg-7 rw2">
              <select name="siralama" class="form-control siralama">
                <option value="1">Tarihe Göre</option>
                <option value="2">İlçeye Göre</option>
                <option value="3">Müşteri Adına Göre</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-7 offset-lg-5">
              <input type="hidden" name="teknisyenArama" value="Ara"/>
              <button type="submit" class="btn btn-primary btn-sm inBtn btn-block btnFilter teknisyenAramaBtn">ARA</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>