<!-- Modal de Visualização de Imagem Ampliada do Material -->
<div class="modal fade" id="modalMaterialImagePreview" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-dark text-white py-2">
        <h6 class="modal-title fw-bold" id="materialImagePreviewTitle">
          <i class="bi bi-image me-2 text-warning"></i>Visualizar Imagem do Material
        </h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body text-center p-3 bg-light">
        <div class="mb-3">
          <span class="badge bg-primary px-3 py-1 fs-6" id="materialImagePreviewSku"></span>
          <h5 class="fw-bold text-dark mt-2 mb-0" id="materialImagePreviewName"></h5>
        </div>
        <div class="p-2 d-inline-block bg-white rounded shadow-sm border" style="max-width: 100%;">
          <img id="materialImagePreviewImg" 
               src="" 
               alt="Imagem do material" 
               class="img-fluid rounded" 
               style="max-height: 70vh; max-width: 100%; object-fit: contain;">
        </div>
      </div>
      <div class="modal-footer bg-white py-2">
        <a href="#" id="materialImageDownloadBtn" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-auto">
          <i class="bi bi-box-arrow-up-right me-1"></i> Abrir em Nova Aba
        </a>
        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i> Fechar
        </button>
      </div>
    </div>
  </div>
</div>
