# Feature Specification: Responsividade Total & PWA (Progressive Web App)

**Feature Branch**: `003-responsive-pwa`

**Created**: 2026-08-13

**Status**: Draft

**Input**: User description: "Tornar o sistema totalmente responsivo e implementar pwa"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Experiência Mobile Responsiva Completa no Almoxarifado (Priority: P1)

Como almoxarife ou administrador acessando o sistema através de um smartphone ou tablet, quero utilizar todas as telas do sistema (dashboard, lançamentos de saídas, empréstimos, entradas por nota fiscal, inventário e relatórios) com leiaute 100% adaptado a telas pequenas, para que eu possa realizar contagens e movimentações de estoque diretamente na área física do almoxarifado sem precisar de um computador de mesa.

**Why this priority**: Permite a operação móvel real do almoxarifado no chão de fábrica/depósito, eliminando a dependência de computadores fixos e aumentando a produtividade nas contagens físicas e entregas de materiais.

**Independent Test**: Pode ser testado de forma independente navegando por todas as páginas do sistema em dispositivos móveis (ou simulador de dispositivos no navegador em resoluções de 320px a 768px), verificando a adaptação da sidebar, formulários empilhados, tabelas adaptativas e modais touch-friendly.

**Acceptance Scenarios**:

1. **Given** um usuário acessando o sistema em um dispositivo móvel (largura < 768px), **When** a página carrega, **Then** o menu lateral (sidebar AdminLTE) deve iniciar recolhido por padrão, permitindo abertura/fechamento fluido via toque no ícone hamburguer sem sobrepor permanentemente o conteúdo principal.
2. **Given** um almoxarife na tela de lançamento de saída/empréstimo em um smartphone, **When** ele preenche os campos do formulário, **Then** os controles (Select2, selects nativos, inputs de data e quantidade) devem se empilhar verticalmente em coluna única com área de toque mínima de 44x44px.
3. **Given** um usuário visualizando a listagem de movimentações ou estoque em um dispositivo móvel, **When** a tabela contém muitas colunas, **Then** a visualização deve se adaptar dinamicamente (rolagem horizontal suave com cabeçalho fixo ou formato de cards expansíveis por registro) mantendo as ações primárias visíveis.

---

### User Story 2 - Instalação do Aplicativo e Suporte PWA (Priority: P2)

Como usuário do almoxarifado (administrador ou almoxarife), quero instalar o sistema como um aplicativo nativo (PWA) na tela inicial do meu celular (Android/iOS) ou desktop (Windows/macOS), para que eu possa abrir o sistema diretamente por um ícone dedicado, em modo tela cheia (standalone) e com inicialização ultrarrápida.

**Why this priority**: Melhora drasticamente a experiência do usuário eliminando a barra de navegação do browser, proporcionando sensação de aplicativo nativo institucional CCB e permitindo acesso com um único toque.

**Independent Test**: Pode ser testado abrindo o sistema em navegadores compatíveis (Chrome, Edge, Safari) e acionando o banner/prompt "Instalar Aplicativo", validando a criação do ícone no sistema operacional e a abertura em janela standalone dedicada.

**Acceptance Scenarios**:

1. **Given** um usuário navegando no sistema pela primeira vez em um navegador compatível, **When** o manifesto e os recursos PWA são validados pelo browser, **Then** o sistema deve disponibilizar a opção de instalação ("Adicionar à Tela Inicial" / "Instalar App CCB Almoxarifado") com nome, ícones institucionais e cores do tema CCB.
2. **Given** o aplicativo instalado no dispositivo do usuário, **When** ele clica no ícone da tela inicial, **Then** o sistema deve abrir em janela independente (standalone) sem barras de endereço do navegador, exibindo uma tela de carregamento (splash screen) institucional com o logotipo da Congregação Cristã no Brasil.

---

### User Story 3 - Suporte Offline Elegante e Cache de Recursos Estáticos (Priority: P3)

Como usuário operando o almoxarifado em áreas de baixa conectividade ou com oscilação de Wi-Fi no depósito, quero que o sistema continue carregando a estrutura visual, ícones e uma página explicativa de status offline quando a conexão cair, para que eu não perca dados nem veja erros genéricos de navegador ("Sem internet").

**Why this priority**: Evita frustração do usuário quando a rede Wi-Fi do depósito oscilar, garantindo feedback claro sobre o estado da conexão e preservando a estabilidade da interface.

**Independent Test**: Pode ser testado desativando a conexão de rede no dispositivo (Modo Avião) e navegando entre páginas, verificando se os assets estáticos (CSS, JS, imagens, fontes) continuam sendo servidos via Service Worker e se a página de aviso offline amigável é exibida em caso de falha de requisição de rede.

