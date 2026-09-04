# Phase 0 Research: Visibilidade da Impressão sem Gráficos de Segundo Plano

## Problema Investigado
Quando o usuário aciona a impressão no Chrome ou Edge e desmarca a opção **"Gráficos de segundo plano"** (Background graphics), a folha A4 fica em branco ou com elementos essenciais invisíveis. Ao marcar a opção, o comprovante é exibido corretamente.

---

## Descobertas Técnicas

### 1. Comportamento do Modo de Economia de Tinta do Chromium
- A opção "Gráficos de segundo plano" nas configurações do Chrome/Edge controla a supressão de cores e fundos CSS para poupar toner/tinta.
- Quando desmarcada:
  1. Propriedades `background-color` e `background-image` são completamente ignoradas.
  2. Elementos com texto claro (ex: `.badge` com `color: #ffffff`) têm seu fundo removido pelo navegador, resultando em texto branco sobre papel branco (invisibilidade total do texto).
  3. Divisores e sombreamentos (`box-shadow`, fundos alternados de tabela `.table-light`, caixas `.bg-light`) perdem contraste visual caso não possuam linhas de borda físicas (`border`).

### 2. A Diretiva CSS `print-color-adjust`
- A especificação W3C CSS Color 4 define a propriedade `print-color-adjust` (anteriormente `-webkit-print-color-adjust` no WebKit/Blink):
  ```css
  print-color-adjust: exact;
  -webkit-print-color-adjust: exact;
  ```
- Essa diretiva sinaliza ao navegador que as cores e fundos daquele elemento ou documento são essenciais para o significado da informação, instruindo o motor de impressão a mantê-los ativos.

### 3. Estratégia Defensiva Monocromática (Fallback Universal)
- Como a caixa de seleção do diálogo de impressão do usuário tem precedência final no navegador sobre preferências de economia de tinta, a aplicação DEVE adotar um padrão CSS defensivo:
  - Garantir que nenhum elemento textual dependa de fundo colorido para contraste.
  - Forçar todos os textos para preto sólido (`#000000`).
  - Forçar contornos pretos (`border: 1px solid #000000`) em badges, caixas e tabelas.
  - Preservar linhas físicas para assinaturas e separadores.

---

## Decisões Arquiteturais

### Decisão 1: Aplicação de `print-color-adjust: exact`
- **Escolha**: Declarar no `body` e em `.printable-receipt`:
  ```css
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
  ```
- **Rationale**: Fornece a melhor experiência institucional possível quando o usuário imprime com cores.

### Decisão 2: Estilos Defensivos de Alto Contraste para Badges e Textos
- **Escolha**: Definir regras de impressão que convertem badges (`.badge`) para contorno preto com texto preto (`color: #000 !important; border: 1px solid #000 !important; background: transparent !important;`).
- **Rationale**: Elimina o risco de texto branco invisível sobre o papel branco quando o fundo colorido for suprimido.

### Decisão 3: Bordas Explícitas de Tabela e Divisores
- **Escolha**: Definir bordas pretas nítidas em todas as células da tabela (`th, td { border: 1px solid #000 !important; }`), divisores (`border-bottom: 1px solid #000 !important;`) e linhas de assinatura (`border-top: 1px solid #000 !important;`).
- **Rationale**: Garante estrutura visual legível em qualquer impressora monocromática ou laser sem segundo plano.
