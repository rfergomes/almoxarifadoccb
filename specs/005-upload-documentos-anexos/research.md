# Phase 0 Research: Upload de Arquivos e Anexos em Entradas e Avarias

## Executive Summary
Esta pesquisa define a arquitetura técnica para suporte a uploads de documentos comprovatórios (Notas Fiscais, Cartas de Doação, Recibos) e fotos/laudos de avarias no sistema Almoxarifado CCB.

---

## Technical Decisions & Rationale

### 1. Attachment Architecture (Polymorphic Model)
- **Decision**: Criar uma tabela dedicada `attachments` com relacionamento polimórfico Eloquent (`attachable_type`, `attachable_id`).
- **Rationale**:
  - Permite vincular anexos a múltiplos modelos sem poluir as tabelas existentes (ex.: `EntryDocument`, `Movement`, `InventoryItem`, etc.).
  - Facilita a expansão futura para anexar arquivos em outras entidades sem migrações adicionais.
  - Centraliza a lógica de upload, validação, limpeza e download em um serviço dedicado (`AttachmentService`).

### 2. Storage Strategy & Security
- **Decision**: Armazenar os arquivos no disco `public` do Laravel (`storage/app/public/attachments/{type}/{year}/{month}/`).
- **Rules**:
  - **Nomenclatura**: Nomes de arquivo únicos gerados com hash/UUID (`Str::uuid()`) para evitar sobreposição e caracteres especiais em SO.
  - **Metadados**: Salvar o nome original (`original_name`), tipo MIME (`mime_type`), tamanho (`file_size`) e o usuário que fez o upload (`uploaded_by`).
  - **Validação de Formatos**: Permitir estritamente `pdf`, `jpg`, `jpeg`, `png`, `webp`. Rejeitar arquivos executáveis ou scripts.
  - **Tamanho Máximo**: 10 MB (`10240` KB em regras de validação do Laravel).

### 3. Service Layer Integration
- **Decision**: Criar `App\Services\AttachmentService` para encapsular a lógica de upload, exclusão física no disco e vincular aos modelos.
- **Methods**:
  - `uploadAttachment(UploadedFile $file, Model $attachable, int $userId): Attachment`
  - `deleteAttachment(Attachment $attachment): bool`
  - `replaceAttachment(UploadedFile $file, Model $attachable, int $userId): Attachment`

### 4. UI & Viewing Components
- **Decision**:
  - **Visualização de Imagens**: Modal SweetAlert2 ou Bootstrap 5 Modal para exibição inline da imagem (sem necessidade de sair da página).
  - **Visualização de PDFs**: Abertura em nova aba do navegador (`target="_blank"`).
  - **Indicador de Anexo**: Badge/Ícone de clipe (`bi bi-paperclip`) visível nas tabelas de entradas, movimentações e relatórios.

---

## Constitution Compliance Verification
- **Camada de Serviços (Service Layer)**: Regras de upload e armazenamento encapsuladas em `AttachmentService`.
- **Rigor de Tipagem (PHP 8.3+)**: `declare(strict_types=1);`, tipagem forte em todos os métodos do serviço e relacionamentos Eloquent.
- **Interface & Feedback**: AdminLTE v4 + Bootstrap 5, preview com modal, mensagens Toastr para sucesso/erro.
