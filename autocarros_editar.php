<?php
session_name("sessao");
session_start();

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

require_once('menu.php');
require_once('ligarbd.php');

 function atualizarAutocarro($basedados) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_autocarro'])) {
    $id_autocarro = $_POST['id_autocarro'];
    $capacidade_autocarro = $_POST['capacidade_autocarro'];

    // Verifica se a capacidade está dentro do intervalo permitido (27-70)
    if (intval($capacidade_autocarro) < 27 || intval($capacidade_autocarro) > 70) {
        $_SESSION['error_message'] = 'A capacidade do autocarro deve estar entre 27 e 70.';
    } else {
        // Atualiza os dados do autocarro no base de dados
        $update_query = "UPDATE autocarro SET capacidade_autocarro = '$capacidade_autocarro' WHERE id_autocarro = '$id_autocarro'";
        $update_result = mysqli_query($basedados, $update_query);

        if ($update_result) {
			echo "<script>window.location.href = 'autocarros.php';</script>";
			exit();
		} else {
			$_SESSION['error_message'] = 'Erro ao atualizar os dados do autocarro!';
			}
		}
	}
 }

// Chamar a função para inserir o autocarro
atualizarAutocarro($basedados);

$id_autocarro = $_GET['id'];

$consulta = "SELECT * FROM autocarro WHERE id_autocarro='$id_autocarro'";
$resultado = mysqli_query($basedados, $consulta);

$registo = mysqli_fetch_array($resultado);
$nregistos = mysqli_num_rows($resultado);
?>

<html>
<head>
    <title>EasyTicket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style_editar.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
</head>
<body>
<center>
    <br>
	<?php if(isset($_SESSION['error_message']) || isset($_SESSION['success_message'])) { ?>
        <div class="error" style="display: block;">
            <div class="error__content">
                <div class="error__icon">
                    <svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m13 13h-2v-6h2zm0 4h-2v-2h2zm-1-15c-1.3132 0-2.61358.25866-3.82683.7612-1.21326.50255-2.31565 1.23915-3.24424 2.16773-1.87536 1.87537-2.92893 4.41891-2.92893 7.07107 0 2.6522 1.05357 5.1957 2.92893 7.0711.92859.9286 2.03098 1.6651 3.24424 2.1677 1.21325.5025 2.51363.7612 3.82683.7612 2.6522 0 5.1957-1.0536 7.0711-2.9289 1.8753-1.8754 2.9289-4.4189 2.9289-7.0711 0-1.3132-.2587-2.61358-.7612-3.82683-.5026-1.21326-1.2391-2.31565-2.1677-3.24424-.9286-.92858-2.031-1.66518-3.2443-2.16773-1.2132-.50254-2.5136-.7612-3.8268-.7612z" fill="#393a37"></path></svg>
                </div>
                <div class="error__text">
                    <?php 
                    if(isset($_SESSION['error_message'])) {
                        echo $_SESSION['error_message']; 
                        unset($_SESSION['error_message']); 
                    }
                    ?>
                </div>
                <div class="error__close" onclick="fecharErro()">
                    <svg height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="m15.8333 5.34166-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z" fill="#393a37"></path></svg>
                </div>
            </div>
        </div>
    <?php } ?>
<a href="autocarros.php">Voltar</a>
<br>
<h1>Editar Autocarro</h1>
<br>
<form method="post" action="autocarros_editar.php?id=<?php echo $id_autocarro; ?>" autocomplete="off" id="editarForm">
            <table>
                <tr>
                    <td> Id do Autocarro </td>
                    <td>
                        <input type="text" name="id_autocarro" readonly value="<?php echo isset($_POST['id_autocarro']) ? htmlspecialchars($_POST['id_autocarro']) : (isset($registo['id_autocarro']) ? htmlspecialchars($registo['id_autocarro']) : ''); ?>" class="centered-input" style="outline: none; box-shadow: none;">
                    </td>
                </tr>
                <tr>
                    <td> Capacidade do Autocarro </td>
                    <td>
                        <input type="text" name="capacidade_autocarro" value="<?php echo (isset($registo['capacidade_autocarro']) ? htmlspecialchars($registo['capacidade_autocarro']) : ''); ?>" class="centered-input" maxlength="2" oninput="allowOnlyNumbers(event)" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)" style="outline: none; box-shadow: none;">
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
        // Obtém o elemento pai (<td>) do input
        var td = input.parentNode;

        // Muda a cor de fundo do <td> e do input
        if (selected) {
            td.style.backgroundColor = "#444"; // Cor mais escura
            input.style.backgroundColor = "#444"; // Cor mais escura
        } else {
            td.style.backgroundColor = ""; // Volta à cor de fundo padrão
            input.style.backgroundColor = ""; // Volta à cor de fundo padrão
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
    
    function fecharErro() {
        document.querySelector('.error').style.display = 'none';
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
        cancelButton.onclick = function () {
            document.body.removeChild(overlay);
        };
        buttonWrapper.appendChild(cancelButton);

        var editButton = document.createElement("button");
        editButton.className = "card-button primary-editar";
        editButton.innerText = "Atualizar";
        editButton.onclick = function () {
            document.getElementById('editarForm').submit();
        };
        buttonWrapper.appendChild(editButton);

        box.appendChild(buttonWrapper);

        overlay.appendChild(box);
        document.body.appendChild(overlay);
    }
</script>
</body>
</html>
