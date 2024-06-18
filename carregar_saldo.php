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

// Processar o carregamento de saldo quando o formulário for submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $nome_titular = $_POST['input-name'];
    $numero_cartao = $_POST['card_number'];
    $cvv = $_POST['cvv'];
    $saldo_a_carregar = floatval(str_replace(',', '.', $_POST['saldo_a_carregar']));

    // Receber os valores do formulário
    $expiration_month = $_POST['expiration_month'];
    $expiration_year = $_POST['expiration_year'];

    // Concatenar para formar a data de validade completa (M/YY)
    $data_validade = $expiration_month . '/' . substr($expiration_year, -2);

    // Verificar se o saldo a carregar é um valor válido e maior que zero
    if ($saldo_a_carregar <= 0 || !is_numeric($saldo_a_carregar)) {
        // Lógica para lidar com saldo inválido
        $_SESSION['error_message'] = "Saldo inválido. Por favor, insira um valor positivo.";
    } else {
        // Atualizar o saldo do utilizador
        $id_utilizador = $_SESSION['id_utilizador'];
        $consulta_atualizar_saldo = "UPDATE utilizadores SET saldo = saldo + ? WHERE id_utilizador = ?";
        $stmt = mysqli_prepare($basedados, $consulta_atualizar_saldo);
        mysqli_stmt_bind_param($stmt, "di", $saldo_a_carregar, $id_utilizador);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {

            // Inserir os dados na tabela depositos_utilizador
            $consulta_inserir_deposito = "INSERT INTO depositos_utilizador (id_utilizador, nome_titular_cartao, numero_cartao, data_validade_cartao, cvv, valor_depositado) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_deposito = mysqli_prepare($basedados, $consulta_inserir_deposito);
            mysqli_stmt_bind_param($stmt_deposito, "issssd", $id_utilizador, $nome_titular, $numero_cartao, $data_validade, $cvv, $saldo_a_carregar);
            mysqli_stmt_execute($stmt_deposito);

            if (mysqli_stmt_affected_rows($stmt_deposito) > 0) {
                // Redirecionar para a página anterior com mensagem de sucesso
                echo '<script>window.location.href = "perfil.php";</script>';
                exit();
            } else {
                // Se a inserção falhar, exibir mensagem de erro
                $_SESSION['error_message'] = "Erro ao inserir os dados do depósito. Por favor, tente novamente.";
            }
        } else {
            // Se a atualização do saldo falhar, exibir mensagem de erro
            $_SESSION['error_message'] = "Erro ao carregar o saldo. Por favor, tente novamente.";
        }
    }
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyTicket - Carregar Saldo</title>
    <link href="style_editar.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
</head>
<body>
<script>

function updateExpirationDate() {
    var monthSelect = document.getElementById("expiration_month");
    var yearSelect = document.getElementById("expiration_year");
    var expirationDateField = document.getElementById("data_validade");

    var month = monthSelect.value;
    var year = yearSelect.value;

    var currentDate = new Date();
    var currentMonth = currentDate.getMonth() + 1; // Mês atual (Janeiro é 0, então adicionamos 1)
    var currentYear = currentDate.getFullYear().toString().slice(-2); // Dois últimos dígitos do ano

    if (!month) {
        monthSelect.setCustomValidity('Selecione o mês de validade.');
    } else if (year === currentYear && parseInt(month) < currentMonth) {
        monthSelect.setCustomValidity('O mês de validade não pode ser anterior ao mês atual.');
    } else {
        monthSelect.setCustomValidity('');
    }

    if (!year) {
        yearSelect.setCustomValidity('Selecione o ano de validade.');
    } else {
        yearSelect.setCustomValidity('');
    }

    if (month && year) {
        expirationDateField.value = month + '/' + year;
    }
}

        function allowOnlyLettersAndSingleSpace(event) {
            const input = event.target;
            const regex = /^[a-zA-ZÀ-ÖØ-öø-ÿ]+(?: [a-zA-ZÀ-ÖØ-öø-ÿ]*)*$/; // Permite letras (inclusive acentuadas) e um único espaço entre elas
            if (!regex.test(input.value)) {
                input.value = input.value.replace(/[^a-zA-ZÀ-ÖØ-öø-ÿ ]/g, ''); // Remove qualquer caractere que não seja letra ou espaço
            }
        }

function allowOnlyNumbersCVV(event) {
    const input = event.target;
    input.value = input.value.replace(/\D/g, ''); // Permite apenas números
}

function formatCardNumber(event) {
    const input = event.target;
    const maxLength = 19;
    let formattedValue = input.value.replace(/\D/g, '') // Remove todos os caracteres que não são dígitos
                                    .substring(0, maxLength) // Limita o comprimento máximo
                                    .replace(/(\d{4})(?!$)/g, '$1 '); // Insere um espaço a cada 4 dígitos
    input.value = formattedValue;

    // Validar o número do cartão
    const cardNumber = input.value.replace(/\s/g, ''); // Remove os espaços em branco
    if (cardNumber.length < 16) {
        input.setCustomValidity('O número do cartão deve ter no mínimo 16 números.');
    } else {
        input.setCustomValidity('');
    }
}


