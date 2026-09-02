# Data Model & Entidades Envolvidas

**Feature**: Adequação da Tela de Login para Produção e Correção da Impressão Direta
**Branch**: `006-login-producao-impressao`
**Date**: 2026-09-02

## 1. Visão Geral dos Modelos de Dados

Esta funcionalidade concentra-se em ajustes de apresentação, segurança de autenticação e compatibilidade de folha de estilos de impressão, não exigindo alterações estruturais de banco de dados (migrations) nem modificação de esquemas existentes.

---

## 2. Entidades Relevantes

### 2.1. Usuário (`User`)
- **Papel na Funcionalidade**: Autenticação limpa de operadores já existentes.
- **Campos Relevantes**:
  - `email`: string (único, fornecido pelo operador na tela de login).
  - `password`: hash (validado contra o formulário).
  - `name`: exibido nas assinaturas de comprovante como Almoxarife Responsável.

### 2.2. Movimentação de Estoque (`Movement`)
- **Papel na Funcionalidade**: Fonte de dados para a renderização do comprovante impresso.
- **Campos e Relacionamentos Exibidos na Impressão**:
  - `code`: string (código identificador, ex: `MOV-20260902-001`).
  - `type`: Enum `MovementType` (`ENTRY`, `CONSUMPTION`, `EPI`, `LOAN`).
  - `status`: Enum `MovementStatus` (`OPEN`, `COMPLETED`, `RETURNED`, etc.).
  - `created_at`: datetime (data e hora do lançamento).
  - `user`: relacionamento com `User` (Almoxarife).
  - `beneficiary`: relacionamento com `Beneficiary` (Quem retira).
  - `destination`: relacionamento com `Destination` (Local de aplicação).
  - `entryDocument`: relacionamento com `EntryDocument` (para entradas: Fornecedor, NF, Valor Total).
  - `items`: relacionamento `hasMany` com `MovementItem`.
  - `notes`: texto com observações gerais.

### 2.3. Item da Movimentação (`MovementItem`)
- **Campos Exibidos na Tabela de Impressão**:
  - `material.code_sku`: código SKU do material.
  - `material.name`: nome descritivo do material.
  - `material.ca_number`: número do Certificado de Aprovação (se aplicável).
  - `quantity`: quantidade solicitada com unidade de medida (`unit_measure`).
  - `returned_quantity`: quantidade devolvida (se empréstimo).
  - `expected_return_date`: data prevista de devolução (se empréstimo).
  - `status`: Enum `ItemStatus`.

---

## 3. Regras de Apresentação e Integridade

1. **Estado Inicial de Autenticação**: Nenhum atributo ou valor padrão persistido na view de login.
2. **Campos Obrigatórios de Impressão**: Cabeçalho institucional, código e tipo da movimentação, responsável, beneficiário/fornecedor, tabela completa de itens e blocos de assinatura para ambas as partes.
