# Feature Specification: Gestão de Almoxarifado Central e Controle de Estoque CCB

**Feature Branch**: `001-gestao-almoxarifado`  
**Created**: 2026-08-11  
**Status**: Draft  
**Input**: Sistema Web de Gestão de Almoxarifado Central e Controle de Estoque para a Congregação Cristã no Brasil (CCB), cobrindo saídas de consumo, entrega de EPIs, empréstimo de ferramentas/equipamentos, devoluções, controle de saldos e controle de acesso RBAC.

---

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Registrar Saídas de Materiais de Consumo (Priority: P1)

Como Almoxarife do sistema, desejo registrar a saída imediata de materiais de consumo para uma Casa de Oração ou Obra específica, atribuída a um beneficiário (voluntário ou trabalhador), para que o saldo do estoque seja atualizado em tempo real sem gerar pendências de devolução.

**Why this priority**: É a operação principal e mais frequente do almoxarifado no dia a dia da igreja. Garantir que o consumo dê baixa imediata e rastreável no estoque é vital para o MVP.

**Independent Test**: Pode ser testado registrando a saída de 5 sacos de cimento para a "C.O. Jardim das Flores" retirado pelo voluntário "João Silva". O sistema deve diminuir 5 unidades do estoque atual e gerar o comprovante de saída com código único.

**Acceptance Scenarios**:

1. **Given** que o material de consumo "Cimento CP II" possui 50 unidades em estoque, **When** o Almoxarife registra uma saída de 10 unidades para a "C.O. Central", **Then** o saldo do estoque é atualizado para 40 unidades e a movimentação é gravada com status concluído.
2. **Given** que o saldo de um material é de 3 unidades, **When** o Almoxarife tenta registrar a saída de 5 unidades, **Then** o sistema nega a movimentação com uma mensagem de alerta informando saldo insuficiente.

---

### User Story 2 - Empréstimo e Devolução de Ferramentas/Equipamentos (Priority: P1)

Como Almoxarife, desejo emprestar ferramentas ou equipamentos a um beneficiário indicando a data prevista para devolução, e posteriormente registrar a devolução parcial ou total desses itens, para manter o controle dos equipamentos que estão em campo.

**Why this priority**: Ferramentas e equipamentos representam patrimônio reutilizável de alto valor. Rastrear quem retirou, onde está sendo usado e quando deve retornar previne perdas.

**Independent Test**: Pode ser testado realizando o empréstimo de uma "Furadeira Industrial" com devolução prevista para 7 dias. O item fica associado ao beneficiário em pendência. Ao registrar a devolução, o saldo disponível é reestabelecido.

**Acceptance Scenarios**:

1. **Given** que a "Furadeira de Impacto" está disponível em estoque, **When** o Almoxarife registra o empréstimo para um construtor informando a data prevista de retorno, **Then** o item é decrementado do saldo disponível e fica registrado como pendente de devolução associado ao beneficiário.
2. **Given** que um empréstimo está com a data prevista de retorno vencida, **When** o painel de controle é consultado, **Then** o empréstimo é destacado visualmente com alerta de atraso.
3. **Given** um empréstimo de 2 escadas extensíveis, **When** o beneficiário devolve 1 escada, **Then** o sistema registra devolução parcial, retorna 1 unidade ao estoque disponível e mantém 1 unidade como pendente de devolução.

---

### User Story 3 - Entrega e Controle de EPIs com Certificado de Aprovação (Priority: P2)

Como Almoxarife, desejo registrar a entrega de Equipamentos de Proteção Individual (EPI) validando o número de CA (Certificado de Aprovação) e sua validade, permitindo registrar a devolução do EPI antigo danificado se necessário.

**Why this priority**: Garante a conformidade de segurança dos trabalhadores e voluntários da obra, rastreando a entrega técnica e validade jurídica do equipamento.

