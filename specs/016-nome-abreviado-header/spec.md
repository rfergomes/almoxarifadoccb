# Feature Specification: Formatação Abreviada do Nome do Usuário no Header

**Feature Branch**: `016-nome-abreviado-header`

**Created**: 2026-09-08

**Status**: Ready for Planning

**Input**: User description: "No header, seria possível apresentar o primeiro e ultimo nome do usuáiro, ou apenas o primeiro nome?"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Exibição Compacta e Responsiva do Nome no Header (Priority: P1)

Como usuário autenticado no Almoxarifado CCB, desejo visualizar meu nome de forma compacta e responsiva no cabeçalho superior (ao lado do meu avatar e do badge de perfil), exibindo meu primeiro e último nome no desktop e apenas meu primeiro nome no mobile, para que a barra de navegação superior não fique sobrecarregada por nomes extensos e mantenha excelente usabilidade e estética em qualquer tamanho de tela.

**Why this priority**: Melhora imediata da ergonomia e legibilidade visual do cabeçalho da aplicação em todas as páginas, impedindo quebras de linha ou compressão do layout em computadores, tablets e smartphones.

**Independent Test**: Fazer login com um usuário cujo nome possua 3 ou mais palavras (ex: "Rodrigo Fernando Gomes Lima") e verificar:
- Em telas desktop/laptops: o cabeçalho exibe o Primeiro e Último Nome ("Rodrigo Lima").
- Em telas mobile/smartphones: o cabeçalho exibe apenas o Primeiro Nome ("Rodrigo").
- Ao clicar no dropdown do usuário: o cabeçalho interno exibe o nome completo original ("Rodrigo Fernando Gomes Lima").

**Acceptance Scenarios**:

1. **Given** um usuário logado com nome composto "Rodrigo Fernando Gomes Lima", **When** ele navega por qualquer tela do sistema em telas médias ou grandes (desktop/laptop), **Then** o cabeçalho superior renderiza o Primeiro e Último Nome ("Rodrigo Lima").
2. **Given** o mesmo usuário em tela móvel/smartphone (< 768px), **When** visualiza o cabeçalho superior, **Then** o nome exibido é sintetizado para apenas o Primeiro Nome ("Rodrigo").
3. **Given** um usuário logado com nome simples de apenas uma palavra (ex: "Almoxarife"), **When** ele acessa o sistema em qualquer resolução, **Then** o cabeçalho exibe exatamente essa palavra sem repetições nem espaços extras.
4. **Given** um usuário com nome composto, **When** ele clica no avatar/nome para abrir o menu dropdown, **Then** o cabeçalho do dropdown exibe o nome completo original ("Conectado como: Rodrigo Fernando Gomes Lima").

---

### User Story 2 - Manutenção da Identificação Completa nos Relatórios e Telas de Gestão (Priority: P2)

Como administrador ou auditor do sistema, desejo que o nome completo do usuário continue sendo preservado e exibido na página de Perfil (`/profile`), na listagem de usuários (`/users`) e nos registros de movimentação/auditoria, garantindo a rastreabilidade total sem perda de dados.

**Why this priority**: Assegura que a formatação do cabeçalho seja estritamente visual e não altere nem trunque os dados cadastrais armazenados no banco.

**Independent Test**: Acessar `/profile` e `/users` e confirmar que o nome do usuário continua exibindo o cadastro integral.

**Acceptance Scenarios**:

1. **Given** que o cabeçalho exibe o nome compacto, **When** o usuário acessa `/profile`, **Then** o campo de edição e o título do perfil continuam exibindo o nome completo cadastrado.
2. **Given** movimentações geradas pelo usuário, **When** visualizadas em relatórios ou tabelas, **Then** o autor da movimentação permanece com seu nome integral.

---

### Edge Cases

- **Usuário com apenas um nome (monônimo)**: Exemplo "Admin" ou "Administrador". O sistema deve exibir o único nome sem duplicação nem espaço adicional tanto no mobile quanto no desktop.
- **Usuários com múltiplos sobrenomes ou agnomes**: Exemplo "Rodrigo Fernando Gomes Lima" ou "João Carlos da Silva". O sistema deve extrair de forma limpa o primeiro nome ("Rodrigo") e o último sobrenome ("Lima"), resultando em "Rodrigo Lima".
- **Nomes com espaços acidentais no início, meio ou fim**: O sistema deve higienizar os espaços antes de extrair os termos.
- **Responsividade em telas móveis (< 576px)**: O componente deve manter o alinhamento harmonioso com o avatar e o badge de papel (Administrador/Almoxarife/Consulta).

---

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O modelo `User` DEVE disponibilizar o accessor `short_name` que retorna o primeiro e o último nome do usuário (ex: "Rodrigo Lima"), ou o próprio nome se for monônimo.
- **FR-002**: O modelo `User` DEVE disponibilizar o accessor `first_name` que retorna apenas o primeiro nome do usuário (ex: "Rodrigo").
- **FR-003**: O componente de navegação do cabeçalho (`resources/views/partials/navbar.blade.php`) DEVE renderizar `short_name` no desktop e `first_name` no mobile através de utilitários responsivos do Bootstrap 5 (`d-none d-md-inline` e `d-inline d-md-none`).
- **FR-004**: O sistema DEVE manter o nome completo original e inalterado na sessão "Conectado como" no menu dropdown do usuário (`Auth::user()->name`).
- **FR-005**: O sistema DEVE preservar a integridade do banco de dados, sem alterar a coluna `name` da tabela de usuários.

---

## Key Entities

- **User**: Entidade que representa o colaborador autenticado no sistema, possuindo atributo `name` (nome completo cadastrado) e atributos derivados para exibição compacta (`first_name`, `short_name`).

---

## Success Criteria *(mandatory)*

- **SC-001**: 100% dos usuários autenticados visualizam o nome de forma compacta (Primeiro e Último Nome no desktop e Primeiro Nome no mobile) em todas as páginas da aplicação sem estouro visual ou quebra indesejada de layout.
- **SC-002**: O menu dropdown e a tela de perfil continuam exibindo o nome completo cadastrado em 100% dos casos.
- **SC-003**: Usuários com apenas um nome ou nomes com múltiplos espaços não apresentam anomalias visuais ou repetições.
