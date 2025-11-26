function validarCadastro() {
    var nome = document.getElementById("nome").value.trim();
    var email = document.getElementById("email").value.trim();
    var senha = document.getElementById("senha").value.trim();
    var confirma_senha = document.getElementById("confirma_senha").value.trim();

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

    // Validação Senha (mínimo 8 caracteres)
    if (senha.length < 8) {
        alert("Senha deve ter no mínimo 8 caracteres.");
        document.getElementById("senha").focus();
        return false;
    }

    // Validação Confirmação de Senha
    if (senha !== confirma_senha) {
        alert("As senhas não correspondem.");
        document.getElementById("confirma_senha").focus();
        return false;
    }

    // Se tudo estiver certo, permite envio do formulário
    return true;
}