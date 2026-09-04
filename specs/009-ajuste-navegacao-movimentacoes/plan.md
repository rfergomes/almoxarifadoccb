# Implementation Plan: Correção de Navegação e Contexto entre Entradas e Saídas

**Branch**: `009-ajuste-navegacao-movimentacoes` | **Date**: 2026-09-04 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `/specs/009-ajuste-navegacao-movimentacoes/spec.md`

---

## Summary

Esta funcionalidade implementa a separação estrita entre os módulos de Entradas e Saídas (Opção A). Ajusta a consulta de `MovementController@index` para listar exclusivamente saídas para consumo e empréstimos, corrige o destaque visual do menu lateral no comprovante de movimentação (`movements.show`) para respeitar o tipo de movimentação (`ENTRY` destaca **Entradas**, outros destacam **Saídas**), e torna o botão "← Voltar" contextual para retornar ao histórico correspondente.

---

## Technical Context

**Language/Version**: PHP 8.3+ (PSR-12, `declare(strict_types=1);`), Blade, HTML5, Bootstrap 5  
**Primary Dependencies**: Laravel 12+, AdminLTE v4 (`admin-lte@4.0.0`), Spatie Laravel Permission  
**Storage**: MySQL / MariaDB (XAMPP)  
**Testing**: PHPUnit / Pest e testes manuais de interface / navegação  
**Target Platform**: Navegador Web (Desktop e Mobile)  
**Project Type**: Aplicação Web Laravel  
**Performance Goals**: Tempo de renderização < 200ms nas telas de listagem e comprovante  
**Constraints**: Não quebrar rotas existentes (`movements.show`, `movements.index`, `entries.index`), respeitar as diretrizes da Constituição do Almoxarifado CCB  
**Scale/Scope**: Ajuste em 3 arquivos (`MovementController.php`, `sidebar.blade.php`, `show.blade.php`)  

---

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **I. Camada de Serviços & POO Estrita**: A alteração respeita a separação de responsabilidades. Não há mutações de estoque sendo adicionadas em controllers.
- **II. Rigor de Tipagem & Padrões PHP 8.3+**: Uso de enums nativos (`MovementType::ENTRY`, `MovementType::CONSUMPTION`, `MovementType::LOAN`) em consultas e checagens lógicas.
- **III. Integridade Transacional e Rastreabilidade**: Preservada integralmente.
- **IV. Gestão de EPIs e Empréstimos**: Nenhuma alteração nas regras de devolução ou controle de EPIs.
- **V. Interface AdminLTE v4 e Feedback**: Melhoria direta de consistência visual no layout do AdminLTE v4.

**Gate Status**: ✅ Aprovado (todos os princípios respeitados, sem violações).

---

## Project Structure

### Documentation (this feature)

```text
specs/009-ajuste-navegacao-movimentacoes/
├── spec.md                     # Feature specification
├── plan.md                     # This plan
├── research.md                 # Technical research & decisions (Phase 0)
├── data-model.md               # Data entities & queries (Phase 1)
├── quickstart.md               # Validation guide (Phase 1)
├── contracts/
│   └── ui-navigation-contract.md # UI & route contracts (Phase 1)
└── checklists/
    └── requirements.md         # Specification quality checklist
```

### Source Code (repository root)

```text
app/
└── Http/
    └── Controllers/
        └── MovementController.php # [MODIFY] Filtrar type in [CONSUMPTION, LOAN] no index()

resources/
└── views/
    ├── partials/
    │   └── sidebar.blade.php      # [MODIFY] Ativação dinâmica contextual de Entradas / Saídas
    └── movements/
        └── show.blade.php         # [MODIFY] Botão "← Voltar" apontando para a rota correta conforme tipo
```

---

## Complexity Tracking

*Nenhuma violação ou complexidade excessiva identificada. Arquitetura 100% alinhada com as convenções do Laravel e do projeto.*
