# Feature Specification: Correção de Navegação e Contexto entre Entradas e Saídas

**Feature Branch**: `009-ajuste-navegacao-movimentacoes`  
**Created**: 2026-09-04  
**Status**: Draft  
**Input**: "Ao entrar com nota fiscal, clicando em detalhes da entrada, sou redirecionado para o Menu saídas. Está correto? Este menu é a movimentação/histórico?"

---

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Consistência de Navegação e Menu Ativo nos Detalhes de Entrada (Priority: P1)

Como Almoxarife ou Administrador, ao consultar o histórico de **Entradas** e clicar em "Detalhes" de um documento de entrada (Nota Fiscal ou Doação), desejo visualizar o comprovante da movimentação mantendo o menu lateral **Entradas** como item ativo e podendo retornar diretamente para a listagem de Entradas pelo botão "Voltar".

**Why this priority**: Corrige a quebra de contexto na experiência do usuário, onde o sistema atualmente acende o menu "Saídas" e força o retorno para uma lista diferente da qual o usuário partiu.

**Independent Test**:
1. Acessar o menu lateral "Entradas".
2. Na tabela de entradas registradas, clicar no botão "Detalhes" de qualquer entrada (ex: `ENT-...`).
3. Verificar que na barra lateral o item ativo permanece "Entradas" (e não "Saídas").
4. Clicar no botão "← Voltar" e constatar que o usuário retorna para a listagem de Entradas (`/entries`).

**Acceptance Scenarios**:
1. **Given** que o usuário está visualizando a listagem de Entradas (`/entries`), **When** clica em "Detalhes" de uma entrada, **Then** o sistema exibe o comprovante da movimentação com o menu lateral "Entradas" destacado como ativo.
2. **Given** que o usuário está na tela de comprovante de uma entrada, **When** clica no botão "← Voltar", **Then** o navegador é direcionado para a listagem de Entradas (`route('entries.index')`).
3. **Given** que o usuário está na tela de comprovante de uma saída ou empréstimo, **When** clica no botão "← Voltar", **Then** o navegador é direcionado para a listagem de Saídas (`route('movements.index')`) com o menu "Saídas" ativo.

---

### User Story 2 - Definição Clara do Escopo do Menu de Saídas vs Histórico Geral (Priority: P1)

Como Almoxarife, desejo que os menus da barra lateral reflitam com clareza o conteúdo exibido, para que a listagem sob o menu "Saídas" não apresente entradas de fornecedores de forma confusa.

**Why this priority**: Evita inconsistências onde o menu "Saídas" exibe títulos e dados conflitantes (por exemplo, listar entradas de nota fiscal dentro de "Histórico de Saídas & Empréstimos").

**Independent Test**:
Acessar a rota de Saídas e validar se ela lista apenas movimentações pertinentes ao escopo definido (ou se a tela possui filtros explícitos caso seja um histórico consolidado).

**Acceptance Scenarios**:
1. **Given** o acesso ao menu lateral "Saídas", **When** a página carregar, **Then** ela exibe apenas movimentações de saída para consumo (`CONSUMPTION`) e empréstimos de ferramentas/equipamentos (`LOAN`), sem misturar entradas de compra/doação (`ENTRY`) na listagem.

---

### Edge Cases

- **Acesso direto via URL ao comprovante (`/movements/{id}`)**: Se a movimentação for do tipo `ENTRY`, o menu lateral ativo deve ser "Entradas" e o botão "Voltar" deve apontar para `/entries`; se for `CONSUMPTION` ou `LOAN`, o menu lateral ativo deve ser "Saídas" e o botão "Voltar" deve apontar para `/movements`.
- **Movimentação sem entrada associada**: Tratar com fallback gracioso para não gerar exceções caso `entryDocument` seja nulo.

---

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE identificar dinamicamente se a movimentação visualizada em `movements.show` é uma Entrada (`MovementType::ENTRY`) ou Saída/Empréstimo (`CONSUMPTION`/`LOAN`).
- **FR-002**: A barra lateral (`sidebar.blade.php`) DEVE marcar como ativo o menu "Entradas" quando a rota for `movements.show` de uma movimentação do tipo `ENTRY`.
- **FR-003**: A barra lateral (`sidebar.blade.php`) DEVE marcar como ativo o menu "Saídas" apenas quando a rota for `movements.show` de uma movimentação do tipo `CONSUMPTION` ou `LOAN`.
- **FR-004**: O botão "← Voltar" na tela de comprovante (`movements/show.blade.php`) DEVE retornar dinamicamente para `route('entries.index')` quando a movimentação for uma Entrada, e para `route('movements.index')` quando for uma Saída ou Empréstimo.
- **FR-005**: A consulta da listagem de Saídas (`MovementController@index`) DEVE filtrar as movimentações para excluir o tipo `MovementType::ENTRY`, exibindo estritamente saídas (`CONSUMPTION`) e empréstimos (`LOAN`).

---

### Key Entities

- **Movement**: Representa uma movimentação de estoque com tipo (`MovementType::ENTRY`, `MovementType::CONSUMPTION`, `MovementType::LOAN`).
- **EntryDocument**: Vinculado a uma `Movement` do tipo `ENTRY`, contendo fornecedor/doador, tipo de documento fiscal e valor.

---

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% das navegações a partir de "Detalhes" no menu Entradas mantêm o usuário no contexto visual de "Entradas" e retornam à listagem de Entradas ao clicar em "Voltar".
- **SC-002**: Eliminação completa de divergências conceituais onde documentos de entrada aparecem listados sob títulos exclusivos de "Saídas & Empréstimos" sem distinção.

---

## Assumptions

- O template e as rotas atuais (`movements.show`, `entries.index`, `movements.index`) serão preservados para manter compatibilidade com relatórios e links existentes.
- A exibição do comprovante compartilhado (`movements.show`) é adequada para ambos os tipos (Entradas e Saídas), necessitando apenas de ajuste contextual de layout e links de retorno.
