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

// Chamar o menu adequado com base no tipo de utilizador
if ($_SESSION['tipo_utilizador'] == 'admin') {        
    require_once('menu.php');
} elseif ($_SESSION['tipo_utilizador'] == 'user') {        
    require_once('menu2.php');
} elseif ($_SESSION['tipo_utilizador'] == 'semregisto') {        
    require_once('menu3.php');
}

// Fazer a ligação à Base de Dados
require_once('ligarbd.php');

$linha_autocarro = 3301;

// Função para inserir um novo horário
function inserirHorario($basedados) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $linha_autocarro = $_POST['linha_autocarro'];
        $sentido = $_POST['sentido'];
        $origem = $_POST['origem'];
        $destino = $_POST['destino'];
        $hora_partida = $_POST['hora_partida'];
        $hora_chegada = $_POST['hora_chegada'];

        // Verificar se já existe um registo com o mesmo sentido, hora de partida e hora de chegada
        $verificar_existencia = "SELECT * FROM viagem WHERE sentido='$sentido' AND hora_partida='$hora_partida' AND hora_chegada='$hora_chegada' AND linha_autocarro = '$linha_autocarro'";
        $resultado_existencia = mysqli_query($basedados, $verificar_existencia);

        if (mysqli_num_rows($resultado_existencia) > 0) {
            echo '<script>';
            echo 'var errorText = document.querySelector(".error__text");';
            echo 'errorText.innerHTML = "<span class=\'msg\'>Já existe um registo com o mesmo sentido, hora de partida e hora de chegada!</span>";';
            echo 'errorText.style.display = "block";';
            echo 'document.querySelector(".error").style.display = "block";';
            echo '</script>';            
        } else {
            // Inserir o novo horário
            $inserir = "INSERT INTO viagem (linha_autocarro, sentido, origem, destino, hora_partida, hora_chegada) 
                        VALUES ('$linha_autocarro', '$sentido', '$origem', '$destino', '$hora_partida', '$hora_chegada')";
            $sucesso = mysqli_query($basedados, $inserir);

            if (!$sucesso) {
                $_SESSION['error_message'] = 'Erro ao inserir os dados!';
            } else {
                // Inserção bem-sucedida
            }
        }
    }
}

// Função para atualizar um horário existente
function atualizarHorario($basedados) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_viagem'])) {
        $id_viagem = $_POST['id_viagem'];
        $linha_autocarro = 3301;
        $sentido = $_POST['sentido'];
        $origem = $_POST['origem'];
        $destino = $_POST['destino'];
        $hora_partida = $_POST['hora_partida'];
        $hora_chegada = $_POST['hora_chegada'];

        // Verificar se já existe um registo igual
        $verificar_duplicatas = "SELECT * FROM viagem WHERE id_viagem != '$id_viagem' AND sentido='$sentido' AND hora_partida='$hora_partida' AND hora_chegada='$hora_chegada' AND linha_autocarro = '$linha_autocarro'";
        $resultado_duplicatas = mysqli_query($basedados, $verificar_duplicatas);

        if (mysqli_num_rows($resultado_duplicatas) > 0) {
            echo '<script>';
        echo 'var errorText = document.querySelector(".error__text");';
        echo 'errorText.innerHTML = "<span class=\'msg\'>Já existe um registo com o mesmo sentido, hora de partida e hora de chegada!</span>";';
        echo 'errorText.style.display = "block";';
        echo 'document.querySelector(".error").style.display = "block";';
        echo 'backToTop();'; // Supondo que a função backToTop() já esteja definida em seu JavaScript
        echo '</script>';
        } else {
            // Atualizar os dados
            $atualizar = "UPDATE viagem SET linha_autocarro='$linha_autocarro', sentido='$sentido', 
                          origem='$origem', destino='$destino', hora_partida='$hora_partida', hora_chegada='$hora_chegada' 
                          WHERE id_viagem='$id_viagem'";
            $sucesso = mysqli_query($basedados, $atualizar);

            if (!$sucesso) {
                $_SESSION['error_message'] = 'Erro ao atualizar os dados!';
            } else {
            }
        }
    }
}

