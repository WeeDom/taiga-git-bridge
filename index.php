
<?php
require __DIR__ . '/vendor/autoload.php';


use GuzzleHttp\Client;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

 // Load .env config
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} else {
    die("No .env file found");
}
$app = AppFactory::create();
$app->get('/taiga/whoami', function ($request, $response, $args) {
    $client = new Client();
    $taigaUrl = $_ENV['TAIGA_API_URL'] ?: 'http://localhost:9000/api/v1';
    $whoamiUrl = rtrim($taigaUrl, '/') . '/users/me';
    $taigaToken = $_ENV['TAIGA_TOKEN'] ?? getenv('TAIGA_TOKEN');
    $headers = [
        'Authorization' => 'Bearer ' . $taigaToken,
        'Content-Type' => 'application/json'
    ];

    $debug = ["taiga_token" => $taigaToken,
        "whoami_url" => $whoamiUrl,
        "headers" => $headers
    ];
    try {
        $res = $client->request('GET', $whoamiUrl, [
            'headers' => $headers
        ]);
        $body = $res->getBody()->getContents();
        $response->getBody()->write(json_encode([
            'debug' => $debug,
            'response' => json_decode($body, true)
        ]));
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage(),
            'debug' => $debug]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});
$app->post('/taiga/token', function ($request, $response, $args) {
    $client = new Client();
    $body = $request->getParsedBody();
    $username = $body['username'] ?? 'admin';
    $password = $body['password'] ?? '123123';
    $taigaUrl = getenv('TAIGA_API_URL') ?: 'https://api.taiga.io/api/v1';
    $authUrl = rtrim($taigaUrl, '/') . '/auth';
    try {
        $res = $client->request('POST', $authUrl, [
            'json' => [
                'type' => 'normal',
                'username' => $username,
                'password' => $password
            ]
        ]);
        $tokenData = json_decode($res->getBody()->getContents(), true);
        $response->getBody()->write(json_encode($tokenData));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// GitHub API connectivity test route
$app->get('/github/user', function ($request, $response, $args) {
    $client = new Client();
    $githubUrl = $_ENV['GITHUB_API_URL'] ?? getenv('GITHUB_API_URL') ?: 'https://api.github.com/user';
    $githubToken = $_ENV['GITHUB_TOKEN'] ?? getenv('GITHUB_TOKEN');
    $headers = [
        'Authorization' => 'token ' . $githubToken,
        'User-Agent' => 'taiga-git-bridge'
    ];
    try {
        $res = $client->request('GET', $githubUrl, [
            'headers' => $headers
        ]);
        $body = $res->getBody()->getContents();
        $response->getBody()->write(json_encode([
            'github_response' => json_decode($body, true)
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => $e->getMessage()
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});



$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write("Hello, SlimPHP Day 1!");
    return $response;
});

$app->get('/taiga/projects', function ($request, $response, $args) {
    // Example: Fetch projects from Taiga API
    $client = new Client();
    $taigaUrl = getenv('TAIGA_API_URL') ?: 'https://api.taiga.io/api/v1/projects';
    $taigaToken = getenv('TAIGA_TOKEN');
    $headers = $taigaToken ? ['Authorization' => 'Bearer ' . $taigaToken] : [];
    try {
        $res = $client->request('GET', $taigaUrl, [
            'headers' => $headers
        ]);
        $body = $res->getBody()->getContents();
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->run();
