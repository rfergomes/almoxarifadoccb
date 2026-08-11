# Implementation Plan: Gestão de Almoxarifado Central e Controle de Estoque CCB

**Branch**: `001-gestao-almoxarifado` | **Date**: 2026-08-11 | **Spec**: [spec.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/001-gestao-almoxarifado/spec.md)

**Input**: Feature specification from `/specs/001-gestao-almoxarifado/spec.md`

## Summary

Desenvolver o Sistema Web de Gestão de Almoxarifado Central e Controle de Estoque para a Congregação Cristã no Brasil (CCB). A solução será construída em PHP 8.3+ com Laravel 12+, utilizando a camada de serviços (`StockService`, `LoanService`) para garantir transações atômicas de estoque, RBAC via `spatie/laravel-permission` (Administrador, Almoxarife, Consulta) e interface gráfica baseada no AdminLTE v4 (Bootstrap 5) integrada com SweetAlert2 para confirmações interativas e Toastr para alertas rápidos.

## Technical Context

**Language/Version**: PHP 8.3+ / 8.4 (XAMPP PHP em `C:\xampp\php\php.exe`)  
**Primary Dependencies**: Laravel 12+, `spatie/laravel-permission` (v6+), `admin-lte@4.0.0` (Bootstrap 5), SweetAlert2, Toastr  
**Storage**: MySQL / MariaDB (XAMPP PDO)  
**Testing**: PHPUnit / Pest  
**Target Platform**: Web Server Intranet (XAMPP Apache/PHP Windows)  
**Project Type**: Web Application (Laravel MVC + Blade AdminLTE v4)  
**Performance Goals**: Carregamento do Dashboard < 500ms, gravação de movimentações < 200ms  
**Constraints**: POO rigorosa, PSR-12, `declare(strict_types=1);`, Service Layer encapsulando `DB::transaction`, Enums nativos do PHP, validação via Form Requests, documentação em pt-BR  
**Scale/Scope**: Almoxarifado Central, ~100 Casas de Oração (Destinos), ~500 SKUs de materiais, ~50 beneficiários frequentes  

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Princípio I (Service Layer & POO Estrita):** PASS - Toda a regra de estoque e empréstimos será isolada em `StockService` e `LoanService` dentro de `DB::transaction`.
- **Princípio II (Tipagem & PHP 8.3+/Laravel 12):** PASS - Uso de PSR-12, `strict_types=1`, Enums nativos para tipos/status e Form Requests isolados.
- **Princípio III (Integridade & Rastreabilidade):** PASS - Impedimento de saldo negativo e auditoria obrigatória de `user_id`, `beneficiary_id` e `destination_id`.
- **Princípio IV (EPIs & Empréstimos com Devolução):** PASS - Validação de `ca_number`/`ca_validity` e controle de prazos com status `OVERDUE`.
- **Princípio V (Interface & Feedback):** PASS - Layout AdminLTE v4, modais SweetAlert2 e notificações Toastr.

## Project Structure

### Documentation (this feature)

```text
specs/001-gestao-almoxarifado/
├── plan.md              # Este arquivo
├── research.md          # Fase 0: Pesquisa arquitetural e escolhas tecnológicas
├── data-model.md        # Fase 1: Entidades, esquemas de BD, Enums e relacionamentos
├── quickstart.md        # Fase 1: Guia de execução e cenários de validação
└── contracts/
    └── api-contracts.md # Fase 1: Contratos de rotas, formulários e respostas do sistema
```

### Source Code (repository root)

```text
app/
├── Enums/
│   ├── MovementType.php
│   ├── MovementStatus.php
│   └── ItemStatus.php
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── MovementController.php
│   │   ├── MaterialController.php
│   │   ├── BeneficiaryController.php
│   │   └── DestinationController.php
│   └── Requests/
│       ├── StoreMovementRequest.php
│       ├── ReturnItemRequest.php
│       ├── StoreMaterialRequest.php
│       ├── StoreBeneficiaryRequest.php
│       └── StoreDestinationRequest.php
├── Models/
│   ├── User.php
│   ├── Destination.php
│   ├── Beneficiary.php
│   ├── Category.php
│   ├── Material.php
│   ├── Movement.php
│   └── MovementItem.php
└── Services/
    ├── StockService.php
    └── LoanService.php

database/
├── migrations/
│   ├── 2026_08_11_000001_create_destinations_table.php
│   ├── 2026_08_11_000002_create_beneficiaries_table.php
│   ├── 2026_08_11_000003_create_categories_table.php
│   ├── 2026_08_11_000004_create_materials_table.php
│   ├── 2026_08_11_000005_create_movements_table.php
│   └── 2026_08_11_000006_create_movement_items_table.php
└── seeders/
    ├── RolesAndPermissionsSeeder.php
    └── DatabaseSeeder.php

resources/
└── views/
    ├── layouts/
    │   └── app.blade.php
    ├── dashboard/
    │   └── index.blade.php
    ├── movements/
    │   ├── create.blade.php
    │   ├── show.blade.php
    │   └── index.blade.php
    └── partials/
        ├── navbar.blade.php
        ├── sidebar.blade.php
        └── alerts.blade.php
```

**Structure Decision**: Aplicação Web monolith em Laravel MVC + Blade AdminLTE 4 com Service Layer dedicado para regras de negócio.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| Nenhuma | N/A | N/A |
