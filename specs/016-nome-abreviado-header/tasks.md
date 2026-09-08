# Tasks: Formatação Abreviada do Nome do Usuário no Header

**Input**: Design documents from `specs/016-nome-abreviado-header/` (`plan.md`, `spec.md`, `research.md`, `data-model.md`, `contracts/`, `quickstart.md`)

**Prerequisites**: `plan.md` (arquitetura e decisões), `spec.md` (histórias de usuário e critérios), `data-model.md` (accessors Eloquent), `contracts/` (contrato da navbar)

**Tests**: Testes automatizados incluídos na fase de polimento (`tests/Feature/UserProfileTest.php`) e roteiro manual em `quickstart.md`.

**Organization**: Tarefas agrupadas por fases e histórias de usuário para viabilizar implementação modular e testes independentes.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Tarefa paralelizada (arquivos independentes, sem dependência de tarefas incompletas)
- **[Story]**: História de usuário à qual a tarefa pertence ([US1], [US2])
- Caminhos de arquivos explicitados em cada descrição

---

## Phase 1: Setup (Infraestrutura e Validação)

**Purpose**: Verificação do ambiente e arquivos-alvo da feature

- [x] T001 Verificar a integridade dos arquivos base em `app/Models/User.php` e `resources/views/partials/navbar.blade.php`

---

## Phase 2: Foundational (Pré-requisitos Bloqueantes)

**Purpose**: Criação dos accessors Eloquent para formatação de nomes

**⚠️ CRITICAL**: A história de usuário depende diretamente dos métodos auxiliares no modelo User.

- [x] T002 Implementar os accessors `getFirstNameAttribute(): string` e `getShortNameAttribute(): string` em `app/Models/User.php` com tratamento de espaços múltiplos, monônimos e nomes compostos

**Checkpoint**: Fundação pronta — modelo `User` expõe `$user->first_name` e `$user->short_name` com tipagem estrita.

---

## Phase 3: User Story 1 - Exibição Compacta e Responsiva no Header (Priority: P1) 🎯 MVP

**Goal**: Exibir o Primeiro e Último Nome no desktop e apenas o Primeiro Nome no mobile na barra de topo, preservando o nome completo no dropdown.

**Independent Test**: Fazer login com "Rodrigo Fernando Gomes Lima", verificar `Rodrigo Lima` no desktop (≥ 768px), `Rodrigo` no mobile (< 768px) e `Rodrigo Fernando Gomes Lima` no dropdown do usuário.

### Implementation for User Story 1

- [x] T003 [US1] Atualizar o gatilho do dropdown do usuário em `resources/views/partials/navbar.blade.php` renderizando `Auth::user()->short_name` com classe `d-none d-md-inline` e `Auth::user()->first_name` com classe `d-inline d-md-none`
- [x] T004 [US1] Garantir a preservação da exibição do nome completo cadastrado `Auth::user()->name` no cabeçalho interno do dropdown ("Conectado como") em `resources/views/partials/navbar.blade.php`

**Checkpoint**: User Story 1 funcional de ponta a ponta (header moderno, responsivo e limpo).

---

## Phase 4: User Story 2 - Manutenção da Identificação nas Telas de Gestão (Priority: P2)

**Goal**: Garantir que as telas de Perfil, Gestão de Usuários e Auditoria continuem exibindo o nome completo cadastrado.

**Independent Test**: Acessar `/profile` e `/users` e constatar que o nome cadastrado integral permanece inalterado.

### Implementation for User Story 2

- [x] T005 [US2] Validar e assegurar a exibição inalterada do nome completo em `resources/views/profile/edit.blade.php` e `resources/views/users/index.blade.php`

**Checkpoint**: Todas as 2 histórias de usuário integradas e consistentes.

---

## Phase 5: Polish & Cross-Cutting Concerns

**Purpose**: Testes automatizados, validação de responsividade e conferência de qualidade

- [x] T006 [P] Atualizar a suíte de testes em `tests/Feature/UserProfileTest.php` com casos de teste cobrindo a geração de `first_name` e `short_name` (monônimos, nomes duplos e longos) e a renderização responsiva no cabeçalho
- [x] T007 Executar os testes automatizados via Artisan (`C:\xampp\php\php.exe artisan test --filter=UserProfileTest`) e validar o roteiro de `specs/016-nome-abreviado-header/quickstart.md`

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências — execução imediata.
- **Foundational (Phase 2)**: Depende da Phase 1 — BLOQUEIA as histórias de usuário.
- **User Story 1 (Phase 3)**: Depende da Phase 2 — Entrega a navbar responsiva.
- **User Story 2 (Phase 4)**: Depende da Phase 3 — Garante integridade em outras telas.
- **Polish (Phase 5)**: Depende da implementação das histórias de usuário.

---

## Implementation Strategy

### MVP First (User Story 1)
1. Concluir Phase 1 (Setup) e Phase 2 (Foundational - Accessors no User).
2. Concluir Phase 3 (US1 - Navbar responsiva com classes Bootstrap 5).
3. **Validar MVP**: Conferir no navegador o nome abreviado no desktop e no mobile.
4. Concluir Phase 4 e Phase 5 (Testes automatizados e polimento final).
