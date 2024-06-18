<?php
session_name("sessao");
session_start();

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

if ($_SESSION['tipo_utilizador'] == 'admin') {        
    require_once('menu.php');
} elseif ($_SESSION['tipo_utilizador'] == 'user') {        
    require_once('menu2.php');
}

require_once('ligarbd.php');

$id_utilizador = $_SESSION['id_utilizador'];

$consulta = "SELECT * FROM utilizadores WHERE id_utilizador = '$id_utilizador'";
$resultado = mysqli_query($basedados, $consulta);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $dados_utilizador = mysqli_fetch_assoc($resultado);
    $nome_utilizador = $dados_utilizador['nome_utilizador']; 
    $email_utilizador = $dados_utilizador['email_utilizador'];
    $password_utilizador = $dados_utilizador['password_utilizador'];
    $contacto_utilizador = $dados_utilizador['contacto_utilizador'];
    $data_nasc = $dados_utilizador['data_nasc'];
}

$alert_messages = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_utilizador_post = isset($_POST['email_utilizador']) ? $_POST['email_utilizador'] : '';
    $password_utilizador_post = isset($_POST['password_utilizador']) ? $_POST['password_utilizador'] : '';
    $contacto_utilizador_post = isset($_POST['contacto_utilizador']) ? $_POST['contacto_utilizador'] : '';
    $data_nasc_post = isset($_POST['data_nasc']) ? $_POST['data_nasc'] : '';
    $novo_nome_utilizador_post = isset($_POST['novo_nome_utilizador']) ? $_POST['novo_nome_utilizador'] : $nome_utilizador;

    $verificar_nome_query = "SELECT * FROM utilizadores WHERE nome_utilizador = '$novo_nome_utilizador_post' AND id_utilizador != '$id_utilizador'";
    $resultado_nome = mysqli_query($basedados, $verificar_nome_query);
    if (mysqli_num_rows($resultado_nome) > 0) {
        $alert_messages[] = "Nome de utilizador já registado!";
    }

    $verificar_email_query = "SELECT * FROM utilizadores WHERE email_utilizador = '$email_utilizador_post' AND id_utilizador != '$id_utilizador'";
    $resultado_email = mysqli_query($basedados, $verificar_email_query);
    if (mysqli_num_rows($resultado_email) > 0) {
        $alert_messages[] = "Email já registado!";
    }

 if (strlen($password_utilizador_post) < 8) {
        $alert_messages[] = "Password deve ter pelo menos 8 caracteres!";
    }
	
    if (!preg_match("/^[9][0-9]{8}$/", $contacto_utilizador_post)) {
        $alert_messages[] = "Insere um contacto válido!";
    } else {
        $verificar_contacto_query = "SELECT * FROM utilizadores WHERE contacto_utilizador = '$contacto_utilizador_post' AND id_utilizador != '$id_utilizador'";
        $resultado_contacto = mysqli_query($basedados, $verificar_contacto_query);
        if (mysqli_num_rows($resultado_contacto) > 0) {
            $alert_messages[] = "Contacto já registado!";
        }
    }

    if ($data_nasc_post) {
        // Verificar se a data de nascimento está no futuro ou é igual ou menor que 1900
        if (strtotime($data_nasc_post) > time() || date('Y', strtotime($data_nasc_post)) < 1900) {
            $alert_messages[] = "A data de nascimento deve ser válida!";
        } else {
            $data_nasc_dt = new DateTime($data_nasc_post);
            $agora = new DateTime();
            $idade = $agora->diff($data_nasc_dt)->y;
    
            // Verificar se a idade é menor que 10 anos
            if ($idade < 10) {
                $alert_messages[] = "Deve ter pelo menos 10 anos de idade!";
            }
        }
    }
    

    if (empty($alert_messages)) {
        $atualizar_query = "UPDATE utilizadores SET nome_utilizador = '$novo_nome_utilizador_post', email_utilizador = '$email_utilizador_post', password_utilizador = '$password_utilizador_post', contacto_utilizador = '$contacto_utilizador_post', data_nasc = '$data_nasc_post' WHERE id_utilizador = '$id_utilizador'";
        $resultado_atualizacao = mysqli_query($basedados, $atualizar_query);

        if (!$resultado_atualizacao) {
            $alert_messages[] = "Erro ao atualizar os dados do utilizador!";
        } else {
            $_SESSION['nome_utilizador'] = $novo_nome_utilizador_post;
            echo '<script>window.location.href = "perfil.php";</script>';
            exit();
        }
    }

    $_SESSION['error_messages'] = $alert_messages;
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyTicket</title>
    <link rel="stylesheet" href="style_perfil_editar.css"> 
