<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

    // --- Chamar menu ---
    require_once('menu.php');
    require_once('ligarbd.php');

function eliminarAutocarro($basedados) {
    if (isset($_GET['id'])) {
        $id_autocarro = $_GET['id'];

        $excluir = "DELETE FROM autocarro WHERE id_autocarro='$id_autocarro'";
        $sucesso = mysqli_query($basedados, $excluir);

        if (!$sucesso) {
            echo '<script>alert("Erro ao excluir os dados!");</script>';
        } else {
        }
    }
}


eliminarAutocarro($basedados);

$consulta = "SELECT id_autocarro, capacidade_autocarro FROM autocarro ORDER BY id_autocarro";
$resultado = mysqli_query($basedados, $consulta);
$nregistos = mysqli_num_rows($resultado);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <title>EasyTicket</title>
</head>
<body>	
<script>
function confirmaremover(id) {
    var overlay = document.createElement("div");
    overlay.className = "confirm-overlay";

    var box = document.createElement("div");
    box.className = "confirm-box";

    var question = document.createElement("p");
    question.className = "card-heading"; // Aplicando estilo para o cabeçalho
    question.innerText = "Deseja realmente eliminar o registo?";
    box.appendChild(question);

    var space = document.createElement("div");
    space.style.height = "25px"; // Adicionando espaço entre a pergunta e os botões
    box.appendChild(space);

    var buttonWrapper = document.createElement("div");
    buttonWrapper.className = "card-button-wrapper";

    var cancelButton = document.createElement("button");
    cancelButton.className = "card-button secondary"; // Estilo para o botão de cancelar
    cancelButton.innerText = "Cancelar";
    cancelButton.onclick = function() {
        document.body.removeChild(overlay);
    };
    buttonWrapper.appendChild(cancelButton);

    var deleteButton = document.createElement("button");
    deleteButton.className = "card-button primary-eliminar"; // Estilo para o botão de eliminar
    deleteButton.innerText = "Eliminar";
    deleteButton.onclick = function() {
        document.location.href = "autocarros_eliminar.php?id=" + id;
    };
    buttonWrapper.appendChild(deleteButton);

    box.appendChild(buttonWrapper);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
}


function confirmaatualizar(id) {
    var overlay = document.createElement("div");
    overlay.className = "confirm-overlay";

    var box = document.createElement("div");
    box.className = "confirm-box";

    var question = document.createElement("p");
    question.className = "card-heading";
    question.innerText = "Deseja realmente editar o registo?";
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

    var deleteButton = document.createElement("button");
    deleteButton.className = "card-button primary-editar"; 
    deleteButton.innerText = "Editar";
    deleteButton.onclick = function() {
		document.location.href = "autocarros_editar.php?id=" + id;
	};

    buttonWrapper.appendChild(deleteButton);

    box.appendChild(buttonWrapper);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
}


function confirmatrajeto(id) {
    var overlay = document.createElement("div");
    overlay.className = "confirm-overlay";

    var box = document.createElement("div");
    box.className = "confirm-box";

    var question = document.createElement("p");
    question.className = "card-heading";
    question.innerText = "Deseja realmente ver o trajeto do autocarro?";
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

    var deleteButton = document.createElement("button");
    deleteButton.className = "card-button primary-trajeto"; 
    deleteButton.innerText = "Ver Trajeto";
    deleteButton.onclick = function() {
        document.location.href = "autocarro_trajeto.php?id=" + id;
    };
    buttonWrapper.appendChild(deleteButton);

    box.appendChild(buttonWrapper);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
}


function confirmainserir() {
    var overlay = document.createElement("div");
    overlay.className = "confirm-overlay";

    var box = document.createElement("div");
    box.className = "confirm-box";

    var question = document.createElement("p");
    question.className = "card-heading"; 
    question.innerText = "Deseja realmente inserir o registo?";
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

    var deleteButton = document.createElement("button");
    deleteButton.className = "card-button primary-inserir"; 
    deleteButton.innerText = "Inserir";
    deleteButton.onclick = function() {
        document.location.href = "autocarros_novo.php";
    };
    buttonWrapper.appendChild(deleteButton);

    box.appendChild(buttonWrapper);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
}
</script>

<center>
<br><br>
<h1>Autocarros</h1>
    <br><br><br>
    <table>
        <tr>
            <th> Id </th>
            <th> Capacidade </th>
            <th> Editar </th>
            <th> Excluir </th>
            <th> Trajetos </th>
        </tr>

        <?php
        for ($i = 0; $i < $nregistos; $i++) {
            $registo = mysqli_fetch_array($resultado);
            echo '<tr>';
            echo '<td>' . $registo['id_autocarro'] . '</td>';
            echo '<td>' . $registo['capacidade_autocarro'] . '</td>';
			
            echo '<td align="center"> <a onClick="confirmaatualizar(' . $registo['id_autocarro'] . ');" style="cursor: pointer;">
            <img src="img/atualizar.png" class="img-icon"  > </a></td>';
					
           echo '<td align="center"> <a onClick="confirmaremover(' . $registo['id_autocarro'] . ');" style="cursor: pointer;">
            <img src="img/eliminar.png"  class="img-icon"  > </a></td>';

            echo '<td align="center"> <a onClick="confirmatrajeto(' . $registo['id_autocarro'] . ');" style="cursor: pointer;">
            <img src="img/consultar.png"  class="img-icon"  > </a></td>';
					
            echo '</tr>';
        }
        ?>
    </table>
    <br><br><br>
    <h2>Inserir Novo Autocarro</h2>
    <br>
	<a onclick="confirmainserir();">
		<img src="img/inserir.png" title="Inserir" style="cursor: pointer;" >
	</a>
	<br><br>
</center>
</body>
</html>