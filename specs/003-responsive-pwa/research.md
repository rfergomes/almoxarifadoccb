# Phase 0 Research: Responsividade Total & PWA (Progressive Web App)

**Feature**: [`spec.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/spec.md) | **Branch**: `003-responsive-pwa` | **Date**: 2026-08-13

## Executive Summary

Esta pesquisa estabelece as decisões técnicas para transformar o sistema Almoxarifado Central CCB em um aplicativo totalmente responsivo e PWA (Progressive Web App) instalável. A aplicação utiliza PHP 8.3+ / Laravel 12 com AdminLTE v4 e Bootstrap 5.

---

## Technical Unknowns & Decisions

### 1. PWA Web App Manifest & Branding Institucional

- **Decision**: Criar o arquivo `public/manifest.json` com especificações do W3C Web App Manifest e registrar no cabeçalho das páginas Blade (`resources/views/layouts/app.blade.php`).
- **Rationale**: Permite a instalação como aplicativo standalone em Android, Windows, macOS e suporte via meta tags dedicadas para iOS (Apple Touch Icons & Standalone Mode).
- **Iconografia**: Incluir ícones institucionais da CCB nos tamanhos 192x192px, 512x512px e maskable icons para Android.

---

### 2. Estratégia de Cache e Service Worker (`sw.js`)

- **Decision**: Implementar um Service Worker nativo em `public/sw.js` utilizando uma estratégia híbrida:
  - **Cache First (Stale-While-Revalidate)** para assets estáticos (CSS, JS do AdminLTE, Bootstrap, Select2, fontes, logotipos institucionais).
  - **Network First com Fallback Offline** para páginas dinâmicas e formulários de lançamento.
- **Rationale**: Conforme estipulado no Princípio III da Constituição, movimentações mutáveis de estoque (baixas, empréstimos, entradas) exigem validação de banco transacional no servidor em tempo real. O Service Worker garante carregamento instantâneo da UI e fornece uma página de fallback amigável (`/offline.html`) quando a conexão for interrompida, evitando quebras de estoque por submissões offline não sincronizadas.

---

### 3. Responsividade no AdminLTE v4 & Bootstrap 5 (Tabelas, Modais e Select2)

- **Decision**:
  - **Sidebar AdminLTE v4**: Configurar a sidebar para colapsar automaticamente em telas menores que 768px (`sidebar-collapse` / `sidebar-mini`).
  - **Tabelas de Estoque e Relatórios**: Aplicar contêineres `.table-responsive` com barras de rolagem touch e visualização otimizada de ações em telas pequenas.
  - **Formulários e Modais de Cadastro Inline**: Garantir que as colunas do Bootstrap se empilhem em tela única (`col-12 col-md-6`) e que botões de ação e Select2 ocupem 100% da largura útil (`w-100`) com altura mínima de clique de 44px.

---

### 4. Suporte a iOS e Safari (Apple Touch Tags)

- **Decision**: Incluir meta tags específicas no `<head>` do `app.blade.php`:
  - `<meta name="apple-mobile-web-app-capable" content="yes">`
  - `<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">`
  - `<meta name="apple-mobile-web-app-title" content="Almoxarifado CCB">`
  - `<link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">`
- **Rationale**: Garante suporte completo para instalação na Tela de Início do iOS/iPadOS sem barras de navegação do Safari.
