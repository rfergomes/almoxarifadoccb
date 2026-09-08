# Implementation Plan: Alteração do Prefixo de SKU Automático de GEN-xxx para CCB-xxx

**Branch**: `012-prefixo-sku-ccb` | **Date**: 2026-09-08 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/012-prefixo-sku-ccb/spec.md`

## Summary

Substituir o prefixo de geração automática de código SKU de materiais de `GEN-###` para o padrão oficial institucional `CCB-###` (ex: `CCB-001`, `CCB-002`, `CCB-010`). A alteração abrange a rotina sequencial no Model `Material`, os placeholders informativos nos modais de cadastro e a atualização da suíte de testes de integração.

## Technical Context

**Language/Version**: PHP 8.3+ / Laravel 12  
**Primary Dependencies**: Eloquent ORM, Blade, Bootstrap 5  
**Storage**: MariaDB / MySQL (tabela `materials`, coluna `code_sku`)  
**Testing**: PHPUnit / Pest Feature Tests (`tests/Feature/MaterialSkuAndDeletionFixTest.php`)  
**Target Platform**: Aplicação Web Almoxarifado CCB  
**Constraints**: Garantir continuidade sequencial e unicidade sem colidir com códigos pré-existentes.

## Constitution Check

- [x] **Princípio I (Camada de Serviços & POO Estrita)**: Regras de negócio e geração sequencial mantidas no Model de forma encapsulada.
- [x] **Princípio II (Rigor de Tipagem & Padrões PHP 8.3+)**: Tipagem estrita preservada em `generateNextSku(): string`.
- [x] **Princípio III (Rastreabilidade e Integridade)**: Unicidade garantida por busca sequencial ordenada.

## Project Structure

### Documentation (this feature)

```text
specs/012-prefixo-sku-ccb/
├── spec.md
├── plan.md
├── research.md
├── data-model.md
├── contracts/
│   └── material-api.md
├── quickstart.md
└── checklists/
    └── requirements.md
```

### Source Code (repository root)

```text
app/
└── Models/
    └── Material.php                        # Altera prefixo de GEN- para CCB- em generateNextSku()

resources/views/
├── materials/
│   └── index.blade.php                     # Placeholder atualizado para CCB-001
└── partials/
    └── modal_quick_material.blade.php      # Placeholder atualizado para CCB-001

tests/Feature/
└── MaterialSkuAndDeletionFixTest.php       # Atualização de asserções para validar CCB-001, CCB-010
```

## Phase 0: Outline & Research
- Detalhado em [research.md](research.md).

## Phase 1: Design & Contracts
- Detalhado em [data-model.md](data-model.md), [contracts/material-api.md](contracts/material-api.md) e [quickstart.md](quickstart.md).

## Verification Plan
1. Executar bateria de testes:
   ```powershell
   C:\xampp\php\php.exe artisan test tests/Feature/MaterialSkuAndDeletionFixTest.php
   ```
2. Validar criação de materiais com `CCB-001`, `CCB-010` e cadastro rápido via AJAX.
