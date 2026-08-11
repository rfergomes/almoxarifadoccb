# Tasks: Gestão de Almoxarifado Central e Controle de Estoque CCB

**Input**: Design documents from `/specs/001-gestao-almoxarifado/`  
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/api-contracts.md, quickstart.md  
**Organization**: Tasks are grouped by phase and user story to enable independent implementation and testing.

## Format: `- [x] [ID] [P?] [Story?] Description with file path`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: User story label (`[US1]`, `[US2]`, `[US3]`, `[US4]`, `[US5]`)
- File paths are exact project-relative paths.

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Instalação e estruturação base da aplicação (AdminLTE v4, Enums, RBAC)

- [x] T001 Configure AdminLTE v4 base layout, SweetAlert2 and Toastr assets in `resources/views/layouts/app.blade.php`, `resources/views/partials/navbar.blade.php`, `resources/views/partials/sidebar.blade.php`, `resources/views/partials/alerts.blade.php`
- [x] T002 [P] Create PHP 8.1+ Enums in `app/Enums/MovementType.php`, `app/Enums/MovementStatus.php`, `app/Enums/ItemStatus.php`
- [x] T003 [P] Configure Spatie Roles and Permissions seeder in `database/seeders/RolesAndPermissionsSeeder.php`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Estrutura de Banco de Dados, Models Eloquent e Cadastros Base (Destinos, Beneficiários, Materiais)

**⚠️ CRITICAL**: Nenhuma user story pode ser iniciada antes da conclusão desta fase.

- [x] T004 [P] Create migrations for Destinations, Beneficiaries, Categories and Materials in `database/migrations/2026_08_11_000001_create_destinations_table.php`, `database/migrations/2026_08_11_000002_create_beneficiaries_table.php`, `database/migrations/2026_08_11_000003_create_categories_table.php`, `database/migrations/2026_08_11_000004_create_materials_table.php`
- [x] T005 [P] Create migrations for Movements and MovementItems in `database/migrations/2026_08_11_000005_create_movements_table.php`, `database/migrations/2026_08_11_000006_create_movement_items_table.php`
- [x] T006 [P] Create Eloquent Models for `Destination`, `Beneficiary`, `Category`, `Material` in `app/Models/Destination.php`, `app/Models/Beneficiary.php`, `app/Models/Category.php`, `app/Models/Material.php`
- [x] T007 [P] Create Eloquent Models for `Movement` and `MovementItem` with Enum casting in `app/Models/Movement.php`, `app/Models/MovementItem.php`
- [x] T008 [P] Create Form Requests `StoreDestinationRequest`, `StoreBeneficiaryRequest`, `StoreMaterialRequest` in `app/Http/Requests/StoreDestinationRequest.php`, `app/Http/Requests/StoreBeneficiaryRequest.php`, `app/Http/Requests/StoreMaterialRequest.php`
- [x] T009 Create Controllers and Views for Destinations, Beneficiaries, Categories and Materials in `app/Http/Controllers/DestinationController.php`, `app/Http/Controllers/BeneficiaryController.php`, `app/Http/Controllers/MaterialController.php`

**Checkpoint**: Infraestrutura pronta. Início do desenvolvimento de User Stories.

---

## Phase 3: User Story 1 - Registrar Saídas de Materiais de Consumo (Priority: P1) 🎯 MVP

**Goal**: Permitir o lançamento de saídas de materiais de consumo com baixa imediata de estoque e rastreabilidade total (Destino e Beneficiário).

**Independent Test**: Lançar uma saída de 5 unidades de Cimento para a "C.O. Central". Verificar se o saldo do estoque diminuiu em 5 unidades sem pendência de devolução.

- [x] T010 [P] [US1] Implement `StockService` for consumption deduction inside `DB::transaction` in `app/Services/StockService.php`
- [x] T011 [P] [US1] Create Form Request `StoreMovementRequest` for consumption validation in `app/Http/Requests/StoreMovementRequest.php`
- [x] T012 [US1] Create `MovementController` for launching consumption movements in `app/Http/Controllers/MovementController.php`
- [x] T013 [P] [US1] Create Blade View dynamic form for new consumption output in `resources/views/movements/create.blade.php`
- [x] T014 [US1] Create Blade Views for movement list and detail receipt in `resources/views/movements/index.blade.php` and `resources/views/movements/show.blade.php`

**Checkpoint**: User Story 1 (MVP) totalmente funcional e testável independentemente.

