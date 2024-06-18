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

// Verificar se o parâmetro "todas" está definido na URL
$todas_compras = isset($_GET['todas']) && $_GET['todas'] == '1';

// Consulta SQL para as compras
if ($todas_compras) {
    $consulta_compras = "SELECT compra_bilhete.data_compra, compra_bilhete.preco_compra, compra_bilhete.quantidade_bilhetes, compra_bilhete.status_pagamento, bilhetes.linha_autocarro 
                         FROM compra_bilhete 
                         INNER JOIN bilhetes ON compra_bilhete.id_bilhete = bilhetes.id_bilhete 
                         WHERE compra_bilhete.id_utilizador = ? 
                         ORDER BY compra_bilhete.data_compra DESC";
} else {
    $consulta_compras = "SELECT compra_bilhete.data_compra, compra_bilhete.preco_compra, compra_bilhete.quantidade_bilhetes, compra_bilhete.status_pagamento, bilhetes.linha_autocarro 
                         FROM compra_bilhete 
                         INNER JOIN bilhetes ON compra_bilhete.id_bilhete = bilhetes.id_bilhete 
                         WHERE compra_bilhete.id_utilizador = ? 
                         AND compra_bilhete.data_compra >= DATE_SUB(CURDATE(), INTERVAL 15 DAY)
                         ORDER BY compra_bilhete.data_compra DESC";
}

$stmt = mysqli_prepare($basedados, $consulta_compras);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_utilizador']);
mysqli_stmt_execute($stmt);
$resultado_compras = mysqli_stmt_get_result($stmt);

// Consultar a quantidade de bilhetes por linha para o utilizador
$consulta_quantidade_bilhetes = "
    SELECT bilhetes.linha_autocarro, totalbilhetesporlinha.id_bilhete, totalbilhetesporlinha.total_bilhetes 
    FROM totalbilhetesporlinha 
    INNER JOIN bilhetes ON totalbilhetesporlinha.id_bilhete = bilhetes.id_bilhete
    WHERE totalbilhetesporlinha.id_utilizador = ? 
";
        
$stmt_quantidade_bilhetes = mysqli_prepare($basedados, $consulta_quantidade_bilhetes);
mysqli_stmt_bind_param($stmt_quantidade_bilhetes, "i", $_SESSION['id_utilizador']);
mysqli_stmt_execute($stmt_quantidade_bilhetes);
$resultado_quantidade_bilhetes = mysqli_stmt_get_result($stmt_quantidade_bilhetes);

// Consulta SQL para as transações de depósito do utilizador
$consulta_transacoes = "
    SELECT * FROM depositos_utilizador
    WHERE id_utilizador = ?
    ORDER BY data_deposito DESC
";

$stmt_transacoes = mysqli_prepare($basedados, $consulta_transacoes);
mysqli_stmt_bind_param($stmt_transacoes, "i", $_SESSION['id_utilizador']);
mysqli_stmt_execute($stmt_transacoes);
$resultado_transacoes = mysqli_stmt_get_result($stmt_transacoes);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Compras</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <style>
 /* Estilos para telas com largura mínima de 1920 pixels */
@media screen and (min-width: 1920px) {
    table {
        width: 80%;
        margin: auto;
        border-collapse: collapse;
        text-align: center;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.1); /* Sombra branca */
    }

    th, td {
        padding: 10px;
        border-bottom: 2px solid #4CAF50;
        border-radius: 0px;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }

    td {
        background-color: #333333;
        color: white;
    }

    tr:hover td {
        background-color: #292929; 
        color: white; 
    }

    .historico-compras {
        width: 18%;
        margin: 20px auto;
        padding: 5px;
        background-color: #2C2C2C; 
        box-shadow: 0 0 40px rgba(250, 250, 250, 0.1); 
        border-radius: 10px;
        text-align: center;
    }

    .titulo-bilhetes-disponiveis {
        margin-bottom: 20px;
    }

    .historico-compras a {
        font-size: 26px;
        color: #4CAF50;
        text-decoration: none;
        font-weight: bold;
    }

    .historico-compras a:hover {
        color: #45a049;
    }


    .lista-linhas {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .linha {
        margin: 15px 0;
        font-size: 20px;
        color: #E0E0E0;
    }

    .linha::before {
        content: "\2022";
        color: #4CAF50;
        font-weight: bold;
        display: inline-block;
        width: 1em;
        margin-left: -1em;
    }

    a.link-compras {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 30px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s, color 0.3s;
    }

    a.link-compras:hover {
        background-color: #45a049;
        color: #fff;
    }

    a.link-compras:active {
        background-color: #3e8e41;
    }
}

