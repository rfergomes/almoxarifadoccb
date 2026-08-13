# Implementation Plan: Responsividade Total & PWA (Progressive Web App)

**Branch**: `003-responsive-pwa` | **Date**: 2026-08-13 | **Spec**: [`spec.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/spec.md)

**Input**: Feature specification from `/specs/003-responsive-pwa/spec.md`

## Summary

Esta funcionalidade tornará a interface do Sistema de Gestão de Almoxarifado Central CCB 100% responsiva (otimizada para smartphones, tablets e desktops) sobre a stack AdminLTE v4 + Bootstrap 5, além de implementar o suporte completo a PWA (Progressive Web App) através de um manifesto Web App (`manifest.json`), ícones institucionais, Service Worker nativo (`sw.js`) para cache de assets estáticos e fallback de página offline.

## Technical Context

**Language/Version**: PHP 8.3+ / Laravel 12+, JavaScript (ES6+), HTML5, CSS3

**Primary Dependencies**: AdminLTE v4 (`admin-lte@4.0.0`), Bootstrap 5.3, Select2 (Bootstrap 5 Theme), SweetAlert2, Toastr

**Storage**: Cache Storage no navegador (via Service Worker) para assets estáticos; MySQL para banco de dados transacional

**Testing**: PHPUnit / Pest para testes de integração Laravel; testes de responsividade e auditoria PWA via Google Lighthouse e Chrome DevTools

**Target Platform**: Navegadores móveis e desktop modernos (Chrome, Edge, Safari iOS 15+, Firefox, Android/iOS standalone PWA)

**Project Type**: Web Application (Laravel Blade + Service Worker)

**Performance Goals**: Pontuação no Google Lighthouse PWA ≥ 90/100; zero estouro horizontal (overflow 0px em 320px); tempo de recarregamento < 1s via Service Worker cache

**Constraints**: Preservar o Princípio III da Constituição (operações de estoque exigem conectividade e validação transacional no banco); áreas de toque com mínimo de 44x44px em mobile.

**Scale/Scope**: Todas as visões Blade (`resources/views/`), layout mestre (`app.blade.php`), manifesto PWA em `public/manifest.json`, Service Worker em `public/sw.js` e ícones institucionais.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Princípio I (Service Layer):** ✅ Passa. A lógica de PWA e responsividade afeta o frontend/layout Blade e Service Worker, mantendo a camada de serviços intacta.
- **Princípio II (Rigor PHP 8.3+ / Laravel 12):** ✅ Passa. Código mantido nas convenções PSR-12 e Laravel.
- **Princípio III (Integridade Transacional de Estoque):** ✅ Passa. Operações mutáveis continuam sendo validadas estritamente no servidor via conectividade ativa com aviso amigável se o usuário estiver offline.
- **Princípio IV (EPIs e Empréstimos):** ✅ Passa. Regras de empréstimos mantidas; formulários adaptados para exibição responsiva.
- **Princípio V (Interface AdminLTE v4 / Bootstrap 5 / SweetAlert2):** ✅ Passa. A responsividade potencializa a experiência visual nativa do AdminLTE v4 e Bootstrap 5.

## Project Structure

### Documentation (this feature)

```text
specs/003-responsive-pwa/
├── plan.md              # Este arquivo (plano de implementação)
├── research.md          # Pesquisa técnica e decisões de arquitetura PWA
├── data-model.md        # Esquema de dados do Manifest, Cache e Breakpoints
├── quickstart.md        # Guia de validação e testes de responsividade / PWA
├── contracts/           # Contratos do Web App Manifest e Service Worker
└── tasks.md             # Tarefas de implementação (comando /speckit-tasks)
```

### Source Code (repository root)

```text
public/
├── manifest.json                  # Manifesto Web App PWA
├── sw.js                         # Service Worker de cache e fallback offline
├── offline.html                   # Tela de fallback quando sem conexão
└── images/
    └── icons/                     # Ícones PWA institucionais (192x192, 512x512)

resources/
├── css/
│   └── responsive-custom.css     # Ajustes de responsividade (tabelas, touch, breakpoints)
├── js/
│   └── pwa-register.js            # Script de registro do Service Worker e prompt PWA
└── views/
    ├── layouts/
    │   └── app.blade.php          # Meta tags PWA, viewport e links de manifesto
    ├── components/                # Componentes Blade ajustados
    └── ...                        # Demais visões Blade responsivas
```

**Structure Decision**: Aplicação Laravel única com componentes visuais Blade e arquivos estáticos PWA sob a pasta `public/`.

## Complexity Tracking

> **Sem violações da Constituição.**
