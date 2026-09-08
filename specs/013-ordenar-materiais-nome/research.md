# Technical Research: Ordenação Padrão da Tabela pelo Nome do Material

**Feature**: [spec.md](spec.md) | **Branch**: `013-ordenar-materiais-nome`

---

## 1. Contexto & Problema

Na tela de catálogo de materiais (`/materials`), os itens eram listados utilizando a instrução Eloquent `$query->latest()`, que equivale a `orderBy('created_at', 'desc')`. Isso fazia com que os materiais mais recentes (ex: `CCB - 05`, `CCB - 04`) aparecessem no topo, espalhando itens da mesma categoria ou com o mesmo tipo de produto ao longo de diferentes páginas dependendo da data de cadastro.

O objetivo do usuário é que a tabela seja ordenada por padrão em ordem alfabética crescente (A-Z) com base no nome do material (`name`), mantendo o comportamento consistente com filtros e paginação.

---

## 2. Decisões Técnicas

### Decisão 1: Ordenação no Back-end via Eloquent Query Builder

- **Decisão**: Alterar no [MaterialController.php](../../app/Http/Controllers/MaterialController.php) a ordenação da consulta de `$query->latest()` para `$query->orderBy('name', 'asc')`.
- **Justificativa**: A listagem utiliza paginação server-side com `paginate(15)->withQueryString()`. Qualquer ordenação realizada no front-end (ex: DataTables / JavaScript) ordenaria apenas os 15 registros da página atual, violando a integridade da navegação alfabética global do estoque. A ordenação no banco de dados garante que a página 1 contenha os itens de A a C, a página 2 de C a F, e assim por diante.
- **Alternativas consideradas**:
  - *DataTables / JS sorting*: Rejeitado porque quebra a paginação server-side do Laravel.
  - *Adicionar parâmetro de query string `?sort=name`*: Desnecessário como exigência inicial, pois o requisito é que a ordenação **padrão** seja sempre por nome.

### Decisão 2: Critério de Desempate e Case-Insensitivity

- **Decisão**: Usar `$query->orderBy('name', 'asc')->orderBy('id', 'asc')` ou `$query->orderBy('name', 'asc')`.
- **Justificativa**: Bancos de dados MySQL/MariaDB configurados com collations `utf8mb4_unicode_ci` ou `utf8mb4_general_ci` realizam ordenação case-insensitive e acentuação natural de forma nativa. O uso de `orderBy('name', 'asc')` atende perfeitamente ao SQLite (em testes) e MariaDB/MySQL (em produção).

---

## 3. Impacto e Riscos

- **Impacto em Filtros**: O filtro por busca textual (`$query->where('name', 'like', ...)`, `category_id`, `expiration`, `has_patrimony`) continuará funcionando normalmente porque o `orderBy('name', 'asc')` é aplicado no final da construção da query, logo antes de `paginate(15)`.
- **Risco**: Risco praticamente nulo, sem impacto destrutivo em dados ou quebra de retrocompatibilidade.
