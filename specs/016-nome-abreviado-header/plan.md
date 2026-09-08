# Implementation Plan: Formatação Abreviada do Nome do Usuário no Header

**Branch**: `016-nome-abreviado-header` | **Date**: 2026-09-08 | **Spec**: [spec.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/016-nome-abreviado-header/spec.md)

**Input**: Feature specification from `specs/016-nome-abreviado-header/spec.md`

## Summary

Esta feature implementa a exibição responsiva e compacta do nome do usuário no cabeçalho superior (`navbar`):
- **Em computadores e notebooks (desktop ≥ 768px)**: exibe o **Primeiro e Último Nome** (ex: `Rodrigo Lima`), mantendo a elegância visual sem sobrecarregar a barra.
- **Em celulares e smartphones (mobile < 768px)**: exibe apenas o **Primeiro Nome** (ex: `Rodrigo`), liberando espaço no topo do dispositivo.
- **No menu dropdown (ao clicar) e na tela de perfil**: preserva o **Nome Completo** cadastrado (`Rodrigo Fernando Gomes Lima`).

A solução é implementada através de accessors Eloquent (`first_name` e `short_name`) no modelo `User` e utilitários responsivos do Bootstrap 5 (`d-none d-md-inline` e `d-inline d-md-none`) na view da navbar, sem necessidade de migrations ou alterações no banco de dados.

---

## Technical Context

**Language/Version**: PHP 8.3+ (strict_types=1) / Laravel 12
**Primary Dependencies**: Bootstrap 5, AdminLTE v4, Blade Templating
**Storage**: N/A (sem alterações no banco de dados; consome a coluna existente `name` da tabela `users`)
**Testing**: PHPUnit / Laravel Feature Tests (`tests/Feature/UserProfileTest.php`)
**Target Platform**: Navegadores modernos (Desktop e Mobile / PWA)
**Project Type**: Aplicação Web Monolítica Laravel com Blade
**Performance Goals**: Renderização no servidor instantânea (zero queries adicionais; processamento puramente in-memory via accessors Eloquent)
**Constraints**: Zero quebra de layout no cabeçalho em resoluções < 768px; preservação total do nome completo nos logs e relatórios.

---

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Princípio I (Service Layer Pattern)**: ✅ Passa. A formatação de apresentação é tratada via accessors do próprio modelo Eloquent `User`, sem regra de negócio de estoque nos controllers.
- **Princípio II (Rigor de Tipagem PHP 8.3+)**: ✅ Passa. Métodos do modelo com tipagem estrita de retorno (`: string`) e tratamento de strings limpo com `preg_split`.
- **Princípio V (UI AdminLTE v4 & Bootstrap 5)**: ✅ Passa. Utiliza as classes responsivas nativas do Bootstrap 5 (`d-none d-md-inline` e `d-inline d-md-none`).
- **Diretrizes Globais de Qualidade**: ✅ Passa. Todo o código, testes e documentação elaborados em Português do Brasil (pt-BR).

---

## Project Structure

### Documentation (this feature)

```text
specs/016-nome-abreviado-header/
├── plan.md              # Este plano de implementação
├── research.md          # Pesquisa técnica e decisões de UX
├── data-model.md        # Accessors do modelo User
├── quickstart.md        # Roteiro de validação desktop e mobile
├── contracts/
│   └── ui-contracts.md  # Contrato de renderização responsiva da Navbar
└── checklists/
    └── requirements.md  # Validação de qualidade da especificação
```

### Source Code Impacted

```text
app/
└── Models/
    └── User.php                       # [MODIFY] Adicionar accessors first_name e short_name

resources/
└── views/
    └── partials/
        └── navbar.blade.php           # [MODIFY] Atualizar renderização do nome com classes responsivas

tests/
└── Feature/
    └── UserProfileTest.php            # [MODIFY] Adicionar asserções para first_name, short_name e navbar
```

---

## Planned Phases

### Phase 1: Modelo Eloquent (`app/Models/User.php`)
- Adicionar accessor `getFirstNameAttribute(): string`:
  - Retorna a primeira palavra do nome higienizado.
- Adicionar accessor `getShortNameAttribute(): string`:
  - Retorna o primeiro e o último nome (ou a única palavra se for monônimo).

### Phase 2: View do Cabeçalho (`resources/views/partials/navbar.blade.php`)
- Atualizar a tag do nome do usuário dentro do gatilho do dropdown:
  - `<span class="fw-semibold text-dark d-none d-md-inline">{{ Auth::user()->short_name }}</span>`
  - `<span class="fw-semibold text-dark d-inline d-md-none">{{ Auth::user()->first_name }}</span>`
- Garantir que o dropdown interno continue renderizando `{{ Auth::user()->name }}` completo.

### Phase 3: Testes Automatizados (`tests/Feature/UserProfileTest.php`)
- Criar testes específicos para:
  - Geração correta de `first_name` e `short_name` para nomes simples, duplos e longos.
  - Verificação da presença de `short_name` e `first_name` no HTML retornado na resposta do dashboard/header.

### Phase 4: Validação Manual e Polimento
- Testar em resolução desktop (≥ 768px) e emulando dispositivo móvel (< 768px).
- Validar se o dropdown abre normalmente exibindo o nome completo.