function allowOnlyNumbers(event) {
    const input = event.target;
    let inputValue = input.value;
    const regex = /[^0-9,.]/g; // Expressão regular para encontrar caracteres que não são números, vírgulas ou pontos
    const maxInputValue = 100; // Valor máximo permitido

    // Remover caracteres não permitidos
    inputValue = inputValue.replace(regex, '');

    // Substituir pontos por vírgulas
    inputValue = inputValue.replace(/\./g, ',');

    // Limitar o número total de caracteres (incluindo a vírgula) a 6
    if (inputValue.length > 6) {
        inputValue = inputValue.slice(0, 6);
    }

    // Verificar se o valor de carregamento é menor ou igual a zero
    const floatValue = parseFloat(inputValue.replace(',', '.'));
    if (floatValue <= 0) {
        input.setCustomValidity('O mínimo de carregamento é de 0,01€.');
    } else {
        // Verificar se o valor de carregamento ultrapassa o valor máximo permitido
        if (!isNaN(floatValue) && floatValue > maxInputValue) {
            input.setCustomValidity('O valor máximo de carregamento é de 100€.');
        } else {
            input.setCustomValidity('');
        }
    }

    // Verificar e limitar o número de dígitos antes e após a vírgula
    const parts = inputValue.split(',');
    if (parts.length > 1) {
        // Se houver casas decimais
        if (parts[0].length > 3) {
            // Se o número de dígitos antes da vírgula for maior que 3, ajusta para o máximo
            inputValue = parts[0].slice(0, 3) + ',' + parts[1];
        }
        if (parts[1].length > 2) {
            // Se o número de dígitos após a vírgula for maior que 2, ajusta para o máximo
            inputValue = parts[0] + ',' + parts[1].slice(0, 2);
        }
    }

    // Atualizar o valor do campo de entrada
    input.value = inputValue;
}


    
    function fecharErro() {
            document.querySelector('.error').style.display = 'none';
        }
</script>

<style>
      h1 {
        font-size: 45px;
        color: #fff;
    }
    label {
        font-size: 20px;
        color: #fff;
    }
    input[type="text"],
    input[type="number"] {
        width: calc(100% - 20px); /* Ocupa 100% menos 20px para compensar o padding */
        padding: 8px;
        font-size: 15px;
        margin: 5px auto; /* Centraliza horizontalmente com margem superior e inferior */
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #333;
        color: #fff;
        text-align: center; /* Centraliza elementos internamente */
    }
    input[type="submit"] {
        width: calc(100% - 20px); /* Ocupa 100% menos 20px para compensar o padding */
        padding: 10px;
        font-size: 16px;
        font-weight: 500;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin: 20px auto; /* Centraliza horizontalmente com margem superior e inferior */
    }
    input[type="submit"]:hover {
        background-color: #45a049;
    }

    select {
        width: calc(50% - 10px); /* Largura dos selects, 50% do espaço disponível com uma margem de 10px entre eles */
        padding: 8px;
        font-size: 15px;
        margin: 5px auto; /* Centraliza horizontalmente com margem superior e inferior */
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #333;
        color: #fff;
        text-align: center; /* Centraliza elementos internamente */
    }
    
  .modal {
  width: fit-content;
  height: fit-content;
  background: #FFFFFF;
  box-shadow: 0px 187px 75px rgba(0, 0, 0, 0.01), 0px 105px 63px rgba(0, 0, 0, 0.05), 0px 47px 47px rgba(0, 0, 0, 0.09), 0px 12px 26px rgba(0, 0, 0, 0.1), 0px 0px 0px rgba(0, 0, 0, 0.1);
  border-radius: 26px;
  max-width: 450px;
}

.form {
    background-color: #242424; 
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
    max-width: 500px;
    max-height: 650px;
    width: 110%;
    height: auto;
    margin: auto; 
    margin-top: 20px; 
    text-align: center; 
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px;
}

.payment--options {
  width: calc(100% - 40px);
  display: grid;
  grid-template-columns: 33% 34% 33%;
  gap: 20px;
  padding: 10px;
}

.payment--options button {
  height: 55px;
  background: #F2F2F2;
  border-radius: 11px;
  padding: 0;
  border: 0;
  outline: none;
}

.payment--options button svg {
  height: 18px;
}

.payment--options button:last-child svg {
  height: 22px;
}

.separator {
  width: calc(100% - 20px);
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  gap: 10px;
  color: #8B8E98;
  margin: 0 10px;
}

.separator > p {
  word-break: keep-all;
  display: block;
  text-align: center;
  font-weight: 600;
  font-size: 11px;
  margin: auto;
}

.separator .line {
  display: inline-block;
  width: 100%;
  height: 1px;
  border: 0;
  background-color: #e8e8e8;
  margin: auto;
}

