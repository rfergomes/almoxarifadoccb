# Feature Specification: Correção do Fluxo de Exclusão de Material, Notificações e SKU Opcional com Sequencial

**Feature Branch**: `011-correcao-exclusao-material`

**Created**: 2026-09-08

**Status**: Draft

**Input**: User description: "Erro ao excluir material: Access to fetch at 'https://static.cloudflareinsights.com/beacon.min.js/v31edd6df95cf4e85bb4c19e7a9bdbcba1788362987495' from origin 'https://almoxarifado.sibem.top' has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource. sw.js:1 Uncaught (in promise) TypeError: Failed to convert value to 'Response'. sw.js:66 Uncaught (in promise) TypeError: Failed to execute 'put' on 'Cache': Request scheme 'chrome-extension' is unsupported ... materials:1 Refused to execute script from 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css' ... materials:815 Uncaught ReferenceError: toastr is not defined. O número de SKU, poderia deixar opcional e gerar um sequencial, caso não informado? Tipo GEN-001, GEN-002, etc... GEN de Generico."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Exclusão Segura e Responsiva de Material Elegível (Priority: P1)

Como um Administrador do Almoxarifado,  
Quero solicitar a exclusão de um material elegível (com estoque zero e sem movimentações) e ter a caixa de diálogo de confirmação exibida de forma imediata e interativa,  
Para que eu possa confirmar a exclusão com segurança e receber a confirmação visual de sucesso sem travamentos de script na página.

**Why this priority**: A exclusão de itens de catálogo é uma funcionalidade administrativa essencial. Falhas na inicialização de scripts da página impedem que o diálogo de confirmação seja aberto e que as notificações pós-ação sejam exibidas, bloqueando o fluxo operacional.

**Independent Test**: Pode ser testado de forma isolada ao acessar o catálogo de materiais como Administrador, clicar no botão de exclusão de um material elegível, confirmar o diálogo e verificar se o material é removido e a notificação de sucesso é exibida no topo da tela.

**Acceptance Scenarios**:

1. **Given** que o usuário autenticado é um Administrador e está na listagem de materiais,  
   **When** clica no botão "Excluir" de um material elegível (saldo zero e sem movimentações),  
   **Then** o sistema deve exibir imediatamente o diálogo modal de confirmação com opções de prosseguir ou cancelar.
2. **Given** que o diálogo de confirmação de exclusão está visível,  
   **When** o usuário clica em confirmar a exclusão,  
   **Then** o registro deve ser excluído do sistema e uma mensagem de notificação de sucesso deve ser exibida ao recarregar a lista.
3. **Given** que o diálogo de confirmação de exclusão está visível,  
   **When** o usuário clica em cancelar ou fecha o modal,  
   **Then** nenhuma requisição de exclusão deve ser enviada e o material permanece inalterado.

---

### User Story 2 - Feedback Claro de Impedimento na Exclusão (Priority: P2)

Como um Administrador do Almoxarifado,  
Quero receber um alerta visual claro caso tente excluir um material que possua saldo físico em estoque ou que já tenha histórico de movimentações,  
Para que eu entenda o motivo pelo qual a integridade do estoque não permite a exclusão e eu seja orientado a inativar o material em vez de excluí-lo.

**Why this priority**: Garantir a integridade do histórico do almoxarifado é uma regra fundamental da organização. A interface precisa comunicar as regras de negócio de maneira clara ao operador caso ocorra alguma tentativa não permitida.

**Independent Test**: Pode ser testado tentando acionar a exclusão de um material que possua movimentações anteriores ou estoque maior que zero, garantindo que o sistema bloqueie a exclusão e exiba o alerta com a justificativa de negócio.

**Acceptance Scenarios**:

1. **Given** que um material possui movimentações registradas no histórico,  
   **When** uma requisição de exclusão for processada para esse material,  
   **Then** o sistema deve recusar a operação e exibir um alerta explicativo orientando a inativação do item para preservar o histórico.
2. **Given** que um material possui saldo físico maior que zero,  
   **When** uma requisição de exclusão for processada para esse material,  
   **Then** o sistema deve recusar a operação e exibir um alerta informando que o saldo deve ser zerado antes de qualquer tentativa.

---

