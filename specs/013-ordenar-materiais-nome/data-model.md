# Data Model: Ordenação Padrão da Tabela pelo Nome do Material

**Feature**: [spec.md](spec.md) | **Branch**: `013-ordenar-materiais-nome`

---

## 1. Entidades Envolvidas

### Material (`materials`)

A tabela `materials` já possui todos os campos necessários. Nenhuma alteração estrutural no banco de dados (migration) é necessária.

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | BIGINT UNSIGNED (PK) | Identificador único do material |
| `name` | VARCHAR(255) | Nome do material (critério primário de ordenação `ASC`) |
| `code_sku` | VARCHAR(50) | Código SKU (ex: CCB-001) |
| `category_id` | BIGINT UNSIGNED (FK) | Categoria associada |
| `current_stock` | DECIMAL(10,2) | Quantidade em estoque |
| `unit_measure` | VARCHAR(10) | Unidade de medida |
| `created_at` | TIMESTAMP | Data de cadastro |
| `updated_at` | TIMESTAMP | Data de atualização |

---

## 2. Regras de Ordenação

- **Ordenação Padrão**: `ORDER BY materials.name ASC`
- **Índice**: O campo `name` é texto e frequentemente pesquisado junto aos filtros.
- **Relacionamento com Paginação**: Comprimento fixo de 15 itens por página via `paginate(15)->withQueryString()`.
