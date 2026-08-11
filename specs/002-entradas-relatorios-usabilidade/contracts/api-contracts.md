# API & Web Interface Contracts: Entradas, Modais AJAX e Relatórios CCB

## 1. Rotas Web & API Endpoints

| Método | URI | Controller | Middleware / Permissão | Descrição |
|--------|-----|------------|------------------------|-----------|
| `GET` | `/entries` | `EntryController@index` | `auth`, `can:view-movements` | Listagem de entradas registradas |
| `GET` | `/entries/create` | `EntryController@create` | `auth`, `can:create-movements` | Formulário de lançamento de entrada por NF/Doação |
| `POST` | `/entries` | `EntryController@store` | `auth`, `can:create-movements` | Processa e incrementa o estoque via `EntryService` |
| `POST` | `/api/quick-beneficiary` | `QuickRegistrationController@beneficiary` | `auth`, `can:manage-beneficiaries` | Salva novo beneficiário via AJAX para modal |
| `POST` | `/api/quick-destination` | `QuickRegistrationController@destination` | `auth`, `can:manage-destinations` | Salva novo destino via AJAX para modal |
| `GET` | `/reports` | `ReportController@index` | `auth`, `can:view-dashboard` | Central de Relatórios Gerenciais |
| `GET` | `/reports/export/pdf` | `ReportController@exportPdf` | `auth`, `can:view-dashboard` | Exporta relatório filtrado em PDF formatado com logo CCB |
| `GET` | `/reports/export/excel` | `ReportController@exportExcel` | `auth`, `can:view-dashboard` | Exporta relatório filtrado em planilha Excel (XLSX/CSV) |

---

## 2. Payload de Entrada de Estoque (`POST /entries`)

```php
[
    'document_type' => ['required', 'string', Rule::enum(DocumentType::class)],
    'document_number' => ['required', 'string', 'max:50'],
    'supplier_or_donor' => ['required', 'string', 'max:150'],
    'total_amount' => ['nullable', 'numeric', 'min:0'],
    'issued_at' => ['nullable', 'date'],
    'notes' => ['nullable', 'string', 'max:1000'],
    'items' => ['required', 'array', 'min:1'],
    'items.*.material_id' => ['required', 'exists:materials,id'],
    'items.*.quantity' => ['required', 'integer', 'min:1'],
    'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
]
```

---

## 3. Formato dos PDFs de Relatórios

- **Cabeçalho Institucional:**
  - Imagem do Logotipo: `/public/images/CCB_Logo_fundo_claro.png` (alinhado à esquerda)
  - Título: **CONGREGAÇÃO CRISTÃ NO BRASIL - ALMOXARIFADO CENTRAL**
  - Subtítulo: Nome do Relatório + Filtros Aplicados + Data/Hora de Emissão.
- **Rodapé:** Número da Página (`Página X de Y`) e termo de autenticidade.
