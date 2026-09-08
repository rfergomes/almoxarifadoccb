# Feature Specification: Alteração do Prefixo de SKU Automático de GEN-xxx para CCB-xxx

**Feature Branch**: `012-prefixo-sku-ccb`

**Created**: 2026-09-08

**Status**: Draft

**Input**: User description: "alterar o prefixo do sku automatico de GEN-xxx para CCB-xxx"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Geração Automática de SKU com Prefixo Institucional CCB-### (Priority: P1) 🎯 MVP

Como um operador ou Almoxarife cadastrando novos materiais no catálogo,  
Quero que o sistema gere automaticamente o código SKU sequencial utilizando o prefixo oficial institucional "CCB-" (ex: `CCB-001`, `CCB-002`, `CCB-003`, etc.) quando eu deixar o campo Código SKU em branco,  
Para que todos os itens do almoxarifado sigam a padronização oficial da entidade e facilitem a identificação e controle nas contagens e etiquetas.

**Why this priority**: A padronização institucional é fundamental para a identificação dos materiais do almoxarifado central. O prefixo `CCB-` alinha o código automático à identidade do projeto em substituição ao código genérico anterior (`GEN-`).

**Independent Test**: Pode ser testado cadastrando um novo material sem preencher o Código SKU e verificando se ele é salvo com o código `CCB-001` (ou o próximo número sequencial caso já existam outros).

**Acceptance Scenarios**:

1. **Given** que o usuário está no modal de cadastro de material ou no modal de cadastro rápido,  
   **When** submete o formulário com o campo Código SKU em branco,  
   **Then** o sistema grava o material atribuindo automaticamente o código sequencial no formato `CCB-###` (ex: `CCB-001`).
2. **Given** que já existem materiais cadastrados com códigos `CCB-001` e `CCB-002`,  
   **When** um novo material é cadastrado sem código SKU,  
   **Then** o sistema gera automaticamente `CCB-003`.
3. **Given** que já existe um material com código `CCB-009`,  
   **When** o próximo material for gerado automaticamente,  
   **Then** o sistema formata o código como `CCB-010`.

---

### User Story 2 - Preservação de Códigos Manuais e Compatibilidade (Priority: P2)

Como um administrador do sistema,  
Quero que o sistema continue permitindo códigos manuais customizados e mantenha a integridade de qualquer código previamente gravado no banco de dados,  
Para que a alteração do prefixo não cause conflitos de integridade nem sobrescreva códigos específicos fornecidos pelos operadores.

**Why this priority**: O sistema deve conviver harmonicamente com materiais já cadastrados, respeitando códigos específicos de fornecedores ou patrimônios quando fornecidos manualmente.

**Independent Test**: Cadastrar um material informando manualmente um código como `MAT-789` ou `FERR-001` e verificar se o código fornecido é preservado sem ser alterado para `CCB-`.

**Acceptance Scenarios**:

1. **Given** que o operador preenche o campo Código SKU com um valor manual (ex: `TINTA-18L`),  
   **When** submete o cadastro com sucesso,  
   **Then** o código `TINTA-18L` é preservado exatamente como digitado.
2. **Given** que existem materiais antigos com outros prefixos no banco de dados (ex: `GEN-001`),  
   **When** novos materiais forem criados sem código SKU,  
   **Then** a busca sequencial considera especificamente os códigos que iniciam com `CCB-`, gerando o próximo número sequencial sem quebrar a unicidade.

---

### User Story 3 - Atualização Visual de Dicas e Placeholders nas Interfaces (Priority: P3)

Como um usuário operando a interface nos formulários de cadastro,  
Quero ver no campo de Código SKU uma dica visual clara indicando que o código automático gerado será no padrão `CCB-001`,  
Para que eu saiba com clareza o formato que o sistema atribuirá caso eu opte por não preencher o campo.

**Why this priority**: Comunicação visual clara no formulário evita dúvidas do operador sobre a obrigatoriedade ou o formato do código automático gerado.

**Independent Test**: Abrir os modais de cadastro de materiais e de cadastro rápido e conferir o texto do placeholder do campo Código SKU.

**Acceptance Scenarios**:

1. **Given** que o usuário abre o modal "Cadastrar Novo Material" na tela de materiais,  
   **When** visualiza o campo Código SKU,  
   **Then** o placeholder exibe `"Vazio para gerar automático (CCB-001)"`.
2. **Given** que o usuário abre o modal de cadastro rápido em uma entrada de estoque,  
   **When** visualiza o campo Código SKU,  
   **Then** o placeholder exibe `"Vazio para gerar automático (CCB-001)"`.

---

### Edge Cases

- O que acontece se o operador digitar o código `CCB-` manualmente sem números?  
  O sistema trata como código manual informado e valida o tamanho e unicidade.
- O que acontece se existirem códigos como `CCB-001` e `CCB-020` com números saltados?  
  O cálculo obtém o maior número existente (`20`) e gera `CCB-021`, garantindo que não haja sobreposição.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE gerar automaticamente o código SKU no formato `CCB-###` (onde `###` possui no mínimo 3 dígitos preenchidos com zeros à esquerda, ex: `CCB-001`, `CCB-002`, `CCB-010`) sempre que um material for cadastrado com o campo `code_sku` vazio, nulo ou composto apenas por espaços.
- **FR-002**: O sistema DEVE calcular o próximo sequencial inspecionando o maior sufixo numérico entre todos os registros da tabela `materials` cujo `code_sku` coincida com o padrão `^CCB-(\d+)$`. Se nenhum registro coincidir, o sequencial gerado DEVE ser `CCB-001`.
- **FR-003**: O sistema DEVE preservar códigos manuais fornecidos pelo usuário, desde que atendam às regras de unicidade e tamanho máximo de 50 caracteres.
- **FR-004**: O sistema DEVE atualizar os placeholders e textos explicativos do campo Código SKU no modal de cadastro principal (`resources/views/materials/index.blade.php`) e no modal rápido (`resources/views/partials/modal_quick_material.blade.php`) para referenciar o novo formato `CCB-001`.

### Key Entities *(include if feature involves data)*

- **Material**: Registro do item do almoxarifado contendo `code_sku` como identificador de catálogo, agora com padrão automático `CCB-###`.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% dos novos materiais cadastrados sem SKU recebem automaticamente código no formato `CCB-###`.
- **SC-002**: 100% dos testes automatizados da aplicação validam a geração e incremento do prefixo `CCB-` com sucesso.
- **SC-003**: Zero colisões de chave única no banco de dados na geração automática de códigos `CCB-###`.

## Assumptions

- O prefixo `CCB-` representa a sigla institucional da Congregação Cristã no Brasil.
- A alteração aplica-se a novos cadastros de materiais; registros existentes com outros códigos continuam válidos e inalterados.
