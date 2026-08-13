# Data Model & Configuration Specifications: Responsividade Total & PWA

**Feature**: [`spec.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/spec.md) | **Branch**: `003-responsive-pwa` | **Date**: 2026-08-13

## Overview

A implementação de Responsividade e PWA não altera as tabelas relacionais do banco de dados MySQL (armazenadas via Eloquent), mas introduz esquemas de dados estáticos para o Manifesto PWA, definições de cache do Service Worker e regras de Breakpoint de UI.

---

## 1. Schema do Web App Manifest (`public/manifest.json`)

| Campo | Tipo | Valor / Descrição |
| :--- | :--- | :--- |
| `name` | String | "Sistema de Gestão de Almoxarifado Central CCB" |
| `short_name` | String | "Almoxarifado CCB" |
| `description` | String | "Sistema de Controle de Estoque, Empréstimos e EPIs da Congregação Cristã no Brasil" |
| `start_url` | String | "/" |
| `display` | String | "standalone" |
| `background_color` | String | "#f4f6f9" |
| `theme_color` | String | "#003b57" |
| `orientation` | String | "any" |
| `icons` | Array[Object] | Lista de ícones (192x192, 512x512, maskable) |

---

## 2. Estrutura do Cache do Service Worker (`public/sw.js`)

### Cache Storage Keys

- **`CACHE_NAME`**: `"ccb-almoxarifado-v1"`

### Pre-cached Core Assets

- `/` (Página principal / Dashboard)
- `/offline.html` (Página de contingência offline)
- `/images/CCB_Logo_fundo_claro.png`
- `/images/icons/icon-192x192.png`
- `/images/icons/icon-512x512.png`
- Build de Assets (Vite CSS / JS do AdminLTE e Bootstrap)

---

## 3. Especificação de Breakpoints e Ajustes CSS/UI

| Breakpoint | Faixa de Largura | Comportamento da Interface |
| :--- | :--- | :--- |
| **Extra Small (xs)** | `< 576px` | Sidebar oculta; tabelas em scroll horizontal; formulários em 1 coluna (col-12); botões com w-100. |
| **Small (sm)** | `≥ 576px` | Formulários em 1 a 2 colunas; modais centralizadas. |
| **Medium (md)** | `≥ 768px` | Sidebar recolhida por padrão em modo mini; formulários em 2 colunas. |
| **Large (lg)** | `≥ 992px` | Layout desktop padrão; sidebar expandida. |
