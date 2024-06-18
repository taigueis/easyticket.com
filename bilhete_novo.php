<?php
require_once('ligarbd.php');
session_name("sessao");
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit();
}

// Incluir o arquivo do menu
require_once('menu.php');

// Função para limitar o número de casas decimais
function limitarCasasDecimais($value) {
    // Limitar a dois caracteres depois do ponto decimal
    return number_format((float)$value, 2, '.', '');
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $linha_autocarro = $_POST['linha_autocarro'];
    $preco_bilhete = $_POST['preco_bilhete'];

    // Verificar se já existe um registro para a linha de autocarro
    $verificar_duplicatas = "SELECT * FROM bilhetes WHERE linha_autocarro = '$linha_autocarro'";
    $resultado_duplicatas = mysqli_query($basedados, $verificar_duplicatas);

    if (mysqli_num_rows($resultado_duplicatas) > 0) {
        $_SESSION['error_message'] = 'Já existe um registo para a mesma linha de autocarro!';
    } else {
        // Obter o próximo ID disponível
        $consulta_ultimo_id = "SELECT MAX(id_bilhete) AS ultimo_id FROM bilhetes";
        $resultado_ultimo_id = mysqli_query($basedados, $consulta_ultimo_id);
        $ultimo_id = mysqli_fetch_assoc($resultado_ultimo_id)['ultimo_id'];
        $id_bilhete = $ultimo_id + 1;

        // Inserir os dados na base de dados
        $preco_bilhete = limitarCasasDecimais($preco_bilhete); // Limitar o número de casas decimais
        $inserir = "INSERT INTO bilhetes (id_bilhete, linha_autocarro, preco_bilhete) 
                    VALUES ('$id_bilhete', '$linha_autocarro', '$preco_bilhete')";
        $sucesso = mysqli_query($basedados, $inserir);

        if ($sucesso) {
            $_SESSION['success_message'] = 'Bilhete inserido com sucesso!';
            echo "<script>window.location.href = 'bilhetes.php';</script>";
            exit(); // Certifique-se de sair do script após o redirecionamento
        } else {
            $_SESSION['error_message'] = 'Erro ao inserir os dados: ' . mysqli_error($basedados);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EasyTicket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_editar.css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <script>
        function fecharErro() {
            document.querySelector('.error').style.display = 'none';
        }

        // Função para limitar o número de casas decimais
        function limitarCasasDecimais(event) {
            var input = event.target;
            var value = input.value;

            // Remover caracteres não numéricos, exceto o ponto decimal
            value = value.replace(/[^\d.]/g, '');

            // Limitar a dois caracteres depois do ponto decimal
            var firstPart = value.split('.')[0].slice(0, 1);
            var secondPart = value.split('.')[1] ? value.split('.')[1].slice(0, 2) : '';

            // Atualizar o valor do input
            input.value = firstPart + '.' + secondPart;
        }
    </script>
</head>
<body>
<center>
    <br><br>
    <div class="error" style="display: <?php echo isset($_SESSION['error_message']) ? 'block' : 'none'; ?>;">
        <div class="error__content">
            <div class="error__icon">
                <svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m13 13h-2v-6h2zm0 4h-2v-2h2zm-1-15c-1.3132 0-2.61358.25866-3.82683.7612-1.21326.50255-2.31565 1.23915-3.24424 2.16773-1.87536 1.87537-2.92893 4.41891-2.92893 7.07107 0 2.6522 1.05357 5.1957 2.92893 7.0711.92859.9286 2.03098 1.6651 3.24424 2.1677 1.21325.5025 2.51363.7612 3.82683.7612 2.6522 0 5.1957-1.0536 7.0711-2.9289 1.8753-1.8754 2.9289-4.4189 2.9289-7.0711 0-1.3132-.2587-2.61358-.7612-3.82683-.5026-1.21326-1.2391-2.31565-2.1677-3.24424-.9286-.92858-2.031-1.66518-3.2443-2.16773-1.2132-.50254-2.5136-.7612-3.8268-.7612z" fill="#393a37"></path></svg>
            </div>
            <div class="error__text">
                <?php if(isset($_SESSION['error_message'])) { echo $_SESSION['error_message']; unset($_SESSION['error_message']); } ?>
            </div>
            <div class="error__close" onclick="fecharErro()">
                <svg height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="m15.8333 5.34166-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175-1.175-4.6583-4.6583z" fill="#393a37"></path></svg>
            </div>
        </div>
    </div>
    <br><br><br>
    <a href="javascript:history.back()">Voltar</a>
    <br><br>
    <h1>Adicionar Bilhete</h1>
    <br><br><br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" autocomplete="off" id="editarForm">
        <table>
            <tr>
                <td>Linha do Autocarro</td>
                <td>
                    <!-- Selecione a linha de autocarro das existentes -->
                    <select name="linha_autocarro">
                        <?php
                        // Consulta para obter todas as linhas de autocarro
                        $consulta_linhas = "SELECT DISTINCT linha_autocarro FROM viagem";
                        $resultado_linhas = mysqli_query($basedados, $consulta_linhas);

                        // Exibir opções de seleção
                        while ($linha = mysqli_fetch_array($resultado_linhas)) {
                            echo "<option value='" . $linha['linha_autocarro'] . "'>" . $linha['linha_autocarro'] . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Preço do Bilhete</td>
                <td><input type="text" name="preco_bilhete" id="preco_bilhete" class="centered-input" oninput="limitarCasasDecimais(event)" onfocus="changeBackgroundColor(this, true)" onblur="changeBackgroundColor(this, false)" style="outline: none; box-shadow: none;"></td>
            </tr>
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
            <tr>
                <td><input type="submit" value="Inserir Dados"></td>
                <td><input type="reset" value="Limpar Dados"></td>
            </tr>
        </table>
    </form>
</center>
</body>
</html>