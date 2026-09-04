# Quickstart: Validação de Perfis Únicos e Exclusão Segura

**Feature**: `010-correcao-perfis-permissoes`  
**Date**: 2026-09-04  

---

## Cenários de Teste Manual

### 1. Teste de Perfil Único no Cadastro
1. Faça login como Administrador e abra o menu **Usuários** (`/users`).
2. Clique em **+ Novo Usuário** e selecione o perfil **Almoxarife**.
3. Salve o cadastro.
4. **Resultado**: O usuário criado aparece na tabela com exatamente **1 badge azul `[Almoxarife]`**, sem acumular Consulta ou Administrador.
5. Ao fazer login com esse novo usuário, a barra superior exibe exatamente `[Almoxarife]`.

### 2. Teste de Exclusão de Usuário
1. Na lista de usuários, tente excluir sua própria conta conectada $\rightarrow$ O sistema bloqueia a ação.
2. Tente excluir um usuário que tenha lançado movimentações $\rightarrow$ O sistema bloqueia informando o histórico existente.
3. Tente excluir um usuário de teste recém-criado sem movimentações $\rightarrow$ O sistema confirma via SweetAlert2 e exclui com sucesso.

### 3. Teste de Exclusão de Material
1. Acesse **Estoque / Materiais** (`/materials`).
2. Tente excluir um material que já possui saídas ou entradas $\rightarrow$ O sistema bloqueia a exclusão e exibe o aviso sobre o histórico de movimentações.
3. Cadastre um material de teste sem movimentações e acione a exclusão $\rightarrow$ O sistema remove o material com sucesso.

### 4. Teste de Exclusão de Movimentação
1. Cadastre uma movimentação de teste.
2. Como Administrador, acione a exclusão da movimentação $\rightarrow$ O sistema estorna o saldo correspondente no estoque e remove a movimentação.