// Função para excluir um horário existente
function excluirHorario($basedados) {
    if (isset($_GET['id'])) {
        $id_viagem = $_GET['id'];

        $excluir = "DELETE FROM viagem WHERE id_viagem='$id_viagem'";
        $sucesso = mysqli_query($basedados, $excluir);

        if (!$sucesso) {
            $_SESSION['error_message'] = 'Erro ao excluir os dados!';
        } else {
        }
    }
}

// Chamar as funções para inserir, atualizar e excluir dados
inserirHorario($basedados);
atualizarHorario($basedados);
excluirHorario($basedados);


// Criar consulta para buscar os horários
$consulta_ida = "SELECT id_viagem, linha_autocarro, origem, destino, SUBSTRING(hora_partida, 1, 5) AS hora_partida, SUBSTRING(hora_chegada, 1, 5) AS hora_chegada 
             FROM viagem WHERE sentido='ida' AND linha_autocarro = '$linha_autocarro' ORDER BY hora_partida";
$resultado_ida = mysqli_query($basedados, $consulta_ida);
$nregistos_ida = mysqli_num_rows($resultado_ida);

$consulta_volta = "SELECT id_viagem, linha_autocarro, origem, destino, SUBSTRING(hora_partida, 1, 5) AS hora_partida, SUBSTRING(hora_chegada, 1, 5) AS hora_chegada 
             FROM viagem WHERE sentido='volta' AND linha_autocarro = '$linha_autocarro' ORDER BY hora_partida";
$resultado_volta = mysqli_query($basedados, $consulta_volta);
$nregistos_volta = mysqli_num_rows($resultado_volta);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <title>EasyTicket</title>
</head>
<body>
<center>
    <br>
    <div class="error" style="display: none;">
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
<center>
<a href="inicio.php#horarios">Voltar</a>
<br>
<h1>Horário - Linha 3301</h1>
    <br>
    <?php
if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin") {
	?>
    <p style="cursor:pointer;" onclick="scrollToBottom()">Adicionar Trajeto</p>
    <br><br>
<?php
}
?>
<?php
if ($nregistos_ida == 0 && $nregistos_volta == 0) {
    // Se não houver registos de ida e volta, mostra apenas a seção de inserir novo trajeto
    echo "<h1>Não há registos existentes para esta linha!</h1>";
    echo '<br><br><br><br>';
	if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin") {
    echo "<h2>Inserir Novo Trajeto</h2>";
    echo "<br>";
    echo "<a onclick='confirmainserir();'>";
    echo "<img src='img/inserir.png' title='Inserir' style='cursor: pointer;' >";
    echo "</a>";
    echo "<table>";
    echo "<tr><td style='background-color: #1a1a1a;'>&nbsp;</td></tr>";
    echo "</table>";
	}
    echo "<br>";
} else {
    // Se houver registos de ida ou volta, exibe tudo
    ?>

<table class="trajeto-table">
    <?php
    // Se não houver registos de ida, mostra mensagem de "Não há registos disponíveis"
    if ($nregistos_ida == 0) {
        echo '<br><br><br><br>';
        echo "<p>Não há registos disponíveis para o sentido de ida.</p>";
    } else {
        // IDA
        if ($nregistos_volta > 0) {
			// Marcador "Sentido Volta" em cima da tabela de Ida
            echo '<div class="spacing"></div>';
            echo '<p class="section-link"><a href="javascript:void(0)" onclick="scrollToSection(\'volta\')">Sentido Volta</a></p>';
            echo '<img src="img/seta_baixo.png" class="section-img" onclick="scrollToSection(\'volta\')">';
            echo '<div class="spacing"></div>';    
        }
        echo '<tr>';
        echo '<br>';
        echo '<td colspan="7" style="text-align: center; font-weight: bold;" id="ida">IDA</td>';
        echo '<tr><td style="background-color: #1a1a1a;">&nbsp;</td></tr>';
        echo '</tr>';
        echo '<tr>';    
        echo '<th> Origem </th>'; 
        echo '<th> Destino </th>';
        echo '<th> Hora de Partida </th>'; 
        echo '<th> Hora de Chegada </th>';
        // Verifica se o utilizador está logado e se é do tipo "admin"
        if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin") {
            echo '<th> Eliminar </th>';
        }          
        echo '</tr>';

        // Mostrar todos os registos da consulta IDA
        for ($i = 0; $i < $nregistos_ida; $i++) {
            $registo = mysqli_fetch_array($resultado_ida);
            echo '<tr>';
            echo '<td>'.$registo['origem'].'</td>';
            echo '<td>'.$registo['destino'].'</td>';
            echo '<td>'.$registo['hora_partida'].'</td>';
            echo '<td>'.$registo['hora_chegada'].'</td>';
            
            // Verifica se o utilizador está logado e se é do tipo "admin"
            if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin") { 
                                
                echo '<td align="center"> <a onClick="confirmaremover(' . $registo['id_viagem'] . ');" style="cursor: pointer;">';
                echo '<img class="img-icon" src="img/eliminar.png"> </a></td>';
            }
            
            echo '</tr>';
        }
    }
    ?>
