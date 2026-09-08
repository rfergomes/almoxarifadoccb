# Quickstart: Validação da Foto de Perfil e Tela de Perfil

**Feature**: `015-foto-perfil-usuario`  
**Date**: 2026-09-08  

---

## 1. Preparação do Ambiente

1. **Executar a migration de avatar**:
   ```powershell
   C:\xampp\php\php.exe artisan migrate
   ```

2. **Garantir link do storage público**:
   ```powershell
   C:\xampp\php\php.exe artisan storage:link
   ```

---

## 2. Roteiro de Validação End-to-End

### Cenário 1: Acesso à Página de Perfil
1. Faça login na aplicação com qualquer usuário (ex: `admin@ccb.org.br`).
2. No canto superior direito do header, clique sobre o nome/avatar do usuário.
3. Clique em **"Meu Perfil"** no menu dropdown.
4. **Resultado Esperado**:
   - A página `/profile` é aberta exibindo o card visual do usuário com suas informações, badge de perfil e formulários de edição.

### Cenário 2: Atualização de Nome e Upload de Foto
1. Na página de Perfil, altere o campo **Nome Completo** (ex: adicione um sobrenome ou corrija a acentuação).
2. No campo de foto, selecione uma imagem válida (`.png`, `.jpg` ou `.webp` menor que 5MB).
3. Observe o preview instantâneo da nova foto na tela.
4. Clique em **"Salvar Alterações"**.
5. **Resultado Esperado**:
   - Notificação de sucesso exibida.
   - A foto de perfil e o nome são atualizados imediatamente no card de perfil e no header superior da aplicação.
   - O arquivo físico existe no diretório `storage/app/public/avatars/`.

### Cenário 3: Validação de Formato Inválido
1. Na tela de perfil, tente selecionar um arquivo `.pdf` ou executável.
2. Tente salvar.
3. **Resultado Esperado**:
   - O sistema bloqueia com erro explicativo: *"O arquivo enviado deve ser uma imagem válida"*.

### Cenário 4: Remoção de Foto de Perfil
1. No perfil de um usuário com foto cadastrada, marque a opção **"Remover foto de perfil"** e salve.
2. **Resultado Esperado**:
   - O avatar volta a exibir o ícone/iniciais padrão no perfil e no header.
   - O arquivo físico antigo é excluído do disco.

### Cenário 5: Exibição na Tabela de Usuários
1. Como Administrador, acesse o menu **Usuários do Sistema** (`/users`).
2. **Resultado Esperado**:
   - Os usuários com foto exibem seus respectivos avatares circulares ao lado do nome.
   - Os usuários sem foto exibem avatar neutro elegante.

---

## 3. Testes Automatizados

Executar a suíte de testes de perfil e usuários:
```powershell
C:\xampp\php\php.exe artisan test --filter=UserProfileTest
```
