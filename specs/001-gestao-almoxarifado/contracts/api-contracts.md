# API & Web Interface Contracts: Gestão de Almoxarifado Central CCB

## 1. Rotas Web e Endpoints

| Método | URI | Name | Middleware / Permissão | Descrição |
|--------|-----|------|------------------------|-----------|
| `GET` | `/dashboard` | `dashboard` | `auth`, `can:view-dashboard` | Exibe o Dashboard principal com cartões de KPI |
| `GET` | `/movements` | `movements.index` | `auth`, `can:view-movements` | Listagem paginada de movimentações |
| `GET` | `/movements/create` | `movements.create` | `auth`, `can:create-movements` | Formulário dinâmico de nova saída/empréstimo |
| `POST` | `/movements` | `movements.store` | `auth`, `can:create-movements` | Lança e processa uma nova movimentação |
| `GET` | `/movements/{movement}` | `movements.show` | `auth`, `can:view-movements` | Detalhes e comprovante de uma movimentação |
| `POST` | `/movements/items/{item}/return` | `movements.items.return` | `auth`, `can:create-movements` | Processa a devolução parcial ou total de um item |
| `GET` | `/materials` | `materials.index` | `auth`, `can:view-materials` | Gestão e consulta de saldo de materiais |
| `POST` | `/materials` | `materials.store` | `auth`, `can:manage-materials` | Cadastro/edição de materiais |
| `GET` | `/beneficiaries` | `beneficiaries.index` | `auth`, `can:view-beneficiaries` | Gestão de beneficiários |
| `POST` | `/beneficiaries` | `beneficiaries.store` | `auth`, `can:manage-beneficiaries` | Cadastro de beneficiários |
| `GET` | `/destinations` | `destinations.index` | `auth`, `can:view-destinations` | Gestão de destinos (Casas de Oração) |
| `POST` | `/destinations` | `destinations.store` | `auth`, `can:manage-destinations` | Cadastro de destinos |

---

## 2. Payload do Formulário de Movimentação (`POST /movements`)

### `StoreMovementRequest` Rules:
```php
[
    'type' => ['required', 'string', Rule::enum(MovementType::class)],
    'beneficiary_id' => ['required', 'exists:beneficiaries,id'],
    'destination_id' => ['required', 'exists:destinations,id'],
    'notes' => ['nullable', 'string', 'max:1000'],
    'items' => ['required', 'array', 'min:1'],
    'items.*.material_id' => ['required', 'exists:materials,id'],
    'items.*.quantity' => ['required', 'integer', 'min:1'],
    'items.*.expected_return_date' => ['required_if:type,LOAN', 'nullable', 'date', 'after_or_equal:today'],
]
```

---

## 3. Payload do Formulário de Devolução (`POST /movements/items/{item}/return`)

### `ReturnItemRequest` Rules:
```php
[
    'quantity' => ['required', 'integer', 'min:1'],
    'notes' => ['nullable', 'string', 'max:500'],
]
```

---

## 4. Estrutura de Notificação Toastr & Flash Messages

Ao redirecionar após ações no Controller:
- Sucesso: `return redirect()->route('movements.index')->with('success', 'Movimentação realizada com sucesso!');`
- Erro: `return back()->with('error', 'Saldo insuficiente para o material selecionado.')->withInput();`
