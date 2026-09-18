---

description: "Lista de tarefas para correção de persistência e filtragem de materiais (Feature 018)"
---

# Tasks: Correção do Erro ao Salvar e Filtrar Material

**Input**: Documentos de design em `specs/018-fix-erro-salvar-material/`  
**Prerequisites**: `plan.md`, `spec.md`, `research.md`, `data-model.md`, `contracts/fix-salvar-material-contract.md`, `quickstart.md`  
**Organization**: Tarefas organizadas por fases e histórias de usuário (US1 - MVP, US2, US3, US4) para viabilizar execução e testes independentes.  

## Format: `[ID] [P?] [Story?] Description with file path`

- **[P]**: Executável em paralelo (arquivos distintos, sem dependências pendentes)
- **[Story]**: Mapeamento para histórias de usuário do `spec.md` (`[US1]`, `[US2]`, `[US3]`, `[US4]`)
- Arquivos com caminhos relativos ao repositório

---

## Phase 1: Setup (Ambiente & Validação Inicial)

**Purpose**: Verificação das variáveis de ambiente e integridade dos arquivos de migração

- [x] T001 Verificar a configuração de banco de dados e drivers ativos em `config/database.php` e `.env`
- [x] T002 [P] Validar a sintaxe e integridade da migração `database/migrations/2026_09_18_000001_add_notes_to_materials_table.php`

---

## Phase 2: Foundational (Alinhamento Estrutural do Banco de Dados)

**Purpose**: Sincronização do esquema estrutural da tabela `materials` para eliminar o erro SQL de coluna inexistente

**⚠️ CRITICAL**: Pré-requisito para evitar falhas de execução nas operações de persistência e filtragem

- [x] T003 [US3] Disponibilizar script DDL de execução imediata no phpMyAdmin (`ALTER TABLE materials ADD COLUMN notes TEXT NULL AFTER patrimony_code;`) em `specs/018-fix-erro-salvar-material/quickstart.md`
- [x] T004 [US3] Executar o comando de migração `php artisan migrate --force` para sincronizar bases conectadas via terminal

**Checkpoint**: Estrutura da tabela `materials` sincronizada contendo a coluna `notes`.

---

## Phase 3: User Story 1 - Cadastro e Atualização de Material com Sucesso (Priority: P1) 🎯 MVP

**Goal**: Assegurar que os operadores cadastrem e atualizem materiais com sucesso, recebendo confirmação visual e sem interrupções por erros 500.

**Independent Test**: Submeter o formulário de cadastro de material com e sem anotações de observação e verificar redirecionamento com mensagem de sucesso na listagem.

### Tests for User Story 1 ⚠️

- [x] T005 [P] [US1] Adicionar testes cobrindo persistência regular de materiais com e sem observações em `tests/Feature/MaterialResilienceTest.php` e `tests/Feature/MaterialObservationTest.php`

### Implementation for User Story 1

- [x] T006 [US1] Ajustar método `store()` em `app/Http/Controllers/MaterialController.php` garantindo tratamento limpo dos dados validados e sanitização do payload de imagem
- [x] T007 [US1] Ajustar método `update()` em `app/Http/Controllers/MaterialController.php` garantindo persistência sem conflitos e integridade do registro existente
- [x] T008 [US1] Validar consistência dos atributos de formulário e CSRF no modal de criação e edição em `resources/views/materials/index.blade.php`

**Checkpoint**: User Story 1 concluída e testável de forma independente (cadastro e edição funcionando sem erro 500).

---

## Phase 4: User Story 2 - Resiliência e Feedback Amigável em Falhas de Persistência (Priority: P2)

**Goal**: Proteger a aplicação contra telas brancas de erro 500 em caso de falhas inesperadas de infraestrutura/banco, preservando dados digitados com `withInput()` e exibindo alerta amigável.

**Independent Test**: Simular falha de conexão/persistência e constatar que o usuário é redirecionado com mensagem explicativa e campos preenchidos mantidos.

### Tests for User Story 2 ⚠️

- [x] T009 [P] [US2] Criar teste automatizado simulando falha de gravação e verificando preservação de input antigo e notificação de erro em `tests/Feature/MaterialResilienceTest.php`

### Implementation for User Story 2

- [x] T010 [US2] Adicionar bloco `try/catch (\Throwable)` no método `store()` de `app/Http/Controllers/MaterialController.php` com log de erro e retorno amigável com `withInput()`
- [x] T011 [US2] Adicionar bloco `try/catch (\Throwable)` no método `update()` de `app/Http/Controllers/MaterialController.php` com log de erro e retorno amigável com `withInput()`

**Checkpoint**: Aplicação blindada com tratamento defensivo em todas as operações de escrita de materiais.

---

## Phase 5: User Story 4 - Busca e Filtragem de Materiais sem Erro 500 (Priority: P2)

**Goal**: Assegurar que a busca textual no catálogo de materiais por qualquer termo (ex: "teste") execute com sucesso sem disparar erro HTTP 500, verificando a existência da coluna `notes` antes da consulta.

**Independent Test**: Realizar requisição GET em `/materials?search=teste` e verificar status 200 com renderização da listagem filtrada sem exceção SQL.

### Tests for User Story 4 ⚠️

- [x] T012 [P] [US4] Adicionar teste cobrindo pesquisa textual por termo em `MaterialResilienceTest.php` garantindo funcionamento sem falhas
### Implementation for User Story 4

- [x] T013 [US4] Blindar a cláusula de busca textual no método `index()` de `app/Http/Controllers/MaterialController.php` com a checagem dinâmica `Schema::hasColumn('materials', 'notes')`

**Checkpoint**: Busca textual do catálogo de materiais 100% resiliente e protegida contra erro 500.

---

## Phase 6: Polish & Validação Final

**Purpose**: Verificação completa de integridade e regressões

- [x] T014 [P] Executar a suíte de testes de materiais através de `php artisan test --filter=Material`
- [x] T015 Executar a suíte completa de testes do projeto via `php artisan test`
- [x] T016 Validar visualmente a pesquisa por `"teste"` no navegador e atualizar conformidade em `specs/018-fix-erro-salvar-material/quickstart.md`

---

## Dependencies & Execution Order

```mermaid
graph TD
    T001[T001: Checagem de Ambiente] --> T003[T003: DDL phpMyAdmin]
    T002[T002: Validação Migration] --> T004[T004: Executar Migrate]
    T003 --> T006[T006: Store MaterialController]
    T004 --> T006
    T005[T005: Testes US1] --> T006
    T006 --> T007[T007: Update MaterialController]
    T007 --> T008[T008: Validação Formulário Blade]
    T008 --> T010[T010: Try/Catch Store]
    T008 --> T011[T011: Try/Catch Update]
    T009[T009: Teste Resiliência] --> T010
    T010 --> T012[T012: Teste Busca Resiliente]
    T012 --> T013[T013: Schema Check em index()]
    T013 --> T014[T014: Testes de Materiais]
    T014 --> T015[T015: Suíte Completa]
    T015 --> T016[T016: Validação Final]
```

---

## Parallel Opportunities

- **T012 e T014**: Testes de busca e testes da suíte de materiais podem ser executados isoladamente.
- **T013**: Implementação no `MaterialController::index()`.
