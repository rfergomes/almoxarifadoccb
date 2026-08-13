# Quickstart Validation Guide: Responsividade Total & PWA

**Feature**: [`spec.md`](file:///d:/xampp/htdocs/almoxarifadoccb/specs/003-responsive-pwa/spec.md) | **Branch**: `003-responsive-pwa` | **Date**: 2026-08-13

## Overview

Este guia descreve o procedimento para validar end-to-end a implementação de Responsividade e PWA no Almoxarifado Central CCB.

---

## 1. Validação de Responsividade (Mobile & Tablet)

### Passos de Teste

1. Abra o navegador Chrome/Edge e acesse o ambiente local ou de staging (`https://almoxarifado.sibem.top` ou `http://127.0.0.1:8000`).
2. Pressione `F12` para abrir as Ferramentas do Desenvolvedor (DevTools) e ative a barra de alternância de dispositivos móveis (`Ctrl + Shift + M`).
3. Selecione as seguintes resoluções de dispositivo:
   - **iPhone SE / Android pequeno (375px x 667px)**
   - **Tablet / iPad (768px x 1024px)**
4. Navegue pelas telas principais:
   - **Dashboard**: Verifique se os quadros de métricas e gráficos se reorganizam verticalmente sem barra de rolagem horizontal na página inteira.
   - **Saídas / Empréstimos (`/movements/create`)**: Verifique se os campos de seleção (Select2), quantidade e botões de ação se empilham em 1 coluna e possuem áreas de toque confortáveis.
   - **Listagens / Tabelas**: Verifique se as tabelas de estoque e empréstimos possuem barra de rolagem horizontal restrita ao contêiner da tabela (`.table-responsive`).

---

## 2. Validação PWA & Instalação

### Passos de Teste

1. No Chrome DevTools, acesse a aba **Application** (Aplicativo).
2. Clique na seção **Manifest**:
   - Verifique se o nome "Sistema de Gestão de Almoxarifado Central CCB" é detectado sem erros.
   - Verifique se os ícones `192x192` e `512x512` são carregados corretamente.
3. Clique na seção **Service Workers**:
   - Verifique se o `sw.js` está registrado e com status `Activated and running`.
4. No navegador (computador ou smartphone), verifique a presença do ícone de **Instalar App** na barra de endereço ou no menu do navegador.
5. Instale o aplicativo e abra-o a partir da tela inicial/desktop:
   - Confirme se a janela abre em modo `standalone` sem barras de endereço do navegador.

---

## 3. Validação de Funcionamento Offline

### Passos de Teste

1. Com a aplicação aberta, abra o DevTools -> aba **Network** (Rede).
2. Marque a opção **Offline**.
3. Recarregue a página (`F5`):
   - Verifique se os arquivos de estilo CSS, logotipos institucionais e scripts da interface continuam carregando do cache do Service Worker.
   - Verifique se a tentativa de submissão de formulário exibe o aviso explicativo de conectividade.
