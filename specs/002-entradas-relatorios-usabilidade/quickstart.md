# Quickstart & Validation Guide: Entradas, Modais Inline e Relatórios CCB

## 1. Instalação do Pacote DomPDF para Geração de PDFs

```bash
C:\xampp\php\composer.bat require barryvdh/laravel-dompdf --no-interaction
```

---

## 2. Migrações de Banco de Dados

```bash
C:\xampp\php\php.exe artisan migrate
```

---

## 3. Cenários de Validação Fim a Fim (Manual & Automated)

### Cenário 1: Lançamento de Entrada de Estoque por Nota Fiscal
- **Ação:** Acessar `/entries/create`, informar Tipo `Nota Fiscal`, Número `NF-10020`, Fornecedor `Votorantim Cimentos`, selecionar `Cimento CP II` e quantidade 50 unidades.
- **Expectativa:** A entrada é salva com sucesso e o estoque do Cimento aumenta em 50 unidades.

### Cenário 2: Cadastro Inline de Beneficiário via Modal AJAX
- **Ação:** Na tela de nova saída (`/movements/create`), clicar no botão "+ Novo" ao lado do campo Beneficiário. Preencher o modal e clicar em "Salvar".
- **Expectativa:** O modal fecha sem recarregar a página, o novo beneficiário é inserido no `<select>` e pré-selecionado automaticamente, mantendo todos os itens da tabela intactos.

### Cenário 3: Exportação de Relatório de Posição de Estoque em PDF
- **Ação:** Acessar `/reports`, selecionar o relatório "Posição Geral de Estoque" e clicar no botão "Exportar PDF".
- **Expectativa:** O arquivo PDF é baixado contendo a logomarca oficial da CCB no topo e a tabela formatada de materiais com saldos e alertas.

### Cenário 4: Exportação de Empréstimos em Atraso em Excel
- **Ação:** Na tela de Relatórios, filtrar "Empréstimos em Atraso" e clicar no botão "Exportar Excel".
- **Expectativa:** O arquivo `.xlsx` / `.csv` é baixado com as colunas formatadas e valores de atrasos.
