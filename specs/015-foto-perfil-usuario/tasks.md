# Tasks: Perfil de Usuário com Foto e Exibição no Header e Tabela

**Input**: Design documents from `specs/015-foto-perfil-usuario/` (`plan.md`, `spec.md`, `research.md`, `data-model.md`, `contracts/`, `quickstart.md`)

**Prerequisites**: `plan.md` (arquitetura e decisões), `spec.md` (histórias de usuário e critérios), `data-model.md` (modelo de dados e validações), `contracts/` (contratos HTTP)

**Tests**: Testes automatizados incluídos na fase de polimento (`tests/Feature/UserProfileTest.php`) e roteiro manual em `quickstart.md`.

**Organization**: Tarefas agrupadas por fases e histórias de usuário para viabilizar implementação modular e testes independentes.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Tarefa paralelizada (arquivos independentes, sem dependência de tarefas incompletas)
- **[Story]**: História de usuário à qual a tarefa pertence ([US1], [US2], [US3])
- Caminhos absolutos/relativos de arquivos explicitados em cada descrição

---

## Phase 1: Setup (Infraestrutura Compartilhada)

**Purpose**: Criação da coluna no banco de dados e configuração de armazenamento de avatares

- [x] T001 Criar migration para adicionar a coluna `avatar_path` (nullable string) na tabela `users` em `database/migrations/2026_09_08_000002_add_avatar_path_to_users_table.php`
- [x] T002 Executar a migração de banco de dados via Artisan (`C:\xampp\php\php.exe artisan migrate`)
- [x] T003 [P] Assegurar a existência do diretório `storage/app/public/avatars` e link simbólico do storage público

---

## Phase 2: Foundational (Pré-requisitos Bloqueantes)

**Purpose**: Modelo Eloquent, Camada de Serviços e FormRequests para gestão de perfil

**⚠️ CRITICAL**: Nenhuma história de usuário pode ser finalizada sem a conclusão desta fundação.

- [x] T004 Atualizar o modelo `User` em `app/Models/User.php` adicionando o campo `avatar_path` no array `$fillable`, o accessor `avatarUrl`, e os métodos auxiliares `hasAvatar(): bool` e `initials(): string`
- [x] T005 [P] Implementar a classe de serviço `UserAvatarService` em `app/Services/UserAvatarService.php` com métodos para upload com UUID, substituição e exclusão física no disco `public`
- [x] T006 [P] Criar classes FormRequest para validação em `app/Http/Requests/UpdateProfileRequest.php` (nome e imagem até 5MB) e `app/Http/Requests/UpdatePasswordRequest.php` (senha atual e nova confirmação)

**Checkpoint**: Fundação pronta — modelo, serviço e validações disponíveis para controllers e views.

---

## Phase 3: User Story 1 - Página de Perfil e Gestão de Dados Pessoais (Priority: P1) 🎯 MVP

**Goal**: Disponibilizar página `/profile` moderna e elegante para que o usuário autenticado possa corrigir seu nome, enviar ou remover sua foto de perfil e atualizar sua senha com validação em tempo real.

**Independent Test**: Acessar `/profile`, alterar o nome e fazer upload de foto válida (.png/.jpg) e salvar; testar erro amigável ao tentar enviar arquivo não-imagem; testar remoção da foto e alteração de senha de acesso.

### Implementation for User Story 1

- [x] T007 [US1] Criar o controlador `app/Http/Controllers/ProfileController.php` com ações `edit`, `update` (dados e avatar via `UserAvatarService`) e `updatePassword`
- [x] T008 [US1] Registrar as rotas autenticadas de perfil (`GET /profile`, `PUT /profile`, `PUT /profile/password`) em `routes/web.php`
- [x] T009 [US1] Criar a view Blade `resources/views/profile/edit.blade.php` com layout elegante em AdminLTE v4, card de identificação visual, preview dinâmico de foto, botão de remoção de foto e aba de segurança/senha

**Checkpoint**: User Story 1 funcional de ponta a ponta (página de perfil ativa e responsiva).

