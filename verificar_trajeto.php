<?php
session_start();
include 'ligarbd.php'; 

$id_autocarro = $_POST['id_autocarro'];
$sentido = $_POST['sentido'];
$hora_partida = $_POST['hora_partida'];
$hora_chegada = $_POST['hora_chegada'];
$linha_autocarro = $_POST['linha_autocarro'];

$verificar_existencia = "SELECT * FROM viagem WHERE id_autocarro='$id_autocarro' AND sentido='$sentido' AND hora_partida='$hora_partida' AND hora_chegada='$hora_chegada' AND linha_autocarro='$linha_autocarro'";
$resultado_existencia = mysqli_query($basedados, $verificar_existencia);

$response = array('existe' => false);

if (mysqli_num_rows($resultado_existencia) > 0) {
    $response['existe'] = true;
}

echo json_encode($response);
?>
