mostrarSenha = function() {
    const campo = document.getElementById('senha');
    if (campo.type === 'password') {
        campo.type = 'text';
    } else {
        campo.type = 'password';
    }
}