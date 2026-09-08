# Implementation Plan: Perfil de Usuário com Foto e Exibição no Header e Tabela

**Branch**: `015-foto-perfil-usuario` | **Date**: 2026-09-08 | **Spec**: [spec.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/015-foto-perfil-usuario/spec.md)

**Input**: Feature specification from `specs/015-foto-perfil-usuario/spec.md`

## Summary

Implementação da foto de perfil para os usuários do sistema, criação de uma página de perfil elegante e moderna (`/profile`) para edição de dados pessoais (nome, foto e senha), exibição do avatar circular personalizado no header da aplicação (ao lado do nome e perfil do usuário logado) e na listagem de usuários do sistema (`/users`).

## Technical Context

**Language/Version**: PHP 8.3+ / Laravel 12+ (conforme `constitution.md`)

**Primary Dependencies**: AdminLTE v4, Bootstrap 5, SweetAlert2, Toastr, `spatie/laravel-permission`

**Storage**: Local Disk `public` via Laravel Storage (`storage/app/public/avatars/`)

**Testing**: PHPUnit / Artisan Test (`C:\xampp\php\php.exe artisan test`)

**Target Platform**: Web Application (Navegadores modernos, PWA responsivo)

**Project Type**: Web Application Monolítica (Laravel Blade + Service Layer)

**Performance Goals**: Renderização do avatar no header com cache de assets em < 50ms; upload de fotos em < 1s; design responsivo sem refluxos de layout.

**Constraints**: Validação estrita de extensão e MIME type de imagens (`jpeg`, `png`, `jpg`, `webp`, `gif`), nomes com UUID randômico para evitar cache stale, exclusão física de arquivos antigos ao substituir ou deletar usuário.

**Scale/Scope**: Migration incremental para `users`, modelo `User`, serviço `UserAvatarService`, `ProfileController`, atualização de `UserController`, view `profile/edit.blade.php`, componentes `partials/navbar.blade.php` e `users/index.blade.php`.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Princípio I (Camada de Serviços & POO Estrita)**: PASS. O processamento, armazenamento, geração de UUID e deleção física das imagens de avatar são encapsulados no serviço dedicado `UserAvatarService`.
- **Princípio II (Rigor de Tipagem & Padrões PHP 8.3+)**: PASS. Uso de `declare(strict_types=1);`, métodos com tipagem rigorosa de parâmetros e retornos, e validação isolada via `FormRequest` / controllers.
- **Princípio III (Integridade Transacional e Rastreabilidade)**: PASS. Preservação de todos os vínculos de autoria de movimentações e logs do usuário.
- **Princípio IV (Gestão Especializada)**: PASS. Preservada.
- **Princípio V (Interface do Usuário Integrada e Feedback)**: PASS. Construído sobre AdminLTE v4 com Bootstrap 5, avatares circulares elegantes, cards de perfil modernos, preview dinâmico e feedback imediato via Toastr.

## Project Structure

### Documentation (this feature)

```text
specs/015-foto-perfil-usuario/
├── plan.md              # Este documento de plano de implementação
├── research.md          # Fase 0: Pesquisa técnica e decisões de arquitetura
├── data-model.md        # Fase 1: Especificação de dados e ciclo de vida do avatar
├── quickstart.md        # Fase 1: Guia prático de validação e testes
├── contracts/           
│   └── http-contracts.md # Fase 1: Contratos HTTP de perfil e usuários
└── tasks.md             # Fase 2: Gerado pelo comando /speckit-tasks
```

### Source Code (repository root)

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProfileController.php             # [NEW] Controller para exibir e atualizar o perfil do usuário logado
│   │   └── UserController.php                # [MODIFY] Suporte a upload/remoção de avatar na gestão de usuários
│   └── Requests/
│       ├── UpdateProfileRequest.php          # [NEW] Validação de nome e avatar
│       └── UpdatePasswordRequest.php         # [NEW] Validação de alteração de senha
├── Models/
│   └── User.php                              # [MODIFY] Campo avatar_path em fillable, accessor avatarUrl, hasAvatar e initials
└── Services/
    └── UserAvatarService.php                 # [NEW] Serviço de manipulação física de avatares (upload, replace, delete)

database/
└── migrations/
    └── 2026_09_08_000002_add_avatar_path_to_users_table.php # [NEW] Coluna avatar_path (nullable string)

resources/views/
├── partials/
│   └── navbar.blade.php                      # [MODIFY] Renderizar avatar circular e atalho "Meu Perfil" no dropdown
├── profile/
│   └── edit.blade.php                        # [NEW] Tela moderna e elegante de perfil e alteração de senha
└── users/
    └── index.blade.php                       # [MODIFY] Exibir avatar circular na listagem e suportar upload na edição

routes/
└── web.php                                   # [MODIFY] Adicionar rotas autenticadas para perfil (/profile)
```

**Structure Decision**: Aplicação monolítica Laravel seguindo Controller -> Service -> Model + Blade Views.

## Complexity Tracking

*Nenhuma violação constitucional registrada.*
