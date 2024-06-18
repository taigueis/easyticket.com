<?php
// Incluir arquivos necessários e iniciar a sessão
require_once('ligarbd.php');
session_name("sessao");
session_start();

// Redirecionar se o usuário não estiver logado
if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit();
}

// Incluir o arquivo do menu
require_once('menu.php');

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $id_bilhete = $_POST['id_bilhete'];
    $linha_autocarro = $_POST['linha_autocarro'];
    $preco_bilhete = $_POST['preco_bilhete'];

    // Validar se o preço do bilhete é maior que 0
    if ($preco_bilhete <= 0) {
    } else {
        // Atualizar a tabela viagem
        $update_query = "UPDATE bilhetes SET linha_autocarro='$linha_autocarro', preco_bilhete='$preco_bilhete' WHERE id_bilhete='$id_bilhete'";
        if (mysqli_query($basedados, $update_query)) {
        } else {
        }
    }
}

// Obter os dados da viagem
$id_bilhete = $_GET['id'];
$consulta = "SELECT * FROM bilhetes WHERE id_bilhete='$id_bilhete'";
$resultado = mysqli_query($basedados, $consulta);
$registo = mysqli_fetch_array($resultado);

// Definir o valor de $linha_autocarro e de $preco_bilhete
$linha_autocarro = $registo['linha_autocarro'];
$preco_bilhete = $registo['preco_bilhete'];
    ?>
<html>
<head>
    <title>EasyTicket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_editar.css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <script>
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

        // Função para limitar o número de casas decimais
        function limitarCasasDecimais(event) {
            var input = event.target;
            var value = input.value;

            // Remover caracteres não numéricos, exceto o ponto decimal
            value = value.replace(/[^\d.]/g, '');

            // Limitar a um caractere antes do ponto decimal
            var firstPart = value.split('.')[0].slice(0, 1);

            // Limitar a dois caracteres depois do ponto decimal
            var secondPart = value.split('.')[1] ? value.split('.')[1].slice(0, 2) : '';

            // Atualizar o valor do input
            input.value = firstPart + '.' + secondPart;
        }
    </script>
</head>
<body>
<center>
<br><br><br>
<a href="bilhetes.php">Voltar</a>
    <br><br>
    <h1>Editar Preço do Bilhete da Linha <?php echo $linha_autocarro; ?></h1>
    <br><br><br>
    <form method="post" action="bilhetes.php" autocomplete="off" id="editarForm">
        <input type="hidden" name="id_bilhete" value="<?php echo $registo['id_bilhete']; ?>">
        <table>
    <tr>
        <td>Linha do Autocarro</td>
        <td><input type="text" name="linha_autocarro" value="<?php echo $linha_autocarro; ?>" readonly style="outline: none; box-shadow: none;"></td>
    </tr>
    <tr>
        <td>Preço do Bilhete</td>
        <td><input type="text" name="preco_bilhete" id="preco_bilhete" value="<?php echo $preco_bilhete; ?>" class="centered-input" oninput="limitarCasasDecimais(event)" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)" style="outline: none; box-shadow: none;"></td>
    </tr>
</table>

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
</script>

        <div class="button-container">
            <input type="button" value="Guardar Alterações" onClick="confirmaatualizar();">
            <input type="reset" value="Apagar Alterações">
        </div> 
    </form>
</center>
</body>
</html>
