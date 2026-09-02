# UI & CSS Contract: Regras de Impressão Direta (`@media print`)

**Escopo**: Global em `resources/views/layouts/app.blade.php`, `resources/css/responsive-custom.css` e `resources/views/movements/show.blade.php`.

## 1. Regras de Reset Estrutural de Mídia Print

Durante a ativação de `@media print`:

1. **Containers Raiz e AdminLTE Layout**:
   - `html, body, .app-wrapper, .app-main, .app-content, .container-fluid`:
     - `height: auto !important;`
     - `min-height: auto !important;`
     - `max-height: none !important;`
     - `overflow: visible !important;`
     - `position: static !important;`
     - `display: block !important;`
     - `background: transparent !important;`
     - `color: #000000 !important;`

2. **Ocultação de Elementos Não-Imprimíveis**:
   - `.app-sidebar, .app-header, .app-footer, .app-content-header, .btn, .alert, .modal, .no-print, nav, aside`:
     - `display: none !important;`

3. **Elementos do Comprovante (`.printable-receipt`)**:
   - `width: 100% !important;`
   - `max-width: 100% !important;`
   - `box-shadow: none !important;`
   - `border: none !important;`
   - `padding: 0 !important;`
   - `margin: 0 !important;`

4. **Tabelas de Itens**:
   - `.table-responsive`: `overflow: visible !important; display: block !important;`
   - `table`: `width: 100% !important; border-collapse: collapse !important;`
   - `tr, td, th`: `page-break-inside: avoid; break-inside: avoid;`

5. **Bloco de Assinaturas**:
   - `.d-print-block`: `display: block !important;`
   - `page-break-inside: avoid; break-inside: avoid;`
   - Linhas de assinatura visíveis com dados do almoxarife e beneficiário/fornecedor.
