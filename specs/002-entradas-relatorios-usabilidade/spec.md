# Feature Specification: Entradas de Estoque, Usabilidade Inline e Relatórios PDF/Excel

**Feature Branch**: `002-entradas-relatorios-usabilidade`  
**Created**: 2026-08-11  
**Status**: Draft  
**Input**: "Como dar entrada de material já cadastrado, entrada de nota fiscal ou documento de doação? Na tela de movimentação, seria possível cadastrar o beneficiário ou local de destino sem sair da tela de movimentação? O que for necessário para facilitar a usabilidade por parte do usuário, pode melhorar. Modais são mais práticos para cadastro e edição. Relatórios gerenciais para saber o estoque atual, itens com estoque baixo, devoluções em atraso, dentre outros. Opções em PDF ou em excel. Usar logotipo disponível em /public/images"

---

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Lançamento de Entradas de Estoque por Nota Fiscal ou Doação (Priority: P1)

Como Almoxarife do sistema, desejo registrar o documento de entrada (Nota Fiscal ou Termo de Doação) incrementando o saldo em estoque de materiais já cadastrados, informando quantidade, fornecedor/doador e número do documento.

**Why this priority**: O sistema atualmente gerencia saídas e empréstimos, mas precisa registrar a recomposição formal de estoque (compras/doações) com rastreabilidade fiscal/auditoria.

**Independent Test**: Registrar uma entrada por Nota Fiscal `NF-9988` de 100 unidades do material "Cimento CP II" do fornecedor "Votorantim". O saldo disponível do material deve aumentar em 100 unidades e o histórico de entradas deve guardar os dados do documento.

**Acceptance Scenarios**:

1. **Given** um material com estoque atual de 20 unidades, **When** o Almoxarife registra uma entrada por Nota Fiscal com quantidade 50, **Then** o saldo do material é atualizado para 70 unidades e a movimentação de entrada é gravada com o tipo `ENTRY`.
2. **Given** o lançamento de uma entrada por doação, **When** o operador escolhe a origem `DOACAO`, **Then** o sistema permite registrar o nome do doador e o número do documento de doação sem exigir dados fiscais de compra.

---

### User Story 2 - Cadastro Rápido Inline via Modais (Destinos & Beneficiários) (Priority: P1)

Como Almoxarife, desejo cadastrar um novo Beneficiário ou novo Destino diretamente via janela modal durante o preenchimento de uma saída ou entrada, sem sair da tela e sem perder os materiais já selecionados.

**Why this priority**: Melhora drasticamente a usabilidade do usuário no dia a dia, evitando que ele perca o preenchimento de um formulário longo ao descobrir que o beneficiário ou destino ainda não estava cadastrado.

**Independent Test**: Abrir a tela de nova saída, selecionar 3 materiais, clicar no botão "+ Novo Beneficiário", salvar o novo beneficiário no modal e verificar que ele é selecionado automaticamente no formulário mantendo os 3 materiais intactos.

**Acceptance Scenarios**:

1. **Given** o formulário de saída parcialmente preenchido, **When** o usuário clica no botão de adição rápida ao lado do campo de Beneficiário, **Then** um modal abre, permite salvar o cadastro via AJAX, fecha o modal e seleciona o novo beneficiário no campo principal sem recarregar a página.
2. **Given** o formulário de entrada ou saída, **When** o usuário abre o modal de criação rápida de Destino, **Then** o novo destino é cadastrado no banco e passa a estar disponível instantaneamente na lista.

---

### User Story 3 - Relatórios Gerenciais com Exportação PDF e Excel (Priority: P2)

Como Administrador ou Almoxarife, desejo gerar relatórios gerenciais com filtros dinâmicos por período, tipo e status, exportando os resultados em arquivos PDF formatados para impressão e em planilhas Excel (XLSX).

**Why this priority**: Essencial para a prestação de contas da administração da igreja, auditoria de estoque, planejamento de compras e cobrança de devoluções.

**Independent Test**: Acessar o módulo de Relatórios, filtrar por "Empréstimos em Atraso", clicar no botão "Exportar PDF" e verificar se o PDF gerado contém o cabeçalho oficial, o logotipo da CCB e a lista correta de atrasos.

**Acceptance Scenarios**:

