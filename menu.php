<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

// Redirecionar se o utilizador não estiver logado
if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style_navbar.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <title>EasyTicket</title>
</head>
<body>

<nav>
    <ul>
        <li class="logo">
            <a href="inicio.php"><img src="img/easybg1_texto.png" style="width:25%; height:auto;"></a>
        </li>
        <div class="items">
            <li <?php echo ($current_page === 'inicio.php') ? 'class="active"' : ''; ?>>
                <a id="HorariosLink" href="inicio.php#horarios" <?php if ($current_page === 'inicio.php') echo 'onclick="smoothScroll(event)"'; ?>>Horários</a>
            </li>
            <li <?php echo (strpos($current_page, 'bilhete') === 0) ? 'class="active"' : ''; ?>>
                <a href="bilhetes.php">Bilhetes</a>
            </li>
            <li <?php echo (strpos($current_page, 'utilizador') === 0) ? 'class="active"' : ''; ?>>
                <a href="utilizadores.php">Utilizadores</a>
            </li>
        </div>
        <img style="cursor: pointer;" class="admin" src="https://img.icons8.com/ios-glyphs/50/FFFFFF/user--v1.png" id="accountIcon">
        <div class="MiniMenuAdmin" id="MiniMenu">
            <span style="cursor: default;"><?php echo $_SESSION['nome_utilizador']; ?></span>
            <hr class="line">
            <?php if ($current_page !== 'perfil.php'): ?>
            <span id="DefinicoesConta">Conta</span>
            <?php endif; ?>
            <span id="Sair">Sair</span>
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

 function scrollToTop() {
    window.location.href = 'inicio.php';
}

// Função para redirecionar para a página de início e rolar suavemente até aos horários após 2 segundos
function redirectAndDelay() {
    setTimeout(function() {
        window.location.href = 'inicio.php#horarios';
    }, 2000);
}

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

    var miniMenu = document.getElementById('MiniMenu');
    var accountIcon = document.getElementById('accountIcon');
    var overlay = document.getElementById('overlay');
    var miniPagina = document.getElementById('MiniPagina');

    accountIcon.addEventListener('mouseover', function () {
        this.style.cursor = 'pointer';
    });

    accountIcon.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleMiniMenu();
    });

    document.addEventListener('click', function () {
        hideMiniMenu();
        fecharMiniPagina();
    });

    miniMenu.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    document.getElementById('Sair').addEventListener('click', function () {
        confirmasair(); // Chama a função para mostrar o pop-up de confirmação 
    });

    // Verificar se o nome do arquivo atual é diferente de "perfil.php" antes de chamar a função confirmaperfil()
    var currentPage = window.location.pathname.split("/").pop(); // Obtém o nome do arquivo atual
    if (currentPage !== 'perfil.php') {
        document.getElementById('DefinicoesConta').addEventListener('click', function (e) {
            e.stopPropagation();
            toggleMiniMenu();
            confirmaperfil(); // Chama a função para mostrar o pop-up de confirmação apenas se não estiver na página perfil.php
        });
    }

// Função para mostrar o pop-up de confirmação de saída
function confirmasair() {
        var overlay = document.createElement("div");
        overlay.className = "confirm-overlay";

        var box = document.createElement("div");
        box.className = "confirm-box";

        var question = document.createElement("p");
        question.className = "card-heading"; // Aplicando estilo para o cabeçalho
        question.innerText = "Tem a certeza que deseja sair?";
        box.appendChild(question);

        var space = document.createElement("div");
        space.style.height = "25px"; // Adicionando espaço entre a pergunta e os botões
        box.appendChild(space);

        var buttonWrapper = document.createElement("div");
        buttonWrapper.className = "card-button-wrapper";

        var cancelButton = document.createElement("button");
        cancelButton.className = "card-button primary-inserir";
        cancelButton.innerText = "Sim";
        cancelButton.onclick = function () {
            document.location.href = 'logout.php';
        };
        buttonWrapper.appendChild(cancelButton);

        var deleteButton = document.createElement("button");
        deleteButton.className = "card-button primary-eliminar";
        deleteButton.innerText = "Não";
        deleteButton.onclick = function () {
            document.body.removeChild(overlay);
        };
        buttonWrapper.appendChild(deleteButton);

        box.appendChild(buttonWrapper);

        overlay.appendChild(box);
        document.body.appendChild(overlay);
    }

        // Função para mostrar o pop-up de confirmação de abertura do perfil
        function confirmaperfil() {
        var overlay = document.createElement("div");
        overlay.className = "confirm-overlay";

        var box = document.createElement("div");
        box.className = "confirm-box";

        var question = document.createElement("p");
        question.className = "card-heading"; // Aplicando estilo para o cabeçalho
        question.innerText = "Tem a certeza que deseja visualizar o seu perfil?";
        box.appendChild(question);

        var space = document.createElement("div");
        space.style.height = "25px"; // Adicionando espaço entre a pergunta e os botões
        box.appendChild(space);

        var buttonWrapper = document.createElement("div");
        buttonWrapper.className = "card-button-wrapper";

        var deleteButton = document.createElement("button");
        deleteButton.className = "card-button primary-inserir";
        deleteButton.innerText = "Sim";
        deleteButton.onclick = function () {
            document.location.href = 'perfil.php';
        };
        buttonWrapper.appendChild(deleteButton);

        var cancelButton = document.createElement("button");
        cancelButton.className = "card-button primary-eliminar";
        cancelButton.innerText = "Não";
        cancelButton.onclick = function () {
            document.body.removeChild(overlay);
        };
        buttonWrapper.appendChild(cancelButton);

        box.appendChild(buttonWrapper);

        overlay.appendChild(box);
        document.body.appendChild(overlay);
    }

    // Função para mostrar/ocultar o MiniMenu
    function toggleMiniMenu() {
        miniMenu.classList.toggle('show');
    }

    // Função para ocultar o MiniMenu
    function hideMiniMenu() {
        miniMenu.classList.remove('show');
    }

    //MiniPagina
    document.getElementById('overlay').addEventListener('click', function (e) {
        e.stopPropagation();
    });

    document.getElementById('MiniPagina').addEventListener('click', function (e) {
        e.stopPropagation();
    });

    document.getElementById('DefinicoesConta').addEventListener('click', function (e) {
        e.stopPropagation();
        toggleMiniMenu();
        abrirMiniPagina();
    });

    function abrirMiniPagina() {
        hideMiniMenu(); // Garante que o MiniMenu seja fechado
        miniPagina.style.display = 'block';
        overlay.style.display = 'block';
    }

    function fecharMiniPagina() {
        miniPagina.style.display = 'none';
        overlay.style.display = 'none';
    }
</script>

</body>
</html>