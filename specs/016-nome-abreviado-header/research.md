# Technical Research: Formatação Responsiva e Abreviada do Nome no Header

**Feature**: `016-nome-abreviado-header`
**Date**: 2026-09-08

## Technical Context & Decisions

### Decision 1: Implementação dos Atributos Derivados no Modelo Eloquent (`User.php`)
- **Decision**: Criar accessors Eloquent `getFirstNameAttribute(): string` e `getShortNameAttribute(): string` na classe `App\Models\User`.
- **Rationale**:
  - Centraliza a lógica de formatação de nome no modelo do domínio, garantindo que qualquer visualização ou componente Blade possa invocar `$user->first_name` ou `$user->short_name` com tipagem estrita e reusabilidade.
  - Não requer alteração na tabela do banco de dados (zero overhead de migration).
  - Trata nomes monônimos (ex: "Admin" -> "Admin"), nomes compostos (ex: "Rodrigo Fernando Gomes Lima" -> "Rodrigo Lima"), e preposições sem falhas de índice.
- **Alternatives Considered**:
  - *Helper global ou Blade Directive (`@shortName($user->name)`)*: Menos elegante e espalha a responsabilidade para fora do modelo de usuário, violando princípios de POO.
  - *JavaScript no cliente para truncar o texto*: Poderia causar flashes de layout (FOUC) e depende de renderização no navegador. A renderização nativa pelo Blade é instantânea e consistente.

### Decision 2: Abordagem Responsiva com Bootstrap 5 na Navbar
- **Decision**: Utilizar classes utilitárias nativas do Bootstrap 5 (`d-none d-md-inline` para `short_name` e `d-inline d-md-none` para `first_name`) no componente `resources/views/partials/navbar.blade.php`.
- **Rationale**:
  - Bootstrap 5 já está embutido no AdminLTE v4 do projeto.
  - O breakpoint `md` (768px) é o divisor padrão entre dispositivos móveis e tablets/desktops.
  - Garante que a transição seja puramente via CSS sem necessidade de requisições AJAX ou listeners pesados de redimensionamento em JavaScript.
- **Alternatives Considered**:
  - *Truncamento com reticências CSS (`text-truncate`)*: Corta palavras no meio (ex: "Rodrigo Fern..."), o que prejudica a estética e a clareza da identidade.

### Decision 3: Preservação do Nome Completo no Dropdown e Demais Módulos
- **Decision**: Manter `Auth::user()->name` no item *"Conectado como"* do menu dropdown, no título da página de perfil `/profile` e em todas as tabelas de auditoria e movimentações.
- **Rationale**:
  - Cumpre rigorosamente a exigência do usuário: o nome condensado é exclusivo para a barra de topo para melhorar o layout, sem comprometer a identidade formal e rastreabilidade do sistema.

## Edge Cases Evaluated
1. **Nome com espaços duplos ou acidentais nas pontas**: Tratado com `trim()` e expressão regular `preg_split('/\s+/', ...)` garantindo que múltiplos espaços seguidos não gerem elementos vazios.
2. **Usuário com apenas uma palavra no nome**: `short_name` retorna a própria palavra única, sem duplicá-la.
3. **Caracteres especiais e acentuação**: Suportado nativamente pelo PHP 8.3 e codificação UTF-8.
