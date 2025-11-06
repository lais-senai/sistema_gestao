 const usuario = localStorage.getItem("usuarioLogado");

        if (!usuario) {
            // Se não houver login, volta para tela de login
            window.location.href = "login.html";
        } else {
            document.getElementById("usuarioLogado").textContent = usuario;
        }

        // Logout
        document.getElementById("logoutBtn").addEventListener("click", () => {
            localStorage.removeItem("usuarioLogado");
            window.location.href = "../login/index.html";
        });

        // Botões de navegação
        document.getElementById("cadastroProdutoBtn").addEventListener("click", () => {
            window.location.href = "../cadastro_produto/index.html";
        });

        document.getElementById("gestaoEstoqueBtn").addEventListener("click", () => {
            window.location.href = "../gestao_estoque/index.html";
        });