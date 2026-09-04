# Quickstart: Validação da Correção de Escala de Impressão

Guia rápido para testar e validar o funcionamento da impressão direta em folha A4 com escala padrão de 100%.

## Pré-requisitos
- Ambiente local ativo com XAMPP (Apache e MySQL) ou servidor PHP iniciado.
- Navegador Google Chrome ou Microsoft Edge.

## Procedimento de Teste

1. **Acessar o sistema**:
   - Navegue até `http://localhost/almoxarifadoccb/public/movements/1` (ou a rota equivalente local/produção).
2. **Visualizar a movimentação**:
   - Certifique-se de que a tela exibe o comprovante com cabeçalho, dados gerais, itens e os botões inferiores.
3. **Acionar a Impressão**:
   - Clique no botão azul **"Imprimir Comprovante"** (ou use o atalho de teclado `Ctrl + P`).
4. **Verificar a Caixa de Diálogo de Impressão**:
   - Certifique-se de que a opção **Escala** esteja definida como **"Padrão"** (100%).
   - Observe a pré-visualização da folha de papel:
     - [ ] A folha **NÃO** está em branco.
     - [ ] O cabeçalho institucional com o logotipo da CCB aparece no topo da folha.
     - [ ] Os dados gerais (Data/Hora, Status, Operador, Fornecedor/Beneficiário) estão organizados em colunas legíveis.
     - [ ] A tabela de itens ocupa a largura total da folha sem estourar as margens.
     - [ ] O rodapé com os campos de assinatura (Almoxarife e Recebedor) aparece na parte inferior do documento.
     - [ ] O número total de folhas para uma movimentação simples com poucos itens é exatamente **1 folha**.
5. **Verificar Ausência de Ajuste Manual**:
   - Nenhuma intervenção de escala personalizada (ex: 40%) deve ser necessária.
