<?php
session_name("sessao");
session_start();

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit();
}

if ($_SESSION['tipo_utilizador'] == 'admin') {
    require_once('menu.php');
} elseif ($_SESSION['tipo_utilizador'] == 'user') {
    require_once('menu2.php');
}

require_once('ligarbd.php');

$id_utilizador = $_SESSION['id_utilizador'];

if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
    $nome_utilizador = $_GET['nome_utilizador'];
    $email_utilizador = $_GET['email_utilizador'];
    $password_utilizador = $_GET['password_utilizador'];
    $contacto_utilizador = $_GET['contacto_utilizador'];
    $data_nasc = $_GET['data_nasc'];
    $saldo = $_GET['saldo'];
} else {
    $consulta = "SELECT * FROM utilizadores WHERE id_utilizador = '$id_utilizador'";
    $resultado = mysqli_query($basedados, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $dados_utilizador = mysqli_fetch_assoc($resultado);
        $nome_utilizador = $dados_utilizador['nome_utilizador'];
        $email_utilizador = $dados_utilizador['email_utilizador'];
        $password_utilizador = $dados_utilizador['password_utilizador'];
        $contacto_utilizador = formatarContacto($dados_utilizador['contacto_utilizador']);
        $data_nasc = formatarDataNascimento($dados_utilizador['data_nasc']);
        $saldo = $dados_utilizador['saldo'];
    }
}

function formatarContacto($contacto) {
    return substr($contacto, 0, 3) . ' ' . substr($contacto, 3, 3) . ' ' . substr($contacto, 6);
}

function formatarDataNascimento($data) {
    return date('d/m/Y', strtotime($data));
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyTicket</title>
    <link rel="stylesheet" href="style_perfil.css">
</head>
<body>
    <center>
    <br><br>
    <a href="inicio.php">Voltar</a>
    <br><br>

    <div class="dados">
    <?php if ($_SESSION['tipo_utilizador'] === "admin"): ?>
        <h2>Perfil do Administrador</h2>
    <?php else: ?>
        <h2>Perfil do Utilizador</h2>
    <?php endif; ?>
    <br><br>
    <table>
        <tr>
            <td><strong>Nome de Utilizador</strong></td>
            <td style="text-align: center;"><?php echo $nome_utilizador; ?></td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td style="text-align: center;"><?php echo $email_utilizador; ?></td>
        </tr>
        <tr>
            <td><strong>Password</strong></td>
            <td style="text-align: center;"><?php echo $password_utilizador; ?></td>
        </tr>
        <tr>
            <td><strong>Contacto</strong></td>
            <td style="text-align: center;"><?php echo $contacto_utilizador; ?></td>
        </tr>
        <tr>
            <td><strong>Data de Nascimento</strong></td>
            <td style="text-align: center;"><?php echo $data_nasc; ?></td>
        </tr>
        <?php if ($_SESSION['tipo_utilizador'] != 'admin') { ?>
        <tr>
            <td><strong>Saldo</strong></td>
            <td style="text-align: center;"><?php echo number_format($saldo, 2, ',', '.') . '€'; ?></td>
        </tr>
        <?php } ?>
    </table>

    <div class="button-container">
        <form action="perfil_editar.php">
            <input type="submit" value="Editar Perfil" class="btn editar">
        </form>
        <?php if ($_SESSION['tipo_utilizador'] == 'user') { ?>
            <form action="carregar_saldo.php?id_utilizador=<?php echo $id_utilizador; ?>">
                <input type="submit" value="Recarregar Saldo" class="btn recarregar">
            </form>
        <?php } ?>
    </div>

    <?php if ($_SESSION['tipo_utilizador'] == 'user') { ?>
        <a href="historico_compras.php?id_utilizador=<?php echo $_SESSION['id_utilizador']; ?>">Histórico de Compras</a>
    <?php } ?>
</div>
    </center>
</body>
</html>
