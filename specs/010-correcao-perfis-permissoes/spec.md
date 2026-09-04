# Feature Specification: Perfis Únicos, Correção de Permissões e Exclusão Segura com Integridade Relacional

**Feature Branch**: `010-correcao-perfis-permissoes`  
**Created**: 2026-09-04  
**Status**: Draft  
**Input**: "Checar a lógica de perfis e permissões. Cadastrei um usuário como almoxarife, na lista de usuários apareceu com dois perfis: Consulta, Almoxarife. Ao logar com este usuário, ficou como Almoxarife, mas na lista de usuários alterou para dois perfis: Almoxarife, Administrador. Incluir opção de excluir usuários, materiais, movimentações exclusivos para administrador desde que o item não tenha nenhuma relação ativa, ou seja, material não pode ser excluído se houver uma movimentação de entrada ou saída."

---

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Atribuição Estrita de Perfil Único no Cadastro e Edição (Priority: P1)

Como Administrador do sistema, ao cadastrar ou editar um usuário e selecionar um perfil de acesso (`Administrador`, `Almoxarife` ou `Consulta`), desejo que o sistema atribua estritamente o perfil escolhido, garantindo que o usuário nunca acumule múltiplos perfis simultâneos no banco de dados.

**Why this priority**: A política de controle de acesso do Almoxarifado CCB define três níveis mutuamente exclusivos. O acúmulo de papéis causa comportamento imprevisível de permissões e incoerências visuais entre a tabela de usuários e o cabeçalho do sistema.

**Independent Test**:
1. Acessar o Gerenciador de Usuários (`/users`).
2. Cadastrar um novo usuário selecionando o perfil "Almoxarife".
3. Verificar na listagem de usuários se o registro exibe única e exclusivamente o badge azul `[Almoxarife]`.
4. Logar com a conta recém-criada e constatar que tanto no topo da página (Navbar) quanto na listagem de usuários o perfil permanece única e exclusivamente como `[Almoxarife]`.

**Acceptance Scenarios**:
1. **Given** o formulário de cadastro de usuário preenchido com o perfil "Almoxarife", **When** o Administrador salva o cadastro, **Then** o sistema vincula exclusivamente o papel "Almoxarife" via sincronização estrita, sem herdar ou acumular outros papéis (como Consulta).
2. **Given** um usuário existente com qualquer perfil, **When** o Administrador edita o usuário alterando seu perfil para "Administrador", **Then** o sistema remove completamente o perfil anterior e mantém apenas o novo perfil.

---

### User Story 2 - Consistência Visual entre o Menu Superior (Navbar) e a Tabela de Usuários (Priority: P1)

Como usuário autenticado (ou Administrador gerenciando acessos), desejo que o perfil exibido na barra superior ao lado do meu nome (`Auth::user()`) coincida com exatidão com o perfil registrado na tabela de usuários.

**Why this priority**: Elimina a discrepância onde a barra superior exibia apenas o primeiro perfil encontrado na relação enquanto a listagem mostrava múltiplos badges conflitantes.

**Independent Test**:
1. Visualizar o perfil do usuário logado no topo direito da tela.
2. Acessar a listagem de usuários e verificar o mesmo usuário.
3. Ambos devem exibir rigorosamente o mesmo perfil e status.

**Acceptance Scenarios**:
1. **Given** que o usuário está autenticado, **When** qualquer página for carregada, **Then** a barra superior exibe exatamente o nome e a cor do badge correspondente ao seu perfil único.
2. **Given** a tabela de gestão de usuários, **When** a lista for renderizada, **Then** cada usuário apresenta exatamente um badge de perfil de acesso.

---

### User Story 3 - Sanitização Automática de Usuários com Múltiplos Perfis Existentes (Priority: P2)

Como Administrador, desejo que o sistema resolva automaticamente qualquer duplicidade histórica na tabela pivô (`model_has_roles`), normalizando registros legados para que cada usuário possua apenas um perfil definitivo.

**Why this priority**: Garante que usuários já cadastrados que ficaram com duplicidade (ex: `Almoxarife` + `Administrador`) sejam sanados de forma transparente no banco de dados.

**Independent Test**:
Executar rotina de validação/migração ou salvar a edição do usuário na interface e verificar que duplicidades em `model_has_roles` são eliminadas.

**Acceptance Scenarios**:
1. **Given** um usuário que acumulou múltiplos registros em `model_has_roles`, **When** o sistema inicializar ou a listagem for consultada/salva, **Then** a duplicidade é regularizada priorizando a hierarquia: Administrador > Almoxarife > Consulta.

---

### User Story 4 - Exclusão Segura de Usuários (Restrito a Administrador) (Priority: P1)

Como Administrador do sistema, desejo poder excluir um usuário do sistema, desde que este usuário não possua movimentações de estoque registradas sob sua responsabilidade e não seja a minha própria conta conectada.

**Why this priority**: Permite higienizar cadastros incorretos ou testes sem comprometer a rastreabilidade e integridade histórica das movimentações do almoxarifado.

