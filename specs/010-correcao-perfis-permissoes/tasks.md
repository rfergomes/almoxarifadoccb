# Tasks: Perfis Únicos, Correção de Permissões e Exclusão Segura com Integridade Relacional

**Input**: Design documents from `/specs/010-correcao-perfis-permissoes/`  
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/deletion-and-roles-contracts.md, quickstart.md  
**Organization**: Tarefas organizadas por histórias de usuário com rastreabilidade direta aos requisitos.

---

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Pode executar em paralelo (arquivos distintos, sem dependência mútua)
- **[Story]**: Mapeamento para as histórias de usuário ([US1], [US2], [US4], [US5], [US6])
- Caminhos de arquivos relativos à raiz do repositório

---

## Phase 1: Setup (Infraestrutura Compartilhada)

**Propósito**: Preparação de rotas e verificações de base

- [X] T001 Verificar estrutura de rotas em `routes/web.php` para suporte a operações de exclusão HTTP DELETE

---

## Phase 2: Foundational (Pré-requisitos do Modelo User)

**Propósito**: Helpers e accessors para sustentação do perfil único

- [X] T002 Implementar rotina de sanitização de múltiplos papéis para normalizar registros legados em `app/Models/User.php`
- [X] T003 [P] Adicionar accessors `primary_role` e `primary_role_badge` em `app/Models/User.php`

**Checkpoint**: Base do modelo User preparada. Início da implementação das histórias de usuário.

---

## Phase 3: User Story 1 & 2 - Garantia de Perfil Único e Consistência Visual (Priority: P1) 🎯 MVP

**Objetivo**: Garantir que nenhum usuário acumule múltiplos perfis na criação/edição e manter consistência entre Navbar e Tabela.

**Critério de Teste Independente**: Cadastrar um novo usuário como Almoxarife e verificar que ele exibe exclusivamente o badge Almoxarife na tabela e no topo.

### Implementação para User Story 1 e 2

- [X] T004 [US1] Alterar `UserController@store` para utilizar `syncRoles([$data['role']])` garantindo perfil único em `app/Http/Controllers/UserController.php`
- [X] T005 [P] [US2] Atualizar visualização do perfil do usuário autenticado no menu superior em `resources/views/partials/navbar.blade.php`
- [X] T006 [P] [US2] Atualizar tabela de gerenciamento de usuários e modal de edição para perfil único em `resources/views/users/index.blade.php`

**Checkpoint**: User Story 1 e 2 concluídas. Nenhum usuário acumula papéis e a interface é 100% coerente.

---

## Phase 4: User Story 4 - Exclusão Segura de Usuários (Priority: P1)

**Objetivo**: Permitir que Administradores excluam usuários que não possuam movimentações associadas, bloqueando autoexclusão.

**Critério de Teste Independente**: Tentar excluir o próprio usuário (bloqueado); tentar excluir usuário com movimentações (bloqueado); excluir usuário sem movimentações (sucesso).

### Implementação para User Story 4

- [X] T007 [US4] Implementar método `destroy` com travas de segurança em `app/Http/Controllers/UserController.php`
- [X] T008 [US4] Registrar rota `DELETE /users/{user}` protegida por `can:manage-users` em `routes/web.php`
- [X] T009 [US4] Adicionar botão de exclusão com confirmação SweetAlert2 na tabela em `resources/views/users/index.blade.php`

**Checkpoint**: User Story 4 concluída. Exclusão de usuários funcional e segura contra quebra de auditoria.

---

## Phase 5: User Story 5 - Exclusão Segura de Materiais Sem Relações Ativas (Priority: P1)

**Objetivo**: Administrador pode excluir materiais que nunca tenham sido movimentados e com estoque zero.

**Critério de Teste Independente**: Tentar excluir material com histórico de movimentações (bloqueado); excluir material sem movimentações (sucesso).

### Implementação para User Story 5

- [X] T010 [US5] Implementar método `destroy` com validação de `movementItems` e saldo em `app/Http/Controllers/MaterialController.php`
- [X] T011 [US5] Registrar rota `DELETE /materials/{material}` restrita a Administrador em `routes/web.php`
- [X] T012 [US5] Adicionar botão de exclusão de material com confirmação SweetAlert2 em `resources/views/materials/index.blade.php`

**Checkpoint**: User Story 5 concluída. Exclusão de materiais protegida por integridade referencial.

---

## Phase 6: User Story 6 - Exclusão Segura de Movimentações com Estorno (Priority: P2)

**Objetivo**: Administrador pode excluir movimentações indevidas, com reversão atômica de saldo no estoque via `StockService`.

**Critério de Teste Independente**: Cadastrar uma movimentação de teste e acionar exclusão como Administrador; o estoque é revertido e a movimentação é apagada.

### Implementação para User Story 6

- [X] T013 [US6] Implementar método `deleteMovement` com estorno de estoque em transação atômica em `app/Services/StockService.php`
- [X] T014 [US6] Implementar método `destroy` restrito a Administrador em `app/Http/Controllers/MovementController.php`
- [X] T015 [US6] Registrar rota `DELETE /movements/{movement}` em `routes/web.php`
- [X] T016 [US6] Adicionar botão de exclusão/estorno de movimentação com SweetAlert2 em `resources/views/movements/show.blade.php`

**Checkpoint**: User Story 6 concluída. Exclusão de movimentações com estorno atômico finalizada.

---

## Phase 7: Polish & Testes Automatizados

**Propósito**: Garantia de qualidade e validação de regressão

- [X] T017 [P] Criar testes automatizados de perfil único e exclusão segura em `tests/Feature/UserRolesAndSafeDeletionTest.php`
- [X] T018 Executar suíte completa de testes automatizados via PHPUnit com `C:\xampp\php\php.exe artisan test`

---

## Dependencies & Execution Order

- **Fases 1 e 2** $\rightarrow$ **Fase 3 (MVP)** $\rightarrow$ **Fases 4 e 5** $\rightarrow$ **Fase 6** $\rightarrow$ **Fase 7 (Testes)**.
- `T005` e `T006` podem executar em paralelo após `T004`.
- `T010` (Materiais) e `T007` (Usuários) podem executar em paralelo.
