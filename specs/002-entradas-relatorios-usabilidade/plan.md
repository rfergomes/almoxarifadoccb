# Implementation Plan: Entradas de Estoque, Usabilidade Inline e Relatórios PDF/Excel

**Branch**: `002-entradas-relatorios-usabilidade` | **Date**: 2026-08-11 | **Spec**: [spec.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/002-entradas-relatorios-usabilidade/spec.md)

**Input**: Feature specification from `/specs/002-entradas-relatorios-usabilidade/spec.md`

## Summary

Expandir o Sistema de Almoxarifado CCB para suportar Entradas de Estoque (Notas Fiscais, Doações, Compras Diretas), Cadastro Rápido Inline de Beneficiários e Destinos via modais AJAX nas telas de formulário, Central de Relatórios Gerenciais com exportação para PDF (formatado com logomarca oficial da CCB) e Excel, e aplicação da identidade visual oficial (`/public/images/`).

## Technical Context

**Language/Version**: PHP 8.2+ / 8.3 / 8.4 (XAMPP PHP)  
**Primary Dependencies**: Laravel 12+, `barryvdh/laravel-dompdf` (para relatórios em PDF), `spatie/laravel-permission`, AdminLTE 4, Bootstrap 5, SweetAlert2, Toastr  
**Storage**: SQLite / MySQL  
**Testing**: PHPUnit / Pest  
**Target Platform**: Web Server Intranet (XAMPP)  
**Project Type**: Web Application (Laravel MVC + Blade AdminLTE v4 + Modais AJAX)  
**Performance Goals**: Geração de relatórios PDF/Excel < 3s, resposta de cadastro por modal AJAX < 200ms  
**Constraints**: Transações SQL em `EntryService.php`, validação rigorosa de Form Requests, compatibilidade com logotipos em `/public/images`, código em pt-BR  

## Constitution Check

- **Princípio I (Service Layer & POO Estrita):** PASS - `EntryService.php` isola a criação de documentos fiscais/doações e incremento de estoque em `DB::transaction`.
- **Princípio II (Tipagem & Enums):** PASS - Adição do caso `ENTRY = 'ENTRY'` no Enum `MovementType` e Enums auxiliares de documentos.
- **Princípio III (Integridade & Rastreabilidade):** PASS - Entradas auditadas vinculando documento fiscal/doação, fornecedor/doador e almoxarife.
- **Princípio IV (EPIs & Empréstimos):** PASS - Manutenção de todas as regras existentes com suporte a novos relatórios de atraso.
- **Princípio V (Interface & Usabilidade):** PASS - Logotipos oficiais CCB, modais inline AJAX para usabilidade fluida, exportações PDF/Excel.

## Project Structure

### Documentation (this feature)

```text
specs/002-entradas-relatorios-usabilidade/
├── plan.md              # Este arquivo
├── research.md          # Fase 0: Pesquisa e decisões de pacotes PDF/Excel e modais AJAX
├── data-model.md        # Fase 1: Novas tabelas (entry_documents), alterações em movements e endpoints AJAX
├── quickstart.md        # Fase 1: Guia de instalação e testes dos novos relatórios
└── contracts/
    └── api-contracts.md # Fase 1: Contratos dos endpoints de relatórios e modais AJAX
```

### Source Code (repository root)

```text
app/
├── Enums/
│   ├── DocumentType.php
│   └── MovementType.php (atualizado)
├── Http/
│   ├── Controllers/
│   │   ├── EntryController.php
│   │   ├── ReportController.php
│   │   └── QuickRegistrationController.php (AJAX)
│   └── Requests/
│       ├── StoreEntryRequest.php
│       └── ReportFilterRequest.php
├── Models/
│   ├── EntryDocument.php
│   └── Movement.php (atualizado)
└── Services/
    ├── EntryService.php
    └── ReportService.php

database/
└── migrations/
    └── 2026_08_11_000007_create_entry_documents_table.php

resources/
└── views/
    ├── entries/
    │   ├── create.blade.php
    │   └── index.blade.php
    ├── reports/
    │   ├── index.blade.php
    │   ├── pdf/
    │   │   ├── inventory.blade.php
    │   │   ├── overdue_loans.blade.php
    │   │   └── movements.blade.php
    │   └── template.blade.php (layout base dos PDFs com logo CCB)
    └── partials/
        ├── modal_quick_beneficiary.blade.php
        └── modal_quick_destination.blade.php
```

**Structure Decision**: Monolith Laravel estendido com Services dedicados (`EntryService`, `ReportService`) e rotas AJAX para modais dinâmicos.
