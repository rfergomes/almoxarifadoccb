# HTTP & UI Contracts: Upload de Arquivos e Anexos em Entradas e Avarias

## Web Controller Endpoints & File Download Contracts

### 1. File Upload in Entry Registration (`EntryController`)

#### `POST /entries`
- **Request Type**: `multipart/form-data`
- **Form Data Payload**:
  - `document_number`: string (required)
  - `document_type`: string (required, Enum `DocumentType`)
  - `supplier_or_donor`: string (required)
  - `issued_at`: date (required)
  - `document_file`: file (nullable, max:10240, mimes:pdf,jpg,jpeg,png,webp)
  - `items`: array
- **Response**:
  - Redirect para `entries.index` com flash toastr `success` e anexo armazenado.

---

### 2. File Upload in Adjustments / Movement Registration (`MovementController` / `InventoryController`)

#### `POST /movements` / `POST /materials/{material}/adjust-stock`
- **Request Type**: `multipart/form-data`
- **Form Data Payload**:
  - `attachment_file`: file (nullable, max:10240, mimes:pdf,jpg,jpeg,png,webp)
  - `justification` / `notes`: string
- **Response**:
  - Anexo associado ao registro de ajuste de estoque / movimentação.

---

### 3. Attachment Download & Preview Endpoints (`AttachmentController`)

#### `GET /attachments/{attachment}/download`
- **Response**: Binary File Download (Stream de arquivo do disco `public`).

#### `DELETE /attachments/{attachment}`
- **Permissions**: `manage-materials` ou `manage-users` (Admin)
- **Response**: JSON `{ "success": true, "message": "Anexo removido com sucesso." }`
