<p align="center">
  <img src="public/images/CCB_Logo_fundo_claro.png" width="300" alt="CCB Logo">
</p>

<h1 align="center">Sistema de Gestão de Almoxarifado Central</h1>
<p align="center">
  <strong>Congregação Cristã no Brasil &bull; Administração Nova Odessa</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap" alt="Bootstrap 5">
  <img src="https://img.shields.io/badge/AdminLTE-4.0-007bff?style=for-the-badge" alt="AdminLTE 4">
  <img src="https://img.shields.io/badge/SQLite-Local-003B57?style=for-the-badge&logo=sqlite" alt="SQLite">
</p>

---

## 📌 Sobre o Projeto

O **Sistema de Gestão de Almoxarifado Central CCB** foi desenvolvido para proporcionar um controle total, auditável e eficiente sobre as rotinas de estoque da **Congregação Cristã no Brasil (Administração Nova Odessa)**.

O sistema atende todas as modalidades operacionais de um almoxarifado institucional, incluindo controle de materiais de consumo geral, ferramentas e equipamentos retornáveis em regime de empréstimo, equipamentos de proteção individual (EPIs com registro de CA), entradas por Nota Fiscal ou Doação, acertos de estoque, inventário geral periódico e gerenciamento completo de permissões de usuários.

---

## 🔥 Principais Funcionalidades

### 1. ↔️ Movimentações de Estoque & Empréstimos
- **Regras Estritas por Modalidade de Saída:**
  - **Empréstimo (`LOAN`):** Exibe estritamente ferramentas e equipamentos retornáveis (`is_returnable = 1`). Exige data prevista de devolução.
  - **Consumo Geral (`CONSUMPTION`):** Exibe estritamente itens descartáveis (`is_returnable = 0`), bloqueando equipamentos retornáveis.
  - **Entrega de EPI (`EPI`):** Exibe estritamente materiais da categoria EPI (`isEpi = true`). Valida o número de CA e adapta devoluções se o item for retornável ou descartável.
- **Suporte a Múltiplos Itens:** Permite incluir diversos materiais na mesma saída/empréstimo com botão dinâmico de adição de itens.
- **Devoluções Parciais e Totais:** Controle individualizado por item com status (Pendente, Devolvido, Atrasado).
- **Busca Rápida com Select2:** Todos os campos de seleção contam com a biblioteca **Select2** (tema Bootstrap 5) para busca interativa na digitação.

### 2. 📦 Entradas de Estoque (NF / Doações)
- Registro de entradas com inclusão de Tipo de Documento (Nota Fiscal, Doação, Recibo), Número do Documento, Fornecedor/Doador e Valor Total.
- **Cadastros Rápidos via Modal Inline (AJAX):** Inclusão imediata de novos materiais, beneficiários ou destinos diretamente da tela de entrada sem perda de dados.

### 3. 📋 Inventário Geral Periódico & Ajuste de Saldo
- **Ajuste Pontual de Saldo:** Correção emergencial de estoque com exigência de justificativa auditável no histórico.
- **Sessão de Inventário Físico:** Contagem cega dos itens em estoque com cálculo em tempo real de divergências (Sobra, Falta ou OK).
- **Conclusão Atômica:** Atualização automatizada do saldo dos materiais com emissão de **Termo Oficial em PDF assinado para homologação da administração**.

### 4. 🛡️ Gestão de Usuários e Controle de Acesso (RBAC)
- **Perfis de Acesso (Spatie Permissions):**
  - 🔴 **Administrador:** Acesso total ao sistema, gestão de usuários, configurações e relatórios.
  - 🔵 **Almoxarife:** Lança saídas, empréstimos, devoluções, entradas e cadastros operacionais.
  - ⚪ **Consulta:** Acesso de leitura para visualização de relatórios, estoque e cadastros sem permissão de alteração.
