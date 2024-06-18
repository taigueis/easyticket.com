<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<html>	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style_navbar.css" rel="stylesheet" type="text/css">
	<link rel="icon" href="img/logo_EasyTicket.png">
	<title> EasyTicket</title>
    <nav>
    <ul>
        <li class="logo">
            <a href="inicio.php"><img src="img/easybg1_texto.png" style="width:28%; height:auto;"></a>
        </li>
        <div class="items">
            <?php if(basename($_SERVER['PHP_SELF']) === 'inicio.php'): ?>
                <li class="active"><a id="HorariosLink" href="#horarios" onclick="smoothScroll(event)">Horários</a></li>
            <?php else: ?>
                <li><a href="inicio.php#horarios">Horários</a></li>
            <?php endif; ?>
            <li <?php echo ($current_page === 'bilhetes.php') ? 'class="active"' : ''; ?>>
                <a href="bilhetes.php">Bilhetes</a>
            </li>
            <li <?php echo ($current_page === 'sobre.php') ? 'class="active"' : ''; ?>>
                <a href="sobre.php">Sobre Nós</a>
            </li>
            <li <?php echo ($current_page === 'contacte.php') ? 'class="active"' : ''; ?>>
                <a href="contacte.php">Contacte-nos</a>
            </li>
            <li><a href="index_login.php">Login</a></li>
        </div>
    </ul>
</nav>


	<script>

        // Bloqueia a ação do botão de scroll do rato em hiperlinks para evitar que abram em um novo separador
        window.onload = function() {
     // Desativa o clique com o botão do meio do mouse (scroll) para links
     var links = document.querySelectorAll('a');
    links.forEach(function(link) {
        link.addEventListener('auxclick', function(e) {
            if (e.button === 1) { // Se o scroll for clicado
                e.preventDefault(); // Cancela a ação padrão do clique
            }
        });
    });

        // Desativa o clique com Ctrl + clique esquerdo do mouse em hiperlinks
        var links = document.querySelectorAll('a');
    links.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (e.ctrlKey) { // Se a tecla Ctrl estiver pressionada
                e.preventDefault(); // Cancela a ação padrão do clique
            }
        });
    });

    // Desativa o menu de contexto padrão para links, imagens e inputs
    var elements = document.querySelectorAll('a,img,input');
    elements.forEach(function(element) {
        element.addEventListener('contextmenu', function(e) {
            e.preventDefault(); // Cancela o menu de contexto padrão
        });
    });

    // Desativa a funcionalidade de colar (paste) para inputs
    var inputs = document.querySelectorAll('input');
    inputs.forEach(function(input) {
        input.addEventListener('paste', function(e) {
            e.preventDefault(); // Cancela a ação de colar
        });
    });

    // Desativa Ctrl+V globalmente para inputs
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'v') {
            var activeElement = document.activeElement;
            if (activeElement.tagName === 'INPUT') {
                e.preventDefault(); // Cancela a ação de colar
            }
        }
    });
};

// Função para rolar suavemente para o destino do link
function smoothScroll(event) {
    event.preventDefault();
    const href = event.target.getAttribute('href');
    const targetId = href.substring(href.indexOf('#') + 1);
    const targetElement = document.getElementById(targetId);
    if (targetElement) {
        window.scrollTo({
            top: targetElement.offsetTop,
            behavior: 'smooth'
        });
    }
}
</script>
</html>