<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

require_once('ligarbd.php');

$nome_utilizador = 'semregisto';
$password_utilizador = 'semregisto';

$consulta = "SELECT * FROM utilizadores WHERE nome_utilizador='$nome_utilizador' AND password_utilizador='$password_utilizador'";
$resultado = mysqli_query($basedados, $consulta); 
$nregistos = mysqli_num_rows($resultado); 
$registo = mysqli_fetch_array($resultado); 

if ($nregistos == 1) {
    $_SESSION['nome_utilizador'] = $registo['nome_utilizador'];
    $_SESSION['tipo_utilizador'] = $registo['tipo_utilizador'];
    $_SESSION['id_utilizador'] = $registo['id_utilizador'];

    header("Location: inicio.php");
    exit(); 
} else {
    require_once('menu3.php');
}
?>
<html>
<head>
</head>
<body>
</body>
</html>