---

## Phase 4: User Story 2 - Empréstimo e Devolução de Ferramentas/Equipamentos (Priority: P1)

**Goal**: Permitir empréstimos com data prevista de retorno, destaque de itens em atraso (`OVERDUE`) e devolução parcial ou total.

**Independent Test**: Realizar um empréstimo com data de retorno retroativa e validar destaque de atraso no painel. Processar devolução parcial e total verificando retorno das unidades ao estoque.

- [x] T015 [P] [US2] Implement `LoanService` for loans and partial/total returns in `app/Services/LoanService.php`
- [x] T016 [P] [US2] Create Form Request `ReturnItemRequest` for return validations in `app/Http/Requests/ReturnItemRequest.php`
- [x] T017 [US2] Update `MovementController` to handle loan creation and return processing in `app/Http/Controllers/MovementController.php`
- [x] T018 [US2] Integrate SweetAlert2 return confirmation modal in `resources/views/movements/show.blade.php`

**Checkpoint**: User Stories 1 e 2 funcionam independentemente.

---

## Phase 5: User Story 3 - Entrega e Controle de EPIs (Priority: P2)

**Goal**: Exigir e validar número de CA e validade em entregas de materiais da categoria EPI.

**Independent Test**: Lançar entrega de EPI e validar obrigatoriedade dos campos de CA. Tentar lançar CA com validade expirada e confirmar bloqueio/alerta.

- [x] T019 [P] [US3] Add CA number and validity check helper methods in `app/Models/Material.php`
- [x] T020 [US3] Update `StoreMovementRequest` to enforce CA fields for EPI category in `app/Http/Requests/StoreMovementRequest.php`
- [x] T021 [US3] Update `resources/views/movements/create.blade.php` to dynamically prompt CA info when EPI category is selected

**Checkpoint**: User Story 3 integrada.

---

## Phase 6: User Story 4 - Painel Geral de Indicadores (Dashboard) (Priority: P2)

**Goal**: Exibir cartões de KPI com totais de estoque, alertas de estoque mínimo, empréstimos atrasados e CAs a vencer.

**Independent Test**: Acessar a página inicial (`/dashboard`) e conferir o reflexo exato das movimentações e saldos.

- [x] T022 [P] [US4] Create `DashboardController` to aggregate stock KPIs, minimum stock alerts, overdue loans, and expiring EPI CAs in `app/Http/Controllers/DashboardController.php`
- [x] T023 [US4] Create Dashboard Blade View with AdminLTE v4 KPI cards in `resources/views/dashboard/index.blade.php`

**Checkpoint**: Painel geral operacional.

---

## Phase 7: User Story 5 - Controle de Acesso por Perfis (RBAC) (Priority: P3)

**Goal**: Restringir permissões de acordo com os perfis (Administrador, Almoxarife, Consulta).

**Independent Test**: Logar como usuário com perfil "Consulta" e verificar ocultação dos botões de ação e bloqueio nas rotas mutáveis.

- [x] T024 [P] [US5] Register route permission middlewares in `routes/web.php`
- [x] T025 [US5] Apply `@can` directives in AdminLTE sidebar menu for role-based navigation in `resources/views/partials/sidebar.blade.php`

**Checkpoint**: Todas as User Stories concluídas.

---

## Phase 8: Polish & Cross-Cutting Concerns

- [x] T026 [P] Seed sample data for testing in `database/seeders/DatabaseSeeder.php`
- [x] T027 Run quickstart validation scenarios and automated tests

---

## Dependencies & Execution Order

```
[Phase 1: Setup] ──> [Phase 2: Foundational] ──┬──> [Phase 3: US1 - Consumo (MVP)]
                                              ├──> [Phase 4: US2 - Empréstimos]
                                              ├──> [Phase 5: US3 - EPIs]
                                              ├──> [Phase 6: US4 - Dashboard]
                                              └──> [Phase 7: US5 - RBAC]
                                                         │
                                                         ▼
                                               [Phase 8: Polish]
```

---

## Implementation Strategy

1. Executar a Fase 1 (Setup) e a Fase 2 (Foundational).
2. Executar a Fase 3 (US1 - Saída de Consumo) para obter o MVP funcional.
3. Validar a Fase 3 de forma independente.
4. Executar sequencialmente ou em paralelo as demais User Stories (US2 -> US3 -> US4 -> US5).
5. Finalizar com a Fase 8 de Polish e Testes Finais.
