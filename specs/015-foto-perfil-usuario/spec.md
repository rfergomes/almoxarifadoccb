# Feature Specification: Perfil de Usuário com Foto e Exibição no Header e Tabela

**Feature Branch**: `015-foto-perfil-usuario`

**Created**: 2026-09-08

**Status**: Draft

**Input**: User description: "Incluir foto de perfil do usuário e exibir no perfil, header e tabela de usuários. Criar a página de perfil de usuário, possibilitando alterar imagem, corrigir nome. Bem elegante e funcional"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Página de Meu Perfil e Gestão de Dados Pessoais (Priority: P1) 🎯 MVP

Como usuário autenticado do sistema (Administrador, Almoxarife ou Consulta),  
Quero acessar uma página dedicada de "Meu Perfil" com design moderno e elegante,  
Para que eu possa atualizar meu nome de exibição, enviar uma foto de perfil e manter minhas informações pessoais corretas.

**Why this priority**: É o ponto central onde o usuário tem autonomia para personalizar sua identidade, corrigir a grafia do seu nome e enviar ou alterar sua foto de perfil.

**Independent Test**: Acessar o menu do usuário no topo da tela, clicar em "Meu Perfil", alterar o nome para um novo valor, carregar uma imagem válida (PNG/JPG) e salvar. A página deve confirmar o sucesso e exibir imediatamente os novos dados e a nova foto.

**Acceptance Scenarios**:

1. **Given** que o usuário está autenticado no sistema,  
   **When** acessa a página de Perfil (`/profile`),  
   **Then** visualiza um cartão/painel elegante exibindo sua foto atual (ou avatar padrão), seu nome, seu e-mail, seu perfil de acesso (com badge colorido) e campos para edição.
2. **Given** que o usuário está na página de perfil,  
   **When** seleciona um arquivo de imagem válido (JPEG, PNG, WEBP ou GIF até 5MB) e/ou corrige seu nome e clica em "Salvar Alterações",  
   **Then** os dados são atualizados, a nova foto é processada e uma notificação de sucesso é exibida.
3. **Given** que o usuário tenta enviar um arquivo que não é imagem (ex: `.pdf`, `.docx`),  
   **When** submete o formulário,  
   **Then** o sistema rejeita o arquivo e exibe mensagem de validação clara informando que apenas imagens são aceitas.
4. **Given** que o usuário possui uma foto cadastrada e deseja removê-la,  
   **When** seleciona a opção "Remover Foto" e confirma,  
   **Then** a foto é desvinculada do perfil, o arquivo é removido com segurança e o sistema passa a exibir o avatar padrão.

---

### User Story 2 - Exibição do Avatar no Header da Aplicação (Priority: P1) 🎯 MVP

Como usuário logado navegando em qualquer módulo do Almoxarifado,  
Quero ver minha foto de perfil em formato de avatar circular no canto superior direito do cabeçalho (header),  
Para ter uma experiência personalizada, profissional e rápida identificação da conta ativa, com acesso imediato à página de perfil pelo menu dropdown.

**Why this priority**: Substitui o ícone genérico azul pelo avatar personalizado na barra superior em 100% das páginas da aplicação, reforçando a identidade visual do usuário.

**Independent Test**: Carregar uma foto no perfil e navegar por telas do sistema; verificar se o cabeçalho superior direito renderiza o avatar circular nítido ao lado do nome do usuário e se o menu dropdown contém o atalho "Meu Perfil".

**Acceptance Scenarios**:

1. **Given** que o usuário possui foto cadastrada,  
   **When** visualiza o header da aplicação,  
   **Then** uma miniatura circular da foto (avatar) é renderizada ao lado do seu nome, mantendo proporção 1:1 sem distorção.
2. **Given** que o usuário não possui foto cadastrada,  
   **When** visualiza o header,  
   **Then** um avatar elegante com ícone neutro de usuário ou iniciais é apresentado harmonicamente.
3. **Given** que o usuário clica no seu nome/avatar no header,  
   **When** o menu suspenso é aberto,  
   **Then** é exibida a opção "Meu Perfil" direcionando diretamente para a tela de gerenciamento de perfil.

---

### User Story 3 - Exibição da Foto na Tabela de Gestão de Usuários (Priority: P2)

