# Interface & Navigation Contracts: Módulos de Entradas e Saídas

**Feature**: `009-ajuste-navegacao-movimentacoes`  
**Date**: 2026-09-04  

---

## 1. Contrato de Rotas e Navegação

| Rota | Método | Descrição | Menu Ativo Esperado | Destino do Botão "Voltar" |
|------|--------|-----------|--------------------|----------------------------|
| `/movements` (`movements.index`) | `GET` | Histórico de Saídas & Empréstimos | **Saídas** | N/A (listagem raiz) |
| `/entries` (`entries.index`) | `GET` | Histórico de Entradas de Estoque (NF / Doações) | **Entradas** | N/A (listagem raiz) |
| `/movements/{movement}` (`movements.show`) onde `type == ENTRY` | `GET` | Comprovante de Entrada de Estoque | **Entradas** | `route('entries.index')` |
| `/movements/{movement}` (`movements.show`) onde `type != ENTRY` | `GET` | Comprovante de Saída ou Empréstimo | **Saídas** | `route('movements.index')` |

---

## 2. Contrato de Filtragem de Dados (`MovementController@index`)

- **Requisição**: `GET /movements`
- **Condição**: A consulta Eloquent para a coleção paginada DEVE conter a cláusula `whereIn('type', [MovementType::CONSUMPTION, MovementType::LOAN])`.
- **Garantia**: Nenhuma linha retornada possui `type === MovementType::ENTRY`.

---

## 3. Contrato Visual do Sidebar (`resources/views/partials/sidebar.blade.php`)

```blade
<!-- Menu Saídas -->
<a href="{{ route('movements.index') }}" 
   class="nav-link {{ (request()->routeIs('movements.index') || request()->routeIs('movements.create') || (request()->routeIs('movements.show') && isset($movement) && $movement->type !== \App\Enums\MovementType::ENTRY)) ? 'active' : '' }}">
   ...
</a>

<!-- Menu Entradas -->
<a href="{{ route('entries.index') }}" 
   class="nav-link {{ (request()->routeIs('entries.*') || (request()->routeIs('movements.show') && isset($movement) && $movement->type === \App\Enums\MovementType::ENTRY)) ? 'active' : '' }}">
   ...
</a>
```
