<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

// Limpar os valores das variáveis da sessão
$_SESSION['nome_utilizador'] = "";
$_SESSION['email_utilizador'] = "";
$_SESSION['password_utilizador'] = "";
$_SESSION['contacto_utilizador'] = "";
$_SESSION['data_nasc'] = "";
$_SESSION['error_message'] = "";

require_once('ligarbd.php');

$nome_utilizador = $email_utilizador = $password_utilizador = $contacto_utilizador = $data_nasc = "";
$data_nascimento_predefinida = date('Y-m-d', strtotime('-10 years'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_utilizador = $_POST['nome_utilizador'];
    $email_utilizador = $_POST['email_utilizador'];
    $password_utilizador = $_POST['password_utilizador'];
    $contacto_utilizador = $_POST['contacto_utilizador'];
    $data_nasc = $_POST['data_nasc'];

    // Verifica se o contato é válido (exatamente 9 caracteres e consiste apenas em números)
   if (!preg_match('/^[0-9]{9}$/', $contacto_utilizador) || intval($contacto_utilizador) < 200000000 || intval($contacto_utilizador) > 999999999) {
    $_SESSION['error_message'] = "Insira um contacto válido!";
} else {
        // Verifica se o nome de utilizador já está em uso
        $verificar_nome_utilizador = "SELECT * FROM utilizadores WHERE nome_utilizador = '$nome_utilizador'";
        $resultado_verificacao_nome = mysqli_query($basedados, $verificar_nome_utilizador);

        if (mysqli_num_rows($resultado_verificacao_nome) > 0) {
            $_SESSION['error_message'] = "Nome de utilizador já está em uso!";
        } else {
            // Verifica se o contato já está em uso
            $verificar_contacto_utilizador = "SELECT * FROM utilizadores WHERE contacto_utilizador = '$contacto_utilizador'";
            $resultado_verificacao_contacto = mysqli_query($basedados, $verificar_contacto_utilizador);

            if (mysqli_num_rows($resultado_verificacao_contacto) > 0) {
                $_SESSION['error_message'] = "Contacto já registado!";
            } else {
                // Verifica se o email já está em uso
                $verificar_email = "SELECT * FROM utilizadores WHERE email_utilizador = '$email_utilizador'";
                $resultado_verificacao_email = mysqli_query($basedados, $verificar_email);

                if (mysqli_num_rows($resultado_verificacao_email) > 0) {
                    $_SESSION['error_message'] = "Email já registado!";
                } else if (strlen($password_utilizador) < 8) {
                    $_SESSION['error_message'] = "A sua password deve conter menos 8 caracteres!";
                } else {
                    // Verificar a idade mínima de 10 anos
                    $data_nascimento = strtotime($data_nasc);
                    $idade_minima = strtotime("-10 years");
                    if ($data_nascimento > $idade_minima) {
                        $_SESSION['error_message'] = "O utilizador deve conter pelo menos 10 anos de idade!";
                    } else {
                        $inserir = "INSERT INTO utilizadores (nome_utilizador, tipo_utilizador, email_utilizador, password_utilizador, contacto_utilizador, data_nasc, ativo) 
                                    VALUES ('$nome_utilizador', 'user', '$email_utilizador', '$password_utilizador', '$contacto_utilizador', '$data_nasc', '1')";
                        $sucesso = mysqli_query($basedados, $inserir);

                        if (!$sucesso) {
                            $_SESSION['error_message'] = "Erro!";
                        } else {
                            $_SESSION['nome_utilizador'] = $nome_utilizador;
                            $_SESSION['tipo_utilizador'] = 'user';  
                            $_SESSION['id_utilizador'] = mysqli_insert_id($basedados);

                            header("Location: inicio.php");
                            exit;
                        }
                    }
                }
            }
        }
    }
}
?>

<html>
<head>
    <link href="style_index2.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyTicket</title>
	
</head>
<body>  
<center>
<?php if(isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])) { ?>
            <div class="error" style="display: block;">
                <div class="error__content">
                    <div class="error__icon">
                        <svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m13 13h-2v-6h2zm0 4h-2v-2h2zm-1-15c-1.3132 0-2.61358.25866-3.82683.7612-1.21326.50255-2.31565 1.23915-3.24424 2.16773-1.87536 1.87537-2.92893 4.41891-2.92893 7.07107 0 2.6522 1.05357 5.1957 2.92893 7.0711.92859.9286 2.03098 1.6651 3.24424 2.1677 1.21325.5025 2.51363.7612 3.82683.7612 2.6522 0 5.1957-1.0536 7.0711-2.9289 1.8753-1.8754 2.9289-4.4189 2.9289-7.0711 0-1.3132-.2587-2.61358-.7612-3.82683-.5026-1.21326-1.2391-2.31565-2.1677-3.24424-.9286-.92858-2.031-1.66518-3.2443-2.16773-1.2132-.50254-2.5136-.7612-3.8268-.7612z" fill="#393a37"></path></svg>
                    </div>
                    <div class="error__text">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                    <div class="error__close" onclick="fecharErro()">
                        <svg height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="m15.8333 5.34166-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z" fill="#393a37"></path></svg>
                    </div>
                </div>
            </div>
            <br><br>
        <?php } ?>
