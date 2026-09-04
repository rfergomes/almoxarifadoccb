# Tasks: Correção de Navegação e Contexto entre Entradas e Saídas

**Input**: Design documents from `/specs/009-ajuste-navegacao-movimentacoes/`  
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/ui-navigation-contract.md, quickstart.md  
**Organization**: Tarefas organizadas por fases e histórias de usuário (US1 e US2) para possibilitar implementação e validação independentes.

---

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Pode executar em paralelo (arquivos distintos, sem dependência)
- **[Story]**: História de usuário correspondente ([US1], [US2])
- Todos os caminhos de arquivos são relativos à raiz do repositório

---

## Phase 1: Setup (Infraestrutura Compartilhada)

**Propósito**: Preparação e verificação do ambiente de execução

- [X] T001 Verificar integridade das rotas e views existentes em `routes/web.php` e `resources/views/partials/sidebar.blade.php`

---

## Phase 2: Foundational (Pré-requisitos Bloqueantes)

**Propósito**: Garantir que as diretrizes e enums necessários estejam importados e prontos

- [X] T002 Validar uso dos enums `MovementType` em `app/Enums/MovementType.php` para suporte a filtros e condições de view

**Checkpoint**: Pré-requisitos confirmados. Início da implementação das histórias de usuário.

---

## Phase 3: User Story 1 - Consistência de Navegação e Menu Ativo nos Detalhes de Entrada (Priority: P1) 🎯 MVP

**Objetivo**: Ao visualizar os detalhes de uma entrada (`movements.show` com `MovementType::ENTRY`), o menu lateral deve destacar **"Entradas"** como ativo e o botão "← Voltar" deve retornar para a listagem de Entradas (`/entries`). Quando for saída ou empréstimo, o menu ativo é **"Saídas"** e o botão "← Voltar" retorna para `/movements`.

**Critério de Teste Independente**: Clicar em "Detalhes" na listagem de Entradas, conferir que o menu "Entradas" fica com classe `active` (e "Saídas" não), e clicar em "← Voltar" para confirmar retorno à listagem `/entries`.

### Implementação para User Story 1

- [X] T003 [P] [US1] Ajustar ativação contextual dos menus "Saídas" e "Entradas" no menu lateral em `resources/views/partials/sidebar.blade.php`
- [X] T004 [P] [US1] Tornar o botão "← Voltar" contextual em `resources/views/movements/show.blade.php` direcionando para `route('entries.index')` se for `ENTRY` ou `route('movements.index')` se for saída/empréstimo

**Checkpoint**: User Story 1 completa e testável independentemente. Navegação e botão voltar 100% consistentes.

---

## Phase 4: User Story 2 - Escopo Exclusivo do Menu de Saídas (Priority: P1)

**Objetivo**: A tela de Saídas (`/movements`) deve filtrar estritamente movimentações de saída para consumo e empréstimos (`MovementType::CONSUMPTION`, `MovementType::LOAN`), eliminando entradas de nota fiscal/doação (`ENTRY`) da listagem.

**Critério de Teste Independente**: Acessar `/movements` e constatar que nenhuma movimentação do tipo `ENTRY` (ex: `ENT-20260904-FB9C`) aparece na tabela.

### Implementação para User Story 2

- [X] T005 [US2] Adicionar filtro `whereIn('type', [MovementType::CONSUMPTION, MovementType::LOAN])` no método `index()` de `app/Http/Controllers/MovementController.php`

**Checkpoint**: User Story 2 completa. Separação de escopo rigorosa entre Entradas e Saídas concluída.

---

## Phase 5: Polish & Cross-Cutting Concerns

**Propósito**: Validação integrada e conformidade visual

- [X] T006 Executar cenários de teste descritos em `specs/009-ajuste-navegacao-movimentacoes/quickstart.md`
- [X] T007 [P] Executar testes automatizados da aplicação via PHPUnit com `C:\xampp\php\php.exe artisan test`

---

## Dependencies & Execution Order

### Dependência de Fases

- **Setup (Phase 1)** $\rightarrow$ **Foundational (Phase 2)** $\rightarrow$ **User Stories (Phase 3 & 4)** $\rightarrow$ **Polish (Phase 5)**.
- **US1** e **US2** tratam de arquivos distintos (`sidebar.blade.php` / `show.blade.php` vs `MovementController.php`) e podem ser desenvolvidas em paralelo ou sequencialmente.

### Oportunidades de Paralelismo

- `T003` ([sidebar.blade.php](file:///d:/xampp/htdocs/almoxarifadoccb/resources/views/partials/sidebar.blade.php)) e `T004` ([show.blade.php](file:///d:/xampp/htdocs/almoxarifadoccb/resources/views/movements/show.blade.php)) podem ser executadas em paralelo.
- `T005` ([MovementController.php](file:///d:/xampp/htdocs/almoxarifadoccb/app/Http/Controllers/MovementController.php)) pode ser executado em paralelo com T003/T004.

---

## Implementation Strategy

1. **Passo 1 (US1)**: Ajustar `sidebar.blade.php` e `show.blade.php` (garantindo navegação correta de imediato).
2. **Passo 2 (US2)**: Ajustar `MovementController.php` (garantindo filtragem pura de saídas).
3. **Passo 3 (Validação)**: Executar testes de integração / PHPUnit e validar no browser conforme `quickstart.md`.
