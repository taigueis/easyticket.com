<?php
// Iniciar a sessão se não estiver iniciada
if (session_id() === '') {
    session_name("sessao");
    session_start();
}

// Verificar se o utilizador está logado
if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

// Incluir o arquivo do menu adequado com base no tipo de utilizador
if ($_SESSION['tipo_utilizador'] == 'admin') {        
    require_once('menu.php');
} elseif ($_SESSION['tipo_utilizador'] == 'user') {        
    require_once('menu2.php');
} elseif ($_SESSION['tipo_utilizador'] == 'semregisto') {        
    require_once('menu3.php');
}

// Fazer a ligação à Base de Dados
require_once('ligarbd.php');

// Função para inserir um novo bilhete
function inserirBilhete($basedados) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['linha_autocarro']) && !empty($_POST['preco_bilhete'])) {
        $linha_autocarro = $_POST['linha_autocarro'];
        $preco_bilhete = $_POST['preco_bilhete'];

        // Verificar se já existe um registo para a linha de autocarro
        $verificar_duplicatas = "SELECT * FROM bilhetes WHERE linha_autocarro = '$linha_autocarro'";
        $resultado_duplicatas = mysqli_query($basedados, $verificar_duplicatas);

        if (mysqli_num_rows($resultado_duplicatas) > 0) {
            $_SESSION['error_message'] = 'Já existe um registo para a mesma linha de autocarro!';
        } else {
            // Inserir o novo bilhete
            $inserir = "INSERT INTO bilhetes (linha_autocarro, preco_bilhete) 
                        VALUES ('$linha_autocarro', '$preco_bilhete')";
            $sucesso = mysqli_query($basedados, $inserir);

            if ($sucesso) {
                // Redirecionar de volta para bilhetes.php após a inserção bem-sucedida
                echo "<script>window.location.href = 'bilhetes.php';</script>";
                exit(); // Certifique-se de sair do script após o redirecionamento
            } else {
                $_SESSION['error_message'] = 'Erro ao inserir os dados: ' . mysqli_error($basedados);
            }
        }
    }
}

// Função para atualizar um bilhete existente
function atualizarBilhete($basedados, $id_bilhete) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_bilhete'])) {
        $linha_autocarro = $_POST['linha_autocarro'];
        $preco_bilhete = $_POST['preco_bilhete'];

        // Verificar se já existe um registo igual
        $verificar_duplicatas = "SELECT * FROM bilhetes WHERE id_bilhete != '$id_bilhete' AND linha_autocarro = '$linha_autocarro'";
        $resultado_duplicatas = mysqli_query($basedados, $verificar_duplicatas);

        if (mysqli_num_rows($resultado_duplicatas) > 0) {
            $_SESSION['error_message'] = 'Já existe um registo para a mesma linha de autocarro!';
        } else {
            // Atualizar os dados
            $atualizar = "UPDATE bilhetes SET linha_autocarro ='$linha_autocarro', preco_bilhete ='$preco_bilhete' 
                          WHERE id_bilhete='$id_bilhete'";
            $sucesso = mysqli_query($basedados, $atualizar);

            if (!$sucesso) {
                $_SESSION['error_message'] = 'Erro ao atualizar os dados!';
            }
        }
    }
}