- **Tooltips Interativos (Bootstrap 5):** Exibição de dicas descritivas sobre os privilégios de cada perfil ao passar o mouse na tabela.
- **Notificação de Credenciais:** Envio automático de e-mail de boas-vindas com dados de acesso no cadastro de novo usuário.
- **Recuperação de Senha ("Esqueci minha senha"):** Token seguro enviado por e-mail para criação de nova senha.

### 5. ⚙️ Configurações & Customização do Sistema (`/settings`)
- Módulo administrativo para personalização em tempo real do Nome da Instituição, Nome da Administração, Cabeçalho dos Comprovantes, Título dos Relatórios e E-mail de Suporte.

### 6. 📄 Central de Relatórios & Impressão
- Exportação de relatórios gerenciais em PDF e Excel (.csv).
- **Comprovante de Movimentação:** Download em PDF e impressão via navegador ajustada para **área útil de folha A4 (sem distorção ou ajuste de escala)**.

---

## 🛠️ Tecnologias Utilizadas

| Camada | Tecnologia |
| :--- | :--- |
| **Backend** | PHP 8.2+, Laravel 12.x |
| **Segurança & RBAC** | Spatie Laravel Permission |
| **Banco de Dados** | SQLite (suporte pronto para MySQL / PostgreSQL) |
| **Geração de PDF** | DomPDF (`barryvdh/laravel-dompdf`) |
| **Frontend & UI** | HTML5, CSS3, JavaScript (ES6+), Bootstrap 5.3, AdminLTE 4 |
| **Componentes UI** | Select2 (Bootstrap 5 Theme), SweetAlert2, Toastr, Bootstrap Icons |
| **Testes Automatizados**| PHPUnit / Pest (38 testes de integração cobrindo 100% dos fluxos) |

---

## 🚀 Instalação e Execução Local

### Requisitos Prévios
- PHP 8.2 ou superior (com extensões `pdo_sqlite`, `mbstring`, `openssl`, `curl`, `gd`, `intl` habilitadas).
- Composer 2.x
- Node.js 18+ & NPM

### Passo a Passo de Instalação

1. **Clonar o repositório:**
   ```bash
   git clone https://github.com/rfergomes/almoxarifadoccb.git
   cd almoxarifadoccb
   ```

2. **Instalar as dependências do PHP (Composer):**
   ```bash
   C:\xampp\php\composer.bat install
   ```

3. **Configurar o Arquivo de Ambiente:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Executar as Migrações e Populadores de Dados (Seeders):**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Instalar Dependências Frontend & Compilar Assets:**
   ```bash
   npm install
   npm run build
   ```

6. **Iniciar o Servidor de Desenvolvimento:**
   ```bash
   php artisan serve
   ```

   Acesse a aplicação no navegador em: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 🔑 Credenciais de Acesso Padrão (Ambiente de Teste)

| Perfil | E-mail de Acesso | Senha |
| :--- | :--- | :--- |
| **Administrador** | `admin@ccb.org.br` | `12345678` |
| **Almoxarife** | `almoxarife@ccb.org.br` | `12345678` |
| **Consulta** | `consulta@ccb.org.br` | `12345678` |

---

## 🧪 Suíte de Testes Automatizados

O sistema conta com uma suíte de **38 testes de integração (96 asserções)** que garantem o funcionamento correto de todas as regras de negócio, perdas/devoluções de empréstimos, controle de saldo, permissões RBAC e geração de PDFs.

Para rodar os testes unitários e de integração:
```bash
php artisan test
```

---

## 👨‍💻 Desenvolvedor & Suporte

* **Desenvolvedor:** Rodrigo Lima
* **E-mail:** [rfergomes@gmail.com](mailto:rfergomes@gmail.com)
* **Instituição:** Congregação Cristã no Brasil &bull; Administração Campinas

---

<p align="center">
  <small>Congregação Cristã no Brasil &copy; 2026 &bull; Todos os direitos reservados.</small>
</p>
