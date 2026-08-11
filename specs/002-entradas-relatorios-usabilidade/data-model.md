# Data Model & Schema Extension: Entradas e Relatórios CCB

## 1. Novos Enums & Atualizações

### Enum `App\Enums\DocumentType`
- `NOTA_FISCAL = 'NOTA_FISCAL'` (Nota Fiscal de Compra)
- `DOACAO = 'DOACAO'` (Termo de Doação)
- `COMPRA_DIRETA = 'COMPRA_DIRETA'` (Recibo de Compra Direta)
- `OUTRO = 'OUTRO'` (Outros Documentos)

### Enum `App\Enums\MovementType` (Atualizado)
- `CONSUMPTION = 'CONSUMPTION'`
- `EPI = 'EPI'`
- `LOAN = 'LOAN'`
- `ENTRY = 'ENTRY'` (Nova Entrada de Estoque por Documento)

---

## 2. Nova Tabela `entry_documents`

Representa o documento fiscal, recibo ou termo de doação que deu origem à entrada de estoque.

- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `document_number`: `VARCHAR(50)` - Número da NF ou Doação
- `document_type`: `VARCHAR(30)` - Enum `DocumentType`
- `supplier_or_donor`: `VARCHAR(150)` - Fornecedor ou Doador
- `total_amount`: `DECIMAL(12, 2) NULLABLE` - Valor total do documento
- `issued_at`: `DATE NULLABLE` - Data de emissão do documento
- `notes`: `TEXT NULLABLE`
- `created_at` / `updated_at`: `TIMESTAMP`

---

## 3. Alterações na Tabela `movements`

- `entry_document_id`: `FOREIGN KEY -> entry_documents(id) NULLABLE` (vinculado apenas quando `type == ENTRY`).
- `beneficiary_id`: Passa a ser `NULLABLE` (em entradas por doação/compra o beneficiário de retirada não se aplica).
- `destination_id`: Passa a ser `NULLABLE` (entradas vão para o saldo geral do almoxarifado).

---

## 4. Endpoints de Cadastro Rápido AJAX (Modais Inline)

### `POST /api/quick-beneficiary`
- Request: `{ "name": "...", "document_cpf": "...", "phone": "...", "role_in_ccb": "..." }`
- Response 201: `{ "success": true, "data": { "id": 12, "name": "João da Silva (Voluntário)" } }`

### `POST /api/quick-destination`
- Request: `{ "code": "...", "name": "...", "type": "casa_de_oracao", "city": "..." }`
- Response 201: `{ "success": true, "data": { "id": 8, "name": "CO-005 - C.O. Vila Real" } }`
