# Phase 0 Research: Correção da Escala de Impressão Direta do Comprovante

## Problema Investigado
Por que o acionamento de `window.print()` na tela `/movements/{id}` gera uma folha de papel completamente em branco quando a escala está em 100% (Padrão) no Chrome/Edge, e apenas exibe os dados quando o usuário reduz manualmente a escala para 40%?

---

## Descobertas Técnicas

### 1. Comportamento do Motor Chromium com Unidades `vw` em `@media print`
- O template **AdminLTE v4 Beta 2** utiliza as seguintes propriedades em seu layout padrão:
  ```css
  .app-wrapper {
    display: grid;
    grid-template-areas: "lte-app-sidebar lte-app-header" "lte-app-sidebar lte-app-main" "lte-app-sidebar lte-app-footer";
    grid-template-columns: auto 1fr;
    max-width: 100vw;
  }
  .app-main {
    max-width: 100vw;
  }
  .sidebar-expand-lg.layout-fixed .app-main {
    flex: 1 1 auto;
    overflow: auto;
  }
  ```
- **Fato Técnico**: De acordo com a especificação W3C e implementação do motor Chromium (Google Chrome e MS Edge), unidades de viewport (`100vw`, `100vh`) dentro do contexto `@media print` avaliam para as dimensões da janela do navegador de segundo plano (neste caso, uma tela desktop Full HD de **1920px**), e NÃO para a largura física do papel A4 configurado em `@page`.
- Uma folha A4 retrato possui 210mm de largura nominal (~794px a 96 DPI). Com margens laterais de 10mm (área imprimível de 190mm), a largura útil é de aproximadamente **~718px**.
- Razão matemática da necessidade de escala:
  $$\frac{718\text{px}}{1920\text{px}} \approx 37,4\% \approx 40\%$$
- A 40%, o navegador reduz 1920px para caber nos 718px da folha A4. Em 100%, o conteúdo é renderizado a 1920px de largura com overflow cortado e deslocado para fora da página 1.

### 2. Falha de Sobrescrita de Especificidade CSS (Cascade Clash)
- O seletor do AdminLTE `.sidebar-expand-lg.layout-fixed .app-main` possui especificidade `0-3-0` (duas classes no body + uma classe no main).
- As regras atuais em `responsive-custom.css` usavam `.layout-fixed .app-main` (especificidade `0-2-0`).
- Consequentemente, `overflow: auto` e `max-width: 100vw` do AdminLTE NÃO eram anulados no print, causando corte de renderização do Chromium e gerando uma página em branco.

### 3. Desestruturação das Linhas de Grid Internas
- No arquivo `show.blade.php`, havia uma regra inline:
  ```css
  html, body, .app-wrapper, .app-main, .app-content, .container-fluid, .row, .col-lg-10 {
    display: block !important;
  }
  ```
- Isso transformava `.row` em elemento de bloco puro (`display: block`), desmontando as colunas Bootstrap (`.col-md-3`, `.col-6`) dos detalhes e das assinaturas.

---

## Decisões Arquiteturais

### Decisão 1: Reset de Alta Especificidade para o Layout AdminLTE 4
- **Escolha**: Criar seletores de impressão que sobrescrevam diretamente a cadeia do AdminLTE 4:
  ```css
  @media print {
    html,
    body,
    body.layout-fixed,
    .app-wrapper,
    .layout-fixed .app-wrapper,
    .app-main,
    .layout-fixed .app-main,
    .sidebar-expand-lg.layout-fixed .app-main,
    .app-content,
    .container-fluid {
      display: block !important;
      position: static !important;
      width: 100% !important;
      min-width: 0 !important;
      max-width: 100% !important;
      height: auto !important;
      min-height: auto !important;
      overflow: visible !important;
      margin: 0 !important;
      padding: 0 !important;
      background: #ffffff !important;
      background-color: #ffffff !important;
      color: #000000 !important;
      float: none !important;
    }
  }
  ```
- **Rationale**: Anula todas as restrições de grid, flex e overflow viewport do AdminLTE 4 sem modificar o comportamento da tela interativa do sistema.
- **Alternativas consideradas**:
  - *Criar uma rota dedicada `/movements/{id}/print`*: Foi avaliada, porém o usuário espera acionar a impressão direta no próprio botão da tela atual com um clique sem redirecionamento extra. A rota de PDF `/movements/{id}/pdf` já supre o papel de download de arquivo estático.

### Decisão 2: Preservação de Colunas Internas e Isolamento do Comprovante
- **Escolha**: Permitir que `.printable-receipt .row` permaneça com `display: flex !important; flex-wrap: wrap !important;` e `.printable-receipt [class*="col-"]` mantenham larguras proporcionais (ex: `.col-6 { width: 50% !important; }`), garantindo que as assinaturas e os blocos de dados fiquem lado a lado.
- **Rationale**: Mantém o layout profissional e institucional idêntico ao modelo físico oficial da CCB.

### Decisão 3: Configuração Estrita de Papel A4
- **Escolha**: Definir `@page { size: A4 portrait; margin: 8mm 10mm; }` e `@media print { .printable-receipt { width: 100% !important; max-width: 100% !important; } }`.
- **Rationale**: Impede que a largura da tela desktop influencie a renderização do papel.
