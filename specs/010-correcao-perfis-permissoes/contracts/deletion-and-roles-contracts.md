# Interface & Routing Contracts: Exclusão Segura e Perfis

**Feature**: `010-correcao-perfis-permissoes`  
**Date**: 2026-09-04  

---

## 1. Rotas de Exclusão

| Método | Endpoint | Controller Action | Permissão / Middleware | Resposta de Sucesso | Resposta de Erro |
|--------|----------|-------------------|------------------------|---------------------|------------------|
| `DELETE` | `/users/{user}` | `UserController@destroy` | `can:manage-users` | `redirect('/users')` com `success` | `back()` com `error` (se autoexclusão ou com movimentações) |
| `DELETE` | `/materials/{material}` | `MaterialController@destroy` | `auth` + `can:manage-materials` + admin | `redirect('/materials')` com `success` | `back()` com `error` (se houver histórico ou estoque > 0) |
| `DELETE` | `/movements/{movement}` | `MovementController@destroy` | `auth` + `can:manage-users` (admin) | `redirect(index)` com `success` | `back()` com `error` (se saldo insuficiente para estorno) |

---

## 2. Contrato de Sincronização de Perfis (`UserController`)

- No `store(Request $request)`:
  ```php
  $user->syncRoles([$data['role']]);
  ```
- No `update(Request $request, User $user)`:
  ```php
  $user->syncRoles([$data['role']]);
  ```
- Garantia: Em nenhuma hipótese um usuário permanecerá com mais de 1 papel em `model_has_roles`.
