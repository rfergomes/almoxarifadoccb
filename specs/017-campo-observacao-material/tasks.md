---

description: "Task list for implementing feature 017-campo-observacao-material"
---

# Tasks: Campo de Observação no Cadastro e Gestão de Materiais

**Input**: Design documents from `specs/017-campo-observacao-material/`  
**Prerequisites**: `plan.md`, `spec.md`, `research.md`, `data-model.md`, `contracts/material-notes-contract.md`, `quickstart.md`  
**Organization**: Tarefas agrupadas por fases e User Stories (P1 - MVP) para permitir implementação e testes independentes.  

## Format: `[ID] [P?] [Story?] Description with file path`

- **[P]**: Tarefas executáveis em paralelo (arquivos distintos, sem dependência mútua)
- **[Story]**: Mapeamento para as histórias de usuário do `spec.md` (`[US1]`, `[US2]`)
- Arquivos com caminhos completos relativos à raiz do projeto

---

## Phase 1: Setup (Infraestrutura de Banco de Dados)

**Purpose**: Criação do esquema de banco de dados para a coluna de observações

- [x] T001 Criar migration incremental para adicionar coluna `notes` (tipo text, nullable) na tabela `materials` em `database/migrations/2026_09_18_000001_add_notes_to_materials_table.php`
- [x] T002 Executar a migração do banco de dados via comando `php artisan migrate`

---

## Phase 2: Foundational (Model Eloquent & Validação)

**Purpose**: Estrutura base de dados e validações que suportam todas as histórias de usuário

**⚠️ CRITICAL**: Pré-requisito obrigatório para implementação das telas e testes de usuário

- [x] T003 [P] Adicionar atributo `notes` ao `$fillable` e implementar helper `hasNotes(): bool` no Model em `app/Models/Material.php`
- [x] T004 [P] Atualizar regras de validação para `notes` (`nullable|string|max:2000`) e atributo amigável em `app/Http/Requests/StoreMaterialRequest.php`

**Checkpoint**: Camada base de dados e validação pronta. As histórias de usuário podem ser desenvolvidas.

---

## Phase 3: User Story 1 - Inserção e Atualização de Observações no Cadastro (Priority: P1) 🎯 MVP

**Goal**: Permitir que operadores insiram, editem ou removam anotações complementares nos formulários de criação padrão, edição e criação rápida de materiais.

**Independent Test**: Acessar o modal de criação ou edição de material, preencher observações com quebras de linha, submeter o formulário e constatar que o texto persiste inalterado e pode ser editado/limpo.

### Tests for User Story 1 ⚠️

- [x] T005 [P] [US1] Criar testes de Feature cobrindo criação com observação, atualização, limpeza para null e bloqueio de texto superior a 2.000 caracteres em `tests/Feature/MaterialObservationTest.php`

### Implementation for User Story 1

- [x] T006 [US1] Adicionar campo `<textarea name="notes">` com rótulo e placeholder explicativo no modal de criação `#modalCreateMaterial` em `resources/views/materials/index.blade.php`
- [x] T007 [US1] Adicionar regra de validação `'notes' => ['nullable', 'string', 'max:2000']` no método `update` de `app/Http/Controllers/MaterialController.php`
- [x] T008 [US1] Adicionar campo `<textarea name="notes" id="edit_notes">` no `#modalEditMaterial`, atributo `data-notes` no botão `.btn-edit-material` e preenchimento dinâmico via JavaScript em `resources/views/materials/index.blade.php`
- [x] T009 [US1] Adicionar campo de observações no modal de cadastro rápido em `resources/views/partials/modal_quick_material.blade.php` e incluir retorno do campo `notes` na resposta JSON em `app/Http/Controllers/QuickRegistrationController.php`

**Checkpoint**: User Story 1 funcional e testável de forma independente (MVP completo de persistência de dados).

---

## Phase 4: User Story 2 - Consulta e Leitura da Observação no Catálogo (Priority: P1)

**Goal**: Permitir a consulta clara e elegante das observações na listagem de materiais sem prejudicar a diagramação da tabela, além de permitir busca por texto de anotação.

**Independent Test**: Cadastrar um material com observação e constatar a renderização do ícone de anotação com Tooltip interativo contendo a íntegra da observação e quebras de linha preservadas.

### Tests for User Story 2 ⚠️

- [x] T010 [P] [US2] Criar teste de Feature validando a renderização do ícone de anotações e exibição correta de materiais com e sem observações na listagem em `tests/Feature/MaterialObservationTest.php`

### Implementation for User Story 2

- [x] T011 [US2] Implementar ícone indicador com Tooltip Bootstrap 5 (`bi bi-chat-left-text-fill`) junto ao nome do material na coluna de visualização em `resources/views/materials/index.blade.php`
- [x] T012 [US2] Adicionar verificação de busca por `notes` no filtro de pesquisa textual de `MaterialController::index` em `app/Http/Controllers/MaterialController.php`

**Checkpoint**: User Stories 1 e 2 plenamente operacionais e integradas.

---

## Phase 5: Polish & Cross-Cutting Concerns

**Purpose**: Verificação de regressão, testes automatizados e conferência final de qualidade

- [x] T013 Executar suíte completa de testes automatizados da funcionalidade via `C:\xampp\php\php.exe artisan test --filter=MaterialObservationTest`
- [x] T014 Executar roteiro de validação ponta a ponta descrito em `specs/017-campo-observacao-material/quickstart.md`

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências prévias — inicia imediatamente (criação da migration e execução no banco).
- **Foundational (Phase 2)**: Depende da Phase 1 — BLOQUEIA a implementação das User Stories.
- **User Story 1 (Phase 3)**: Depende da Phase 2 — Entrega o fluxo central de gravação (MVP).
- **User Story 2 (Phase 4)**: Depende da Phase 2 e integra-se à visualização do que é gravado pela US1.
- **Polish (Phase 5)**: Depende da conclusão das fases 3 e 4.

### Parallel Opportunities

- `T003` e `T004` (Model e FormRequest) podem ser implementados em paralelo.
- `T005` (Testes automatizados da US1) pode ser escrito em paralelo com as alterações de views.
- `T006`, `T008` e `T009` tratam de seções específicas de views, com escopos bem delimitados.

---

## Implementation Strategy

### MVP First (User Story 1)
1. Concluir Setup (Phase 1) e Foundational (Phase 2).
2. Concluir User Story 1 (Phase 3).
3. Validar gravação e persistência do campo de observação no banco.
4. Concluir User Story 2 (Phase 4) para exibição elegante e pesquisa.
5. Executar os testes automatizados e o roteiro do quickstart (Phase 5).
