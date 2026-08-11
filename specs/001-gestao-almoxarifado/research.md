# Research & Decisions: Gestão de Almoxarifado Central CCB

## 1. Padrão Arquitetural da Camada de Serviços (Service Layer)

- **Decisão:** Implementar `StockService.php` e `LoanService.php` no diretório `app/Services/` utilizando explicitamente blocos de transação SQL (`DB::transaction`).
- **Justificativa:** Garantir atomicidade nas saídas, entregas de EPI e devoluções parciais ou totais. Caso ocorra qualquer falha no meio de uma gravação de múltiplos itens, a transação sofre rollback automático, impedindo inconsistências entre o saldo em estoque (`current_stock`) e os itens registrados (`movement_items`).
- **Alternativas Consideradas:** 
  - *Lógica em Controllers:* Rejeitada por violar a responsabilidade única (SRP), dificultar testes automatizados e espalhar regras de negócio.
  - *Lógica em Observers de Model:* Rejeitada por esconder efeitos colaterais em mutações simples de dados e dificultar o controle fino sobre movimentações que envolvem cabeçalho e itens simultaneamente.

---

## 2. Tipagem com Enums Nativos do PHP 8.1+

- **Decisão:** Criar Enums em `app/Enums/`:
  - `MovementType`: `CONSUMPTION = 'CONSUMPTION'`, `EPI = 'EPI'`, `LOAN = 'LOAN'`
  - `MovementStatus`: `OPEN = 'OPEN'`, `COMPLETED = 'COMPLETED'`, `PARTIALLY_RETURNED = 'PARTIALLY_RETURNED'`, `OVERDUE = 'OVERDUE'`
  - `ItemStatus`: `DELIVERED = 'DELIVERED'`, `RETURNED = 'RETURNED'`, `PENDING_RETURN = 'PENDING_RETURN'`
- **Justificativa:** Fornece tipagem estática forte em tempo de compilação/execução, eliminando o risco de "strings mágicas" e facilitando a renderização segura de badges nas views Blade do AdminLTE.
- **Alternativas Consideradas:** 
  - *Constantes em Classes/Models:* Rejeitada por falta de métodos nativos de casting e suporte menos transparente em Form Requests e validações do Laravel.

---

## 3. Gestão de Perfis de Acesso (RBAC)

- **Decisão:** Utilizar a biblioteca `spatie/laravel-permission` integrando 3 perfis principais (`Administrador`, `Almoxarife`, `Consulta`).
- **Justificativa:** Padrão consagrado no ecossistema Laravel com suporte a middlewares de rota (`can:permission`), diretivas Blade (`@can`, `@hasrole`) e seeders reproduzíveis.
- **Alternativas Consideradas:** 
  - *Gates/Policies puras sem pacotes:* Rejeitada por exigir desenvolvimento manual de tabelas pivot e controle de permissões dinâmicas que o pacote já entrega testado e mantido pela comunidade.

---

## 4. Integração de UI Front-End (AdminLTE v4, SweetAlert2 e Toastr)

- **Decisão:** 
  - AdminLTE v4 com Bootstrap 5 no layout base `resources/views/layouts/app.blade.php`.
  - Toastr para exibir notificações flashes de sessão (`session('success')`, `session('error')`).
  - SweetAlert2 para intercepção de ações de confirmação de devolução e baixa crítica.
- **Justificativa:** Atende diretamente aos requisitos visuais do projeto, proporcionando visual limpo, moderno, responsivo e seguro contra cliques acidentais em ações destrutivas.
- **Alternativas Consideradas:** 
  - *TailwindCSS / Jetstream:* Rejeitado devido ao requisito explícito do template AdminLTE v4 com Bootstrap 5.