/* Estilos para telas com largura entre 501 pixels e 1919 pixels */
@media screen and (max-width: 1919px) and (min-width: 501px) {
    table {
        width: 70%;
        margin: auto;
        border-collapse: collapse;
        text-align: center;
        box-shadow: 0 0 25px rgba(255, 255, 255, 0.1); /* Sombra branca */
    }

    th, td {
        padding: 8px;
        border-bottom: 2px solid #4CAF50;
        border-radius: 0px;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }

    td {
        background-color: #333333;
        color: white;
    }

    tr:hover td {
        background-color: #292929; /* Cor mais escura para todos os td quando o tr é hover */
        color: white; /* Altera a cor do texto para branco para melhor legibilidade */
    }

    body {
        background-color: #121212;
        color: #E0E0E0;
    }

    .historico-compras {
        width: 20%;
        margin: 20px auto;
        padding: 2px;
        background-color: #2C2C2C; 
        box-shadow: 0 0 20px rgba(250, 250, 250, 0.1); 
        border-radius: 10px;
        text-align: center;
    }

    .titulo-bilhetes-disponiveis {
        margin-bottom: 20px;
    }

    .historico-compras a {
        font-size: 21px;
        color: #4CAF50;
        text-decoration: none;
        font-weight: bold;
    }

    .historico-compras a:hover {
        color: #45a049;
    }

    .lista-linhas {
        list-style-type: none;
        padding: 1;
        margin: -10;
    }

    .linha {
        margin: 15px 0;
        font-size: 18px;
        color: #E0E0E0;
    }

    .linha::before {
        content: "\2022";
        color: #4CAF50;
        font-weight: bold;
        display: inline-block;
        width: 1em;
        margin-left: -1em;
    }

    a.link-compras {
        display: inline-block;
        padding: 8px 16px;
        margin-top: 25px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s, color 0.3s;
    }

    a.link-compras:hover {
        background-color: #45a049;
        color: #fff;
    }

    a.link-compras:active {
        background-color: #3e8e41;
    }
}


