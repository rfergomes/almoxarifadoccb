# Research: Inclusão de Campo de Observação em Materiais

**Feature**: `017-campo-observacao-material`  
**Date**: 2026-09-18  
**Status**: Completed  

---

## 1. Nomenclatura e Estratégia de Armazenamento no Banco de Dados

### Decisão
Adicionar a coluna `notes` do tipo `text` (anulável) diretamente na tabela `materials` via migration incremental `add_notes_to_materials_table`.

### Racional
- **Consistência de vocabulário no ecossistema**: As tabelas existentes do sistema (`inventories`, `movements`, `entry_documents`) já padronizam `notes` como o nome do atributo de banco para observações e anotações contextuais.
- **Flexibilidade e extensão textual**: O tipo `text` no banco de dados permite armazenar notas técnicas curtas ou parágrafos extensos de especificações do fabricante sem truncamento rígido de 255 caracteres, aceitando formatações com quebras de linha.
- **Desempenho**: Ter `notes` na própria tabela `materials` preserva o padrão de consulta única nas listagens e formulários, sem exigir queries auxiliares ou tabelas secundárias.

### Alternativas Consideradas
- **Nomear como `observation` ou `observacao`**: Descartado em favor de `notes` para manter conformidade com as demais entidades do Almoxarifado CCB (`movements.notes`, `inventories.notes`, etc.).
- **Usar `varchar(255)`**: Descartado porque 255 caracteres costuma ser insuficiente para observações técnicas ou instruções completas de manuseio e estocagem.

---

## 2. Validação e Limites de Entrada de Dados

### Decisão
Estabelecer validação em `StoreMaterialRequest` e no método `update` de `MaterialController`:
- **Regras no Back-End**:
  `'notes' => ['nullable', 'string', 'max:2000']`
- **Atributo amigável**:
  `'notes' => 'observações'`
- **Comportamento para strings em branco**:
  Utilização do middleware nativo do Laravel (`ConvertEmptyStringsToNull`), convertendo automaticamente envios compostos apenas por espaços para `NULL`.

### Racional
- Proteger o banco de dados contra payloads desproporcionais, garantindo espaço de sobra para descrições detalhadas (até 2.000 caracteres, aproximadamente 300 a 400 palavras).
- Garantir que materiais sem observação fiquem com valor limpo (`null`), simplificando verificações em views e relatórios.

### Alternativas Consideradas
- **Não limitar caracteres (apenas `string`)**: Descartado para prevenir abusos acidentais ou degradação na renderização de telas e relatórios de impressão.

---

## 3. Experiência de Interface de Usuário (UI/UX)

### Decisão
1. **Formulários de Cadastro e Edição**:
   - Utilizar elemento `<textarea name="notes" rows="3" class="form-control">` com placeholder orientativo em:
     - Modal de inclusão de material (`#modalCreateMaterial`).
     - Modal de edição de material (`#modalEditMaterial`).
     - Modal de cadastro rápido durante entradas de estoque (`#modalQuickMaterial`).
   - No modal de edição, carregar o valor via JavaScript dinamicamente a partir do `data-notes` do botão de ação, com limpeza ao fechar o modal.
2. **Listagem e Consulta (Tabela de Materiais)**:
   - Na coluna do nome do material (`materials.index`), caso o item possua notas, exibir um ícone elegante de anotação com tooltip Bootstrap:
     `<i class="bi bi-chat-left-text-fill text-primary ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ Str::limit($mat->notes, 150) }}"></i>`
   - Opcionalmente, ao clicar no ícone ou no item, disponibilizar modal ou expansão para leitura na íntegra de textos mais longos com quebras de linha preservadas (`nl2br`).

### Racional
- Não polui a tabela com textos longos que quebrariam a diagramação compacta e o alinhamento das colunas.
- Permite leitura rápida via tooltip com mouse hover e visualização detalhada sem redirecionar de tela.

---

## 4. Conformidade com a Constituição do Projeto

### Avaliação de Princípios:
- **Princípio I (Service Layer)**: As operações básicas de catálogo seguem o padrão RESTful do `MaterialController`, mantendo a consistência dos serviços existentes (`MaterialImageService`, `AttachmentService`).
- **Princípio II (Rigor de Tipagem & PHP 8.3+)**: `declare(strict_types=1);`, tipagem forte em métodos e Models. Uso de `StoreMaterialRequest` para validação isolada.
- **Princípio V (AdminLTE v4 + Bootstrap 5)**: Uso consistente de classes de estilo Bootstrap 5, Tooltips nativos e SweetAlert2/Toastr para feedback.
