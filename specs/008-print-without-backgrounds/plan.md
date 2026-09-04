# Implementation Plan: Visibilidade da Impressão sem Gráficos de Segundo Plano

**Branch**: `008-print-without-backgrounds` | **Date**: 2026-09-04 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/008-print-without-backgrounds/spec.md`

## Summary

Implementar estilos defensivos de alto contraste para o comprovante de movimentação em `@media print`, garantindo que o documento seja renderizado de forma nítida e completa na folha A4 mesmo quando o usuário mantiver a opção "Gráficos de segundo plano" (Background graphics) desmarcada no navegador. A solução aplica a diretiva nativa `print-color-adjust: exact`, converte badges para texto preto com contorno sólido e assegura bordas pretas explícitas em tabelas, observações e campos de assinatura.

## Technical Context

**Language/Version**: PHP 8.3+ (Laravel 12) & CSS3 (Vite + Tailwind / Bootstrap)
**Primary Dependencies**: Bootstrap 5.3.3, AdminLTE v4.0.0-beta2
**Storage**: N/A
**Testing**: Suíte de testes de regressão de Movimentação e validação no Chrome Print Preview
**Target Platform**: Navegadores Desktop (Chromium, Edge, Firefox) e Impressoras Laser/Jato de Tinta
**Project Type**: Web Application (Blade + CSS)
**Constraints**: Não afetar a exibição interativa em tela nem a rota de download de PDF

## Constitution Check

- [x] **Princípio I (Camada de Serviços & POO Estrita)**: Nenhuma regra de negócio de estoque é violada.
- [x] **Princípio II (Tipagem e PSR-12)**: Sem alterações em backend além de Blade views.
- [x] **Princípio III (Integridade Transacional)**: Sem alterações de escrita em banco de dados.
- [x] **Princípio IV (Gestão de EPIs e Empréstimos)**: Os campos de CA e datas mantêm contraste legível no papel.
- [x] **Princípio V (Interface do Usuário & AdminLTE v4)**: A interface em tela permanece inalterada; apenas o comportamento do `@media print` é aprimorado.

## Project Structure

### Documentation (this feature)

```text
specs/008-print-without-backgrounds/
├── plan.md              # Este plano de implementação
├── research.md          # Análise técnica de economia de tinta no Chromium
├── data-model.md        # Entidades exibidas no comprovante
├── quickstart.md        # Guia de validação da impressão sem gráficos de fundo
├── contracts/
│   └── print-high-contrast.md # Contrato de estilo de alto contraste para impressão
└── checklists/
    └── requirements.md  # Checklist de requisitos
```

### Source Code (repository root)

```text
resources/
├── css/
│   └── responsive-custom.css        # Diretivas globais print-color-adjust e badges defensivos
└── views/
    └── movements/
        └── show.blade.php           # Estilos de alto contraste no bloco de impressão do comprovante
```

## Proposed Changes

### 1. `resources/views/movements/show.blade.php`
- Adicionar propriedades `print-color-adjust: exact !important;` e `-webkit-print-color-adjust: exact !important;` no `@media print`.
- Adicionar regras defensivas para badges no print:
  ```css
  .printable-receipt .badge {
    color: #000000 !important;
    background-color: transparent !important;
    background: transparent !important;
    border: 1px solid #000000 !important;
    font-weight: 600 !important;
  }
  ```
- Forçar todas as cores de texto e labels para tons escuros de alto contraste:
  ```css
  .printable-receipt, .printable-receipt * {
    color: #000000 !important;
  }
  .printable-receipt .text-muted, .printable-receipt label {
    color: #333333 !important;
  }
  ```
- Definir bordas pretas sólidas para a tabela e caixas:
  ```css
  .printable-receipt table,
  .printable-receipt th,
  .printable-receipt td {
    border: 1px solid #000000 !important;
  }
  ```

### 2. `resources/css/responsive-custom.css`
- Declarar globalmente `print-color-adjust: exact !important; -webkit-print-color-adjust: exact !important;` no seletor `body` e `html` dentro de `@media print`.

### 3. Recompilação de Assets
- Executar `pnpm run build` para consolidar o CSS em `public/build/assets/`.

## Verification Plan

### Automated Tests
- `C:\xampp\php\php.exe artisan test --filter=Movement`

### Manual Verification
1. Abrir `/movements/1`.
2. Acionar `Ctrl + P`.
3. Alternar "Gráficos de segundo plano" entre marcado e desmarcado.
4. Confirmar que em ambos os estados o texto e a estrutura da página aparecem nítidos na folha A4 em escala 100%.
