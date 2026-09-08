# Interface Contracts: Prefixo de SKU CCB-###

## 1. Cadastro de Material
- **Rota**: `POST /materials`
- **Payload**:
  ```json
  {
    "code_sku": "", // Vazio ou nulo
    "name": "Cabo Coaxial RG6",
    "category_id": 1,
    "unit_measure": "M",
    "current_stock": 50,
    "minimum_stock": 10
  }
  ```
- **Resultado no Banco**:
  - `code_sku`: `"CCB-001"` (ou o próximo sequencial `CCB-002`, `CCB-010`, etc.).

## 2. Cadastro Rápido de Material (AJAX)
- **Rota**: `POST /api/quick-material`
- **Resposta Sucesso (201)**:
  ```json
  {
    "success": true,
    "message": "Material cadastrado com sucesso!",
    "data": {
      "id": 25,
      "code_sku": "CCB-001",
      "name": "Cabo Coaxial RG6",
      "label": "CCB-001 - Cabo Coaxial RG6 (Atual: 0 M)"
    }
  }
  ```