---

## Phase 4: User Story 2 - Exibição do Avatar no Header da Aplicação (Priority: P1) 🎯 MVP

**Goal**: Exibir o avatar circular personalizado do usuário autenticado no canto superior direito do header em todas as páginas, e adicionar atalho "Meu Perfil" no dropdown.

**Independent Test**: Acessar qualquer tela do sistema e verificar se o cabeçalho renderiza a foto circular do usuário (ou avatar com iniciais/ícone neutro caso não possua foto) e se o menu dropdown direciona para `/profile`.

### Implementation for User Story 2

- [x] T010 [US2] Atualizar o componente de navegação em `resources/views/partials/navbar.blade.php` para renderizar o avatar circular do usuário logado (`Auth::user()->avatar_url`) e adicionar o item "Meu Perfil" no menu dropdown

**Checkpoint**: Header exibe a identidade visual do usuário com link direto para o perfil.

---

## Phase 5: User Story 3 - Exibição da Foto na Tabela de Gestão de Usuários (Priority: P2)

**Goal**: Exibir o avatar circular de cada usuário cadastrado na tabela de usuários do sistema (`/users`) e permitir que administradores visualizem e gerenciem fotos de perfil.

**Independent Test**: Acessar `/users` como administrador e constatar que cada linha exibe o avatar circular do respectivo usuário ao lado do nome em vez do ícone genérico.

### Implementation for User Story 3

- [x] T011 [US3] Atualizar a tabela de usuários em `resources/views/users/index.blade.php` para exibir o avatar circular correspondente a cada usuário ao lado do nome na listagem
- [x] T012 [US3] Atualizar `app/Http/Controllers/UserController.php` injetando `UserAvatarService` para remover o arquivo de avatar ao excluir usuário e permitir gerenciamento administrativo de foto

**Checkpoint**: Todas as 3 histórias de usuário integradas e funcionais.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Testes automatizados, validação de responsividade e conferência de qualidade

- [x] T013 [P] Criar suíte de testes automatizados em `tests/Feature/UserProfileTest.php` cobrindo exibição do perfil, atualização de nome, upload de foto, rejeição de formatos inválidos, remoção de avatar e alteração de senha
- [x] T014 Executar o roteiro de validação end-to-end definido em `specs/015-foto-perfil-usuario/quickstart.md` e testar responsividade mobile e desktop

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências — execução imediata.
- **Foundational (Phase 2)**: Depende da Phase 1 — BLOQUEIA as histórias de usuário.
- **User Story 1 (Phase 3)**: Depende da Phase 2 — Entrega a tela de perfil e endpoints de salvamento.
- **User Story 2 (Phase 4)**: Depende da Phase 2 e Phase 3 — Header consome a foto do usuário e aponta para a rota de perfil.
- **User Story 3 (Phase 5)**: Depende da Phase 2 — Exibição na listagem administrativa e exclusão limpa.
- **Polish (Phase 6)**: Depende da implementação das histórias de usuário.

### Parallel Opportunities

- `T003` (Setup) pode rodar em paralelo com `T001`/`T002`.
- `T005` (`UserAvatarService`) e `T006` (`FormRequests`) podem ser implementados em paralelo com `T004`.
- `T010` (Header Navbar) pode ser ajustado em paralelo com a criação da view de perfil `T009`.
- `T013` (Testes automatizados) pode ser elaborado em paralelo com `T014`.

---

## Implementation Strategy

### MVP First (User Stories 1 & 2)
1. Concluir Phase 1 (Setup) e Phase 2 (Foundational).
2. Concluir Phase 3 (US1 - Página de Perfil e Edição de Dados).
3. Concluir Phase 4 (US2 - Avatar no Header e Menu Dropdown).
4. **Validar MVP**: Acessar o perfil, trocar foto e nome, conferir reflexo imediato no header.
5. Concluir Phase 5 (US3 - Avatar na Tabela de Usuários).
6. Concluir Phase 6 (Testes Automatizados e Polimento).
