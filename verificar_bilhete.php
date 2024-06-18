<?php
require_once("ligarbd.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['linha_autocarro'])) {
    $linha_autocarro = $_POST['linha_autocarro'];

    $verificar_duplicatas = "SELECT * FROM bilhetes WHERE linha_autocarro = '$linha_autocarro'";
    $resultado_duplicatas = mysqli_query($basedados, $verificar_duplicatas);

    $response = array();
    if (mysqli_num_rows($resultado_duplicatas) > 0) {
        $response["existe"] = true;
    } else {
        $response["existe"] = false;
    }

    echo json_encode($response);
}
?>