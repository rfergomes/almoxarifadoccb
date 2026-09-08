# Feature Specification: Inclusão e Visualização de Imagem no Cadastro de Materiais

**Feature Branch**: `014-imagem-produto-material`

**Created**: 2026-09-08

**Status**: Draft

**Input**: User description: "No cadastro de materiais, gostaria de incluir uma imagem do produto/material. Na tabela, exibir uma miniatura e possibilidade de visualizar a imagem maior. Limitar arquivos apenas de imagens para os produtos/materiais"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Upload e Gestão de Imagem no Cadastro/Edição de Material (Priority: P1) 🎯 MVP

Como Almoxarife ou Administrador ao cadastrar ou atualizar um produto/material,  
Quero anexar uma fotografia ou ilustração representativa do item,  
Para que a identificação física do material no almoxarifado seja clara, facilitando a separação e reduzindo erros operacionais.

**Why this priority**: É a funcionalidade central que permite alimentar e manter o repositório visual de itens do almoxarifado, garantindo a validação adequada para proteger a integridade dos dados.

**Independent Test**: Acessar o formulário de cadastro ou edição de material, selecionar um arquivo de imagem válido (ex: PNG, JPG ou WEBP) e salvar. O material deve salvar com sucesso e associar a imagem ao seu registro.

**Acceptance Scenarios**:

1. **Given** que o usuário está no formulário de inclusão ou edição de material,  
   **When** seleciona um arquivo de imagem válido (JPEG, PNG, WEBP ou GIF) dentro do limite de tamanho estabelecido e submete o formulário,  
   **Then** o material é salvo com a imagem associada e uma mensagem de sucesso é apresentada.
2. **Given** que o usuário tenta enviar um arquivo que não seja uma imagem (exemplo: `.pdf`, `.docx`, `.exe`),  
   **When** submete o formulário ou tenta carregar o arquivo,  
   **Then** o sistema bloqueia o envio e exibe uma mensagem de validação clara informando que apenas arquivos de imagem são aceitos.
3. **Given** que um material já possui uma imagem previamente cadastrada,  
   **When** o usuário edita outros dados do material sem alterar o arquivo de imagem,  
   **Then** a imagem existente permanece inalterada e vinculada ao material.
4. **Given** que o usuário deseja substituir ou remover a imagem de um material existente,  
   **When** seleciona uma nova imagem ou aciona a remoção da imagem atual e salva,  
   **Then** a imagem antiga é desvinculada/substituída e a nova configuração passa a vigorar.

---

### User Story 2 - Exibição de Miniatura na Tabela de Materiais (Priority: P1) 🎯 MVP

Como operador ou Almoxarife consultando a listagem geral de materiais,  
Quero visualizar uma miniatura da imagem de cada produto diretamente em uma coluna da tabela,  
Para que eu consiga reconhecer visualmente o item de imediato sem precisar abrir o registro individual.

**Why this priority**: Proporciona reconhecimento visual imediato durante a conferência diária e buscas no catálogo, acelerando o fluxo de trabalho.

**Independent Test**: Acessar a tela de listagem de materiais e verificar a presença da coluna de imagem com miniaturas nítidas para materiais com foto e indicador padrão para materiais sem foto.

**Acceptance Scenarios**:

1. **Given** que um material possui imagem cadastrada,  
   **When** a tabela de materiais é renderizada,  
   **Then** uma miniatura proporcional, compacta e de boa qualidade é exibida na linha correspondente ao material.
2. **Given** que um material não possui imagem cadastrada,  
   **When** a tabela de materiais é renderizada,  
   **Then** um elemento visual padrão neutro (placeholder de "sem imagem") é exibido de forma consistente, preservando o alinhamento das colunas.

---

### User Story 3 - Visualização Ampliada da Imagem (Priority: P2)

Como usuário na tabela ou nos detalhes de materiais,  
Quero clicar na miniatura para visualizá-la em tamanho ampliado e em alta definição,  
Para conferir detalhes do item (especificações impressas, formato, rótulo ou modelo físico) antes de realizar movimentações.

**Why this priority**: A miniatura na tabela é reduzida para não quebrar o layout; a visualização ampliada permite inspeção detalhada de características do produto.

**Independent Test**: Na listagem de materiais, clicar sobre a miniatura de um material com imagem e verificar a abertura de uma janela de visualização em destaque (modal/lightbox) com a imagem ampliada e título do produto.

