# Data Model: Alteração do Prefixo de SKU para CCB-###

## Entidade Material (`materials`)

### Atributos Impactados
| Campo | Tipo | Regra de Negócio |
|---|---|---|
| `code_sku` | varchar(50) | `nullable`, `unique`, `max:50`. Quando omitido ou nulo na criação, recebe automaticamente `CCB-###` sequencial. |

### Regra de Geração
1. Filtro: `code_sku LIKE 'CCB-%'`.
2. Regex de extração de número: `/^CCB-(\d+)$/`.
3. Próximo sequencial: `max(números) + 1` formatado com `str_pad(..., 3, '0', STR_PAD_LEFT)`.
4. Valor inicial caso nenhum exista: `CCB-001`.