**Acceptance Scenarios**:

1. **Given** um usuário navegando com o aplicativo PWA instalado, **When** a conexão com a internet é perdida, **Then** os arquivos estáticos da interface (AdminLTE CSS, Bootstrap, JS, logos e ícones) continuam sendo carregados do cache local pelo Service Worker.
2. **Given** uma tentativa de submeter dados ou carregar uma nova página enquanto o dispositivo está totalmente sem internet, **When** a requisição falha, **Then** o sistema exibe uma página de aviso offline amigável e explicativa com botão de "Tentar Novamente", sem quebrar o leiaute.

---

### Edge Cases

- O que acontece quando o usuário tenta submeter um formulário de saída de estoque no modo offline? O sistema deve bloquear o envio com aviso amigável informando que movimentações de estoque exigem validação de saldo no servidor em tempo real.
- Como o sistema se comporta em telas ultrapequenas (largura < 360px)? Os botões de ação e tabelas devem se ajustar sem quebrar a largura total do viewport (sem overflow horizontal indesejado da página inteira).
- Como funciona o PWA no iOS (Safari)? O manifesto deve conter meta tags específicas para iOS (`apple-mobile-web-app-capable`, `apple-touch-icon`) garantindo suporte à instalação no iOS.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE possuir um Manifesto de Aplicativo Web (`manifest.json`) completo, definindo o nome oficial ("Almoxarifado CCB"), nome curto ("Almoxarifado"), cores de tema/fundo institucionais, modo de exibição `standalone` e ícones responsivos em múltiplas resoluções (192x192px, 512x512px, máscara para Android e iOS).
- **FR-002**: O sistema DEVE registrar um Service Worker (`sw.js`) com estratégia de cache adequada para recursos estáticos (CSS, JS, fontes, logotipos) e suporte a fallback de página offline.
- **FR-003**: A interface gráfica DEVE ser totalmente responsiva (breakpoints: xs < 576px, sm ≥ 576px, md ≥ 768px, lg ≥ 992px, xl ≥ 1200px), garantindo adaptação dinâmica de todos os módulos (Dashboard, Lançamentos, Entradas, Devoluções, Inventário, Usuários, Configurações e Relatórios).
- **FR-004**: Todas as tabelas de dados do sistema DEVEM possuir tratamento responsivo em dispositivos móveis (rolagem horizontal touch com indicação visual ou visualização adaptativa por registro).
- **FR-005**: Os formulários de lançamento e modais de cadastro rápido DEVEM se empilhar verticalmente em telas menores que 768px, com espaçamento adequado entre campos e área de toque para botões de no mínimo 44x44px.
- **FR-006**: O sistema DEVE incluir meta tags de controle de Viewport (`viewport-fit=cover`, `width=device-width, initial-scale=1.0`) e suporte a ícones de toque da Apple para dispositivos iOS.
- **FR-007**: O componente de filtro e busca de materiais (Select2 e buscas inline) DEVE se ajustar à largura inteira do contêiner em telas pequenas, permitindo digitação e seleção confortáveis na tela do smartphone.

### Key Entities *(include if feature involves data)*

- **PWA Manifest Settings**: Configuração de metadata da aplicação Web instalável (nome, ícone, tema, orientação, escopo de URLs).
- **Service Worker Cache Store**: Estrutura de armazenamento temporário no cliente para arquivos de estilo (CSS), scripts (JS), fontes institucionais e páginas estáticas de fallback.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% das páginas e módulos do sistema devem passar no teste de compatibilidade móvel (Mobile-Friendly Test) sem estouro horizontal de tela (zero overflow horizontal em 320px).
- **SC-002**: Pontuação de PWA igual ou superior a 90/100 na auditoria do Google Lighthouse para critérios de Manifesto, Service Worker, HTTPS e Responsividade.
- **SC-003**: Tempo de carregamento de páginas repetidas reduzido em no mínimo 40% em dispositivos móveis através do cache de recursos estáticos pelo Service Worker.
- **SC-004**: 100% das ações de clique/toque nos botões de formulários e tabelas em dispositivos móveis devem possuir área de toque mínima de 44x44 pixels.

## Assumptions

- O servidor de hospedagem (HostGator / cPanel) possui certificado SSL ativo (HTTPS), requisito obrigatório para o funcionamento de Service Workers e PWA no navegador.
- Os navegadores utilizados pelos usuários (Chrome, Edge, Safari, Firefox em suas versões modernas) possuem suporte nativo a Web App Manifests e Service Workers.
- Operações que alteram dados do banco de dados (baixa de estoque, devoluções, entradas) exigem conectividade ativa com a internet para garantir a integridade transacional de estoque prevista na Constituição do projeto.