### User Story 3 - Código SKU Opcional com Geração Sequencial Automática (Priority: P2)

Como um operador ou Almoxarife cadastrando novos materiais no catálogo ou em entrada rápida,  
Quero que o campo de Código SKU seja opcional no formulário de cadastro,  
Para que, caso eu não informe um código customizado, o sistema gere automaticamente o próximo código sequencial padronizado como "GEN-001", "GEN-002", etc., agilizando o cadastro sem bloqueios de digitação.

**Why this priority**: Muitos materiais não possuem código SKU prévio de fábrica. Forçar o operador a inventar ou controlar manualmente uma numeração gera atrito e erros de duplicidade. A geração automática padronizada simplifica a operação.

**Independent Test**: Pode ser testado cadastrando um novo material deixando o campo Código SKU em branco e verificando se o material é salvo com sucesso com código gerado no formato `GEN-###` incrementando o último existente.

**Acceptance Scenarios**:

1. **Given** que o usuário está no formulário de cadastro de material (ou modal de cadastro rápido),  
   **When** preenche os campos obrigatórios (nome, categoria, unidade, estoques) e deixa o Código SKU vazio,  
   **Then** o sistema aceita o envio e gera automaticamente o código sequencial sequente (ex: `GEN-001` se for o primeiro, ou `GEN-002` se `GEN-001` já existir).
2. **Given** que o usuário está no formulário de cadastro de material,  
   **When** informa um Código SKU manual específico (ex: `MAT-1234`),  
   **Then** o sistema preserva exatamente o código digitado pelo usuário desde que seja único.
3. **Given** que existem materiais com códigos `GEN-001`, `GEN-002` e `GEN-005`,  
   **When** um novo material for cadastrado sem código SKU,  
   **Then** o sistema calcula o próximo número baseado no maior sufixo numérico existente (`5 + 1 = 6`) gerando `GEN-006`.

---

### User Story 4 - Estabilidade de Notificações e Isolamento de Cache do Navegador (Priority: P3)

Como um operador do sistema acessando via diferentes navegadores ou perfis com extensões ativas,  
Quero navegar pela plataforma sem que scripts de notificação quebrem ou que o mecanismo de cache local interfira em chamadas do navegador ou de serviços auxiliares,  
Para que a interface responda de forma fluida, as notificações apareçam pontualmente e o console de navegação permaneça sem falhas não tratadas.

**Why this priority**: Erros na carga de bibliotecas de feedback visual ou manipulação incorreta de requisições pelo gerenciador de cache local degradam a experiência do usuário, causam comportamentos intermitentes e mascaram feedbacks de operação.

**Independent Test**: Pode ser testado recarregando qualquer página autenticada com alertas pendentes e verificando se todas as notificações são apresentadas adequadamente e sem erros de execução de scripts de interface.

**Acceptance Scenarios**:

1. **Given** que o usuário realiza qualquer ação que gere mensagens informativas, de sucesso ou de erro,  
   **When** a página é carregada ou atualizada,  
   **Then** os componentes de notificação visual devem inicializar e exibir as mensagens sem interrupção por falta de definição de scripts dependentes.
2. **Given** que o navegador do usuário executa extensões ou solicita recursos externos auxiliares,  
   **When** o gerenciador de cache intercepta tráfego de rede,  
   **Then** o sistema deve restringir o gerenciamento de cache estritamente aos esquemas de protocolo e recursos válidos da própria aplicação, sem interromper requisições de terceiros ou protocolos não suportados.

---

### Edge Cases

- O que acontece se o operador digitar apenas espaços em branco no campo Código SKU?  
  O sistema trata a entrada como nula/vazia e gera o sequencial automático `GEN-###`.
- O que acontece se dois operadores cadastrarem materiais sem SKU simultaneamente?  
  A rotina de geração de código sequencial deve consultar o estado atual com bloqueio/transação garantindo unicidade sem conflito de integridade no banco de dados.
- O que acontece se a conexão de internet oscilar no momento em que um recurso externo opcional falhar na rede?  
  O sistema deve manter o funcionamento normal da página e ignorar falhas de telemetria ou serviços externos não essenciais sem quebrar a execução do fluxo da aplicação.
