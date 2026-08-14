<!-- Modal de Pré-visualização de Anexos e Evidências -->
<div class="modal fade" id="modalAttachmentPreview" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title fw-bold" id="attachmentPreviewTitle">
          <i class="bi bi-paperclip me-2"></i>Visualizar Comprovante / Anexo
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-4" id="attachmentPreviewBody">
        <div class="spinner-border text-primary my-4" role="status">
          <span class="visually-hidden">Carregando anexo...</span>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <span class="text-muted small me-auto" id="attachmentMetaInfo"></span>
        <a href="#" id="btnDownloadAttachment" target="_blank" class="btn btn-primary btn-sm rounded-pill">
          <i class="bi bi-download me-1"></i> Baixar Arquivo Original
        </a>
        <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
