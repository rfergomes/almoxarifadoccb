# Interface Contract: Impressão Direta do Comprovante de Movimentação

## 1. Gatilho de Interface
- **Elemento**: Botão "Imprimir Comprovante"
- **Ação**: Invocação JavaScript nativa `window.print()`
- **Localização**: Rodapé do card de detalhes da movimentação (`/movements/{id}`)
- **Visibilidade**: Visível apenas em tela (`.no-print`); oculto na folha impressa.

## 2. Contrato de Estilo de Impressão (`@media print`)

### Contêiner Raiz do Papel
- **Orientação**: Retrato (`portrait`)
- **Tamanho**: A4 (210mm x 297mm)
- **Margens do Papel**: 8mm superior/inferior, 10mm esquerda/direita
- **Largura Efetiva de Renderização**: 100% da área útil do papel (~190mm)

### Elementos Ocultos (`display: none !important`)
- `.app-sidebar`
- `.app-header`
- `.app-footer`
- `.app-content-header`
- `.no-print` (botões "Voltar", "Baixar PDF", "Imprimir Comprovante", modais de devolução)

### Elementos Visíveis Exclusivos de Impressão (`display: block !important`)
- `.d-print-block`: Bloco oficial de assinaturas físicas (Almoxarife Responsável e Beneficiário/Fornecedor).

### Estrutura de Distribuição de Colunas
- Detalhes Gerais da Movimentação: 2 a 4 colunas proporcionais por linha.
- Tabela de Itens: Largura total (100%), bordas colapsadas, quebra de página proibida dentro das linhas (`break-inside: avoid`).
- Assinaturas: 2 colunas lado a lado de 50% de largura cada, com linha superior para rubrica e identificação textual abaixo.
