# Feature Specification: Campo de Observação no Cadastro e Gestão de Materiais

**Feature Branch**: `017-campo-observacao-material`

**Created**: 2026-09-18

**Status**: Draft

**Input**: User description: "Incluir um campo de observação em material (formulário de cadastro de novo material e gestão de catálogo)"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Inserção e Atualização de Observações no Cadastro de Materiais (Priority: P1) 🎯 MVP

Como Almoxarife ou Administrador do Almoxarifado Central,  
Quero registrar anotações e observações complementares de texto livre ao cadastrar ou editar um material,  
Para que informações operacionais relevantes (instruções de armazenamento, especificações do fabricante, lote de compra original, dicas de conservação ou restrições de manuseio) fiquem registradas junto à ficha do item.

**Why this priority**: É a funcionalidade primária solicitada pelo usuário, permitindo registrar e salvar notas contextuais que não se enquadram nos campos estruturados existentes (como código SKU, validade ou categoria).

**Independent Test**: Acessar o formulário de cadastro de novo material, preencher os campos obrigatórios e adicionar um texto descritivo no campo "Observações". Salvar o item e conferir se o texto permanece gravado e editável na tela de alteração.

**Acceptance Scenarios**:

1. **Given** que o usuário está no modal/formulário "Cadastrar Novo Material",  
   **When** preenche o campo opcional "Observação" com notas informativas e clica em "Salvar Material",  
   **Then** o material é gravado com sucesso e o conteúdo da observação é associado ao cadastro do item.
2. **Given** que o usuário está editando um material previamente cadastrado,  
   **When** modifica o texto do campo de observação e confirma a gravação,  
   **Then** o novo texto substitui o anterior mantendo o histórico de alterações íntegro.
3. **Given** que o usuário decide remover a observação de um material existente,  
   **When** limpa todo o conteúdo do campo no formulário de edição e salva,  
   **Then** o registro é atualizado com sucesso, ficando sem observação vinculada.
4. **Given** que o usuário realiza um cadastro rápido de material durante uma entrada de estoque,  
   **When** visualiza o formulário simplificado de criação de material,  
   **Then** tem a opção de preencher a observação caso deseje registrar notas imediatas sobre o item recebido.

---

### User Story 2 - Consulta e Leitura da Observação no Catálogo de Materiais (Priority: P1) 🎯 MVP

Como Almoxarife, Operador ou Usuário de Consulta ao navegar pela listagem ou ficha do material,  
Quero visualizar com clareza as observações cadastradas para cada produto,  
Para que eu tome decisões seguras sobre a entrega, manuseio e conservação do material no dia a dia.

**Why this priority**: Registrar informações só agrega valor se os operadores conseguirem lê-las de forma rápida, legível e não intrusiva nas interfaces operacionais do sistema.

**Independent Test**: Acessar a listagem de materiais com um item que possua observações cadastradas e verificar que o conteúdo pode ser lido facilmente (seja através de indicador/ícone interativo com tooltip/popover, modal de detalhes ou coluna correspondente), preservando a diagramação limpa da tabela.

**Acceptance Scenarios**:

1. **Given** que um material possui texto cadastrado no campo de observação,  
   **When** o usuário consulta a listagem ou os detalhes do material,  
   **Then** o sistema apresenta um indicativo visual evidente que permite ler a íntegra da observação com formatação e quebras de linha preservadas.
2. **Given** que um material não possui observações cadastradas,  
   **When** a listagem é exibida,  
   **Then** a ausência de observação é tratada de forma discreta e limpa, sem poluir visualmente a tabela com textos de erro ou marcadores vazios excessivos.
3. **Given** que o usuário aciona a impressão de ficha ou relatório do material,  
   **When** o documento de visualização/impressão é gerado,  
   **Then** o campo de observação é apresentado de forma legível quando preenchido.

---

### Edge Cases

