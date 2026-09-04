# Feature Specification: Visibilidade da Impressão sem Gráficos de Segundo Plano

**Feature Branch**: `008-print-without-backgrounds`

**Created**: 2026-09-04

**Status**: Draft

**Input**: User description: "Agora só aparece ser marcado gráfico de segundo plano"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Impressão Visível com ou sem "Gráficos de Segundo Plano" (Priority: P1) 🎯 MVP

Como um operador do almoxarifado ou usuário do sistema, ao imprimir o comprovante de movimentação (`window.print()`), desejo que todas as informações textuais, dados, tabelas, bordas e campos de assinatura sejam perfeitamente visíveis e legíveis na folha impressa, mesmo se a opção "Gráficos de segundo plano" (Background graphics) do navegador estiver desmarcada.

**Why this priority**: Por padrão, muitos computadores e navegadores mantêm a opção "Gráficos de segundo plano" desmarcada para economizar tinta. O comprovante deve ser legível por padrão independente do estado dessa opção.

**Independent Test**:
Acessar `/movements/1`, acionar a impressão no navegador, desmarcar manualmente a opção "Gráficos de segundo plano" nas configurações da impressora e verificar se o texto, o logotipo, a tabela com bordas visíveis e os campos de assinatura continuam perfeitamente impressos em preto e branco com alto contraste sobre a folha de papel.

**Acceptance Scenarios**:

1. **Given** a caixa de diálogo de impressão aberta no Chrome com "Gráficos de segundo plano" **desmarcado**, **When** o usuário inspeciona o preview, **Then** o cabeçalho CCB, código da movimentação, tipo, status, operador, observações, tabela de itens e assinaturas são exibidos com texto preto nítido e bordas pretas/cinzas.
2. **Given** elementos de status e badges (ex: "Concluído", "Entrada de Estoque", "Pendente"), **When** impressos sem segundo plano, **Then** o texto interno é renderizado em preto com borda sólida ao redor (em vez de texto branco sobre fundo transparente invisível).
3. **Given** a folha A4 com escala padrão de 100%, **When** a opção "Gráficos de segundo plano" é alternada entre marcada e desmarcada, **Then** o layout e o dimensionamento da folha permanecem idênticos, sem gerar páginas em branco.

---

### User Story 2 - Diretiva Nativa `print-color-adjust: exact` (Priority: P2)

Como administrador do sistema, desejo que o código CSS do comprovante declare explicitamente as propriedades `print-color-adjust: exact;` e `-webkit-print-color-adjust: exact;` para solicitar ao navegador que preserve os contrastes institucionais e cores quando o usuário mantiver os gráficos de segundo plano ativados.

**Why this priority**: Garante fidelidade visual idêntica ao design institucional da CCB quando o usuário desejar impressão com cores e preenchimentos.

**Independent Test**:
Verificar se o arquivo de estilos contém as diretivas de color-adjust e se ao marcar "Gráficos de segundo plano" as cores institucionais (fundo de cabeçalhos de tabela e badges) são renderizadas com precisão.

**Acceptance Scenarios**:

1. **Given** o comprovante de movimentação impresso com "Gráficos de segundo plano" marcado, **Then** as cores institucionais e contrastes de badges são renderizados conforme a interface em tela.
2. **Given** o comprovante impresso com "Gráficos de segundo plano" desmarcado, **Then** a tipografia comuta automaticamente para preto monocromático de alto contraste com bordas delimitadoras.

---

### Edge Cases

- **Badges com texto branco (`color: #fff`)**: No modo sem gráficos de segundo plano, badges perdem o preenchimento de cor. O CSS deve forçar `color: #000 !important; border: 1px solid #000 !important; background: transparent !important;` para que o texto não desapareça sobre o papel branco.
- **Logotipos e Imagens Institucionais**: O logotipo da CCB deve ser referenciado via tag `<img>` nativa (e não via CSS `background-image`), assegurando sua presença física mesmo com economia de tinta ativada.
- **Linhas de Tabela e Separadores**: As divisões de linhas da tabela e do bloco de assinaturas devem usar propriedades explícitas de borda (`border: 1px solid #000 !important`), que o navegador nunca descarta como gráfico de fundo.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE garantir legibilidade integral de todos os dados do comprovante na folha A4 com a opção "Gráficos de segundo plano" desmarcada.
- **FR-002**: O sistema DEVE declarar `-webkit-print-color-adjust: exact !important;` e `print-color-adjust: exact !important;` no escopo de impressão para maximizar a renderização fiel quando disponível.
- **FR-003**: O sistema DEVE converter automaticamente badges e textos que utilizam cores claras ou texto branco para preto sólido (`#000000`) com contorno quando impressos.
- **FR-004**: O sistema DEVE aplicar bordas nítidas em tabelas (`border: 1px solid #333`) e divisores físicos (`border-top: 1px solid #000`) para dispensar qualquer dependência de sombreamento ou cor de fundo para legibilidade.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos dados textuais permanecem visíveis e legíveis na impressão tanto com "Gráficos de segundo plano" marcado quanto desmarcado.
- **SC-002**: Zero ocorrências de texto branco invisível ou páginas em branco ao desmarcar a opção de segundo plano no Chrome/Edge.
- **SC-003**: A fidelidade dimensional (escala 100%, 1 folha A4) é preservada em ambos os modos.

## Assumptions

- O usuário utiliza impressoras jato de tinta ou laser convencionais conectadas ao Google Chrome / Edge.
- O modo sem gráficos de segundo plano é utilizado primariamente para economia de toner/tinta.