</table>
<br>
<table class="trajeto-table">
    <?php
    // Se não houver registos de volta, mostra mensagem de "Não há registos disponíveis"
    if ($nregistos_volta == 0) {
        echo '<br><br><br><br>';
        echo "<p>Não há registos disponíveis para o sentido de volta.</p>";
    } else {
        // VOLTA 
        if ($nregistos_ida > 0) {
			// Marcador "Sentido Ida" em cima da tabela Volta
            echo '<div class="spacing"></div>';
            echo '<img src="img/seta_cima.png" class="section-img" onclick="scrollToSection(\'ida\')">';
            echo '<p class="section-link"><a href="javascript:void(0)" onclick="scrollToSection(\'ida\')">Sentido Ida</a></p>';
            echo '<div class="spacing"></div>';
        }
        echo '<tr>';
        echo '<tr><td style="background-color: #1a1a1a;">&nbsp;</td></tr>';
        echo '<td colspan="7" style="text-align: center; font-weight: bold;" id="volta">VOLTA</td>';
        echo '<tr><td style="background-color: #1a1a1a;">&nbsp;</td></tr>';
        echo '</tr>';
        echo '<tr>'; 
        echo '<th> Origem </th>'; 
        echo '<th> Destino </th>';
        echo '<th> Hora de Partida </th>'; 
        echo '<th> Hora de Chegada </th>';
        // Verifica se o utilizador está logado e se é do tipo "admin"
        if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin") {
            echo '<th> Eliminar </th>';
        }  
        echo '</tr>';

        // Mostrar todos os registos da consulta VOLTA
        for ($i = 0; $i < $nregistos_volta; $i++) {
            $registo = mysqli_fetch_array($resultado_volta);
            echo '<tr>';
            echo '<td>'.$registo['origem'].'</td>';
            echo '<td>'.$registo['destino'].'</td>';
            echo '<td>'.$registo['hora_partida'].'</td>';
            echo '<td>'.$registo['hora_chegada'].'</td>';
            
            // Verifica se o utilizador está logado e se é do tipo "admin"
			if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin") {
								
				echo '<td align="center"> <a onClick="confirmaremover(' . $registo['id_viagem'] . ');" style="cursor: pointer;">';
				echo '<img class="img-icon" src="img/eliminar.png"> </a></td>';
			}
            
            echo '</tr>';
        }
    }
    ?>
</table>

<?php
if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin") {
	?>
<br><br>
<h2>Inserir Novo Trajeto</h2>
<br>
<a onclick="confirmainserir('<?php echo $linha_autocarro; ?>');">
    <img src="img/inserir.png" title="Inserir" style="cursor: pointer;" >
