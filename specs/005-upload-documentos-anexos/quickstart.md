# Quickstart Validation Guide: Upload de Arquivos e Anexos em Entradas e Avarias

## Prerequisites
- PHP 8.3+ em `C:\xampp\php\php.exe`
- MySQL/MariaDB ativo via XAMPP
- Link simbólico de storage criado (`php artisan storage:link`)

---

## Environment Setup & Migration
1. Executar as migrações para criar a tabela polimórfica `attachments`:
   ```bash
   C:\xampp\php\php.exe artisan migrate
   ```
2. Garantir o link simbólico do storage:
   ```bash
   C:\xampp\php\php.exe artisan storage:link
   ```

---

## End-to-End Validation Scenarios

### Scenario 1: Lançamento de Entrada de Estoque com Anexo de Nota Fiscal em PDF
1. Acesse o menu **Entradas de Estoque** > **Nova Entrada**.
2. Preencha os dados da Nota Fiscal e selecione os materiais recebidos.
3. No campo **Comprovante / Anexo da Nota (PDF/Imagem)**, selecione um arquivo `.pdf` ou `.png`.
4. Clique em **Confirmar Entrada**.
5. Na listagem de entradas, confirme a presença do ícone de clipe/anexo e clique para abrir/baixar o arquivo em nova aba.

---

### Scenario 2: Ajuste de Inventário / Avaria com Foto da Peça Danificada
1. Acesse **Materiais** e clique na ação **Inventário** em um item.
2. Ajuste o saldo físico e selecione o anexo fotográfico da avaria.
3. Confirme o ajuste.
4. Na consulta de histórico, abra a pré-visualização da imagem no modal.

---

## Automated Verification Command
```bash
C:\xampp\php\php.exe artisan test --filter=AttachmentUploadTest
```
