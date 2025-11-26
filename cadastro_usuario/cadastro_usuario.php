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
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $confirma_senha = trim($_POST['confirma_senha'] ?? '');

    // ========== VALIDAÇÕES ==========
    
    // Validar se campos estão preenchidos
    if (empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
        $erro = "Todos os campos são obrigatórios.";
    }
    
    // Validar formato do email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    }
    
    // Validar se senhas são iguais
    elseif ($senha !== $confirma_senha) {
        $erro = "As senhas não correspondem.";
    }
    
    // Validar comprimento da senha
    elseif (strlen($senha) < 8) {
        $erro = "Senha deve ter no mínimo 8 caracteres.";
    }
    
    // Se passou nas validações
    else {
        // Verificar se email já existe
        $sql_check = "SELECT id_cadastro FROM cadastro_usuario WHERE email_usuario = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $erro = "Este email já está cadastrado.";
        } else {
            // Inserir novo usuário
            $sql = "INSERT INTO cadastro_usuario (nome_usuario, email_usuario, senha_usuario) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                $erro = "Erro ao preparar consulta: " . $conn->error;
            } else {
                $stmt->bind_param("sss", $nome, $email, $senha);
                
                if ($stmt->execute()) {
                    $sucesso = true;
                    // Redireciona para o login após 2 segundos
                    header("refresh:2;url=../login/index.php");
                } else {
                    $erro = "Erro ao cadastrar usuário: " . $stmt->error;
                }
                
                $stmt->close();
            }
        }
        
        $stmt_check->close();
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

        <form method="POST" onsubmit="return validarCadastro()">

            <legend>Cadastro de Usuário</legend>
            <br><br>

            <?php if ($sucesso): ?>
                <p style="color:green; font-weight:bold;">Cadastro realizado com sucesso! Redirecionando para login...</p>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <p style="color:red; font-weight:bold;"><?= $erro ?></p>
            <?php endif; ?>

            <label for="nome">Nome:</label><br>
            <input type="text" name="nome" id="nome" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" required><br><br>

            <label for="senha">Senha:</label><br>
            <input type="password" name="senha" id="senha" required><br><br>

            <label for="confirma_senha">Confirmar Senha:</label><br>
            <input type="password" name="confirma_senha" id="confirma_senha" required><br><br>

            <button type="submit">Cadastrar</button>
            <button type="button" onclick="window.location.href='../login/index.php'">Voltar ao Login</button>

        </form>

    </div>

    <script src="script.js"></script>

</body>

</html>