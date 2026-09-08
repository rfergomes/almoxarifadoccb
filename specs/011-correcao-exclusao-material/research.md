# Research: Correção do Fluxo de Exclusão de Material, Notificações e SKU Opcional com Sequencial

## Technical Context
- **Stack**: PHP 8.3 / Laravel 12 / Blade / AdminLTE 4 / Bootstrap 5 / SweetAlert2 / Toastr / PWA Service Worker (`sw.js`).
- **Target**: `resources/views/layouts/app.blade.php`, `public/sw.js`, `app/Http/Requests/StoreMaterialRequest.php`, `app/Models/Material.php`, `resources/views/materials/index.blade.php`, `resources/views/partials/modal_quick_material.blade.php`.

---

## Findings & Decisions

### 1. Carregamento de Toastr JS e CSS no Layout Principal
- **Problema**: O arquivo `resources/views/layouts/app.blade.php` carregava na linha 135:
  ```html
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"></script>
  ```
  Isso causava recusa do script pelo navegador (`Refused to execute script from '...toastr.min.css' because its MIME type ('text/css') is not executable`), fazendo com que `window.toastr` não ficasse disponível. Na linha seguinte (`partials.alerts`), `toastr.options = ...` disparava `Uncaught ReferenceError: toastr is not defined`, quebrando a execução do JavaScript na página e bloqueando a abertura do modal de confirmação SweetAlert2 ao clicar em "Excluir Material".
- **Decisão**: Corrigir a tag para carregar o arquivo JavaScript:
  ```html
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  ```
- **Alternativas consideradas**:
  - Empacotar toastr via Vite: Viável, porém o projeto já adota CDN padronizada para jQuery, Bootstrap, AdminLTE, Select2 e SweetAlert2 no layout principal. Manter o CDN com a URL correta `.min.js` restaura o funcionamento imediato com máxima compatibilidade.

---

### 2. Estabilidade do Service Worker (`public/sw.js`)
- **Problema**:
  1. `sw.js` intercepta todo evento `fetch` com método `GET`. Quando extensões do navegador (Chrome Extensions, DevTools) fazem requisições, o esquema é `chrome-extension://...`. A chamada `cache.put(event.request, ...)` falha fatalmente com: `TypeError: Failed to execute 'put' on 'Cache': Request scheme 'chrome-extension' is unsupported`.
  2. Chamadas a serviços externos (como beacons Cloudflare `https://static.cloudflareinsights.com/...`) que falham por CORS ou rede entram no bloco `.catch()` do Service Worker. Como não há match de cache e não é navegação HTML, a função async do catch retornava `undefined`. No Service Worker, retornar `undefined` para `event.respondWith()` resulta em `TypeError: Failed to convert value to 'Response'`.
- **Decisão**:
  - No Service Worker `fetch` listener:
    1. Filtrar o protocolo: ignorar requisições que não iniciem com `http://` ou `https://` (permitindo que o navegador gerencie extensões normalmente).
    2. Ignorar domínios de terceiros que não façam parte dos assets essenciais do aplicativo (ex: beacons ou telemetria como `static.cloudflareinsights.com`).
    3. Garantir que, se o `fetch` falhar e o recurso não estiver em cache, o Service Worker retorne uma resposta vazia ou deixe o erro de rede fluir normalmente sem produzir `Failed to convert value to 'Response'`.
- **Alternativas consideradas**:
  - Desabilitar o Service Worker: Indesejado, pois o projeto possui especificação de PWA e navegação offline para o almoxarifado.

---

### 3. Código SKU Opcional com Geração Automática Sequencial (`GEN-###`)
- **Problema**: O formulário exigia obrigatoriamente que o usuário digitasse um código SKU (`code_sku`). Em muitos materiais, o operador não possui SKU próprio e deseja que o sistema atribua um código genérico sequencial automático no formato `GEN-001`, `GEN-002`, etc.
- **Decisão**:
  1. Tornar `code_sku` em `StoreMaterialRequest` como `nullable`:
     ```php
     'code_sku' => ['nullable', 'string', 'max:50', 'unique:materials,code_sku'],
     ```
  2. No Model `Material`, implementar o método `generateNextSku(): string`:
     - Consulta os registros onde `code_sku` começa com `GEN-`.
     - Extrai o maior valor numérico existente via expressão regular (`/^GEN-(\d+)$/`).
     - Incrementa o maior número encontrado (ou inicia em 1 caso não haja nenhum).
     - Formata com preenchimento de zeros à esquerda com 3 dígitos mínimos (ex: `GEN-001`, `GEN-010`, `GEN-100`).
  3. No método `booted()` do Model `Material`, interceptar o evento `creating`:
     - Se `empty(trim((string) $material->code_sku))`, atribui `$material->code_sku = static::generateNextSku()`.
  4. Atualizar os formulários Blade:
     - `resources/views/materials/index.blade.php`: alterar o label para "Código SKU (Opcional)", remover o atributo `required` e adicionar placeholder explicativo `"Vazio para gerar automático (GEN-001)"`.
     - `resources/views/partials/modal_quick_material.blade.php`: mesma alteração visual e remoção de `required`.
- **Alternativas consideradas**:
  - Gerar no Controller: Centralizar no Model `booted()` garante que o SKU sequencial seja gerado tanto no cadastro principal quanto no cadastro rápido (`QuickRegistrationController`), em seeders e em importações.
