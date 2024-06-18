<?php
session_start();
include('ligarbd.php'); 

// Verifica se o id_bilhete está presente na URL e se o id_utilizador está presente na URL
if (!isset($_GET['id_bilhete']) || !isset($_GET['id_utilizador'])) {
    echo "Parâmetros incompletos. Por favor, verifique o URL.";
    exit;
}

// Obtém os valores dos parâmetros da URL
$id_bilhete = $_GET['id_bilhete'];
$id_utilizador = $_GET['id_utilizador'];

// Prepara a consulta para reduzir a quantidade de bilhetes em 1
$consulta_quantidade_bilhetes = "
    UPDATE totalbilhetesporlinha 
    SET total_bilhetes = total_bilhetes - 1 
    WHERE id_bilhete = ? AND id_utilizador = ? AND total_bilhetes > 0
";
$stmt_quantidade_bilhetes = mysqli_prepare($basedados, $consulta_quantidade_bilhetes);
mysqli_stmt_bind_param($stmt_quantidade_bilhetes, "ii", $id_bilhete, $id_utilizador);

// Executa a consulta
if (mysqli_stmt_execute($stmt_quantidade_bilhetes) && mysqli_stmt_affected_rows($stmt_quantidade_bilhetes) > 0) {
    // Redireciona de volta para bilhetes_utilizador.php
    header("Location: bilhetes_utilizador.php");
    exit;
} else {
    echo "Erro ao validar o bilhete ou não há bilhetes suficientes. Por favor, tente novamente.";
}

?>