1. **Given** a consulta ao relatório de "Posição Geral de Estoque", **When** o usuário clica em "Exportar Excel", **Then** o sistema faz o download imediato da planilha `.xlsx` com colunas de SKU, Nome, Categoria, Estoque Atual e Mínimo.
2. **Given** a geração do relatório de "Devoluções em Atraso" em PDF, **When** o documento é gerado, **Then** o arquivo exibe no topo a logomarca da CCB (`/public/images/CCB_Logo_fundo_claro.png`), o título do relatório e a data/hora de emissão.

---

### User Story 4 - Identidade Visual & Aplicação do Logotipo Oficial CCB (Priority: P2)

Como usuário do sistema, desejo visualizar a marca e logotipos oficiais da CCB na barra superior, página de login, comprovantes e relatórios em PDF.

**Why this priority**: Padronização visual institucional conforme a identidade da Congregação Cristã no Brasil.

**Independent Test**: Verificar se o topo do sistema exibe a marca da igreja e se os comprovantes emitidos em tela e impressão incluem a imagem de cabeçalho `/public/images/CCB_Logo_fundo_claro.png`.

**Acceptance Scenarios**:

1. **Given** a navegação no sistema ou tela de login, **When** a página é carregada, **Then** o logotipo oficial da CCB localizado em `/public/images` é renderizado corretamente com proporções harmoniosas.

---

### Edge Cases

- **Entrada com quantidade zero ou negativa**: O sistema deve rejeitar entradas com quantidade <= 0 no formulário de entrada.
- **Falha no salvamento do modal inline (erro de validação)**: Se o CPF ou código já existir no cadastro rápido por modal, a mensagem de erro deve ser exibida dentro do próprio modal sem fechá-lo ou resetar a página.
- **Exportação de relatórios vazios**: Quando um filtro não retornar resultados, a exportação em PDF/Excel deve gerar um documento limpo informando que não foram encontrados registros para os filtros informados.

