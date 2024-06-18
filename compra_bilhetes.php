<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

// Verificar se o utilizador está autenticado
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

// Consultar as linhas de autocarro e os seus preços correspondentes
$consulta_linhas = "SELECT linha_autocarro, preco_bilhete FROM bilhetes";
$resultado_linhas = mysqli_query($basedados, $consulta_linhas);

// Verificar se há resultados
if (!$resultado_linhas || mysqli_num_rows($resultado_linhas) === 0) {
    exit();
}

// Definir a variável de mensagem de erro
$error_message = '';

// Consultar o saldo do utilizador
$consulta_saldo = "SELECT saldo FROM utilizadores WHERE id_utilizador = ?";
$stmt = mysqli_prepare($basedados, $consulta_saldo);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_utilizador']);
mysqli_stmt_execute($stmt);
$resultado_saldo = mysqli_stmt_get_result($stmt);

// Verificar se a consulta foi bem-sucedida e obter o saldo disponível
if ($resultado_saldo && mysqli_num_rows($resultado_saldo) > 0) {
    $dados_saldo = mysqli_fetch_assoc($resultado_saldo);
    $saldoDisponivel = floatval($dados_saldo['saldo']); // Definir saldo disponível
} else {
    // Tratar o erro de consulta ao saldo
    $error_message = "Erro ao consultar o saldo do utilizador.";
}

