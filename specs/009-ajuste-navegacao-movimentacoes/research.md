# Research: Correção de Navegação e Contexto entre Entradas e Saídas

**Feature**: `009-ajuste-navegacao-movimentacoes`  
**Date**: 2026-09-04  

---

## Pesquisas e Decisões Técnicas

### 1. Filtragem da Listagem de Saídas em `MovementController@index`

- **Decisão**: Ajustar a consulta de `MovementController@index` para filtrar explicitamente apenas os tipos de consumo e empréstimo:
  ```php
  Movement::whereIn('type', [MovementType::CONSUMPTION, MovementType::LOAN])
      ->with(['user', 'beneficiary', 'destination', 'items.material'])
      ->latest()
      ->paginate(15);
  ```
- **Rationale**: A Opção A aprovada pelo usuário estabelece separação estrita de módulos. O menu "Saídas" e a tela "Histórico de Saídas & Empréstimos" não devem conter lançamentos fiscais de entrada (`ENTRY`).
- **Alternativas consideradas**:
  - *Excluir com `where('type', '!=', MovementType::ENTRY)`*: Funciona, mas `whereIn` com tipos expressos é mais explícito e seguro contra tipos futuros não relacionados a saídas.
  - *Manter visão geral com abas*: Rejeitado na etapa de especificação (o usuário optou pela separação estrita dos menus).

---

### 2. Ativação Contextual da Barra Lateral (`sidebar.blade.php`)

- **Decisão**: A rota compartilhada `movements.show` injeta a variável `$movement` na view. A lógica de classe `.active` no menu lateral será ajustada:
  - **Menu Saídas**:
    ```blade
    {{ (request()->routeIs('movements.index') || request()->routeIs('movements.create') || (request()->routeIs('movements.show') && isset($movement) && $movement->type !== \App\Enums\MovementType::ENTRY)) ? 'active' : '' }}
    ```
  - **Menu Entradas**:
    ```blade
    {{ (request()->routeIs('entries.*') || (request()->routeIs('movements.show') && isset($movement) && $movement->type === \App\Enums\MovementType::ENTRY)) ? 'active' : '' }}
    ```
- **Rationale**: Permite reaproveitar a infraestrutura robusta e homologada de impressão e visualização de comprovante de `movements.show`, sem quebrar a identidade visual e o mapa mental do usuário de estar dentro do módulo "Entradas".
- **Alternativas consideradas**:
  - *Criar uma rota duplicada `entries.show`*: Duplicaria lógica de controller, testes e o arquivo blade de 480+ linhas que contém todas as regras de impressão A4. O uso condicional na sidebar é infinitamente mais limpo e manutenível.

---

### 3. Direcionamento Inteligente do Botão "← Voltar" em `movements.show`

- **Decisão**: No rodapé da visualização do comprovante ([show.blade.php](file:///d:/xampp/htdocs/almoxarifadoccb/resources/views/movements/show.blade.php#L415)), definir a rota de retorno com base no tipo da movimentação:
  ```blade
  @php
    $backRoute = ($movement->type === \App\Enums\MovementType::ENTRY) ? route('entries.index') : route('movements.index');
  @endphp
  <a href="{{ $backRoute }}" class="btn btn-light">
    <i class="bi bi-arrow-left me-1"></i> Voltar
  </a>
  ```
- **Rationale**: Garante retorno confiável e previsível para a lista correta (se era uma entrada, volta para a lista de Notas Fiscais/Doações; se era saída/empréstimo, volta para a lista de Saídas).
- **Alternativas consideradas**:
  - *`url()->previous()`*: Pode gerar comportamento errático se a página foi aberta diretamente em nova guia, recarregada via F5 ou se o usuário veio de uma tela de impressão. A rota explícita é 100% determinística.
