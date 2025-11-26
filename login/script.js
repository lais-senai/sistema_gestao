function validarCadastro() {
    // e.defaultPrevented;
    var nome = document.getElementById("nome").value.trim();
    var email = document.getElementById("email").value.trim();
    var senha = document.getElementById("senha").value.trim();


    // Validação Nome: só letras e espaços (com acentos)
    var regexNome = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/;
    if (nome === "" || !regexNome.test(nome)) {
        alert("Nome inválido! Use apenas letras e espaços.");
        document.getElementById("nome").focus();
        return false;
    }
    // Validação Email: formato básico
    var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === "" || !regexEmail.test(email)) {
        alert("Email inválido! Use um formato como exemplo@dominio.com.");
        document.getElementById("email").focus();
        return false;
    }

    // Validação Senha Forte
    var regexSenha = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\W]{8,}$/;
    if (senha.length < 8 || !regexSenha.test(senha)) {
        alert("Senha fraca! (Requisitos: Mínimo de 8 caracteres, com pelo menos uma Maiúscula, uma Minúscula e um Número).");
        document.getElementById("senha").focus();
        return false;
    }

    // Se tudo estiver certo
    alert("Cadastro validado com sucesso!");

    // Abre a nova página em uma nova aba
    window.location.href = './tela_inicial/index.php';

    // Impede que o formulário recarregue a página atual
    return false;

}