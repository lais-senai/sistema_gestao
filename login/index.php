<?php
session_start();

// =============================
// CONFIGURAÇÃO DO BANCO
// =============================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "almoxarifado"; // coloque o nome correto do seu banco

$conn = mysqli_connect($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro ao conectar: " . $conn->connect_error);
}

// =============================
// SE O FORM FOI ENVIADO
// =============================
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    // Validações básicas
    if (empty($email) || empty($senha)) {
        $erro = "Email e senha são obrigatórios.";
    } else {
        // BUSCAR USUÁRIO NA TABELA cadastro_usuario
        $sql = "SELECT id_cadastro, nome_usuario, email_usuario FROM cadastro_usuario WHERE email_usuario = ? AND senha_usuario = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            $erro = "Erro ao preparar consulta: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $email, $senha);
            $stmt->execute();
            $result = $stmt->get_result();

            // SE ENCONTROU USUÁRIO
            if ($result->num_rows === 1) {
                $usuario = $result->fetch_assoc();

                // Criar sessão com dados do usuário
                $_SESSION['id_usuario'] = $usuario['id_cadastro'];
                $_SESSION['nome_usuario'] = $usuario['nome_usuario'];
                $_SESSION['email_usuario'] = $usuario['email_usuario'];

                // Redireciona para a tela inicial
                header("Location: ../tela_inicial/index.php");
                exit;
            } else {
                $erro = "Email ou senha incorretos.";
            }
            
            $stmt->close();
        }
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div id="centralizar">

        <form method="POST" onsubmit="return validarLogin()">

            <legend>Login de Usuário</legend>
            <br><br>

            <?php if (!empty($erro)): ?>
                <p style="color:red; font-weight:bold;"><?= $erro ?></p>
            <?php endif; ?>

            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" required><br><br>

            <label for="senha">Senha:</label><br>
            <input type="password" name="senha" id="senha" required><br><br>

            <button type="submit">Entrar</button>
            <button type="button" onclick="window.location.href='../cadastro_usuario/cadastro_usuario.php'">Criar Conta</button>

        </form>

    </div>

    <script src="script.js"></script>

</body>

</html>