</a>
<table>
    <tr><td style="background-color: #1a1a1a;">&nbsp;</td></tr>
</table>
<?php
}
?>

<br><br><br>
<a class="back-to-top-link" onclick="backToTop()" id="back-to-top">
  <img src="img/seta.png" class="back-to-top-image">
</a>
<?php
// Se houver registos tanto de ida quanto de volta, exibe os marcadores de sentido no final da página
if ($nregistos_ida > 0 && $nregistos_volta > 0) {
?>
    <br>
    <p class="section-link"> <a href="javascript:void(0)" onclick="scrollToSection('ida')"><strong>IDA</strong></a> &nbsp;|&nbsp; <a href="javascript:void(0)" onclick="scrollToSection('volta')"><strong>VOLTA</strong></a> </p>
<?php
    }
}
?>
    <br><br>
</center>

<script>

    // JavaScript para mostrar a caixa de erro se houver uma mensagem de erro definida
    window.onload = function() {
             var errorMessage = "<?php echo isset($_SESSION['error_message']) ? $_SESSION['error_message'] : ''; ?>"; if(errorMessage !== '') { document.querySelector('.error').style.display = 'block'; } 
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
            
       function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth', block: 'start' });
        }


        // Função para rolar para o fim da página
        function scrollToBottom() {
            window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' });
        }

        // Função para rolar de volta para o topo da página
        function backToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Mostra o botão de voltar ao topo quando o usuário rolar para baixo
        window.onscroll = function() {
            scrollFunction()
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("back-to-top").style.display = "block";
            } else {
                document.getElementById("back-to-top").style.display = "none";
            }
        }
		
function fecharErro() {
        document.querySelector('.error').style.display = 'none';
    }

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
        document.location.href = "horarios_eliminar.php?id=" + id + "&linha_autocarro=<?php echo $linha_autocarro; ?>";

    };
    buttonWrapper.appendChild(deleteButton);

    box.appendChild(buttonWrapper);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
}

function confirmainserir(linha_autocarro) {
    // Verificar se já existem viagens com as mesmas características
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'verificar_existencia_viagem.php?linha_autocarro=' + linha_autocarro, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.existemViagens) {
                // Se existirem viagens com as mesmas características, exibir uma mensagem de erro
                var errorText = document.querySelector('.error__text');
                errorText.innerHTML = "<span class='msg'>Não é possível adicionar mais trajetos para esta linha!</span>";
                errorText.style.display = 'block';
                document.querySelector('.error').style.display = 'block';
                // Chamar a função para rolar de volta para o topo da página
                backToTop();
            } else {
                // Se não existem viagens com as mesmas características, verificar o número de registos
                if (linha_autocarro === '3301' && response.num_registos >= 68) {
                    // Não é possível adicionar mais registos para a linha 3301
                    var errorText = document.querySelector('.error__text');
                    errorText.innerHTML = "<span class='msg'>Não é possível adicionar mais trajetos para esta linha!</span>";
                    errorText.style.display = 'block';
                    document.querySelector('.error').style.display = 'block';
                    // Chamar a função para rolar de volta para o topo da página
                    backToTop();
                } else {
                    // É possível adicionar mais registos, exibir a caixa de diálogo de confirmação
                    showConfirmationDialog(linha_autocarro);
                }
            }
        }
    };
    xhr.send();
}

function showConfirmationDialog(linha_autocarro) {
    // Aqui continua o restante do código para criar a caixa de diálogo de confirmação (overlay)
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

    var insertButton = document.createElement("button");
    insertButton.className = "card-button primary-inserir"; 
    insertButton.innerText = "Inserir";
    insertButton.onclick = function() {
        // Redireciona para horarios_novo.php, passando o valor da linha_autocarro
        document.location.href = "horarios_novo.php?linha_autocarro=" + linha_autocarro;
    };
    buttonWrapper.appendChild(insertButton);

    box.appendChild(buttonWrapper);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
}

window.onload = function() {
            document.querySelector('.error').style.display = 'none';
        };
</script>
</body>
</html>