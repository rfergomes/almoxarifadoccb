# Data Model: Perfis Únicos e Exclusão Segura com Integridade Relacional

**Feature**: `010-correcao-perfis-permissoes`  
**Date**: 2026-09-04  

---

## 1. Entidades e Relacionamentos

### `User`
- **Tabela**: `users`
- **Campos**: `id`, `name`, `email`, `password`, `status`, `created_at`, `updated_at`
- **Relações**:
  - `roles()`: `MorphToMany(Role)` via `model_has_roles` (regra de negócio: cardinalidade estrita 1:1)
  - `movements()`: `HasMany(Movement)` (impede exclusão se `count > 0`)
- **Novos Métodos/Helpers**:
  - `primary_role`: String com o nome do perfil único (`Administrador`, `Almoxarife` ou `Consulta`)
  - `primary_role_badge`: Classe CSS e ícone Bootstrap correspondentes

### `Material`
- **Tabela**: `materials`
- **Campos**: `id`, `code_sku`, `name`, `category_id`, `unit_measure`, `current_stock`, `minimum_stock`, `status`
- **Relações**:
  - `movementItems()`: `HasMany(MovementItem)`
- **Regra de Exclusão**:
  - Se `movementItems()->exists() == true` $\rightarrow$ Bloqueado
  - Se `current_stock > 0` $\rightarrow$ Bloqueado
  - Se ambas forem falsas $\rightarrow$ Permitido

### `Movement` & `MovementItem`
- **Tabela**: `movements` e `movement_items`
- **Relações**:
  - `items`: `HasMany(MovementItem)`
  - `entryDocument`: `HasOne(EntryDocument)`
- **Regra de Exclusão**:
  - Restrita a usuários com papel `Administrador`
  - Reversão de estoque executada atomicamente por `StockService::deleteMovement($movement)`

---

## 2. Diagrama de Validação de Exclusão

```mermaid
flowchart TD
    A[Solicitação de Exclusão] --> B{Tipo de Entidade}
    
    B -->|Usuário| U1{É o próprio usuário logado?}
    U1 -->|Sim| U_ERR1[Erro: Não pode excluir a própria conta]
    U1 -->|Não| U2{Possui movimentações associadas?}
    U2 -->|Sim| U_ERR2[Erro: Possui histórico de auditoria. Inative o usuário.]
    U2 -->|Não| U_OK[Exclusão Concluída]
    
    B -->|Material| M1{Possui movimentações de entrada ou saída?}
    M1 -->|Sim| M_ERR1[Erro: Material possui histórico. Inative o item.]
    M1 -->|Não| M2{Estoque atual > 0?}
    M2 -->|Sim| M_ERR2[Erro: Estoque diferente de zero.]
    M2 -->|Não| M_OK[Exclusão Concluída]
    
    B -->|Movimentação| V1{Usuário é Administrador?}
    V1 -->|Não| V_ERR1[Erro 403: Acesso Restrito]
    V1 -->|Sim| V2{Reversão de Estoque causa saldo negativo?}
    V2 -->|Sim| V_ERR2[Erro: Saldo insuficiente para estornar entrada]
    V2 -->|Não| V_OK[Estorna estoque e exclui movimentação]
```
