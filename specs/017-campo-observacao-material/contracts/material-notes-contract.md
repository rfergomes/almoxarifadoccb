# Contract: Campo de Observação no Módulo de Materiais

**Feature**: `017-campo-observacao-material`  
**Date**: 2026-09-18  

---

## 1. Interface Web / Formulários (HTML/Blade)

### 1.1 Modal de Criação (`#modalCreateMaterial`)
- **Método**: `POST`
- **Action**: `route('materials.store')`
- **Campo**:
  ```html
  <div class="col-12">
    <label class="form-label fw-semibold">Observações <small class="text-muted">(Opcional)</small></label>
    <textarea name="notes" class="form-control" rows="3" placeholder="Anotações técnicas, instruções de uso, fornecedor habitual ou restrições..."></textarea>
  </div>
  ```

### 1.2 Modal de Edição (`#modalEditMaterial`)
- **Método**: `POST` (com `@method('PUT')`)
- **Action**: `/materials/{material}`
- **Campo**:
  ```html
  <div class="col-12">
    <label class="form-label fw-semibold">Observações <small class="text-muted">(Opcional)</small></label>
    <textarea name="notes" id="edit_notes" class="form-control" rows="3" placeholder="Anotações técnicas, instruções de uso, fornecedor habitual ou restrições..."></textarea>
  </div>
  ```
- **Contrato JavaScript (`materials.index`)**:
  - Botão acionador: atributo `data-notes="{{ $mat->notes }}"`
  - Manipulador de abertura: `document.getElementById('edit_notes').value = btn.dataset.notes || '';`

### 1.3 Modal de Criação Rápida (`#modalQuickMaterial`)
- **Método**: `POST` (Fetch AJAX)
- **Endpoint**: `/api/quick-material`
- **Campo**:
  ```html
  <div class="col-12">
    <label class="form-label">Observações <small class="text-muted">(Opcional)</small></label>
    <textarea name="notes" class="form-control" rows="2" placeholder="Observações complementares do material..."></textarea>
  </div>
  ```

---

## 2. Contrato de API: `/api/quick-material`

### Requisição
- **Método**: `POST`
- **Headers**:
  - `Accept: application/json`
  - `X-CSRF-TOKEN: {token}`
- **Payload**:
  ```json
  {
    "code_sku": "CCB-042",
    "name": "Massa Corrida PVA 25Kg",
    "category_id": 2,
    "unit_measure": "CX",
    "current_stock": 10,
    "minimum_stock": 2,
    "notes": "Armazenar em local coberto, empilhamento máximo de 3 caixas."
  }
  ```

### Resposta de Sucesso (HTTP 201)
```json
{
  "success": true,
  "message": "Material cadastrado com sucesso!",
  "data": {
    "id": 42,
    "code_sku": "CCB-042",
    "name": "Massa Corrida PVA 25Kg",
    "current_stock": 10,
    "unit_measure": "CX",
    "notes": "Armazenar em local coberto, empilhamento máximo de 3 caixas.",
    "label": "CCB-042 - Massa Corrida PVA 25Kg (Atual: 10 CX)"
  }
}
```

---

## 3. Contrato de Visualização na Tabela de Materiais

- Se `hasNotes()` for verdadeiro:
  - Exibição de ícone de observação junto ao nome do item na coluna "Material":
    ```html
    <span class="d-inline-flex align-items-center gap-1">
      <span class="fw-semibold">{{ $mat->name }}</span>
      <i class="bi bi-chat-left-text-fill text-primary small" 
         data-bs-toggle="tooltip" 
         data-bs-placement="top" 
         data-bs-title="{{ $mat->notes }}"></i>
    </span>
    ```
- Se não possuir notas:
  - Renderiza apenas o nome do material, mantendo a interface limpa e desobstruída.
