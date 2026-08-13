# Tasks: Responsividade Total & PWA (Progressive Web App)

**Input**: Design documents from `/specs/003-responsive-pwa/`
**Prerequisites**: [`plan.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/plan.md), [`spec.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/spec.md), [`research.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/research.md), [`data-model.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/data-model.md), [`contracts/pwa-manifest-contract.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/contracts/pwa-manifest-contract.md)

## Format: `- [x] [TaskID] [P?] [Story?] Description with file path`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Assets base e diretórios para PWA e suporte responsivo

- [x] T001 Criar estrutura de diretórios para ícones PWA em `public/images/icons/`
- [x] T002 [P] Gerar ícone PWA institucional de 192x192px em `public/images/icons/icon-192x192.png`
- [x] T003 [P] Gerar ícone PWA institucional de 512x512px em `public/images/icons/icon-512x512.png`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Configuração base de layout Blade e folha de estilos responsiva customizada

- [x] T004 Configurar meta tags de Viewport, PWA e cores de tema em `resources/views/layouts/app.blade.php`
- [x] T005 [P] Criar stylesheet customizado de responsividade em `resources/css/responsive-custom.css`
- [x] T006 Incluir a folha de estilos `responsive-custom.css` no pipeline de build do Vite em `vite.config.js`

**Checkpoint**: Infraestrutura de layout e estilos base pronta para adaptação das User Stories.

---

## Phase 3: User Story 1 - Experiência Mobile Responsiva Completa no Almoxarifado (Priority: P1) 🎯 MVP

**Goal**: Permitir a navegação e operação completa do sistema em smartphones e tablets sem estouro de tela (0px overflow em 320px).

- [x] T007 [P] [US1] Ajustar comportamento responsivo da barra de navegação e sidebar AdminLTE v4 em `resources/views/layouts/app.blade.php`
- [x] T008 [P] [US1] Otimizar formulários e seletores Select2 para empilhamento touch de 1 coluna em `resources/views/movements/create.blade.php`
- [x] T009 [P] [US1] Otimizar modais inline de cadastro rápido de beneficiários, fornecedores e materiais para tela cheia em mobile em `resources/views/movements/modals/`
- [x] T010 [P] [US1] Aplicar wrappers de tabelas responsivas `.table-responsive` e toque otimizado na listagem de estoque em `resources/views/materials/index.blade.php`
- [x] T011 [P] [US1] Aplicar adaptação responsiva na listagem de empréstimos e devoluções em `resources/views/movements/index.blade.php`
- [x] T012 [P] [US1] Adaptar componentes do painel/dashboard para exibição em grade de 1 coluna em mobile em `resources/views/dashboard/index.blade.php`
- [x] T013 [US1] Adicionar regras CSS de áreas de toque mínimas (44x44px) e ajuste de tabelas touch em `resources/css/responsive-custom.css`

**Checkpoint**: User Story 1 funcional e testável de forma independente em qualquer dispositivo móvel.

---

## Phase 4: User Story 2 - Instalação do Aplicativo e Suporte PWA (Priority: P2)

**Goal**: Permitir a instalação do sistema como um aplicativo nativo (PWA) na tela inicial em Android, iOS, Windows e macOS.

- [x] T014 [P] [US2] Criar o arquivo de Manifesto de Aplicativo Web em `public/manifest.json` com especificações W3C e temas CCB
- [x] T015 [P] [US2] Vincular o `manifest.json` e as meta tags específicas de iOS no cabeçalho de `resources/views/layouts/app.blade.php`
- [x] T016 [P] [US2] Criar o script JS de registro de Service Worker e captura do evento `beforeinstallprompt` em `resources/js/pwa-register.js`
- [x] T017 [US2] Incluir o script `pwa-register.js` no arquivo de layout mestre `resources/views/layouts/app.blade.php`

**Checkpoint**: Aplicativo PWA instalável e operacional em modo standalone em múltiplos sistemas operacionais.

---

## Phase 5: User Story 3 - Suporte Offline Elegante e Cache de Recursos Estáticos (Priority: P3)

**Goal**: Garantir carregamento instantâneo de assets via cache e exibir uma tela explicativa de contingência em caso de desconexão.

- [x] T018 [P] [US3] Criar a página HTML estática de fallback offline institucional em `public/offline.html`
- [x] T019 [P] [US3] Criar o Service Worker em `public/sw.js` com estratégia Stale-While-Revalidate para assets estáticos e fallback para `offline.html`
- [x] T020 [US3] Configurar as regras de segurança e escopo do Service Worker em `public/sw.js` garantindo isolamento de requisições POST mutáveis

**Checkpoint**: Todas as user stories (US1, US2 e US3) implementadas e testáveis de forma independente.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Otimizações finais, compilação de produção e validação do guia de testes.

- [x] T021 [P] Executar compilação de assets de produção via `pnpm run build` / `npx vite build`
- [x] T022 Executar a suíte de testes de integração Laravel via `php artisan test`
- [x] T023 Validar todos os cenários de teste descritos no guia de rápida validação [`quickstart.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/quickstart.md)
