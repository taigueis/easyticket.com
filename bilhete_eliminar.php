<?php
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit();
}

require_once('menu.php');

require_once('ligarbd.php');

$id_bilhete = $_GET['id'];

require_once ('bilhetes.php');

echo "<script>window.location.href = 'bilhetes.php';</script>";
exit();
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