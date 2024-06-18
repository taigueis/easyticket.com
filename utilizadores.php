<?php
session_name("sessao");
session_start();

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

require_once('menu.php');
require_once('ligarbd.php');

// Inicializa a variavel de sessao para armazenar a pesquisa
if (!isset($_SESSION['search'])) {
    $_SESSION['search'] = '';
}

$filter = isset($_POST['filter']) ? $_POST['filter'] : 'all';

// Atualiza a variavel de sessao com o valor da pesquisa
if (isset($_POST['search'])) {
    $_SESSION['search'] = trim($_POST['search']);
}

$search = $_SESSION['search'];
$tipo_utilizador = $_SESSION['tipo_utilizador'];

// Montar a query SQL de acordo com a pesquisa e o filtro selecionados
if (!empty($search) && strlen(trim($search)) > 0) {
    $search_term = mysqli_real_escape_string($basedados, $search);
    switch ($filter) {
        case 'user':
            $query = "SELECT * FROM utilizadores WHERE tipo_utilizador = 'user' AND nome_utilizador LIKE '$search_term%' ORDER BY tipo_utilizador";
            break;
        case 'admin':
            $query = "SELECT * FROM utilizadores WHERE tipo_utilizador = 'admin' AND nome_utilizador LIKE '$search_term%' ORDER BY tipo_utilizador";
            break;
        case 'inativos':
            $query = "SELECT * FROM utilizadores WHERE ativo = '0' AND tipo_utilizador = 'user' AND nome_utilizador LIKE '$search_term%' ORDER BY tipo_utilizador";
            break;
        case 'ativos':
            $query = "SELECT * FROM utilizadores WHERE ativo = '1' AND tipo_utilizador = 'user' AND nome_utilizador LIKE '$search_term%' ORDER BY tipo_utilizador";
            break;
        default:
            $query = "SELECT * FROM utilizadores WHERE nome_utilizador LIKE '$search_term%' ORDER BY tipo_utilizador";
    }
} else {
    switch ($filter) {
        case 'user':
            $query = "SELECT * FROM utilizadores WHERE tipo_utilizador = 'user' ORDER BY tipo_utilizador";
            break;
        case 'admin':
            $query = "SELECT * FROM utilizadores WHERE tipo_utilizador = 'admin' ORDER BY tipo_utilizador";
            break;
        case 'inativos':
            $query = "SELECT * FROM utilizadores WHERE ativo = '0' AND tipo_utilizador = 'user' ORDER BY tipo_utilizador";
            break;
        case 'ativos':
            $query = "SELECT * FROM utilizadores WHERE ativo = '1' AND tipo_utilizador = 'user' ORDER BY tipo_utilizador";
            break;
        default:
            $query = "SELECT * FROM utilizadores ORDER BY tipo_utilizador";
    }
}

$resultado = mysqli_query($basedados, $query);
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
    function confirmativar(id, nome) {
        var overlay = document.createElement("div");
        overlay.className = "confirm-overlay";

        var box = document.createElement("div");
        box.className = "confirm-box";

        var question = document.createElement("p");
        question.className = "card-heading";
        question.innerText = "Deseja realmente ativar a conta de " + nome + "?";
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
        deleteButton.innerText = "Ativar";
        deleteButton.onclick = function() {
            document.location.href = "utilizadores_ativar.php?id=" + id;
        };
        buttonWrapper.appendChild(deleteButton);

        box.appendChild(buttonWrapper);

        overlay.appendChild(box);
        document.body.appendChild(overlay);
    }
    
    function confirmadesativar(id, nome) {
        var overlay = document.createElement("div");
        overlay.className = "confirm-overlay";

        var box = document.createElement("div");
        box.className = "confirm-box";

        var question = document.createElement("p");
        question.className = "card-heading";
        question.innerText = "Deseja realmente desativar a conta de " + nome + "?";
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
        deleteButton.className = "card-button primary-eliminar"; 
        deleteButton.innerText = "Desativar";
        deleteButton.onclick = function() {
            document.location.href = "utilizadores_desativar.php?id=" + id;
        };
        buttonWrapper.appendChild(deleteButton);

        box.appendChild(buttonWrapper);

        overlay.appendChild(box);
        document.body.appendChild(overlay);
    }

    function confirmaatualizar(id, nome) {
        var overlay = document.createElement("div");
        overlay.className = "confirm-overlay";

        var box = document.createElement("div");
        box.className = "confirm-box";

        var question = document.createElement("p");
        question.className = "card-heading";
        question.innerText = "Deseja realmente editar os dados de " + nome + "?";
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
            document.location.href = "utilizadores_editar.php?id=" + id;
        };
        buttonWrapper.appendChild(deleteButton);

        box.appendChild(buttonWrapper);

        overlay.appendChild(box);
        document.body.appendChild(overlay);
    }
</script>

