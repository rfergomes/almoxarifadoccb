# Implementation Plan: Correção do Erro ao Salvar Material

**Branch**: `018-fix-erro-salvar-material` | **Date**: 2026-09-18 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/018-fix-erro-salvar-material/spec.md`

## Summary

Corrigir a falha de persistência e de filtragem que resulta em HTTP 500 (Server Error) ao salvar, editar ou pesquisar materiais no Almoxarifado CCB. O problema decorre do desalinhamento estrutural entre a aplicação e a tabela `materials` no banco de dados MySQL `sibemo33_almoxarifadoccb`, na qual a coluna `notes` não foi adicionada. A solução abrange:
1. Disponibilização de comando DDL direto para phpMyAdmin e instruções de migração para adicionar a coluna `notes` na tabela `materials`.
2. Aprimoramento defensivo no `MaterialController` com captura de exceções (`try/catch`), registro detalhado de logs e redirecionamento amigável com preservação dos dados submetidos (`withInput()`), prevenindo telas brancas de erro 500 caso ocorram falhas imprevistas de infraestrutura.
3. Blindagem da consulta de busca e filtragem no método `index()` do `MaterialController`, verificando dinamicamente a presença da coluna `notes` antes de incluí-la na cláusula `orWhere`, eliminando o erro 500 ao pesquisar por qualquer termo (ex: "teste").
4. Validação funcional ponta a ponta garantindo gravação, edição e busca textual com sucesso.

## Technical Context

**Language/Version**: PHP 8.3+ com `declare(strict_types=1);`  
**Primary Dependencies**: Laravel 12+, Bootstrap 5, AdminLTE v4, Blade Templates  
**Storage**: MySQL / MariaDB (banco `sibemo33_almoxarifadoccb` via phpMyAdmin / Artisan) e SQLite para testes locais  
**Testing**: PHPUnit / Pest (`php artisan test`)  
**Target Platform**: Servidor Web Apache/PHP (XAMPP Windows / Hospedagem cPanel)  
**Project Type**: Aplicação Web Monolítica (Blade + Controllers + Services Laravel)  
**Performance Goals**: Tempo de resposta ao salvar material inferior a 2 segundos; eliminação total de erros 500  
**Constraints**: Preservação de dados digitados em caso de falha transitória; compatibilidade com bancos já populados  
**Scale/Scope**: Operação de cadastro e edição de materiais no módulo de almoxarifado  

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Princípio I (Service Layer & POO)**: PASS. O `MaterialController` orquestra a requisição e delega operações; regras de transação e serviços são preservados.
- **Princípio II (Rigor de Tipagem PHP 8.3+)**: PASS. Manutenção de `declare(strict_types=1);`, tipagem estrita de parâmetros e retornos.
- **Princípio III (Integridade Transacional)**: PASS. A persistência de dados de cadastro mantém integridade com chaves estrangeiras e controle de estoque.
- **Princípio V (Interface AdminLTE & Feedback)**: PASS. Em caso de falha capturada, o usuário recebe notificação amigável via Toastr/Session Flash com `withInput()` sem perder dados digitados.
- **Qualidade & Idioma (pt-BR)**: PASS. Mensagens de feedback, logs contextuais e documentação integralmente em Português do Brasil.

*Veredito*: Todos os princípios constitucionais foram respeitados. Nenhum portão foi violado.

## Project Structure

### Documentation (this feature)

```text
specs/018-fix-erro-salvar-material/
├── plan.md              # Este plano de implementação (/speckit-plan)
├── research.md          # Pesquisa técnica e diagnóstico da causa raiz (Fase 0)
├── data-model.md        # Esquema estrutural e script DDL para o banco (Fase 1)
├── quickstart.md        # Guia passo a passo de validação no phpMyAdmin/browser (Fase 1)
├── contracts/           # Contratos de rotas, payloads e respostas (Fase 1)
│   └── fix-salvar-material-contract.md
└── checklists/
    └── requirements.md  # Checklist de qualidade da especificação
```

### Source Code (repository root)

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── MaterialController.php              # Adicionar try/catch resiliente em store() e update()
│   └── Requests/
│       └── StoreMaterialRequest.php            # Validação do formulário de criação de material
database/
└── migrations/
    └── 2026_09_18_000001_add_notes_to_materials_table.php # Migration para adição da coluna notes
tests/
└── Feature/
    └── MaterialObservationTest.php             # Validação de testes automatizados de materiais
```

**Structure Decision**: Monolito Laravel seguindo PSR-12, arquitetura padrão MVC com camada de requisições validadas (`FormRequest`).

## Complexity Tracking

> Nenhuma violação identificada. Nenhuma exceção aos princípios constitucionais necessária.
