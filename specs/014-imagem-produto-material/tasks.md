# Tasks: Inclusão e Visualização de Imagem no Cadastro de Materiais

**Input**: Design documents from `specs/014-imagem-produto-material/` (`plan.md`, `spec.md`, `research.md`, `data-model.md`, `contracts/`, `quickstart.md`)

**Prerequisites**: `plan.md` (arquitetura e decisões), `spec.md` (histórias de usuário e critérios), `data-model.md` (modelo de dados e validações), `contracts/` (contratos HTTP)

**Tests**: Testes automatizados incluídos na fase de polimento (`tests/Feature/MaterialImageUploadTest.php`) e roteiro manual em `quickstart.md`.

**Organization**: Tarefas agrupadas por fases e por histórias de usuário para viabilizar implementação modular e testes independentes.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Tarefa paralelizada (arquivos independentes, sem dependência de tarefas incompletas)
- **[Story]**: História de usuário à qual a tarefa pertence ([US1], [US2], [US3])
- Caminhos absolutos/relativos de arquivos explicitados em cada descrição

---

## Phase 1: Setup (Infraestrutura Compartilhada)

**Purpose**: Criação da estrutura de banco de dados e configuração de armazenamento de imagens

- [x] T001 Criar migration para adicionar a coluna `image_path` (nullable string) na tabela `materials` em `database/migrations/2026_09_08_000001_add_image_path_to_materials_table.php`
- [x] T002 Executar a migração de banco de dados via Artisan (`C:\xampp\php\php.exe artisan migrate`)
- [x] T003 [P] Assegurar o link simbólico do storage público (`C:\xampp\php\php.exe artisan storage:link`) e diretório `storage/app/public/materials/images`

---

## Phase 2: Foundational (Pré-requisitos Bloqueantes)

**Purpose**: Modelo Eloquent e Camada de Serviço para manipulação segura de imagens

**⚠️ CRITICAL**: Nenhuma história de usuário pode ser concluída sem a finalização desta fundação.

- [x] T004 Atualizar o modelo `Material` em `app/Models/Material.php` com o campo `image_path` no array `$fillable`, o accessor `imageUrl` e o método auxiliar `hasImage(): bool`
- [x] T005 [P] Implementar a classe de serviço `MaterialImageService` em `app/Services/MaterialImageService.php` com métodos estritos para upload com UUID, substituição e exclusão física no disco `public`

**Checkpoint**: Fundação pronta — modelo e serviço disponíveis para controllers e views.

---

## Phase 3: User Story 1 - Upload e Gestão de Imagem no Cadastro/Edição (Priority: P1) 🎯 MVP

**Goal**: Permitir que operadores anexem uma imagem válida no cadastro/edição de materiais, com validação estrita (máx 5MB, apenas imagens), preservação de imagem existente e opção de substituição ou remoção.

**Independent Test**: Cadastrar um material enviando arquivo de imagem (.png/.jpg) e constatar gravação com sucesso; tentar enviar arquivo inválido (.pdf/.txt) e verificar bloqueio com erro amigável; editar material alterando outros campos sem perder a imagem existente, ou optar por substituí-la/removê-la.

### Implementation for User Story 1

- [x] T006 [US1] Atualizar as regras de validação e atributos amigáveis em `app/Http/Requests/StoreMaterialRequest.php` para suportar o campo `image` com validação de imagem (`image`, `mimes:jpeg,png,jpg,webp,gif`, `max:5120`)
- [x] T007 [US1] Injetar `MaterialImageService` e atualizar os métodos `store`, `update` e `destroy` no controlador `app/Http/Controllers/MaterialController.php` para persistir, substituir ou remover imagens físicas
- [x] T008 [US1] Atualizar os modais `#modalCreateMaterial` e `#modalEditMaterial` em `resources/views/materials/index.blade.php` com `enctype="multipart/form-data"`, input de arquivo de imagem, preview dinâmico em JavaScript e controle de remoção de imagem