</style>
</head>
<body>
<center>
        <br>
        <a href="compra_bilhetes.php">Voltar</a>
        <br>
        <h1>Histórico de Compras</h1>
        <br>
        <?php
            // Verificar se existem compras
            if (mysqli_num_rows($resultado_compras) > 0) {
                if (!$todas_compras) {
                    echo "<br><h3>Compras Efetuadas nos Últimos 15 Dias</h3>";
                } else {
                    echo "<br><h3>Todas as Compras Efetuadas</h3>";
                }
                echo "<br><br>";
                echo "<table>";
                echo "<tr>";
                echo "<th>Linha de Autocarro</th>";
                echo "<th>Quantidade de Bilhetes</th>";
                echo "<th>Preço Total</th>";
                echo "<th>Data da Compra</th>";
                echo "</tr>";

                // Mostrar as compras do utilizador
                while ($compra = mysqli_fetch_assoc($resultado_compras)) {
                    echo "<tr>";
                    echo "<td>" . $compra['linha_autocarro'] . "</td>";
                    echo "<td>" . $compra['quantidade_bilhetes'] . "</td>";
                    echo "<td>" . number_format($compra['preco_compra'], 2, ',', '.') . "€</td>";
                    echo "<td>" . date("H:i | d/m/Y", strtotime($compra['data_compra'])) . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<br><br><br>";
                echo "<h2>Nenhuma compra efetuada";
                if (!$todas_compras) {
                    echo " nos últimos 15 dias!";
                } else {
                    echo "!";
                }
                echo "</h2>";
                echo "<br><br>";
            }

            if ($todas_compras) {
                echo "<br><a href='historico_compras.php' class='link-compras'>Ver Apenas as Compras Efetuadas nos Últimos 15 Dias</a>";
            } else {
                echo "<br><a href='historico_compras.php?todas=1' class='link-compras'>Ver Todas as Compras Efetuadas</a>";
            }
            ?>


        <br><br><br>
        <div class="historico-compras">
            <?php
            $bilhetes_disponiveis = false;
            if (mysqli_num_rows($resultado_quantidade_bilhetes) > 0) {
                while ($quantidade_bilhetes = mysqli_fetch_assoc($resultado_quantidade_bilhetes)) {
                    if ($quantidade_bilhetes['total_bilhetes'] > 0) {
                        $bilhetes_disponiveis = true;
                        break;
                    }
                }
                mysqli_data_seek($resultado_quantidade_bilhetes, 0);
            }

            if ($bilhetes_disponiveis) {
                echo "<h1 class='titulo-bilhetes-disponiveis'><a href='bilhetes_utilizador.php?id_utilizador={$_SESSION['id_utilizador']}'>Bilhetes Disponíveis</a></h1>";
            } else {
                echo "<h1 class='titulo-bilhetes-disponiveis'><a href='' onclick='return false;'>Bilhetes Disponíveis</a></h1>";
            }
            ?>
            <ul class="lista-linhas">
                <?php
                if (mysqli_num_rows($resultado_quantidade_bilhetes) > 0) {
                    while ($quantidade_bilhetes = mysqli_fetch_assoc($resultado_quantidade_bilhetes)) {
                        if ($quantidade_bilhetes['total_bilhetes'] == 0 || empty($quantidade_bilhetes['total_bilhetes'])) {

                        } else {
                            echo "<li class='linha'>" . $quantidade_bilhetes['linha_autocarro'] . ": " . $quantidade_bilhetes['total_bilhetes'] . " bilhetes</li>";
                        }
                    }
                } else {
                    echo "<p>Sem Bilhetes Disponíveis</p>";
                }
                ?>
            </ul>
            <br>
        </div>

        <br><br><br>
        <h1 id="historico-transacoes">Histórico de Transações</h1>
        <div class="historico-transacoes">
        <?php
            // Definir $todos_depositos com base no parâmetro GET
            $todos_depositos = isset($_GET['todos_depositos']) && $_GET['todos_depositos'] == '1';

            // Mostrar as transações de depósito do utilizador
            if (mysqli_num_rows($resultado_transacoes) > 0) {
                if (!$todos_depositos) {
                    echo "<br><br><br><h3>Depósitos Efetuados nos Últimos 15 Dias</h3><br><br>";
                } else {
                    echo "<br><br><br><h3>Todos os Depósitos Efetuados</h3><br><br>";
                }

                echo "<table>";
                echo "<tr>";
                echo "<th>Nome do Titular do Cartão</th>";
                echo "<th>Número do Cartão</th>";
                echo "<th>Valor Depositado</th>";
                echo "<th>Data do Depósito</th>";
                echo "</tr>";

                while ($transacao = mysqli_fetch_assoc($resultado_transacoes)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($transacao['nome_titular_cartao']) . "</td>";
                    echo "<td>" . htmlspecialchars($transacao['numero_cartao']) . "</td>";
                    echo "<td>" . number_format($transacao['valor_depositado'], 2, ',', '.') . "€</td>";
                    echo "<td>" . date("H:i | d/m/Y", strtotime($transacao['data_deposito'])) . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<br><br><br>";
                echo "<h2>Nenhuma transação de depósito efetuada";
                if (!$todos_depositos) {
                    echo " nos últimos 15 dias!";
                } else {
                    echo "!";
                }
                echo "</h2>";
                echo "<br><br>";
            }

            // Exibir links para filtrar entre depósitos dos últimos 15 dias ou todos os depósitos
            if ($todos_depositos) {
                echo "<br><a href='historico_compras.php#historico-transacoes' class='link-compras'>Ver Apenas os Depósitos Efetuados nos Últimos 15 Dias</a>";
            } else {
                echo "<br><a href='historico_compras.php?todos_depositos=1#historico-transacoes' class='link-compras'>Ver Todos os Depósitos Efetuados</a>";
            }
            ?>

        </div>
        <br><br>
    </center>
</body>
</html>