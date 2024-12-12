# Sistema de Reconhecimento de Sentimento usando a API do WoolBall

Este projeto foi desenvolvido para ser usado no site [Bugadoz.dev](https://bugadoz.dev) como uma ferramenta de moderação de mensagens. Ele analisa o sentimento das mensagens enviadas pelos usuários e, com base nos resultados, pode reportá-las para os administradores caso sejam detectados sentimentos inadequados.

O sistema utiliza o banco de dados **MongoDB** para armazenar:
- O sentimento identificado na mensagem.
- A porcentagem dos demais sentimentos detectados.

## Como funciona?
1. **Análise de Sentimentos**:
   O sistema consome a [API do WoolBall](https://woolball.xyz/) para identificar os sentimentos presentes em uma mensagem enviada pelo usuário.

2. **Armazenamento de Dados**:
   - O sentimento predominante na mensagem é armazenado no MongoDB.
   - As porcentagens de todos os sentimentos detectados também são salvas para futura referência.

3. **Moderação Automática**:
   - Caso sentimentos negativos ou ofensivos sejam detectados com alta probabilidade, a mensagem é reportada automaticamente aos administradores do site para revisão.

## Tecnologias Utilizadas
- **Linguagem:** PHP 8.2
- **Banco de Dados:** MongoDB
- **API de Sentimento:** [WoolBall](https://woolball.xyz/)
- **Infraestrutura:** aaPanel para gerenciamento de servidores

## Requisitos
Antes de executar o sistema, verifique se você possui:
- PHP 8.2 instalado.
- MongoDB configurado e em execução.
- Chave de acesso à API do WoolBall.

## Instalação
1. Clone o repositório do projeto:
   ```bash
   git clone https://github.com/bugadoz/Sistema-de-Reconhecimento-de-Sentimento-usando-a-API-do-WoolBall.git
   cd sentimento-woolball
   ```

2. Instale as dependências necessárias:
   ```bash
   composer install
   ```

3. Configure o uso e a classe com as informações da API e do banco de dados:
   ```env
   MONGODB_URI=mongodb://localhost:27017
   MONGODB_DATABASE=nome_do_banco
   WOOLBALL_API_KEY=sua_chave_api
   ```

4. Execute o sistema:
   ```bash
   php -S localhost:8000
   ```

## Uso
- Envie uma mensagem para o endpoint configurado para moderação.
- O sistema irá processar a mensagem, detectar os sentimentos e armazenar os resultados no banco de dados.

## Exemplos de Uso
### Enviar Mensagem para Moderação
```php
$mensagem = "Essa mensagem precisa ser analisada.";
$resultado = enviarMensagemParaAnalise($mensagem);

print_r($resultado);
```
Saída esperada:
```json
{
  "sentimento_predominante": "positivo",
  "detalhes": {
    "positivo": 75,
    "negativo": 10,
    "neutro": 15
  }
}
```

## Detalhes da API WoolBall
Para maiores informações sobre a API, consulte a documentação oficial: [WoolBall API](https://woolball.xyz/).

## Contribuições
Contribuições são bem-vindas! Por favor, envie um pull request ou abra uma issue no repositório.

## Licença
Este projeto está licenciado sob a [Licença MIT](LICENSE).

