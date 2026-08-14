# Quickstart Validation Guide: Controle de Validade e Patrimônio de Produtos

## Prerequisites
- PHP 8.3+ em `C:\xampp\php\php.exe`
- MySQL/MariaDB ativo via XAMPP
- Servidor web ativo (ou `php artisan serve`)

---

## Environment Setup & Migration
1. Executar as migrações para adicionar os campos `expiration_date` e `patrimony_code` na tabela `materials`:
   ```bash
   C:\xampp\php\php.exe artisan migrate
   ```

---

## End-to-End Validation Scenarios

### Scenario 1: Cadastro e Identificação de Produtos Vencidos e a Vencer
1. Acesse o menu **Materiais** > **Novo Material**.
2. Cadastre um produto perecível (ex.: "Tinta Acrílica Branca 18L"):
   - Preencha a **Data de Validade** com uma data no passado (ex.: 10 dias atrás).
   - Salve o produto.
3. Verifique na listagem de materiais se o produto exibe a badge **Vencido** (`bg-danger`).
4. Cadastre outro produto (ex.: "Massa Corrida 25kg"):
   - Preencha a **Data de Validade** com uma data nos próximos 15 dias.
   - Salve o produto.
5. Verifique na listagem se o produto exibe a badge **Próximo de Vencer** (`bg-warning text-dark`).

---

### Scenario 2: Cadastro e Busca por Código de Patrimônio
1. Acesse o menu **Materiais** > **Novo Material**.
2. Cadastre uma ferramenta/equipamento da entidade (ex.: "Furadeira de Impacto Industrial"):
   - Preencha o **Código de Patrimônio** (ex.: `PAT-CCB-2026-001`).
   - Salve o item.
3. Na busca rápida da listagem de materiais, digite `PAT-CCB-2026-001`.
4. Confirme que apenas o equipamento correspondente é retornado e que a tag de patrimônio é exibida.

---

### Scenario 3: Painel Principal (Dashboard) e Relatórios
1. Acesse o **Dashboard**.
2. Confirme a presença dos cards de resumo com a contagem de **Produtos Vencidos** e **Produtos a Vencer**.
3. Clique no card de **Produtos Vencidos** para ser direcionado aos relatórios/listagem filtrada.
4. Acesse o menu **Relatórios** > selecione o filtro de validade **Produtos Vencidos**.
5. Exporte ou visualize o relatório e confirme a precisão dos dados exibidos.

---

## Automated Verification Command
```bash
C:\xampp\php\php.exe artisan test --filter=MaterialExpirationAndPatrimonyTest
```
