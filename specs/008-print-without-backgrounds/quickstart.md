# Quickstart: Validação da Impressão sem Gráficos de Segundo Plano

## Procedimento de Teste

1. **Acessar a movimentação**:
   - Abra a página `/movements/1` no navegador Chrome ou Edge.
2. **Abrir diálogo de impressão**:
   - Clique no botão "Imprimir Comprovante" (ou pressione `Ctrl + P`).
3. **Cenário 1: "Gráficos de segundo plano" Marcado**:
   - Na seção "Mais definições", marque a opção "Gráficos de segundo plano".
   - Verifique: o documento aparece nítido, proporcional na folha A4 com escala padrão.
4. **Cenário 2: "Gráficos de segundo plano" Desmarcado**:
   - Desmarque a opção "Gráficos de segundo plano".
   - Verifique:
     - [ ] A folha **NÃO** fica em branco.
     - [ ] Os badges de status exibem o texto em preto delimitado por borda.
     - [ ] A tabela de itens exibe linhas e colunas pretas bem demarcadas.
     - [ ] As linhas de assinatura e os nomes continuam perfeitamente visíveis.
     - [ ] O logotipo CCB continua visível.