**Independent Test**: Pode ser testado selecionando a categoria "EPI" e o item "Capacete de Segurança", informando o número e validade do CA e confirmando o recebimento de uma bota antiga em troca.

**Acceptance Scenarios**:

1. **Given** a seleção de um material da categoria EPI, **When** o Almoxarife preenche a entrega, **Then** o sistema exige/valida a presença do número e da data de validade do CA.
2. **Given** que o CA de um EPI está vencido, **When** o operador tenta incluir este EPI na movimentação, **Then** o sistema emite um alerta de validade expirada.

---

### User Story 4 - Painel Geral de Indicadores (Dashboard) e Alertas (Priority: P2)

Como Administrador ou Almoxarife, desejo visualizar um painel geral com cartões de indicadores trazendo totais em estoque, alertas de estoque mínimo, empréstimos em atraso e EPIs com CA a vencer.

**Why this priority**: Proporciona visão gerencial rápida e proativa para compras, reposição de materiais e cobrança de devoluções.

**Independent Test**: Acessar a tela inicial do sistema e verificar se os contadores de itens críticos, empréstimos atrasados e saldos gerais correspondem exatamente aos dados consolidados no sistema.

**Acceptance Scenarios**:

1. **Given** que 3 materiais estão com saldo abaixo do estoque mínimo, **When** o usuário acessa o Dashboard, **Then** o cartão de "Alertas de Estoque Mínimo" exibe o número 3 e permite listar os itens afetados.
2. **Given** que existem 2 empréstimos com data limite ultrapassada, **When** o usuário acessa o Dashboard, **Then** o painel destaca a lista de empréstimos em atraso com o nome dos beneficiários responsáveis.

---

### User Story 5 - Controle de Acesso por Perfis (RBAC) (Priority: P3)

Como Administrador, desejo atribuir perfis específicos aos usuários (Administrador, Almoxarife, Consulta) para limitar os privilégios de execução no sistema.

**Why this priority**: Garante a segurança das informações e impede que usuários apenas leitores alterem saldos ou lancem movimentações indevidas.

**Independent Test**: Logar com uma conta com perfil "Consulta" e verificar que os botões de lançamento de saída, devolução e cadastro de materiais não estão acessíveis.

**Acceptance Scenarios**:

1. **Given** um usuário logado com perfil "Consulta", **When** ele navega pelo sistema, **Then** ele consegue apenas visualizar relatórios e saldos, sem opções de edição ou movimentação.
2. **Given** um usuário logado com perfil "Almoxarife", **When** ele navega pelo sistema, **Then** ele tem permissão para lançar saídas, entradas, empréstimos e devoluções.

---

### Edge Cases

- **Tentativa de baixa com quantidade zero ou negativa**: O sistema deve rejeitar entradas com quantidade <= 0 no formulário.
- **Exclusão de material vinculado a movimentações históricas**: O sistema não deve permitir exclusão física de materiais ou beneficiários que possuem histórico de movimentações, mantendo integridade auditável (status inativo).
- **Tentativa de devolução superior à quantidade emprestada**: Ao registrar a devolução de um empréstimo de 2 unidades, informar 3 unidades deve disparar erro de validação.
- **Beneficiário inativo no cadastro**: Não deve permitir realizar novos empréstimos ou saídas para beneficiários cujo cadastro esteja desativado.

