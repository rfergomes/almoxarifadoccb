# Implementation Plan: Perfis Únicos, Correção de Permissões e Exclusão Segura com Integridade Relacional

**Branch**: `010-correcao-perfis-permissoes` | **Date**: 2026-09-04 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `/specs/010-correcao-perfis-permissoes/spec.md`

---

## Summary

Esta funcionalidade estabelece a política estrita de perfil único (1:1) por usuário no Spatie Permission, impedindo o acúmulo de múltiplos papéis no cadastro e na edição, alinhando a barra de navegação (`navbar`) com a tabela de usuários (`users.index`), e implementando rotinas de exclusão segura exclusivas para Administradores nos módulos de Usuários, Materiais e Movimentações com checagem rigorosa de integridade relacional e estorno atômico de estoque via `StockService`.

---

## Technical Context

**Language/Version**: PHP 8.3+ (PSR-12, `declare(strict_types=1);`), Blade, HTML5, Bootstrap 5, SweetAlert2  
**Primary Dependencies**: Laravel 12+, Spatie Laravel Permission, AdminLTE v4 (`admin-lte@4.0.0`)  
**Storage**: MySQL / MariaDB / SQLite  
**Testing**: PHPUnit / Pest e testes manuais de integridade relacional  
**Target Platform**: Navegador Web (Desktop e Mobile)  
**Project Type**: Aplicação Web Laravel  
**Performance Goals**: Exclusões e sincronizações transacionais executadas em < 150ms  
**Constraints**: Respeitar a integridade de dados e a Constituição do Almoxarifado CCB (Service Layer Pattern, DB Transactions)  
**Scale/Scope**: Ajuste em controllers, models, views e serviços (`UserController`, `MaterialController`, `MovementController`, `StockService`, `User`, views correspondentes)  

---

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **I. Camada de Serviços & POO Estrita**: O estorno e a exclusão de movimentações de estoque são delegados e centralizados em `StockService::deleteMovement(...)` com `DB::transaction`.
- **II. Rigor de Tipagem & Padrões PHP 8.3+**: Strict types em todos os métodos, validações em FormRequests / Controllers.
- **III. Integridade Transacional e Rastreabilidade**: Nenhuma exclusão de usuário ou material que possua histórico de movimentações é permitida.
- **IV. Gestão Especializada de EPIs e Empréstimos**: Respeito às regras de devolução e estornos de empréstimos.
- **V. Interface AdminLTE v4 e Feedback**: Confirmações com SweetAlert2 antes de executar qualquer exclusão.

**Gate Status**: ✅ Aprovado (todos os princípios respeitados, sem violações).

---

## Project Structure

### Documentation (this feature)

```text
specs/010-correcao-perfis-permissoes/
├── spec.md                     # Feature specification
├── plan.md                     # This implementation plan
├── research.md                 # Technical research & decisions (Phase 0)
├── data-model.md               # Data model & validation flows (Phase 1)
├── quickstart.md               # Manual validation scenarios (Phase 1)
├── contracts/
│   └── deletion-and-roles-contracts.md # Endpoints & contracts (Phase 1)
└── checklists/
    └── requirements.md         # Requirements checklist
```

### Source Code (repository root)

```text
app/
├── Models/
│   └── User.php                  # [MODIFY] Accessors para primary_role e primary_role_badge
├── Services/
│   └── StockService.php          # [MODIFY] Implementar deleteMovement() com estorno de estoque transacional
└── Http/
    └── Controllers/
        ├── UserController.php     # [MODIFY] syncRoles no store, sanitização e método destroy()
        ├── MaterialController.php # [MODIFY] método destroy() com checagem de movement_items e current_stock
        └── MovementController.php # [MODIFY] método destroy() restrito a admin via StockService

routes/
└── web.php                       # [MODIFY] Adicionar rotas DELETE para users, materials e movements

resources/
└── views/
    ├── partials/
    │   └── navbar.blade.php      # [MODIFY] Exibição confiável do perfil único
    ├── users/
    │   └── index.blade.php       # [MODIFY] Botão excluir usuário + modal/swal + badge único
    ├── materials/
    │   └── index.blade.php       # [MODIFY] Botão excluir material com verificação de histórico
    └── movements/
        └── show.blade.php        # [MODIFY] Botão de exclusão/estorno de movimentação (apenas admin)
```

---

## Complexity Tracking

*Sem violações aos princípios constitucionais. Lógica transacional devidamente encapsulada na camada de serviços.*
