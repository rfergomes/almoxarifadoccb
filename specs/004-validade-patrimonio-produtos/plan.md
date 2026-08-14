# Implementation Plan: Controle de Validade e Registro de Patrimônio de Produtos

**Branch**: `004-validade-patrimonio-produtos` | **Date**: 2026-08-14 | **Spec**: [spec.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/004-validade-patrimonio-produtos/spec.md)

**Input**: Feature specification from `/specs/004-validade-patrimonio-produtos/spec.md`

## Summary

Implementação do controle de data de validade para produtos/insumos perecíveis (como tintas, massas e grafiatos) e adição do campo de código de patrimônio para equipamentos e ferramentas pertencentes à entidade. O plano engloba a atualização da tabela `materials`, a adição do Enum `ExpirationStatus`, a extensão das regras de negócio em `StockService` e `ReportService`, além do aprimoramento das telas de materiais, relatórios e dashboard no AdminLTE v4 com feedback via SweetAlert2/Toastr.

## Technical Context

**Language/Version**: PHP 8.3+ / Laravel 12+ (conforme `constitution.md`)

**Primary Dependencies**: `spatie/laravel-permission`, AdminLTE v4, Bootstrap 5, SweetAlert2, Toastr

**Storage**: MySQL/MariaDB (via XAMPP)

**Testing**: PHPUnit / Artisan Test (`C:\xampp\php\php.exe artisan test`)

**Target Platform**: Web application (XAMPP Server / Navegador Web)

**Project Type**: Web Application (Laravel Blade + Service Layer)

**Performance Goals**: Geração e exportação de relatórios de validade e busca por código de patrimônio em < 3s / busca em < 5s.

**Constraints**: PHP 8.3+ strict types, PSR-12, Service Layer Pattern, transações DB para alterações de estoque, confirmação via SweetAlert2 para movimentações de itens vencidos.

**Scale/Scope**: Adição de 2 novas colunas (`expiration_date`, `patrimony_code`) na tabela `materials`, criação do enum `ExpirationStatus`, atualização de `StockService`, `ReportService`, `MaterialController`, `ReportController`, `DashboardController` e Blade Views relacionadas.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Princípio I (Camada de Serviços & POO Estrita)**: PASS. A lógica de alerta de validade e relatórios patrimoniais é encapsulada em `StockService` e `ReportService`.
- **Princípio II (Rigor de Tipagem & Enums PHP 8.3+)**: PASS. Uso de `declare(strict_types=1);`, tipagem forte em todos os parâmetros/retornos e Enum nativo `ExpirationStatus`.
- **Princípio III (Integridade Transacional & Rastreabilidade)**: PASS. Integridade referencial mantida, `patrimony_code` com chave única, movimentações de produtos mantendo rastro completo.
- **Princípio IV (Gestão Especializada)**: PASS. Validade e Patrimônio integrados às regras de negócio com verificações dedicadas.
- **Princípio V (Interface do Usuário Integrada & Feedback)**: PASS. AdminLTE v4 + Bootstrap 5 badges, SweetAlert2 para saídas de produtos vencidos, Toastr para alertas.

## Project Structure

### Documentation (this feature)

```text
specs/004-validade-patrimonio-produtos/
├── plan.md              # Este plano de implementação
├── research.md          # Fase 0: Pesquisa e decisões de arquitetura
├── data-model.md        # Fase 1: Atualização do modelo de dados e enums
├── quickstart.md        # Fase 1: Guia rápido de validação end-to-end
├── contracts/           
│   └── http-contracts.md # Fase 1: Contratos de controllers, rotas e views
└── tasks.md             # Fase 2: Gerado por /speckit-tasks
```

### Source Code (repository root)

```text
app/
├── Enums/
│   └── ExpirationStatus.php           # [NEW] Enum nativo PHP 8.3 para status de validade
├── Http/
│   ├── Controllers/
│   │   ├── MaterialController.php     # [MODIFY] Suporte a campos e filtros de validade/patrimônio
│   │   ├── ReportController.php       # [MODIFY] Relatórios de validade e bens patrimoniados
│   │   └── DashboardController.php    # [MODIFY] Métricas de produtos vencidos/a vencer
│   └── Requests/
│       └── MaterialRequest.php        # [MODIFY] Validação de expiration_date e patrimony_code
├── Models/
│   └── Material.php                   # [MODIFY] Atributos, casts, scopes e helpers de validade/patrimônio
└── Services/
    ├── StockService.php               # [MODIFY] Regras de alerta de vencimento em baixas/movimentações
    └── ReportService.php              # [MODIFY] Consultas agregadas de relatórios de validade e patrimônio

database/
└── migrations/
    └── 2026_08_14_000001_add_expiration_and_patrimony_to_materials_table.php # [NEW] Migração DB

resources/views/
├── dashboard.blade.php                # [MODIFY] Cards de produtos vencidos/a vencer
├── materials/
│   ├── index.blade.php                # [MODIFY] Badges, coluna de patrimônio e filtros de validade
│   └── form.blade.php                 # [MODIFY] Campos de data de validade e código de patrimônio
└── reports/
    ├── index.blade.php                # [MODIFY] Opções de filtro por validade e patrimônio
    └── pdf/                           # [MODIFY] Templates de impressão/PDF de relatórios
        └── expiration.blade.php
```

**Structure Decision**: Aplicação web monolítica Laravel de projeto único seguindo o padrão Controller -> Service -> Model + Blade Views.

## Complexity Tracking

*Nenhuma violação constitucional registrada.*
