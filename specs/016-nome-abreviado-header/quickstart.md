# Quickstart: Validação da Formatação do Nome no Header

**Feature**: `016-nome-abreviado-header`
**Date**: 2026-09-08

## Pré-requisitos
- Servidor local rodando via Apache (XAMPP) ou PHP CLI (`php artisan serve`).
- Usuário cadastrado com nome composto (ex: "Rodrigo Fernando Gomes Lima").

## Cenários de Validação

### Cenário 1: Visualização em Tela Desktop (≥ 768px)
1. Acesse o sistema e faça login com um usuário com nome composto.
2. Olhe para o canto superior direito do cabeçalho.
3. **Resultado Esperado**: O nome é exibido no formato Primeiro e Último Nome (ex: `Rodrigo Lima`), acompanhado do avatar e do badge de perfil.

### Cenário 2: Visualização em Tela Mobile (< 768px)
1. Pressione `F12` no navegador e alterne para o modo de inspeção de dispositivo móvel (ou redimensione a janela para largura < 768px).
2. Verifique o cabeçalho superior.
3. **Resultado Esperado**: O nome é sintetizado automaticamente para apenas o Primeiro Nome (ex: `Rodrigo`), mantendo o layout limpo sem quebras.

### Cenário 3: Visualização do Nome Completo no Dropdown
1. Clique sobre o avatar/nome no cabeçalho.
2. O menu suspenso será aberto.
3. **Resultado Esperado**: O cabeçalho do dropdown exibe `Conectado como: Rodrigo Fernando Gomes Lima` de forma integral.

### Cenário 4: Testes Automatizados
Execute no terminal:
```bash
C:\xampp\php\php.exe artisan test --filter=UserProfileTest
```
**Resultado Esperado**: 100% dos testes aprovados com asserções cobrindo `first_name`, `short_name` e a renderização do header.