---

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE disponibilizar módulo de Entrada de Estoque (tipo `ENTRY`) permitindo associar materiais já cadastrados, tipo de origem (Nota Fiscal, Doação, Compra Direta), número do documento, fornecedor/doador, valor unitário/total e quantidade.
- **FR-002**: O sistema DEVE incrementar automaticamente o saldo do estoque (`current_stock`) do material ao confirmar o registro de uma entrada.
- **FR-003**: O sistema DEVE fornecer modais inline para cadastro rápido de Beneficiários e Destinos nas telas de movimentação sem recarregar a página (via requisição AJAX).
- **FR-004**: O sistema DEVE atualizar dinamicamente as caixas de seleção (`select`) de Beneficiários e Destinos após o salvamento por modal inline, pré-selecionando o item recém-cadastrado.
- **FR-005**: O sistema DEVE disponibilizar uma central de Relatórios Gerenciais com os relatórios de: (a) Posição Geral de Estoque, (b) Alertas de Estoque Mínimo, (c) Empréstimos e Devoluções em Atraso, e (d) Histórico de Entradas/Saídas por Período.
- **FR-006**: O sistema DEVE permitir a exportação de qualquer relatório gerencial em formato PDF formatado para impressão e em formato Excel (XLSX).
- **FR-007**: O sistema DEVE utilizar os logotipos oficiais disponíveis em `/public/images` (`CCB_Logo_fundo_claro.png`, `CCB_Logo_funco_escuro.png`, `CCB_Logo_Reduzido.png`) na interface do sistema, comprovantes de saída e relatórios em PDF.
- **FR-008**: O sistema DEVE permitir a alteração e edição rápida de dados de materiais, beneficiários e destinos utilizando modais na própria listagem das tabelas.
- **FR-009**: Todos os botões de ação situados em cabeçalhos de cards (`.card-header`) DEVEM ser alinhados estritamente à direita da interface (utilizando a classe utilitária `ms-auto`).
- **FR-010**: O sistema DEVE fornecer modais de edição para Beneficiários, Destinos e Materiais nas respectivas tabelas de listagem.
- **FR-011**: O formulário de edição de Materiais NÃO DEVE permitir a alteração direta do saldo atual de estoque (`current_stock`).
- **FR-012**: O sistema DEVE disponibilizar funcionalidade de Ajuste de Estoque / Inventário de Materiais via modal dedicado, exigindo a contagem física do novo saldo e justificativa obrigatória.
- **FR-013**: O sistema DEVE disponibilizar um módulo completo de **Inventário Geral Periódico**, permitindo abrir sessões de inventário com número de controle, responsável (`user_id`), data/hora de abertura e título.
- **FR-014**: A sessão de Inventário Geral DEVE apresentar a lista de todos os materiais ativos com o saldo atual do sistema para entrada da **Contagem Física Real** e cálculo automático da divergência (`Sobra`, `Falta` ou `Conforme`).
- **FR-015**: Ao finalizar um Inventário Geral, o sistema DEVE atualizar atomicamente o saldo de estoque (`current_stock`) de todos os materiais contados com divergência e registrar data/hora de fechamento, total de itens ajustados e auditoria.
- **FR-016**: O sistema DEVE permitir a impressão e exportação em PDF da Termo/Folha de Inventário Geral com a logomarca da CCB, lista de divergências, data/hora e termo de responsabilidade para assinatura de quem realizou.
- **FR-017**: Na modalidade **Empréstimo**, o sistema DEVE permitir a seleção estritamente de materiais retornáveis (`is_returnable = 1`), desabilitando materiais de consumo descartável (`is_returnable = 0`).
- **FR-018**: Na modalidade **Consumo Geral**, o sistema DEVE permitir a seleção estritamente de materiais de consumo (`is_returnable = 0`), desabilitando equipamentos e ferramentas retornáveis (`is_returnable = 1`).
- **FR-019**: Na modalidade **Entrega de EPI**, o sistema DEVE permitir a seleção estritamente de materiais da categoria EPI (`isEpi = true`), desabilitando materiais de outras categorias (como cimento, tintas ou equipamentos gerais). Se o EPI for retornável (ex: Capacete), habilita data de retorno; se for descartável (ex: Luva), registra como consumo sem devolução.
- **FR-020**: O sistema DEVE integrar a biblioteca **Select2** (com tema Bootstrap 5) nos campos de seleção (`<select>`) de Materiais, Beneficiários e Destinos, permitindo busca em tempo real com filtro interativo na digitação.
- **FR-021**: Ao cadastrar um novo usuário, o sistema DEVE enviar automaticamente um e-mail de boas-vindas com a identidade visual da CCB, informando as credenciais de acesso e link para primeiro acesso.
- **FR-022**: O sistema DEVE fornecer o fluxo completo de **Recuperação de Senha ("Esqueci minha senha")** na tela de login, enviando um link com token seguro de redefinição por e-mail e permitindo a atualização da senha.
- **FR-023**: O sistema DEVE exibir **Tooltips interativos** do Bootstrap 5 na listagem e nos formulários de gestão de usuários, descrevendo resumidamente os privilégios de cada perfil (`Administrador`, `Almoxarife`, `Consulta`).
- **FR-024**: O estilo de impressão CSS (`@media print`) DEVE definir a página A4 com margens de 15mm e ajuste de 100% de largura, garantindo visualização perfeita sem necessidade de ajuste de escala no navegador.
- **FR-025**: O sistema DEVE disponibilizar o módulo de **Configurações do Sistema (`/settings`)** para personalização de títulos institucionais, cabeçalhos de comprovantes, relatórios PDF e termo de inventário.

### Key Entities

- **EntryDocument**: Representa o documento fiscal ou termo de doação que deu origem à entrada de estoque (número do documento, tipo de documento, fornecedor/doador, valor total, data de emissão).
- **Movement (Atualizada)**: Passa a suportar também o tipo de movimentação `ENTRY` (Entrada de Estoque).
- **ReportFilter**: Filtros aplicados para geração de relatórios (data inicial, data final, tipo de movimentação, categoria, destino, beneficiário, formato de saída).

---

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: O tempo necessário para dar entrada de uma Nota Fiscal com 5 itens de materiais pré-cadastrados é inferior a 1 minuto.
- **SC-002**: 100% dos cadastros rápidos de beneficiários/destinos via modal inline preservam os itens já preenchidos no formulário principal de saída sem perda de dados.
- **SC-003**: A geração e download de relatórios gerenciais em PDF e Excel é concluída em menos de 3 segundos para até 5.000 registros.
- **SC-004**: 100% dos relatórios em PDF gerados contêm o cabeçalho institucional com o logotipo oficial da CCB.

---

## Assumptions

- O servidor possui extensão PHP `gd` ou `dompdf`/`barryvdh/laravel-dompdf` para renderização de PDFs.
- Os logotipos em `/public/images` serão mantidos no caminho público do servidor web para inclusão nos documentos HTML e PDFs.
- O formato de exportação Excel poderá ser gerado em `.csv` ou `.xlsx` (via pacotes padrão Laravel como `maatwebsite/excel` ou exportador nativo CSV).
