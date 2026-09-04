# Implementation Plan: Correção da Escala e Layout de Impressão Direta do Comprovante de Movimentação

**Branch**: `007-fix-receipt-print-scale` | **Date**: 2026-09-04 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/007-fix-receipt-print-scale/spec.md`

## Summary

Corrigir a renderização de impressão direta (`window.print()`) no comprovante de movimentação (`/movements/{id}`), eliminando o corte de conteúdo e a necessidade de redução manual para 40% de escala no Chrome. A solução neutraliza as restrições de CSS Grid, `max-width: 100vw` e `overflow: auto` do layout-fixed do AdminLTE 4 Beta 2 dentro da diretiva `@media print`, isolando o `.printable-receipt` para ocupar 100% da largura útil da folha A4 com proporções e colunas intactas.

## Technical Context

**Language/Version**: PHP 8.3+ (Laravel 12) & CSS3 Moderno
**Primary Dependencies**: Bootstrap 5.3.3, AdminLTE v4.0.0-beta2, Vite
**Storage**: N/A (não há persistência de novos dados)
**Testing**: PHPUnit / Laravel Feature Tests e Validação Visual via Chromium Print Preview
**Target Platform**: Navegadores Web Desktop (Chromium/Edge/Firefox) e Impressoras Físicas A4
**Project Type**: Aplicação Web (Blade + CSS)
**Performance Goals**: Renderização instantânea do diálogo de impressão (< 500ms)
**Constraints**: Não quebrar o layout interativo de tela do AdminLTE 4 nem alterar a geração de PDF via DomPDF (`/movements/{id}/pdf`)
**Scale/Scope**: Módulo de Movimentações (`resources/views/movements/show.blade.php` e `resources/css/responsive-custom.css`)

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- [x] **Princípio I (Camada de Serviços & POO Estrita)**: Nenhuma lógica de negócio de estoque é violada; a alteração é puramente de apresentação (view e folhas de estilo).
- [x] **Princípio II (Tipagem & PSR-12)**: Sem alterações em backend além de manter contratos Blade.
- [x] **Princípio III (Integridade Transacional)**: Sem mutações em dados de estoque.
- [x] **Princípio IV (Gestão de EPIs e Empréstimos)**: Os campos de CA, data prevista de retorno e devoluções permanecem perfeitamente visíveis no comprovante impresso.
- [x] **Princípio V (Interface do Usuário & AdminLTE v4)**: A interface interativa permanece inalterada; apenas as regras de `@media print` são corrigidas para contornar limitações do template em impressão.

## Project Structure

### Documentation (this feature)

```text
specs/007-fix-receipt-print-scale/
├── plan.md              # Este plano de implementação
├── research.md          # Análise técnica do Chromium e AdminLTE 4
├── data-model.md        # Entidades apresentadas no comprovante
├── quickstart.md        # Guia de validação da impressão
├── contracts/
│   └── print-receipt.md # Contrato de estilo e comportamento da interface de impressão
└── checklists/
    └── requirements.md  # Checklist de qualidade da especificação
```

### Source Code (repository root)

```text
resources/
├── css/
│   └── responsive-custom.css        # Reset global de alta especificidade para @media print
└── views/
    ├── layouts/
    │   └── app.blade.php            # Ajuste de regras @media print no layout principal
    └── movements/
        └── show.blade.php           # Estilos dedicados e estruturação do #printable-receipt
```

## Proposed Changes

### 1. `resources/views/movements/show.blade.php`
- Substituir o bloco `@push('styles')` atual por um reset cirúrgico de impressão:
  - Definir `@page { size: A4 portrait; margin: 8mm 10mm; }`.
  - Isolar o container `.printable-receipt` para `width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important;`.
  - Sobrescrever os pais (`html`, `body`, `.app-wrapper`, `.app-main`, `.app-content`, `.container-fluid`, `col-lg-10`) com `display: block !important; width: 100% !important; max-width: 100% !important; min-width: 0 !important; overflow: visible !important; margin: 0 !important; padding: 0 !important; position: static !important;`.
  - Manter `.printable-receipt .row` com `display: flex !important; flex-wrap: wrap !important;` e definir as colunas (`.col-6`, `.col-md-3`, `.col-md-4`, `.col-12`) com larguras percentuais explícitas (`50%`, `25%`, `33.33%`, `100%`) para garantir alinhamento perfeito em qualquer resolução de impressão.
  - Assegurar `break-inside: avoid !important; page-break-inside: avoid !important;` no bloco de assinaturas (`.d-print-block`) e nas linhas da tabela de itens (`tr`).

### 2. `resources/css/responsive-custom.css`
- Atualizar a seção `5. Regras Globais de Reset para Impressão Direta (@media print)`:
  - Incluir os seletores com especificidade máxima para o AdminLTE 4:
    `.sidebar-expand-lg.layout-fixed .app-main`, `body.layout-fixed .app-wrapper`, etc.
  - Forçar `max-width: 100% !important; min-width: 0 !important; overflow: visible !important;`.

### 3. Recompilação de Assets
- Executar `pnpm run build` para que o CSS de produção (`public/build/assets/...`) seja devidamente atualizado.

## Verification Plan

### Automated Tests
- Executar a suíte de testes de movimentação:
  ```powershell
  C:\xampp\php\php.exe artisan test --filter=Movement
  ```

### Manual Verification
1. Abrir a tela `/movements/1` no navegador.
2. Clicar em "Imprimir Comprovante".
3. Verificar se o preview da impressora abre com "Escala: Padrão (100%)" exibindo o comprovante perfeitamente enquadrado, sem cortes e sem folhas em branco.
