# Feature Specification: Controle de Validade e Registro de Patrimônio de Produtos

**Feature Branch**: `004-validade-patrimonio-produtos`

**Created**: 2026-08-14

**Status**: Draft

**Input**: User description: "Existem produtos com prazo de validade determinado, por exemplo, tintas, massa corrida, grafiato, poderia incluir uma data de validade no cadastro e sinaliza-los quando próximos de vencer e quando vencidos. Incluir em relatórios os produtos a vencer e vencidos. Também inlcuir campo de patrimônio, alguns equipamentos/ferramentas são de propriedade da entidade e podem ser armazenados até terem um destino."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Alerta e Controle de Validade de Produtos (Priority: P1)

Como almoxarife, quero registrar a data de validade de produtos perecíveis (como tintas, massas e grafiatos) e visualizar alertas claros sobre produtos vencidos ou próximos do vencimento, para evitar a utilização de materiais vencidos ou o desperdício de insumos.

**Why this priority**: A utilização de produtos vencidos compromete a qualidade das obras e manutenções, além de gerar prejuízo financeiro e de recursos. Identificar preventivamente itens a vencer é fundamental para a gestão operacional do almoxarifado.

**Independent Test**: Pode ser testado cadastrando produtos com datas de validade no passado, nos próximos 30 dias e com datas distantes, verificando se o sistema sinaliza corretamente com indicadores visuais os itens vencidos e a vencer na listagem e no painel principal.

**Acceptance Scenarios**:

1. **Given** um produto cadastrado com data de validade inferior à data atual, **When** o usuário visualiza a lista de produtos ou dashboard, **Then** o sistema exibe um indicador visual destacado de "Vencido" alertando a indisponibilidade recomendada para uso.
2. **Given** um produto cadastrado com data de validade dentro da janela de alerta (ex.: 30 dias a partir da data atual), **When** o usuário acessa o sistema, **Then** o produto é sinalizado visualmente como "Próximo do Vencimento".
3. **Given** um produto cadastrado com data de validade distante (superior à janela de alerta), **When** o usuário consulta o produto, **Then** a data de validade é exibida sem alertas críticos de vencimento.

---

### User Story 2 - Relatório de Produtos Vencidos e a Vencer (Priority: P2)

Como gestor do almoxarifado, quero gerar relatórios filtrados por status de validade (produtos já vencidos e produtos a vencer em determinado período), para planejar o descarte adequado, priorizar a saída de materiais ou solicitar reposição.

**Why this priority**: Permite uma tomada de decisão gerencial baseada em dados agrupados, facilitando auditorias e a distribuição prioritária de materiais com prazo de validade mais curto.

**Independent Test**: Pode ser testado aplicando o filtro de controle de validade na área de relatórios, conferindo se os itens exibidos e exportados correspondem exatamente ao período e aos critérios de validade selecionados.

**Acceptance Scenarios**:

1. **Given** a tela de relatórios do almoxarifado, **When** o operador seleciona o filtro "Produtos Vencidos", **Then** o relatório exibe apenas os produtos cuja data de validade já expirou, incluindo quantidade em estoque e localização.
2. **Given** a tela de relatórios do almoxarifado, **When** o operador seleciona o filtro "Produtos a Vencer" informando um período (ex.: próximos 30, 60 ou 90 dias), **Then** o relatório apresenta a lista ordenada por data de vencimento mais próxima.
3. **Given** um relatório de validade gerado na tela, **When** o operador aciona a opção de impressão ou exportação, **Then** o arquivo gerado mantém a identificação do status de validade dos produtos.

---

### User Story 3 - Cadastro e Acompanhamento de Código de Patrimônio (Priority: P3)

Como almoxarife, quero registrar o número ou código de patrimônio em equipamentos e ferramentas de propriedade da entidade, para garantir a rastreabilidade desses bens armazenados no almoxarifado até que seja feita sua destinação final (uso, transferência, empréstimo ou baixa).

**Why this priority**: Garante o controle patrimonial de bens duráveis e ferramentas da entidade armazenados temporariamente no almoxarifado, prevenindo extravios e facilitando a identificação individualizada.

**Independent Test**: Pode ser testado cadastrando ou editando um equipamento/ferramenta preenchendo o campo de patrimônio, e em seguida realizando buscas por esse código no sistema e relatórios.

**Acceptance Scenarios**:

