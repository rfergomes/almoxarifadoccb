# UI Contract: Tela de Autenticação (Login em Produção)

**Endpoint/Rota**: `GET /login` e `POST /login`
**View Blade**: `resources/views/auth/login.blade.php`

## 1. Estrutura do Formulário de Entrada

| Campo | Atributo `name` | Tipo | Valor Inicial | Validação / Obrigatório |
|---|---|---|---|---|
| E-mail de Acesso | `email` | `email` | `old('email')` (vazio por padrão) | `required`, formato de e-mail válido |
| Senha | `password` | `password` | `""` (vazio por padrão) | `required` |
| Lembrar Acesso | `remember` | `checkbox` | Desmarcado | Opcional |

## 2. Regras de Interface e Segurança

1. **Campos Desmarcados/Limpos**: Nenhum dado estático de teste (`admin@ccb.org.br`, `12345678`) deve aparecer nos atributos `value`.
2. **Ausência de Atalhos**: Não conter botões `.btn-quick-login` ou links de demonstração.
3. **Comportamento em Falha**: Em caso de credenciais inválidas, exibir alerta do tipo `alert-danger` e manter preenchido apenas o e-mail digitado via `old('email')`.
