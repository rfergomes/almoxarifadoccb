# Phase 0 Research: Controle de Validade e Patrimônio de Produtos

## Executive Summary
Esta pesquisa estabelece as definições arquiteturais para a implementação de **Controle de Validade** e **Código de Patrimônio** no sistema Almoxarifado CCB, mantendo estrito alinhamento com a Constituição do Projeto (PHP 8.3+, PSR-12, Service Layer Pattern, AdminLTE v4).

---

## Technical Decisions & Rationale

### 1. Data Model Extension for Expiration and Patrimony
- **Decision**: Adicionar colunas `expiration_date` (`DATE`, nullable) e `patrimony_code` (`VARCHAR(50)`, nullable, unique) na tabela `materials`.
- **Rationale**:
  - A tabela `materials` já centraliza metadados específicos do item (ex.: `ca_number` e `ca_validity` para EPIs).
  - Manter `expiration_date` e `patrimony_code` no modelo `Material` simplifica consultas, ordenação, relatórios e buscas por código de patrimônio sem overhead de joins desnecessários.
- **Alternatives Considered**:
  - *Tabela separada `patrimonies`*: Descartada por trazer complexidade excessiva nesta fase para itens que possuem controle unitário/único de patrimônio no almoxarifado.
  - *Tabela de lotes `material_batches`*: Descartada para manter uniformidade com o padrão simples de movimentação de estoque atual.

### 2. Expiration Status Calculation & Alert Window
- **Decision**: Implementar método no modelo `Material` (`getExpirationStatusAttribute()` ou métodos auxiliares `isExpired()`, `isExpiringSoon()`) e desacoplar consultas agregadas na camada de serviço (`StockService` / `ReportService`).
- **Rules**:
  - `EXPIRED` ("Vencido"): `expiration_date` < `now()->startOfDay()`.
  - `EXPIRING_SOON` ("Próximo de Vencer"): `expiration_date` entre `now()->startOfDay()` e `now()->addDays(30)->endOfDay()`.
  - `VALID` ("Válido"): `expiration_date` > `now()->addDays(30)`.
  - `NO_EXPIRATION` ("Sem Validade"): `expiration_date` é `null`.
- **Alert Window**: Janela padrão definida em 30 dias (customizável ou obtida via `Setting`).

### 3. Business Service Layer Integration
- **Decision**: Centralizar toda a lógica de filtragem, alertas do dashboard e validação de saídas de produtos vencidos em `StockService` e `ReportService`.
- **Rules**:
  - `StockService`: Método para verificar produtos vencidos e alertar ao realizar baixas ou saídas de materiais.
  - `ReportService`: Novos métodos de filtragem `getExpirationReport(string $filterType)` e `getPatrimonyReport()`.

### 4. UI & Visual Indicators (AdminLTE v4 / Bootstrap 5 / SweetAlert2 / Toastr)
- **Decision**:
  - **Badges de Status**:
    - `bg-danger`: Item Vencido.
    - `bg-warning text-dark`: Item A Vencer (nos próximos 30 dias).
    - `bg-secondary`: Sem Validade.
    - `bg-info`: Ícone e tag de Patrimônio.
  - **Dashboard Cards**: Cards informativos com contagem de produtos vencidos e produtos a vencer nos próximos 30 dias.
  - **SweetAlert2**: Alerta de confirmação caso o operador tente dar saída em um produto vencido.

---

## Constitution Compliance Verification
- **Camada de Serviços (Service Layer)**: Lógica isolada em `StockService` e `ReportService`.
- **Rigor de Tipagem (PHP 8.3+)**: `declare(strict_types=1);`, métodos com tipagem estrita de parâmetro e retorno.
- **Tipagem Enums**: Criação de `ExpirationStatus`Enum se aplicável.
- **Interface & Feedback**: AdminLTE v4 + Bootstrap 5 badges, SweetAlert2 e Toastr.
