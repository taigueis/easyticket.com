<?php
if (!isset($_SESSION)) {
    session_name("sessao");
    ob_start();
    session_start();
}

require_once('ligarbd.php');

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

if ($_SESSION['tipo_utilizador'] == 'admin') {    
    require_once('menu.php');
} elseif ($_SESSION['tipo_utilizador'] == 'user') {        
    require_once('menu2.php');
} elseif ($_SESSION['tipo_utilizador'] == 'semregisto') {        
    require_once('menu3.php');
}

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
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css"/>
    <link rel="icon" href="img/logo_EasyTicket.png">
    <title>EasyTicket</title>
    <style>
        @media screen and (min-width: 1920px) {
            table {
    width: 80%;
    margin: auto;
    border-collapse: collapse;
    text-align: center;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
}

th, td {
    padding: 10px;
    border-bottom: 2px solid #4CAF50;
    border-radius: 0px;
    font-size: 22px; /* Tamanho do texto aumentado */
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


        /* Modal Styles */
    .modal {
        display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
            font-family: 'Arial', sans-serif;
    }

    .modal-content {
        background-color: #1e1e1e;
        margin: 15% auto; /* Aumentando a margem superior */
        padding: 30px; /* Aumentando o padding */
        border: 2px solid #888; /* Aumentando a espessura da borda */
        width: 90%; /* Aumentando a largura do conteúdo do modal */
        max-width: 700px;
        text-align: center;
        color: #fff;
        border-radius: 15px; /* Aumentando o raio da borda */
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5); /* Aumentando a intensidade da sombra */
    }

    .modal-header {
        font-size: 28px; /* Aumentando o tamanho da fonte */
        margin-bottom: 30px; /* Aumentando a margem inferior */
        color: #4CAF50;
    }

    .modal-body {
        margin-bottom: 20px; /* Aumentando a margem inferior */
    }

    .modal-body img {
        max-width: 100%;
        height: auto;
    }

    .modal-footer button {
        background-color: #4CAF50;
        color: white;
        margin-top: 30px;
        padding: 13px 23px; /* Aumentando o padding */
        border: none;
        cursor: pointer;
        border-radius: 5px; /* Aumentando o raio da borda */
        font-size: 18px; /* Aumentando o tamanho da fonte */
        transition: background-color 0.3s ease;
    }

    .modal-footer button:hover {
        background-color: #45a049;
    }
        }

        @media screen and (max-width: 1919px) {
            table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            text-align: center;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }

        th, td {
            padding: 10px;
            border-bottom: 2px solid #4CAF50;
            border-radius: 0px;
            font-size: 20px; /* Tamanho do texto aumentado */
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


        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
            font-family: 'Arial', sans-serif;
        }

        .modal-content {
            background-color: #1e1e1e;
            margin: 11% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            text-align: center;
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            font-size: 24px;
            margin-bottom: 30px;
            color: #4CAF50;
        }

        .modal-body {
            margin-bottom: 15px;
        }

        .modal-body img {
            max-width: 100%;
            height: auto;
        }

        .modal-footer button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin-top: 30px; 
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .modal-footer button:hover {
            background-color: #45a049;
        }
        }
    </style>
</head>
<body>
    <center>
        <br>
        <a href="historico_compras.php?id_utilizador=<?php echo $_SESSION['id_utilizador']; ?>">Voltar</a>
        <br><br>
        <h1>Os Seus Bilhetes Disponíveis</h1>
        <br>
        <br><br>
        <table>
            <tr>
                <th>Linha de Autocarro</th>
                <th>Quantidade de Bilhetes</th>
                <th>Clica Para Usar QR Code</th>
            </tr>
            <?php
            while ($quantidade_bilhetes = mysqli_fetch_assoc($resultado_quantidade_bilhetes)) {
                $url = "bilhetes_validar.php?id_bilhete=" . $quantidade_bilhetes['id_bilhete'] . "&id_utilizador=" . $_SESSION['id_utilizador'];
                $qrCodeURL_table = "https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=" . urlencode($url);
                $qrCodeURL_modal = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($url);

                if ($quantidade_bilhetes['total_bilhetes'] > 0) {
                    echo "<tr>";
                    echo "<td>" . $quantidade_bilhetes['linha_autocarro'] . "</td>";
                    echo "<td>" . $quantidade_bilhetes['total_bilhetes'] . "</td>";
                    echo "<td><img style='cursor:pointer;' src='" . $qrCodeURL_table . "' class='qr-code' data-linha='" . $quantidade_bilhetes['linha_autocarro'] . "' data-url='" . $url . "'></td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>
        <br><br><br>
    </center>

<!-- Modal Structure -->
<div id="qrModal" class="modal">
    <div class="modal-content">
        <div class="modal-header" id="modal-header"></div>
        <div class="modal-body">
            <img id="modal-qr-image">
        </div>
        <div class="modal-footer">
            <form id="validation-form" method="POST" action="">
                <button type="submit">Validar</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.qr-code').forEach(function(element) {
        element.addEventListener('click', function() {
            var linha = this.getAttribute('data-linha');
            var url = this.getAttribute('data-url');
            
            document.getElementById('modal-header').innerText = 'Linha de Autocarro: ' + linha;
            document.getElementById('modal-qr-image').src = '<?php echo $qrCodeURL_modal; ?>';
            document.getElementById('validation-form').action = url;

            document.getElementById('qrModal').style.display = 'block';
        });
    });

    window.onclick = function(event) {
        var modal = document.getElementById('qrModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>

</body>
</html>