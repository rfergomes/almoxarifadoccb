---

description: "Lista de tarefas para suporte à impressão visível sem gráficos de segundo plano"
---

# Tasks: Visibilidade da Impressão sem Gráficos de Segundo Plano

**Input**: Design documents from `specs/008-print-without-backgrounds/`
**Prerequisites**: [plan.md](plan.md), [spec.md](spec.md), [research.md](research.md), [contracts/print-high-contrast.md](contracts/print-high-contrast.md), [quickstart.md](quickstart.md)

## Format: `- [ ] [TaskID] [P?] [Story?] Description with file path`
- **[P]**: Pode rodar em paralelo (arquivos distintos, sem dependência mútua)
- **[Story]**: Rótulo da User Story correspondente (ex: `[US1]`, `[US2]`)

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Verificação do ambiente e baseline de testes

- [X] T001 Verificar a integridade dos arquivos de estilo e views em resources/views/movements/show.blade.php e resources/css/responsive-custom.css
- [X] T002 Executar testes automatizados existentes de movimentação com `php artisan test --filter=Movement`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Diretivas globais de ajuste de cor para impressão

- [X] T003 Adicionar `print-color-adjust: exact !important; -webkit-print-color-adjust: exact !important;` no escopo global de `@media print` em resources/css/responsive-custom.css
- [X] T004 Adicionar diretivas de `print-color-adjust: exact !important;` no cabeçalho de `@media print` em resources/views/layouts/app.blade.php

---

## Phase 3: User Story 1 - Impressão Visível com ou sem Gráficos de Segundo Plano (Priority: P1) 🎯 MVP

**Goal**: Garantir que ao imprimir com a opção "Gráficos de segundo plano" desmarcada no navegador, todo o documento permaneça perfeitamente legível com textos pretos, contornos em badges e bordas visíveis em tabelas, sem páginas em branco.

**Independent Test**: Abrir `/movements/1`, acionar `window.print()`, desmarcar a opção "Gráficos de segundo plano" no Chrome e conferir se todos os dados, textos, tabela de itens e assinaturas permanecem nítidos na folha A4 em escala 100%.

### Implementation for User Story 1

- [X] T005 [US1] Adicionar regras defensivas de alto contraste para badges convertendo texto branco para preto e aplicando borda preta sólida em resources/views/movements/show.blade.php
- [X] T006 [US1] Forçar tipografia preta sólida (`#000000`) e labels escuros em todo o escopo de `.printable-receipt` em resources/views/movements/show.blade.php
- [X] T007 [US1] Definir bordas pretas sólidas para tabela de itens, células e caixa de observações em resources/views/movements/show.blade.php

**Checkpoint**: User Story 1 concluída — o comprovante agora é legível mesmo sem gráficos de segundo plano.

---

## Phase 4: User Story 2 - Diretiva Nativa e Fidelidade Institucional (Priority: P2)

**Goal**: Assegurar que ao imprimir com gráficos de segundo plano ativados, as cores institucionais e preenchimentos sejam preservados com exatidão.

**Independent Test**: Imprimir com "Gráficos de segundo plano" marcado e verificar a renderização fiel das cores originais.

### Implementation for User Story 2

- [X] T008 [US2] Configurar `print-color-adjust: exact !important;` no contêiner `.printable-receipt` em resources/views/movements/show.blade.php
- [X] T009 [US2] Assegurar que linhas de assinatura e divisores utilizem bordas físicas sem dependência de cor de fundo em resources/views/movements/show.blade.php

**Checkpoint**: User Story 2 concluída — fidelidade total de impressão em ambos os modos.

---

## Phase 5: Polish & Cross-Cutting Concerns

**Purpose**: Compilação de assets de produção e validação final

- [X] T010 Recompilar os assets front-end para produção executando `pnpm run build`
- [X] T011 Executar a suíte de testes de regressão com `php artisan test --filter=Movement`
- [X] T012 Validar o checklist de testes manuais conforme descrito em specs/008-print-without-backgrounds/quickstart.md

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências — execução imediata.
- **Foundational (Phase 2)**: Depende do Setup — bloqueia as User Stories.
- **User Story 1 (Phase 3)**: Depende da Fase Foundational — entrega o MVP de legibilidade sem segundo plano.
- **User Story 2 (Phase 4)**: Depende da User Story 1 — refina a diretiva de ajuste de cor.
- **Polish (Phase 5)**: Depende da conclusão de US1 e US2.

---

## Implementation Strategy

### MVP First (User Story 1)
1. Completar Setup (T001-T002) e Foundational (T003-T004).
2. Implementar User Story 1 (T005-T007).
3. Validar: comprovante visível com "Gráficos de segundo plano" desmarcado no Chrome.

### Entrega Incremental (User Story 2)
1. Implementar User Story 2 (T008-T009).
2. Validar ambos os modos (marcado e desmarcado).
3. Compilar assets e rodar testes de regressão (T010-T012).
