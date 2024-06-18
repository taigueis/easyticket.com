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

	// Conexão com a Base de Dados
	require_once('ligarbd.php');

// Definir $linha_autocarro com um valor padrão 
if(isset($_GET['linha_autocarro'])){
    $_SESSION['linha_autocarro'] = $_GET['linha_autocarro'];
} else {
    $_SESSION['linha_autocarro'] = ''; // Ou qualquer valor padrão desejado
}


	// Verificar se o formulário foi submetido
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$linha_autocarro = $_POST['linha_autocarro'];
			$sentido = $_POST['sentido'];
			$origem = $_POST['origem'];
			$destino = $_POST['destino'];
			$hora_partida = $_POST['hora_partida'];
			$hora_chegada = $_POST['hora_chegada'];
	
			// Verificar se já existe um registro com o mesmo sentido, hora de partida e hora de chegada
			$verificar_existencia = "SELECT * FROM viagem WHERE sentido='$sentido' AND hora_partida='$hora_partida' AND hora_chegada='$hora_chegada' AND linha_autocarro = '$linha_autocarro'";
			$resultado_existencia = mysqli_query($basedados, $verificar_existencia);
	
			if (mysqli_num_rows($resultado_existencia) > 0) {
				$_SESSION['error_message'] ="Já existe um registo com o mesmo sentido, hora de partida e hora de chegada!";           
			} else {
				// Inserir o novo horário
				$inserir = "INSERT INTO viagem (linha_autocarro, sentido, origem, destino, hora_partida, hora_chegada) 
							VALUES ('$linha_autocarro', '$sentido', '$origem', '$destino', '$hora_partida', '$hora_chegada')";
				$sucesso = mysqli_query($basedados, $inserir);
	
				if (!$sucesso) {
					$_SESSION['error_message'] = 'Erro ao inserir os dados!';
				} else {
					echo "<script>window.location.href = 'linha_" . $linha_autocarro . ".php';</script>";
					exit(); // Certifique-se de sair do script após o redirecionamento
				}
			}
		}
	}
	?>
	<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="style_editar.css" rel="stylesheet" type="text/css">
		<link rel="icon" href="img/logo_EasyTicket.png">
		<title> EasyTicket</title>
	</head>
		<body>
			<script>
			
				// Função para definir valores padrão para origem, destino e hora de partida ao carregar a página
				window.onload = function() {
					<?php if ($_SESSION['linha_autocarro'] == '3001'): ?>
					document.getElementById("origem").value = "Rua de Santo António";
					document.getElementById("destino").value = "Estação Metro Portas Fronhas";
					document.getElementById("hora_partida").value = "06:00"; // Definindo hora de partida como 06:00
					document.getElementById("hora_chegada").value = "06:30"; // Definindo hora de chegada como 06:30
					
					<?php elseif ($_SESSION['linha_autocarro'] == '3002'): ?>
					document.getElementById("origem").value = "Torrinha";
					document.getElementById("destino").value = "Estação Metro Portas Fronhas";
					document.getElementById("hora_partida").value = "06:15"; // Definindo hora de partida como 06:15
					document.getElementById("hora_chegada").value = "06:45"; // Definindo hora de chegada como 06:45
					
					<?php elseif ($_SESSION['linha_autocarro'] == '3301'): ?>
					document.getElementById("origem").value = "Barranha";
					document.getElementById("destino").value = "Estação Metro Vila do Conde";
					document.getElementById("hora_partida").value = "05:00";
					document.getElementById("hora_chegada").value = "05:30";
					
					<?php elseif ($_SESSION['linha_autocarro'] == '3302'): ?>
					document.getElementById("origem").value = "Zona Industrial de Amorim";
					document.getElementById("destino").value = "Areia Tanque";
					document.getElementById("hora_partida").value = "06:30";
					document.getElementById("hora_chegada").value = "07:00";
					
					<?php endif; ?>
					
					ajustarHoraPartida(); // Ajusta a hora de partida ao carregar a página
					ajustarHoraChegada(); // Ajusta a hora de chegada ao carregar a página
				};

				function ajustarHoraPartida() {
					var horaChegada = document.getElementById("hora_chegada").value;
					var horaPartida = document.getElementById("hora_partida");

					var horaChegadaMinutos = parseInt(horaChegada.split(":")[0]) * 60 + parseInt(horaChegada.split(":")[1]);
					var horaPartidaMinutos = horaChegadaMinutos - 30;

					var novaHoraPartidaHoras = Math.floor(horaPartidaMinutos / 60);
					var novaHoraPartidaMinutos = horaPartidaMinutos % 60;

					var novaHoraPartida = (novaHoraPartidaHoras < 10 ? '0' : '') + novaHoraPartidaHoras + ":" + (novaHoraPartidaMinutos < 10 ? '0' : '') + novaHoraPartidaMinutos;

					horaPartida.value = novaHoraPartida;
				}

				function ajustarHoraChegada() {
					var horaPartida = document.getElementById("hora_partida").value;
					var horaChegada = document.getElementById("hora_chegada");

					var horaPartidaMinutos = parseInt(horaPartida.split(":")[0]) * 60 + parseInt(horaPartida.split(":")[1]);
					var horaChegadaMinutos = horaPartidaMinutos + 30;

					var novaHoraChegadaHoras = Math.floor(horaChegadaMinutos / 60);
					var novaHoraChegadaMinutos = horaChegadaMinutos % 60;

					var novaHoraChegada = (novaHoraChegadaHoras < 10 ? '0' : '') + novaHoraChegadaHoras + ":" + (novaHoraChegadaMinutos < 10 ? '0' : '') + novaHoraChegadaMinutos;

					horaChegada.value = novaHoraChegada;
				}

				function alterarOrigemDestino() {
        var sentido = document.getElementById("sentido").value;
        var origem = document.getElementById("origem");
        var destino = document.getElementById("destino");

        <?php if ($_SESSION['linha_autocarro'] == '3001'): ?>
        if (sentido === "ida") {
            origem.value = "Rua de Santo António";
            destino.value = "Estação Metro Portas Fronhas";
        } else if (sentido === "volta") {
            origem.value = "Estação Metro Portas Fronhas";
            destino.value = "Rua de Santo António";
        }
        
        <?php elseif ($_SESSION['linha_autocarro'] == '3002'): ?>
        if (sentido === "ida") {
            origem.value = "Torrinha";
            destino.value = "Estação Metro Portas Fronhas";
        } else if (sentido === "volta") {
            origem.value = "Estação Metro Portas Fronhas";
            destino.value = "Torrinha";
        }
        
        <?php elseif ($_SESSION['linha_autocarro'] == '3301'): ?>
        if (sentido === "ida") {
            origem.value = "Barranha";
            destino.value = "Estação Metro Vila do Conde";
        } else if (sentido === "volta") {
            origem.value = "Estação Metro Vila do Conde";
            destino.value = "Barranha";
        }
        
        <?php elseif ($_SESSION['linha_autocarro'] == '3302'): ?>
        if (sentido === "ida") {
            origem.value = "Zona Industrial de Amorim";
            destino.value = "Areia Tanque";
        } else if (sentido === "volta") {
            origem.value = "Areia Tanque";
            destino.value = "Zona Industrial de Amorim";
        }
        
        <?php endif; ?>
    }

	function resetValoresPadrao() {
    // Definir o sentido como "ida"
    document.getElementById("sentido").value = "ida";

    // Definir os valores padrão de hora de partida e chegada
    <?php if ($_SESSION['linha_autocarro'] == '3001'): ?>
    document.getElementById("hora_partida").value = "06:00";
    document.getElementById("hora_chegada").value = "06:30";
    
    <?php elseif ($_SESSION['linha_autocarro'] == '3002'): ?>
    document.getElementById("hora_partida").value = "06:15";
    document.getElementById("hora_chegada").value = "06:45";
    
    <?php elseif ($_SESSION['linha_autocarro'] == '3301'): ?>
    document.getElementById("hora_partida").value = "05:00";
    document.getElementById("hora_chegada").value = "05:30";
    
    <?php elseif ($_SESSION['linha_autocarro'] == '3302'): ?>
    document.getElementById("hora_partida").value = "06:30";
    document.getElementById("hora_chegada").value = "07:00";
    
    <?php endif; ?>


    <?php if ($_SESSION['linha_autocarro'] == '3001'): ?>
    document.getElementById("origem").value = "Rua de Santo António";
    document.getElementById("destino").value = "Estação Metro Portas Fronhas";
    <?php elseif ($_SESSION['linha_autocarro'] == '3002'): ?>
    document.getElementById("origem").value = "Torrinha";
    document.getElementById("destino").value = "Estação Metro Portas Fronhas";
    <?php elseif ($_SESSION['linha_autocarro'] == '3301'): ?>
    document.getElementById("origem").value = "Barranha";
    document.getElementById("destino").value = "Estação Metro Vila do Conde";
    <?php elseif ($_SESSION['linha_autocarro'] == '3302'): ?>
    document.getElementById("origem").value = "Zona Industrial de Amorim";
    document.getElementById("destino").value = "Areia Tanque";
    <?php endif; ?>
}