**Independent Test**:
1. Tentar excluir a própria conta logada $\rightarrow$ sistema bloqueia com mensagem de impedimento.
2. Tentar excluir um usuário com movimentações lançadas $\rightarrow$ sistema impede a exclusão informando que o operador possui histórico de estoque.
3. Excluir um usuário sem movimentações $\rightarrow$ sistema solicita confirmação via SweetAlert2 e executa a exclusão com sucesso.

**Acceptance Scenarios**:
1. **Given** um usuário sem histórico de movimentações, **When** o Administrador clica em "Excluir" e confirma, **Then** o usuário e seus vínculos de perfil são removidos com sucesso.
2. **Given** um usuário com movimentações vinculadas, **When** o Administrador solicita exclusão, **Then** o sistema rejeita a operação e sugere a inativação do usuário.

---

### User Story 5 - Exclusão Segura de Materiais Sem Relações Ativas (Priority: P1)

Como Administrador do sistema, desejo poder excluir um material cadastrado, desde que ele nunca tenha sido movimentado (sem histórico de entrada por NF/doação e sem saídas/empréstimos).

**Why this priority**: Permite remover itens cadastrados por engano ou duplicados, preservando rigorosamente o histórico fiscal e físico dos materiais que já tiveram movimentação.

**Independent Test**:
1. Acessar a tela de Materiais (`/materials`).
2. Tentar excluir um material que já possui itens movimentados $\rightarrow$ sistema impede a exclusão explicando que há histórico de movimentação.
3. Excluir um material recém-criado sem movimentações $\rightarrow$ exclusão realizada com sucesso após confirmação.

**Acceptance Scenarios**:
1. **Given** um material com saldo ou movimentação vinculada (`movement_items`), **When** o Administrador solicita exclusão, **Then** o sistema bloqueia e informa que o material possui movimentações registradas, permitindo apenas a inativação.
2. **Given** um material sem nenhum registro em `movement_items`, **When** o Administrador confirma a exclusão, **Then** o material é excluído do banco de dados.

---

### User Story 6 - Exclusão Segura de Movimentações (Restrito a Administrador) (Priority: P2)

Como Administrador do sistema, desejo poder excluir uma movimentação de estoque registrada indevidamente, desde que seja garantida a reversão atômica do saldo em estoque e que não existam empréstimos pendentes de devolução em aberto com terceiros.

**Why this priority**: Permite cancelar/reverter lançamentos incorretos de entrada ou saída, estornando saldos sem deixar inconsistências contábeis.

**Independent Test**:
1. Cadastrar uma saída para consumo de 5 unidades de um material de teste.
2. Como Administrador, acionar a exclusão da movimentação.
3. Confirmar que o saldo do material é restaurado em +5 unidades e o registro da movimentação é removido.

**Acceptance Scenarios**:
1. **Given** uma movimentação de saída para consumo, **When** o Administrador confirma a exclusão, **Then** o estoque é recomposto e a movimentação é deletada dentro de uma transação segura.
2. **Given** uma movimentação de empréstimo com itens pendentes, **When** o Administrador solicita a exclusão, **Then** os itens pendentes são regularizados e o saldo devolvido ao almoxarifado.

---

### Edge Cases

- **Tentativa de exclusão por Almoxarife ou Consulta**: O sistema rejeita via middleware e gates com erro 403.
- **Exclusão do último Administrador**: O sistema bloqueia a exclusão ou alteração de perfil se restar apenas um Administrador ativo.
- **Material com saldo em estoque**: Se o material tiver estoque físico diferente de zero, ele não pode ser excluído diretamente.

---

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O método `UserController@store` DEVE utilizar `syncRoles([$data['role']])` em vez de `assignRole(...)`, garantindo que o novo usuário tenha exclusivamente o perfil selecionado.
- **FR-002**: O método `UserController@update` DEVE manter o uso de `syncRoles([$data['role']])` e limpar o cache de permissões do Spatie.
- **FR-003**: A barra superior (`navbar.blade.php`) e a tabela de usuários (`users/index.blade.php`) DEVEM exibir com precisão e consistência o perfil único do usuário.
- **FR-004**: O sistema DEVE implementar o método `UserController@destroy` exclusivo para Administradores (`manage-users`), com validação impeditiva se `auth()->id() === $user->id` ou se `$user->movements()->exists()`.
- **FR-005**: O sistema DEVE implementar o método `MaterialController@destroy` exclusivo para Administradores (`manage-materials` com perfil Administrador), com validação impeditiva se `movementItems()->exists()` ou `current_stock > 0`.
- **FR-006**: O sistema DEVE implementar o método `MovementController@destroy` exclusivo para Administradores (`manage-users` / Administrador), estornando saldos de estoque via `StockService` dentro de transação `DB::transaction`.
- **FR-007**: Todas as ações de exclusão DEVEM possuir confirmação explícita no front-end via SweetAlert2 e mensagens claras de feedback de sucesso ou impedimento.

---

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos usuários possuem exatamente 1 perfil associado no banco e na interface.
- **SC-002**: Zero exclusões acidentais de registros que possuam vínculos ativos de histórico ou auditoria.
- **SC-003**: 100% de reversão exata do saldo de estoque em caso de exclusão autorizada de movimentações.
- **SC-004**: Botões e rotas de exclusão acessíveis estritamente por usuários com perfil `Administrador`.
