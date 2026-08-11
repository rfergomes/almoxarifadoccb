<div class="modal fade" id="modalQuickBeneficiary" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formQuickBeneficiary" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-person-plus text-primary me-2"></i>Novo Beneficiário (Rápido)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nome Completo *</label>
          <input type="text" name="name" class="form-control" placeholder="Ex: Lucas Gabriel" required>
        </div>
        <div class="mb-3">
          <label class="form-label">CPF</label>
          <input type="text" name="document_cpf" class="form-control" placeholder="000.000.000-00">
        </div>
        <div class="mb-3">
          <label class="form-label">Telefone</label>
          <input type="text" name="phone" class="form-control" placeholder="(11) 99999-9999">
        </div>
        <div class="mb-3">
          <label class="form-label">Função na CCB</label>
          <input type="text" name="role_in_ccb" class="form-control" placeholder="Ex: Voluntário, Construtor, Pedreiro">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSaveQuickBeneficiary">Salvar e Selecionar</button>
      </div>
    </form>
  </div>
</div>
