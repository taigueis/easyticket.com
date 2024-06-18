      <?php
        // --- Iniciar sessão ---
session_name("sessao");

// Verificar se a sessão já está iniciada
if (session_id() === '') {
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
          
        $id_autocarro = $_GET['id'];
           
          
        require_once ('autocarros.php');
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