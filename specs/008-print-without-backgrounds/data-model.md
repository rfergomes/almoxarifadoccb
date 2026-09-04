# Data Model: Visibilidade de Impressão Monocromática

Esta feature não realiza alterações estruturais em tabelas, migrações de banco de dados ou modelos Eloquent.

## Entidades Envolvidas no Comprovante

- **Movement (`movements`)**:
  - `code`: Código identificador da movimentação (ex: `ENT-20260904-FB9C`).
  - `type`: Enum de tipo (`MovementType`).
  - `status`: Enum de status (`MovementStatus`).
  - `created_at`: Data e hora de emissão.
  - `notes`: Observações gerais.
- **MovementItem (`movement_items`)**:
  - `quantity`: Quantidade movimentada.
  - `returned_quantity`: Quantidade devolvida (empréstimos).
  - `status`: Status do item.
- **Material (`materials`)**:
  - `code_sku`: Código SKU do item.
  - `name`: Nome do material.
  - `unit_measure`: Unidade de medida (UN, KG, M, etc.).
  - `ca_number`: Certificado de aprovação para EPIs.
- **User (`users`)**:
  - `name`: Nome do almoxarife operador.
- **Beneficiary / Supplier**:
  - `name` / `supplier_or_donor`: Identificação da contraparte na assinatura física.
