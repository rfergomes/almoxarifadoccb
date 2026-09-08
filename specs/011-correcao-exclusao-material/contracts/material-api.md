# Interface Contracts: Operações de Materiais e Notificações

## 1. Cadastro de Material (Web Form)
- **Rota**: `POST /materials`
- **Controller**: `MaterialController@store`
- **FormRequest**: `StoreMaterialRequest`
- **Payload**:
  ```json
  {
    "code_sku": null, // ou string opcional "MAT-123"
    "name": "Parafuso Sextavado 1/4",
    "category_id": 1,
    "unit_measure": "UN",
    "current_stock": 10,
    "minimum_stock": 5,
    "is_returnable": 0
  }
  ```
- **Resposta com SKU Opcional**:
  - Se `code_sku` for omitido ou vazio: O material é gravado no banco com `code_sku: "GEN-001"` (ou o próximo número sequencial).
  - Redireciona para `materials.index` com flash session `success`.

## 2. Cadastro Rápido de Material (AJAX)
- **Rota**: `POST /quick-registration/material`
- **Controller**: `QuickRegistrationController@material`
- **Payload**:
  ```json
  {
    "code_sku": "", // Vazio ou preenchido
    "name": "Broca de Aço Rápido 8mm",
    "category_id": 2,
    "unit_measure": "UN",
    "current_stock": 0,
    "minimum_stock": 2,
    "is_returnable": 1
  }
  ```
- **Resposta Sucesso (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Material cadastrado com sucesso!",
    "data": {
      "id": 15,
      "code_sku": "GEN-002",
      "name": "Broca de Aço Rápido 8mm",
      "current_stock": 0,
      "unit_measure": "UN",
      "label": "GEN-002 - Broca de Aço Rápido 8mm (Atual: 0 UN)"
    }
  }
  ```

## 3. Exclusão de Material
- **Rota**: `DELETE /materials/{material}`
- **Controller**: `MaterialController@destroy`
- **Autorização**: Apenas perfil `Administrador` (`manage-users`).
- **Pré-condições**:
  - Saldo em estoque deve ser zero (`current_stock == 0`).
  - Não deve possuir itens de movimentação vinculados.
  - Não deve possuir itens em inventário vinculados.
- **Resposta**:
  - Sucesso: Redireciona com flash `success: "Material '{name}' excluído com sucesso!"`.
  - Bloqueio por integridade: Redireciona com flash `error: "Não é possível excluir o material '{name}' pois existem movimentações registradas..."`.

## 4. Contrato de Notificação no Front-end
- **Layout**: `resources/views/layouts/app.blade.php`
- **Biblioteca**: Toastr v2.1.4 via CDN.
- **Formato correto no HTML**:
  - CSS: `<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">`
  - JS: `<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>`
- **Chamada Global**: `toastr.success()`, `toastr.error()`, `toastr.warning()`, `toastr.info()` renderizados dinamicamente a partir das chaves de sessão Laravel via `partials.alerts`.
