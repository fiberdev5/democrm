<div class="modal fade" id="surveyModal" tabindex="-1" aria-labelledby="surveyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="surveyForm" method="POST" action="{{ route('survey.save', [$tenant_id]) }}">
      @csrf
      <input type="hidden" name="servisid" id="servisid" value="">
      <input type="hidden" name="bayi" id="bayi" value="0"> <!-- Varsayılan bayi 0 -->
      <input type="hidden" name="tip" id="tip" value="personel"> <!-- personel veya bayi -->

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="surveyModalLabel">Müşteri Anketi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">

          <!-- Soru 1 -->
          <div class="mb-3">
            <label>Teknisyen dediği saatte geldi mi?</label>
            <select class="form-control" name="soru1" id="soru1">
              <option value="0">Belli Değil</option>
              <option value="1">Evet</option>
              <option value="2">Hayır</option>
            </select>
            <input type="text" class="form-control mt-1" name="soru1Text" id="soru1Text" placeholder="Açıklama">
          </div>

          <!-- Soru 2 -->
          <div class="mb-3">
            <label>Teknisyen davranışlarından, kılık ve kıyafetlerinden memnun musunuz?</label>
            <select class="form-control" name="soru2" id="soru2">
              <option value="0">Belli Değil</option>
              <option value="1">Evet</option>
              <option value="2">Hayır</option>
            </select>
            <input type="text" class="form-control mt-1" name="soru2Text" id="soru2Text" placeholder="Açıklama">
          </div>

          <!-- Soru 3 -->
          <div class="mb-3">
            <label>Teknisyen cihazınızla yeterince ilgilendi mi?</label>
            <select class="form-control" name="soru3" id="soru3">
              <option value="0">Belli Değil</option>
              <option value="1">Evet</option>
              <option value="2">Hayır</option>
            </select>
            <input type="text" class="form-control mt-1" name="soru3Text" id="soru3Text" placeholder="Açıklama">
          </div>

          <!-- Soru 4 (Ücret) -->
          <div class="mb-3">
            <label>Sizden Talep Edilen Ücret</label>
            <input type="text" class="form-control" name="soru4Text" id="soru4Text" placeholder="0.00" onkeyup="sayiKontrol(this)">
          </div>

          <!-- Soru 5 -->
          <div class="mb-3">
            <label>Genel olarak servis hizmetimizden memnun musunuz?</label>
            <select class="form-control" name="soru5" id="soru5">
              <option value="0">Belli Değil</option>
              <option value="1">Evet</option>
              <option value="2">Hayır</option>
            </select>
            <input type="text" class="form-control mt-1" name="soru5Text" id="soru5Text" placeholder="Açıklama">
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Kaydet</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function sayiKontrol(input) {
    // Sadece rakam, nokta ve virgül kabul et
    input.value = input.value.replace(/[^0-9.,]/g, '');
    // Virgülü noktaya çevir
    input.value = input.value.replace(',', '.');
  }
</script>