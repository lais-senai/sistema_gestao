<?php
// index.php (Menu Principal - Localizado em C:\...\tela_inicial\)

// 1. Inicia a sessão
session_start();

// 2. Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    // Se não estiver logado, redireciona para a tela de login
    header("Location: ../login/index.php");
    exit;
}

// 3. Lógica de Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login/index.php");
    exit;
}

// 4. Pega o nome do usuário da sessão
$nomeUsuario = isset($_SESSION['nome_usuario']) ? htmlspecialchars($_SESSION['nome_usuario']) : 'Usuário';

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
            Usuário: <span><?php echo $nomeUsuario; ?></span>

            <button id="logoutBtn" onclick="window.location.href='?logout=true'">Logout</button>

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