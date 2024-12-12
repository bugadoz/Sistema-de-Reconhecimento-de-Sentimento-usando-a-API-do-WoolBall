<?php

class SentimentAnalysis {

    private $client;
    private $db;
    private $collection;

    public function __construct($mongoUri, $dbName, $collectionName) {
        // Conectar ao MongoDB com autenticação
        try {
            $this->client = new MongoDB\Client($mongoUri);
            $this->db = $this->client->$dbName;
            $this->collection = $this->db->$collectionName;
        } catch (Exception $e) {
            echo 'Erro de conexão: ' . $e->getMessage();
            exit;
        }
    }

    // Função para realizar a análise de sentimento
   // Função para realizar a análise de sentimento
// Função para realizar a análise de sentimento
public function analyzeSentiment($text) {
    // Configuração da API
    $apiBaseUrl = "https://api.woolball.xyz";
    $endpoint = "/v1/zero-shot-classification";
    $url = $apiBaseUrl . $endpoint;

    // Texto e rótulos candidatos
    $payload = [
        "Text" => $text,
        "CandidateLabels" => ["positive", "negative", "question", "neutral", "happy", "sad", "angry", "surprised", "anxiety", "disgust", "confused", "enthusiasm"]
    ];

    // Cabeçalhos da requisição
    $headers = [
        "Authorization: Bearer SUA_CHAVE_API", // Substitua com sua chave de API 
        "Content-Type: application/json"
    ];

    // Configurando e enviando a requisição
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Executa a requisição e captura a resposta
    $response = curl_exec($ch);

    // Verificação de erros na requisição
    if (curl_errno($ch)) {
        throw new Exception('Erro na requisição: ' . curl_error($ch));
    }

    // Fechar a conexão CURL
    curl_close($ch);

    // Verifique se a resposta não está vazia
    if (empty($response)) {
        throw new Exception('Resposta vazia da API.');
    }

    // Exibir a resposta para depuração (remova após o teste)
    echo '<pre>' . htmlspecialchars($response) . '</pre>';

    // Verifique se a resposta está no formato JSON
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Erro ao decodificar a resposta JSON: ' . json_last_error_msg());
    }

    // Verificação se os dados esperados existem na resposta
    $labels = $data['data']['Labels'] ?? [];
    $scores = $data['data']['Scores'] ?? [];

    if (empty($labels) || empty($scores)) {
        throw new Exception('Dados de rótulos ou pontuações ausentes na resposta da API.');
    }

    // Se a resposta for válida
    if ($labels && $scores) {
        // Tradução dos rótulos para categorias em português
        $categorias = [
            "negative" => "Negativo",
            "positive" => "Positivo",
            "neutral" => "Neutro",
            "question" => "Ajuda",
            "happy" => "Feliz",
            "sad" => "Triste",
            "angry" => "Raiva",
            "surprised" => "Surpresa",
            "anxiety" => "Ansiedade",
            "disgust" => "Desgosto",
            "confused" => "Confuso",
            "enthusiasm" => "Entusiasmo"
        ];

        // Encontrando o sentimento com maior pontuação
        $maxScore = max($scores);
        $maxIndex = array_search($maxScore, $scores);
        $topLabel = $labels[$maxIndex];

        // Traduzindo o rótulo para português
        $sentimento = $categorias[$topLabel] ?? "Desconhecido";
        $porcentagem = round($maxScore * 100, 2);

        return ['sentimento' => $sentimento, 'porcentagem' => $porcentagem];
    } else {
        throw new Exception('Erro ao processar a resposta da API: Dados de rótulos ou pontuação ausentes.');
    }
}



    // Função para registrar os sentimentos e porcentagens no MongoDB
    public function registerSentiment($text, $sentimento, $porcentagem) {
        // Verifica se o texto já existe
        $existingRecord = $this->collection->findOne(['text' => $text]);

        if (!$existingRecord) {
            // Registra o sentimento e a porcentagem
            $this->collection->insertOne([
                'text' => $text,
                'sentimento' => $sentimento,
                'porcentagem' => $porcentagem,
                'data' => new MongoDB\BSON\UTCDateTime() // Data de registro
            ]);
        }
    }

    // Função para verificar o texto no MongoDB e trazer os dados de sentimento se existir
    public function checkAndRetrieveSentiment($text) {
        // Verifica se o texto já existe no banco de dados
        $existingRecord = $this->collection->findOne(['text' => $text]);

        if ($existingRecord) {
            // Retorna os dados do sentimento do MongoDB
            return [
                'sentimento' => $existingRecord['sentimento'],
                'porcentagem' => $existingRecord['porcentagem']
            ];
        } else {
            // Caso não exista, realiza a análise e registra no banco
            $result = $this->analyzeSentiment($text);
            $this->registerSentiment($text, $result['sentimento'], $result['porcentagem']);
            return $result;
        }
    }
}


?>
