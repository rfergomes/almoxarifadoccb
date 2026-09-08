# Feature Specification: Ordenação Padrão da Tabela pelo Nome do Material

**Feature Branch**: `013-ordenar-materiais-nome`

**Created**: 2026-09-08

**Status**: Draft

**Input**: User description: "ordenar tabela pelo nome do material"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Consulta do Catálogo em Ordem Alfabética (Priority: P1) 🎯 MVP

Como um operador ou Almoxarife acessando a tela de Catálogo de Materiais & Saldo de Estoque,  
Quero que os materiais sejam listados por padrão em ordem alfabética crescente (A-Z) com base no nome do produto,  
Para que eu consiga localizar e conferir visualmente os materiais de forma rápida, previsível e intuitiva.

**Why this priority**: A navegação alfabética é o padrão mais natural e eficiente para consulta de itens de almoxarifado em depósitos físicos. A listagem por ordem de criação (`latest`) fragmentava itens de mesma natureza em posições dispersas.

**Independent Test**: Acessar `/materials` e verificar se o primeiro item exibido começa com as primeiras letras do alfabeto (ex: itens com "ARAME..." antes de "MARRETA..." e "MARTELO...").

**Acceptance Scenarios**:

1. **Given** que existem materiais cadastrados com nomes diversos (ex: "ARAME", "MARRETA", "MARTELO"),  
   **When** o usuário acessa a listagem principal de materiais,  
   **Then** a tabela deve exibir os itens ordenados alfabeticamente pelo nome de A a Z.
2. **Given** que um novo material que começa com "A" é cadastrado,  
   **When** o usuário recarrega a tabela de materiais,  
   **Then** esse material deve aparecer posicionado alfabeticamente no início da listagem e não no topo deslocando a ordem alfabética.

---

### User Story 2 - Manutenção da Ordenação Alfabética com Filtros e Paginação (Priority: P2)

Como um usuário aplicando filtros por categoria, status de validade ou busca textual na listagem de materiais,  
Quero que os resultados filtrados e as páginas seguintes continuem respeitando a ordenação alfabética por nome,  
Para que a experiência de navegação e localização permaneça consistente em qualquer conjunto de dados pesquisado.

**Why this priority**: Usuários frequentemente filtram por categoria (ex: "Construção Civil", "EPI") ou pesquisam por palavras-chave; os itens filtrados precisam manter a ordem alfabética.

**Independent Test**: Filtrar por uma categoria ou termo de busca e verificar se os itens retornados e suas páginas subsequentes mantêm a ordenação alfabética por nome.

**Acceptance Scenarios**:

1. **Given** que o usuário filtra por uma categoria específica contendo múltiplos materiais,  
   **When** a listagem é atualizada,  
   **Then** os materiais dessa categoria são exibidos estritamente ordenados pelo nome de A a Z.
2. **Given** que a consulta retorna mais registros do que o limite da página (15 itens),  
   **When** o usuário navega para a página 2,  
   **Then** a sequência alfabética continua a partir do último item da página 1.

---

### Edge Cases

- O que acontece com materiais cujos nomes possuem acentos ou caracteres especiais (ex: "Água", "Açúcar", "Óleo")?  
  A ordenação do banco de dados (collation utf8mb4_unicode_ci / padrão do banco) deve ordenar os caracteres acentuados de forma natural e coerente junto às suas letras base.
- O que acontece se dois materiais tiverem o mesmo nome?  
  O sistema utiliza o identificador como critério de desempate consistente.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE ordenar os materiais exibidos na tabela da tela principal (`/materials`) por padrão em ordem alfabética ascendente (A-Z) através do campo `name`.
- **FR-002**: A ordenação alfabética por nome DEVE ser mantida na presença de filtros de busca por texto (`search`), filtro por categoria (`category_id`), filtro por validade (`expiration`) e filtro por patrimônio (`has_patrimony`).
- **FR-003**: A paginação da listagem DEVE preservar a ordenação alfabética em todas as páginas, mantendo os parâmetros de consulta através de `withQueryString()`.

### Key Entities *(include if feature involves data)*

- **Material**: Catálogo de itens do almoxarifado, onde o atributo `name` define a ordenação padrão de apresentação.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% das visualizações da tela de materiais exibem os registros em ordem alfabética A-Z por nome.
- **SC-002**: 100% das consultas filtradas por categoria ou busca mantêm a ordenação alfabética por nome.
- **SC-003**: Zero inconsistências na paginação de resultados.

## Assumptions

- A ordenação padrão por nome atende à necessidade primordial dos operadores de almoxarifado para localização visual imediata de produtos em estoque.
