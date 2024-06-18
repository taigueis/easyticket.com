<?php
require_once('ligarbd.php'); 

if(isset($_GET['id'])) {
    $id_utilizador = $_GET['id'];
    
    // Execute a consulta para atualizar o status do utilizador para inativo
    $consulta_desativar = "UPDATE utilizadores SET ativo = '0' WHERE id_utilizador = $id_utilizador";
    mysqli_query($basedados, $consulta_desativar);
}

// Redirecionar de volta para a página de dados de utilizador
header("Location: utilizadores.php");
exit();
?>
