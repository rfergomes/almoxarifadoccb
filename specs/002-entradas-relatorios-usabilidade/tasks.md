# Tasks: Entradas de Estoque, Usabilidade Inline e Relatórios PDF/Excel CCB

**Input**: Design documents from `/specs/002-entradas-relatorios-usabilidade/`  
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/api-contracts.md, quickstart.md  
**Organization**: Tasks are grouped by phase and user story to enable independent implementation and testing.

## Format: `- [x] [ID] [P?] [Story?] Description with file path`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: User story label (`[US1]`, `[US2]`, `[US3]`, `[US4]`)
- File paths are exact project-relative paths.

---

## Phase 1: Setup (Shared Infrastructure & Logotipo CCB)

**Purpose**: Instalação do pacote DomPDF, enums e integração dos logotipos institucionais da CCB

- [x] T001 [P] Install `barryvdh/laravel-dompdf` package via Composer
- [x] T002 [P] Create PHP Enum `DocumentType` in `app/Enums/DocumentType.php` and update `MovementType` with `ENTRY` in `app/Enums/MovementType.php`
- [x] T003 [P] Update AdminLTE v4 layout, navbar, sidebar and login view to embed official CCB logos (`/public/images/CCB_Logo_fundo_claro.png`, `CCB_Logo_funco_escuro.png`, `CCB_Logo_Reduzido.png`) in `resources/views/layouts/app.blade.php`, `resources/views/partials/navbar.blade.php`, `resources/views/partials/sidebar.blade.php`, `resources/views/auth/login.blade.php`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Estrutura de Banco de Dados para Documentos de Entrada e Models Eloquent

**⚠️ CRITICAL**: Nenhuma user story pode ser iniciada antes da conclusão desta fase.

- [x] T004 [P] Create migration for `entry_documents` table and update `movements` table schema in `database/migrations/2026_08_11_000007_create_entry_documents_table.php`
- [x] T005 [P] Create Eloquent Model `EntryDocument` in `app/Models/EntryDocument.php` and update `Movement` model in `app/Models/Movement.php`
- [x] T006 [P] Create Form Request `StoreEntryRequest` in `app/Http/Requests/StoreEntryRequest.php`

**Checkpoint**: Infraestrutura pronta. Início do desenvolvimento das histórias.

---

## Phase 3: User Story 1 - Lançamento de Entradas de Estoque por NF ou Doação (Priority: P1)

**Goal**: Permitir o lançamento formal de entradas por Nota Fiscal ou Doação com incremento automático no estoque do material.

**Independent Test**: Registrar uma entrada por Nota Fiscal `NF-10020` de 50 sacos de cimento. Verificar se o estoque atual do cimento aumentou em 50 unidades e o histórico registrou a entrada.

- [x] T007 [P] [US1] Implement `EntryService` for entry registration and stock increment inside `DB::transaction` in `app/Services/EntryService.php`
- [x] T008 [US1] Create `EntryController` for handling entry form and storing entries in `app/Http/Controllers/EntryController.php`
- [x] T009 [P] [US1] Create Blade View for new entry form in `resources/views/entries/create.blade.php`
- [x] T010 [US1] Create Blade View for entries list in `resources/views/entries/index.blade.php`

**Checkpoint**: User Story 1 (Entradas de Estoque) totalmente funcional.

---

## Phase 4: User Story 2 - Cadastro Rápido Inline via Modais (Destinos & Beneficiários) (Priority: P1)

**Goal**: Permitir salvar novos Beneficiários ou Destinos em modais AJAX durante o preenchimento de saídas/entradas sem perder os itens selecionados.

**Independent Test**: Abrir o formulário de saída, preencher 3 itens, cadastrar um beneficiário via modal e verificar a seleção automática do novo item sem recarregar a página.

- [x] T011 [P] [US2] Create `QuickRegistrationController` for handling AJAX modal creation of beneficiaries and destinations in `app/Http/Controllers/QuickRegistrationController.php`
- [x] T012 [P] [US2] Create partial views for quick beneficiary and quick destination modals in `resources/views/partials/modal_quick_beneficiary.blade.php` and `resources/views/partials/modal_quick_destination.blade.php`
- [x] T013 [US2] Integrate AJAX modal scripts into `resources/views/movements/create.blade.php` and `resources/views/entries/create.blade.php`

**Checkpoint**: Usabilidade inline com modais AJAX concluída.

---

## Phase 5: User Story 3 - Relatórios Gerenciais com Exportação PDF e Excel (Priority: P2)

**Goal**: Disponibilizar central de relatórios filtráveis com exportação em PDF (formatado com logo CCB) e Excel (XLSX/CSV).

**Independent Test**: Filtrar "Empréstimos em Atraso", exportar PDF e conferir o cabeçalho oficial com logo da CCB e listagem dos itens em atraso.

- [x] T014 [P] [US3] Implement `ReportService` for querying inventory status, low stock, overdue loans, and entry/exit history in `app/Services/ReportService.php`
- [x] T015 [US3] Create `ReportController` for filtering and exporting reports to PDF and Excel/CSV in `app/Http/Controllers/ReportController.php`
- [x] T016 [P] [US3] Create PDF report template with official CCB logo in `resources/views/reports/pdf/template.blade.php`
- [x] T017 [P] [US3] Create PDF view templates for inventory, overdue loans, and movements history in `resources/views/reports/pdf/inventory.blade.php`, `resources/views/reports/pdf/overdue_loans.blade.php`, `resources/views/reports/pdf/movements.blade.php`
- [x] T018 [US3] Create Central de Relatórios Blade View in `resources/views/reports/index.blade.php`

**Checkpoint**: Central de relatórios gerenciais e exportação PDF/Excel funcional.

---

## Phase 6: User Story 4 - Identidade Visual & Aplicação do Logotipo Oficial CCB (Priority: P2)

**Goal**: Garantir a aplicação das logomarcas oficiais da CCB nos comprovantes impressos e telas do sistema.

**Independent Test**: Acessar o detalhe de uma movimentação e verificar a renderização do cabeçalho com a logo oficial da CCB.

- [x] T019 [P] [US4] Update receipt details view in `resources/views/movements/show.blade.php` to render header with official CCB logo

---

## Phase 7: Polish & Cross-Cutting Concerns

- [x] T020 [P] Update database seeders and routes in `routes/web.php`
- [x] T021 Run quickstart validation scenarios and automated tests for entries, reports, and quick registration

---

## Dependencies & Execution Order

```
[Phase 1: Setup & Logos] ──> [Phase 2: Foundational] ──┬──> [Phase 3: US1 - Entradas NF/Doação]
                                                      ├──> [Phase 4: US2 - Modais Inline AJAX]
                                                      ├──> [Phase 5: US3 - Relatórios PDF/Excel]
                                                      └──> [Phase 6: US4 - Logotipos CCB]
                                                                     │
                                                                     ▼
                                                           [Phase 7: Polish]
```

---

## Implementation Strategy

1. Executar a Fase 1 (Setup & DomPDF & Logos) e a Fase 2 (Foundational).
2. Executar a Fase 3 (US1 - Entradas) e a Fase 4 (US2 - Modais Inline AJAX).
3. Executar a Fase 5 (US3 - Relatórios PDF/Excel) e a Fase 6 (US4 - Aplicação de Logos).
4. Finalizar com a Fase 7 de Polimento e Testes Finais.
