# Tasks: Controle de Validade e Registro de Patrimônio de Produtos

**Input**: Design documents from `/specs/004-validade-patrimonio-produtos/`
**Prerequisites**: [plan.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/004-validade-patrimonio-produtos/plan.md), [spec.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/004-validade-patrimonio-produtos/spec.md), [research.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/004-validade-patrimonio-produtos/research.md), [data-model.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/004-validade-patrimonio-produtos/data-model.md), [contracts/http-contracts.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/004-validade-patrimonio-produtos/contracts/http-contracts.md), [quickstart.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/004-validade-patrimonio-produtos/quickstart.md)

## Organization: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`
- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Preparação do banco de dados e estrutura básica de colunas.

- [x] T001 Criar arquivo de migração para colunas `expiration_date` e `patrimony_code` em `database/migrations/2026_08_14_000001_add_expiration_and_patrimony_to_materials_table.php`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Componentes essenciais da camada de modelo e tipo exigidos por todas as histórias.

**⚠️ CRITICAL**: Nenhuma tarefa de user story pode ser iniciada antes da conclusão desta fase.

- [x] T002 Criar o Enum `App\Enums\ExpirationStatus` em `app/Enums/ExpirationStatus.php`
- [x] T003 Atualizar o modelo `Material` com campos fillable, casts (`expiration_date`), atributos calculados (`expirationStatus`), scopes e checagens (`isExpired`, `isExpiringSoon`) em `app/Models/Material.php`
- [x] T004 Atualizar as regras de validação para `expiration_date` (data) e `patrimony_code` (único, opcional) em `app/Http/Requests/MaterialRequest.php`

**Checkpoint**: Base sólida concluída - implementação das histórias de usuário liberada.

---

## Phase 3: User Story 1 - Alerta e Controle de Validade de Produtos (Priority: P1) 🎯 MVP

**Goal**: Permitir registrar a data de validade de insumos perecíveis e exibir alertas visuais de produtos vencidos e a vencer na listagem e no dashboard.

**Independent Test**: Cadastrar produtos perecíveis com validade expirada e a vencer (próximos 30 dias) e verificar se as badges (`bg-danger` e `bg-warning`) e indicadores do dashboard correspondem exatamente às datas.

### Implementation for User Story 1

- [x] T005 [P] [US1] Adicionar métodos de cálculo de estatísticas e filtros de validade em `app/Services/StockService.php`
- [x] T006 [US1] Atualizar `app/Http/Controllers/DashboardController.php` para enviar os contadores de produtos vencidos e a vencer para a view do dashboard
- [x] T007 [P] [US1] Atualizar a view `resources/views/dashboard.blade.php` incluindo os cards de alerta visual para produtos vencidos e a vencer
- [x] T008 [US1] Atualizar `app/Http/Controllers/MaterialController.php` para processar `expiration_date` no cadastro/edição e aplicar filtro por status de validade na listagem
- [x] T009 [P] [US1] Atualizar `resources/views/materials/form.blade.php` para incluir o campo de data de validade
- [x] T010 [P] [US1] Atualizar `resources/views/materials/index.blade.php` para exibir as badges de status de validade e o filtro correspondente
- [x] T011 [US1] Adicionar alerta SweetAlert2 em `resources/views/movements/create.blade.php` ao tentar movimentar/dar saída em produto vencido

**Checkpoint**: A User Story 1 está totalmente funcional e testável de forma independente.

---

## Phase 4: User Story 2 - Relatório de Produtos Vencidos e a Vencer (Priority: P2)

**Goal**: Gerar e exportar relatórios filtrados de materiais vencidos ou a vencer em determinado período.

**Independent Test**: Selecionar o filtro de produtos vencidos ou a vencer em relatórios e verificar se os dados e o arquivo exportado (PDF/Excel) correspondem ao filtro.

### Implementation for User Story 2

- [x] T012 [P] [US2] Adicionar o método `getExpirationReport` em `app/Services/ReportService.php` para filtrar produtos por janela de vencimento e status
- [x] T013 [US2] Atualizar `app/Http/Controllers/ReportController.php` com a ação de renderizar e exportar o relatório de validade de produtos
- [x] T014 [P] [US2] Atualizar a view de relatório principal `resources/views/reports/index.blade.php` com a opção de filtro por validade
- [x] T015 [P] [US2] Criar template de impressão de relatório em `resources/views/reports/pdf/expiration.blade.php`

**Checkpoint**: User Stories 1 e 2 funcionam de forma independente e integrada.

---

## Phase 5: User Story 3 - Cadastro e Acompanhamento de Código de Patrimônio (Priority: P3)

**Goal**: Permitir cadastrar e pesquisar equipamentos e ferramentas da entidade através do código de patrimônio.

**Independent Test**: Cadastrar um equipamento com código de patrimônio, realizar a busca rápida por esse código e verificar se o item é localizado imediatamente.

### Implementation for User Story 3

- [x] T016 [P] [US3] Adicionar scope de busca por patrimônio em `app/Models/Material.php` (`scopeSearchPatrimony`) e em `app/Services/StockService.php`
- [x] T017 [P] [US3] Incluir o campo Código de Patrimônio em `resources/views/materials/form.blade.php`
- [x] T018 [P] [US3] Incluir a coluna de Patrimônio e campo de busca rápida em `resources/views/materials/index.blade.php`
- [x] T019 [US3] Atualizar relatório de bens em `app/Services/ReportService.php` e em `resources/views/reports/index.blade.php` para incluir a coluna e filtro por código de patrimônio

**Checkpoint**: Todas as User Stories (US1, US2, US3) estão funcionais e testáveis de forma independente.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Testes automatizados, verificação final e validação de conformidade.

- [x] T020 Criar teste automatizado em `tests/Feature/MaterialExpirationAndPatrimonyTest.php` cobrindo validações, scopes e filtros
- [x] T021 Executar migrações e suíte de testes com `C:\xampp\php\php.exe artisan test`
- [x] T022 Executar o roteiro de validação end-to-end em `specs/004-validade-patrimonio-produtos/quickstart.md`

---

## Dependencies & Execution Order

### Phase Dependencies
- **Setup (Phase 1)**: Sem dependências - inicia imediatamente.
- **Foundational (Phase 2)**: Depende da Phase 1. BLOQUEIA todas as User Stories.
- **User Stories (Phases 3, 4, 5)**: Dependem da Phase 2. Podem ser executadas em sequência prioritária (P1 → P2 → P3) ou em paralelo.
- **Polish (Phase 6)**: Depende da conclusão das User Stories.

### Parallel Opportunities
- T005, T007, T009, T010 podem ser executados em paralelo dentro da US1.
- T012, T014, T015 podem ser executados em paralelo dentro da US2.
- T016, T017, T018 podem ser executados em paralelo dentro da US3.

---

## Implementation Strategy

### MVP First (User Story 1 Only)
1. Concluir Phase 1 (Setup) e Phase 2 (Foundational).
2. Concluir Phase 3 (User Story 1 - Validade & Alertas).
3. **VALIDAR**: Testar a sinalização visual de validade no cadastro e dashboard.
4. Entregar o MVP funcional aos usuários do almoxarifado.