**Checkpoint**: User Story 1 funcional de ponta a ponta (upload, validação, persistência e limpeza física).

---

## Phase 4: User Story 2 - Exibição de Miniatura na Tabela de Materiais (Priority: P1) 🎯 MVP

**Goal**: Exibir miniatura da foto do material diretamente na tabela da listagem do catálogo (`/materials`) e exibir placeholder elegante quando o item não tiver imagem cadastrada.

**Independent Test**: Acessar `/materials` e constatar que a tabela renderiza a coluna de foto com miniaturas de 45x45px nítidas e enquadradas para itens com foto, e ícone neutro alinhado para itens sem foto.

### Implementation for User Story 2

- [x] T009 [US2] Adicionar coluna "Foto" na tabela de materiais em `resources/views/materials/index.blade.php`, renderizando thumbnail responsivo (45x45px com `object-fit: cover`) para materiais com imagem e placeholder padronizado para materiais sem foto

**Checkpoint**: Tabela exibe catálogo visual completo e harmonioso com as miniaturas.

---

## Phase 5: User Story 3 - Visualização Ampliada da Imagem (Priority: P2)

**Goal**: Permitir que o usuário clique na miniatura ou ícone de visualização para abrir um modal em destaque com a imagem ampliada em alta resolução, nome e SKU do material.

**Independent Test**: Clicar sobre a miniatura de qualquer material com foto na tabela e verificar abertura de modal responsivo com a foto ampliada sem distorção, fechando via ESC ou botão de fechar sem rolar a página.

### Implementation for User Story 3

- [x] T010 [US3] Criar o componente de modal `#modalMaterialImagePreview` em `resources/views/partials/modal_material_image_preview.blade.php` com layout responsivo e proporção original preservada
- [x] T011 [US3] Incluir o modal e implementar os event listeners de clique nas miniaturas da tabela em `resources/views/materials/index.blade.php` para preencher dinamicamente título, SKU e URL da imagem

**Checkpoint**: Todas as 3 histórias de usuário plenamente integradas e funcionais.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Testes automatizados, limpeza de código e validação completa end-to-end

- [x] T012 [P] Atualizar ou criar testes de integração em `tests/Feature/MaterialImageUploadTest.php` cobrindo upload de imagem válida, validação contra arquivos não-imagem e remoção física no disco
- [x] T013 Executar o roteiro de validação end-to-end definido em `specs/014-imagem-produto-material/quickstart.md` e validar layout responsivo no navegador

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências — execução imediata.
- **Foundational (Phase 2)**: Depende da Phase 1 — BLOQUEIA a implementação das histórias de usuário.
- **User Story 1 (Phase 3)**: Depende da Phase 2 — Permite cadastrar e gerenciar materiais com imagens.
- **User Story 2 (Phase 4)**: Depende da Phase 3 (para haver materiais com foto e URL) e da Phase 2.
- **User Story 3 (Phase 5)**: Depende da Phase 4 (para interagir com as miniaturas renderizadas na tabela).
- **Polish (Phase 6)**: Depende da conclusão das histórias de usuário.

### Parallel Opportunities

- `T003` (Setup) pode rodar em paralelo com `T001`/`T002`.
- `T005` (`MaterialImageService`) pode ser implementado em paralelo com `T004` (`Material.php`).
- `T010` (Template do modal de ampliação) pode ser desenvolvido em paralelo com `T009`.
- `T012` (Testes automatizados) pode ser elaborado em paralelo com as validações finais.

---

## Implementation Strategy

### MVP First (User Stories 1 & 2)
1. Concluir Phase 1 (Setup) e Phase 2 (Foundational).
2. Concluir Phase 3 (US1 - Upload e Gestão de Imagens).
3. Concluir Phase 4 (US2 - Exibição de Miniaturas na Tabela).
4. **Validar MVP**: Cadastrar materiais com foto e visualizar miniaturas na listagem.
5. Concluir Phase 5 (US3 - Modal Ampliado).
6. Concluir Phase 6 (Testes e Polimento).
