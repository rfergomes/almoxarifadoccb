# Implementation Plan: Upload de Arquivos e Anexos em Entradas e Avarias

**Branch**: `005-upload-documentos-anexos` | **Date**: 2026-08-14 | **Spec**: [spec.md](file:///D:/xampp/htdocs/almoxarifadoccb/specs/005-upload-documentos-anexos/spec.md)

**Input**: Feature specification from `/specs/005-upload-documentos-anexos/spec.md`

## Summary

Implementação do sistema polimórfico de armazenamento e gestão de anexos digitais (Notas Fiscais, Cartas de Doação, Recibos e Fotos de Avarias). O plano envolve a criação da tabela `attachments`, do modelo `Attachment`, do serviço `AttachmentService`, além da atualização dos formulários e tabelas de Entradas, Inventário/Avarias, Movimentações e Relatórios com suporte a preview em modal e download de PDFs.

## Technical Context

**Language/Version**: PHP 8.3+ / Laravel 12+ (conforme `constitution.md`)

**Primary Dependencies**: `spatie/laravel-permission`, AdminLTE v4, Bootstrap 5, SweetAlert2, Toastr

**Storage**: Local Disk `public` via Laravel Storage (`storage/app/public/attachments/`)

**Testing**: PHPUnit / Artisan Test (`C:\xampp\php\php.exe artisan test`)

**Target Platform**: Web application (XAMPP Server / Navegador Web)

**Project Type**: Web Application (Laravel Blade + Service Layer)

**Performance Goals**: Upload e download de comprovantes de até 10MB concluídos em < 2s.

**Constraints**: Validação estrita de extensões (`pdf`, `png`, `jpg`, `jpeg`, `webp`), nomes randômicos de arquivo (UUID), deleção física do disco ao remover o registro, permissões RBAC.

**Scale/Scope**: Tabela `attachments`, modelo `Attachment`, `AttachmentService`, `AttachmentController`, atualização de `EntryController`, `InventoryController`, `MaterialController` e views Blade correspondentes.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Princípio I (Camada de Serviços & POO Estrita)**: PASS. O gerenciamento de uploads, armazenamento e exclusão física fica encapsulado em `AttachmentService`.
- **Princípio II (Rigor de Tipagem & Enums PHP 8.3+)**: PASS. Uso de `declare(strict_types=1);`, tipagem estrita nos métodos de upload e relacionamentos Eloquent.
- **Princípio III (Integridade Transacional & Rastreabilidade)**: PASS. Registros de anexo mantidos com rastreio do usuário criador (`uploaded_by`).
- **Princípio IV (Gestão Especializada)**: PASS. Suporte a comprovantes fiscais em entradas e fotos em avarias.
- **Princípio V (Interface do Usuário Integrada & Feedback)**: PASS. AdminLTE v4 + Bootstrap 5, modal de preview de imagens, SweetAlert2 e Toastr.

## Project Structure

### Documentation (this feature)

```text
specs/005-upload-documentos-anexos/
├── plan.md              # Este plano de implementação
├── research.md          # Fase 0: Pesquisa e decisões de arquitetura
├── data-model.md        # Fase 1: Especificação da tabela e modelo Attachment
├── quickstart.md        # Fase 1: Guia rápido de validação end-to-end
├── contracts/           
│   └── http-contracts.md # Fase 1: Contratos de endpoints e downloads
└── tasks.md             # Fase 2: Gerado por /speckit-tasks
```

### Source Code (repository root)

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AttachmentController.php   # [NEW] Controller para download e exclusão de anexos
│   │   ├── EntryController.php        # [MODIFY] Suporte a multipart/form-data e upload de NF
│   │   └── InventoryController.php    # [MODIFY] Suporte a upload de fotos/laudos em avarias
│   └── Requests/
│       ├── StoreEntryRequest.php      # [MODIFY] Validação do campo de arquivo de documento
│       └── StoreAttachmentRequest.php # [NEW] Validação dedicada de anexos
├── Models/
│   ├── Attachment.php                 # [NEW] Modelo polimórfico de anexo
│   ├── EntryDocument.php              # [MODIFY] Relacionamento morphMany/morphOne com Attachment
│   ├── Movement.php                   # [MODIFY] Relacionamento com Attachment
│   └── InventoryItem.php              # [MODIFY] Relacionamento com Attachment
└── Services/
    ├── AttachmentService.php          # [NEW] Serviço de upload, salvamento e remoção do storage
    └── EntryService.php               # [MODIFY] Integração com AttachmentService no salvamento de entradas

database/
└── migrations/
    └── 2026_08_14_000002_create_attachments_table.php # [NEW] Migração para tabela polimórfica

resources/views/
├── entries/
│   ├── create.blade.php               # [MODIFY] Campo input file enctype="multipart/form-data"
│   └── index.blade.php                # [MODIFY] Ícone e link de anexo de nota fiscal
├── inventories/
│   └── show.blade.php                 # [MODIFY] Upload de evidências e pré-visualização de fotos de avarias
├── partials/                          
│   └── modal_attachment_preview.blade.php # [NEW] Modal de preview de imagem/PDF
└── reports/                           
    └── pdf/                           # [MODIFY] Indicação de documento anexado em relatórios
```

**Structure Decision**: Aplicação web monolítica Laravel de projeto único seguindo o padrão Controller -> Service -> Model + Blade Views.

## Complexity Tracking

*Nenhuma violação constitucional registrada.*
