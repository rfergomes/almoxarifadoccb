# Implementation Plan: Inclusão e Visualização de Imagem no Cadastro de Materiais

**Branch**: `014-imagem-produto-material` | **Date**: 2026-09-08 | **Spec**: [spec.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/014-imagem-produto-material/spec.md)

**Input**: Feature specification from `specs/014-imagem-produto-material/spec.md`

## Summary

Implementação da capacidade de upload, gestão, miniatura e visualização ampliada de fotos/imagens para os produtos e materiais do almoxarifado. O plano contempla a inclusão da coluna `image_path` na tabela `materials`, a criação do serviço `MaterialImageService` para isolar as operações de arquivos no disco `public`, validações estritas aceitando exclusivamente arquivos de imagem (JPEG, PNG, WEBP, GIF até 5MB), além da adição da coluna de miniatura na tabela do catálogo e de modal com lightbox para visualização em alta resolução.

## Technical Context

**Language/Version**: PHP 8.3+ / Laravel 12+ (conforme `constitution.md`)

**Primary Dependencies**: Bootstrap 5, AdminLTE v4 (`admin-lte@4.0.0`), SweetAlert2, Toastr, `spatie/laravel-permission`

**Storage**: Local Disk `public` via Laravel Storage (`storage/app/public/materials/images/`)

**Testing**: PHPUnit / Artisan Test (`C:\xampp\php\php.exe artisan test`)

**Target Platform**: Web Application (XAMPP Server / Navegadores modernos)

**Project Type**: Web Application Monolítica (Laravel Blade + Service Layer)

**Performance Goals**: Carregamento da página com miniaturas em < 1s; abertura instantânea da imagem ampliada; uploads de até 5MB concluídos em < 1.5s.

**Constraints**: Validação estrita de extensão e MIME type de imagens (`jpeg`, `png`, `jpg`, `webp`, `gif`), nomes com UUID randômico, exclusão física de arquivos antigos ao substituir ou deletar material, preservação do layout responsivo da tabela e dos modais no AdminLTE.

**Scale/Scope**: Migration incremental para `materials`, modelo `Material`, serviço `MaterialImageService`, `StoreMaterialRequest`, `MaterialController`, views `materials/index.blade.php`, e modal de preview.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Princípio I (Camada de Serviços & POO Estrita)**: PASS. O processamento de arquivos físicos (upload, sanitização de nome via UUID, deleção do disco `public` e substituição) é encapsulado no serviço dedicado `MaterialImageService`.
- **Princípio II (Rigor de Tipagem & Padrões PHP 8.3+)**: PASS. Uso de `declare(strict_types=1);`, métodos com tipagem rigorosa de parâmetros/retornos, e validação isolada via `FormRequest`.
- **Princípio III (Integridade Transacional e Rastreabilidade)**: PASS. A imagem é associada diretamente ao registro do material sem interferir nas travas transacionais de saldo de estoque e rastreabilidade de movimentações.
- **Princípio IV (Gestão Especializada)**: PASS. As regras de EPI (CA) e controle de patrimônio permanecem íntegras e coexistentes com a imagem.
- **Princípio V (Interface do Usuário Integrada e Feedback)**: PASS. Componentes AdminLTE v4 + Bootstrap 5 com visual moderno, miniatura com enquadramento proporcional, modal de alta resolução com fechamento via ESC/fundo, e notificações Toastr.

## Project Structure

### Documentation (this feature)

```text
specs/014-imagem-produto-material/
├── plan.md              # Este documento de plano de implementação
├── research.md          # Fase 0: Pesquisa técnica e decisões de arquitetura
├── data-model.md        # Fase 1: Especificação de dados e ciclo de vida de imagens
├── quickstart.md        # Fase 1: Guia prático de validação e testes
├── contracts/           
│   └── http-contracts.md # Fase 1: Contratos HTTP de cadastro, edição e modal
└── tasks.md             # Fase 2: Gerado pelo comando /speckit-tasks
```

### Source Code (repository root)

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── MaterialController.php            # [MODIFY] Injetar MaterialImageService, tratar upload na criação, edição e exclusão
│   └── Requests/
│       ├── StoreMaterialRequest.php          # [MODIFY] Adicionar validação de imagem (mimes, max:5120)
│       └── UpdateMaterialRequest.php         # [NEW/MODIFY] Validação de edição e remoção de imagem
├── Models/
│   └── Material.php                          # [MODIFY] Adicionar image_path em $fillable, accessor imageUrl e helper hasImage()
└── Services/
    └── MaterialImageService.php              # [NEW] Serviço de manipulação de imagens (upload, replace, delete)

database/
└── migrations/
    └── 2026_09_08_000001_add_image_path_to_materials_table.php # [NEW] Coluna image_path (nullable string)

resources/views/
└── materials/
    └── index.blade.php                       # [MODIFY] Coluna com miniatura, formulários multipart, preview dinâmico e modal ampliado
```

**Structure Decision**: Aplicação web monolítica padrão Laravel seguindo Controller -> Service -> Model + Blade Views, em estrito acordo com os padrões do projeto.

## Complexity Tracking

*Nenhuma violação constitucional registrada.*
