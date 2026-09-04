# Quickstart: Validação de Navegação entre Entradas e Saídas

**Feature**: `009-ajuste-navegacao-movimentacoes`  
**Date**: 2026-09-04  

---

## Procedimento de Validação Rápida

### Cenário 1: Visualização de Detalhes de uma Entrada
1. Acesse o sistema e navegue até o menu lateral **Entradas** (`/entries`).
2. Localize um lançamento de entrada existente (ex: `ENT-20260904-FB9C`).
3. Clique no botão **Detalhes**.
4. **Resultado Esperado**:
   - O comprovante da movimentação é exibido.
   - O menu lateral **Entradas** está destacado como ativo (`class="nav-link active"`).
   - O menu **Saídas** NÃO está ativo.
5. Clique no botão **← Voltar** no final do comprovante.
6. **Resultado Esperado**:
   - O usuário é levado de volta à página de **Entradas** (`/entries`), mantendo o fluxo contínuo.

---

### Cenário 2: Visualização de Detalhes de uma Saída / Empréstimo
1. Acesse o menu lateral **Saídas** (`/movements`).
2. **Resultado Esperado**:
   - A listagem exibe apenas saídas e empréstimos.
   - Nenhum item com código `ENT-...` ou badge "Entrada de Estoque" aparece na tabela.
3. Clique em **Detalhes** de uma saída/empréstimo.
4. **Resultado Esperado**:
   - O menu lateral **Saídas** permanece ativo.
   - Ao clicar em **← Voltar**, o usuário retorna à lista de **Saídas** (`/movements`).
