# Contrato de Interface & Operações: Persistência de Materiais

**Feature**: `018-fix-erro-salvar-material`  
**Data**: 2026-09-18  

---

## 1. Endpoints de Persistência

### 1.1 Criar Novo Material
- **Método**: `POST`
- **Rota**: `/materials` (`route('materials.store')`)
- **Content-Type**: `multipart/form-data`
- **Autorização**: Usuário autenticado com permissão `manage-materials`

#### Payload de Requisição (Form Data)
```json
{
  "code_sku": "CCB-042",          // Opcional: string (máx 50) ou vazio para auto-gerar
  "name": "Tinta Acrílica Branca",// Obrigatório: string (máx 150)
  "category_id": 2,               // Obrigatório: inteiro, existe em categories.id
  "unit_measure": "UN",           // Obrigatório: string (máx 10)
  "current_stock": 10,            // Obrigatório: inteiro (min 0)
  "minimum_stock": 5,             // Obrigatório: inteiro (min 0)
  "is_returnable": 0,             // Opcional: boolean (0 ou 1)
  "expiration_date": "2027-12-31",// Opcional: date (YYYY-MM-DD)
  "patrimony_code": null,         // Opcional: string (máx 50)
  "notes": "Armazenar em local seco e arejado.", // Opcional: string (máx 2000)
  "ca_number": null,              // Opcional: string (máx 50)
  "ca_validity": null,            // Opcional: date (YYYY-MM-DD)
  "image": null                   // Opcional: file (imagem max 5MB)
}
```

#### Respostas
- **Sucesso (HTTP 302 Redirect)**:
  - Redireciona para: `route('materials.index')`
  - Flash Session: `['success' => 'Material cadastrado com sucesso!']`
- **Erro de Validação (HTTP 302 Redirect)**:
  - Redireciona de volta com erros de validação e `old()` inputs.
- **Falha de Persistência no Banco (HTTP 302 Redirect Tratado)**:
  - Redireciona de volta com `withInput()`
  - Flash Session: `['error' => 'Ocorreu um erro ao salvar o material. Por favor, tente novamente ou contate o suporte.']`
  - Registro de log: `Log::error(...)` com a stack trace detalhada da exceção.

---

### 1.2 Atualizar Material Existente
- **Método**: `PUT` / `PATCH`
- **Rota**: `/materials/{material}` (`route('materials.update', $material)`)
- **Content-Type**: `multipart/form-data`
- **Autorização**: Usuário autenticado com permissão `manage-materials`

#### Payload de Requisição (Form Data)
Mesmos campos cadastrais do cadastro, com exceção de `current_stock` (que é gerenciado via inventário/movimentação) e adição opcional de `remove_image`.

#### Respostas
- **Sucesso (HTTP 302 Redirect)**:
  - Redireciona para: `route('materials.index')`
  - Flash Session: `['success' => 'Cadastro do material atualizado com sucesso! (Estoque inalterado)']`
- **Falha de Persistência no Banco (HTTP 302 Redirect Tratado)**:
  - Redireciona de volta com `withInput()`
  - Flash Session: `['error' => 'Ocorreu um erro ao atualizar o material.']`
