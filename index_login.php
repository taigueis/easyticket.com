<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

// Definindo os valores padrão das variáveis de sessão
$nome_utilizador = isset($_SESSION['nome_utilizador']) ? ($_SESSION['nome_utilizador'] !== "semregisto" ? $_SESSION['nome_utilizador'] : '') : '';
$password_utilizador = isset($_SESSION['password_utilizador']) ? ($_SESSION['password_utilizador'] !== "semregisto" ? $_SESSION['password_utilizador'] : '') : '';

// Limpar os valores das variáveis da sessão
$_SESSION['nome_utilizador'] = "";
$_SESSION['password_utilizador'] = "";
?>

<html>
<head>
    <link href="style_index.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> EasyTicket</title>
</head>
<script>
    window.onload = function() {
        var errorMessage = "<?php echo isset($_SESSION['error_message']) ? $_SESSION['error_message'] : ''; ?>";
        if (errorMessage !== '') {
            document.querySelector('.error').style.display = 'block';
            document.getElementById('logo-container').classList.add('margin-top');
        }

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

        // Captura o elemento de entrada de password
        var passwordInput = document.getElementById("password");

        // Adiciona um ouvinte de evento de entrada para o campo de password
        passwordInput.addEventListener("input", function(event) {
            // Remove espaços em branco no início e no fim do valor do campo de password
            var password = this.value.trim();
            
            // Remove todos os espaços em branco da password
            this.value = this.value.replace(/\s/g, "");

            // Limita o comprimento máximo da password a 16 caracteres
            if (password.length > 16) {
                this.value = password.slice(0, 16);
            }
        });

        // Adiciona um ouvinte de evento de teclado para interceptar a pressão da tecla de espaço no campo de password
        passwordInput.addEventListener("keydown", function(event) {
            if (event.key === " ") {
                // Impede o comportamento padrão da tecla de espaço (não insere espaço)
                event.preventDefault();
            }
        });

        // Captura o elemento de entrada de utilizador
        var userInput = document.getElementById("nome_utilizador");

        // Adiciona um ouvinte de evento de entrada para o campo de utilizador
        userInput.addEventListener("input", function(event) {
            // Remove espaços em branco no início e no fim do valor do campo de utilizador
            var user = this.value.trim();
            
            // Remove todos os espaços em branco do utilizador
            this.value = this.value.replace(/\s/g, "");

            // Limita o comprimento máximo do utilizador a 16 caracteres
            if (user.length > 16) {
                this.value = user.slice(0, 16);
            }
        });

        // Adiciona um ouvinte de evento de teclado para interceptar a pressão da tecla de espaço
        userInput.addEventListener("keydown", function(event) {
            if (event.key === " ") {
                // Impede o comportamento padrão da tecla de espaço (não insere espaço)
                event.preventDefault();
            }
        });
    };

    function togglePassword() {
        var passwordInput = document.getElementById("password");
        var eyeIcon = document.getElementById("eye-icon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.src = "img/eye-slash.png";
        } else {
            passwordInput.type = "password";
            eyeIcon.src = "img/eye.png";
        }
    }

    function fecharErro() {
        document.querySelector('.error').style.display = 'none';
        document.getElementById('logo-container').classList.remove('margin-top');
    }
</script>
<body>
    <br>
<center>
<?php if(isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])) { ?>
            <div class="error" style="display: block;">
                <div class="error__content">
                    <div class="error__icon">
                        <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m13 13h-2v-6h2zm0 4h-2v-2h2zm-1-15c-1.3132 0-2.61358.25866-3.82683.7612-1.21326.50255-2.31565 1.23915-3.24424 2.16773-1.87536 1.87537-2.92893 4.41891-2.92893 7.07107 0 2.6522 1.05357 5.1957 2.92893 7.0711.92859.9286 2.03098 1.6651 3.24424 2.1677 1.21325.5025 2.51363.7612 3.82683.7612 2.6522 0 5.1957-1.0536 7.0711-2.9289 1.8753-1.8754 2.9289-4.4189 2.9289-7.0711 0-1.3132-.2587-2.61358-.7612-3.82683-.5026-1.21326-1.2391-2.31565-2.1677-3.24424-.9286-.92858-2.031-1.66518-3.2443-2.16773-1.2132-.50254-2.5136-.7612-3.8268-.7612z" fill="#393a37"></path></svg>
                    </div>
                    <div class="error__text">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                    <div class="error__close" onclick="fecharErro()">
                        <svg  viewBox="0 0 20 20"  xmlns="http://www.w3.org/2000/svg"><path d="m15.8333 5.34166-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z" fill="#393a37"></path></svg>
                    </div>
                </div>
            </div>
            <br><br>
        <?php } ?>
</center>    
<div id="container"> 
    <div id="logo-container"><img src="img/easybg.png"> </div>
    <div id="form-container">
        <form method="post" action="index2.php" autocomplete="off">
            <table align="center">
                <tr>
                    <td> Utilizador <input type="text" id="nome_utilizador" name="nome_utilizador" value="<?php echo $nome_utilizador; ?>" required /> </td>
                </tr>
                <tr>
                    <td class="password-container"> 
                        Password 
                        <input type="password" name="password_utilizador" id="password" maxlength="16" value="<?php echo $password_utilizador; ?>" required />
                        <img id="eye-icon" src="img/eye.png" onclick="togglePassword()">
                    </td>
                </tr>
                <tr>
                    <td> <input type="submit" class="botao" value="Login" /> </td>
                </tr>
                <tr>
                    <td> Não tem conta? <br> <a href="registo.php"> Registe-se </a> <br> ou <br> <a href="index.php"> Continuar sem conta </a> </td>
                </tr>
            </table>
        </form>
    </div>
</div>
</body>
</html>
