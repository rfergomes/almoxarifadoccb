# Feature Specification: Upload de Arquivos e Anexos em Entradas e Avarias

**Feature Branch**: `005-upload-documentos-anexos`

**Created**: 2026-08-14

**Status**: Draft

**Input**: User description: "Possibilidade de upload de arquivos (nota fiscal, carta de doação, etc...). Principalemte na entrada de produtos e avarias"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Anexo de Documentos Comprovatórios nas Entradas de Estoque (Priority: P1)

Como almoxarife ou gestor, quero anexar arquivos digitais (como Notas Fiscais em PDF, DANFE, Cartas de Doação ou Recibos) no momento do registro de entrada de produtos no almoxarifado, para garantir a comprovação fiscal, auditabilidade e transparência das aquisições e doações recebidas.

**Why this priority**: A comprovação documental de entradas de materiais e doações é um requisito essencial de prestação de contas, governança e auditoria da entidade, prevenindo divergências físicas e fiscais.

**Independent Test**: Pode ser testado registrando uma nova entrada de materiais anexando um arquivo PDF ou imagem (comprovante/nota fiscal), e em seguida acessando a consulta dessa entrada para visualizar ou baixar o arquivo anexado.

**Acceptance Scenarios**:

1. **Given** a tela de lançamento de nova entrada de estoque, **When** o usuário seleciona um arquivo válido (PDF, JPG, PNG) no campo de anexo de documento, **Then** o sistema armazena o arquivo e o vincula ao registro da entrada.
2. **Given** uma entrada gravada com anexo, **When** o operador consulta os detalhes da movimentação de entrada, **Then** o sistema exibe um botão/link direto para visualização rápida ou download do documento anexado.
3. **Given** a tela de lançamento de entrada, **When** o operador escolhe registrar a entrada sem anexar arquivo, **Then** o sistema permite a gravação da entrada normalmente, tratando o anexo como opcional.

---

### User Story 2 - Anexo de Fotos e Laudos em Ocorrências de Avarias e Danos (Priority: P2)

Como almoxarife, quero anexar fotos de produtos danificados, laudos técnicos ou comprovantes de sinistro ao registrar uma baixa ou ajuste de estoque motivado por avaria/perda, para justificar a saída extraordinária do material.

**Why this priority**: Ocorrendo quebras, avarias ou perda de validade/qualidade, o registro fotográfico ou documental da avaria assegura que a baixa de saldo foi devidamente motivada e comprovada por evidências visuais.

**Independent Test**: Pode ser testado realizando um ajuste/baixa por avaria em um item de estoque anexando uma imagem do produto danificado, e verificando se a imagem fica vinculada ao histórico do ajuste de inventário/avaria.

**Acceptance Scenarios**:

1. **Given** o formulário de registro de avaria/ajuste extraordinário, **When** o usuário anexa uma imagem (JPG, PNG) ou laudo (PDF) demonstrando o dano, **Then** o arquivo é salvo e associado à justificativa de baixa.
2. **Given** um histórico de ajuste/avaria registrado com anexo, **When** o administrador consulta o histórico de movimentações/ajustes, **Then** é possível abrir a imagem do produto avariado diretamente na tela.

---

### User Story 3 - Visualização e Gerenciamento Unificado de Anexos (Priority: P3)

Como gestor ou auditor, quero visualizar uma lista de documentos anexados nos relatórios e fichas do almoxarifado, podendo substituir ou remover um anexo incorreto caso tenha permissão de administração.

**Why this priority**: Facilita a conferência rápida de documentos em auditorias sem necessidade de procurar arquivos físicos em pastas externas.

**Independent Test**: Pode ser testado acessando o relatório de movimentações ou a ficha de uma entrada/avaria e confirmando a presença do indicador visual de documento anexado.

**Acceptance Scenarios**:

1. **Given** o relatório de entradas ou movimentações, **When** um registro possui arquivo anexado, **Then** uma linha de atalho/ícone de clipe exibe o nome do arquivo permitindo a abertura com 1 clique.
2. **Given** um usuário administrador consultando uma entrada com anexo incorreto, **When** ele aciona a opção de alterar anexo, **Then** o sistema permite substituir o arquivo por uma nova versão válida.

