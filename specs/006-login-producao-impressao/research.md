# Phase 0: Research & Technical Analysis

**Feature**: Adequação da Tela de Login para Produção e Correção da Impressão Direta
**Branch**: `006-login-producao-impressao`
**Date**: 2026-09-02

## 1. Contexto & Diagnóstico Técnico

### 1.1. Tela de Autenticação (`resources/views/auth/login.blade.php`)
- **Problema**: O formulário de login continha valores padrão em hardcode (`admin@ccb.org.br` e `12345678`) nos atributos `value` dos campos de e-mail e senha, além de um bloco visual com botões `.btn-quick-login` e script associado para auto-preenchimento de contas de teste.
- **Solução Técnica**:
  - Limpar o valor padrão do e-mail para `value="{{ old('email') }}"`.
  - Limpar o valor do campo de senha para `value=""`.
  - Remover completamente o container HTML dos atalhos de teste (`.btn-quick-login`) e seu bloco de script JS.
  - Manter integridade do CSRF token, validação de erros (`$errors->any()`), alerta de sessão e link de recuperação de senha.

### 1.2. Impressão Direta no Navegador (`window.print()`) vs PDF
- **Problema**: Ao acionar `window.print()` (ex: comprovantes de movimentação em `movements/show.blade.php` ou inventários), a página sai em branco ou incompleta na caixa de diálogo do navegador.
- **Causa Raiz**:
  - O layout base (`layouts/app.blade.php`) utiliza as classes do AdminLTE v4 `layout-fixed sidebar-expand-lg` e a estrutura `.app-wrapper > .app-main`.
  - Em telas com `layout-fixed`, o AdminLTE aplica regras CSS como `height: 100vh; overflow: hidden;` ou `position: fixed` na raiz, impedindo que o mecanismo de impressão do navegador calcule a rolagem e o fluxo de renderização dos nós DOM, resultando em corte total (página em branco).
  - Em `movements/show.blade.php`, `@media print` oculta `.app-sidebar`, `.app-header`, etc., mas não reseta `.app-wrapper`, `html`, `body`, `.app-main`, `.table-responsive` para `overflow: visible !important; height: auto !important; position: static !important;`.
- **Solução Técnica**:
  - Criar regras globais de `@media print` no CSS do sistema (`responsive-custom.css` ou `app.blade.php`) que neutralizem todo e qualquer `overflow: hidden`, `height: 100vh`, `position: fixed` e transformações do AdminLTE v4 no momento da impressão.
  - Garantir que `.printable-receipt`, `.app-content`, `.container-fluid`, `main` e tabelas tenham `overflow: visible !important`, `height: auto !important`, `page-break-inside: avoid` e regras de quebra limpas (`break-inside: avoid`).
  - Otimizar o componente de impressão do comprovante em `movements/show.blade.php` para renderizar perfeitamente no cabeçalho institucional CCB, tabela de itens e bloco de assinaturas.

---

## 2. Decisões Técnicas & Justificativas

| Decisão | Alternativas Consideradas | Justificativa |
|---|---|---|
| **Limpeza Direta no Blade de Login** | Criar flag condicional no `.env` para exibir/ocultar atalhos | O sistema já está homologado e em produção. Manter atalhos no Blade mesmo condicionais adiciona complexidade desnecessária e risco de exposição acidental. |
| **Reset Global de `@media print` + Ajuste Específico de Views** | Abrir rota HTML separada para impressão popup | O uso de `@media print` aproveita a rota existente (`movements.show`) e o DOM já hidratado, sem requisição extra ao servidor, mantendo a experiência rápida e nativa do navegador. |
| **Preservação dos Controladores e Geração de PDF** | Refatorar bibliotecas de PDF | A exportação de PDF (via DomPDF) já está funcionando 100% corretamente e não deve sofrer alterações, garantindo estabilidade. |

---

## 3. Conformidade com a Constituição

- **Princípio I (Service Layer)**: As rotas continuam delegando fluxo sem regras em controllers.
- **Princípio II (Padrões PHP 8.3+)**: Padrões rigorosos mantidos.
- **Princípio V (Interface & AdminLTE)**: Aderência estrita ao AdminLTE v4 e Bootstrap 5, corrigindo conflitos de mídia print inerentes ao framework.
- **Segurança (Hardcoded Secrets)**: Elimina credenciais de teste expostas no código client-side.
