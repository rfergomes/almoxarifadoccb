# Data Model & Schema Specification: Gestão de Almoxarifado Central CCB

## 1. Enums PHP 8.1+

### `App\Enums\MovementType`
- `CONSUMPTION = 'CONSUMPTION'` (Saída definitiva de materiais de consumo)
- `EPI = 'EPI'` (Entrega técnica de equipamento de proteção individual)
- `LOAN = 'LOAN'` (Empréstimo temporário de ferramenta ou equipamento)

### `App\Enums\MovementStatus`
- `OPEN = 'OPEN'` (Movimentação aberta / itens pendentes de devolução)
- `COMPLETED = 'COMPLETED'` (Movimentação finalizada / todos os itens entregues ou devolvidos)
- `PARTIALLY_RETURNED = 'PARTIALLY_RETURNED'` (Devolução parcial registrada)
- `OVERDUE = 'OVERDUE'` (Empréstimo com data prevista de devolução expirada)

### `App\Enums\ItemStatus`
- `DELIVERED = 'DELIVERED'` (Entregue / sem necessidade de retorno)
- `PENDING_RETURN = 'PENDING_RETURN'` (Aguardando devolução)
- `RETURNED = 'RETURNED'` (Devolvido ao estoque)

---

## 2. Tabelas do Banco de Dados & Entidades Eloquent

### Tabela `destinations`
Representa os locais físicos e organizacionais da CCB.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `name`: `VARCHAR(150)` - Ex: "C.O. Jardim das Flores"
- `code`: `VARCHAR(30) UNIQUE` - Código do relatório/setor
- `type`: `ENUM('casa_de_oracao', 'obra', 'administracao', 'outro')` - Tipo do destino
- `city`: `VARCHAR(100)`
- `address`: `VARCHAR(255) NULLABLE`
- `status`: `BOOLEAN DEFAULT TRUE` - Ativo/Inativo
- `created_at` / `updated_at`: `TIMESTAMP`

### Tabela `beneficiaries`
Representa quem retira o material no almoxarifado.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `name`: `VARCHAR(150)`
- `document_cpf`: `VARCHAR(14) UNIQUE`
- `phone`: `VARCHAR(20) NULLABLE`
- `role_in_ccb`: `VARCHAR(100)` - Ex: Voluntário, Construtor, Pedreiro, Oficial
- `status`: `BOOLEAN DEFAULT TRUE` - Ativo/Inativo
- `created_at` / `updated_at`: `TIMESTAMP`

### Tabela `categories`
Categorias temáticas de materiais.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `name`: `VARCHAR(100) UNIQUE` - Ex: "EPI", "Consumo", "Ferramenta/Equipamento"
- `description`: `TEXT NULLABLE`
- `created_at` / `updated_at`: `TIMESTAMP`

### Tabela `materials`
Itens físicos de estoque.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `code_sku`: `VARCHAR(50) UNIQUE` - Código SKU do item
- `name`: `VARCHAR(150)`
- `category_id`: `FOREIGN KEY -> categories(id)`
- `unit_measure`: `VARCHAR(10)` - UN, KG, M, CX, PAR, etc.
- `current_stock`: `INT DEFAULT 0` - Saldo disponível
- `minimum_stock`: `INT DEFAULT 0` - Alerta de estoque mínimo
- `ca_number`: `VARCHAR(50) NULLABLE` - Certificado de Aprovação (EPIs)
- `ca_validity`: `DATE NULLABLE` - Validade do CA (EPIs)
- `is_returnable`: `BOOLEAN DEFAULT FALSE` - Exige devolução (ex: ferramentas)
- `status`: `BOOLEAN DEFAULT TRUE` - Ativo/Inativo
- `created_at` / `updated_at`: `TIMESTAMP`

### Tabela `movements`
Cabeçalho das movimentações de saída, empréstimo ou entrega de EPI.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `code`: `VARCHAR(30) UNIQUE` - Ex: "MOV-20260811-0001"
- `user_id`: `FOREIGN KEY -> users(id)` - Almoxarife responsável
- `beneficiary_id`: `FOREIGN KEY -> beneficiaries(id)` - Quem retirou
- `destination_id`: `FOREIGN KEY -> destinations(id)` - Destino da aplicação
- `type`: `VARCHAR(20)` - Enum `MovementType`
- `status`: `VARCHAR(20)` - Enum `MovementStatus`
- `notes`: `TEXT NULLABLE`
- `created_at` / `updated_at`: `TIMESTAMP`

### Tabela `movement_items`
Itens de cada movimentação.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `movement_id`: `FOREIGN KEY -> movements(id) ON DELETE CASCADE`
- `material_id`: `FOREIGN KEY -> materials(id)`
- `quantity`: `INT` - Quantidade entregue
- `returned_quantity`: `INT DEFAULT 0` - Quantidade já devolvida
- `expected_return_date`: `DATE NULLABLE` - Para empréstimos
- `actual_return_date`: `DATE NULLABLE` - Data da devolução completa
- `status`: `VARCHAR(20)` - Enum `ItemStatus`
- `created_at` / `updated_at`: `TIMESTAMP`

---

## 3. Relacionamentos Eloquent

- `Category` **hasMany** `Material`
- `Material` **belongsTo** `Category`
- `User` **hasMany** `Movement`
- `Beneficiary` **hasMany** `Movement`
- `Destination` **hasMany** `Movement`
- `Movement` **belongsTo** `User`
- `Movement` **belongsTo** `Beneficiary`
- `Movement` **belongsTo** `Destination`
- `Movement` **hasMany** `MovementItem`
- `MovementItem` **belongsTo** `Movement`
- `MovementItem` **belongsTo** `Material`

---

## 4. Máquina de Estados e Transições

### Transição de Movimentação do Tipo CONSUMPTION:
1. `Movement`: `COMPLETED`
2. `MovementItem`: `DELIVERED`
3. Ação: Decrementa `current_stock` em `quantity`. Sem pendências.

### Transição de Movimentação do Tipo EPI:
1. `Movement`: `COMPLETED`
2. `MovementItem`: `DELIVERED`
3. Ação: Valida `ca_number` e `ca_validity`. Decrementa `current_stock` em `quantity`.

### Transição de Movimentação do Tipo LOAN (Empréstimo):
1. Criação: `Movement` -> `OPEN`, `MovementItem` -> `PENDING_RETURN`. Decrementa `current_stock`.
2. Verificação Temporal: Se `expected_return_date < NOW()` e status ainda for `OPEN` -> `Movement` passa para `OVERDUE`.
3. Devolução Parcial: `returned_quantity += N`. `Movement` -> `PARTIALLY_RETURNED`. Incrementa `current_stock` em `N`.
4. Devolução Total: `returned_quantity == quantity`. `MovementItem` -> `RETURNED`. `Movement` -> `COMPLETED`. Incrementa `current_stock` em `N` restante.