---

### Edge Cases

- O que acontece se o usuário tentar anexar um arquivo com formato não permitido (ex.: executável `.exe` ou script `.bat`)? O sistema rejeita o upload no momento do envio e exibe mensagem de alerta sobre os formatos válidos (PDF, PNG, JPG, WEBP).
- O que acontece se o arquivo selecionado exceder o limite máximo de tamanho (ex.: maior que 10MB)? O sistema interrompe o envio e solicita que o usuário selecione um arquivo otimizado ou menor.
- O que ocorre com o arquivo armazenado caso uma entrada seja cancelada ou estornada pela administração? O anexo é preservado no histórico de auditoria do sistema para manter o rastro da operação.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE permitir o envio (upload) de arquivos de comprovante (Nota Fiscal, DANFE, Termo de Doação, Recibo) no formulário de registro de Entradas de Estoque.
- **FR-002**: O sistema DEVE permitir o envio de arquivos de evidência (fotos da avaria, laudos técnicos, BOs) no formulário de registro de Avarias / Ajustes Extraordinários de Estoque.
- **FR-003**: O sistema DEVE aceitar arquivos nos formatos PDF e Imagens (JPG, JPEG, PNG, WEBP), com limite máximo configurado por arquivo (ex.: 10MB).
- **FR-004**: O sistema DEVE tratar o upload de arquivos como um campo opcional, permitindo a gravação do registro mesmo sem anexo quando o usuário não possuir a cópia digitalizada no momento.
- **FR-005**: O sistema DEVE exibir um indicador visual (ícone de anexo/clipe ou badge) e link direto de download/visualização em todas as telas de consulta de entradas, baixas por avaria e histórico de movimentações.
- **FR-006**: O sistema DEVE permitir a visualização rápida de anexos em imagens (modal de pré-visualização) ou abertura de PDFs em nova aba do navegador.
- **FR-007**: O sistema DEVE permitir que usuários com perfil Administrador possam substituir ou remover um documento anexado incorretamente.
- **FR-008**: O sistema DEVE incluir a indicação de existência de anexo e link de acesso nos relatórios gerenciais exportáveis (HTML/PDF).

### Key Entities

- **Anexo / Documento Comprovatório**: Representa o arquivo digital armazenado. Atributos: nome original do arquivo, caminho de armazenamento seguro, tipo MIME (pdf, png, jpg), tamanho em bytes, data de upload, usuário responsável pelo upload e vínculo com o registro (Entrada de Estoque ou Ajuste por Avaria).
- **Entrada de Estoque (com Anexo)**: Registro de recebimento de materiais com metadados do documento fiscal/doação e o arquivo anexado correspondente.
- **Avaria / Baixa por Dano (com Anexo)**: Registro de perda ou avaria de material acompanhado por foto ou laudo comprobatório.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos uploads de arquivos nos formatos válidos (PDF, PNG, JPG, WEBP) até 10MB são processados e armazenados com sucesso.
- **SC-002**: Tempo de abertura/download de um documento anexado inferior a 2 segundos em rede local ou conexão padrão.
- **SC-003**: Operador consegue anexar a Nota Fiscal no registro de entrada em menos de 10 segundos durante o lançamento.
- **SC-004**: Eliminação de 100% das divergências em auditoria sobre a motivação de baixas por avaria através da comprovação por foto ou laudo anexado.

## Assumptions

- Os tipos de arquivo permitidos serão restritos a PDF, PNG, JPG, JPEG e WEBP para garantir segurança e compatibilidade com navegadores web.
- O limite máximo padrão de tamanho por arquivo será de 10MB.
- Os arquivos serão armazenados em diretório protegido no servidor com nomes únicos gerados aleatoriamente para evitar sobreposição de arquivos com o mesmo nome.
- Caso o usuário não anexe um arquivo durante a entrada, ele poderá adicionar o anexo posteriormente na tela de detalhes do registro.
