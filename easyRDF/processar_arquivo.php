<?php
require 'vendor/autoload.php';

if (isset($_POST["arquivo"])) {
    $nomeArquivo = $_POST["arquivo"];
    $diretorio = $_SERVER['DOCUMENT_ROOT'] . "/arquivos/";

    $caminhoArquivo = $diretorio . $nomeArquivo;

    // Verifica se o arquivo existe antes de tentar lê-lo
    if (file_exists($caminhoArquivo)) {
        $conteudo = file_get_contents($caminhoArquivo);

        // Inicializa uma variável para armazenar as linhas de prefixo
        $prefixos = '';
        // Itera sobre o conteúdo para pegar os prefixos e mantê-los no conteúdo principal
        $linhas = explode("\n", $conteudo);
        foreach ($linhas as $linha) {
            if (strpos($linha, "@prefix") === 0) {
                $prefixos .= $linha . "\n";
            } 
        }

        // PRIMEIRO PASSO
        @$graph = new \EasyRdf\Graph();
        $graph->parse($conteudo, 'turtle');
        // passa para tutle
        $turtle = $graph->serialise('turtle', array('format' => 'pretty'));
        // remove as aspas
        $turtle_noquotes = preg_replace('/(\'|")/', '', $turtle);

        // SEGUNDO PASSO
        $graph = new \EasyRdf\Graph();

        // adicionar os prefix perdidos
        $turtle_noquotes = $prefixos.$turtle_noquotes;
        $graph->parse($turtle_noquotes, 'turtle');

        // passa para tutle novamente
        $turtle_noquotes = $graph->serialise('turtle', array('format' => 'pretty'));

        // Faça o que quiser com o conteúdo do arquivo, aqui estamos apenas imprimindo-o
        echo "<pre>" . htmlspecialchars($turtle_noquotes) . "</pre>";
    } else {
        echo "O arquivo $nomeArquivo não foi encontrado.";
    }
} else {
    echo "Parâmetro de arquivo ausente.";
}
