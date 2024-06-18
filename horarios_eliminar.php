<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

// --- Chamar menu ---
require_once('menu.php');
   
// --- Fazer a ligação à Base de Dados ---
require_once('ligarbd.php');
   
// Receber e editar o id do autocarro a remover --------------------
  
$id_viagem = $_GET['id'];
  
$linha_autocarro = isset($_GET['linha_autocarro']) ? $_GET['linha_autocarro'] : '';
   
// --- Eliminar o registo ---
               
$remover = "DELETE FROM viagem WHERE id_viagem='$id_viagem'";
$sucesso = mysqli_query($basedados, $remover);
   
if (!$sucesso) {
    echo 'Erro ao eliminar os dados!'."<br>";
    exit;
} else {
}
     
require_once("linha_" . $linha_autocarro . ".php");
?>
<html>
<head>
    <title> EasyTicket </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
</head>
<body>    
</body>
</html>