---
description: "Lista de tarefas para implementação da correção de exclusão de material, notificações e SKU sequencial"
---

# Tasks: Correção de Exclusão de Material, Notificações e SKU Sequencial

**Input**: Design documents from `/specs/011-correcao-exclusao-material/`  
**Prerequisites**: [plan.md](plan.md), [spec.md](spec.md), [research.md](research.md), [data-model.md](data-model.md), [contracts/material-api.md](contracts/material-api.md)

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Pode executar em paralelo (arquivos diferentes, sem dependências)
- **[Story]**: História de usuário à qual a tarefa pertence ([US1], [US2], [US3], [US4])
- Caminhos de arquivos exatos em todas as tarefas

---

## Phase 1: Setup (Infraestrutura Compartilhada)

**Purpose**: Preparação do ambiente de testes e validação das rotas envolvidas

- [x] T001 Criar estrutura do arquivo de testes automatizados em tests/Feature/MaterialSkuAndDeletionFixTest.php

---

## Phase 2: Foundational (Pré-requisitos Bloqueantes)

**Purpose**: Correções críticas de assets e service worker que desbloqueiam a execução dos scripts de interface

**⚠️ CRITICAL**: A correção do script Toastr e do Service Worker é pré-requisito para o funcionamento correto das notificações e janelas modais de confirmação na interface

- [x] T002 Corrigir importação do script Toastr substituindo toastr.min.css por toastr.min.js em resources/views/layouts/app.blade.php
- [x] T003 Implementar filtro de protocolo HTTP/HTTPS e tratamento seguro de catch no fetch do Service Worker em public/sw.js

**Checkpoint**: Base de scripts e cache estabilizada — a implementação das histórias de usuário pode prosseguir

---

## Phase 3: User Story 1 - Exclusão Segura e Responsiva de Material Elegível (Priority: P1) 🎯 MVP

**Goal**: Permitir que o Administrador exclua materiais elegíveis (saldo zero e sem movimentações) com confirmação via SweetAlert2 e feedback imediato de sucesso

**Independent Test**: Cadastrar um material com estoque 0 e sem movimentações, clicar em "Excluir", confirmar no modal SweetAlert2 e validar se o registro é removido com notificação Toastr de sucesso

- [x] T004 [US1] Criar teste automatizado para exclusão permitida de material elegível por Administrador em tests/Feature/MaterialSkuAndDeletionFixTest.php
- [x] T005 [US1] Validar integração da ação destroy e submissão do formulário SweetAlert2 em resources/views/materials/index.blade.php e app/Http/Controllers/MaterialController.php

**Checkpoint**: Exclusão de materiais elegíveis 100% funcional e testada de forma independente

---

## Phase 4: User Story 2 - Feedback Claro de Impedimento na Exclusão (Priority: P2)

**Goal**: Garantir que tentativas de exclusão de materiais com estoque físico ou histórico de movimentação/inventário sejam bloqueadas e apresentem alertas claros ao operador

**Independent Test**: Tentar excluir um material com saldo físico > 0 ou com movimentações registradas e verificar o retorno de alerta flash de erro explicando o impedimento

- [x] T006 [US2] Criar teste automatizado de bloqueio de exclusão para materiais com saldo ou movimentações em tests/Feature/MaterialSkuAndDeletionFixTest.php
- [x] T007 [US2] Assegurar retorno das mensagens flash de erro orientando a inativação do item em app/Http/Controllers/MaterialController.php

**Checkpoint**: Regras de integridade de exclusão física protegidas e com feedback ao usuário

---

## Phase 5: User Story 3 - Código SKU Opcional com Geração Sequencial Automática (Priority: P2)

**Goal**: Tornar o campo Código SKU opcional no cadastro de materiais e gerar automaticamente o código sequencial GEN-### (ex: GEN-001, GEN-002, etc.) quando não informado

**Independent Test**: Cadastrar um novo material com campo SKU em branco e verificar se ele é persistido automaticamente com o próximo sequencial GEN-###; em seguida cadastrar outro com SKU manual e verificar a preservação do código informado