</center>  
<div class="container">
<div id="logo-container"><img src="img/easybg.png"> </div>
<div id="form-container">
		<form method="post" action="registo.php" autocomplete="off">
			<table align="center">
                <tr> 
                    <td> Utilizador <br> <input type="text" id="nome_utilizador" name="nome_utilizador" value="<?php echo $nome_utilizador; ?>" required /> </td> 
                </tr>
                <tr> 
                    <td> Email <br> <input type="email" name="email_utilizador" id="email_utilizador" value="<?php echo $email_utilizador; ?>" required /> </td> 
                </tr>
				<tr> 
					<td style="position: relative;"> Password <br> <input type="password" name="password_utilizador" id="password" maxlength="16" value="<?php echo $password_utilizador; ?>" required /> 
					<img id="eye-icon" src="img/eye.png" onclick="togglePassword()">
					</td> 
				</tr>
				<tr> 
					<td> Contacto <br> <input type="tel" name="contacto_utilizador" maxlength="9" value="<?php echo $contacto_utilizador; ?>" required oninput="allowOnlyNumbers(event)"/> </td> 
				</tr>
				<tr> 
					<td> Data de Nascimento <br> <input type="date" name="data_nasc" value="<?php echo $data_nascimento_predefinida; ?>" required /> </td> 
				</tr>
				<tr> 
					<td> <input type="submit" class="botao" value="Registar" /> </td> 
				</tr>
				<br>
				<tr> 
					<td> Já tem conta? <a href="index_login.php"> Login </a> <br> ou <br> <a href="index.php"> Continuar sem conta </a> </td> 
				</tr>
			</table>
        </form>
    </div>
</div>
    <script>
       		function fecharErro() {
            document.querySelector('.error').style.display = 'none';
        }

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


            // Captura o elemento de entrada de e-mail
            var emailInput = document.getElementById("email_utilizador");

            // Adiciona um ouvinte de evento de entrada para o campo de e-mail
            emailInput.addEventListener("input", function(event) {
                // Remove espaços em branco no início e no fim do valor do campo de e-mail
                var email = this.value.trim();
                
                // Limita o comprimento máximo do e-mail a 30 caracteres
                if (email.length > 30) {
                    this.value = email.slice(0, 30);
                }

                // Remove todos os espaços em branco do e-mail
                this.value = this.value.replace(/\s/g, "");

                // Mantém o cursor na posição correta
                setCaretPosition(this, email.length);
            });

            // Adiciona um ouvinte de evento de teclado para interceptar a pressão da tecla de espaço
            emailInput.addEventListener("keydown", function(event) {
                if (event.key === " ") {
                    // Impede o comportamento padrão da tecla de espaço (não insere espaço)
                    event.preventDefault();
                }
            });

            // Função para definir a posição do cursor
            function setCaretPosition(elem, caretPos) {
                if (elem.setSelectionRange) {
                    elem.focus();
                    elem.setSelectionRange(caretPos, caretPos);
                } else if (elem.createTextRange) {
                    var range = elem.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', caretPos);
                    range.moveStart('character', caretPos);
                    range.select();
                }
            }
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
		
		function allowOnlyNumbers(event) {
    var input = event.target;
    var inputValue = input.value;
    var regex = /[^0-9]/g; // Expressão regular para encontrar caracteres que não são números

    if (regex.test(inputValue)) {
        // Se caracteres não permitidos forem detectados, limpa o valor do campo de entrada
        input.value = inputValue.replace(regex, '');
    }
}

    </script>
</body>
</html>