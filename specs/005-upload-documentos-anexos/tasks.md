# Tasks: Upload de Arquivos e Anexos em Entradas e Avarias

**Input**: Design documents from `/specs/005-upload-documentos-anexos/`
**Prerequisites**: [plan.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/005-upload-documentos-anexos/plan.md), [spec.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/005-upload-documentos-anexos/spec.md), [research.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/005-upload-documentos-anexos/research.md), [data-model.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/005-upload-documentos-anexos/data-model.md), [contracts/http-contracts.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/005-upload-documentos-anexos/contracts/http-contracts.md), [quickstart.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/005-upload-documentos-anexos/quickstart.md)

## Organization: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`
- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Criação da tabela polimórfica `attachments` e infraestrutura de storage.

- [x] T001 Criar migração da tabela polimórfica `attachments` em `database/migrations/2026_08_14_000002_create_attachments_table.php`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Componentes essenciais da camada de modelo, serviço e controller de anexos.

**⚠️ CRITICAL**: Nenhuma tarefa de user story pode ser iniciada antes da conclusão desta fase.

- [x] T002 Criar o modelo polimórfico `App\Models\Attachment` em `app/Models/Attachment.php`
- [x] T003 Criar a classe `App\Services\AttachmentService` em `app/Services/AttachmentService.php` para upload, salvamento e exclusão física do disco public
- [x] T004 Adicionar relacionamentos polimórficos `attachments()` e `attachment()` nos modelos `app/Models/EntryDocument.php` e `app/Models/Movement.php`
- [x] T005 Criar o controller `App\Http\Controllers\AttachmentController.php` com ações de download e exclusão de anexos

**Checkpoint**: Infraestrutura de anexos pronta - implementação das histórias liberada.

---

## Phase 3: User Story 1 - Anexo de Documentos Comprovatórios nas Entradas de Estoque (Priority: P1) 🎯 MVP

**Goal**: Permitir o upload de arquivos de Nota Fiscal / Doação no lançamento de entradas de estoque e exibição do anexo com download/visualização.

**Independent Test**: Registrar uma entrada anexando um arquivo PDF ou imagem, verificar o armazenamento e o link de acesso na listagem.

### Implementation for User Story 1

- [x] T006 [P] [US1] Atualizar `app/Http/Requests/StoreEntryRequest.php` incluindo validação do campo de arquivo `document_file` (max 10MB, mimes pdf/jpg/png/webp)
- [x] T007 [US1] Atualizar `app/Services/EntryService.php` para integrar com `AttachmentService` e salvar o anexo na criação da entrada
- [x] T008 [P] [US1] Atualizar `resources/views/entries/create.blade.php` com o atributo `enctype="multipart/form-data"` e campo input file para anexo de Nota Fiscal/Doação
- [x] T009 [P] [US1] Atualizar `resources/views/entries/index.blade.php` para exibir o ícone de anexo/clipe com link direto para download/visualização do comprovante

**Checkpoint**: User Story 1 totalmente funcional e testável de forma independente.

---

## Phase 4: User Story 2 - Anexo de Fotos e Laudos em Ocorrências de Avarias e Danos (Priority: P2)

**Goal**: Permitir o envio de fotos ou laudos técnicos no registro de baixas/ajustes por avarias e perdas de insumos ou ferramentas.

**Independent Test**: Realizar um ajuste de inventário motivado por avaria anexando a foto da peça danificada e verificar o vínculo da foto ao histórico.

### Implementation for User Story 2

- [x] T010 [P] [US2] Atualizar `app/Services/InventoryService.php` e `app/Services/StockService.php` para suportar salvamento de anexos de evidência de avaria
- [x] T011 [P] [US2] Criar partial modal `resources/views/partials/modal_attachment_preview.blade.php` para visualização inline de fotos e PDFs
- [x] T012 [US2] Atualizar a view de inventário `resources/views/inventories/show.blade.php` e modal de ajuste em `resources/views/materials/index.blade.php` incluindo campo para foto da avaria

**Checkpoint**: User Stories 1 e 2 funcionam de forma independente e integrada.

---

## Phase 5: User Story 3 - Visualização e Gerenciamento Unificado de Anexos (Priority: P3)

**Goal**: Exibir anexos nos relatórios gerenciais e permitir que administradores substituam ou removam anexos incorretos.

**Independent Test**: Acessar um registro com anexo incorreto como administrador, substituir o arquivo por um novo e verificar a atualização.

### Implementation for User Story 3

- [x] T013 [P] [US3] Atualizar `resources/views/reports/index.blade.php` exibindo o indicador de anexo e link direto nas tabelas de relatórios
- [x] T014 [US3] Adicionar rotas e ações em `app/Http/Controllers/AttachmentController.php` e interface para substituição/remoção de anexo por administradores

**Checkpoint**: Todas as User Stories (US1, US2, US3) estão funcionais.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Testes automatizados, verificação final e validação de conformidade.

- [x] T015 Criar teste automatizado em `tests/Feature/AttachmentUploadTest.php` cobrindo uploads, validações de tipo/tamanho, downloads e deleção
- [x] T016 Executar migrações e suíte de testes com `C:\xampp\php\php.exe artisan test`
- [x] T017 Executar o roteiro de validação end-to-end em `specs/005-upload-documentos-anexos/quickstart.md`

---

## Dependencies & Execution Order

### Phase Dependencies
- **Setup (Phase 1)**: Sem dependências - inicia imediatamente.
- **Foundational (Phase 2)**: Depende da Phase 1. BLOQUEIA todas as User Stories.
- **User Stories (Phases 3, 4, 5)**: Dependem da Phase 2. Podem ser executadas em sequência prioritária (P1 → P2 → P3) ou em paralelo.
- **Polish (Phase 6)**: Depende da conclusão das User Stories.

### Parallel Opportunities
- T006, T008, T009 podem ser executados em paralelo dentro da US1.
- T010, T011 podem ser executados em paralelo dentro da US2.
- T013 pode ser executado em paralelo dentro da US3.

---

## Implementation Strategy

### MVP First (User Story 1 Only)
1. Concluir Phase 1 (Setup) e Phase 2 (Foundational).
2. Concluir Phase 3 (User Story 1 - Anexo de Nota Fiscal / Doações em Entradas).
3. **VALIDAR**: Testar o upload e download de comprovantes de entrada.
4. Entregar o MVP funcional aos operadores.
