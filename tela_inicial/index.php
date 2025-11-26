<?php
// index.php (Menu Principal - Localizado em C:\...\tela_inicial\)

// 1. Inicia a sessão
session_start();

// =====================================
// 2. CONFIGURAÇÃO E CONEXÃO COM O BANCO (MySQLi)
//    (DEFINIDO DIRETAMENTE AQUI, JÁ QUE conexao.php NÃO EXISTE)
// =====================================
$host = "localhost";
$user = "root";
$pass = ""; // Sua senha do MySQL
$db   = "almoxarifado"; // Nome do seu banco de dados

// Tenta estabelecer a conexão
$conn = mysqli_connect($host, $user, $pass, $db);

if ($conn->connect_error) {
    // Se houver erro, para o script
    die("Desculpe, não foi possível conectar ao banco de dados. Erro: " . $conn->connect_error);
}

// // 3. Verificar se o usuário está logado
// if (!isset($_SESSION['id_login'])) {
//     // Se não estiver logado, redireciona para a tela de login
//     // Certifique-se de que a tela de login está na mesma pasta (login.php)
//     header("Location: ../login/index.php");
//     exit;
// }

// $idLogin = $_SESSION['id_login'];

// ==================================================
// 4. Buscar dados do usuário (MySQLi Prepared Statement para segurança)
// ==================================================
$sql = "SELECT nome_login FROM login WHERE id_login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idLogin); // 'i' para integer
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// // Se por algum motivo o login não existir mais (segurança extra)
// if (!$usuario) {
//     session_destroy();
//     header("Location: ../login/index.php");
//     exit;
// }

// // 5. Lógica de Logout
// if (isset($_GET['logout'])) {
//     session_destroy();
//     header("Location: ../login/index.php");
//     exit;
// }

$conn->close(); // Fecha a conexão após buscar os dados
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <h2>Sistema de Gestão</h2>
        <div class="user-info">
            Usuário: <span> Pessoa </span>
            <!-- Logica de aparecer o usuario ao logar -> echo htmlspecialchars($usuario['nome_login']);  -->

            <button id="logoutBtn" onclick="window.location.href='../'">Logout</button>

        </div>
    </header>

    <div class="container">
        <h3>Bem-vindo ao Sistema!</h3>
        <p>Escolha uma das opções abaixo:</p>

        <form action="../cadastro_produto/index.php" method="GET">
            <button type="submit">Cadastro de Produto</button>
        </form>

        <form action="../gestao_estoque/index.php" method="GET">
            <button type="submit">Gestão de Estoque</button>
        </form>
    </div>
</body>

</html>