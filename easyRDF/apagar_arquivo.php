<?php 

$response = array();

if (isset($_POST["arquivo"])) {
    $nomeArquivo = $_POST["arquivo"];
    $diretorio = "arquivos/";

    $caminhoArquivo = $diretorio . $nomeArquivo;

    if (unlink($caminhoArquivo)) {
        $response["success"] = true;
        $response["message"] = "Arquivo '$nomeArquivo' foi apagado com sucesso!";
    } else {
        $response["success"] = false;
        $response["message"] = "Ocorreu um erro ao apagar o arquivo '$nomeArquivo'.";
    }
} else {
    $response["success"] = false;
    $response["message"] = "Parâmetro de arquivo ausente.";
}

// Envia a resposta JSON
header('Content-Type: application/json');
echo json_encode($response);