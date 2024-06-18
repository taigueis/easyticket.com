<?php
// Fazer a ligação à Base de Dados
require_once('ligarbd.php');

// Função para verificar se já existe uma viagem com as mesmas características para uma linha específica
function verificarExistenciaViagem($basedados, $linha_autocarro) {
    // Consulta SQL para contar o número de viagens para a linha_autocarro especificada
    $verificar_existencia = "SELECT COUNT(*) AS num_registos FROM viagem 
                             WHERE linha_autocarro = '$linha_autocarro'";
                             
    $resultado_existencia = mysqli_query($basedados, $verificar_existencia);
    $num_registos = mysqli_fetch_assoc($resultado_existencia)['num_registos'];
    
    // Verificar se o número de registos é igual a 68 para a linha 3001
    if ($linha_autocarro === '3001' && $num_registos >= 68) {
        return true; // Não é possível adicionar mais registos
    }

    // Verificar se o número de registos é igual a 68 para a linha 3002
    if ($linha_autocarro === '3002' && $num_registos >= 68) {
        return true; // Não é possível adicionar mais registos
    }

       // Verificar se o número de registos é igual a 72 para a linha 3301
       if ($linha_autocarro === '3301' && $num_registos >= 72) {
        return true; // Não é possível adicionar mais registos
    }

    // Verificar se o número de registos é igual a 64 para a linha 3302
    if ($linha_autocarro === '3302' && $num_registos >= 66) {
        return true; // Não é possível adicionar mais registos
    }

    return false; // É possível adicionar mais registos
}

// Extrair o número da linha_autocarro do parâmetro GET
$linha_autocarro = $_GET['linha_autocarro'];

// Verificar se já existem viagens com as mesmas características para a linha_autocarro especificada
$existemViagens = verificarExistenciaViagem($basedados, $linha_autocarro);

// Retornar o resultado como JSON
echo json_encode(['existemViagens' => $existemViagens]);
?>
