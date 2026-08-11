<!--
Sync Impact Report:
- Version change: Initial -> 1.0.0
- List of modified principles: Initialized principles for Almoxarifado Central CCB.
- Added sections: Core Principles, Requisitos Tecnológicos & Arquitetura, Processo de Desenvolvimento & Controle de Qualidade, Governance.
- Removed sections: N/A (template placeholders replaced).
- Follow-up TODOs: None.
-->

# Almoxarifado CCB Constitution

## Core Principles

### I. Camada de Serviços & POO Estrita (Service Layer Pattern)
Toda a lógica de negócio, manipulando movimentações de estoque, validações transacionais, saídas, empréstimos e devoluções, DEVE ser obrigatoriamente encapsulada em classes de Serviço (`StockService`, `LoanService`). Controllers e Models NÃO DEVEM conter regra de negócio complexa. Operações mutáveis de estoque DEVEM ser executadas dentro de transações de banco de dados (`DB::transaction`).

### II. Rigor de Tipagem & Padrões PHP 8.3+ / Laravel 12
O código PHP DEVE seguir estritamente a PSR-12, utilizar `declare(strict_types=1);`, tipagem forte em todos os argumentos e retornos de métodos, e Enums nativos para gerenciar tipos (`CONSUMPTION`, `EPI`, `LOAN`) e status (`OPEN`, `COMPLETED`, `PARTIALLY_RETURNED`, `OVERDUE`, `DELIVERED`, `RETURNED`, `PENDING_RETURN`). Validações de requisições DEVEM ser isoladas em classes `FormRequest`.

### III. Integridade Transacional e Rastreabilidade Completa de Estoque
Nenhuma saída ou empréstimo PODE reduzir o estoque abaixo de zero (`quantity <= current_stock`). Toda movimentação DEVE possuir rastreabilidade completa vinculando obrigatoriamente o responsável pelo lançamento (`user_id`), o beneficiário (`beneficiary_id`) e o destino (`destination_id`).

### IV. Gestão Especializada de EPIs e Empréstimos com Devolução
Materiais da categoria EPI EXIGEM o registro de Certificado de Aprovação (`ca_number`) e a verificação da validade (`ca_validity`). Empréstimos de ferramentas e equipamentos EXIGEM data prevista de devolução (`expected_return_date`) e DEVEM manter os itens vinculados ao beneficiário com status rastreável (`OVERDUE`, `PENDING_RETURN`) até a efetivação da devolução.

### V. Interface do Usuário Integrada e Experiência de Feedback
A interface gráfica DEVE ser construída sobre o template AdminLTE v4 com Bootstrap 5. Confirmações de ações críticas (baixas de estoque, devoluções, exclusões) DEVEM ser intermediadas via SweetAlert2. Notificações globais e alertas de sessão (`success`, `error`) DEVEM ser exibidos via componentes de notificação Toastr / AdminLTE.

## Requisitos Tecnológicos & Arquitetura

### 1. Stack Tecnológico Padrão
- **Back-End:** PHP 8.3+ / Laravel 12+ com MySQL/MariaDB (via XAMPP).
- **Controle de Acesso (RBAC):** `spatie/laravel-permission` definindo perfis `Administrador`, `Almoxarife` e `Consulta`.
- **Front-End & Assets:** AdminLTE v4 (`admin-lte@4.0.0`), Bootstrap 5, SweetAlert2 e Toastr.

### 2. Padrão de Modelagem e Dados
- Migrations DEVEM conter chaves estrangeiras com integridade referencial e índices apropriados para campos de busca rápida (`code_sku`, `code`, `status`, `type`).
- Models DEVEM explicitar seus relacionamentos Eloquent (`belongsTo`, `hasMany`) e usar enums nativos para casting de atributos.

## Processo de Desenvolvimento & Controle de Qualidade

### 1. Idioma e Formatação
- Todas as respostas, comentários de código, documentações e mensagens de commit DEVEM ser escritas em **Português do Brasil (pt-BR)**.
- Indentação: 4 espaços para PHP; 2 espaços para HTML, CSS, JavaScript, JSON, YAML e Markdown.

### 2. Segurança e Variáveis de Ambiente
- Nenhuma credencial, segredo ou chave de API DEVE ser hardcoded no código-fonte. Todas as configurações sensíveis DEVEM ser lidas via `.env`.

## Governance

### 1. Supremacia da Constituição
Esta Constituição rege todas as decisões arquiteturais, padrões de código e diretrizes de desenvolvimento do projeto Almoxarifado CCB. Revisões de código e Pull Requests DEVEM verificar a conformidade com estes princípios.

### 2. Emendas e Versionamento
- **MAJOR:** Remoção ou alteração incompatível de princípios constitucionais ou regras de integridade de estoque.
- **MINOR:** Adição de novos princípios, novos perfis de acesso ou novos módulos arquiteturais.
- **PATCH:** Esclarecimentos, correções gramaticais ou ajustes em descrições de diretrizes.

**Version**: 1.0.0 | **Ratified**: 2026-08-11 | **Last Amended**: 2026-08-11
