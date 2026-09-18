# Feature Specification: Correção do Erro ao Salvar Material

**Feature Branch**: `018-fix-erro-salvar-material`

**Created**: 2026-09-18

**Status**: Draft

**Input**: User description: "Erro ao salvar material" acompanhado de evidência de erro HTTP 500 (Server Error) na aplicação e inspeção de estrutura no phpMyAdmin (banco de dados `sibemo33_almoxarifadoccb`, tabela `materials`) evidenciando a ausência do campo `notes` (observações).

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Cadastro e Atualização de Material com Sucesso (Priority: P1)

Como um almoxarife ou administrador do sistema, desejo cadastrar ou editar materiais preenchendo todos os campos cadastrais (incluindo observações, unidade de medida, estoque mínimo e foto opcional) e concluir a gravação com sucesso, sem interrupções por erros internos de servidor (HTTP 500).

**Why this priority**: É a funcionalidade central do módulo de materiais. A ocorrência de erro 500 impede completamente a entrada de novos itens no almoxarifado e paralisa a operação.

**Independent Test**: Pode ser testado de forma independente acessando o módulo de materiais, abrindo o modal de "Cadastrar Novo Material", preenchendo as informações obrigatórias e opcionais, clicando em salvar e verificando se o material é criado com mensagem de sucesso na listagem.

**Acceptance Scenarios**:

1. **Given** que o usuário está autenticado com permissão para gerenciar materiais, **When** preenche o formulário de cadastro com nome, categoria, unidade de medida, estoques e observações válidas e clica em salvar, **Then** o sistema grava o registro com sucesso, exibe uma notificação amigável de confirmação e exibe o novo material na listagem sem apresentar erro 500.
2. **Given** um material já existente no almoxarifado, **When** o usuário edita seus dados cadastrais (como observações ou nome) e submete o formulário, **Then** as alterações são gravadas com sucesso e refletidas na interface.

---

### User Story 2 - Resiliência e Feedback Amigável em Falhas de Persistência (Priority: P2)

Como usuário do sistema, caso ocorra qualquer falha inesperada durante a gravação de dados, desejo receber um aviso claro e compreensível sobre o ocorrido sem que as informações digitadas no formulário sejam descartadas.

**Why this priority**: Erros genéricos de servidor (HTTP 500 em tela branca) causam frustração, perda de dados digitados e sensação de instabilidade no sistema.

**Independent Test**: Simular falha de persistência ou submissão de dados inválidos e validar se o sistema mantém o estado dos campos preenchidos e exibe mensagem amigável com indicação do problema.

**Acceptance Scenarios**:

1. **Given** que o usuário preencheu um formulário de material extenso, **When** ocorrer uma exceção inesperada durante a gravação, **Then** o sistema registra o log técnico detalhado e apresenta uma mensagem clara ao usuário, preservando os campos preenchidos na tela para reenvio.

---

### User Story 3 - Alinhamento Estrutural do Banco de Dados entre Ambientes (Priority: P3)

Como administrador ou mantenedor do sistema, necessito que a estrutura do banco de dados (especialmente no servidor de produção/hospedagem MySQL `sibemo33_almoxarifadoccb`) esteja 100% sincronizada com os campos exigidos pela aplicação, dispondo de migrações e instruções DDL diretas para execução ágil.

**Why this priority**: Evita inconsistências de esquema estrutural (colunas ausentes) que ocasionam falhas imediatas de SQL (`Unknown column in field list`) em ambientes que utilizam phpMyAdmin ou deploy manual.

**Independent Test**: Executar a migração ou comando DDL no banco MySQL e verificar se todas as colunas declaradas nos modelos e requisições constam na tabela `materials`.

**Acceptance Scenarios**:

1. **Given** o banco de dados MySQL em uso pela aplicação, **When** as migrações estruturais forem aplicadas ou o script DDL for executado, **Then** a tabela `materials` passa a conter a coluna `notes` com tipo e configurações compatíveis.

---

### Edge Cases

- **Formulário submetido sem preencher o campo de observações**: O sistema deve salvar normalmente com valor nulo ou vazio no campo, sem gerar erros de constraint ou validação.
- **Banco de dados com migração pendente no ambiente de produção**: A aplicação deve tratar a ausência transitória de colunas opcionais ou fornecer script SQL direto para que o administrador execute imediatamente no phpMyAdmin.
- **Tamanho excessivo no texto de observações**: Se o usuário colar um texto muito longo (acima de 2.000 caracteres), o sistema deve barrar na validação antes da consulta SQL e informar o limite ao usuário.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE permitir a criação de materiais com sucesso via formulário de cadastro, persistindo todos os dados validados sem lançar exceções 500.
- **FR-002**: O banco de dados da aplicação DEVE conter a coluna `notes` na tabela `materials` em conformidade com a modelagem do sistema, suportando textos descritivos de observações.
- **FR-003**: O sistema DEVE disponibilizar o comando/script de migração e o comando SQL direto compatível com phpMyAdmin/MySQL para inserção da coluna `notes` caso o ambiente não suporte execução direta via terminal artisan.
- **FR-004**: O sistema DEVE assegurar que a atualização cadastral de materiais existentes processe e persista o campo `notes` adequadamente.
- **FR-005**: O sistema DEVE registrar em log adequado quaisquer exceções capturadas durante operações de persistência para facilitar diagnósticos operacionais.

### Key Entities *(include if feature involves data)*

- **Material**: Representa os itens gerenciados pelo almoxarifado. Possui identificador único, código SKU, nome descritivo, categoria associada, unidade de medida, saldos de estoque (atual e mínimo), dados de EPI/CA, datas de validade/patrimônio, caminho da foto e texto descritivo de observações (`notes`).

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Redução da taxa de erros HTTP 500 ao salvar ou editar materiais para 0% em operação regular.
- **SC-002**: 100% dos materiais cadastrados com ou sem observações são gravados com sucesso e exibidos imediatamente na listagem.
- **SC-003**: Tempo total de resposta na submissão de cadastro de material inferior a 2 segundos.
- **SC-004**: 100% de conformidade entre a estrutura da tabela `materials` no banco de dados e os atributos manipulados pela aplicação.

## Assumptions

- O ambiente onde o erro foi visualizado está conectado ao banco de dados MySQL `sibemo33_almoxarifadoccb` no qual a migration `2026_09_18_000001_add_notes_to_materials_table.php` ainda não foi executada na tabela `materials`.
- O usuário possui acesso ao phpMyAdmin ou permissão para executar comandos no servidor para sincronização do esquema da tabela.
- O campo `notes` é opcional (nullable), com limite de até 2.000 caracteres no formulário.
