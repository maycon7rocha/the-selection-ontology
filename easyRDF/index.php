<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/processar_upload.php");
?>
<!DOCTYPE html>
<html lang="pt_BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Arquivo Turtle/RDF</title>
    <!-- Inclua os arquivos de estilo do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <style>
        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
            display: none;
        }

        pre {
            border: 2px solid black;
            padding: 10px;
        }

        /* #exportarTTL {
            float: right;
            margin-left: 10px;
        } */
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Upload de Arquivo Turtle/RDF</h1>
        <?php
        if (!empty($msg)) {
            mensagem($msg);
        }
        ?>

        <!-- Formulário para fazer upload do arquivo -->
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="arquivo" class="form-label">Selecione um arquivo TTL ou RDF:</label>
                <input type="file" class="form-control" id="arquivo" name="arquivo" accept=".ttl, .rdf, .rdfs, .txt" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Arquivo</button>
        </form>

        <?php
        // Chamada da função para listar os arquivos na pasta /arquivos
        $listarDiretorio = $_SERVER['DOCUMENT_ROOT'] . '/arquivos/';
        if (!file_exists($listarDiretorio)) {
            mkdir($listarDiretorio, 0777, true); // Cria o diretório recursivamente
        }
        $itens = scandir($listarDiretorio);

        // Remova as entradas "." e ".." da lista
        $itens = array_diff($itens, array(".", ".."));

        // Verifique se ainda há itens na lista após a remoção de "." e ".."
        if (count($itens) > 0) {
        ?>
            <hr>

            <table class="table table-striped table-bordered">
                <thead>
                    <th>Arquivos</th>
                    <th>Ação</th>
                </thead>
                <tbody>

                    <?php
                    listarArquivos($listarDiretorio);
                    ?>
                    </tr>
                </tbody>
            </table>
            <br>
            <!-- Adicione um botão "Exportar .ttl" -->
            <div class="d-flex flex-row-reverse">
                <button id="exportarTTL" class="btn btn-primary mb-1" style="display: none;">Exportar</button>
            </div>
            <!-- Div para exibir o resultado do Turtle -->
            <div id="resultadoTurtle"></div>
        <?php
        } ?>
    </div>

    <!-- Inclua os scripts do Bootstrap e jQuery (necessário para o Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>

<script>
    function processarArquivo(nomeArquivo) {
        // Envie uma solicitação AJAX para processar o arquivo no servidor
        $.post("processar_arquivo.php", {
            arquivo: nomeArquivo
        }, function(data) {
            // Exiba uma mensagem de sucesso ou erro para o usuário
            console.log(data);
            $("#resultadoTurtle").html(data);
            $("#exportarTTL").show();
        });
    }

    function apagarArquivo(nomeArquivo) {
        // Envie uma solicitação AJAX para apagar o arquivo no servidor
        $.post("apagar_arquivo.php", {
            arquivo: nomeArquivo
        }, function(data) {
            if (data.success) {
                alert(data.message);
                // Atualiza a página após a exclusão bem-sucedida
                location.href = '/';
            } else {
                alert(data.message);
            }
        }, "json");
    }

    // Verifica se a div resultadoTurtle tem conteúdo
    function verificaConteudo() {
        if ($("#resultadoTurtle").text().trim().length > 0) {
            $("#exportarTTL").show();
        } else {
            $("#exportarTTL").hide();
        }
    }

    setTimeout(function() {
        document.getElementById('alerta').classList.add('fade-out');
    }, 5000); // 5000 milissegundos (5 segundos)

    $(document).ready(function() {
        // Função para exportar o conteúdo como um arquivo .ttl
        $("#exportarTTL").click(function() {
            // Obtém o conteúdo do resultado
            var conteudo = $("#resultadoTurtle").text();

            // Cria um objeto Blob com o conteúdo
            var blob = new Blob([conteudo], {
                type: "text/turtle"
            });

            // Cria um URL para o Blob
            var url = URL.createObjectURL(blob);

            // Cria um elemento de link para download
            var link = document.createElement("a");
            link.href = url;
            link.download = "conditionConstruct.ttl";

            // Simula o clique no link para iniciar o download
            document.body.appendChild(link);
            link.click();

            // Remove o elemento de link após o download
            document.body.removeChild(link);
        });
    });
</script>