// Função para excluir bilhete
function excluirBilhete($basedados, $id_bilhete) {
    if (isset($_GET['id'])) {
        $id_bilhete = $_GET['id'];

        // Verificar se a tabela compra_bilhete existe no banco de dados
        $verificar_tabela = "SHOW TABLES LIKE 'compra_bilhete'";
        $resultado_verificar_tabela = mysqli_query($basedados, $verificar_tabela);

        if (mysqli_num_rows($resultado_verificar_tabela) == 0) {
            $_SESSION['error_message'] = 'A tabela compra_bilhete não existe no banco de dados!';
            return;
        }

        // Obter o id do utilizador associado ao bilhete e o preço total gasto
        $consulta_utilizador = "
            SELECT totalbilhetesporlinha.id_utilizador, SUM(compra_bilhete.preco_compra) AS valor_total
            FROM totalbilhetesporlinha
            INNER JOIN compra_bilhete ON totalbilhetesporlinha.id_bilhete = compra_bilhete.id_bilhete
            WHERE totalbilhetesporlinha.id_bilhete='$id_bilhete'
            GROUP BY totalbilhetesporlinha.id_utilizador";
        $resultado_utilizador = mysqli_query($basedados, $consulta_utilizador);
        
        if (!$resultado_utilizador) {
            $_SESSION['error_message'] = 'Erro ao consultar os dados do utilizador!';
            return;
        }
        
        $dados_utilizador = mysqli_fetch_assoc($resultado_utilizador);
        $id_utilizador = $dados_utilizador['id_utilizador'];
        $valor_total = $dados_utilizador['valor_total'];

        // Adicionar o valor ao saldo do utilizador
        $atualizar_saldo = "
            UPDATE utilizadores
            SET saldo = saldo + '$valor_total'
            WHERE id_utilizador='$id_utilizador'";
        $sucesso_atualizar_saldo = mysqli_query($basedados, $atualizar_saldo);

        if (!$sucesso_atualizar_saldo) {
            $_SESSION['error_message'] = 'Erro ao atualizar o saldo do utilizador!';
            return;
        }

        // Excluir os registos dependentes na tabela totalbilhetesporlinha
        $excluir_totalbilhetesporlinha = "DELETE FROM totalbilhetesporlinha WHERE id_bilhete='$id_bilhete'";
        $sucesso_totalbilhetesporlinha = mysqli_query($basedados, $excluir_totalbilhetesporlinha);
        
        if (!$sucesso_totalbilhetesporlinha) {
            $_SESSION['error_message'] = 'Erro ao excluir os dados dependentes em totalbilhetesporlinha!';
            return;
        }
        
        // Em seguida, excluir os registos dependentes na tabela compra_bilhete
        $excluir_compra_bilhete = "DELETE FROM compra_bilhete WHERE id_bilhete='$id_bilhete'";
        $sucesso_compra_bilhete = mysqli_query($basedados, $excluir_compra_bilhete);
        
        if (!$sucesso_compra_bilhete) {
            $_SESSION['error_message'] = 'Erro ao excluir os dados dependentes em compra_bilhete!';
            return;
        }

        // Por último, excluir o registo na tabela bilhetes
        $excluir = "DELETE FROM bilhetes WHERE id_bilhete='$id_bilhete'";
        $sucesso = mysqli_query($basedados, $excluir);

        if (!$sucesso) {
            $_SESSION['error_message'] = 'Erro ao excluir os dados!';
        }
    }
}


// Chamar as funções para inserir, atualizar e excluir dados
$id_bilhete = isset($_POST['id_bilhete']) ? $_POST['id_bilhete'] : null; // Get the value of id_bilhete if it exists in POST data, otherwise set to null

// Verificar se todas as linhas de autocarro têm um bilhete atribuído
$consulta_linhas_autocarro = "SELECT DISTINCT linha_autocarro FROM viagem";
$resultado_linhas_autocarro = mysqli_query($basedados, $consulta_linhas_autocarro);
$linhas_autocarro = mysqli_fetch_all($resultado_linhas_autocarro, MYSQLI_ASSOC);

$consulta_bilhetes = "SELECT DISTINCT linha_autocarro FROM bilhetes";
$resultado_bilhetes = mysqli_query($basedados, $consulta_bilhetes);
$linhas_bilhetes = mysqli_fetch_all($resultado_bilhetes, MYSQLI_ASSOC);

$linhas_autocarro_count = count($linhas_autocarro);
$linhas_bilhetes_count = count($linhas_bilhetes);

if ($linhas_autocarro_count === $linhas_bilhetes_count) {
} else {
    inserirBilhete($basedados, $id_bilhete);
}

atualizarBilhete($basedados, $id_bilhete);
excluirBilhete($basedados, $id_bilhete);

