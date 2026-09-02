# Quickstart & Guia de Validação da Feature

**Feature**: Adequação da Tela de Login para Produção e Correção da Impressão Direta
**Branch**: `006-login-producao-impressao`
**Date**: 2026-09-02

## 1. Pré-requisitos
- Servidor Web (Apache/Nginx via XAMPP) ativo em `http://localhost/almoxarifadoccb` ou `php artisan serve`.
- Banco de dados MySQL/MariaDB configurado.
- Compilação de assets executada (`pnpm build` ou `pnpm dev`).

---

## 2. Roteiro de Validação Manual

### Cenário 1: Validação da Tela de Login
1. Abra uma janela anônima no navegador e acesse a rota `/login`.
2. **Verificações**:
   - O campo "E-mail de Acesso" deve estar em branco com placeholder `usuario@ccb.org.br`.
   - O campo "Senha" deve estar completamente em branco.
   - Não deve haver seção de botões de atalho (*Admin*, *Almoxarife*, *Consulta*).
   - Digite as credenciais do usuário real cadastrado e confirme que o login é efetuado com sucesso.

### Cenário 2: Validação da Impressão Direta do Comprovante
1. Acesse o menu **Movimentações** (`/movements`).
2. Abra o detalhe de qualquer movimentação existente (`/movements/{id}`).
3. Clique no botão **"Imprimir Comprovante"**.
4. **Verificações no Diálogo de Impressão do Navegador**:
   - A pré-visualização deve exibir o logotipo CCB, cabeçalho da congregação/administração, código da movimentação, tipo, datas, responsável e beneficiário/fornecedor.
   - A tabela com os materiais e quantidades deve estar completamente visível e nítida.
   - As linhas e nomes para assinaturas devem estar renderizadas ao final.
   - A barra lateral, cabeçalho superior e botões devem estar ocultos.
   - Não deve haver páginas extras em branco.

### Cenário 3: Validação da Exportação de PDF
1. Na mesma tela de movimentação, clique no botão **"Baixar PDF"**.
2. Confirme que o PDF é baixado e gerado normalmente via DomPDF com integridade idêntica.
