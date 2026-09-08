# Quickstart: Validação da Ordenação Alfabética de Materiais

**Feature**: [spec.md](spec.md) | **Branch**: `013-ordenar-materiais-nome`

---

## 1. Pré-requisitos

- Servidor Apache/MySQL ativo no XAMPP ou ambiente de teste configurado com SQLite em memória.
- PHP 8.3+ disponível em `C:\xampp\php\php.exe`.

---

## 2. Validação Automatizada (Testes de Feature)

Execute a suite de testes no terminal:

```powershell
C:\xampp\php\php.exe artisan test --filter=MaterialSkuAndDeletionFixTest
```

Ou a suite completa de testes de materiais:

```powershell
C:\xampp\php\php.exe artisan test
```

### Critério de Sucesso Automatizado
- O teste `test_materials_are_ordered_alphabetically_by_name` deve confirmar que materiais cadastrados em ordem aleatória (ex: "Zarcão", "Arame", "Martelo") são retornados estritamente na ordem alfabética ("Arame", "Martelo", "Zarcão").

---

## 3. Validação Manual no Navegador

1. Acesse `http://localhost/materials` ou navegue para o menu **Catálogo de Materiais & Saldo de Estoque**.
2. Observe a coluna **NOME DO MATERIAL**.
3. Verifique se os registros são exibidos iniciando com as primeiras letras do alfabeto (ex: "ARAME GALVANIZADO..." antes de "MARRETA...", que por sua vez vem antes de "MARTELO...").
4. Realize uma busca no campo de pesquisa ou selecione uma categoria específica: os resultados exibidos devem permanecer ordenados de A a Z.