// Processar compra quando o formulário for submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $linha_autocarro = $_POST['linha_autocarro'];
    $preco_bilhete = floatval(str_replace(',', '.', $_POST['preco_bilhete'])); // Converter para float
    $quantidade = intval($_POST['quantidade']); // Converter para inteiro

    // Calcular o custo total da compra com base no preço do bilhete e na quantidade
    $custo_total = $preco_bilhete * $quantidade;
    $total_preco = $custo_total; // Atualizar também a variável $total_preco para exibição posterior

    // Verificar se o saldo é suficiente para a compra
    if ($saldoDisponivel >= $custo_total) {
        // Atualizar o saldo do utilizador de forma atômica
        $novo_saldo = $saldoDisponivel - $custo_total;
        $atualizar_saldo = "UPDATE utilizadores SET saldo = ? WHERE id_utilizador = ?";
        $stmt = mysqli_prepare($basedados, $atualizar_saldo);
        mysqli_stmt_bind_param($stmt, "di", $novo_saldo, $_SESSION['id_utilizador']);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Exibir mensagem de sucesso
            $success_message = "Compra efetuada com sucesso. O valor total de $total_preco foi retirado do seu saldo.";
        
            // Atualizar o saldo disponível para refletir a compra
            $saldoDisponivel = $novo_saldo;

            // Inserir os detalhes da compra na tabela compra_bilhete
            date_default_timezone_set('Europe/Lisbon'); // Definir o fuso horário para Lisboa
            $data_compra = date("Y-m-d H:i:s"); // Obtém a data e hora atual
            
            // Obter o ID do bilhete correspondente à linha de autocarro selecionada
            $consulta_id_bilhete = "SELECT id_bilhete FROM bilhetes WHERE linha_autocarro = ?";
            $stmt_id_bilhete = mysqli_prepare($basedados, $consulta_id_bilhete);
            mysqli_stmt_bind_param($stmt_id_bilhete, "s", $linha_autocarro);
            mysqli_stmt_execute($stmt_id_bilhete);
            $resultado_id_bilhete = mysqli_stmt_get_result($stmt_id_bilhete);

            // Verificar se a consulta foi bem-sucedida e obter o ID do bilhete
            if ($resultado_id_bilhete && mysqli_num_rows($resultado_id_bilhete) > 0) {
                $dados_id_bilhete = mysqli_fetch_assoc($resultado_id_bilhete);
                $id_bilhete = $dados_id_bilhete['id_bilhete']; // Obtém o ID do bilhete correspondente à linha selecionada
        
                // Consulta SQL para inserir os detalhes da compra na tabela compra_bilhete
                $inserir_compra = "INSERT INTO compra_bilhete (id_bilhete, id_utilizador, data_compra, preco_compra, quantidade_bilhetes) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($basedados, $inserir_compra);
                mysqli_stmt_bind_param($stmt, "iisdi", $id_bilhete, $_SESSION['id_utilizador'], $data_compra, $custo_total, $quantidade);
                mysqli_stmt_execute($stmt);
        
                // Verificar se a inserção foi bem-sucedida
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    // Atualizar a tabela totalbilhetesporlinha
                    $consulta_total = "SELECT total_bilhetes FROM totalbilhetesporlinha WHERE id_utilizador = ? AND id_bilhete = ?";
                    $stmt_total = mysqli_prepare($basedados, $consulta_total);
                    mysqli_stmt_bind_param($stmt_total, "ii", $_SESSION['id_utilizador'], $id_bilhete);
                    mysqli_stmt_execute($stmt_total);
                    $resultado_total = mysqli_stmt_get_result($stmt_total);

                    if ($resultado_total && mysqli_num_rows($resultado_total) > 0) {
                        // Se já houver registro, atualizar a quantidade total de bilhetes
                        $dados_total = mysqli_fetch_assoc($resultado_total);
                        $novo_total_bilhetes = $dados_total['total_bilhetes'] + $quantidade;
                        $atualizar_total = "UPDATE totalbilhetesporlinha SET total_bilhetes = ? WHERE id_utilizador = ? AND id_bilhete = ?";
                        $stmt_update_total = mysqli_prepare($basedados, $atualizar_total);
                        mysqli_stmt_bind_param($stmt_update_total, "iii", $novo_total_bilhetes, $_SESSION['id_utilizador'], $id_bilhete);
                        mysqli_stmt_execute($stmt_update_total);
                    } else {
                        // Se não houver registro, inserir um novo
                        $inserir_total = "INSERT INTO totalbilhetesporlinha (id_utilizador, id_bilhete, total_bilhetes) VALUES (?, ?, ?)";
                        $stmt_insert_total = mysqli_prepare($basedados, $inserir_total);
                        mysqli_stmt_bind_param($stmt_insert_total, "iii", $_SESSION['id_utilizador'], $id_bilhete, $quantidade);
                        mysqli_stmt_execute($stmt_insert_total);
                    }
                } else {
                    // Tratar o erro de inserção na tabela compra_bilhete
                    $error_message = "Erro ao inserir os detalhes da compra na base de dados.";
                }
            } else {
                // Tratar o erro de consulta do ID do bilhete
                $error_message = "Erro ao obter o ID do bilhete.";
            }

            // Redirecionar de volta à página de compra para evitar o reenvio do formulário
            echo '<script>window.location.replace("compra_bilhetes.php");</script>';
            exit();
        } else {
            // Tratar o erro de atualização do saldo
            $error_message = "Erro ao atualizar o saldo do utilizador.";
        }
    } else {
        // Se o saldo for insuficiente, exibir mensagem de erro
        $error_message = "O utilizador não tem saldo suficiente para efetuar a compra.";
    }
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyTicket</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <style>

@media screen and (min-width: 1920px) {
    form {
        background-color: rgb(40, 40, 40);
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        padding: 30px;
        border-radius: 10px;
        max-width: 500px;
        max-height: 630px;
        width: 100%;
        margin: auto; 
        margin-top: 20px; 
        text-align: center; 
    }
    h1 {
        font-size: 45px;
        color: #fff;
    }
    label {
        font-size: 20px;
        color: #fff;
    }
    select,
    input[type="text"],
    input[type="number"] {
        width: calc(100% - 320px); 
        padding: 8px;
        font-size: 15px;
        margin: 5px auto;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #333;
        color: #fff;
        text-align: center;
    }
    #total_preco {
        font-size: 20px;
        color: #fff;
        margin-top: 20px;
    }
}

    @media screen and (max-width: 1919px) and (min-width: 501px) {
    form {
        background-color: rgb(40, 40, 40);
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        padding: 20px; 
        border-radius: 10px;
        max-width: 400px; 
        max-height: 550px; 
        width: 100%;
        margin: auto;
        margin-top: 10px;
        text-align: center;
    }

    h1 {
        font-size: 35px; 
        color: #fff;
    }

    label {
        font-size: 18px; 
        color: #fff;
    }

    select,
    input[type="text"],
    input[type="number"] {
        width: calc(100% - 250px); /* Ajustado para 100% menos 40px */
        padding: 6px; /* Diminuído de 8px para 6px */
        font-size: 14px; /* Diminuído de 15px para 14px */
        margin: 1px auto; /* Centraliza horizontalmente com margem superior e inferior */
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #333;
        color: #fff;
        text-align: center; /* Centraliza elementos internamente */
    }

    #total_preco {
        font-size: 18px; /* Diminuído de 20px para 18px */
        color: #fff;
        margin-top: 10px;
    }
}

    </style>