**Acceptance Scenarios**:

1. **Given** que um material possui imagem exibida na tabela,  
   **When** o usuário clica sobre a miniatura ou sobre um ícone de visualização,  
   **Then** uma visualização em destaque (modal com imagem ampliada) é aberta, exibindo a foto em maior resolução e o nome do produto.
2. **Given** que a visualização ampliada está aberta na tela,  
   **When** o usuário clica no botão de fechar, clica fora da imagem ou pressiona a tecla de escape,  
   **Then** a visualização fecha suavemente, retornando à tela de navegação anterior exatamente no mesmo ponto de rolagem.

---

### Edge Cases

- **Envio de arquivos não autorizados**: Caso o usuário selecione arquivos de documentos, planilhas ou executáveis renomeados, o sistema deve validar a assinatura real do arquivo de imagem, rejeitando o upload com mensagem amigável.
- **Tamanho excessivo de arquivo**: Caso o arquivo de imagem ultrapasse o limite máximo estipulado (ex: 5MB), o sistema deve impedir o upload e informar o limite aceito de forma orientativa.
- **Exclusão de material**: Quando um material com imagem for excluído, o arquivo físico associado deve ser limpo adequadamente para não gerar acúmulo desnecessário de arquivos órfãos.
- **Imagens em formatos com proporções variadas**: Imagens horizontais, verticais ou quadradas devem ser enquadradas de forma harmoniosa na miniatura (sem distorções na proporção de aspecto).
- **Dispositivos móveis e telas reduzidas**: A tabela e o visualizador ampliado devem ser totalmente responsivos, garantindo que a visualização em destaque se ajuste à largura da tela do celular/tablet.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE disponibilizar campo para upload de imagem no formulário de inclusão e no formulário de edição de materiais.
- **FR-002**: O sistema DEVE restringir e validar estritamente o upload de arquivos, aceitando exclusivamente formatos de imagem (JPG/JPEG, PNG, WEBP e GIF), rejeitando qualquer outro tipo de arquivo.
- **FR-003**: O sistema DEVE limitar o tamanho do arquivo de imagem a no máximo 5MB por upload.
- **FR-004**: O sistema DEVE permitir a substituição da imagem por uma nova ou a remoção da imagem vinculada durante a edição do material.
- **FR-005**: O sistema DEVE exibir uma coluna dedicada à imagem na tabela da tela principal de materiais, apresentando uma miniatura enquadrada sem distorção.
- **FR-006**: Para materiais sem imagem cadastrada, o sistema DEVE exibir um ícone/placeholder padrão e elegante, mantendo a harmonia visual da tabela.
- **FR-007**: O sistema DEVE permitir que o usuário abra a visualização ampliada da imagem ao clicar na miniatura ou em ação correspondente, exibindo o nome do material e botão para fechar.
- **FR-008**: A visualização ampliada DEVE ser fechável por botão dedicado, clique no fundo ou teclado (ESC), mantendo o contexto de navegação e rolagem do usuário.

### Key Entities *(include if feature involves data)*

- **Material**: Registro do item de almoxarifado, contendo agora a referência ao arquivo de imagem associado, além de suas propriedades já existentes (código SKU, nome, categoria, estoque, etc.).
- **Imagem do Material**: Arquivo visual representativo do produto, possuindo caminho de armazenamento, metadados de visualização e vínculo com o respectivo material.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos uploads de arquivos não compatíveis com formatos de imagem são bloqueados com mensagem explicativa ao usuário.
- **SC-002**: Visualização de miniaturas disponível em 100% das linhas da tabela de materiais, com exibição instantânea da imagem ou do placeholder neutro.
- **SC-003**: Abertura da imagem ampliada em menos de 1 segundo após o clique do usuário em condições normais de navegação.
- **SC-004**: Zero distorção na proporção de aspecto (aspect ratio) das imagens apresentadas nas miniaturas e no visualizador ampliado.

## Assumptions

- O upload de imagem é opcional; o cadastro de materiais continua sendo plenamente funcional mesmo se o usuário optar por não incluir uma imagem.
- Apenas uma imagem principal por material é necessária nesta versão.
- Limite de 5MB por imagem é suficiente para equilibrar qualidade visual e desempenho de carregamento do sistema.
- A visualização ampliada será apresentada em camada sobreposta (modal), não redirecionando o usuário para outra página.
