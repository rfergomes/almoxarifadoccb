# Implementation Plan: Ordenação Padrão da Tabela pelo Nome do Material

**Branch**: `013-ordenar-materiais-nome` | **Date**: 2026-09-08 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/013-ordenar-materiais-nome/spec.md`

---

## Summary

Modificar a ordenação padrão da listagem no Catálogo de Materiais (`MaterialController@index`), substituindo a ordenação cronológica decrescente (`latest()`) por ordenação alfabética crescente (`orderBy('name', 'asc')`). A alteração garante que operadores encontrem materiais de forma previsível e intuitiva, preservando a paginação e os filtros já existentes.

---

## Technical Context

**Language/Version**: PHP 8.3+ / Laravel 12  
**Primary Dependencies**: Laravel Framework, Eloquent ORM, Blade Templates  
**Storage**: MariaDB / MySQL (XAMPP), SQLite (ambiente de testes)  
**Testing**: PHPUnit / Laravel TestSuite (`artisan test`)  
**Target Platform**: Servidor Web Apache (XAMPP no Windows)  
**Project Type**: Aplicação Web MVC (Laravel)  
**Performance Goals**: Tempo de resposta de listagem < 100ms  
**Constraints**: Preservar compatibilidade total com os filtros existentes (`search`, `category_id`, `expiration`, `has_patrimony`) e manter paginação com `withQueryString()`.  
**Scale/Scope**: Listagem de catálogo de materiais do almoxarifado.

---

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Princípio | Conformidade | Justificativa |
|---|---|---|
| I. Camada de Serviços & POO Estrita | ✅ PASS | Trata-se de uma ordenação de consulta de apresentação no controller de listagem (`index`), sem lógica de negócio de movimentação de estoque ou mutação. |
| II. Rigor de Tipagem & Padrões PHP 8.3+ | ✅ PASS | Controller segue convenções PSR-12 com tipagem e strict types. |
| III. Integridade Transacional | ✅ PASS | Nenhuma mutação ou redução de saldo; operação puramente de leitura. |
| IV. Gestão de EPIs e Empréstimos | ✅ PASS | Materiais do tipo EPI e ferramentas continuam catalogados e exibidos em ordem alfabética. |
| V. Interface do Usuário Integrada | ✅ PASS | Preserva layout AdminLTE v4 com Bootstrap 5 e componentes visuais existentes. |

---

## Project Structure

### Documentation (this feature)

```text
specs/013-ordenar-materiais-nome/
├── spec.md              # Feature Specification
├── checklists/
│   └── requirements.md  # Specification Quality Checklist
├── plan.md              # Implementation Plan
├── research.md          # Phase 0 Research & Findings
├── data-model.md        # Phase 1 Data Model
├── quickstart.md        # Phase 1 Quickstart Validation Guide
├── contracts/
│   └── materials-index.contract.md # Contract for GET /materials
└── tasks.md             # Phase 2 Tasks (via /speckit-tasks)
```

### Source Code (repository root)

```text
app/
├── Http/
│   └── Controllers/
│       └── MaterialController.php   # [MODIFY] Alterar ordenação de query para orderBy('name', 'asc')

tests/
└── Feature/
    └── MaterialSkuAndDeletionFixTest.php # [MODIFY] Adicionar asserção de ordenação alfabética
```

**Structure Decision**: Alteração concisa e pontual no controller de materiais (`MaterialController.php`), acompanhada de teste automatizado de Feature para garantir que a ordenação alfabética A-Z seja respeitada.

---

## Complexity Tracking

*Nenhuma violação constitucional identificada.*
