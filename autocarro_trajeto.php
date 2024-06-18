<?php
session_name("sessao");
session_start();

if (!isset($_SESSION['tipo_utilizador'])) {
    header("Location: index_login.php");
    exit(); 
}

// --- Chamar menu ---
require_once('menu.php');
// --- Fazer a ligação à Base de Dados ---
require_once('ligarbd.php');
//----- Receber o Id do Autocarro -------------------------
$id_autocarro = $_GET['id'];
// --- Criar consulta e contar o nº de registos ---
$consulta_ida = "SELECT id_viagem, linha_autocarro, origem, destino, SUBSTRING(hora_partida, 1, 5) AS hora_partida, SUBSTRING(hora_chegada, 1, 5) AS hora_chegada 
             FROM viagem WHERE id_autocarro='$id_autocarro' AND sentido='ida' ORDER BY hora_partida";
$resultado_ida = mysqli_query($basedados, $consulta_ida);
$nregistos_ida = mysqli_num_rows($resultado_ida);

$consulta_volta = "SELECT id_viagem, linha_autocarro, origem, destino, SUBSTRING(hora_partida, 1, 5) AS hora_partida, SUBSTRING(hora_chegada, 1, 5) AS hora_chegada 
             FROM viagem WHERE id_autocarro='$id_autocarro' AND sentido='volta' ORDER BY hora_partida";
$resultado_volta = mysqli_query($basedados, $consulta_volta);
$nregistos_volta = mysqli_num_rows($resultado_volta);
?>

<br><br>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/logo_EasyTicket.png">
    <title>EasyTicket</title>
</head>
<center>
<br><br><br>
<a href="autocarros.php">Voltar</a>
<br><br>
<h1>Trajetos Efetuados pelo Autocarro de ID: <?php echo $id_autocarro?> </h1>
    <br><br><br><br>
	
    <?php if ($nregistos_ida > 0 || $nregistos_volta > 0) : ?>
        <?php if ($nregistos_ida > 0) : ?>
            <p style="font-size: 20px; font-weight: bold;">Sentido Ida</p>
            <br><br>
            <table>
                <tr>
                    <th>Linha do Autocarro</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Hora de Partida</th>
                    <th>Hora de Chegada</th>
                </tr>
                <?php
                // --- Mostrar todos os registos da consulta IDA ---
                while ($registo_ida = mysqli_fetch_assoc($resultado_ida)) {
                    echo '<tr>';
                    echo '<td>' . $registo_ida['linha_autocarro'] . '</td>';
                    echo '<td>' . $registo_ida['origem'] . '</td>';
                    echo '<td>' . $registo_ida['destino'] . '</td>';
                    echo '<td>' . $registo_ida['hora_partida'] . '</td>';
                    echo '<td>' . $registo_ida['hora_chegada'] . '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        <?php endif; ?>

        <?php if ($nregistos_volta > 0) : ?>
            <br><br><br><br><br>
            <p style="font-size: 20px; font-weight: bold;">Sentido Volta</p>
            <br><br>
            <table>
                <tr>
                    <th>Linha do Autocarro</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Hora de Partida</th>
                    <th>Hora de Chegada</th>
                </tr>
                <?php
                // --- Mostrar todos os registos da consulta VOLTA ---
                while ($registo_volta = mysqli_fetch_assoc($resultado_volta)) {
                    echo '<tr>';
                    echo '<td>' . $registo_volta['linha_autocarro'] . '</td>';
                    echo '<td>' . $registo_volta['origem'] . '</td>';
                    echo '<td>' . $registo_volta['destino'] . '</td>';
                    echo '<td>' . $registo_volta['hora_partida'] . '</td>';
                    echo '<td>' . $registo_volta['hora_chegada'] . '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        <?php endif; ?>
    <?php else : ?>
        <h2 style="font-size: 30px; font-weight: bold; color: white;">Não há registos disponíveis!</h2>
    <?php endif; ?>
</center>
