# Quickstart: Validação da Imagem no Cadastro de Materiais

**Feature**: `014-imagem-produto-material`  
**Date**: 2026-09-08  

---

## 1. Pré-requisitos e Preparação do Ambiente

1. **Garantir que o storage público esteja linkado**:
   ```powershell
   C:\xampp\php\php.exe artisan storage:link
   ```

2. **Executar as migrations**:
   ```powershell
   C:\xampp\php\php.exe artisan migrate
   ```

3. **Iniciar o servidor XAMPP / Laravel (se necessário)**:
   Acesse a aplicação em `http://localhost/almoxarifadoccb/public` ou via `artisan serve`.

---

## 2. Roteiro de Validação End-to-End

### Cenário 1: Cadastro de Material com Imagem Válida
1. Acesse o menu **Materiais** (`/materials`).
2. Clique no botão **"Novo Material"**.
3. Preencha os campos obrigatórios (Nome, Categoria, Unidade, Estoque Mínimo).
4. No campo **"Imagem do Material"**, selecione um arquivo de imagem (`.png`, `.jpg` ou `.webp` menor que 5MB).
5. Clique em **Salvar Material**.
6. **Resultado Esperado**:
   - Mensagem de sucesso Toastr/AdminLTE.
   - O material aparece na tabela exibindo a miniatura da foto cadastrada.
   - O arquivo físico existe no diretório `storage/app/public/materials/images/`.

### Cenário 2: Validação de Formato Inválido (Apenas Imagens)
1. Abra novamente o modal de **Novo Material**.
2. Tente selecionar um arquivo `.pdf`, `.docx` ou `.exe`.
3. Submeta o formulário.
4. **Resultado Esperado**:
   - O sistema bloqueia o envio com mensagem de validação explicativa ("O arquivo deve ser uma imagem válida nos formatos jpeg, png, jpg, webp ou gif").
   - Nenhum arquivo é gravado no storage.

### Cenário 3: Exibição de Miniatura e Placeholder
1. Cadastre um material **sem** selecionar nenhuma imagem.
2. Na tabela de materiais, localize a linha deste novo item.
3. **Resultado Esperado**:
   - O item sem foto exibe um placeholder visual elegante com ícone neutro (sem quebra de layout).
   - O item com foto cadastrada no Cenário 1 exibe sua respectiva miniatura enquadrada proporcionalmente.

### Cenário 4: Visualização Ampliada (Modal)
1. Na tabela de materiais, clique sobre a miniatura do material cadastrado com foto no Cenário 1.
2. **Resultado Esperado**:
   - O modal de visualização abre suavemente.
   - A imagem é exibida em tamanho expandido e nítido, acompanhada pelo nome e SKU do produto.
   - Ao pressionar `ESC` ou clicar no botão "Fechar", o modal se encerra sem alterar a posição de rolagem da página.

### Cenário 5: Edição, Substituição e Remoção de Imagem
1. Clique em **Editar** no material com foto.
2. Verifique se o formulário exibe o preview da imagem atual.
3. Marque a opção de remover imagem (ou faça upload de uma nova imagem) e salve.
4. **Resultado Esperado**:
   - Caso removida: a miniatura na tabela volta a ser o placeholder e a imagem antiga é apagada do disco.
   - Caso substituída: a nova miniatura é exibida e o arquivo anterior é removido do disco.

---

## 3. Testes Automatizados

Execução dos testes automatizados de materiais e anexos:
```powershell
C:\xampp\php\php.exe artisan test --filter=MaterialTest
```
