<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

// Fazer a ligação à Base de Dados
require_once('ligarbd.php');

// Evitar possíveis ataques de injeção de SQL escapando os valores
$nome_utilizador = mysqli_real_escape_string($basedados, $_POST['nome_utilizador']);
$password_utilizador = mysqli_real_escape_string($basedados, $_POST['password_utilizador']);

// Verificar se as credenciais são "semregisto"
if ($nome_utilizador === 'semregisto' && $password_utilizador === 'semregisto') {
    // Definir a mensagem de erro
    $_SESSION['error_message'] = "As credenciais fornecidas são reservadas. Por favor, entre com outras credenciais.";
    
    // Armazenar os valores do formulário nas variáveis de sessão
    $_SESSION['nome_utilizador'] = $nome_utilizador;
    $_SESSION['password_utilizador'] = $password_utilizador;

    // Redirecionar de volta para a página de login com mensagem de erro específica
    header("Location: index_login.php");
    exit;
}

// Consulta preparada para evitar injeção de SQL
$consulta = "SELECT * FROM utilizadores WHERE nome_utilizador=? AND password_utilizador=?";
$stmt = mysqli_prepare($basedados, $consulta);
mysqli_stmt_bind_param($stmt, "ss", $nome_utilizador, $password_utilizador);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

// Verificar se encontrou um registo e se está ativo
if ($registo = mysqli_fetch_array($resultado)) {
    if ($registo['ativo'] == 0) {
        // Definir a mensagem de erro
        $_SESSION['error_message'] = "A sua conta está desativada. Por favor, entre em contacto com o administrador.";
        
        // Armazenar os valores do formulário nas variáveis de sessão
        $_SESSION['nome_utilizador'] = $nome_utilizador;
        $_SESSION['password_utilizador'] = $password_utilizador;

        // Redirecionar de volta para a página de login com mensagem de erro específica
        header("Location: index_login.php");
        exit;
    }

    // Se a conta estiver ativa, configurar a sessão e redirecionar para a página de início
    $_SESSION['nome_utilizador'] = $registo['nome_utilizador'];
    $_SESSION['tipo_utilizador'] = $registo['tipo_utilizador'];
    $_SESSION['id_utilizador'] = $registo['id_utilizador'];

    header("Location: inicio.php");
    exit;
} else {
    // Definir a mensagem de erro
    $_SESSION['error_message'] = "As credenciais fornecidas estão incorretas. Por favor, verifique e tente novamente.";
    
    // Armazenar os valores do formulário nas variáveis de sessão
    $_SESSION['nome_utilizador'] = $nome_utilizador;
    $_SESSION['password_utilizador'] = $password_utilizador;

    // Redirecionar de volta para a página de login com mensagem de erro genérica
    header("Location: index_login.php");
    exit;
}
?>