Como Administrador acessando o módulo de Usuários do Sistema (`/users`),  
Quero que cada linha da tabela exiba a foto de perfil do respectivo usuário em vez do ícone genérico,  
Para facilitar a conferência visual rápida dos operadores e membros cadastrados.

**Why this priority**: Complementa a gestão de usuários com identificação visual enriquecida no catálogo administrativo.

**Independent Test**: Acessar `/users` como Administrador e conferir se os usuários com foto exibem seu avatar circular e os usuários sem foto exibem o placeholder correspondente.

**Acceptance Scenarios**:

1. **Given** que existem usuários com foto e sem foto cadastrados,  
   **When** o Administrador acessa a tela `/users`,  
   **Then** a coluna com o nome do usuário apresenta o avatar circular correspondente para quem tem foto e um avatar placeholder para quem não tem.
2. **Given** que um Administrador edita um usuário através do modal de gestão,  
   **When** visualiza as informações,  
   **Then** pode visualizar a foto do usuário e, se necessário, remover ou atualizar a imagem.

---

### Edge Cases

- **Troca de imagem com cache no navegador**: A URL da nova imagem gerada deve possuir nome com identificador único (UUID) para evitar que o navegador mantenha a imagem antiga em cache após a atualização.
- **Remoção de usuário**: Ao excluir um usuário do sistema (quando permitido pelas regras de integridade), o arquivo físico de avatar deve ser limpo do disco para evitar arquivos órfãos.
- **Nomes longos no header**: O header deve manter truncamento responsivo e layout estável mesmo para usuários com nomes extensos e com foto carregada.
- **Tamanho e proporção variada de fotos enviadas**: Fotos verticais ou horizontais enviadas pelo usuário devem ser centralizadas e cortadas circularmente em proporção perfeita (aspect ratio 1:1 com `object-fit: cover`).

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE disponibilizar uma rota e tela dedicada de perfil do usuário logado (`/profile`).
- **FR-002**: A tela de perfil DEVE permitir a alteração do nome do usuário e o upload opcional de uma foto de perfil.
- **FR-003**: O sistema DEVE validar estritamente os uploads de foto de perfil, aceitando apenas arquivos de imagem (JPEG, PNG, WEBP e GIF) com tamanho máximo de 5MB.
- **FR-004**: O sistema DEVE permitir a remoção da foto de perfil existente, restaurando o avatar padrão.
- **FR-005**: O header principal da aplicação DEVE exibir o avatar do usuário logado (foto personalizada ou ícone/placeholder padrão circular).
- **FR-006**: O menu dropdown do usuário no header DEVE conter um item de navegação claro para "Meu Perfil".
- **FR-007**: A tabela de gerenciamento de usuários (`/users`) DEVE exibir o avatar circular de cada usuário cadastrado na listagem.
- **FR-008**: Administradores DEVEM poder visualizar e gerenciar (ou remover) a foto do usuário através do formulário de edição de usuários.
- **FR-009**: A tela de perfil DEVE também disponibilizar de forma elegante um formulário opcional para alteração de senha de acesso com confirmação e validação de segurança.

### Key Entities *(include if feature involves data)*

- **User**: Entidade de usuário do sistema (`users`), contendo o novo atributo `avatar_path` (nullable string), além dos atributos existentes (`name`, `email`, `password`, `status`).

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos usuários autenticados conseguem atualizar seu nome e foto em menos de 3 cliques a partir de qualquer tela do sistema.
- **SC-002**: 100% das imagens de avatar são renderizadas em formato circular proporcional sem distorção visual em telas desktop e mobile.
- **SC-003**: Atualização do avatar reflete imediatamente no header da aplicação após salvar a alteração.
- **SC-004**: Bloqueio de 100% dos arquivos que não atendem aos formatos de imagem ou excedem 5MB com mensagens de feedback em pt-BR.

## Assumptions

- O upload de foto de perfil é opcional; usuários que não cadastrarem foto continuam utilizando o sistema normalmente com avatar neutro.
- A alteração do e-mail do usuário permanece restrita a administradores por motivos de segurança e governança institucional, enquanto a correção do nome próprio é livre para o próprio titular.
- O armazenamento físico dos avatares utiliza o disco `public` do Laravel em `storage/app/public/avatars/`.
