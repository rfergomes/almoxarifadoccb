# HTTP Contracts: Inclusão e Visualização de Imagem de Materiais

**Feature**: `014-imagem-produto-material`  
**Date**: 2026-09-08  

---

## 1. Endpoints Modificados

### 1.1 `POST /materials` (Cadastro de Material)

- **Content-Type**: `multipart/form-data`
- **Autorização**: Usuário autenticado com permissão `manage-materials`.
- **Campos do Payload**:
  - `code_sku` (string, opcional)
  - `name` (string, obrigatório)
  - `category_id` (integer, obrigatório)
  - `unit_measure` (string, obrigatório)
  - `current_stock` (integer, obrigatório)
  - `minimum_stock` (integer, obrigatório)
  - `is_returnable` (boolean, opcional)
  - `expiration_date` (date, opcional)
  - `patrimony_code` (string, opcional)
  - `ca_number` (string, opcional)
  - `ca_validity` (date, opcional)
  - `image` (file, opcional) - Apenas imagens (jpeg, png, jpg, webp, gif), máx. 5MB.
- **Respostas**:
  - `302 Redirect` -> `/materials` com `session('success', 'Material cadastrado com sucesso!')`
  - `422 Unprocessable Content` -> Erros de validação (ex: arquivo não é imagem, tamanho excedido).

---

### 1.2 `POST /materials/{material}` ou `PUT /materials/{material}` (Atualização de Material)

- **Content-Type**: `multipart/form-data` (com `_method=PUT`)
- **Autorização**: Usuário autenticado com permissão `manage-materials`.
- **Campos do Payload**:
  - `code_sku` (string, obrigatório)
  - `name` (string, obrigatório)
  - `category_id` (integer, obrigatório)
  - `unit_measure` (string, obrigatório)
  - `minimum_stock` (integer, obrigatório)
  - `is_returnable` (boolean, obrigatório)
  - `status` (boolean, obrigatório)
  - `expiration_date` (date, opcional)
  - `patrimony_code` (string, opcional)
  - `ca_number` (string, opcional)
  - `ca_validity` (date, opcional)
  - `image` (file, opcional) - Nova imagem (mimes: jpeg, png, jpg, webp, gif; máx 5MB).
  - `remove_image` (boolean/flag, opcional) - Se marcado como `1`, indica que a imagem existente deve ser removida.
- **Respostas**:
  - `302 Redirect` -> `/materials` com `session('success', 'Cadastro do material atualizado com sucesso!')`
  - `422 Unprocessable Content` -> Erros de validação.

---

### 1.3 `GET /materials` (Listagem Geral)

- **Parâmetros de Consulta**: `search`, `category_id`, `expiration_status`, `has_patrimony`, `page`.
- **Renderização**: View Blade `materials.index`.
- **Nova Apresentação de Dados**:
  - Cada item na tabela exibe a miniatura da imagem (`$material->image_url`) ou o placeholder SVG/ícone caso não possua foto.
  - Atributos `data-image-url`, `data-name`, `data-sku` nos elementos interativos para disparar a visualização ampliada via modal `#modalImagePreview`.

---

## 2. Contrato de Interface e Modal de Visualização

### Modal de Imagem Ampliada (`#modalMaterialImagePreview`)
- Disparado pelo clique na miniatura ou botão de zoom na linha da tabela.
- **Estrutura**:
  ```html
  <div class="modal fade" id="modalMaterialImagePreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="materialPreviewTitle">Nome do Material</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center p-3">
          <img id="materialPreviewImage" src="" alt="" class="img-fluid rounded shadow-sm" style="max-height: 75vh; object-fit: contain;">
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <span id="materialPreviewSku" class="badge bg-secondary"></span>
          <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
  ```
