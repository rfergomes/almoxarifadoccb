# Quickstart & Validation Guide: Gestão de Almoxarifado Central CCB

## 1. Pré-requisitos do Ambiente

- **PHP**: PHP 8.3 / 8.4 em `C:\xampp\php\php.exe`
- **Composer**: Executável em `C:\xampp\php\composer.bat`
- **Node.js / pnpm**: Node v24.19.0+ com `pnpm`
- **Banco de Dados**: MySQL / MariaDB via XAMPP ativo no porto 3306

---

## 2. Passo a Passo de Instalação e Execução

### Passo 1: Instalação das Dependências PHP e Front-End
```bash
# Executar a partir da raiz do projeto
C:\xampp\php\composer.bat install
pnpm install
```

### Passo 2: Instalação das Bibliotecas de UI (AdminLTE v4, SweetAlert2, Toastr)
```bash
pnpm add admin-lte@4.0.0 sweetalert2 toastr bootstrap@5
```

### Passo 3: Execução das Migrations e Seeders
```bash
C:\xampp\php\php.exe artisan migrate:fresh --seed
```

---

## 3. Cenários de Validação Fim a Fim (Manual & Automated)

### Cenário 1: Saída de Consumo e Baixa Automática de Estoque
- **Ação:** Logar como Almoxarife, navegar até `/movements/create`, selecionar tipo `CONSUMPTION`, escolher Beneficiário, Destino "C.O. Jardim das Flores" e adicione 10 unidades de "Cimento CP II".
- **Expectativa:** A movimentação é gravada com sucesso, uma notificação Toastr verde é exibida, e o estoque atual do item "Cimento CP II" é reduzido em exatamente 10 unidades.

### Cenário 2: Bloqueio de Saída com Saldo Insuficiente
- **Ação:** Tentar lançar uma saída de 500 unidades de um item que possui apenas 10 unidades em estoque.
- **Expectativa:** O formulário impede a confirmação ou dispara erro de validação ("Saldo insuficiente"), mantendo o estoque inalterado.

### Cenário 3: Empréstimo de Ferramenta e Controle de Atraso (`OVERDUE`)
- **Ação:** Criar um empréstimo de "Furadeira de Impacto" para um voluntário com data prevista de devolução retroativa (ex: ontem).
- **Expectativa:** O item fica pendente de devolução (`PENDING_RETURN`), e o Dashboard destaca o empréstimo no cartão de **Empréstimos em Atraso**.

### Cenário 4: Devolução Parcial e Total de Empréstimo
- **Ação:** No detalhe da movimentação, utilizar o modal com SweetAlert2 para devolver 1 de 2 unidades emprestadas.
- **Expectativa:** O status passa para `PARTIALLY_RETURNED`, o saldo em estoque aumenta em 1 unidade. Ao devolver a unidade restante, o status do item é alterado para `RETURNED` e a movimentação para `COMPLETED`.

---

## 4. Execução dos Testes Automatizados

```bash
# Executar suite de testes PHPUnit/Pest
C:\xampp\php\php.exe artisan test
```