- [x] T008 [US3] Criar testes automatizados para geração de SKU sequencial automático e preservação de SKU manual em tests/Feature/MaterialSkuAndDeletionFixTest.php
- [x] T009 [P] [US3] Alterar regra de validação do campo code_sku para nullable em app/Http/Requests/StoreMaterialRequest.php
- [x] T010 [US3] Implementar método generateNextSku() e gancho creating no evento booted() em app/Models/Material.php
- [x] T011 [P] [US3] Atualizar rótulo para opcional, remover atributo required e definir placeholder do SKU no modal principal em resources/views/materials/index.blade.php
- [x] T012 [P] [US3] Atualizar rótulo para opcional, remover atributo required e definir placeholder do SKU no modal rápido em resources/views/partials/modal_quick_material.blade.php

**Checkpoint**: SKU opcional com sequência automática funcionando tanto no cadastro padrão quanto no cadastro rápido

---

## Phase 6: User Story 4 - Estabilidade de Notificações e Isolamento de Cache do Navegador (Priority: P3)

**Goal**: Garantir que as notificações Toastr globais sejam exibidas sem erros de script e que extensões do Chrome não disparem falhas no Service Worker

**Independent Test**: Disparar mensagens de sucesso, aviso e erro de sessão e verificar renderização limpa do Toastr no topo da tela sem qualquer erro de console

- [x] T013 [US4] Criar teste automatizado garantindo que toastr.min.js é carregado no layout HTML em tests/Feature/MaterialSkuAndDeletionFixTest.php
- [x] T014 [US4] Validar resiliência do Service Worker para requisições de extensões e navegação offline em public/sw.js

**Checkpoint**: Console de scripts 100% limpo de exceções e notificações Toastr em pleno funcionamento

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Verificação integrada, execução da suíte de testes e validação funcional

- [x] T015 Executar suíte completa de testes da funcionalidade via php artisan test tests/Feature/MaterialSkuAndDeletionFixTest.php
- [x] T016 Executar validação manual de ponta a ponta conforme roteiro em specs/011-correcao-exclusao-material/quickstart.md

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências - inicia imediatamente.
- **Foundational (Phase 2)**: Depende do Setup - BLOQUEIA os testes e execução das histórias de usuário.
- **User Stories (Phase 3 a Phase 6)**: Dependem da conclusão da Fase Foundational.
  - US1 (P1 - Exclusão de Material Elegível): MVP principal.
  - US2 (P2 - Bloqueio de Exclusão com Estoque/Movimentação): complementa a integridade de exclusão.
  - US3 (P2 - SKU Opcional e Sequencial Automático): implementa a nova solicitação de cadastro.
  - US4 (P3 - Notificações e Service Worker): consolida a estabilidade do front-end.
- **Polish (Phase 7)**: Depende da conclusão de todas as histórias.

### Parallel Opportunities

- Tarefas T009, T011 e T012 na Fase 5 foram executadas com sucesso em arquivos distintos (`StoreMaterialRequest.php`, `materials/index.blade.php`, `modal_quick_material.blade.php`).
- Tarefas T002 e T003 corrigiram arquivos independentes (`layouts/app.blade.php` e `sw.js`).

---

## Implementation Strategy

### MVP First (User Story 1 & Foundational)
1. Concluir Phase 1 e Phase 2 (correção do Toastr JS e Service Worker).
2. Concluir Phase 3 (US1 - Exclusão segura de materiais).
3. Testar a exclusão e verificar o feedback no navegador.

### Entrega Incremental
1. Adicionar Phase 4 (US2 - Bloqueio e mensagens de integridade).
2. Adicionar Phase 5 (US3 - Código SKU opcional e sequencial automático `GEN-###`).
3. Adicionar Phase 6 (US4 - Consolidação de notificações).
4. Executar Phase 7 (Bateria completa de testes automatizados e checklist).
