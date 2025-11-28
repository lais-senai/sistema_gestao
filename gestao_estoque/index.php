<?php
session_start();

// =====================================
// CONFIGURAÇÃO DO BANCO
// =====================================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "almoxarifado";

// Habilita relatório de erros do banco para facilitar debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Erro ao conectar: " . $conn->connect_error);
}

// Variável para mensagens
$mensagem = "";

// =====================================
// 1. REGISTRAR MOVIMENTAÇÃO (POST)
// =====================================
if (isset($_POST['registrar'])) {

  $produto_id = $_POST['produtoSelect'];
  $tipo       = $_POST['tipoMovimentacao'];
  $quantidade = intval($_POST['quantidadeMov']); // Garante que é número inteiro
  $data       = $_POST['dataMovimentacao'];

  // Variável de controle para saber se podemos prosseguir
  $podeRegistrar = true;

  // --- VERIFICAÇÃO DE ESTOQUE (IMPEDIR SALDO NEGATIVO) ---
  if ($tipo === "saida") {
    // Busca o estoque atual do produto
    $queryCheck = $conn->prepare("SELECT quantidade_cp FROM cadastro_produtos WHERE id_cp = ?");
    $queryCheck->bind_param("i", $produto_id);
    $queryCheck->execute();
    $resultCheck = $queryCheck->get_result();
    $dadosProduto = $resultCheck->fetch_assoc();

    if ($dadosProduto && $quantidade > $dadosProduto['quantidade_cp']) {
      $mensagem = "<script>alert('ERRO: Estoque insuficiente! Você tem {$dadosProduto['quantidade_cp']} e tentou retirar {$quantidade}.');</script>";
      $podeRegistrar = false;
    } else {
      // Se tem saldo, torna o número negativo para o cálculo
      $quantidade = -$quantidade;
    }
  }

  if ($podeRegistrar) {
    // REGISTRA NA TABELA gestao_estoque
    // CORREÇÃO: 4 colunas = 4 interrogações (?)
    $sql = "INSERT INTO gestao_estoque 
                (tipo_movimentacao_ge, quantidade_ge, data_ge, cadastro_produtos_id)
                VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // CORREÇÃO: 'sisi' -> string, integer, string, integer
    $stmt->bind_param("sisi", $tipo, $quantidade, $data, $produto_id);

    if ($stmt->execute()) {
      // Atualiza quantidade em cadastro_produtos (Usando Prepared Statement para segurança)
      $sqlUpdate = "UPDATE cadastro_produtos SET quantidade_cp = quantidade_cp + ? WHERE id_cp = ?";
      $stmtUpdate = $conn->prepare($sqlUpdate);
      $stmtUpdate->bind_param("ii", $quantidade, $produto_id);
      $stmtUpdate->execute();

      // Verifica estoque mínimo após atualização
      $queryVerify = $conn->prepare("SELECT quantidade_cp FROM cadastro_produtos WHERE id_cp = ?");
      $queryVerify->bind_param("i", $produto_id);
      $queryVerify->execute();
      $resultVerify = $queryVerify->get_result();
      $dadosVerify = $resultVerify->fetch_assoc();

      if($dadosVerify['quantidade_cp'] <= 5){
        $mensagem = "<script>alert('⚠️ Atenção: o estoque está abaixo ou igual ao mínimo (5).');</script>";
      } else {
        $mensagem = "<script>alert('Movimentação registrada com sucesso!');</script>";
      }
    } else {
      $mensagem = "<script>alert('Erro ao registrar movimentação!');</script>";
    }
  }
}

// =====================================
// 2. CARREGAR DADOS (SELECTS)
// =====================================
// Carrega lista para o Select
$produtos = $conn->query("SELECT id_cp, nome_produto_cp FROM cadastro_produtos ORDER BY nome_produto_cp ASC");

// Carrega tabela de estoque atualizado
$estoque = $conn->query("SELECT * FROM cadastro_produtos ORDER BY nome_produto_cp ASC");

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Estoque</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php if (!empty($mensagem)) echo $mensagem; ?>

  <header>
    <h2>Gestão de Estoque</h2>
    <button onclick="window.location.href='../tela_inicial/index.php'">Voltar ao Menu Principal</button>
  </header>

  <div class="container">
    <h3>Movimentação de Estoque</h3>

    <form method="POST">

      <label for="produtoSelect">Selecione o produto:</label>
      <select name="produtoSelect" id="produtoSelect" required>
        <option value="">-- Escolher --</option>
        <?php while ($p = $produtos->fetch_assoc()) { ?>
          <option value="<?= $p['id_cp'] ?>">
            <?= $p['nome_produto_cp'] ?>
          </option>
        <?php } ?>
      </select>

      <label for="tipoMovimentacao">Tipo de movimentação:</label>
      <select name="tipoMovimentacao" id="tipoMovimentacao" required>
        <option value="">-- Selecione --</option>
        <option value="entrada">Entrada</option>
        <option value="saida">Saída</option>
      </select>

      <label for="quantidadeMov">Quantidade:</label>
      <input type="number" name="quantidadeMov" id="quantidadeMov" placeholder="Quantidade" min="1" required>

      <label for="dataMovimentacao">Data da movimentação:</label>
      <input type="date" name="dataMovimentacao" id="dataMovimentacao" required>

      <button type="submit" name="registrar">Registrar Movimentação</button>

    </form>

    <hr>

    <h3>Estoque Atual</h3>
    <table id="tabelaEstoque">
      <thead>
        <tr>
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
        </tr>
      </thead>

      <tbody>
        <?php while ($e = $estoque->fetch_assoc()) { ?>
          <tr style="<?= $e['quantidade_cp'] <= 0 ? 'background-color: #ffcccc;' : '' ?>">
            <td><?= $e['nome_produto_cp'] ?></td>
            <td><?= number_format($e['preco_cp'], 2, ',', '.') ?></td>
            <td><?= $e['quantidade_cp'] ?></td>
            <td><?= $e['data_cp'] ?></td>
            <td><?= $e['cor_cp'] ?></td>
            <td><?= $e['textura_cp'] ?></td>
            <td><?= $e['material_fabricacao_cp'] ?></td>
            <td><?= $e['peso_cp'] ?></td>
            <td><?= $e['unidade_cp'] ?></td>
            <td><?= $e['aplicacao_cp'] ?></td>
          </tr>
        <?php } ?>
      </tbody>

    </table>

  </div>

</body>

</html>