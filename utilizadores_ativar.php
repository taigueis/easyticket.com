<?php
require_once('ligarbd.php'); 

if(isset($_GET['id'])) {
    $id_utilizador = $_GET['id'];
    
    // Execute a consulta para atualizar o status do utilizador para ativo
    $consulta_ativar = "UPDATE utilizadores SET ativo = '1' WHERE id_utilizador = $id_utilizador";
    mysqli_query($basedados, $consulta_ativar);
}

// Redirecionar de volta para a página de dados de utilizador
header("Location: utilizadores.php");
exit();
?>