- Como o sistema se comporta quando o usuário opera em um navegador sem suporte a Service Worker ou com modo anônimo estrito?  
  A página deve funcionar de maneira transparente, processando normalmente todas as confirmações e notificações visuais via rede padrão.
- O que acontece se um usuário sem permissão administrativa tentar acionar a exclusão de material?  
  O sistema deve impedir a exibição do botão e bloquear a requisição com resposta de acesso negado.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE garantir que todas as dependências de estilo e scripts de notificação visual sejam carregadas na ordem e com os tipos de conteúdo corretos, garantindo a disponibilidade das funções de notificação em todas as páginas do sistema.
- **FR-002**: O sistema DEVE exibir um diálogo de confirmação com destaque visual antes de submeter qualquer requisição de exclusão definitiva de material.
- **FR-003**: O sistema DEVE restringir a exclusão de materiais exclusivamente a usuários com perfil de Administrador, ocultando o controle de exclusão para perfis não autorizados.
- **FR-004**: O sistema DEVE validar e impedir a exclusão física de materiais que possuam saldo em estoque maior que zero ou registros históricos de movimentação ou contagem de inventário, apresentando ao usuário mensagens de erro objetivas e orientando a inativação do item.
- **FR-005**: O sistema DEVE tornar o campo Código SKU opcional nos formulários de cadastro de material (modal principal e cadastro rápido), ajustando os rótulos e marcadores visuais.
- **FR-006**: Quando um material for criado sem código SKU (ou com valor em branco/nulo), o sistema DEVE gerar automaticamente um código sequencial no formato `GEN-###` (onde `###` possui no mínimo 3 dígitos preenchidos com zeros à esquerda, ex: `GEN-001`, `GEN-002`, `GEN-010`), incrementando o maior número sequencial já registrado com esse prefixo.
- **FR-007**: O mecanismo de cache local em segundo plano do aplicativo DEVE filtrar e gerenciar estritamente requisições da própria aplicação sob protocolos suportados (HTTP/HTTPS padrão), ignorando esquemas de extensões do navegador ou chamadas que não façam parte do ciclo de vida offline da aplicação.
- **FR-008**: Caso ocorra falha de rede em requisições de serviços externos ou de monitoramento, o mecanismo de interceptação de rede em segundo plano NÃO DEVE rejeitar silenciosamente o ciclo de resposta nem gerar respostas inválidas que bloqueiem a execução de scripts da página.

### Key Entities *(include if feature involves data)*

- **Material**: Representa o item cadastrado no almoxarifado. Atributos: identificador, `code_sku` (código SKU manual ou gerado automaticamente `GEN-###`), nome, categoria, unidade de medida, saldo de estoque atual, estoque mínimo, etc.
- **Notificação / Alerta de Sistema**: Representa o feedback visual apresentado ao operador contendo tipo (sucesso, erro, aviso, informação), título e mensagem descritiva.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos cliques no botão de exclusão de material por Administradores abrem a janela de confirmação em menos de 500 milissegundos, sem erros de script no console.
- **SC-002**: 100% das tentativas de cadastro de material com campo Código SKU vazio resultam em materiais cadastrados com sucesso contendo códigos sequenciais únicos no padrão `GEN-###`.
- **SC-003**: 100% das mensagens de feedback de operações (sucesso, erro, validação de regras de exclusão) são exibidas visualmente ao usuário no topo da tela logo após a conclusão da requisição.
- **SC-004**: Redução a zero de exceções não tratadas de carregamento de biblioteca de notificação e de erros de protocolo não suportado no gerenciador de cache local.
- **SC-005**: Usuários sem perfil de Administrador não conseguem visualizar opções nem executar a exclusão de materiais em 100% dos testes de acesso.

## Assumptions

- O prefixo `GEN-` representa "Genérico" para itens cadastrados sem código SKU explícito.
- Caso o usuário edite um material existente, o código SKU previamente atribuído é preservado a menos que o usuário o altere explicitamente.
- O ambiente de produção utiliza conexões seguras sob domínio próprio com suporte a recursos modernos de navegador (PWA / Service Worker).
- Materiais que já foram movimentados devem ser mantidos no banco de dados para garantir a rastreabilidade histórica das entradas e saídas de estoque conforme princípios da constituição do projeto.
