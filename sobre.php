<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="style.css" rel="stylesheet" type="text/css">
	<link rel="icon" href="img/logo_EasyTicket.png">
    <title>EasyTicket</title>
</head>
<body>
<?php

		// --- Chamar menu ---
if (!isset($_SESSION)) {
    session_name("sessao");
    ob_start();
    session_start();
}

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

if ($_SESSION['tipo_utilizador'] == 'user') {        
    require_once('menu2.php');
    }
elseif ($_SESSION['tipo_utilizador'] == 'semregisto') {        
    require_once('menu3.php');
}
?>
	<br><br>
    <section id="sobre">
        <p id="titulo">Sobre Nós</p>
        <p>Bem-vindo à <strong> EasyTicket</strong>, a sua plataforma dedicada à simplificação das suas viagens de autocarro. Proporcionamos uma experiência de compra de bilhetes intuitiva e eficiente, oferecendo também uma visão abrangente dos horários.</p>
        
        <p>Nós acreditamos na conveniência e na mobilidade sem complicações. <br> Com apenas alguns cliques, pode planear as suas viagens e verificar horários em tempo real.</p>

        <p>Descubra a facilidade de viajar connosco, onde a nossa missão é tornar a sua jornada mais simples, segura e agradável. <br> <br> Obrigado por escolher a <strong> EasyTicket </strong> para as suas viagens!</p>
    </section>
</body>
</html>
