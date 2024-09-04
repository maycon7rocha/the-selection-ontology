<?php
if (!empty($_FILES["arquivo"])) {
    // Verifica se o arquivo foi enviado sem erros
    if ($_FILES["arquivo"]["error"] == UPLOAD_ERR_OK) {
        // Verifica se o tamanho do arquivo é menor ou igual a 10MB (10 * 1024 * 1024 bytes)
        if ($_FILES["arquivo"]["size"] <= 10485760) {
            // Verifica se a extensão do arquivo é .ttl ou .rdf
            $extensoes_permitidas = array("ttl", "rdf", "rdfs", "txt");
            $extensao_arquivo = pathinfo($_FILES["arquivo"]["name"], PATHINFO_EXTENSION);

            if (in_array($extensao_arquivo, $extensoes_permitidas)) {
                // Diretório de destino
                $diretorio_destino = $_SERVER['DOCUMENT_ROOT'] . "/arquivos/";

                // Move o arquivo para o diretório de destino
                $caminho_destino = $diretorio_destino . $_FILES["arquivo"]["name"];
                move_uploaded_file($_FILES["arquivo"]["tmp_name"], $caminho_destino);

                // Exibe um alerta de sucesso usando Bootstrap
                $msg['tipo'] = 'success';
                $msg['msg'] = 'O arquivo foi enviado com sucesso.';
            } else {
                // Exibe um alerta de erro para extensão inválida
                $msg['tipo'] = 'danger';
                $msg['msg'] = 'Extensão de arquivo inválida. Por favor, envie um arquivo TTL, RDF ou RDFS';
            }
        } else {
            // Exibe um alerta de erro para tamanho de arquivo excedido
            $msg['tipo'] = 'danger';
            $msg['msg'] = ' O tamanho do arquivo excede o limite de 10MB.';
        }
    } else {
        // Exibe um alerta de erro para erros de upload
        $msg['tipo'] = 'danger';
        $msg['msg'] = 'Ocorreu um erro durante o upload do arquivo. Por favor, tente novamente.';
    }
}

function mensagem($mensagem = false)
{
    // vardump($mensagem);
    echo '<div class="alert alert-' . $mensagem['tipo'] . ' alert-dismissible fade show "  role="alert" id="alerta">
            ' . $mensagem['msg'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </button>
        </div>';

}

function listarArquivos($diretorio) {
    // Verifica se o diretório existe
    if (is_dir($diretorio)) {
        // Abre o diretório
        if ($handle = opendir($diretorio)) {
            // Loop para ler os arquivos do diretório
            while (false !== ($arquivo = readdir($handle))) {
                // Exclui os diretórios pai e atual
                if ($arquivo != "." && $arquivo != "..") {
                    // Exibe o nome do arquivo
                    echo '<tr>';
                    echo '<td><a href="' . $diretorio . $arquivo . '">' . $arquivo . '</a></td>';
                    echo '<td>';
                    echo '<button type="button" class="btn btn-primary m-1" onclick="processarArquivo(\'' . $arquivo . '\')">Gerar Pretty Turtle</button>';
                    echo '<button type="button" class="btn btn-danger" onclick="apagarArquivo(\'' . $arquivo . '\')">Apagar</button>';
                    echo '</td>';
                    echo '</tr>';
                }
            }
            // Fecha o diretório
            closedir($handle);
        }else{
           echo 'nenhum arquivo'; 
        }
    } else {
        echo "O diretório não existe.";
    }
}
