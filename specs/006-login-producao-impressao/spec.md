# Feature Specification: Adequação da Tela de Login para Produção e Correção da Impressão Direta

**Feature Branch**: `006-login-producao-impressao`

**Created**: 2026-09-02

**Status**: Draft

**Input**: User description: "Sistema já está online, homologado, aprovado. Dados de teste removidos. Usuários iniciais já cadastrados. Preciso alterar a tela de login, removendo os dados de acesso de teste. os botões existentes de impressão, está abrindo sem os dados, tudo em branco, apenas o PDF está gerando correto."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Acesso Seguro e Limpo na Tela de Login (Priority: P1)

Como um usuário cadastrado no sistema do Almoxarifado (Administrador, Almoxarife ou Operador de Consulta), desejo acessar a tela de autenticação limpa, sem credenciais de teste pré-preenchidas ou atalhos de demonstração, para que eu possa inserir minhas próprias credenciais com total privacidade e segurança operacional.

**Why this priority**: O sistema entrou em ambiente de produção homologado. Manter credenciais ou atalhos de teste na tela de login expõe o ambiente e confunde os operadores reais já cadastrados.

**Independent Test**: Pode ser testado acessando a tela inicial de autenticação e verificando que os campos de e-mail e senha iniciam completamente vazios, sem nenhum botão ou sugestão de credenciais de teste, permitindo o preenchimento manual e login com conta real.

**Acceptance Scenarios**:

1. **Given** que o usuário acessa a página de autenticação, **When** a página termina de carregar, **Then** os campos de e-mail e senha devem estar vazios, com apenas os textos de orientação (*placeholders*), e nenhuma credencial ou atalho de teste deve estar visível.
2. **Given** que o usuário digita seu e-mail e senha reais cadastrados, **When** clica no botão para entrar, **Then** o sistema autentica com sucesso e redireciona para a área principal do almoxarifado.
3. **Given** que o usuário erra a senha ou e-mail, **When** a validação falha, **Then** o sistema preserva apenas o e-mail digitado e mantém o campo de senha limpo para nova tentativa.

---

### User Story 2 - Impressão Direta Completa de Comprovantes e Documentos (Priority: P1)

Como almoxarife ou responsável operacional, desejo utilizar o botão de impressão direta nos comprovantes de movimentação e documentos do sistema, para imprimir via impressora física ou diálogo do navegador com todos os dados visíveis, sem páginas em branco ou ausência de informações.

**Why this priority**: A impressão direta é utilizada diariamente no balcão de atendimento para emissão imediata de vias assinadas por voluntários e fornecedores. Imprimir em branco paralisa o atendimento físico.

**Independent Test**: Pode ser testado abrindo o comprovante de qualquer movimentação (entrada, saída, empréstimo) e acionando o botão de impressão direta; o diálogo de impressão deve exibir o cabeçalho institucional, dados do beneficiário/fornecedor, tabela de itens e campos de assinatura idênticos ao documento em tela.

**Acceptance Scenarios**:

1. **Given** que o almoxarife está visualizando um comprovante de movimentação com itens, **When** aciona a opção "Imprimir Comprovante", **Then** a janela de impressão deve apresentar todos os dados do comprovante (cabeçalho, datas, destinatário, materiais, quantidades e campos de assinatura) sem folhas em branco ou cortes indevidos.
2. **Given** que o operador aciona a impressão em qualquer navegador compatível, **When** a visualização prévia é gerada, **Then** elementos de navegação (menus, barras laterais, botões) são ocultados automaticamente, mantendo estritamente o conteúdo do documento para impressão.

---

### User Story 3 - Impressão e Exportação Consistente de Relatórios e Inventários (Priority: P2)

Como administrador ou gestor do almoxarifado, desejo que todas as telas com opções de impressão e relatórios mantenham a mesma integridade e fidelidade dos dados que os arquivos exportados em formato PDF.

