 const usuario = localStorage.getItem("usuarioLogado");

        if (!usuario) {
            // Se não houver login, volta para tela de login
            window.location.href = "index.php";
        } else {
            document.getElementById("usuarioLogado").textContent = usuario;
        }

        // Logout
        document.getElementById("logoutBtn").addEventListener("click", () => {
            localStorage.removeItem("usuarioLogado");
            window.location.href = "../login/index.php";
        });

        // Botões de navegação
        document.getElementById("cadastroProdutoBtn").addEventListener("click", () => {
            window.location.href = "../cadastro_produto/index.php";
        });

        document.getElementById("gestaoEstoqueBtn").addEventListener("click", () => {
            window.location.href = "../gestao_estoque/index.php";
        });