function fecharErro() {
            document.querySelector('.error').style.display = 'none';
        }
	</script>
	<center>
	<br>
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
	<a href="linha_<?php echo isset($_SESSION['linha_autocarro']) ? $_SESSION['linha_autocarro'] : ''; ?>.php">Voltar</a>
	<br>
	<h1>Inserir Novo Horário - Linha <?php echo $_SESSION['linha_autocarro']; ?> </h1>
	<br>
	<form method="post" action="horarios_novo.php?linha_autocarro=<?php echo $_SESSION['linha_autocarro']; ?>" autocomplete="off" id="editarForm">
        	<table>
				<table>
					<tr hidden>
						<td>Linha do Autocarro</td>
						<td><input type="text" name="linha_autocarro" value="<?php echo $_SESSION['linha_autocarro']; ?>" class="centered-input" readonly style="outline: none; box-shadow: none;"></td>
					</tr>

					<tr>
						<td>Sentido</td>
						<td>
							<select name="sentido" id="sentido" class="centered-input" style="outline: none; box-shadow: none;" required onchange="alterarOrigemDestino()" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)">
								<option value="ida">Ida</option>
								<option value="volta">Volta</option>
							</select>
						</td>
					</tr>
	<tr>
		<td>Origem</td>
		<td><input type="text" name="origem" id="origem" value="" class="centered-input" readonly style="outline: none; box-shadow: none;"></td>
	</tr>
	<tr>
		<td>Destino</td>
		<td><input type="text" name="destino" id="destino" value="" class="centered-input" readonly style="outline: none; box-shadow: none;"></td>
	</tr>
		<tr>
		<td>Insira a Hora de Partida</td>
		<td>
			<select name="hora_partida" id="hora_partida" onchange="ajustarHoraChegada()" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)" class="centered-input" style="outline: none; box-shadow: none;" required>
				<?php
					// Definir a primeira e última hora de partida conforme a linha de autocarro
					if ($_SESSION['linha_autocarro'] == '3001') {
						$start = strtotime('06:00');
						$end = strtotime('22:30');
					} elseif ($_SESSION['linha_autocarro'] == '3002') {
						$start = strtotime('06:15');
						$end = strtotime('22:45');
					}
					elseif ($_SESSION['linha_autocarro'] == '3301') {
						$start = strtotime('05:00');
						$end = strtotime('22:30');
					}
					elseif ($_SESSION['linha_autocarro'] == '3302') {
						$start = strtotime('06:30');
						$end = strtotime('22:30');
					}

					// Iterar sobre o intervalo e exibir as opções de seleção
					for ($i = $start; $i <= $end; $i += 30 * 60) {
						echo "<option value='" . date('H:i', $i) . "'>" . date('H:i', $i) . "</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Insira a Hora de Chegada</td>
		<td>
			<select name="hora_chegada" id="hora_chegada" onchange="ajustarHoraPartida()" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)" class="centered-input" style="outline: none; box-shadow: none;" required>
				<?php
					// Definir a primeira e última hora de chegada conforme a linha de autocarro
					if ($_SESSION['linha_autocarro'] == '3001') {
						$start_chegada = strtotime('06:30');
						$end_chegada = strtotime('23:00');
					} elseif ($_SESSION['linha_autocarro'] == '3002') {
						$start_chegada = strtotime('06:45');
						$end_chegada = strtotime('23:15');
					}
					elseif ($_SESSION['linha_autocarro'] == '3301') {
						$start_chegada = strtotime('05:30');
						$end_chegada = strtotime('23:00');
					}
					elseif ($_SESSION['linha_autocarro'] == '3302') {
						$start_chegada = strtotime('07:00');
						$end_chegada = strtotime('23:00');
					}

					// Iterar sobre o intervalo e exibir as opções de seleção
					for ($i = $start_chegada; $i <= $end_chegada; $i += 30 * 60) {
						echo "<option value='" . date('H:i', $i) . "'>" . date('H:i', $i) . "</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><input type="submit" value="Inserir Dados"></td>
		<td><input type="button" value="Limpar Dados" onclick="resetValoresPadrao()"></td>
	</tr>
</form>
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
</body>
</html>