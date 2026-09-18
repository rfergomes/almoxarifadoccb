# Implementation Plan: Inclusão de Campo de Observação em Materiais

**Branch**: `017-campo-observacao-material` | **Date**: 2026-09-18 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/017-campo-observacao-material/spec.md`

## Summary

Implementar a inclusão de um campo de texto livre opcional denominado "Observações" para o catálogo de materiais. A funcionalidade abrangerá a criação da coluna `notes` na tabela `materials`, atualização do Model Eloquent `Material`, validação via `StoreMaterialRequest` e `MaterialController::update`, adição do campo em forma de `textarea` nos formulários de criação, edição e criação rápida (durante entradas de estoque), além de renderização elegante com tooltip informativo na tabela de materiais e preservação de quebras de linha.

## Technical Context

**Language/Version**: PHP 8.3+ com `declare(strict_types=1);`  
**Primary Dependencies**: Laravel 12+, Bootstrap 5, AdminLTE v4, Blade Templates  
**Storage**: MySQL / MariaDB (tabela `materials` via migração incremental Laravel)  
**Testing**: PHPUnit / Pest (`php artisan test`) com testes de Feature dedicados  
**Target Platform**: Servidor Web Apache/PHP (XAMPP Windows local / Linux produção)  
**Project Type**: Aplicação Web Monolítica (Blade + AdminLTE + Controllers/Services Laravel)  
**Performance Goals**: Nenhuma degradação perceptível no tempo de renderização da listagem de materiais ou resposta de submissão de formulário (< 100ms)  
**Constraints**: Validação de texto até 2.000 caracteres, campo estritamente opcional, integridade referencial mantida, responsividade completa em mobile  
**Scale/Scope**: Módulo de Materiais do Almoxarifado Central (cadastro, edição, exibição em tabela e modal rápido de entradas)  

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Princípio I (Service Layer & POO)**: PASS. O fluxo respeita a separação de responsabilidades. Validações isoladas em FormRequests; operações em `MaterialController` e `QuickRegistrationController`.
- **Princípio II (Rigor de Tipagem PHP 8.3+)**: PASS. `declare(strict_types=1);` em todas as classes, tipagem estrita de retornos e argumentos.
- **Princípio III (Integridade Transacional)**: PASS. O campo de observação não impacta saldos de estoque físico, preservando a integridade das operações de movimentação.
- **Princípio V (Interface AdminLTE & Feedback)**: PASS. Formulários construídos com componentes Bootstrap 5, Tooltips nativos para exibição limpa de observações na tabela e notificações via Toastr/SweetAlert2.
- **Qualidade & Idioma (pt-BR)**: PASS. Todos os textos, validações, comentários e documentações redigidos estritamente em Português do Brasil.

*Veredito*: Todos os portões constitucionais foram atendidos sem violações.

## Project Structure

### Documentation (this feature)

```text
specs/017-campo-observacao-material/
├── plan.md              # Este plano de implementação (/speckit-plan)
├── research.md          # Pesquisa técnica e decisões de arquitetura (Fase 0)
├── data-model.md        # Modelo de dados e modificações de esquema (Fase 1)
├── quickstart.md        # Guia de validação ponta a ponta e testes (Fase 1)
├── contracts/           # Contratos de interface e formulários (Fase 1)
│   └── material-notes-contract.md
├── checklists/          # Checklists de qualidade
│   └── requirements.md
└── tasks.md             # Tarefas de implementação (/speckit-tasks)
```

### Source Code (repository root)

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── MaterialController.php              # Atualizar update() para validar/salvar notes
│   │   └── QuickRegistrationController.php     # Retornar notes no JSON se aplicável
│   └── Requests/
│       └── StoreMaterialRequest.php            # Regra 'notes' => ['nullable', 'string', 'max:2000']
├── Models/
│   └── Material.php                            # Adicionar 'notes' no $fillable e helper hasNotes()
database/
└── migrations/
    └── 2026_09_18_000001_add_notes_to_materials_table.php # Migration para criar coluna 'notes'
resources/
└── views/
    ├── materials/
    │   └── index.blade.php                     # Adicionar campo nos modais create/edit e tooltip na tabela
    └── partials/
        └── modal_quick_material.blade.php      # Adicionar campo no cadastro rápido de materiais
tests/
└── Feature/
    └── MaterialObservationTest.php             # Testes de integração cobrindo criação, edição e exibição
```

**Structure Decision**: Aplicação monolítica Laravel com separação padrão MVC + FormRequests e Blade Views com AdminLTE v4.

## Complexity Tracking

> Nenhuma violação aos princípios constitucionais. Nenhuma exceção necessária.
