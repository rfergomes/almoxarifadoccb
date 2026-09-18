# Quickstart: Validação do Campo de Observação em Materiais

**Feature**: `017-campo-observacao-material`  
**Date**: 2026-09-18  

---

## 1. Preparação do Ambiente

1. **Executar a nova migration**:
   ```powershell
   C:\xampp\php\php.exe artisan migrate
   ```

2. **Limpar caches de configuração e view (se aplicável)**:
   ```powershell
   C:\xampp\php\php.exe artisan view:clear
   ```

---

## 2. Roteiro de Testes Manuais (End-to-End)

### Cenário 1: Cadastro de Material com Observação
1. Acesse o menu **Materiais** (`/materials`).
2. Clique no botão **"Novo Material"** para abrir o modal.
3. Preencha os campos obrigatórios (Nome: "Tinta Látex Fosca 18L", Categoria, Unidade: "LT", Estoque Mínimo: 2).
4. No campo **"Observações"**, digite:
   `Uso exclusivo para manutenção predial interna. Fornecedor: Tintas Coral.`
5. Clique em **Salvar Material**.
6. **Resultado Esperado**:
   - Mensagem de sucesso Toastr/AdminLTE.
   - O material é listado na tabela.
   - Um ícone de observação é exibido ao lado do nome do material; ao passar o mouse, o tooltip exibe o texto digitado.

### Cenário 2: Edição e Atualização da Observação
1. Na linha do material cadastrado no Cenário 1, clique no botão **Editar**.
2. Verifique se o campo "Observações" está devidamente preenchido com o texto digitado anteriormente.
3. Modifique o texto acrescentando uma nova instrução:
   `Uso exclusivo para manutenção predial interna. Fornecedor: Tintas Coral. Validade de 2 anos após aberto.`
4. Clique em **Salvar Alterações**.
5. **Resultado Esperado**:
   - Mensagem de sucesso.
   - Ao passar o cursor pelo ícone na tabela, a nova observação atualizada é exibida no tooltip.

### Cenário 3: Remoção da Observação na Edição
1. Clique em **Editar** no mesmo material.
2. Apague completamente o texto do campo "Observações" e salve.
3. **Resultado Esperado**:
   - Material atualizado com sucesso.
   - O ícone de anotação/tooltip não é mais exibido para esse material, mantendo a linha limpa.

### Cenário 4: Cadastro Rápido de Material em Nova Entrada
1. Acesse o menu **Entradas** -> **Nova Entrada** (`/entries/create`).
2. Clique em **"Cadastrar Novo Material Não Cadastrado"**.
3. Preencha os dados e inclua uma observação rápida no campo "Observações".
4. Clique em **Salvar e Selecionar**.
5. **Resultado Esperado**:
   - Mensagem de sucesso.
   - O material é criado, associado ao estoque com sua observação salva no banco de dados.

---

## 3. Testes Automatizados de Regressão

Executar a suíte de testes de materiais:
```powershell
C:\xampp\php\php.exe artisan test --filter=MaterialObservationTest
```
E validar testes gerais:
```powershell
C:\xampp\php\php.exe artisan test --filter=Material
```
