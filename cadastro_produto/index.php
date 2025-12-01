<?php
// ===========================================
// CONFIGURAÇÃO DO BANCO
// ===========================================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "almoxarifado"; // Coloque o nome do seu banco

// Configura o reporte de erros do MySQLi para garantir Prepared Statements
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $user, $pass, $db);
} catch (Exception $e) {
    die("Falha na conexão: " . $e->getMessage());
}

// Variável para armazenar dados do produto em modo de edição
$produto_para_edicao = null;
$modo_edicao = false;
$botao_texto = "Cadastrar";

// ===========================================
// 1. PROCESSAR DADOS (Cadastro / Atualização)
// ===========================================

// Verifica se a requisição é um POST para Cadastro ou Atualização
if (isset($_POST['operacao'])) {

    // Coleta os dados comuns
    $nome       = $_POST['nomeProduto'];
    $preco      = $_POST['precoProduto'];
    $quantidade = $_POST['quantidadeProduto'];
    $data       = $_POST['validadeProduto'];
    $cor        = $_POST['corProduto'];
    $textura    = $_POST['texturaProduto'];
    $material   = $_POST['materialProduto'];
    $peso       = $_POST['pesoProduto'];
    $unidade    = $_POST['unidadeMedida'];
    $aplicacao  = $_POST['aplicacaoProduto'];

    if ($_POST['operacao'] == 'cadastrar') {
        // --- LÓGICA DE CADASTRO (INSERT) ---
        $sql = "INSERT INTO cadastro_produtos 
            (nome_produto_cp, preco_cp, quantidade_cp, data_cp, cor_cp, textura_cp, 
            material_fabricacao_cp, peso_cp, unidade_cp, aplicacao_cp)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            //Os ? são espaços vazios onde o PHP vai colocar os valores depois.
            //PREPARE STATEMENT = QUESTOES DE SEGURANCA 

            
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdissssiss", $nome, $preco, $quantidade, $data, $cor, $textura, $material, $peso, $unidade, $aplicacao);
//bind_param() diz quais variáveis vão entrar em cada ? e informa o tipo de dado
        if ($stmt->execute()) {
            header("Location: index.php?status=cadastro_sucesso");
            exit();
        } else {
            echo "<script>alert('Erro ao cadastrar!');</script>";
        }
    } elseif ($_POST['operacao'] == 'atualizar') {
        // --- LÓGICA DE ATUALIZAÇÃO (UPDATE) ---
        $id_update = $_POST['id_produto']; // ID oculto

        $sql_update = "UPDATE cadastro_produtos SET
            nome_produto_cp = ?, preco_cp = ?, quantidade_cp = ?, data_cp = ?, cor_cp = ?, textura_cp = ?, 
            material_fabricacao_cp = ?, peso_cp = ?, unidade_cp = ?, aplicacao_cp = ?
            WHERE id_cp = ?";

        $stmt_update = $conn->prepare($sql_update);
        // Tipos: sdissssiss (para os 10 campos de dados) + i (para o id_update)
        $stmt_update->bind_param("sdissssissi", $nome, $preco, $quantidade, $data, $cor, $textura, $material, $peso, $unidade, $aplicacao, $id_update);

        if ($stmt_update->execute()) {
            header("Location: index.php?status=edicao_sucesso");
            exit();
        } else {
            echo "<script>alert('Erro ao atualizar o produto!');</script>";
        }
    }
}

// ===========================================
// 2. BUSCAR PRODUTO PARA MODO EDIÇÃO
// ===========================================
if (isset($_GET['editar'])) {
    $id_editar = $_GET['editar'];

    // Prepared Statement para a busca
    $stmt_fetch = $conn->prepare("SELECT * FROM cadastro_produtos WHERE id_cp = ?");
    $stmt_fetch->bind_param("i", $id_editar);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();

    if ($result->num_rows > 0) {
        $produto_para_edicao = $result->fetch_assoc();
        $modo_edicao = true;
        $botao_texto = "Salvar Alterações";
    } else {
        echo "<script>alert('Produto não encontrado!'); window.location.href='index.php';</script>";
    }
}


// ======================================================
// 3. EXCLUIR PRODUTO (COM REMOÇÃO FORÇADA DE HISTÓRICO)
// ======================================================
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    // PASSO 1: Apagar primeiro o histórico na tabela 'gestao_estoque'
    // Atenção: O nome da coluna aqui deve ser o da chave estrangeira na tabela de estoque.
    // Pelo erro que você mandou antes, o nome é 'cadastro_produtos_id'.
    $sql_limpa_historico = "DELETE FROM gestao_estoque WHERE cadastro_produtos_id = ?";
    $stmt_historico = $conn->prepare($sql_limpa_historico);
    $stmt_historico->bind_param("i", $id);
    
    // Executa a limpeza do histórico. Não precisamos verificar se deu certo, 
    // pois se não tiver histórico, ele só não apaga nada e segue o baile.
    $stmt_historico->execute();
    $stmt_historico->close();

    // PASSO 2: Agora sim, apagar o produto da tabela 'cadastro_produtos'
    $sql_delete = "DELETE FROM cadastro_produtos WHERE id_cp = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id);

    if ($stmt_delete->execute()) {
        header("Location: index.php?status=exclusao_sucesso");
        exit();
    } else {
        // Caso dê algum erro inesperado
        echo "<script>alert('Erro ao excluir: " . addslashes($stmt_delete->error) . "'); window.location.href='index.php';</script>";
    }
}

// ======================================================
// 4. MENSAGENS DE STATUS E BUSCAR TODOS OS PRODUTOS
// ======================================================

