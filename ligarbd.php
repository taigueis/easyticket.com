<?php
    $servidor = 'localhost';
    $utilizador = 'root';
    $pass = '';
    $nome_bd = 'easyticket';

    $basedados = mysqli_connect($servidor, $utilizador, $pass, $nome_bd);

    if (!$basedados) {
        echo "Erro ao ligar à base de dados!" . mysqli_connect_error();
        exit;
    }
?>
