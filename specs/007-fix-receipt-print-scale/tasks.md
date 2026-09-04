---

description: "Lista de tarefas para correção da escala e layout de impressão do comprovante"
---

# Tasks: Correção da Escala e Layout de Impressão Direta do Comprovante de Movimentação

**Input**: Design documents from `specs/007-fix-receipt-print-scale/`
**Prerequisites**: [plan.md](plan.md), [spec.md](spec.md), [research.md](research.md), [contracts/print-receipt.md](contracts/print-receipt.md), [quickstart.md](quickstart.md)

## Format: `- [ ] [TaskID] [P?] [Story?] Description with file path`
- **[P]**: Pode rodar em paralelo (arquivos distintos, sem dependência mútua)
- **[Story]**: Rótulo da User Story correspondente (ex: `[US1]`, `[US2]`)

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Preparação do ambiente e verificação dos ativos de estilo

- [X] T001 Verificar a integridade do ambiente e ferramentas de build em package.json e vite.config.js
- [X] T002 Validar os testes automatizados existentes de movimentação executando `php artisan test --filter=Movement`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Reset global das restrições do AdminLTE 4 no contexto de impressão

- [X] T003 Atualizar seletores de alta especificidade para `@media print` anulando `max-width: 100vw`, `grid-template-areas` e `overflow: auto` em resources/css/responsive-custom.css
- [X] T004 Atualizar as regras de `@media print` no cabeçalho do layout principal em resources/views/layouts/app.blade.php

---

## Phase 3: User Story 1 - Impressão Direta em Escala Padrão 100% (Priority: P1) 🎯 MVP

**Goal**: Garantir que ao clicar em "Imprimir Comprovante" (`window.print()`), a folha A4 seja renderizada completa e legível com a escala em 100% (Padrão) do navegador, eliminando o corte e a folha em branco.

**Independent Test**: Acessar `/movements/1`, acionar `window.print()` com "Escala: Padrão (100%)", e verificar no preview se o cabeçalho, dados e itens preenchem a folha A4 sem cortes nem páginas em branco.

### Implementation for User Story 1

- [X] T005 [US1] Definir `@page { size: A4 portrait; margin: 8mm 10mm; }` e isolar o contêiner `.printable-receipt` para `width: 100% !important; max-width: 100% !important; margin: 0 !important;` no bloco `@push('styles')` em resources/views/movements/show.blade.php
- [X] T006 [US1] Remover a regra destrutiva `html, body, ..., .row { display: block !important; }` que desarmava os grids e substituí-la por reset pontual dos contêineres pais em resources/views/movements/show.blade.php
- [X] T007 [US1] Assegurar a ocultação estrita de todos os botões de ação e modais via classe `.no-print` em resources/views/movements/show.blade.php

**Checkpoint**: User Story 1 concluída — o comprovante agora é exibido na escala de 100% na folha A4.

---

## Phase 4: User Story 2 - Preservação da Estrutura de Grid das Informações e Assinaturas (Priority: P2)

**Goal**: Assegurar que os blocos de dados gerais (Data/Hora, Status, Almoxarife, Fornecedor/Beneficiário) e os campos de assinatura física permaneçam organizados em colunas lado a lado no papel impresso.

**Independent Test**: Inspecionar o preview de impressão e conferir se os blocos de dados estão alinhados em colunas e se as assinaturas (Almoxarife e Recebedor) dividem a largura da folha em 50%/50% com linha de assinatura e nomes centralizados.

### Implementation for User Story 2

- [X] T008 [US2] Configurar regras de impressão para que `.printable-receipt .row` permaneça com `display: flex !important; flex-wrap: wrap !important;` com larguras proporcionais para `.col-6`, `.col-md-3`, `.col-md-4` em resources/views/movements/show.blade.php
- [X] T009 [US2] Estilizar o bloco `.d-print-block` com as duas colunas de assinaturas lado a lado e `break-inside: avoid !important;` em resources/views/movements/show.blade.php
- [X] T010 [US2] Ajustar o espaçamento, tipografia e bordas da tabela de itens para renderização nítida em folha A4 física em resources/views/movements/show.blade.php

**Checkpoint**: User Story 2 concluída — layout de comprovante institucional CCB preservado na impressão física.

---

## Phase 5: Polish & Cross-Cutting Concerns

**Purpose**: Compilação de assets de produção e validação final

- [X] T011 Recompilar os assets front-end para produção executando `pnpm run build`
- [X] T012 Executar a suíte de testes de regressão com `php artisan test --filter=Movement`
- [X] T013 Realizar a checagem manual dos cenários de teste descritos em specs/007-fix-receipt-print-scale/quickstart.md

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências — execução imediata.
- **Foundational (Phase 2)**: Depende do Setup — bloqueia as User Stories.
- **User Story 1 (Phase 3)**: Depende da Fase Foundational — entrega o MVP de impressão em 100%.
- **User Story 2 (Phase 4)**: Depende da User Story 1 — refina o grid e diagramação das assinaturas.
- **Polish (Phase 5)**: Depende da conclusão de US1 e US2.

---

## Implementation Strategy

### MVP First (User Story 1)
1. Completar Setup (T001-T002) e Foundational (T003-T004).
2. Implementar User Story 1 (T005-T007).
3. Validar: o comprovante não fica mais em branco com escala de 100% no Chrome.

### Entrega Incremental (User Story 2)
1. Implementar User Story 2 (T008-T010).
2. Validar: as colunas de dados e assinaturas ficam diagramadas lado a lado.
3. Compilar assets e rodar testes automatizados (T011-T013).
