# Data Model: Correção de Exclusão de Material e Geração de SKU

## 1. Entidade Material (`materials`)

### Atributos e Regras
| Campo | Tipo | Validação / Regras | Descrição |
|---|---|---|---|
| `id` | bigint (PK) | Auto-increment | Identificador único do material. |
| `code_sku` | varchar(50) | `nullable`, `unique`, `max:50` | Código de identificação SKU. Opcional na entrada. Caso nulo ou em branco na criação, recebe automaticamente `GEN-###` sequencial. |
| `name` | varchar(150) | `required`, `max:150` | Nome/descrição do material. |
| `category_id` | bigint (FK) | `required`, `exists:categories,id` | Categoria vinculada. |
| `unit_measure` | varchar(20) | `required`, `max:20` | Unidade de medida (UN, KG, CX, M, etc.). |
| `current_stock` | int | `min:0`, não alterável diretamente no cadastro | Saldo físico atual em estoque. Se for > 0, impede a exclusão física do material. |
| `minimum_stock` | int | `required`, `min:0` | Estoque mínimo para alertas de reposição. |
| `is_returnable` | boolean | `required`, boolean | Define se o item é consumível ou retornável/ferramenta. |
| `status` | boolean | boolean | Status ativo (`true`) ou inativo (`false`). Itens com movimentações anteriores não podem ser excluídos, devendo ser inativados. |

### Regras de Transição e Validação de Negócio

1. **Geração Sequencial de SKU (`GEN-###`)**:
   - Critério: Campo `code_sku` nulo ou vazio no evento `creating`.
   - Algoritmo:
     1. Busca todos os `code_sku` com padrão `GEN-%`.
     2. Extrai os sufixos numéricos inteiros via regex `^GEN-(\d+)$`.
     3. Obtém o valor máximo `N`.
     4. Retorna `'GEN-' . str_pad((string)($N + 1), 3, '0', STR_PAD_LEFT)`.
     5. Exemplo: se o maior for `GEN-002`, gera `GEN-003`. Se for o primeiro, gera `GEN-001`. Se existirem `GEN-001` e `GEN-009`, gera `GEN-010`.

2. **Exclusão Física Segura (Safe Deletion)**:
   - Permissão exigida: Perfil `Administrador` (`manage-users`).
   - Bloqueio 1: Se `$material->movementItems()->exists()`, retorna erro e bloqueia exclusão.
   - Bloqueio 2: Se `$material->current_stock > 0`, retorna erro e bloqueia exclusão.
   - Bloqueio 3: Se `$material->inventoryItems()->exists()`, retorna erro e bloqueia exclusão.
   - Sucesso: Remove o registro e redireciona com mensagem flash de sucesso.
