# Feature Specification: Correção da Escala e Layout de Impressão Direta do Comprovante de Movimentação

**Feature Branch**: `007-fix-receipt-print-scale`

**Created**: 2026-09-04

**Status**: Draft

**Input**: User description: "Ainda com problemas na impressão. Preciso reduzir a scala para apresentar os dados. Como resolver?"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Impressão Direta em Escala Padrão 100% (Priority: P1)

Como um almoxarife ou operador do sistema, ao visualizar um comprovante de movimentação de estoque (entrada, saída ou empréstimo) e clicar em "Imprimir Comprovante", desejo que a página seja formatada perfeitamente para folha A4 com escala 100% (Padrão) do navegador, sem cortes laterais, sem páginas em branco e sem necessidade de ajustar manualmente a escala para 40%.

**Why this priority**: É o fluxo principal de atendimento e expedição de materiais no balcão. Atualmente o operador precisa acessar configurações avançadas de impressão do navegador para reduzir a escala para 40% a fim de conseguir ler e imprimir o comprovante.

**Independent Test**:
Acessar qualquer movimentação concluída (`/movements/{id}`), acionar a opção "Imprimir Comprovante" (ou Ctrl+P) mantendo a escala em "Padrão (100%)", e verificar no preview do navegador se o comprovante preenche perfeitamente a folha A4 com cabeçalho CCB, dados gerais, itens e assinaturas legíveis na primeira folha.

**Acceptance Scenarios**:

1. **Given** que o usuário está na tela de comprovante de movimentação (`/movements/{id}`), **When** clica em "Imprimir Comprovante", **Then** a janela de impressão do navegador é aberta com o documento ocupando 100% da largura útil da folha A4 em escala padrão.
2. **Given** o preview de impressão do navegador aberto com "Escala: Padrão", **When** o usuário inspeciona a visualização, **Then** a folha NÃO aparece em branco e todos os dados textuais, tabelas e campos de assinatura estão legíveis e bem diagramados.
3. **Given** elementos de interface como navbar superior, sidebar lateral, botões de ação e rodapé da aplicação, **When** a visualização de impressão é gerada, **Then** todos estes elementos permanecem totalmente ocultos, isolando apenas o comprovante oficial.

---

### User Story 2 - Preservação da Estrutura de Grid das Informações na Impressão (Priority: P2)

Como usuário conferente, desejo que os blocos de informações (Data/Hora, Status, Almoxarife, Documento/NF, Fornecedor/Beneficiário) e as assinaturas mantenham a organização visual em colunas alinhadas no papel, em vez de se empilharem desordenadamente.

**Why this priority**: Garante que o documento impresso mantenha a sobriedade institucional da CCB e permita leitura rápida em auditorias físicas.

**Independent Test**:
Comparar o documento gerado pela impressão direta do navegador com o layout oficial do PDF exportado, assegurando consistência nas informações apresentadas e nos campos de assinatura lado a lado.

**Acceptance Scenarios**:

1. **Given** os dados de cabeçalho e detalhes da movimentação, **When** impressos em papel A4, **Then** são distribuídos em linhas e colunas proporcionais sem overflow horizontal.
2. **Given** o rodapé de assinaturas (Almoxarife Responsável e Beneficiário/Fornecedor), **When** impressos, **Then** permanecem dispostos lado a lado com linhas para rubrica/assinatura física.

---

### Edge Cases

- **Tabelas com muitos itens**: Movimentações com mais de 10 a 15 itens devem quebrar páginas de forma limpa (`break-inside: avoid` nas linhas `tr`), mantendo o cabeçalho da tabela repetido ou legível nas páginas subsequentes, sem cortar linhas pela metade.
- **Diferentes navegadores (Chrome, Edge, Firefox)**: O reset de impressão deve neutralizar particularidades de motores baseados em Chromium e Gecko, especialmente no tratamento de unidades `vw` e containers com CSS Grid/Flexbox no elemento raiz.
- **Resoluções e Monitores Ultrawide / Full HD**: O dimensionamento para impressão deve ser estritamente fixado às dimensões do papel A4 (`@page`), tornando-se imune à largura do monitor ou janela do usuário.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE garantir que o acionamento de impressão via navegador (`window.print()`) formate o comprovante de movimentação para proporções exatas de folha A4 retrato (portrait).
- **FR-002**: O sistema DEVE resetar o contêiner raiz de layout no contexto de impressão (`@media print`), eliminando restrições de `max-width: 100vw`, `grid-template-areas` e `overflow: auto` herdadas do template AdminLTE 4.
- **FR-003**: O sistema DEVE manter o comprovante perfeitamente visível e dimensionado na escala padrão de 100% da caixa de diálogo de impressão do navegador, dispensando qualquer ajuste de escala manual por parte do usuário.
- **FR-004**: O sistema DEVE manter ocultos da impressão todos os componentes de navegação, painéis laterais, botões de ação e alertas de sessão.
- **FR-005**: O sistema DEVE preservar a diagramação das colunas de detalhes e campos de assinatura no papel sem quebrar a proporção entre os blocos.
- **FR-006**: O sistema DEVE garantir que tabelas de itens respeitem quebras de página naturais sem cortes de conteúdo ou omissão de linhas.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% das tentativas de impressão direta de comprovante de movimentação em navegadores modernos (Chrome, Edge) apresentam o documento legível e centralizado com a configuração de escala em "Padrão (100%)".
- **SC-002**: Zero páginas geradas em branco ou com conteúdo extravasado para fora dos limites imprimíveis do papel A4.
- **SC-003**: Redução a zero do tempo despendido pelos usuários na configuração manual de parâmetros de impressão (zoom/escala) antes de imprimir comprovantes.
- **SC-004**: O comprovante com até 6 itens cabe integralmente em uma única folha A4 (1 página).

## Assumptions

- O usuário utiliza impressoras convencionais configuradas para papel formato A4 com orientação Retrato.
- O navegador padrão do ambiente é baseado em Chromium (Google Chrome / Microsoft Edge).
- A exportação em PDF via DomPDF (`/movements/{id}/pdf`) continuará disponível como método alternativo para arquivamento digital.
