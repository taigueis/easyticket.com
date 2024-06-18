<?php
// Iniciar a sessão se ainda não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit();
}

// --- Incluir o menu ---
require_once('menu.php');

// Incluir o arquivo de conexão com o banco de dados
require_once('ligarbd.php');

$id_viagem = $_GET['id'];
$consulta = "SELECT * FROM viagem WHERE id_viagem='$id_viagem'";
$resultado = mysqli_query($basedados, $consulta);
$registo = mysqli_fetch_array($resultado);

$linha_autocarro = $registo['linha_autocarro'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $linha_autocarro = $_POST['linha_autocarro'];
        $sentido = $_POST['sentido'];
        $origem = $_POST['origem'];
        $destino = $_POST['destino'];
        $hora_partida = $_POST['hora_partida'];
        $hora_chegada = $_POST['hora_chegada'];

        $verificar_existencia = "SELECT * FROM viagem WHERE sentido='$sentido' AND hora_partida='$hora_partida' AND hora_chegada='$hora_chegada' AND linha_autocarro = '$linha_autocarro'";
        $resultado_existencia = mysqli_query($basedados, $verificar_existencia);

        if (mysqli_num_rows($resultado_existencia) > 0) {
            $_SESSION['error_message'] = "Já existe um registo com o mesmo sentido, hora de partida e hora de chegada!";
        } else {
            $update_query = "UPDATE viagem SET linha_autocarro='$linha_autocarro', sentido='$sentido', origem='$origem', destino='$destino', hora_partida='$hora_partida', hora_chegada='$hora_chegada' WHERE id_viagem='$id_viagem'";
            if (mysqli_query($basedados, $update_query)) {
                echo "<script>window.location.href = 'linha_" . $linha_autocarro . ".php';</script>";
                exit();
            } else {
                $_SESSION['error_message'] = "Erro ao atualizar o registo: " . mysqli_error($basedados);
            }
        }
    }

$hora_partida = $registo['hora_partida'];
$hora_chegada = $registo['hora_chegada'];
?>

<html>
<head>
    <title>	EasyTicket </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_editar.css">
    <link rel="stylesheet" href="style_navbar.css">
    <link rel="icon" href="img/logo_EasyTicket.png">
</head>
<body>
    <script>
            function changeBackgroundColor(input, selected) {
        // Obtém o elemento (<td>) do input
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

        function fecharErro() {
            document.querySelector('.error').style.display = 'none';
        }

        function confirmaatualizar() {


    var sentido = document.getElementsByName('sentido')[0].value;
    var horaPartida = document.getElementsByName('hora_partida')[0].value;
    var horaChegada = document.getElementsByName('hora_chegada')[0].value;
    var linhaAutocarro = document.getElementsByName('linha_autocarro')[0].value;

    // Fazer a requisição AJAX para verificar a existência do registro
    fetch('verificar_trajeto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `sentido=${sentido}&hora_partida=${horaPartida}&hora_chegada=${horaChegada}&linha_autocarro=${linhaAutocarro}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.existe) {
            var errorText = document.querySelector('.error__text');
            errorText.innerHTML = "<span class='msg'>Já existe um registo com o mesmo sentido, hora de partida e hora de chegada!</span>";
            errorText.style.display = 'block';
            document.querySelector('.error').style.display = 'block';
            return false;
        } else {
            mostrarConfirmacao();
        }
    })
    .catch(error => console.error('Erro:', error));
}

