# Implementation Plan: Correção de Exclusão de Material, Notificações e SKU Opcional com Sequencial

**Branch**: `011-correcao-exclusao-material` | **Date**: 2026-09-08 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/011-correcao-exclusao-material/spec.md`

## Summary

Esta funcionalidade resolve as causas-raiz dos erros observados na interface de materiais e atende à nova solicitação de negócio do operador:
1. **Correção de Dependência de Notificações**: Corrige a inclusão do script Toastr em `resources/views/layouts/app.blade.php`, que estava importando um arquivo CSS em uma tag `<script>`, quebrando `window.toastr` e impedindo a execução dos diálogos SweetAlert2 de confirmação de exclusão.
2. **Resiliência do Service Worker (`sw.js`)**: Restringe a interceptação e cache de requisições a protocolos HTTP/HTTPS suportados, ignorando esquemas de extensões (`chrome-extension://`) e respostas não tratadas que geravam exceções no console.
3. **Código SKU Opcional com Sequencial Automático (`GEN-###`)**: Atualiza `StoreMaterialRequest`, `Material` model e as views do modal de cadastro (normal e rápido) para tornar o campo SKU opcional. Quando não informado, o sistema gera automaticamente o próximo sequencial único no formato `GEN-001`, `GEN-002`, `GEN-003`, etc.
4. **Garantia de Exclusão Segura**: Preserva e valida a integridade histórica de materiais que possuam movimentações ou saldo físico, exibindo feedback claro ao usuário.

## Technical Context

**Language/Version**: PHP 8.3+ (strict types habilitado) / JavaScript ES6  
**Primary Dependencies**: Laravel 12, Bootstrap 5, AdminLTE 4, SweetAlert2 v11, Toastr v2.1.4  
**Storage**: MariaDB / MySQL (tabela `materials`, coluna `code_sku`)  
**Testing**: PHPUnit / Pest Feature Tests (`tests/Feature/MaterialSkuAndDeletionFixTest.php`)  
**Target Platform**: Navegadores Desktop & Mobile modernos com suporte a PWA (Chrome, Edge, Firefox, Safari)  
**Project Type**: Aplicação Web Laravel / PWA  
**Performance Goals**: Confirmação visual em < 500ms; geração sequencial de SKU em tempo de persistência com bloqueio atômico  
**Constraints**: Não quebrar registros de materiais existentes; manter integridade referencial com movimentações e inventários  
**Scale/Scope**: Catálogo com centenas/milhares de materiais cadastrados  

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- [x] **Princípio I (Camada de Serviços & POO Estrita)**: Operações e mutações de dados mantêm consistência; regras de geração sequencial isoladas no Model/Service com encapsulamento limpo.
- [x] **Princípio II (Rigor de Tipagem & Padrões PHP 8.3+ / Laravel 12)**: `declare(strict_types=1);` em todas as classes, validação isolada em `StoreMaterialRequest`.
- [x] **Princípio III (Integridade Transacional e Rastreabilidade)**: Bloqueio estrito de exclusão para materiais com estoque > 0 ou histórico de movimentação/inventário preservado.
- [x] **Princípio IV (Gestão de EPIs e Empréstimos)**: Não afeta campos específicos de EPIs e ferramentas, que continuam funcionando harmoniosamente.
- [x] **Princípio V (Interface do Usuário Integrada e Experiência de Feedback)**: Notificações com Toastr e confirmações com SweetAlert2 (`confirmAction`) plenamente restauradas e padronizadas.

## Project Structure

### Documentation (this feature)

```text
specs/011-correcao-exclusao-material/
├── spec.md              # Especificação de requisitos e histórias de usuário
├── plan.md              # Este plano de implementação
├── research.md          # Diagnóstico técnico e decisões arquiteturais (Fase 0)
├── data-model.md        # Modelagem de dados e regras de negócio do SKU (Fase 1)
├── contracts/           # Contratos de rotas e interface (Fase 1)
│   └── material-api.md
├── quickstart.md        # Roteiro de validação funcional e manual (Fase 1)
├── checklists/
│   └── requirements.md  # Checklist de qualidade de especificação
└── tasks.md             # Tarefas de implementação (gerado por /speckit-tasks)
```

### Source Code (repository root)

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── MaterialController.php          # Ações store, update e destroy
│   │   └── QuickRegistrationController.php # Cadastro rápido de material via AJAX
│   └── Requests/
│       └── StoreMaterialRequest.php        # code_sku nullable com regra unique
└── Models/
    └── Material.php                        # generateNextSku() e hook creating em booted()

public/
└── sw.js                                   # Filtro de protocolos e correção de retorno do catch

resources/views/
├── layouts/
│   └── app.blade.php                       # Correção do script toastr.min.js
├── materials/
│   └── index.blade.php                     # Label/placeholder de SKU opcional no modal
└── partials/
    └── modal_quick_material.blade.php      # Label/placeholder de SKU opcional no modal rápido

tests/Feature/
└── MaterialSkuAndDeletionFixTest.php       # Cobertura automatizada de SKU sequencial e exclusão
```

## Phase 0: Outline & Research
- Concluído em [research.md](research.md).
- Resolução dos erros de carregamento do Toastr (`toastr.min.css` em tag script corrigido para `toastr.min.js`).
- Resolução dos erros do Service Worker ao interceptar `chrome-extension:` ou falhas em serviços de telemetria externa.
- Estratégia de geração sequencial automática `GEN-###` via regex `^GEN-(\d+)$`.

## Phase 1: Design & Contracts
- Concluído em [data-model.md](data-model.md), [contracts/material-api.md](contracts/material-api.md) e [quickstart.md](quickstart.md).
- Modelagem de dados e transições validadas contra a Constituição do Almoxarifado.

## Verification Plan

### Testes Automatizados (PHPUnit / Pest)
Executar os testes de funcionalidade:
```powershell
C:\xampp\php\php.exe artisan test tests/Feature/MaterialSkuAndDeletionFixTest.php
```
Cenários cobertos:
1. `test_creates_material_with_automatic_sequential_sku_when_left_blank`: Valida criação sem SKU gerando `GEN-001`.
2. `test_increments_sequential_sku_based_on_highest_existing_gen_code`: Valida incremento a partir do maior número existente (ex: `GEN-002`, `GEN-010`).
3. `test_preserves_custom_sku_when_provided`: Valida que SKU customizado digitado pelo operador é mantido.
4. `test_admin_can_safely_delete_eligible_material`: Valida exclusão permitida para estoque 0 e sem movimentações.
5. `test_blocks_deletion_of_material_with_movements_or_stock`: Valida bloqueio de exclusão com mensagem de feedback.
6. `test_layout_includes_toastr_javascript_correctly`: Valida que `toastr.min.js` está presente na view.

### Verificação Manual
- Seguir os passos detalhados no [quickstart.md](quickstart.md) no navegador.
