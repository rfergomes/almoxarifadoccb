# HTTP & UI Contracts: Controle de Validade e Patrimônio de Produtos

## Web Controller Endpoints & UI Interaction Contracts

### 1. Materials Management (`MaterialController`)

#### `GET /materials`
- **Query Parameters**:
  - `search`: string (busca por nome, SKU ou `patrimony_code`)
  - `category_id`: int
  - `expiration_status`: `all` | `expired` | `expiring_soon` | `valid`
  - `has_patrimony`: `0` | `1`
- **View Data Payload**:
  - `materials`: LengthAwarePaginator de `Material`
  - `categories`: Collection de `Category`

#### `POST /materials` & `PUT /materials/{material}`
- **Form Data Payload**:
  - `name`: string (required)
  - `code_sku`: string (required)
  - `category_id`: int (required)
  - `unit_measure`: string (required)
  - `current_stock`: int (required)
  - `minimum_stock`: int (required)
  - `expiration_date`: date (nullable, YYYY-MM-DD)
  - `patrimony_code`: string (nullable, max:50, unique)
- **Response**:
  - Redirect para `materials.index` com flash toastr `success` ou `error`.

---

### 2. Reports (`ReportController` / `ReportService`)

#### `GET /reports`
- **Query Parameters**:
  - `type`: `stock` | `movements` | `expiration` | `patrimony`
  - `expiration_filter`: `expired` | `expiring_30` | `expiring_60` | `expiring_90` | `all`
  - `start_date`: date (nullable)
  - `end_date`: date (nullable)
  - `export`: `pdf` | `excel` (opcional)
- **View Data Payload**:
  - `reportData`: Collection ou Paginator contendo os itens formatados com data de validade, contagem de dias restantes para vencimento, status e número de patrimônio.

---

### 3. Dashboard Metrics (`DashboardController` / `StockService`)

#### `GET /dashboard`
- **Summary Cards Payload**:
  - `expired_products_count`: int (quantidade de produtos com validade expirada)
  - `expiring_soon_products_count`: int (quantidade de produtos a vencer nos próximos 30 dias)
  - `patrimony_items_count`: int (quantidade de produtos/ferramentas com registro de patrimônio)
