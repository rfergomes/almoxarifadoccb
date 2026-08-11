# Research & Decisions: Entradas, Usabilidade Inline e Relatórios CCB

## 1. Entrada de Estoque por Nota Fiscal e Doação (`EntryService.php`)

- **Decisão:** Criar a tabela `entry_documents` e o tipo de movimentação `ENTRY = 'ENTRY'`.
- **Justificativa:** Isola o registro fiscal/doação (`document_number`, `supplier_or_donor`, `document_type`) de forma que cada entrada gera uma movimentação de estoque atômica com incremento em `current_stock`.
- **Alternativas Consideradas:** 
  - *Atualizar saldo diretamente na edição do material:* Rejeitada por não deixar histórico auditável de quem comprou/doou, data e número do documento.

---

## 2. Usabilidade Inline via Modais AJAX (`QuickRegistrationController.php`)

- **Decisão:** Criar modais parciais em `resources/views/partials/modal_quick_beneficiary.blade.php` e `modal_quick_destination.blade.php`, consumindo rotas AJAX (`/api/quick-beneficiary`, `/api/quick-destination`).
- **Justificativa:** O operador pode salvar um novo beneficiário ou destino durante o lançamento de saídas ou entradas sem recarregar a página e sem perder o formulário preenchido. O script insere a nova opção no `<select>` e a marca como selecionada automaticamente.
- **Alternativas Consideradas:** 
  - *Redirecionar para a página de cadastro com return_url:* Rejeitada por ser lenta e aumentar o risco de perda de dados nos campos do formulário principal.

---

## 3. Geração de Relatórios em PDF e Excel

- **Decisão:** 
  - **PDF:** Utilizar a biblioteca `barryvdh/laravel-dompdf` renderizando views Blade limpas otimizadas para impressão (`resources/views/reports/pdf/`).
  - **Excel:** Disponibilizar exportação em `.xlsx` / `.csv` formatado.
- **Justificativa:** DomPDF permite renderizar layouts HTML/CSS com imagens locais (`/public/images/CCB_Logo_fundo_claro.png`) convertendo direto para PDF com alta precisão visual.
- **Alternativas Consideradas:** 
  - *Print nativo via JavaScript (`window.print`):* Rejeitado por depender da configuração do navegador do usuário e não gerar arquivo para envio/arquivamento.

---

## 4. Aplicação da Identidade Visual CCB (`/public/images/`)

- **Decisão:** Mapear e aplicar as imagens de logotipo:
  - `CCB_Logo_fundo_claro.png`: Empregada no cabeçalho das páginas públicas, comprovantes e relatórios PDF.
  - `CCB_Logo_funco_escuro.png`: Empregada na tela de login e sidebar com tema escuro.
  - `CCB_Logo_Reduzido.png`: Empregada como ícone de marca/favicon.
