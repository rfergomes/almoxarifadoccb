# Modelo de Dados: Correção do Esquema da Tabela Materials

**Feature**: `018-fix-erro-salvar-material`  
**Data**: 2026-09-18  

---

## 1. Entidade `Material`

Representa o catálogo de itens mantidos e movimentados no almoxarifado.

### 1.1 Esquema Completo da Tabela `materials` (MySQL)

| # | Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|---|
| 1 | `id` | `bigint(20) UNSIGNED` | Não | AUTO_INCREMENT | Identificador primário |
| 2 | `code_sku` | `varchar(50)` | Não | Nenhum | Código SKU único (ex: `CCB-001`) |
| 3 | `name` | `varchar(150)` | Não | Nenhum | Nome do material |
| 4 | `category_id` | `bigint(20) UNSIGNED` | Não | Nenhum | Chave estrangeira para `categories.id` |
| 5 | `image_path` | `varchar(255)` | Sim | NULL | Caminho da imagem/foto no storage |
| 6 | `unit_measure` | `varchar(10)` | Não | 'UN' | Unidade de medida (UN, KG, CX, M, etc.) |
| 7 | `current_stock` | `int(11)` | Não | 0 | Saldo em estoque atual |
| 8 | `minimum_stock` | `int(11)` | Não | 0 | Estoque mínimo de segurança |
| 9 | `ca_number` | `varchar(50)` | Sim | NULL | Número do Certificado de Aprovação (EPI) |
| 10 | `ca_validity` | `date` | Sim | NULL | Validade do CA (EPI) |
| 11 | `expiration_date` | `date` | Sim | NULL | Data de validade para perecíveis |
| 12 | `patrimony_code` | `varchar(50)` | Sim | NULL | Código de patrimônio da entidade |
| 13 | **`notes`** | **`text`** | **Sim** | **NULL** | **Observações gerais e anotações técnicas** *(coluna faltante)* |
| 14 | `is_returnable` | `tinyint(1)` | Não | 0 | Indica se o item é retornável (1) ou de consumo (0) |
| 15 | `status` | `tinyint(1)` | Não | 1 | 1 = Ativo, 0 = Inativo |
| 16 | `created_at` | `timestamp` | Sim | NULL | Data/hora de criação do registro |
| 17 | `updated_at` | `timestamp` | Sim | NULL | Data/hora da última atualização |

---

## 2. Script DDL de Correção (MySQL / phpMyAdmin)

Para aplicar no banco de dados `sibemo33_almoxarifadoccb`:

```sql
-- Adiciona a coluna notes logo após a coluna patrimony_code na tabela materials
ALTER TABLE `materials` 
ADD COLUMN `notes` TEXT NULL AFTER `patrimony_code`;
```

---

## 3. Regras de Validação no Eloquent / FormRequest

```php
'notes' => ['nullable', 'string', 'max:2000']
```

- **Obrigatório**: Não (opcional / nulo permitido).
- **Tipo**: Texto livre.
- **Tamanho Máximo**: 2.000 caracteres.
- **Tratamento de Strings Vazias**: Convertido para `null` automaticamente pelo middleware `ConvertEmptyStringsToNull` do Laravel.
