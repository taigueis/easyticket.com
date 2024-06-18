<?php
session_name("sessao");
session_start();

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

require_once('ligarbd.php');

$alert_message = "";

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_utilizador'])) {
    $id_utilizador = $_POST['id_utilizador'];
    $nome_utilizador = $_POST['nome_utilizador'];
    $tipo_utilizador = $_POST['tipo_utilizador'];
    $email_utilizador = $_POST['email_utilizador'];
    $password_utilizador = $_POST['password_utilizador'];
    $contacto_utilizador = $_POST['contacto_utilizador'];
    $data_nasc = $_POST['data_nasc'];

    // Verificar se o nome de utilizador já existe na base de dados
    $verificar_duplicata_query = "SELECT id_utilizador FROM utilizadores WHERE nome_utilizador = '$nome_utilizador' AND id_utilizador != '$id_utilizador'";
    $verificar_duplicata_resultado = mysqli_query($basedados, $verificar_duplicata_query);

    if (mysqli_num_rows($verificar_duplicata_resultado) > 0) {
        $_SESSION['error_message'] = "Já existe um utilizador com o mesmo nome. Por favor, escolha outro nome de utilizador.";
    } else {
        // Verificar se o email já existe na base de dados
        $verificar_email_query = "SELECT id_utilizador FROM utilizadores WHERE email_utilizador = '$email_utilizador' AND id_utilizador != '$id_utilizador'";
        $verificar_email_resultado = mysqli_query($basedados, $verificar_email_query);

        if (mysqli_num_rows($verificar_email_resultado) > 0) {
            $_SESSION['error_message'] = "O email fornecido já está em uso por outro utilizador. Por favor, escolha outro email.";
        } else {
            // Verificar se o contacto já existe na base de dados
            $verificar_contacto_query = "SELECT id_utilizador FROM utilizadores WHERE contacto_utilizador = '$contacto_utilizador' AND id_utilizador != '$id_utilizador'";
            $verificar_contacto_resultado = mysqli_query($basedados, $verificar_contacto_query);

            if (mysqli_num_rows($verificar_contacto_resultado) > 0) {
                $_SESSION['error_message'] = "O número de contacto fornecido já está em uso por outro utilizador. Por favor, escolha outro número de contacto.";
            } else {
                // Verificar se o contacto tem exatamente 9 dígitos
                if (strlen($contacto_utilizador) !== 9 || !ctype_digit($contacto_utilizador) || intval($contacto_utilizador) < 900000000 || intval($contacto_utilizador) > 999999999) {
                    $_SESSION['error_message'] = "O número de contacto deve ter exatamente 9 dígitos!";
                } else {
                    // Verificar se a palavra-passe tem pelo menos 8 caracteres
                    if (strlen($password_utilizador) < 8) {
                        $_SESSION['error_message'] = "A sua password deve ter pelo menos 8 caracteres!";
                    } else {
						// Verificar a idade mínima de 10 anos
                    $data_nascimento = strtotime($data_nasc);
                    $idade_minima = strtotime("-10 years");
                    if ($data_nascimento > $idade_minima) {
                        $_SESSION['error_message'] = "O utilizador deve conter pelo menos 10 anos de idade!";
                    } else {
                        // Atualizar os dados na base de dados
                        $query = "UPDATE utilizadores SET 
                            nome_utilizador = '$nome_utilizador',
                            tipo_utilizador = '$tipo_utilizador',
                            email_utilizador = '$email_utilizador',
                            password_utilizador = '$password_utilizador',
                            contacto_utilizador = '$contacto_utilizador',
                            data_nasc = '$data_nasc'
                            WHERE id_utilizador = '$id_utilizador'";

                        $resultado = mysqli_query($basedados, $query);

                        if ($resultado) {
                            header("Location: utilizadores.php");
                            exit();
                        } else {
                            $_SESSION['error_message'] = "Erro ao atualizar o utilizador. Por favor, tente novamente.";
                        }
                    }
                }
            }
        }
    }
}
}

$id_utilizador = isset($_GET['id']) ? $_GET['id'] : null;
if ($id_utilizador === null) {
}

require_once('menu.php');

$consulta = "SELECT * FROM utilizadores WHERE id_utilizador='".$id_utilizador."'";
$resultado = mysqli_query($basedados, $consulta);
$registo = mysqli_fetch_assoc($resultado);
?>

<html>
<head>
    <title>EasyTicket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style_editar.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="style_navbar.css">
    <link rel="icon" href="img/logo_EasyTicket.png">