<center>
<br><br>
<h1>Utilizadores</h1>
    <br><br><br>
    
<div class="search-filter-container <?php echo !empty($search) ? 'search-active' : ''; ?>">
    <form method="post" action="" autocomplete="off" style="display: flex; align-items: center;">
        <input type="text" name="search" id="search" class="search-bar" placeholder="Pesquisar por nome..." value="<?php echo $search; ?>" onkeydown="if (event.keyCode == 13) this.form.submit()" style="margin-right: 5px;">
        <button class="button">
            <span class="span">🔎</span>
        </button>
        <div id="show-all-button">
        <?php if (!empty($search) || $search === '0'): ?>
            <button type="button" onclick="mostrarTodos()">Mostrar Todos</button>
        <?php endif; ?>
        </div>
    </form>
    <form method="post" action="">
        <label for="filter">Filtrar Utilizadores :</label>
        <select name="filter" id="filter" class="custom-select" onchange="this.form.submit()">
            <option value="all" <?php echo ($filter == 'all') ? 'selected' : ''; ?>>Todos</option>
            <option value="admin" <?php echo ($filter == 'admin') ? 'selected' : ''; ?>>Apenas Administrador</option>
            <option value="user" <?php echo ($filter == 'user') ? 'selected' : ''; ?>>Apenas Utilizadores</option>
            <option value="ativos" <?php echo ($filter == 'ativos') ? 'selected' : ''; ?>>Utilizadores Ativos</option>
            <option value="inativos" <?php echo ($filter == 'inativos') ? 'selected' : ''; ?>>Utilizadores Inativos</option>
        </select>
    </form>
</div>

<br><br>

    <?php if ($nregistos > 0 && $search !== '0'): ?>
    <table>
        <tr>    
            <th> Nome </th> 
            <th> Tipo </th>
            <th> Email </th> 
            <th> Password </th>
            <th> Contacto </th>
            <th> Data de Nascimento </th>
            <th> Editar </th>
            <th> Ação </th>
        </tr>

        <?php
            // --- Mostrar todos os registos da consulta ---
            while ($registo = mysqli_fetch_array($resultado)) {
                // Verificar se o ID do utilizador é diferente de "0"
                if ($registo['id_utilizador'] != 0) {
                    echo '<tr>';
                    echo '<td>'.$registo['nome_utilizador'].'</td>';
                    echo '<td>'.$registo['tipo_utilizador'].'</td>';
                    echo '<td>'.$registo['email_utilizador'].'</td>';
                    echo '<td>'.$registo['password_utilizador'].'</td>';      
                    $contacto_formatado = chunk_split($registo['contacto_utilizador'], 3, ' ');
                    echo '<td>'.$contacto_formatado.'</td>';
                    echo '<td>'.date("d / m / Y", strtotime($registo['data_nasc'])).'</td>';				
                    
            
                    if ($registo['tipo_utilizador'] !== 'admin') {
                        echo '<td align="center"> <a onClick="confirmaatualizar(' . $registo['id_utilizador'] . ', \'' . $registo['nome_utilizador'] . '\');" style="cursor: pointer;">
                                <img class="img-icon" src="img/atualizar.png" alt="Atualizar" > </a></td>';
                        if ($registo['ativo']) {
                            echo '<td align="center"> <a onClick="confirmadesativar(' . $registo['id_utilizador'] . ', \'' . $registo['nome_utilizador'] . '\');" style="cursor: pointer;">
                                <img class="img-icon" src="img/green-circle.png" alt="Círculo Verde" > </a></td>';
                        } else {
                            echo '<td align="center"> <a onClick="confirmativar(' . $registo['id_utilizador'] . ', \'' . $registo['nome_utilizador'] . '\');" style="cursor: pointer;">
                                <img class="img-icon" src="img/red-circle.png" alt="Círculo Vermelho" > </a></td>';
                        }
                    } else {
                        echo '<td align="center"> <a(' . $registo['id_utilizador'] . ');">
                                <img class="img-icon admin-icon grayscale" src="img/atualizar.png" alt="Atualizar" > </a></td>';
                        echo '<td align="center"> <a (' . $registo['id_utilizador'] . ');">
                                <img class="img-icon admin-icon grayscale" src="img/green-circle.png" alt="Círculo verde" > </a></td>';
                    }
            }
        }
        ?>
    </table>
    <?php else: ?>
    <br><br>
    <center><p style="font-size: 25px; font-weight: 600;">Nenhum registo existente!</p></center>
    <?php endif; ?>
<br><br>
<a style="cursor:pointer;" onclick="backToTop()" id="back-to-top"> <img src="img/seta.png"></a>
<br><br>
</center>
<script>
    function backToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function mostrarTodos() {
        document.getElementById('filter').value = 'all';
        document.getElementById('search').value = '';
        document.forms[0].submit();
    }
</script>
</body>
</html>
