# Research: Alteração do Prefixo de SKU Automático de GEN-xxx para CCB-xxx

## Technical Context
- **Stack**: PHP 8.3 / Laravel 12 / Eloquent Model `Material` / Blade Templates.
- **Affected Files**:
  - `app/Models/Material.php`: Método `generateNextSku()` e hook `creating` em `booted()`.
  - `resources/views/materials/index.blade.php`: Placeholder do campo SKU no modal de cadastro.
  - `resources/views/partials/modal_quick_material.blade.php`: Placeholder do campo SKU no modal de cadastro rápido.
  - `tests/Feature/MaterialSkuAndDeletionFixTest.php`: Asserções dos testes automatizados de SKU automático.

---

## Findings & Decisions

### 1. Padrão do Prefixo de SKU
- **Decisão**: Alterar o prefixo de geração sequencial de `GEN-` para `CCB-`.
- **Formato**: `CCB-###`, preenchido com zeros à esquerda com 3 dígitos mínimos (ex: `CCB-001`, `CCB-002`, ..., `CCB-010`, `CCB-100`).
- **Algoritmo no Model `Material`**:
  ```php
  public static function generateNextSku(): string
  {
      $existingSkus = static::where('code_sku', 'LIKE', 'CCB-%')->pluck('code_sku');

      $maxNumber = 0;
      foreach ($existingSkus as $sku) {
          if (is_string($sku) && preg_match('/^CCB-(\d+)$/', $sku, $matches)) {
              $num = (int) $matches[1];
              if ($num > $maxNumber) {
                  $maxNumber = $num;
              }
          }
      }

      $nextNumber = $maxNumber + 1;
      $padded = str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

      return "CCB-{$padded}";
  }
  ```
- **Rationale**: A busca por `CCB-%` garante isolamento e evita colisões com outros códigos já existentes ou customizados.

### 2. Interface com o Usuário
- **Decisão**: Atualizar os placeholders dos modais de cadastro de `Vazio para gerar automático (GEN-001)` para:
  `Vazio para gerar automático (CCB-001)`.
- **Rationale**: Fornece feedback imediato e transparente ao usuário sobre o padrão institucional atribuído ao item.