---

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE registrar cadastros de Destinos (Casas de Oração, Obras, Administração, Outro) contendo nome, código de setor/relatório, cidade, endereço e status.
- **FR-002**: O sistema DEVE registrar cadastros de Beneficiários (voluntários, construtores, pedreiros, oficiais) contendo nome, CPF, telefone, função na CCB e status ativo/inativo.
- **FR-003**: O sistema DEVE registrar Categorias (EPI, Consumo, Ferramenta/Equipamento) e Materiais associados com código SKU, unidade de medida, estoque atual, estoque mínimo, número de CA, validade do CA e indicativo de ser retornável.
- **FR-004**: O sistema DEVE registrar movimentações de saída de materiais agrupando um cabeçalho (código único, responsável, beneficiário, destino, tipo, status, observações) e múltiplos itens.
- **FR-005**: O sistema DEVE impedir a confirmação de saídas e empréstimos caso a quantidade solicitada seja maior que o estoque atual disponível.
- **FR-006**: O sistema DEVE executar a atualização de saldo de estoque e registro de movimentações dentro de transações de banco de dados para garantir consistência.
- **FR-007**: O sistema DEVE gerenciar empréstimos de ferramentas e equipamentos vinculando obrigatoriamente uma data prevista para devolução e o beneficiário responsável.
- **FR-008**: O sistema DEVE permitir a baixa de devolução (parcial ou total) de itens emprestados, incrementando novamente o estoque disponível e atualizando o status do item/movimentação.
- **FR-009**: O sistema DEVE identificar e destacar automaticamente empréstimos cujo prazo de devolução expirou em relação à data atual (status em atraso).
- **FR-010**: O sistema DEVE disponibilizar um painel gerencial (Dashboard) com cartões de indicadores (KPIs) de total de estoque, alertas de estoque mínimo, empréstimos atrasados e EPIs com CA a vencer.
- **FR-011**: O sistema DEVE exigir solicitação de confirmação interativa antes de efetivar ações de devolução, baixa ou exclusão de dados.
- **FR-012**: O sistema DEVE exibir mensagens globais de notificação (sucesso, alerta, erro) após a conclusão das operações.
- **FR-013**: O sistema DEVE aplicar controle de acesso baseado em perfis (RBAC) com perfis de Administrador, Almoxarife e Consulta.

### Key Entities

- **User**: Representa os operadores do sistema. Possui relacionamento com os perfis de acesso (RBAC) e com o histórico de movimentações lançadas.
- **Destination**: Locais físicos ou organizacionais da CCB (Casas de Oração, Obras, Setores Administrativos) para onde os materiais são enviados.
- **Beneficiary**: Pessoas (voluntários, trabalhadores, oficiais) que retiram materiais do almoxarifado sob responsabilidade cadastrada.
- **Category**: Agrupamento temático de materiais (EPI, Consumo, Ferramenta/Equipamento).
- **Material**: Itens físicos controlados no almoxarifado. Contém atributos de identificação, saldos, dados de CA (para EPIs) e regras de devolução.
- **Movement**: Cabeçalho de registro de uma operação de saída, empréstimo ou entrega de EPI. Vincula o usuário almoxarife, o beneficiário, o destino e o tipo da operação.
- **MovementItem**: Itens individuais associados a uma movimentação. Registra quantidade entregue, quantidade devolvida, data prevista de retorno, data efetiva de retorno e status individual do item.

---

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Almoxarifes conseguem concluir o registro completo de uma saída de materiais (com múltiplos itens) em menos de 1 minuto.
- **SC-002**: 100% das tentativas de movimentação com quantidade superior ao saldo disponível são bloqueadas com precisão pelo sistema.
- **SC-003**: 100% dos empréstimos em atraso são identificados automaticamente no Dashboard em tempo real sem necessidade de relatórios manuais.
- **SC-004**: Redução a zero de divergências de saldo decorrentes de concorrência ou falhas de gravação intermediária (garantido por transações de banco de dados).
- **SC-005**: Usuários com perfil de "Consulta" são impedidos de realizar alterações mutáveis em 100% dos cenários de uso.

---

## Assumptions

- O sistema funcionará em ambiente web local/intranet da administração do almoxarifado central.
- O cadastro inicial de Casas de Oração (Destinos) e Categorias padrão será disponibilizado na implantação do sistema.
- Todos os materiais controlados como EPI possuem a flag de categoria identificada para acionar os campos de CA.
- Os operadores acessam o sistema via navegadores web modernos (Chrome, Edge, Firefox).
