<?php
session_name("sessao");
session_start();

if (isset($_SESSION['tipo_utilizador'])) {

	
    // Destruir a sessão
    session_unset();    // Limpa todas as variáveis da sessão
    session_destroy();  // Destrói a sessão
	
	$_SESSION['tipo_utilizador'] = "semregisto";

    // Certificar-se de que não é possível fazer o cache da página
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Data no passado
}

// Redireciona para a página de início
header("Location: inicio.php");
exit();
?>
