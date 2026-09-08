# HTTP Contracts: Perfil de Usuário com Foto e Avatar

**Feature**: `015-foto-perfil-usuario`  
**Date**: 2026-09-08  

---

## 1. Endpoints de Perfil do Usuário

### 1.1 `GET /profile` (Visualização / Edição de Perfil)

- **Middleware**: `web`, `auth`
- **Renderização**: View Blade `profile.edit`
- **Dados Fornecidos**:
  - Usuário autenticado (`Auth::user()`) com nome, e-mail, perfil, avatar atual e data de cadastro.
- **Resposta**: `200 OK`

---

### 1.2 `PUT /profile` (Atualização de Dados do Perfil)

- **Middleware**: `web`, `auth`
- **Content-Type**: `multipart/form-data`
- **Payload**:
  - `name` (string, obrigatório, máx 150)
  - `avatar` (file, opcional, formatos jpeg, png, jpg, webp, gif, máx 5MB)
  - `remove_avatar` (boolean/flag, opcional)
- **Respostas**:
  - `302 Redirect` -> `/profile` com mensagem de sucesso em sessão Toastr: `"Perfil atualizado com sucesso!"`
  - `422 Unprocessable Content` -> Erros de validação (ex: arquivo não é imagem, tamanho excessivo).

---

### 1.3 `PUT /profile/password` (Alteração de Senha)

- **Middleware**: `web`, `auth`
- **Payload**:
  - `current_password` (string, obrigatório, correspondente à senha atual)
  - `password` (string, obrigatório, mín 8 caracteres, confirmado)
  - `password_confirmation` (string, obrigatório)
- **Respostas**:
  - `302 Redirect` -> `/profile` com mensagem de sucesso: `"Senha alterada com sucesso!"`
  - `422 Unprocessable Content` -> Senha atual incorreta ou nova senha não preenche os requisitos.

---

## 2. Endpoints Administrativos de Gestão de Usuários

### 2.1 `GET /users` (Listagem de Usuários)
- Exibe o avatar circular ao lado do nome do usuário na tabela.

### 2.2 `PUT /users/{user}` (Atualização Administrativa)
- Suporte para upload ou remoção de avatar de qualquer usuário por um Administrador através do formulário de edição de usuários.