.credit-card-info--form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.input_container {
  width: 100%;
  height: fit-content;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.split {
    display: flex; 
    align-items: center; 
    justify-content: center; 
    gap: 10px; 
}


.split-datacvv {
  display: grid;
  grid-template-columns: 2fr 2fr;
}

.split-datacvv input {
width: 50%;
}

.input_label {
  font-size: 10px;
  color: #8B8E98;
  font-weight: 600;
}

.purchase--btn {
    width: calc(100% - 20px); /* Ocupa 100% menos 20px para compensar o padding */
    padding: 10px;
    font-size: 16px;
    font-weight: 500;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 10px auto; /* Centraliza horizontalmente com margem superior e inferior */
}

.purchase--btn:hover {
    background-color: #45a049;
}

/* Reset input number styles */
.input_field::-webkit-outer-spin-button,
.input_field::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.input_field[type=number] {
  -moz-appearance: textfield;
} 
   </style>
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
        <br>
    <?php } ?>
        <br>
        <a href="perfil.php">Voltar</a>
<h1>Carregar Saldo</h1>
<br>
    <div class="modal">
<form class="form" method="post" autocomplete="off">
  <div class="payment--options">

    <button name="paypal" type="button">
        <a href="https://www.paypal.com/pt/home" target="_blank">
    <img src="img/paypal.png" width="105px" height="auto">
        </a>
    </button>

    <button name="paysafecard" type="button">
    <a href="https://www.paysafecard.com/pt/" target="_blank">
    <img src="img/paysafecard.png" width="105px" height="auto">
        </a>
    </button>

    <button name="mbway" type="button">
    <a href="https://www.mbway.pt/" target="_blank">
    <img src="img/mbway.png" width="70px" height="auto">
    </a>
    </button>
  </div>

  <div class="separator">
    <hr class="line">
    <p>ou pague com cartão de crédito</p>
    <hr class="line">
  </div>
  <div class="credit-card-info--form">
    <div class="input_container">
      <label for="password_field" class="input_label">Nome completo do titular do cartão</label>
      <input id="password_field" class="input_field" type="text" name="input-name" required placeholder="Escreva o seu nome completo" oninput="allowOnlyLettersAndSingleSpace(event)">
    </div>
    <div class="input_container">
      <label for="password_field" class="input_label">Número do Cartão</label>
      <input id="card_number_field" class="input_field" type="text" name="card_number" required placeholder="0000 0000 0000 0000" maxlength="19" oninput="formatCardNumber(event)">
    </div>
    
    <div class="split">
    <div class="input_container">
        <label for="expiration_month" class="input_label" style="display:block;">Mês de Validade</label>
        <select id="expiration_month" class="input_field" name="expiration_month" required style="width:150px;" onchange="updateExpirationDate()">
            <option value="" disabled selected style="display:none;">Mês</option>
            <option value="01">Janeiro</option>
            <option value="02">Fevereiro</option>
            <option value="03">Março</option>
            <option value="04">Abril</option>
            <option value="05">Maio</option>
            <option value="06">Junho</option>
            <option value="07">Julho</option>
            <option value="08">Agosto</option>
            <option value="09">Setembro</option>
            <option value="10">Outubro</option>
            <option value="11">Novembro</option>
            <option value="12">Dezembro</option>
        </select>
    </div>

    <div class="input_container">
        <label for="expiration_year" class="input_label" style="display:block;">Ano de Validade</label>
        <select id="expiration_year" class="input_field" name="expiration_year" required style="width:150px;" onchange="updateExpirationDate()">
            <option value="" disabled selected style="display:none;">Ano</option>
            <?php 
                $currentYear = date('Y'); 
                for ($i = $currentYear; $i <= $currentYear + 9; $i++) { 
                    echo '<option value="' . substr($i, -2) . '">' . $i . '</option>'; 
                } 
            ?>
        </select>
    </div>
</div>

<div class="split-datacvv">
    <div class="input_container">
        <label for="data_validade" class="input_label">Data de validade</label>
        <input id="data_validade" class="input_field" type="text" name="expiration_date" style="cursor:default;" readonly placeholder="MM/AA">       
    </div>
    <div class="input_container">
    <label for="password_field" class="input_label">CVV</label>
    <input id="password_field" class="input_field" type="text" name="cvv" required placeholder="CVV" maxlength="3" oninput="allowOnlyNumbersCVV(event)">
  </div>
</div>

    <div class="separator">
    <hr class="line">
        <p id="password_field">Valor a Carregar (€)</p>
    <hr class="line">
    </div>
    <div class="input_container">
        <input id="password_field" type="text" name="saldo_a_carregar" required oninput="allowOnlyNumbers(event)">
        <label for="password_field" class="input_label">mínimo 0,01€ e máximo 100€</label>
    </div>
    <button class="purchase--btn">Depositar</button>
</div>
</form>
</div>
<br>
</center>
</body>
</html>