- **Texto com múltiplas linhas e quebras de parágrafo**: Quando o usuário inserir observações estruturadas em tópicos ou quebras de linha, o sistema deve preservar os saltos de linha ao renderizar a informação nas consultas e formulários.
- **Observações com caracteres especiais ou pontuação complexa**: Inserção de caracteres como aspas, barras, parênteses, hífen, pontuação técnica (ex: 220V, 3/4", pH neutro) não deve quebrar a renderização da tela nem desconfigurar campos de formulário.
- **Tentativa de envio de texto excessivamente longo**: Caso o usuário insira um texto desmedido, o campo deve possuir limite superior razoável (ex: até 1.000 ou 2.000 caracteres) com indicação clara para evitar degradação de layout ou esgotamento de espaço.
- **Espaços vazios em branco**: Se o usuário preencher apenas espaços ou quebras de linha em branco, o sistema deve tratar o campo como nulo/vazio, evitando salvar textos invisíveis.
- **Dispositivos móveis e telas estreitas**: A exibição das observações na tabela e formulários deve se adaptar perfeitamente a telas de smartphones e tablets, sem forçar rolagem horizontal indesejada ou sobreposição de botões.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE disponibilizar um campo opcional de texto livre denominado "Observações" ou "Observação" no modal/tela de cadastro de novos materiais.
- **FR-002**: O sistema DEVE disponibilizar o campo "Observações" no modal/tela de edição de materiais existentes, permitindo inclusão, alteração e exclusão do texto.
- **FR-003**: O sistema DEVE disponibilizar o campo opcional "Observações" no fluxo de cadastro rápido de materiais (durante a tela de nova entrada de estoque), mantendo paridade com os dados essenciais do item.
- **FR-004**: O campo de observação DEVE ser do tipo texto multilinha (área de texto com altura confortável), permitindo anotações detalhadas pelo operador.
- **FR-005**: O sistema DEVE tratar o campo como estritamente opcional, nunca impedindo a gravação do material caso o campo permaneça vazio.
- **FR-006**: O sistema DEVE validar o tamanho do texto para um limite seguro (máximo recomendado de 1.000 a 2.000 caracteres), notificando o usuário amigavelmente caso o limite seja ultrapassado.
- **FR-007**: O sistema DEVE permitir a visualização intuitiva das observações cadastradas na consulta do catálogo de materiais, garantindo que o layout da tabela permaneça organizado e harmonioso.
- **FR-008**: O sistema DEVE preservar as quebras de linha e formatação básica de parágrafos na exibição de detalhes do material.

### Key Entities *(include if feature involves data)*

- **Material**: Registro do item de catálogo do almoxarifado, que passa a incorporar o atributo complementar de texto `Observação` junto aos atributos existentes (Código SKU, Nome, Categoria, Unidade de Medida, É Retornável, Validade, Patrimônio, Estoques, Dados de CA e Imagem).
- **Observação do Material**: Informação textual descritiva de apoio, sem regras fiscais ou contábeis obrigatórias, com finalidade puramente operacional e consultiva para a equipe de gestão.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos formulários de materiais (inclusão padrão, edição e inclusão rápida) possuem o campo de observação acessível e funcional.
- **SC-002**: 100% dos cadastros ou edições de material com observações preenchidas preservam integralmente o texto e suas quebras de linha sem distorção.
- **SC-003**: A visualização ou consulta da observação não causa quebra de layout, sobreposição de elementos ou lentidão perceptível na listagem de materiais em nenhum dos dispositivos suportados (desktop e mobile).
- **SC-004**: Usuários conseguem preencher ou atualizar a observação de um item e salvar a alteração em menos de 15 segundos.

## Assumptions

- O preenchimento da observação é facultativo; nenhum fluxo de trabalho existente de cadastro ou movimentação será bloqueado pela ausência de preenchimento deste campo.
- O campo tem natureza descritiva geral e não substitui os campos específicos existentes como código de patrimônio, CA de EPI ou data de validade.
- A permissão necessária para editar o campo de observação acompanha a mesma política de acesso vigente para a gestão de materiais (`manage-materials`).
