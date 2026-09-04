# Data Model: Comprovante de Movimentação para Impressão

Esta funcionalidade de correção visual e dimensionamento de impressão não altera a estrutura de banco de dados nem cria novas migrações. As entidades existentes utilizadas na renderização do comprovante são descritas a seguir.

## Entidades Envolvidas

### 1. Movement (`movements`)
Representa a movimentação de estoque exibida.
- **Campos Principais**:
  - `code` (string): Código único da movimentação (ex: `ENT-20260904-FB9C`).
  - `type` (enum `MovementType`): Tipo (`ENTRY`, `EXIT`, `LOAN`, `DEVOLUTION`).
  - `status` (enum `MovementStatus`): Status (`OPEN`, `COMPLETED`, `CANCELLED`).
  - `created_at` (datetime): Timestamp da criação.
  - `notes` (text, opcional): Observações adicionais.
- **Relacionamentos**:
  - `user`: Almoxarife responsável pela operação.
  - `beneficiary`: Beneficiário (para saídas e empréstimos).
  - `destination`: Localidade / destino de aplicação.
  - `entryDocument`: Documento fiscal ou declaração de doação (para entradas).
  - `items`: Lista de itens movimentados.

### 2. MovementItem (`movement_items`)
Linhas da tabela de materiais do comprovante.
- **Campos Principais**:
  - `quantity` (integer/decimal): Quantidade movimentada.
  - `returned_quantity` (integer/decimal): Quantidade devolvida (para empréstimos).
  - `expected_return_date` (date, opcional): Previsão de retorno.
  - `status` (enum `ItemStatus`): Status do item.
- **Relacionamentos**:
  - `material`: Material associado (`code_sku`, `name`, `unit_measure`, `ca_number`).

### 3. Settings (`settings`)
Configurações institucionais da CCB exibidas no cabeçalho e rodapé do comprovante:
- `institution_name`: Nome da instituição (padrão: "CONGREGAÇÃO CRISTÃ NO BRASIL").
- `administration_name`: Nome da administração local (padrão: "Gestão de Almoxarifado - Administração Nova Odessa").
- `receipt_header_title`: Título do comprovante.