function mostrarConfirmacao() {
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



        function ajustarHoraPartida() {
    var horaChegada = document.getElementById("hora_chegada").value;
    var horaPartida = document.getElementById("hora_partida");

    // Separar horas e minutos da hora de chegada
    var horaChegadaHoras = parseInt(horaChegada.split(":")[0]);
    var horaChegadaMinutos = parseInt(horaChegada.split(":")[1]);

    // Verificar a linha do autocarro
    var linhaAutocarro = '<?php echo $linha_autocarro; ?>';
    var inicioLinha, fimLinha;

    // Definir as horas de início e fim para cada linha de autocarro
    if (linhaAutocarro === '3001') {
        inicioLinha = 6;
        fimLinha = 22.5;
    } else if (linhaAutocarro === '3002') {
        inicioLinha = 6.25;
        fimLinha = 22.75;
    } else if (linhaAutocarro === '3301') {
        inicioLinha = 5;
        fimLinha = 22.5;
    } else if (linhaAutocarro === '3302') {
        inicioLinha = 6.5;
        fimLinha = 22.5;
    }

    // Calcular a nova hora de partida, 30 minutos antes da hora de chegada
    var novaHoraPartidaHoras = horaChegadaHoras;
    var novaHoraPartidaMinutos = horaChegadaMinutos - 30;
    if (novaHoraPartidaMinutos < 0) {
        novaHoraPartidaMinutos += 60;
        novaHoraPartidaHoras--;
    }

    // Verificar se a nova hora de partida é anterior à hora de início permitida
    if (novaHoraPartidaHoras < inicioLinha || (novaHoraPartidaHoras == inicioLinha && novaHoraPartidaMinutos < 0)) {
        novaHoraPartidaHoras = Math.ceil(inicioLinha);
        novaHoraPartidaMinutos = 0;
    }

    // Formatar a nova hora de partida
    var novaHoraPartida = (novaHoraPartidaHoras < 10 ? '0' : '') + novaHoraPartidaHoras + ":" + (novaHoraPartidaMinutos < 10 ? '0' : '') + novaHoraPartidaMinutos;

    horaPartida.value = novaHoraPartida;
}


function ajustarHoraChegada() {
    var horaPartida = document.getElementById("hora_partida").value;
    var horaChegada = document.getElementById("hora_chegada");

    // Separar horas e minutos da hora de partida
    var horaPartidaHoras = parseInt(horaPartida.split(":")[0]);
    var horaPartidaMinutos = parseInt(horaPartida.split(":")[1]);

    // Verificar a linha do autocarro
    var linhaAutocarro = '<?php echo $linha_autocarro; ?>';
    var inicioLinha, fimLinha;

    // Definir as horas de início e fim para cada linha de autocarro
    if (linhaAutocarro === '3001') {
        inicioLinha = 6;
        fimLinha = 22.5;
    } else if (linhaAutocarro === '3002') {
        inicioLinha = 6.25;
        fimLinha = 22.75;
    } else if (linhaAutocarro === '3301') {
        inicioLinha = 5;
        fimLinha = 22.5;
    } else if (linhaAutocarro === '3302') {
        inicioLinha = 6.5;
        fimLinha = 22.5;
    }

    // Calcular a nova hora de chegada, 30 minutos após a hora de partida
    var novaHoraChegadaHoras = horaPartidaHoras;
    var novaHoraChegadaMinutos = horaPartidaMinutos + 30;
    if (novaHoraChegadaMinutos >= 60) {
        novaHoraChegadaMinutos -= 60;
        novaHoraChegadaHoras++;
    }

    // Verificar se a nova hora de chegada é posterior à hora de fim permitida
    if (novaHoraChegadaHoras > fimLinha || (novaHoraChegadaHoras == fimLinha && novaHoraChegadaMinutos > 0)) {
        novaHoraChegadaHoras = Math.floor(fimLinha);
        novaHoraChegadaMinutos = 0;
    }

    // Formatar a nova hora de chegada
    var novaHoraChegada = (novaHoraChegadaHoras < 10 ? '0' : '') + novaHoraChegadaHoras + ":" + (novaHoraChegadaMinutos < 10 ? '0' : '') + novaHoraChegadaMinutos;

    horaChegada.value = novaHoraChegada;
}



function alterarOrigemDestino() {
    var sentido = document.getElementById("sentido").value;
    var origem = document.getElementById("origem");
    var destino = document.getElementById("destino");

    <?php if ($linha_autocarro == '3001'): ?>
        if (sentido === "ida") {
            origem.value = "Rua de Santo António";
            destino.value = "Estação Metro Portas Fronhas";
        } else if (sentido === "volta") {
            origem.value = "Estação Metro Portas Fronhas";
            destino.value = "Rua de Santo António";
        }
    <?php elseif ($linha_autocarro == '3002'): ?>
        if (sentido === "ida") {
            origem.value = "Torrinha";
            destino.value = "Estação Metro Portas Fronhas";
        } else if (sentido === "volta") {
            origem.value = "Estação Metro Portas Fronhas";
            destino.value = "Torrinha";
        }
    <?php elseif ($linha_autocarro == '3301'): ?>
        if (sentido === "ida") {
            origem.value = "Barranha";
            destino.value = "Estação Metro Vila do Conde";
        } else if (sentido === "volta") {
            origem.value = "Estação Metro Vila do Conde";
            destino.value = "Barranha";
        }
    <?php elseif ($linha_autocarro == '3302'): ?>
        if (sentido === "ida") {
            origem.value = "Zona Industrial de Amorim";
            destino.value = "Areia Tanque";
        } else if (sentido === "volta") {
            origem.value = "Areia Tanque";
            destino.value = "Zona Industrial de Amorim";
        }
    <?php endif; ?>
}

    </script>
<center>
    <div class="error" style="display: <?php echo isset($_SESSION['error_message']) ? 'block' : 'none'; ?>;">
        <div class="error__content">
            <div class="error__icon">
                <svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m13 13h-2v-6h2zm0 4h-2v-2h2zm-1-15c-1.3132 0-2.61358.25866-3.82683.7612-1.21326.50255-2.31565 1.23915-3.24424 2.16773-1.87536 1.87537-2.92893 4.41891-2.92893 7.07107 0 2.6522 1.05357 5.1957 2.92893 7.0711.92859.9286 2.03098 1.6651 3.24424 2.1677 1.21325.5025 2.51363.7612 3.82683.7612 2.6522 0 5.1957-1.0536 7.0711-2.9289 1.8753-1.8754 2.9289-4.4189 2.9289-7.0711 0-1.3132-.2587-2.61358-.7612-3.82683-.5026-1.21326-1.2391-2.31565-2.1677-3.24424-.9286-.92858-2.031-1.66518-3.2443-2.16773-1.2132-.50254-2.5136-.7612-3.8268-.7612z" fill="#393a37"></path></svg>
            </div>
            <div class="error__text">
                <?php if(isset($_SESSION['error_message'])) { echo $_SESSION['error_message']; unset($_SESSION['error_message']); } ?>
            </div>
			<div class="error__close" onclick="fecharErro()">
                    <svg height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="m15.8333 5.34166-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z" fill="#393a37"></path></svg>
            </div>
        </div>
    </div>
<br><br>
<a href="linha_<?php echo isset($linha_autocarro) ? $linha_autocarro : ''; ?>.php">Voltar</a>
<br><br>
<h1>Editar Trajeto - Linha <?php echo $linha_autocarro; ?> </h1>
    <br><br>
    <form method="post" action="linha_<?php echo $linha_autocarro; ?>.php" autocomplete="off" id="editarForm">
        <input type="hidden" name="id_viagem" value="<?php echo $registo['id_viagem']; ?>">
        <input type="hidden" name="linha_autocarro" value="<?php echo $linha_autocarro; ?>">
        <table>
            <tr hidden>
                    <td>Linha do Autocarro</td>
                    <td><input type="text" name="linha_autocarro" value="<?php echo $linha_autocarro; ?>" readonly style="outline: none; box-shadow: none;"></td>
            </tr>
            <tr>
                <td>Sentido</td>
                <td>
                    <input type="text" name="sentido" id="sentido" class="centered-input" style="outline: none; box-shadow: none;" readonly value="<?php echo ucfirst($registo['sentido']); ?>">
                </input>
                </td>
            </tr>

            <tr>
                <td>Origem</td>
                <td><input type="text" name="origem" id="origem" value="<?php echo $registo['origem']; ?>" class="centered-input" readonly style="outline: none; box-shadow: none;"></td>
            </tr>
            <tr>
                <td>Destino</td>
                <td><input type="text" name="destino" id="destino" value="<?php echo $registo['destino']; ?>" class="centered-input" readonly style="outline: none; box-shadow: none;"></td>
            </tr>
            <tr>
                <td>Hora de Partida</td>
                <td>
                <input name="hora_partida" id="hora_partida" class="centered-input" style="outline: none; box-shadow: none;" value="<?php echo $registo['hora_partida']; ?>" hidden selected><?php echo date('H:i', strtotime($registo['hora_partida'])); ?>
                </input>
                </td>
            </tr>
            <tr>
                <td>Hora de Chegada</td>
                <td>
                <input name="hora_chegada" id="hora_chegada" class="centered-input" style="outline: none; box-shadow: none;" value="<?php echo $registo['hora_chegada']; ?>" hidden selected><?php echo date('H:i', strtotime($registo['hora_chegada'])); ?>
                </input>
                </td>
            </tr>
        </table>
        <div class="button-container">
            <input type="button" value="Guardar Alterações" onClick="confirmaatualizar();">
            <input type="reset" value="Apagar Alterações">
        </div> 
        <br>
    </form>
</center>
</body>
</html>