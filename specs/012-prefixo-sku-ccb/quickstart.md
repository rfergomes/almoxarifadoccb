# Quickstart: Validação do Prefixo de SKU CCB-###

## Passos de Verificação
1. Acessar a tela de Materiais (`/materials`).
2. Clicar em **"Novo Material"**.
3. Conferir o placeholder do campo Código SKU: deve indicar `Vazio para gerar automático (CCB-001)`.
4. Deixar o campo Código SKU vazio e preencher os demais campos obrigatórios.
5. Salvar o material.
6. **Resultado Esperado**: O material deve ser exibido com o código `CCB-001` (ou o próximo sequencial `CCB-002`, etc.).
7. Na tela de Entradas (`/entries/create`), abrir o modal rápido de material e verificar o mesmo comportamento.
