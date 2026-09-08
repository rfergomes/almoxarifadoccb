# Tasks: Ordenação Padrão da Tabela pelo Nome do Material

**Input**: Design documents from `specs/013-ordenar-materiais-nome/`  
**Prerequisites**: [plan.md](plan.md), [spec.md](spec.md), [research.md](research.md), [data-model.md](data-model.md), [contracts/materials-index.contract.md](contracts/materials-index.contract.md)

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Pode rodar em paralelo (arquivos distintos, sem dependência)
- **[Story]**: Histórias de usuário mapeadas do spec.md ([US1], [US2])
- Caminhos absolutos/relativos de arquivos explícitos em todas as tarefas

---

## Phase 1: Setup (Infraestrutura e Pré-requisitos)

**Propósito**: Garantir integridade do ambiente antes das alterações.

- [X] T001 Verificar status do ambiente e suíte de testes existente com `C:\xampp\php\php.exe artisan test --filter=MaterialSkuAndDeletionFixTest`

---

## Phase 2: Foundational (Pré-requisitos da Funcionalidade)

**Propósito**: Mapear e preparar os pontos exatos de alteração no controller.

- [X] T002 Inspecionar a construção da query de listagem em `app/Http/Controllers/MaterialController.php`

---

## Phase 3: User Story 1 - Consulta do Catálogo em Ordem Alfabética (Priority: P1) 🎯 MVP

**Goal**: Exibir os materiais da tabela `/materials` por padrão ordenados de A a Z pelo nome do material.  
**Independent Test**: Acessar a listagem `/materials` e verificar se itens como "ARAME..." aparecem antes de "MARRETA..." e "MARTELO...".

### Tests for User Story 1

- [X] T003 [P] [US1] Criar teste automatizado `test_materials_are_ordered_alphabetically_by_name` em `tests/Feature/MaterialSkuAndDeletionFixTest.php`

### Implementation for User Story 1

- [X] T004 [US1] Modificar ordenação da query no método `index` de `$query->latest()` para `$query->orderBy('name', 'asc')` em `app/Http/Controllers/MaterialController.php`

**Checkpoint**: Materiais agora são retornados por padrão em ordem alfabética crescente (A-Z).

---

## Phase 4: User Story 2 - Manutenção da Ordenação com Filtros e Paginação (Priority: P2)

**Goal**: Garantir que filtros (busca por texto, categoria, validade) e páginas seguintes mantenham a ordem alfabética.  
**Independent Test**: Executar requisição com parâmetros `?category_id=X` ou `?page=2` e verificar a persistência da ordem alfabética.

### Tests for User Story 2

- [X] T005 [P] [US2] Adicionar asserções de ordenação com filtro de categoria e paginação em `tests/Feature/MaterialSkuAndDeletionFixTest.php`

---

## Phase 5: Polish & Validação Final

**Propósito**: Validação integrada e garantia de regressão zero.

- [X] T006 Executar suíte completa de testes via `C:\xampp\php\php.exe artisan test`
- [X] T007 Validar o fluxo visual conforme os critérios definidos em `specs/013-ordenar-materiais-nome/quickstart.md`

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências.
- **Foundational (Phase 2)**: Depende de Phase 1.
- **User Story 1 (Phase 3)**: Depende de Phase 2 (P1 - MVP).
- **User Story 2 (Phase 4)**: Depende de Phase 3 (P2).
- **Polish (Phase 5)**: Depende de Phase 3 e Phase 4.

### Parallel Opportunities

- T003 e T005 podem ser desenvolvidos de maneira incremental no mesmo arquivo de testes após a implementação de T004.

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Executar Phase 1 e Phase 2.
2. Implementar T003 (teste) e T004 (alteração em `MaterialController.php`).
3. Validar US1 com aprovação imediata do MVP.
4. Adicionar validação de US2 (filtros/paginação) em T005.
5. Rodar bateria geral de testes em T006/T007.