// Exibe mensagem de sucesso
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'cadastro_sucesso') {
        echo "<script>alert('Produto cadastrado com sucesso!');</script>";
    } else if ($_GET['status'] == 'exclusao_sucesso') {
        echo "<script>alert('Produto excluído!');</script>";
    } else if ($_GET['status'] == 'edicao_sucesso') {
        echo "<script>alert('Produto atualizado com sucesso!');</script>";
    }
}

// Busca todos os produtos
$produtos = $conn->query("SELECT * FROM cadastro_produtos ORDER BY id_cp DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $modo_edicao ? 'Editar Produto' : 'Cadastro de Produtos' ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <h2><?= $modo_edicao ? 'Modo Edição de Produto' : 'Cadastro de Produto' ?></h2>
        <button onclick="window.location.href='../tela_inicial/index.php'">Voltar ao Menu Principal</button>
    </header>

    <div class="container">
        <h3>Gerenciar Produtos</h3>

        <div class="topo">
            <input type="text" id="busca" placeholder="Buscar produto..." onkeyup="buscarProduto()">
        </div>

        <form method="POST">

            <input type="hidden" name="operacao" value="<?= $modo_edicao ? 'atualizar' : 'cadastrar' ?>">
            <?php if ($modo_edicao): ?>
                <input type="hidden" name="id_produto" value="<?= $produto_para_edicao['id_cp'] ?>">
            <?php endif; ?>

            <input type="text" name="nomeProduto" placeholder="Nome do produto" required
                value="<?= $modo_edicao ? $produto_para_edicao['nome_produto_cp'] : '' ?>">

            <input type="number" name="precoProduto" placeholder="Preço" required step="0.01"
                value="<?= $modo_edicao ? $produto_para_edicao['preco_cp'] : '' ?>">

            <input type="number" name="quantidadeProduto" placeholder="Quantidade" required
                value="<?= $modo_edicao ? $produto_para_edicao['quantidade_cp'] : '' ?>">

            <input type="date" name="validadeProduto"
                value="<?= $modo_edicao ? $produto_para_edicao['data_cp'] : '' ?>">

            <input type="text" name="corProduto" placeholder="Cor"
                value="<?= $modo_edicao ? $produto_para_edicao['cor_cp'] : '' ?>">

            <input type="text" name="texturaProduto" placeholder="Textura"
                value="<?= $modo_edicao ? $produto_para_edicao['textura_cp'] : '' ?>">

            <input type="text" name="materialProduto" placeholder="Material de fabricação"
                value="<?= $modo_edicao ? $produto_para_edicao['material_fabricacao_cp'] : '' ?>">

            <input type="number" name="pesoProduto" placeholder="Peso" step="0.01" min="0"
                value="<?= $modo_edicao ? $produto_para_edicao['peso_cp'] : '' ?>">

            <select name="unidadeMedida" required>
                <option value="">Selecione a unidade</option>
                <?php
                $unidades = ['Kg', 'L', 'm²', 'm³', 'un'];
                $unidade_selecionada = $modo_edicao ? $produto_para_edicao['unidade_cp'] : '';
                foreach ($unidades as $u) {
                    $selected = ($u == $unidade_selecionada) ? 'selected' : '';
                    echo "<option value=\"$u\" $selected>$u</option>";
                }
                ?>
            </select>

            <select name="aplicacaoProduto" required>
                <option value="">Selecione a aplicação</option>
                <?php
                $aplicacoes = ['Fundação', 'Estrutura', 'Acabamento'];
                $aplicacao_selecionada = $modo_edicao ? $produto_para_edicao['aplicacao_cp'] : '';
                foreach ($aplicacoes as $a) {
                    $selected = ($a == $aplicacao_selecionada) ? 'selected' : '';
                    echo "<option value=\"$a\" $selected>" . ucfirst($a) . "</option>";
                }
                ?>
            </select>

            <button type="submit" name="submit"><?= $botao_texto ?></button>

            <?php if ($modo_edicao): ?>
                <button type="button" onclick="window.location.href='index.php'">Cancelar Edição</button>
            <?php endif; ?>

        </form>

        <hr>

        <table id="tabelaProdutos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço (R$)</th>
                    <th>Qtd</th>
                    <th>Validade</th>
                    <th>Cor</th>
                    <th>Textura</th>
                    <th>Material</th>
                    <th>Peso</th>
                    <th>Unidade</th>
                    <th>Aplicação</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($p = $produtos->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $p['id_cp'] ?></td>
                        <td><?= $p['nome_produto_cp'] ?></td>
                        <td><?= number_format($p['preco_cp'], 2, ',', '.') ?></td>
                        <td><?= $p['quantidade_cp'] ?></td>
                        <td><?= $p['data_cp'] ?></td>
                        <td><?= $p['cor_cp'] ?></td>
                        <td><?= $p['textura_cp'] ?></td>
                        <td><?= $p['material_fabricacao_cp'] ?></td>
                        <td><?= $p['peso_cp'] ?></td>
                        <td><?= $p['unidade_cp'] ?></td>
                        <td><?= $p['aplicacao_cp'] ?></td>

                        <td class="acoes">
                            <a href="?editar=<?= $p['id_cp'] ?>">
                                <button>Editar</button>
                            </a>
                            <a href="?excluir=<?= $p['id_cp'] ?>" onclick="return confirm('Tem certeza que deseja excluir o produto <?= $p['nome_produto_cp'] ?>?')">
                                <button class="delete" name='deletar'>Excluir</button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

    <script>
        function buscarProduto() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("busca");
            filter = input.value.toUpperCase();
            table = document.getElementById("tabelaProdutos");
            tr = table.getElementsByTagName("tr");

            // Começa em i = 1 para pular o cabeçalho (thead)
            for (i = 1; i < tr.length; i++) {
                // Pega a coluna do Nome (índice 1)
                td = tr[i].getElementsByTagName("td")[1];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>

</body>

</html>