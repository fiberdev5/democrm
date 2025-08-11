<div id="accordion" id="serviceReportAccordion">
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading1">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" 
        data-bs-target="#collapse1" aria-expanded="true" 
        aria-controls="collapse1">
        <strong>Yapılan Anketler</strong>
      </button>
    </h2>
    <div id="collapse1" class="accordion-collapse collapse" 
      aria-labelledby="heading1" data-bs-parent="#serviceReportAccordion">
      <div class="accordion-body">
        <form  id="operatorArama">
          <div class="row form-group">
            <div class="col-lg-5 rw1"><label>Personel</label></div>
            <div class="col-lg-7 rw2">
              <select name="operator_pers" class="form-control personeller">
                <option value="0">Tüm Personeller</option>  
                @foreach ($operators as $item)
                    <option value="{{$item->user_id}}">{{$item->name}}</option>
                @endforeach                         
              </select>
            </div>
          </div>

          <div class="row form-group">
            <div class="col-lg-5 rw1"><label>Tarih Aralığı</label></div>
            <div class="col-lg-7 rw2">
              <input type="date" name="operator_tarih1" class="form-control tarih1 "  value="{{date('Y-m-d')}}" style="background:#fff;margin-bottom: 3px;">
              <input type="date" name="operator_tarih2" class="form-control tarih2 "  value="{{date('Y-m-d')}}"  style="background:#fff;margin-bottom: 2px;">
            </div>
          </div>

          <div class="row">
            <div class="col-lg-7 offset-lg-5">
              <button type="submit" class="btn btn-primary btn-sm inBtn btn-block btnFilter">ARA</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>