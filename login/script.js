function validarLogin() {
    var email = document.getElementById("email").value.trim();
    var senha = document.getElementById("senha").value.trim();

    // Validação Email: formato básico
    var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === "" || !regexEmail.test(email)) {
        alert("Email inválido! Use um formato como exemplo@dominio.com.");
        document.getElementById("email").focus();
        return false;
    }

    // Validação Senha
    if (senha.length < 8) {
        alert("Senha deve ter no mínimo 8 caracteres.");
        document.getElementById("senha").focus();
        return false;
    }

    // Se tudo estiver certo, permite envio do formulário
    return true;
}