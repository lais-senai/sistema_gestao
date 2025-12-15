<?php
session_start();

// =============================
// CONFIGURAÇÃO DO BANCO
// =============================
// $host = "localhost";
// $user = "root";
// $pass = "";
// $db   = "almoxarifado"; // coloque o nome correto do seu banco

// $conn = mysqli_connect($host, $user, $pass, $db);

// if ($conn->connect_error) {
//     die("Erro ao conectar: " . $conn->connect_error);
// }

// // =============================
// // SE O FORM FOI ENVIADO
// // =============================
// $erro = "";

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {

//     $nome  = $_POST['nome'];
//     $email = $_POST['email'];
//     $senha = $_POST['senha'];

//     // BUSCAR USUÁRIO
//     $sql = "SELECT * FROM login WHERE email_login = ? AND senha_login = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("ss", $email, $senha);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     // SE ENCONTROU USUÁRIO
//     if ($result->num_rows === 1) {
//         $usuario = $result->fetch_assoc();

//         // Criar sessão
//         $_SESSION['login_id'] = $usuario['id_login'];
//         $_SESSION['nome_login'] = $usuario['nome_login'];

//         // Redireciona para o menu principal
//         header("Location: index.php");
//         exit;
//     } else {
//         $erro = "Email ou senha incorretos.";
//     }
// }

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./login/style.css">
</head>

<body>

    <div id="centralizar">

        <form method="POST" onsubmit="return validarCadastro()">

            <legend>Login de Usuário PHP</legend>
            <br><br>

            <?php if (!empty($erro)): ?>
                <p style="color:red; font-weight:bold;"><?= $erro ?></p>
            <?php endif; ?>

            <label for="nome">Nome:</label><br>
            <input type="text" name="nome" id="nome" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" required><br><br>

            <label for="senha">Senha:</label><br>
            <input type="password" name="senha" id="senha" required><br><br>

            <button type="submit">Entrar</button>

        </form>

    </div>

    <script src="./login/script.js"></script>

</body>

</html>