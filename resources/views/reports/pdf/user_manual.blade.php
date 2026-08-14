<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Manual do Usuário - Almoxarifado CCB</title>
    <style>
        @page {
            margin: 2cm 1.5cm 2cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #2b2b2b;
        }
        .header {
            position: fixed;
            top: -1.2cm;
            left: 0;
            right: 0;
            height: 1cm;
            border-bottom: 2px solid #003366;
            text-align: right;
            font-size: 8pt;
            color: #666;
        }
        .footer {
            position: fixed;
            bottom: -1.2cm;
            left: 0;
            right: 0;
            height: 1cm;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #777;
            padding-top: 5px;
        }
        .page-number:before {
            content: "Página " counter(page);
        }
        .cover {
            text-align: center;
            padding-top: 3cm;
            page-break-after: always;
        }
        .cover-title {
            font-size: 26pt;
            font-weight: bold;
            color: #003366;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .cover-subtitle {
            font-size: 16pt;
            color: #4A5568;
            margin-bottom: 2cm;
        }
        .cover-badge {
            display: inline-block;
            background-color: #EBF8FF;
            color: #2B6CB0;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 11pt;
            font-weight: bold;
            border: 1px solid #BEE3F8;
            margin-bottom: 3cm;
        }
        .cover-meta {
            font-size: 10pt;
            color: #718096;
            border-top: 1px solid #E2E8F0;
            padding-top: 1cm;
        }
        h1 {
            font-size: 18pt;
            color: #003366;
            border-bottom: 2px solid #003366;
            padding-bottom: 4px;
            margin-top: 1.5cm;
            margin-bottom: 12px;
            page-break-after: avoid;
        }
        h2 {
            font-size: 14pt;
            color: #2B6CB0;
            margin-top: 1cm;
            margin-bottom: 8px;
            page-break-after: avoid;
        }
        h3 {
            font-size: 12pt;
            color: #2D3748;
            margin-top: 15px;
            margin-bottom: 6px;
            page-break-after: avoid;
        }
        p {
            margin-bottom: 10px;
            text-align: justify;
        }
        ul, ol {
            margin-top: 5px;
            margin-bottom: 12px;
            padding-left: 20px;
        }
        li {
            margin-bottom: 5px;
        }
        .alert-box {
            padding: 12px 15px;
            border-radius: 5px;
            margin: 12px 0;
            font-size: 10.5pt;
        }
        .alert-info {
            background-color: #EBF8FF;
            border-left: 4px solid #3182CE;
            color: #2B6CB0;
        }
        .alert-warning {
            background-color: #FEFCBF;
            border-left: 4px solid #D69E2E;
            color: #744210;
        }
        .alert-danger {
            background-color: #FED7D7;
            border-left: 4px solid #E53E3E;
            color: #9B2C2C;
        }
        .alert-success {
            background-color: #C6F6D5;
            border-left: 4px solid #38A169;
            color: #22543D;
        }
        .badge {
            display: inline-block;
            padding: 2px 7px;
            font-size: 9pt;
            font-weight: bold;
            border-radius: 3px;
            color: #fff;
        }
        .badge-danger { background-color: #E53E3E; }
        .badge-warning { background-color: #D69E2E; color: #1A202C; }
        .badge-success { background-color: #38A169; }
        .badge-info { background-color: #3182CE; }
        .badge-secondary { background-color: #718096; }

        table.styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
        }
        table.styled-table th {
            background-color: #003366;
            color: #ffffff;
            padding: 8px;
            text-align: left;
        }
        table.styled-table td {
            padding: 8px;
            border-bottom: 1px solid #E2E8F0;
        }
        table.styled-table tr:nth-child(even) {
            background-color: #F7FAFC;
        }
        .step-number {
            display: inline-block;
            background-color: #003366;
            color: white;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            line-height: 22px;
            margin-right: 6px;
        }
    </style>
</head>
<body>

    <div class="header">
        Almoxarifado Central CCB — Manual Prático do Usuário
    </div>

    <div class="footer">
        Almoxarifado Central CCB &bull; Guia do Operador &bull; <span class="page-number"></span>
    </div>

    <!-- Capa -->
    <div class="cover">
        <div class="cover-title">Almoxarifado CCB</div>
        <div class="cover-subtitle">Manual Prático de Utilização do Sistema</div>
        
        <div class="cover-badge">Guia Ilustrado para Operadores e Almoxarifes</div>

        <div style="margin-top: 2cm; margin-bottom: 2cm;">
            <p style="text-align: center; color: #4A5568;">
                Este manual destina-se a orientar de forma simples e passo a passo a gestão de estoques, cadastro de materiais, controle de validade de insumos, registro de patrimônio, saídas, empréstimos de ferramentas e relatórios gerenciais.
            </p>
        </div>

        <div class="cover-meta">
            <strong>Congregação Cristã no Brasil — Almoxarifado Central</strong><br>
            Versão do Sistema: 2026.1 &bull; Documento Atualizado em {{ date('d/m/Y') }}
        </div>
    </div>

    <!-- Índice de Conteúdo -->
    <h1>1. Visão Geral do Sistema</h1>
    <p>
        O <strong>Sistema de Almoxarifado CCB</strong> foi desenvolvido para facilitar o controle diário de materiais de construção, tintas, insumos, ferramentas e Equipamentos de Proteção Individual (EPIs) utilizados em obras e manutenções das Casas de Oração.
    </p>
    
    <div class="alert-box alert-info">
        <strong>💡 Objetivo Principal:</strong> Garantir que nenhum material seja extraviado, controlar o prazo de validade de insumos perecíveis (como tintas e massas), monitorar empréstimos de ferramentas e oferecer relatórios transparentes para a administração.
    </div>

    <h2>1.1 Perfis de Acesso</h2>
    <p>Cada operador possui um nível de permissão no sistema:</p>
    <ul>
        <li><strong>Administrador:</strong> Acesso total ao sistema, gestão de usuários, configurações e permissões.</li>
        <li><strong>Almoxarife:</strong> Pode cadastrar materiais, registrar entradas (compras/doações), realizar saídas/empréstimos, devoluções e contagens de inventário.</li>
        <li><strong>Consulta:</strong> Apenas visualiza os saldos e gera relatórios, sem permissão para alterar estoques.</li>
    </ul>

    <h1>2. Entendendo o Painel Principal (Dashboard)</h1>
    <p>Ao fazer login no sistema, você verá o <strong>Painel de Indicadores</strong> com cartões coloridos que mostram o estado atual do almoxarifado:</p>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Indicador / Cor</th>
                <th>O que significa?</th>
                <th>Ação Recomendada</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-info">Total em Estoque</span></td>
                <td>Quantidade de tipos de materiais ativos cadastrados no sistema.</td>
                <td>Informativo geral do catálogo.</td>
            </tr>
            <tr>
                <td><span class="badge badge-danger">Produtos Vencidos</span></td>
                <td>Tintas, massas ou insumos que ultrapassaram a data de validade.</td>
                <td>Verificar itens e priorizar descarte ou separar para uso imediato autorizado.</td>
            </tr>
            <tr>
                <td><span class="badge badge-warning">A Vencer (30 dias)</span></td>
                <td>Materiais cuja validade expira nos próximos 30 dias.</td>
                <td>Priorizar a distribuição e saída desses materiais antes que vençam.</td>
            </tr>
            <tr>
                <td><span class="badge badge-info">Itens com Patrimônio</span></td>
                <td>Ferramentas e máquinas identificadas com código da entidade.</td>
                <td>Consultar localização e destinação dos equipamentos.</td>
            </tr>
            <tr>
                <td><span class="badge badge-danger">Estoque Mínimo</span></td>
                <td>Produtos cujo saldo atual está abaixo do limite de segurança.</td>
                <td>Solicitar nova compra ou reposição do material.</td>
            </tr>
            <tr>
                <td><span class="badge badge-warning">Empréstimos Atrasados</span></td>
                <td>Ferramentas emprestadas que não foram devolvidas no prazo.</td>
                <td>Entrar em contato com o beneficiário para solicitar a devolução.</td>
            </tr>
        </tbody>
    </table>

    <h1>3. Gestão do Catálogo de Materiais</h1>

    <h2>3.1 Como Buscar um Material</h2>
    <ol>
        <li>Acesse o menu <strong>Materiais</strong> no lado esquerdo.</li>
        <li>No campo de busca no topo da página, digite o <strong>Nome</strong>, o código <strong>SKU</strong> ou o <strong>Código de Patrimônio</strong>.</li>
        <li>Se quiser, use os filtros por <strong>Categoria</strong>, <strong>Status de Validade</strong> (Vencidos/A Vencer) ou <strong>Patrimônio</strong>.</li>
        <li>Clique no botão <strong>Filtrar</strong>.</li>
    </ol>

    <h2>3.2 Como Cadastrar um Novo Material</h2>
    <ol>
        <li>Na tela de Materiais, clique no botão azul <strong>+ Novo Material</strong>.</li>
        <li>Preencha os campos obrigatórios:
            <ul>
                <li><strong>Código SKU:</strong> Código único de identificação (ex: <code>MAT-001</code>).</li>
                <li><strong>Nome do Material:</strong> Nome claro (ex: <code>Tinta Acrílica Branca 18L</code> ou <code>Serra Tico-Tico</code>).</li>
                <li><strong>Categoria:</strong> Selecione a categoria adequada (Construção, Elétrica, Pintura, EPI, Ferramentas, etc.).</li>
                <li><strong>Unidade de Medida:</strong> Selecione se é por UN (Unidade), KG (Quilograma), GL (Galão), CX (Caixa), M (Metro).</li>
                <li><strong>É Retornável?:</strong> Marque <em>"Sim (Ferramenta/Eqp)"</em> se for um equipamento que deve ser devolvido após o uso, ou <em>"Não (Consumo)"</em> se for gasto na obra.</li>
            </ul>
        </li>
        <li><strong>Campos Especiais (MUITO IMPORTANTES):</strong>
            <ul>
                <li><strong>Data de Validade:</strong> Preencha a data de vencimento para tintas, massa corrida, grafiatos, colas e produtos perecíveis. O sistema avisará automaticamente quando estiver próximo de vencer!</li>
                <li><strong>Código de Patrimônio:</strong> Digite a plaqueta/código de patrimônio da entidade para equipamentos e ferramentas duráveis (ex: <code>PAT-CCB-2026-001</code>).</li>
                <li><strong>Nº CA e Validade CA:</strong> Preencha apenas no caso de Equipamentos de Proteção Individual (EPIs).</li>
            </ul>
        </li>
        <li>Clique em <strong>Salvar Material</strong>.</li>
    </ol>

    <div class="alert-box alert-warning">
        <strong>⚠️ Atenção na Edição de Cadastro:</strong> Para manter a segurança, a alteração direta de estoque NÃO é feita na edição cadastral. Se precisar acertar a contagem física do estoque, clique na ação <strong>"Inventário"</strong> ao lado do material.
    </div>

    <h1>4. Registrando Entradas no Estoque (Compras e Doações)</h1>
    <p>Toda vez que chegarem materiais no almoxarifado (por compra ou doação):</p>

    <ol>
        <li>Acesse o menu <strong>Entradas de Estoque</strong> > <strong>Nova Entrada</strong>.</li>
        <li>Informe o número da Nota Fiscal ou documento de doação, a data e o fornecedor/doador.</li>
        <li>Adicione os materiais recebidos e informe a quantidade exata entregue.</li>
        <li>Clique em <strong>Confirmar Entrada</strong>. O saldo do estoque será atualizado automaticamente!</li>
    </ol>

    <h1>5. Registrando Saídas e Empréstimos</h1>

    <h2>5.1 Tipos de Saída</h2>
    <p>O sistema possui 3 modalidades de saída de materiais:</p>
    <ul>
        <li><strong>Consumo Geral:</strong> Para materiais descartáveis ou aplicados definitivamente na obra (cimento, lâmpadas, tintas, parafusos).</li>
        <li><strong>Entrega de EPI:</strong> Para equipamentos de proteção individual (capacetes, luvas, óculos) entregues a voluntários ou trabalhadores.</li>
        <li><strong>Empréstimo:</strong> Exclusivo para ferramentas e máquinas retornáveis. Exige informar a <strong>Data Prevista de Devolução</strong>.</li>
    </ul>

    <h2>5.2 Passo a Passo para Lançar uma Saída</h2>
    <ol>
        <li>Acesse o menu <strong>Saídas / Empréstimos</strong> > <strong>Nova Saída</strong>.</li>
        <li>Selecione o <strong>Tipo de Movimentação</strong> (Consumo, EPI ou Empréstimo).</li>
        <li>Selecione o <strong>Beneficiário</strong> (quem está retirando) e o <strong>Destino</strong> (Casa de Oração ou Obra).</li>
        <li>Selecione o material e informe a quantidade retirada.</li>
        <li>No caso de Empréstimos, informe a <strong>Data Prevista de Devolução</strong>.</li>
        <li>Clique em <strong>Confirmar e Gravar Movimentação</strong>.</li>
    </ol>

    <div class="alert-box alert-danger">
        <strong>🚨 Alerta de Produto Vencido:</strong> Se você tentar dar saída em uma tinta ou massa corrida com data de validade vencida, o sistema exibirá uma mensagem de aviso no topo da tela. Confirme com a administração se o produto pode ser aplicado ou se deve ser descartado!
    </div>

    <h1>6. Devolução de Ferramentas Emprestadas</h1>
    <p>Quando o voluntário devolver uma ferramenta emprestada:</p>
    <ol>
        <li>Acesse o menu <strong>Saídas / Empréstimos</strong>.</li>
        <li>Localize o empréstimo na lista (filtre por empréstimos "Abertos" ou "Em Atraso").</li>
        <li>Clique no botão <strong>Devolver Itens</strong>.</li>
        <li>Confirme a quantidade de ferramentas devolvidas e o estado de conservação.</li>
        <li>Ao confirmar, o item retorna ao saldo disponível em estoque e a pendência do beneficiário é encerrada!</li>
    </ol>

    <h1>7. Inventário e Contagem Física de Estoque</h1>
    <p>Para conferência periódica dos saldos físicos no almoxarifado:</p>
    <ol>
        <li>Acesse o menu <strong>Inventário Periódico</strong>.</li>
        <li>Clique em <strong>Iniciar Nova Sessão de Inventário</strong>.</li>
        <li>Digite a contagem física real contada na prateleira para cada item.</li>
        <li>O sistema calculará automaticamente a diferença (sobra ou falta).</li>
        <li>Após revisão da administração, clique em <strong>Concluir e Ajustar Estoque Atomica</strong> para atualizar todos os saldos oficiais.</li>
    </ol>

    <h1>8. Emitindo Relatórios em PDF e Excel</h1>
    <p>Na <strong>Central de Relatórios</strong>, você pode gerar documentos oficiais organizados:</p>
    <ul>
        <li><strong>Posição Geral de Estoque:</strong> Lista completa dos saldos e valores armazenados.</li>
        <li><strong>Validade de Insumos:</strong> Relatório exclusivo mostrando produtos vencidos, a vencer nos próximos 30 dias e produtos válidos.</li>
        <li><strong>Bens & Patrimônio:</strong> Relatório com todas as ferramentas e equipamentos identificados com código de patrimônio.</li>
        <li><strong>Devoluções em Atraso:</strong> Lista de ferramentas emprestadas fora do prazo para cobrança.</li>
        <li><strong>Histórico de Movimentações:</strong> Registro completo de entradas e saídas com filtro por período.</li>
    </ul>

    <p style="margin-top: 1cm; text-align: center; font-weight: bold; color: #003366;">
        Em caso de dúvidas operacionais, consulte a administração do Almoxarifado Central CCB.
    </p>

</body>
</html>