**Why this priority**: Garante que qualquer relatório gerencial ou folha de conferência de inventário possa ser impresso diretamente ou exportado com o mesmo nível de qualidade e confiabilidade.

**Independent Test**: Pode ser testado acessando as telas de relatórios e folhas de inventário e validando que os dados exibidos na pré-visualização de impressão refletem exatamente os mesmos dados da consulta e do PDF correspondente.

**Acceptance Scenarios**:

1. **Given** que o gestor está na tela de conferência de inventário ou relatório, **When** solicita a impressão do documento, **Then** os dados tabulares e somatórios são legíveis, formatados para folha padrão e sem páginas adicionais em branco.

---

### Edge Cases

- O que acontece se o usuário tiver preenchido previamente os dados de acesso e o navegador sugerir o preenchimento automático do chaveiro do usuário? O sistema deve permitir o preenchimento automático legítimo do gerenciador do próprio usuário, mas não fornecer valores padrão do sistema.
- O que acontece quando o comprovante possui uma lista extensa de itens que ultrapassa uma única página? A quebra de página durante a impressão deve ocorrer de forma limpa, mantendo o cabeçalho das tabelas e as linhas legíveis, sem gerar páginas em branco ao final do documento.
- O que acontece se o usuário imprimir utilizando temas visuais diferentes (modo claro/escuro) ou zoom do navegador? O layout de impressão deve forçar fundo branco, texto escuro de alto contraste e escala proporcional adequada para papel A4.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE disponibilizar a tela de autenticação sem valores padrão pré-carregados para e-mail e senha.
- **FR-002**: O sistema NÃO DEVE exibir atalhos rápidos, credenciais de demonstração ou botões de preenchimento automático de usuários de teste na tela de login.
- **FR-003**: O sistema DEVE manter o recurso "Lembrar acesso" e o link de recuperação de senha funcionando normalmente para os usuários de produção.
- **FR-004**: O sistema DEVE garantir que a ação de impressão direta nos comprovantes de movimentação renderize todos os dados (identificação institucional, código da movimentação, tipo, datas, responsável, beneficiário/fornecedor, destino, itens e assinaturas).
- **FR-005**: O sistema DEVE ocultar automaticamente menus laterais, barras de navegação superior, rodapés do sistema e botões de ação durante a impressão direta.
- **FR-006**: O sistema DEVE assegurar que a pré-visualização e a saída de impressão não gerem páginas em branco decorrentes de contêineres com dimensões inadequadas ou regras de visibilidade conflitantes.
- **FR-007**: O sistema DEVE preservar a geração e download de arquivos PDF existentes, garantindo equivalência entre a impressão direta no navegador e o PDF baixado.

### Key Entities

- **Credenciais de Acesso**: Conjunto de e-mail e senha digitados pelo operador autenticado.
- **Comprovante de Movimentação**: Documento oficial contendo o registro histórico e itens de entrada, saída, EPI ou empréstimo.
- **Documento para Impressão**: Visão estruturada e formatada para folha A4 com alto contraste e layout sem ruídos de navegação.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos acessos à tela de login exibem campos vazios e limpos, sem qualquer menção a dados de teste ou demonstração.
- **SC-002**: 100% das tentativas de impressão direta de comprovantes geram visualizações completas com todos os dados visíveis na primeira página (ou páginas subsequentes caso o conteúdo exceda uma folha), com zero páginas em branco adicionais.
- **SC-003**: O tempo para um operador emitir um comprovante impresso no balcão é inferior a 5 segundos após a finalização da movimentação.
- **SC-004**: A taxa de sucesso na geração de comprovantes impressos e PDFs atinge 100% sem erros de renderização.

## Assumptions

- Os usuários reais e seus respectivos perfis de acesso já foram devidamente cadastrados no banco de dados de produção.
- Os modelos e relatórios em PDF já estão em conformidade e não necessitam de alteração na estrutura de dados, servindo como referência de conteúdo para a impressão direta.
- O formato de impressão padrão dos almoxarifados é folha A4 em orientação retrato.
