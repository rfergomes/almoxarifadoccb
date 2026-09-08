# Quickstart: Validação da Correção de Exclusão de Material, Notificações e SKU Sequencial

## Pré-requisitos
- Servidor PHP / Apache em execução (`http://127.0.0.1:8000` ou vhost local).
- Usuário autenticado com perfil `Administrador`.

---

## 1. Validação de Carregamento de Scripts e Toastr
1. Acessar a aplicação no navegador em qualquer tela autenticada (ex: `/materials`).
2. Abrir o DevTools (F12) na aba **Console**.
3. **Resultado Esperado**:
   - Nenhum erro do tipo `Refused to execute script ... toastr.min.css`.
   - Nenhum erro `ReferenceError: toastr is not defined`.
   - `typeof toastr` digitado no console retorna `'object'`.

---

## 2. Validação da Exclusão Segura de Material
1. Na listagem de materiais (`/materials`):
   - Localizar ou criar um material de teste sem movimentações e com estoque 0.
   - Clicar no botão vermelho com ícone de lixeira **"Excluir"**.
2. **Resultado Esperado**:
   - A janela modal de confirmação SweetAlert2 abre imediatamente com o título "Excluir Material?" e botões de confirmação.
   - Ao clicar em "Cancelar", a caixa fecha e o registro permanece intacto.
   - Ao clicar em "Sim, excluir!", a requisição é disparada, a página recarrega e exibe a notificação Toastr de sucesso no canto superior direito: `"Material '...' excluído com sucesso!"`.
3. Tentar excluir um material que possua saldo físico em estoque ou histórico de movimentação:
   - **Resultado Esperado**: O sistema recusa a exclusão e exibe notificação Toastr de erro informando a razão do bloqueio (preservação do saldo/histórico).

---

## 3. Validação do Código SKU Opcional e Geração Automática `GEN-###`
1. Clicar no botão **"Cadastrar Material"** (ou no modal de entrada rápida).
2. Deixar o campo **Código SKU** vazio.
3. Preencher os demais campos obrigatórios (Nome, Categoria, Unidade de Medida, Estoque).
4. Submeter o formulário.
5. **Resultado Esperado**:
   - O material é cadastrado com sucesso sem erro de validação.
   - O campo `Código SKU` na listagem recebe automaticamente o próximo código sequencial: `GEN-001` (ou `GEN-002`, `GEN-003`, etc., incrementando o maior existente).
6. Cadastrar um material informando um código manual (ex: `MAT-ABC`):
   - **Resultado Esperado**: O sistema mantém o código informado `MAT-ABC`.

---

## 4. Validação do Service Worker com Extensões do Navegador
1. Com extensões do Chrome ativas, navegar e recarregar a tela `/materials`.
2. Verificar o console do navegador.
3. **Resultado Esperado**:
   - Zero erros do tipo `Failed to execute 'put' on 'Cache': Request scheme 'chrome-extension' is unsupported`.
   - Zero erros do tipo `TypeError: Failed to convert value to 'Response'`.
