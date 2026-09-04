# Interface Contract: Impressão Defensiva de Alto Contraste

## 1. Contrato de Estilo Monocromático (`@media print`)

### Diretivas de Cores
```css
-webkit-print-color-adjust: exact !important;
print-color-adjust: exact !important;
```

### Contrato de Elementos Específicos

1. **Tipografia Geral**:
   - Todo texto deve ser forçado para `#000000 !important;` no contexto de impressão.
   - Textos de labels (`.text-muted`) devem usar `#333333 !important` para não ficarem apagados.

2. **Badges (`.badge`, `.bg-*`)**:
   - `color: #000000 !important;`
   - `background-color: transparent !important;`
   - `background: transparent !important;`
   - `border: 1px solid #000000 !important;`
   - `font-weight: 600 !important;`

3. **Caixa de Observações**:
   - `border: 1px solid #000000 !important;`
   - `background-color: transparent !important;`
   - `color: #000000 !important;`

4. **Tabela de Itens**:
   - Cabeçalho: `border: 1px solid #000000 !important; font-weight: bold; color: #000;`
   - Células: `border: 1px solid #000000 !important; color: #000;`

5. **Campos de Assinatura**:
   - Linha superior: `border-top: 1px solid #000000 !important;`
   - Identificação do signatário: centralizada, texto preto sólido.