1. **Given** o formulário de cadastro ou edição de produto/equipamento, **When** o usuário insere o número de patrimônio da entidade, **Then** o dado é salvo e vinculado ao item.
2. **Given** a listagem de produtos ou busca rápida, **When** o operador pesquisa pelo código de patrimônio digitado, **Then** o sistema localiza e exibe o equipamento correspondente.
3. **Given** um item com patrimônio cadastrado, **When** o operador consulta os detalhes do item ou relatório de estoque, **Then** o código de patrimônio é exibido claramente junto com as informações do item.

---

### Edge Cases

- O que acontece se a data de validade informada for uma data inválida ou inconsistente? O sistema valida o formato da data no preenchimento e exibe mensagem amigável de erro.
- O que acontece ao registrar movimentação de saída em um produto já vencido? O sistema exibe um alerta de confirmação ressaltando que o produto está vencido antes de confirmar a baixa de estoque.
- Como o sistema se comporta caso o código de patrimônio digitado já esteja atribuído a outro bem em estoque? O sistema verifica a unicidade do código de patrimônio cadastrado para evitar duplicidades acidentais.
- O que acontece se um produto não possui data de validade (ex.: pregos, parafusos)? O campo de validade permanece opcional, sem gerar alertas visuais desnecessários.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE permitir a inclusão e edição de uma Data de Validade no cadastro de produtos e movimentações de estoque.
- **FR-002**: O sistema DEVE disponibilizar o campo Data de Validade como opcional, sendo aplicável a produtos perecíveis ou sujeitos a degradação (como tintas, massas, grafiatos, reagentes e colas).
- **FR-003**: O sistema DEVE identificar e sinalizar automaticamente com indicador visual de alerta "Próximo do Vencimento" produtos cuja data de validade esteja dentro da janela pré-definida de alerta (ex.: 30 dias).
- **FR-004**: O sistema DEVE identificar e sinalizar automaticamente com indicador visual destacado de "Vencido" produtos cuja data de validade seja menor que a data atual.
- **FR-005**: O sistema DEVE incluir no painel principal (dashboard) um resumo/contador dos produtos vencidos e a vencer, permitindo acesso rápido à listagem detalhada.
- **FR-006**: O sistema DEVE permitir a inclusão dos filtros "Produtos Vencidos" e "Produtos a Vencer" nos relatórios de estoque e movimentação, com opção de filtro por intervalo de datas.
- **FR-007**: O sistema DEVE permitir o cadastro e a edição de um campo de Código/Número de Patrimônio para identificação de bens, equipamentos e ferramentas da entidade.
- **FR-008**: O sistema DEVE permitir a busca rápida de itens pelo Código de Patrimônio na consulta de estoque e telas de movimentação/saída/empréstimo.
- **FR-009**: O sistema DEVE exibir o Código de Patrimônio nos relatórios de inventário, fichas de estoque e relatórios de bens armazenados.

### Key Entities

- **Produto / Item de Estoque**: Representa o material ou equipamento armazenado. Atributos relevantes atualizados: nome, categoria, unidade de medida, quantidade em estoque, **data de validade**, **código de patrimônio**, localização física e status de validade (Normal, Próximo do Vencimento, Vencido).
- **Alerta de Validade**: Representa o estado operacional derivado do confronto entre a data atual e a data de validade do item/lote.
- **Relatório de Validade e Patrimônio**: Consolidação analítica e sintética dos produtos com controle de validade e dos bens identificados com registro patrimonial.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos produtos cadastrados com data de validade expirada são identificados e sinalizados com o status "Vencido" no sistema em tempo real.
- **SC-002**: Almoxarife consegue identificar produtos com validade a vencer nos próximos 30 dias diretamente no painel/dashboard com 1 único clique.
- **SC-003**: Geração e exportação do relatório de produtos a vencer ou vencidos concluída em menos de 3 segundos para a base de dados em operação.
- **SC-004**: Redução a zero do envio ou aplicação inadvertida de materiais e produtos vencidos em obras e serviços de manutenção.
- **SC-005**: Localização e identificação de equipamentos/ferramentas de propriedade da entidade pelo código de patrimônio realizada em menos de 5 segundos através da busca rápida.

## Assumptions

- A janela padrão para alarme de "Próximo do Vencimento" será de 30 dias contados a partir da data atual, podendo o usuário visualizar essa contagem regressiva de dias.
- O campo de patrimônio é opcional e direcionado principalmente a bens duráveis, equipamentos e ferramentas de propriedade da entidade.
- O código de patrimônio deve ser único por bem/equipamento quando preenchido, evitando códigos de patrimônio duplicados em itens ativos no estoque.
- Produtos sem data de validade informada serão tratados como "Sem Validade Indefinida" e não receberão alertas de vencimento.