</head>
<body>
    <div id="loading" style="display: none;">
        <div class="spinner"></div>
    </div>
    <center>
    <?php if(isset($_SESSION['error_messages']) && !empty($_SESSION['error_messages'])) { ?>
    <div class="error" style="display: block;">
        <div class="error__content">
            <div class="error__icon">
                <svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m13 13h-2v-6h2zm0 4h-2v-2h2zm-1-15c-1.3132 0-2.61358.25866-3.82683.7612-1.21326.50255-2.31565 1.23915-3.24424 2.16773-1.87536 1.87537-2.92893 4.41891-2.92893 7.07107 0 2.6522 1.05357 5.1957 2.92893 7.0711.92859.9286 2.03098 1.6651 3.24424 2.1677 1.21325.5025 2.51363.7612 3.82683.7612 2.6522 0 5.1957-1.0536 7.0711-2.9289 1.8753-1.8754 2.9289-4.4189 2.9289-7.0711 0-1.3132-.2587-2.61358-.7612-3.82683-.5026-1.21326-1.2391-2.31565-2.1677-3.24424-.9286-.92858-2.031-1.66518-3.2443-2.16773-1.2132-.50254-2.5136-.7612-3.8268-.7612z" fill="#393a37"></path></svg>
            </div>
            <div class="error__text">
                <?php foreach ($_SESSION['error_messages'] as $error_message) {
                    echo $error_message . "<br>";
                }
                unset($_SESSION['error_messages']);
                ?>
            </div>
            <div class="error__close" onclick="fecharErro()">
                <svg height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="m15.8333 5.34166-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z" fill="#393a37"></path></svg>
            </div>
        </div>
    </div>
<?php } ?>
<br>       
<a href="perfil.php">Voltar</a>
    <h1>Editar Perfil</h1>
    </center> 
    <form id="perfilForm" action="perfil_editar.php" method="post" autocomplete="off">
        <input type="hidden" id="nome_utilizador" name="nome_utilizador" value="<?php echo $nome_utilizador; ?>" readonly><br><br>
        
        <label>Nome de Utilizador</label>
        <input type="text" id="novo_nome_utilizador" name="novo_nome_utilizador" value="<?php echo $nome_utilizador; ?>" required><br><br>
       
        <label>Email</label>
        <input type="email" id="email_utilizador" name="email_utilizador" value="<?php echo $email_utilizador; ?>" required><br><br>
    
        <label>Password</label>
        <input type="text" id="password_utilizador" name="password_utilizador" value="<?php echo $password_utilizador; ?>" required><br><br>

        <label>Contacto</label>
        <input type="text" id="contacto_utilizador" name="contacto_utilizador"  maxlength="9" value="<?php echo $contacto_utilizador; ?>" required oninput="allowOnlyNumbers(event)"><br><br>

        <label>Data de Nascimento</label>
<input type="date" id="data_nasc" name="data_nasc" value="<?php echo $data_nasc; ?>" onchange="validarAno(this)"><br><br>


        <input type="button" value="Atualizar" onClick="return confirmaatualizar();">
		<br><br>
		<input type="reset" value="Apagar Alterações"></td>
    </form>
    <br>

    <script>
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
// Captura o elemento de entrada de password
var passwordInput = document.getElementById("password_utilizador");

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
var userInput = document.getElementById("novo_nome_utilizador");

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

function confirmaatualizar() {
    var overlay = document.createElement("div");
    overlay.className = "confirm-overlay";

    var box = document.createElement("div");
    box.className = "confirm-box";

    var question = document.createElement("p");
    question.className = "card-heading";
    question.innerText = "Deseja realmente atualizar os seus dados?";
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
    editButton.className = "card-button primary-inserir"; 
    editButton.innerText = "Atualizar";
    editButton.onclick = function() {
        // Exibindo a tela de carregamento
        document.getElementById('loading').style.display = 'block';

        // Removendo a overlay de confirmação
        document.body.removeChild(overlay);

        // Submetendo o formulário
        document.getElementById('perfilForm').submit();
    };
    buttonWrapper.appendChild(editButton);

    box.appendChild(buttonWrapper);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
}

	
        function fecharErro() {
            document.querySelector('.error').style.display = 'none';
        }
        
        function allowOnlyNumbers(event) {
            var input = event.target;
            var inputValue = input.value;
            var regex = /[^0-9]/g;

            if (regex.test(inputValue)) {
                input.value = inputValue.replace(regex, '');
            }
        }
    </script>

<script>
    // Captura o evento de envio do formulário
    document.getElementById('perfilForm').addEventListener('submit', function(event) {
        // Exibindo a tela de carregamento antes do envio do formulário
        document.getElementById('loading').style.display = 'block';
        
        <?php if (isset($_SESSION['error_message']) && $_SESSION['error_message'] != "") { ?>
            // Se houver mensagens de erro, exibe-as e impede o envio do formulário
            document.querySelector('.error__text').innerText = "<?php echo $_SESSION['error_message']; ?>";
            document.querySelector('.error').style.display = 'block';
            event.preventDefault(); // Evita o envio padrão do formulário
            <?php unset($_SESSION['error_message']); ?> // Limpa a mensagem de erro após exibição
        <?php } ?>
    });
</script>
</body>
</html>