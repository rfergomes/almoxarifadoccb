# Research: Perfis Únicos, Permissões e Exclusão Segura com Integridade Relacional

**Feature**: `010-correcao-perfis-permissoes`  
**Date**: 2026-09-04  

---

## Pesquisa e Decisões de Engenharia

### 1. Garantia Estrita de Perfil Único por Usuário (1:1)

- **Problema Encontrado**: O método `UserController@store` utilizava `$user->assignRole($data['role'])`, que acumula múltiplos papéis na tabela `model_has_roles`. Além disso, a visualização no cabeçalho pegava arbitrariamente `$user->roles->first()`, gerando divergência entre o que o usuário vê na barra superior e os papéis acumulados na tabela.
- **Decisão**:
  1. No cadastro (`store`) e na atualização (`update`), aplicar estritamente `$user->syncRoles([$data['role']])`.
  2. Adicionar no Model `User` um método/accessor helper `getRoleAttribute()` e `getRoleBadgeAttribute()` que retorne de forma previsível e tipada o perfil único e a classe visual correspondente.
  3. No cabeçalho (`navbar.blade.php`), consultar o perfil único de forma harmonizada.
- **Rationale**: Impede que qualquer usuário acumule papéis no banco de dados e alinha 100% a experiência de usuário entre a barra superior e o gerenciador de usuários.

---

### 2. Exclusão Segura de Usuários (`UserController@destroy`)

- **Decisão**: Criar rota `DELETE /users/{user}` restrita a Administradores (`can:manage-users`).
- **Regras de Integridade**:
  1. `auth()->id() === $user->id`: Bloqueio estrito (usuário não pode deletar a própria conta).
  2. `User::role('Administrador')->count() <= 1` quando `$user->hasRole('Administrador')`: Bloqueio estrito (não pode excluir o último administrador do sistema).
  3. `$user->movements()->exists()`: Se o usuário foi o operador responsável pelo lançamento de qualquer movimentação de estoque, a exclusão física é bloqueada com mensagem informativa ("Este usuário possui movimentações vinculadas e não pode ser excluído para preservar o histórico de auditoria. Altere o status para Inativo").
  4. Caso não possua movimentações, `$user->syncRoles([])` e `$user->delete()` são executados.

---

### 3. Exclusão Segura de Materiais (`MaterialController@destroy`)

- **Decisão**: Criar rota `DELETE /materials/{material}` restrita a Administradores.
- **Regras de Integridade**:
  1. Permissão: Apenas usuário com perfil `Administrador` pode acionar a exclusão.
  2. Relações ativas de estoque: Verificar se existem registros vinculados em `movement_items` (`$material->movementItems()->exists()`). Se houver qualquer histórico (seja de entrada por NF/doação, consumo, EPI ou empréstimo), o sistema bloqueia a exclusão com aviso explicativo e sugere inativar o item.
  3. Saldo físico: Se `current_stock > 0`, bloqueio adicional impeditivo.
  4. Caso não possua nenhum histórico nem saldo, `$material->delete()` é executado com sucesso.

---

### 4. Exclusão Segura de Movimentações (`MovementController@destroy`)

- **Decisão**: Criar rota `DELETE /movements/{movement}` restrita a Administradores.
- **Regras de Integridade & Reversão Transacional (`DB::transaction`)**:
  1. Se a movimentação for de **Entrada (`ENTRY`)**:
     - Cada item adicionou estoque. Para excluir a entrada, o sistema deve deduzir a quantidade de cada material.
     - *Trava de Segurança*: Se `material.current_stock < item.quantity`, a exclusão é rejeitada com mensagem ("Não é possível excluir esta entrada porque parte dos materiais já foi consumida/movimentada e causaria saldo negativo").
     - Se o saldo permitir, subtrai do estoque e remove o registro e o documento anexo associado.
  2. Se a movimentação for de **Saída (`CONSUMPTION` ou `EPI`)**:
     - Cada item reduziu estoque. A exclusão reverte estornando a quantidade de volta ao `current_stock`.
  3. Se a movimentação for de **Empréstimo (`LOAN`)**:
     - Estorna os itens que ainda estavam pendentes de devolução, recompondo o saldo disponível do material.
  4. Todos os itens de movimentação (`MovementItem`) e a movimentação (`Movement`) são removidos dentro da transação atômica via `StockService`.

---

### 5. Sanitização de Registros Existentes no Banco

- **Decisão**: Criar comando artisan ou seeder/rotina (`UserRoleSanitizer`) que varre todos os usuários e garante que nenhum possua mais de 1 papel em `model_has_roles`. Se possuir múltiplos, preserva a ordem de precedência: `Administrador > Almoxarife > Consulta`.