</head>
<body>
<div class="saldo-disponivel"> 
   <center> <p>Saldo: <strong> <?php echo number_format($saldoDisponivel, 2, ',', '.'); ?>€</p> </strong></center>
    <br>
    <a href="carregar_saldo.php?id_utilizador=<?php echo $_SESSION['id_utilizador']; ?>">Carregar Saldo</a>
    <br>
    <a href="historico_compras.php?id_utilizador=<?php echo $_SESSION['id_utilizador']; ?>">Histórico de Compras</a>
</div>
    <center>
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

<div class="warning" style="display: none;">
<div class="error__content">
    <div class="warning__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" height="24" fill="none"><path fill="#393a37" d="m13 14h-2v-5h2zm0 4h-2v-2h2zm-12 3h22l-11-19z"></path></svg>
    </div>
        <div class="warning__text">
            <?php if(isset($_SESSION['error_message'])) { echo $_SESSION['error_message']; unset($_SESSION['error_message']); } ?>
    </div>
    <div class="warning__close" onclick="fecharErro()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 20 20" height="20"><path fill="#393a37" d="m15.8333 5.34166-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z"></path></svg>
    </div>
</div>
</div>
        <br>
        <a href="bilhetes.php">Voltar</a>
        <br><br>
        <h1>Compra de Bilhetes</h1>
        <br><br>
        <form action="compra_bilhetes.php" method="post">
            <label for="linha_autocarro">Escolha a Linha de Autocarro</label><br><br>
            <select name="linha_autocarro" id="linha_autocarro" onchange="atualizarPreco()">
                <?php
                // Exibir as opções de linha de autocarro
                while ($linha = mysqli_fetch_assoc($resultado_linhas)) {
                    echo '<option value="' . htmlspecialchars($linha['linha_autocarro']) . '" data-preco="' . htmlspecialchars($linha['preco_bilhete']) . '">' . htmlspecialchars($linha['linha_autocarro']) . '</option>';
                }
                ?>
            </select><br><br><br>
            <label for="preco_bilhete">Preço do Bilhete</label><br><br>
            <input type="text" id="preco_bilhete" name="preco_bilhete" readonly style="cursor: default;"><br><br><br>
            <label for="quantidade">Quantidade de Bilhetes</label><br><br>
            <input type="number" id="quantidade" name="quantidade" min="1" max="10" required oninput="apenasNumeros(event); verificarQuantidade(event)"><br><br><br>
            <label id="total_label" for="total_preco">Total</label><br>
            <input type="text" id="total_preco" name="total_preco" readonly style="cursor: default;"><br><br><br>
            <input type="button" value="Comprar Bilhetes" onclick="verificarQuantidadeAntesDeComprar()">
        </form>
        <br>
    </center>
</body>
<script>

        // Função para rolar de volta para o topo da página
        function backToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

function fecharErro() {
        document.querySelector('.error').style.display = 'none';
        document.querySelector('.warning').style.display = 'none';
    }
    
// Função para confirmar a compra
function confirmarCompra() {
    var overlay = document.createElement("div");
    overlay.className = "confirm-overlay";

    var box = document.createElement("div");
    box.className = "confirm-box";

    var question = document.createElement("p");
    question.className = "card-heading"; 
    question.innerText = "Deseja realmente comprar os bilhetes?";
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

    var confirmButton = document.createElement("button");
    confirmButton.className = "card-button primary-inserir"; 
    confirmButton.innerText = "Confirmar";
    confirmButton.onclick = function() {
        verificarSaldo();
        document.body.removeChild(overlay);
    };
    buttonWrapper.appendChild(confirmButton);

    box.appendChild(buttonWrapper);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
}

