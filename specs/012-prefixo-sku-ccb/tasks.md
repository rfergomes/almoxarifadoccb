---
description: "Lista de tarefas para alteração do prefixo de SKU automático de GEN-xxx para CCB-xxx"
---

# Tasks: Alteração do Prefixo de SKU Automático de GEN-xxx para CCB-xxx

**Input**: Design documents from `/specs/012-prefixo-sku-ccb/`  
**Prerequisites**: [plan.md](plan.md), [spec.md](spec.md), [research.md](research.md), [data-model.md](data-model.md), [contracts/material-api.md](contracts/material-api.md)

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Pode executar em paralelo (arquivos diferentes, sem dependências)
- **[Story]**: História de usuário à qual a tarefa pertence ([US1], [US2], [US3])
- Caminhos de arquivos exatos em todas as tarefas

---

## Phase 1: Setup (Infraestrutura)

**Purpose**: Preparação do ambiente e alinhamento dos testes

- [x] T001 Preparar ambiente e verificar casos de teste de SKU em tests/Feature/MaterialSkuAndDeletionFixTest.php

---

## Phase 2: User Story 1 - Geração Automática de SKU com Prefixo Institucional CCB-### (Priority: P1) 🎯 MVP

**Goal**: Fazer com que novos materiais cadastrados sem código SKU recebam o prefixo institucional CCB-### (ex: CCB-001, CCB-002, CCB-010)

**Independent Test**: Criar um novo material sem informar SKU e validar se é persistido com código CCB-001 (ou sequencial subsequente)

- [x] T002 [US1] Atualizar método generateNextSku() para buscar CCB-% e gerar CCB-### em app/Models/Material.php
- [x] T003 [US1] Atualizar casos de teste para validar geração de CCB-001, incremento para CCB-010 e cadastro rápido em tests/Feature/MaterialSkuAndDeletionFixTest.php

**Checkpoint**: Novos materiais geram automaticamente o código no formato CCB-###

---

## Phase 3: User Story 2 - Preservação de Códigos Manuais e Compatibilidade (Priority: P2)

**Goal**: Garantir que códigos manuais informados pelo operador continuem sendo rigorosamente preservados

**Independent Test**: Cadastrar material com SKU customizado (ex: TINTA-CORAL-18L) e verificar que o código não é substituído por CCB-

- [x] T004 [US2] Assegurar execução do teste de preservação de código SKU customizado em tests/Feature/MaterialSkuAndDeletionFixTest.php

**Checkpoint**: Códigos manuais e legados convivem sem conflitos com o novo padrão CCB-

---

## Phase 4: User Story 3 - Atualização Visual de Dicas e Placeholders nas Interfaces (Priority: P3)

**Goal**: Atualizar os textos de orientação e placeholders nos modais para exibir CCB-001

**Independent Test**: Abrir os modais de cadastro de materiais e de cadastro rápido e inspecionar o texto de exemplo do campo SKU

- [x] T005 [P] [US3] Atualizar placeholder do campo SKU no modal principal em resources/views/materials/index.blade.php
- [x] T006 [P] [US3] Atualizar placeholder do campo SKU no modal rápido em resources/views/partials/modal_quick_material.blade.php

**Checkpoint**: Interface visual alinhada ao novo prefixo oficial

---

## Phase 5: Polish & Cross-Cutting Concerns

**Purpose**: Execução da suíte de testes e validação funcional

- [x] T007 Executar suíte de testes da feature via php artisan test tests/Feature/MaterialSkuAndDeletionFixTest.php
- [x] T008 Executar validação manual de cadastro conforme specs/012-prefixo-sku-ccb/quickstart.md

---

## Dependencies & Execution Order

### Phase Dependencies
- **Setup (Phase 1)**: Concluída.
- **US1 (Phase 2)**: Concluída — Model e testes atualizados com sucesso.
- **US2 (Phase 3)**: Concluída — Preservação de códigos customizados validada.
- **US3 (Phase 4)**: Concluída — Placeholders atualizados para CCB-001.
- **Polish (Phase 5)**: Concluída — 75 testes aprovados sem falhas.

### Parallel Opportunities
- Tarefas T005 e T006 foram executadas em paralelo com sucesso.
