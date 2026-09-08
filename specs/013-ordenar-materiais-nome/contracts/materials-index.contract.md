# Contract: GET /materials (Listagem de Materiais)

**Feature**: [spec.md](../spec.md) | **Branch**: `013-ordenar-materiais-nome`

---

## 1. Requisição HTTP

- **Método**: `GET`
- **Rota**: `/materials`
- **Controller**: `MaterialController@index`
- **Autenticação**: Requer sessão autenticada (middleware `auth`).

### Parâmetros de Query String (Opcionais)

| Parâmetro | Tipo | Descrição |
|---|---|---|
| `search` | string | Termo de pesquisa por Nome, SKU ou Patrimônio |
| `category_id` | integer | Filtro por ID da categoria |
| `expiration` | string | Filtro por validade (`expired`, `expiring_30`, etc.) |
| `has_patrimony`| integer | Filtro de patrimônio (`1` para com patrimônio, `0` para sem) |
| `page` | integer | Número da página solicitada (padrão: 1) |

---

## 2. Resposta

- **Status Code**: `200 OK`
- **View Renderizada**: `materials.index`
- **Variáveis Injetadas**:
  - `$materials`: Objeto `Illuminate\Pagination\LengthAwarePaginator` contendo instâncias de `Material` ordenadas por `name` em ordem ascendente (`ASC`).
  - `$categories`: Coleção de categorias para os filtros.
  - `$suppliers`: Coleção de fornecedores para o modal de cadastro.

---

## 3. Garantias do Contrato

1. A primeira página (`page=1`) DEVE listar os materiais em ordem alfabética ascendente por `name`.
2. Páginas subsequentes (`page=2, 3...`) DEVEM dar continuidade estrita à ordem alfabética iniciada na página anterior.
3. A aplicação de qualquer parâmetro de busca ou filtro DEVE preservar a ordenação alfabética ascendente nos registros resultantes.
