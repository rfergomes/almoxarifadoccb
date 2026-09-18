# Guia Rápido de Validação (Quickstart): Correção do Erro ao Salvar Material

**Feature**: `018-fix-erro-salvar-material`  
**Data**: 2026-09-18  

---

## 1. Passo a Passo para Sincronização no phpMyAdmin

Como o banco ativo evidenciado na captura de tela é o MySQL `sibemo33_almoxarifadoccb`, siga os passos abaixo para adicionar a coluna que está causando o erro 500:

1. Abra o **phpMyAdmin** no navegador.
2. Selecione o banco de dados **`sibemo33_almoxarifadoccb`** na coluna lateral esquerda.
3. Clique na aba **SQL** no menu superior.
4. Cole o comando SQL abaixo e clique no botão **Executar**:

```sql
ALTER TABLE `materials` ADD COLUMN `notes` TEXT NULL AFTER `patrimony_code`;
```

5. Verifique se a mensagem *"A consulta SQL foi executada com êxito"* é exibida.
6. Clique na tabela **materials** e vá na aba **Estrutura**: a coluna `notes` agora aparecerá como a 13ª coluna (entre `patrimony_code` e `is_returnable`).

---

## 2. Passo a Passo Alternativo via Terminal (Artisan Migrate)

Caso o terminal do servidor ou ambiente local com MySQL esteja disponível:

```bash
# Executa as migrations pendentes no banco de dados configurado no .env
php artisan migrate --force
```

---

## 3. Validação Funcional no Navegador

Após sincronizar a estrutura do banco:

1. Acesse o sistema e navegue até a tela de **Materiais** (`/materials`).
2. Clique no botão **"Novo Material"** para abrir o modal de cadastro.
3. Preencha os campos obrigatórios:
   - **Nome**: `Material de Teste Validação`
   - **Categoria**: Selecione qualquer categoria disponível.
   - **Unidade de Medida**: `UN`
   - **Estoque Inicial**: `10`
   - **Estoque Mínimo**: `2`
   - **Observações**: `Anotação de teste para validação de salvamento sem erro 500.`
4. Clique em **Salvar**.
5. **Resultado Esperado**:
   - O modal se fecha.
   - Uma notificação verde de sucesso é exibida: *"Material cadastrado com sucesso!"*.
   - A página de erro 500 **não** aparece.
   - O item consta na listagem com o ícone de observação e seu tooltip informativo.

---

## 4. Validação da Busca e Filtragem no Navegador

1. No catálogo de materiais (`/materials`), localize o campo de pesquisa textual no topo da tabela.
2. Digite o termo de teste (ex: `teste`).
3. **Resultado Esperado**:
   - A página recarrega aplicando o filtro de busca sem apresentar tela de erro 500.
   - Caso existam itens correspondentes, eles são exibidos; caso contrário, é exibida a mensagem de que nenhum registro foi encontrado.

---

## 5. Execução de Testes Automatizados

Para certificar que nenhuma regressão foi introduzida:

```bash
# Executa a suíte de testes de materiais e cadastros
php artisan test --filter=Material
```
