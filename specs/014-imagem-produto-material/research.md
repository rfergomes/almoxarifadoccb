# Research: Inclusão e Visualização de Imagem no Cadastro de Materiais

**Feature**: `014-imagem-produto-material`  
**Date**: 2026-09-08  
**Status**: Completed  

---

## 1. Estratégia de Armazenamento da Imagem

### Decisão
Adicionar a coluna `image_path` (string, nullable) diretamente na tabela `materials` através de uma migration dedicada (`add_image_path_to_materials_table`), armazenando os arquivos físicos no disco `public` do Laravel em `storage/app/public/materials/images/{uuid}.{ext}`.

### Racional
- **Desempenho**: Ter a coluna `image_path` diretamente no modelo `Material` evita joins ou queries adicionais polimórficas (como ocorria em `attachments`) durante a listagem e paginação do catálogo de materiais.
- **Isolamento semântico**: O sistema já utiliza a tabela `attachments` para anexar comprovantes de ajuste de estoque/inventário (laudos, PDFs, relatórios de avaria). A foto do produto possui ciclo de vida e semântica de catálogo de produto, sendo 1-para-1 com o material.
- **Acesso direto e URLs**: Com o link simbólico `storage` já existente no Laravel (`php artisan storage:link`), os arquivos são servidos de forma rápida através do accessor `$material->image_url`.

### Alternativas Consideradas
- **Utilizar a tabela polimórfica `attachments`**: Descartado porque a tabela `attachments` não possui coluna de tipo/categoria, misturando documentos fiscais e laudos de inventário com a foto oficial do catálogo, além de onerar a query de listagem com relacionamentos polimórficos.

---

## 2. Validação Estrita de Arquivos (Apenas Imagens)

### Decisão
Implementar regras estritas de validação no Laravel via `FormRequest` e no front-end via atributo `accept`:
- **Regras no Back-End**:
  `'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120']`
  (máximo de 5MB, validação real de MIME type e assinatura de imagem pelo PHP FileInfo).
- **Atributo no Front-End**:
  `<input type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif" class="form-control">`
- **Mensagens de Validação**:
  Mensagens claras e amigáveis em português (`pt-BR`) informando que apenas arquivos de imagem são aceitos e com limite de 5MB.

### Racional
Garante total segurança contra uploads maliciosos (PDFs com scripts, executáveis renomeados) e previne consumo excessivo de disco.

---

## 3. Padrão de Arquitetura e Camada de Serviço

### Decisão
Criar a classe de serviço `App\Services\MaterialImageService` para gerenciar:
1. `uploadImage(UploadedFile $file): string`: gera UUID único, armazena no subdiretório `materials/images` e retorna o caminho relativo do arquivo.
2. `deleteImage(?string $path): bool`: remove o arquivo físico do disco `public` com segurança caso ele exista.
3. `replaceImage(UploadedFile $file, ?string $oldPath): string`: remove a imagem anterior (se existir) e armazena a nova.

### Racional
Atende estritamente ao **Princípio I (Service Layer Pattern)** da Constituição do projeto Almoxarifado CCB, mantendo o `MaterialController` limpo e focado no fluxo HTTP/redirecionamento.

---

## 4. Experiência de Interface (UI/UX) e Visualização Ampliada

### Decisão
1. **Tabela de Materiais**:
   - Inserção de uma coluna "Foto" logo após ou antes do SKU.
   - Miniatura de 45x45px com `object-fit: cover`, bordas arredondadas (`rounded`), sombra sutil e cursor em ponteiro (`cursor: pointer`).
   - Para materiais sem foto cadastrada, exibição de um placeholder neutro padronizado com ícone Bootstrap `<i class="bi bi-image text-muted"></i>`.
2. **Visualização Ampliada (Modal)**:
   - Ao clicar na miniatura ou em ação rápida de visualização, abrir o modal Bootstrap 5 `#modalImagePreview` (ou modal dedicado responsivo), apresentando:
     - Título com o Nome e SKU do Material.
     - Imagem em tamanho grande com proporção original preservada (`max-height: 75vh; object-fit: contain`).
     - Botão para fechar ou tecla ESC.
3. **Formulários de Cadastro e Edição**:
   - Atualização das tags `<form>` para incluir `enctype="multipart/form-data"`.
   - Adição do campo de upload de imagem com preview dinâmico em JavaScript antes do envio.
   - Na edição, exibir miniatura da imagem atual com opção de substituição ou checkbox/botão para "Remover Imagem".
