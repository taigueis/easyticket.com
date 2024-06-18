<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css">
	<link rel="icon" href="img/logo_EasyTicket.png">
    <title>EasyTicket</title>
</head>
<body>
    <?php
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
    <section id="contacte">
  <p>Contacte-nos</p>
  <br>
  <p>Estamos aqui para ajudar! Se tiver alguma dúvida, comentário ou se precisar de assistência, não hesite em contactar-nos através dos seguintes meios:</p>
  <br>
  <ul>
    <li>
      <img src="https://img.icons8.com/fluency/40/mail--v1.png" />
      <a href="mailto:infoeasyticket.com@gmail.com" target="_blank">infoeasyticket.com@gmail.com</a>
    </li>
    <li>
      <img src="https://img.icons8.com/fluency/40/phone--v1.png" />
      <span>+351 252 456 789</span>
    </li>
    <li>
      <img src="https://img.icons8.com/color/40/pin.png" />
      <span>Póvoa de Varzim</span>
    </li>
  </ul>
  <br><br>
  <center>
    <p>Teremos todo o gosto em responder às suas questões.<br>
    Agradecemos a sua preferência pela <strong>EasyTicket</strong>!</p>
  </center>
</section>
</body>
</html>