# Pesquisa Técnica & Decisões Arquiteturais: Correção do Erro ao Salvar Material

**Feature**: `018-fix-erro-salvar-material`  
**Data**: 2026-09-18  
**Status**: Concluído  

---

## 1. Contexto do Problema & Diagnóstico da Falha

### 1.1 Sintoma Apresentado
Ao submeter o formulário de cadastro de novo material ou atualização de material existente através do sistema Almoxarifado CCB, o navegador exibe a mensagem de erro HTTP `500 | SERVER ERROR`.

### 1.2 Diagnóstico Técnico da Causa Raiz
Conforme evidenciado na inspeção visual via phpMyAdmin no banco de dados ativo (`sibemo33_almoxarifadoccb`):
- A tabela `materials` possui apenas 16 colunas: `id`, `code_sku`, `name`, `category_id`, `image_path`, `unit_measure`, `current_stock`, `minimum_stock`, `ca_number`, `ca_validity`, `expiration_date`, `patrimony_code`, `is_returnable`, `status`, `created_at`, `updated_at`.
- A coluna `notes` (introduzida na especificação `017-campo-observacao-material` através da migration `2026_09_18_000001_add_notes_to_materials_table.php`) **não existe** na tabela `materials` no banco MySQL em questão.
- Ao salvar um material, o `StoreMaterialRequest` valida os campos e envia a chave `notes` para o Eloquent (`Material::create($data)`).
- O driver MySQL tenta executar `INSERT INTO materials (..., notes) VALUES (...)`, gerando a exceção `PDOException: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'notes' in 'field list'`.
- Por se tratar de uma exceção de banco de dados não tratada no controller, o Laravel devolve uma resposta HTTP 500 com a página de erro padrão.

---

## 2. Decisões Técnicas

### Decisão 1: Sincronização Estrutural do Banco de Dados (DDL e Migrações)
- **Decisão**: Disponibilizar e documentar duas vias imediatas e seguras para alinhamento da tabela `materials`:
  1. **Via phpMyAdmin (SQL direto)**: Fornecer o comando DDL exato e idêntico à migration para execução direta na aba SQL do phpMyAdmin do banco `sibemo33_almoxarifadoccb`:
     ```sql
     ALTER TABLE `materials` ADD COLUMN `notes` TEXT NULL AFTER `patrimony_code`;
     ```
  2. **Via Artisan Migrate**: Para ambientes com terminal e acesso ao `.env` configurado para MySQL:
     ```bash
     php artisan migrate --force
     ```
- **Justificativa**: Em ambientes compartilhados de hospedagem cPanel/phpMyAdmin onde o terminal não está disponível ou o deploy é feito via FTP, a execução do comando SQL direto na interface web resolve a pendência em segundos sem necessidade de terminal.
- **Alternativas descartadas**:
  - *Remover o campo `notes` do código*: Descartado porque o campo de observações é um requisito essencial aprovado e entregue na feature 017.

### Decisão 2: Resiliência no Controller (`MaterialController`)
- **Decisão**: Implementar bloco `try/catch` com captura de `\Throwable` ou `\Illuminate\Database\QueryException` nos métodos `store` e `update` do `MaterialController`.
  - Em caso de falha de banco de dados, a aplicação deve:
    1. Registrar o log detalhado de erro (`Log::error(...)`).
    2. Redirecionar o usuário de volta com `withInput()` (evitando que o operador perca os dados digitados).
    3. Exibir uma notificação amigável (`with('error', 'Não foi possível salvar o material...')`).
- **Justificativa**: Garante resiliência e aderência aos princípios de usabilidade: mesmo que ocorra uma falha de infraestrutura ou banco, o usuário não perde o trabalho digitado e não recebe uma tela inerte de erro 500.
- **Alternativas descartadas**:
  - *Manter sem try-catch*: Deixa o sistema vulnerável a qualquer erro de conexão ou esquema, exibindo tela branca 500.

### Decisão 3: Verificação de Existência de Coluna ou Schema Check
- **Decisão**: Caso a coluna `notes` temporariamente não exista no banco (ex: ambiente com migração atrasada), o controller ou model pode opcionalmente verificar `Schema::hasColumn('materials', 'notes')` antes de tentar salvar o atributo, ou simplesmente manter o esquema do banco estritamente sincronizado com a migração aplicada. A abordagem principal deve ser a atualização imediata da tabela no MySQL, complementada pelo tratamento de exceção.
- **Justificativa**: A integridade referencial exige que o banco de dados reflita o código. A sincronização do DDL é a solução definitiva.

### Decisão 4: Blindagem da Consulta de Busca (`index`) com Schema Check Dinâmico
- **Decisão**: Ajustar a consulta de busca em `MaterialController::index()` para verificar se a coluna `notes` existe no banco ativo (`Schema::hasColumn('materials', 'notes')`) antes de adicionar a cláusula `orWhere('notes', 'like', "%{$search}%")`.
- **Justificativa**: Ao pesquisar por qualquer texto (ex: `"teste"`), se a coluna `notes` não existir no banco de dados ativo, o MySQL rejeita a query com erro de coluna inexistente em cláusula WHERE, quebrando a página com erro 500. Com a verificação dinâmica, a busca funciona perfeitamente por nome, SKU e patrimônio mesmo antes de rodar o comando DDL no phpMyAdmin.

---

## 3. Matriz de Compatibilidade

| Ambiente | Conexão Atual | Estado da Tabela `materials` | Ação Necessária |
|---|---|---|---|
| Local (SQLite) | `sqlite` (`database.sqlite`) | Coluna `notes` já aplicada via batch 8 | Manter testes íntegros |
| Hospedagem/XAMPP MySQL | `mysql` (`sibemo33_almoxarifadoccb`) | Coluna `notes` **ausente** | Executar `ALTER TABLE` via phpMyAdmin ou `artisan migrate` |
