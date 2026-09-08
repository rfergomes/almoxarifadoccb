# Data Model: Formatação Abreviada do Nome do Usuário no Header

**Feature**: `016-nome-abreviado-header`
**Date**: 2026-09-08

## Entities & Accessors

### Entidade: `User` (`App\Models\User`)

Esta feature não adiciona nem modifica colunas no banco de dados. Ela adiciona atributos virtuais (accessors) ao modelo Eloquent para consumo na camada de apresentação:

#### Atributos Existentes no Banco:
| Campo | Tipo | Descrição |
|---|---|---|
| `id` | bigint (PK) | Identificador único do usuário |
| `name` | string (150) | Nome completo cadastrado |
| `email` | string (150) | E-mail corporativo |
| `avatar_path` | string (255, nullable) | Caminho relativo do avatar |
| `status` | boolean | Status da conta (ativo/inativo) |

#### Novos Accessors Derivados (In-Memory):

1. **`first_name`** (`getFirstNameAttribute(): string`):
   - **Descrição**: Extrai a primeira palavra do nome.
   - **Exemplo**: `"Rodrigo Fernando Gomes Lima"` -> `"Rodrigo"`.
   - **Regra**: Limpa espaços com `trim()`, quebra por espaços múltiplos com `preg_split('/\s+/', $name)`. Se vazio, retorna string vazia ou `"Usuário"`.

2. **`short_name`** (`getShortNameAttribute(): string`):
   - **Descrição**: Extrai o primeiro e o último nome do usuário.
   - **Exemplo**: `"Rodrigo Fernando Gomes Lima"` -> `"Rodrigo Lima"`.
   - **Exemplo monônimo**: `"Administrador"` -> `"Administrador"`.
   - **Regra**: Se houver apenas 1 elemento no array de palavras, retorna a própria palavra. Se houver 2 ou mais elementos, retorna `$words[0] . ' ' . end($words)`.