</head>
<body> 
<center>
    <br>
    <?php if(isset($_SESSION['error_message'])) { ?>
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
    <?php } ?>
    <a href="utilizadores.php">Voltar</a>
    <br><br>
	<h1>Atualizar Dados Utilizador </h1>
	<br>
    <br>
    <form method="post" action="" autocomplete="off" id="editarForm">
            <input type="hidden" name="id_utilizador" value="<?php echo $registo['id_utilizador']; ?>">
            <table>
                <tr>
                    <td> Nome </td>
                    <td>
                        <input type="text" id="nome_utilizador" name="nome_utilizador" value="<?php echo $registo['nome_utilizador']; ?>" class="centered-input" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)" style="outline: none; box-shadow: none;">
                    </td>
                </tr>
                <tr>
                    <td> Tipo (User/Admin) </td>
                    <td>
                        <select name="tipo_utilizador" class="centered-input" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)"  style="outline: none; box-shadow: none;">
                            <option value="user" <?php echo ($registo['tipo_utilizador'] === 'user') ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo ($registo['tipo_utilizador'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td> Email </td>
                    <td>
                        <input type="email" id="email_utilizador" name="email_utilizador" value="<?php echo $registo['email_utilizador']; ?>" class="centered-input" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)"  style="outline: none; box-shadow: none;">
                    </td>
                </tr>
                <tr>
                    <td> Password </td>
                    <td>
                        <input type="text" id="password" name="password_utilizador" value="<?php echo $registo['password_utilizador']; ?>" class="centered-input" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)"  style="outline: none; box-shadow: none;">
                    </td>
                </tr>
                <tr>
                    <td> Contacto </td>
                    <td>
                        <input type="text" name="contacto_utilizador" maxlength="9" value="<?php echo $registo['contacto_utilizador']; ?>" oninput="allowOnlyNumbers(event)" class="centered-input" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)" style="outline: none; box-shadow: none;">
                    </td>
                </tr>
                <tr>
                    <td> Data de Nascimento </td>
                    <td>
                        <input type="date" name="data_nasc" value="<?php echo $registo['data_nasc']; ?>" class="centered-input" onchange="validarAno(this)" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)"  style="outline: none; box-shadow: none;">
                    </td>
                </tr>
            </table>
            <div class="button-container">
                <input type="button" value="Guardar Alterações" onClick="confirmaatualizar();">
                <input type="reset" value="Apagar Alterações">
            </div>
        </form>
</center>  
    <script>
            function changeBackgroundColor(input, selected) {
        // Obtém o elemento (<td>) do input
        var td = input.parentNode;

        // Muda a cor de fundo do <td> e do input
        if (selected) {
            td.style.backgroundColor = "#444"; 
            input.style.backgroundColor = "#444"; 
        } else {
            td.style.backgroundColor = ""; // Volta à cor de fundo padrão
            input.style.backgroundColor = ""; 
        }
    }
             function validarAno(input) {
        var valor = input.value;
        var partes = valor.split('-');
        if (partes.length === 3 && partes[0].length > 4) {
            var ano = partes[0].substring(0, 4);
            var mes = partes[1];
            var dia = partes[2];
            var novaData = ano + '-' + mes + '-' + dia;
            input.value = novaData;
        }
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

function allowOnlyNumbers(event) {
    var input = event.target;
    var inputValue = input.value;
    var regex = /[^0-9]/g; // Expressão regular para encontrar caracteres que não são números

    if (regex.test(inputValue)) {
        // Se caracteres não permitidos forem detectados, limpa o valor do campo de entrada
        input.value = inputValue.replace(regex, '');
    }
}

        function confirmaatualizar() {
            var overlay = document.createElement("div");
            overlay.className = "confirm-overlay";

            var box = document.createElement("div");
            box.className = "confirm-box";

            var question = document.createElement("p");
            question.className = "card-heading";
            question.innerText = "Deseja realmente atualizar o registo?";
            box.appendChild(question);

            var space = document.createElement("div");
            space.style.height = "25px"; 
            box.appendChild(space);

            var buttonWrapper = document.createElement("div");
            buttonWrapper.className = "card-button-wrapper";

            var cancelButton = document.createElement("button");
            cancelButton.className = "card-button secondary"; 
            cancelButton.innerText = "Cancelar";
            cancelButton.onclick = function() {
                document.body.removeChild(overlay);
            };
            buttonWrapper.appendChild(cancelButton);

            var editButton = document.createElement("button");
            editButton.className = "card-button primary-editar"; 
            editButton.innerText = "Atualizar";
            editButton.onclick = function() {
                document.getElementById('editarForm').submit();
            };
            buttonWrapper.appendChild(editButton);

            box.appendChild(buttonWrapper);

            overlay.appendChild(box);
            document.body.appendChild(overlay);
        }

        function fecharErro() {
            document.querySelector('.error').style.display = 'none';
        }
    </script>
</body>
</html>