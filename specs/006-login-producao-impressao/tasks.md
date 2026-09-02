# Tasks: Adequação da Tela de Login para Produção e Correção da Impressão Direta

**Input**: Design documents from `specs/006-login-producao-impressao/`
**Prerequisites**: [plan.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/006-login-producao-impressao/plan.md), [spec.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/006-login-producao-impressao/spec.md), [research.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/006-login-producao-impressao/research.md), [data-model.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/006-login-producao-impressao/data-model.md), [contracts/](file:///d:/xampp/htdocs/almoxarifadoccb/specs/006-login-producao-impressao/contracts/)

## Format: `[ID] [P?] [Story] Description`
- **[P]**: Tarefas executáveis em paralelo (arquivos distintos, sem dependência mútua)
- **[Story]**: Mapeamento para as histórias de usuário ([US1], [US2], [US3])
- Caminhos absolutos/relativos exatos incluídos em cada descrição

---

## Phase 1: Setup (Infraestrutura Compartilhada)

**Purpose**: Preparação do ambiente de execução e build de assets

- [X] T001 Verificar ambiente e dependências de build em `package.json` e `vite.config.js`

---

## Phase 2: Foundational (Pré-requisitos de Estilos de Impressão)

**Purpose**: Reset estrutural de `@media print` no AdminLTE v4 para eliminar colapso de layout e páginas em branco

**⚠️ CRITICAL**: Garante que o layout base libere o fluxo de renderização para impressão do navegador

- [X] T002 [P] Implementar regras globais de reset de `@media print` (neutralizando `height: 100vh`, `overflow: hidden`, forçando `position: static` e `overflow: visible`) em `resources/css/responsive-custom.css`
- [X] T003 [P] Ajustar classes de contêiner e visibilidade de impressão no layout mestre em `resources/views/layouts/app.blade.php`

**Checkpoint**: Base de estilos de impressão pronta - implementação das histórias de usuário desbloqueada.

---

## Phase 3: User Story 1 - Acesso Seguro e Limpo na Tela de Login (Priority: P1) 🎯 MVP

**Goal**: Remover valores padrão de teste e atalhos de demonstração na tela de login, garantindo privacidade e acesso para operadores reais de produção.

**Independent Test**: Acessar a tela `/login` e verificar que os campos de e-mail e senha estão vazios, sem botões de atalho rápido de teste, efetuando o login com usuário real com sucesso.

### Implementation for User Story 1

- [X] T004 [US1] Limpar valores padrão de teste nos atributos `value` dos inputs de e-mail (`old('email')`) e senha (`""`) em `resources/views/auth/login.blade.php`
- [X] T005 [US1] Remover o bloco HTML de atalhos rápidos de teste (`.btn-quick-login`) e seu listener JavaScript correspondente em `resources/views/auth/login.blade.php`

**Checkpoint**: User Story 1 completa e testável de forma independente (MVP concluído).

---

## Phase 4: User Story 2 - Impressão Direta Completa de Comprovantes (Priority: P1)

**Goal**: Garantir que o botão "Imprimir Comprovante" emita o diálogo de impressão com todos os dados renderizados (cabeçalho institucional CCB, dados da movimentação, itens e assinaturas), sem folhas em branco.

**Independent Test**: Acessar o detalhe de uma movimentação (`/movements/{id}`), clicar no botão "Imprimir Comprovante" e constatar que a pré-visualização de impressão do navegador exibe todas as informações completas e sem páginas em branco extras.

### Implementation for User Story 2

- [X] T006 [US2] Otimizar os estilos de impressão de `.printable-receipt`, tabelas de itens e quebra de páginas em `resources/views/movements/show.blade.php`
- [X] T007 [US2] Validar visibilidade das assinaturas (`.d-print-block`) e ocultação dos botões e barras de navegação (`.no-print`) em `resources/views/movements/show.blade.php`

**Checkpoint**: User Story 2 completa e comprovantes de movimentação imprimindo com 100% de fidelidade.

---

## Phase 5: User Story 3 - Impressão e Exportação Consistente de Relatórios e Inventários (Priority: P2)

**Goal**: Assegurar que folhas de contagem de inventário e exportações de relatórios mantenham consistência visual entre impressão direta e PDF.

**Independent Test**: Acessar a folha de conferência de inventário (`/inventories/{id}`) e os relatórios gerenciais (`/reports`), verificando a correta impressão direta e o download do PDF.

### Implementation for User Story 3

- [X] T008 [US3] Ajustar visibilidade e compatibilidade de impressão na folha de contagem física em `resources/views/inventories/show.blade.php`
- [X] T009 [US3] Verificar e validar que as rotas de exportação de PDF em `app/Http/Controllers/MovementController.php` e `app/Http/Controllers/ReportController.php` continuam operando normalmente sem conflitos

**Checkpoint**: Todas as histórias de usuário funcionais e integradas.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Compilação de produção, validação de regressão e testes finais

- [X] T010 Compilar os assets front-end para produção via comando `pnpm build`
- [X] T011 Executar a suíte de testes automatizados do Laravel via `php artisan test` para assegurar integridade
- [X] T012 Executar o roteiro de validação manual conforme [quickstart.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/006-login-producao-impressao/quickstart.md)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências - execução imediata.
- **Foundational (Phase 2)**: Depende do Setup - BLOQUEIA a correção de impressão das histórias de usuário.
- **User Story 1 (Phase 3 - Login)**: Pode ser executada imediatamente ou em paralelo com a Phase 2 (arquivos independentes).
- **User Story 2 (Phase 4 - Comprovantes)**: Depende da conclusão da Phase 2 (Foundational CSS Reset).
- **User Story 3 (Phase 5 - Inventários/Relatórios)**: Depende da conclusão da Phase 2.
- **Polish (Phase 6)**: Depende de todas as histórias concluídas.

---

## Parallel Opportunities

- **T002 e T003**: Podem ser executadas em paralelo (arquivos distintos de CSS e Blade Layout).
- **T004/T005 (US1)**: Podem ser implementadas em paralelo com **T002/T003 (Foundational)**.
- **T006/T007 (US2)** e **T008 (US3)**: Podem ser implementadas em paralelo após a conclusão da Phase 2.

---

## Implementation Strategy

### MVP First (User Story 1 - Login em Produção)
1. Executar Phase 1 e implementar Phase 3 (`auth/login.blade.php`).
2. Validar imediatamente a tela de login em produção (campos limpos e seguros).

### Incremental Delivery (Impressão Direta)
1. Aplicar os resets globais de `@media print` (Phase 2).
2. Otimizar comprovantes de movimentação (Phase 4 - US2).
3. Ajustar folhas de inventário e validar relatórios (Phase 5 - US3).
4. Compilar assets e rodar testes finais (Phase 6).
