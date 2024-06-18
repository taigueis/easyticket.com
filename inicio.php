<?php
if (!isset($_SESSION)) {
    session_name("sessao");
    ob_start();
    session_start();
}

require_once('ligarbd.php');

// Redirecionar se o utilizador não estiver logado
if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit();
}

if ($_SESSION['tipo_utilizador'] == 'admin') {    
    require_once('menu.php');
} elseif ($_SESSION['tipo_utilizador'] == 'user') {        
    require_once('menu2.php');
} elseif ($_SESSION['tipo_utilizador'] == 'semregisto') {        
    require_once('menu3.php');
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <link rel="icon" href="img/logo_EasyTicket.png">
    <title>EasyTicket</title>
</head>
<body>
   <div class="image-overlay">
	<img src="img/black_bus.jpg" class="full-size">
		<div class="text-overlay">
			<h1>Bem-vindo ao EasyTicket</h1>   
			<p>A sua plataforma preferida para a compra de bilhetes de autocarro!</p>
		</div>
	</div>
	
    <div class="section-divider" id="horarios"></div>
    
		<h1>Horários</h1>
        <br><br>
	<div class="horarios-sections">
        <!-- Secção 1 -->
        <div class="horario-section">
            <h2>Linha 3001</h2>
            <p>Póvoa de Varzim (Paranho) <br> Vila do Conde (Estação Portas Fronhas)</p>
			<button class="ver-mais-button" onclick="redirectToPage('linha_3001.php')">Ver Mais</button>
        </div>

        <!-- Secção 2 -->
        <div class="horario-section">
            <h2>Linha 3002</h2>
            <p>Póvoa de Varzim (Torrinha) <br> Vila do Conde (Estação Portas Fronhas)</p>
			<button class="ver-mais-button" onclick="redirectToPage('linha_3002.php')">Ver Mais</button>
        </div>

        <!-- Secção 3 -->
        <div class="horario-section">
            <h2>Linha 3301</h2>
            <p>RU Póvoa de Varzim (Aguçadoura) <br> Vila do Conde (Estação)</p>
			<button class="ver-mais-button" onclick="redirectToPage('linha_3301.php')">Ver Mais</button>
        </div>

        <!-- Secção 4 -->
        <div class="horario-section">
            <h2>Linha 3302</h2>
            <p>RU Póvoa de Varzim (Z.I. Amorim) <br> Vila do Conde (Estação Varziela)</p>
			<button class="ver-mais-button" onclick="redirectToPage('linha_3302.php')">Ver Mais</button>
        </div>
    </div>
    <br><br><br>
<!-- Verificar se o utilizador não é do tipo "admin" -->
<?php if ($_SESSION['tipo_utilizador'] !== 'admin'): ?>
    <!-- Footer -->
    <footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>Mapa do Site</h3>
            <ul>
                <li><a style="cursor: pointer;" onclick="scrollToTop()">Início</a></li>
                <li><a style="cursor: pointer;" onclick="scrollToHorarios()">Horários</a></li>
                <li><a href="bilhetes.php">Bilhetes</a></li>
                <li><a href="sobre.php">Sobre Nós</a></li>
                <li><a href="contacte.php">Contacte-nos</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contactos</h3>
            <ul style="font-size: 18px; font-weight: 400; list-style-type: none;">
                <li>Email: &nbsp  <a href="https://mail.google.com/mail/?view=cm&fs=1&to=infoeasyticket.com@gmail.com" target="_blank">infoeasyticket.com@gmail.com</a></li>
                <br>
                <li>Telefone: &nbsp  +351 252 456 789</li>
                <br>
                <li>Morada: &nbsp Póvoa de Varzim</li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Siga-nos nas Redes Sociais</h3>
            <ul class="example-2">
                <li class="icon-content">
                    <a href="https://www.facebook.com/profile.php?id=61560032728057" aria-label="Facebook" data-social="facebook" target="_blank">
                        <div class="filled"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z"/></svg>
                    </a>
                    <div class="tooltip">Facebook</div>
                </li>
                <li class="icon-content">
                    <a href="https://www.instagram.com/easyticket_pvz" aria-label="Instagram" data-social="instagram" target="_blank">
                        <div class="filled"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
                    </a>
                    <div class="tooltip">Instagram</div>
                </li>
                <li class="icon-content">
                    <a href="https://twitter.com/easyticket_pvz" aria-label="Twitter" data-social="twitter" target="_blank">
                        <div class="filled"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
                    </a>
                    <div class="tooltip">Twitter</div>
                </li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
    &copy; <?php echo date("Y"); ?> <span class="brand-name">EasyTicket.</span> <span class="rights">Todos os direitos reservados.</span>
</div>
</footer>
<?php endif; ?>


<script>
    
    document.addEventListener("DOMContentLoaded", function() {
        function applyLineBreak() {
            const rights = document.querySelector(".footer-bottom .rights");

            if (window.innerWidth <= 500) {
                rights.style.display = "block";
                rights.style.marginTop = "5px";  
            } else {
                rights.style.display = "inline";
                rights.style.marginTop = "0";
            }
        }

        applyLineBreak();

        window.addEventListener("resize", applyLineBreak);
    });


    function redirectToPage(page) {
        window.location.href = page;
    }

        function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }


    function scrollToHorarios() {
        if (window.location.pathname.endsWith('inicio.php')) {
            const horariosElement = document.getElementById('horarios');
            if (supportsSmoothScroll()) {
                horariosElement.scrollIntoView({ behavior: 'smooth' });
            } else {
                horariosElement.scrollIntoView();
            }
        }
    }

    function supportsSmoothScroll() {
        return 'scrollBehavior' in document.documentElement.style;
    }
</script>
</body>
</html>