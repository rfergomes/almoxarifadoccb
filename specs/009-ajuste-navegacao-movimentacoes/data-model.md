# Data Model: Correção de Navegação e Contexto entre Entradas e Saídas

**Feature**: `009-ajuste-navegacao-movimentacoes`  
**Date**: 2026-09-04  

---

## Entidades e Modelos Existentes

### 1. `Movement` (`movements` table)

Representa tanto registros de saída/empréstimo quanto de entrada de estoque.

- **Campos Principais**:
  - `id`: unsigned big integer (PK)
  - `code`: string (código identificador, ex: `ENT-20260904-FB9C` ou `MOV-20260904-001`)
  - `type`: `App\Enums\MovementType` enum (`ENTRY`, `CONSUMPTION`, `LOAN`)
  - `status`: `App\Enums\MovementStatus` enum (`COMPLETED`, `OPEN`, `PARTIALLY_RETURNED`, etc.)
  - `user_id`: unsigned big integer (operador responsável)
  - `beneficiary_id`: nullable unsigned big integer (para saídas/empréstimos)
  - `destination_id`: nullable unsigned big integer (para saídas/empréstimos)
  - `created_at`: datetime

- **Relacionamentos**:
  - `entryDocument()`: `hasOne(EntryDocument::class)` (presente quando `type === MovementType::ENTRY`)
  - `items()`: `hasMany(MovementItem::class)`
  - `beneficiary()`: `belongsTo(Beneficiary::class)`
  - `destination()`: `belongsTo(Destination::class)`
  - `user()`: `belongsTo(User::class)`

---

### 2. `MovementType` (`App\Enums\MovementType`)

Enum nativo PHP que categoriza a movimentação:

- `ENTRY = 'entry'` ("Entrada de Estoque")
- `CONSUMPTION = 'consumption'` ("Saída para Consumo")
- `LOAN = 'loan'` ("Empréstimo")

---

## Regras de Consulta e Apresentação de Dados

1. **Listagem de Saídas (`/movements`)**:
   - Critério de seleção: `type IN ('consumption', 'loan')`
   - Ordenação: `latest()` (pela data mais recente)
   - Exclusão expressa: Registros onde `type = 'entry'` nunca devem ser retornados nesta listagem.

2. **Listagem de Entradas (`/entries`)**:
   - Critério de seleção: `type = 'entry'` com `entryDocument` carregado
   - Inalterado (já funciona corretamente).

3. **Comprovante de Movimentação (`/movements/{id}`)**:
   - Acesso unificado a qualquer movimentação válida.
   - Contexto de navegação derivado do valor do atributo `movement.type`.
