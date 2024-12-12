<?php
// URI de conexão sem autenticação
$uri = "mongodb://localhost:27017";  // Sem usuário e senha
$dbName = "metadados"; // Nome do banco de dados
$collectionName = "texts"; // Nome da coleção

try {
    $sentimentAnalyzer = new SentimentAnalysis($uri, $dbName, $collectionName);

    // Texto a ser analisado
    $text = "Não gosto de acordar cedo";

    // Verifica se o sentimento do texto já está no banco de dados ou realiza a análise
    $result = $sentimentAnalyzer->checkAndRetrieveSentiment($text);

    // Exibe o resultado
    echo "Sentimento: " . $result['sentimento'] . "\n";
    echo "Porcentagem: " . $result['porcentagem'] . "%\n";

} catch (MongoDB\Driver\Exception\AuthenticationException $e) {
    echo "Erro de autenticação: " . $e->getMessage() . "\n";
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Erro na conexão ou execução do MongoDB: " . $e->getMessage() . "\n";
}