// Criar consulta para buscar os bilhetes
$consulta = "SELECT id_bilhete, linha_autocarro, preco_bilhete FROM bilhetes ORDER BY linha_autocarro";
$resultado = mysqli_query($basedados, $consulta);
$nregistos = mysqli_num_rows($resultado);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <title>EasyTicket</title>
<script>
    
       function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Função para rolar de volta para o topo da página
        function backToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Mostra o botão de voltar ao topo quando o utilizador rolar para baixo
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
        document.querySelector('.warning').style.display = 'none';
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
        document.location.href = "bilhete_eliminar.php?id=" + id;
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
        document.location.href = "bilhete_editar.php?id=" + id;
    };
    buttonWrapper.appendChild(deleteButton);

    box.appendChild(buttonWrapper);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
}

function confirmainserir() {
    <?php if ($linhas_autocarro_count === $linhas_bilhetes_count): ?>
        var errorText = document.querySelector('.error__text');
        errorText.innerHTML = "<span>Todas as linhas de autocarro já possuem um bilhete atribuído!</span>";
        errorText.style.display = 'block';
        document.querySelector('.error').style.display = 'block';
        backToTop();
    <?php else: ?>
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
            document.location.href = "bilhete_novo.php";
        };
        buttonWrapper.appendChild(insertButton);

        box.appendChild(buttonWrapper);
        overlay.appendChild(box);
        document.body.appendChild(overlay);
    <?php endif; ?>
}

        function comprarBilhete() {
    <?php if ($_SESSION['tipo_utilizador'] === 'semregisto'): ?>
        // Se o utilizador for "semregisto", exibir mensagem de erro
        var errorText = document.querySelector('.warning__text');
        errorText.innerHTML = "<span>Faça login para comprar bilhetes!</span>";
        errorText.style.display = 'block';
        document.querySelector('.warning').style.display = 'block';
        backToTop();
    <?php else: ?>
        // Caso contrário, redirecionar para a página de compra de bilhetes
        window.location.href = "compra_bilhetes.php";
    <?php endif; ?>
}

</script>
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

<h1>Bilhetes</h1>

    <br><br>
    <table>
        <tr>
            <?php if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin"): ?>
                <th>ID do Bilhete</th>
            <?php endif; ?>
            <th>Linha de Autocarro</th>
            <th>Preço do Bilhete</th>
            <?php if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin"): ?>
                <th>Editar</th>
                <th>Eliminar</th>
            <?php endif; ?>
        </tr>
        <?php
        // Mostrar todos os registos da consulta
        for ($i = 0; $i < $nregistos; $i++) {
            $registo = mysqli_fetch_array($resultado);
            echo '<tr>';
            if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin") {
                echo '<td>' . $registo['id_bilhete'] . '</td>';
            }
            echo '<td>' . $registo['linha_autocarro'] . '</td>';
            // Formatar o preço do bilhete
            echo '<td>' . number_format($registo['preco_bilhete'], 2, ',', '.') . '€</td>';
            if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === "admin") {
                echo '<td align="center"> <a onClick="confirmaatualizar(' . $registo['id_bilhete'] . ');" style="cursor: pointer;">';
                echo '<img src="img/atualizar.png"  class="img-icon" > </a></td>';

                echo '<td align="center"> <a onClick="confirmaremover(' . $registo['id_bilhete'] . ');" style="cursor: pointer;">';
                echo '<img src="img/eliminar.png"  class="img-icon"> </a></td>';
            }
            echo '</tr>';
        }
        ?>
    </table>
    <br>
    <br>
    <?php if ($_SESSION['tipo_utilizador'] === 'semregisto' || $_SESSION['tipo_utilizador'] === 'user'): ?>
        <!-- Mostrar o botão apenas para utilizadores "semregisto" ou "user" -->
        <input type="button" class="botaocompra" value="Comprar Bilhete" onclick="comprarBilhete()">
    <?php endif; ?>
    <br>
    <br>
    <?php if ($_SESSION['tipo_utilizador'] === 'admin'): ?>
<h2>Inserir Novo Bilhete</h2>
<br>
<a onclick=confirmainserir()>
    <img src="img/inserir.png" style="cursor: pointer;" >
</a>
<br><br>
<?php endif; ?>
</center>
</body>
</html>