# Research: Foto de Perfil do Usuário e Página de Perfil

**Feature**: `015-foto-perfil-usuario`  
**Date**: 2026-09-08  
**Status**: Completed  

---

## 1. Decisões de Arquitetura e Armazenamento

### Decisão
Adicionar a coluna `avatar_path` (nullable string) diretamente na tabela `users` através da migration `add_avatar_path_to_users_table`. Os arquivos de foto/avatar serão armazenados no disco `public` do Laravel sob o diretório `storage/app/public/avatars/{uuid}.{ext}`.

### Racional
- **Acesso direto e alta performance**: Como o avatar do usuário logado é carregado em todas as páginas da aplicação no header (`navbar.blade.php`), ter `avatar_path` diretamente no modelo `User` evita consultas ou joins adicionais em toda requisição autenticada.
- **Link público**: Utiliza o link simbólico `storage` já existente (`/storage/avatars/...`), garantindo entrega estática imediata pelo servidor web.
- **Isolamento de arquivos**: Pasta dedicada `avatars/` separada de anexos de movimentações e fotos de materiais de almoxarifado.

---

## 2. Camada de Serviço (Service Layer Pattern)

### Decisão
Criar o serviço dedicado `App\Services\UserAvatarService` para centralizar a manipulação física das imagens de usuário:
- `uploadAvatar(UploadedFile $file): string`: gera UUID único, salva na pasta `avatars` no disco público e retorna o caminho.
- `deleteAvatar(?string $path): bool`: remove com segurança o arquivo físico caso exista.
- `replaceAvatar(UploadedFile $file, ?string $oldPath): string`: remove o avatar antigo e faz upload do novo.

### Racional
Atende estritamente ao **Princípio I** da Constituição do projeto, mantendo os controllers (`ProfileController`, `UserController`) livres de manipulação direta do sistema de arquivos e garantindo consistência na remoção de arquivos órfãos.

---

## 3. Experiência de Usuário (UI/UX) e Telas Impactadas

### Decisão
1. **Header Principal (`partials/navbar.blade.php`)**:
   - Substituir o ícone genérico `bi bi-person-circle` pelo avatar circular (32x32px com borda suave, `object-fit: cover` e `rounded-circle`).
   - Caso o usuário não possua foto, exibir um avatar elegante com as iniciais ou ícone SVG refinado.
   - Adicionar ao menu dropdown a opção **"Meu Perfil"** com ícone `bi bi-person-gear` e divisor visual antes do botão de logout.
2. **Tabela de Usuários (`users/index.blade.php`)**:
   - Substituir o ícone azul genérico na coluna "Nome do Usuário" pelo avatar circular real de cada operador/administrador cadastrado (32x32px), com placeholder neutro para quem não possui foto.
3. **Página de Perfil (`/profile`)**:
   - Criar uma interface dedicada com design moderno no padrão AdminLTE v4 + Bootstrap 5.
   - **Card de Perfil**: avatar em destaque (110x110px), nome em tipografia limpa, e-mail, badge do perfil principal e data de adesão.
   - **Formulário de Dados Pessoais**: campo para correção do nome, input de foto de perfil com preview dinâmico em tempo real e opção de remoção da foto existente.
   - **Formulário de Segurança**: alteração segura de senha de acesso com verificação da senha atual, nova senha e confirmação.

---

## 4. Validação Estrita de Arquivos

### Decisão
- Regra de validação: `'avatar' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120']`.
- Limite máximo: 5MB.
- Validação real de MIME type com mensagens amigáveis em português (`pt-BR`).