var saldoDisponivel = <?php echo isset($saldoDisponivel) ? $saldoDisponivel : 0; ?>;

// Função para verificar o saldo e enviar o formulário
function verificarSaldo() {
    // Obter o custo total da compra
    var custoTotal = parseFloat(document.getElementById("total_preco").value.replace(',', '.'));

    // Verificar se o saldo é suficiente para a compra
    if (saldoDisponivel < custoTotal) {
    var errorText = document.querySelector('.error__text');
    errorText.innerHTML = "<span>Não tens saldo suficiente para efetuar esta compra!</span>";
    errorText.style.display = 'block';
    document.querySelector('.error').style.display = 'block';
    backToTop();
    } else {
        document.querySelector('form').submit();
    }
}

    // Função para verificar e corrigir a entrada no campo de quantidade de bilhetes
    function verificarQuantidade(event) {
        // Obtém o valor atual do campo de quantidade
        var quantidade = event.target.value;

        // Se a quantidade for maior que 10, define o valor como 10
        if (quantidade > 10) {
            event.target.value = 10;
        } else if (quantidade === '0') { // Se a quantidade for "0", define o valor como 1
            event.target.value = 1;
        }
    }

    
// Função para atualizar o preço do bilhete na caixa de texto
function atualizarPreco() {
    var select = document.getElementById("linha_autocarro");
    var preco = select.options[select.selectedIndex].getAttribute("data-preco");
    // Converter o preço para um número com duas casas decimais
    preco = parseFloat(preco).toFixed(2);
    console.log(preco); // Adicionando um console.log para verificar o valor do preço
    document.getElementById("preco_bilhete").value = preco.replace('.', ',') + '€';

    // Atualizar o total do preço dos bilhetes
    atualizarTotal();
}



    // Função para permitir apenas números no input de número
    function apenasNumeros(event) {
        // Obtém o valor atual do input
        var input = event.target;
        // Filtra o valor para remover caracteres não numéricos
        input.value = input.value.replace(/[^\d]/g, '');

        // Atualizar o total do preço dos bilhetes
        atualizarTotal();
    }

    // Função para calcular e atualizar o total do preço dos bilhetes
    function atualizarTotal() {
        // Obter o valor da quantidade
        var quantidade = document.getElementById("quantidade").value;

        // Se a quantidade estiver vazia, definir o total como 0,00€
        if (quantidade === '') {
            document.getElementById("total_preco").value = '0,00€';
            return; // Sair da função
        }

        // Se a quantidade for 0, definir a quantidade como 1
        if (quantidade === '0') {
            quantidade = '1';
            document.getElementById("quantidade").value = quantidade; // Atualizar o campo quantidade
        }

        // Limitar a quantidade ao máximo de 10 bilhetes
        quantidade = Math.min(parseInt(quantidade), 10);

        // Obter o preço do bilhete
        var precoUnitario = parseFloat(document.getElementById("preco_bilhete").value.replace(',', '.'));

        // Calcular o total
        var total = precoUnitario * quantidade;

        // Exibir o total formatado
        document.getElementById("total_preco").value = total.toFixed(2).replace('.', ',') + '€';
    }

    // Função para verificar a quantidade antes de comprar
function verificarQuantidadeAntesDeComprar() {
    // Obter o valor da quantidade
    var quantidade = document.getElementById("quantidade").value;

    // Verificar se a quantidade está vazia
    if (quantidade === '') {
        var errorText = document.querySelector('.warning__text');
        errorText.innerHTML = "<span>Selecione a quantidade de bilhetes antes de comprar!</span>";
        errorText.style.display = 'block';
        document.querySelector('.warning').style.display = 'block';
    backToTop();
    } else {
        // Caso contrário, chamar a função para confirmar a compra
        confirmarCompra();
    }
}


    // Chamada à função atualizarPreco() quando a página é carregada
    window.onload = function() {
        atualizarPreco();
        atualizarTotal(); // Adicionado para calcular o total inicial
    };
</script>
</html>