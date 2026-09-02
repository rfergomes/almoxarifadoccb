# Implementation Plan: Adequação da Tela de Login para Produção e Correção da Impressão Direta

**Branch**: `006-login-producao-impressao` | **Date**: 2026-09-02 | **Spec**: [spec.md](file:///d:/xampp/htdocs/almoxarifadoccb/specs/006-login-producao-impressao/spec.md)

**Input**: Feature specification from `specs/006-login-producao-impressao/spec.md`

## Summary

Esta funcionalidade adequa a tela de autenticação para o ambiente de produção (removendo valores padrão de teste e atalhos de demonstração) e resolve o problema dos botões de impressão direta que abriam páginas em branco, garantindo a renderização completa dos comprovantes físicos e documentos através de um reset das propriedades de `@media print` no layout do AdminLTE v4.

---

## Technical Context

**Language/Version**: PHP 8.3+, Blade Templates, HTML5, CSS3, JavaScript ES6
**Primary Dependencies**: Laravel 12+, AdminLTE 4 (Beta 2), Bootstrap 5, DomPDF (barryvdh/laravel-dompdf)
**Storage**: N/A (Ajustes de camada de apresentação e estilos de impressão)
**Testing**: PHPUnit / Pest / Testes manuais de interface e diálogo de impressão
**Target Platform**: Navegadores Web Modernos (Chrome, Edge, Firefox, Safari) e Dispositivos Móveis
**Project Type**: Web Application (Laravel Blade + AdminLTE v4)
**Performance Goals**: Impressão direta instantânea (< 500ms para abertura do diálogo de impressão)
**Constraints**: Fundo branco de alto contraste em impressão, zero páginas em branco decorrentes de overflow
**Scale/Scope**: Todas as telas de comprovante, relatórios e tela de login do sistema

---

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- [x] **Service Layer**: As operações de negócio permanecem isoladas na camada de serviço.
- [x] **Padrões de Código**: Strict types, PSR-12 e formatação limpa preservadas.
- [x] **Segurança**: Nenhuma credencial de teste hardcoded ou exposta ao usuário final.
- [x] **AdminLTE & UX**: Compatibilidade de impressão ajustada respeitando a estrutura do AdminLTE v4.

---

## Project Structure

### Documentation (this feature)

```text
specs/006-login-producao-impressao/
├── spec.md              # Especificação de requisitos (Fase anterior)
├── plan.md              # Plano de implementação técnica
├── research.md          # Diagnóstico técnico e decisões
├── data-model.md        # Modelos e entidades envolvidas
├── quickstart.md        # Roteiro de validação manual
├── contracts/           # Contratos de UI e Mídia Print
│   ├── login-contract.md
│   └── print-media-contract.md
└── checklists/
    └── requirements.md  # Checklist de qualidade de requisitos
```

### Source Code (affected paths)

```text
resources/
├── views/
│   ├── auth/
│   │   └── login.blade.php                 # Remoção de credenciais de teste e atalhos
│   ├── layouts/
│   │   └── app.blade.php                   # Inclusão de regras globais de reset de @media print
│   └── movements/
│       └── show.blade.php                  # Otimização de estilos de impressão do comprovante
└── css/
    └── responsive-custom.css               # Regras de mídia print para overflow e quebra de página
```

**Structure Decision**: Modificações concentradas nas views Blade e nos arquivos de folha de estilo (`responsive-custom.css` / `app.blade.php`), sem necessidade de novas migrações de banco de dados ou novos endpoints de API.

---

## Complexity Tracking

> Nenhuma violação à Constituição identificada. A complexidade do projeto é mantida mínima e eficiente.
