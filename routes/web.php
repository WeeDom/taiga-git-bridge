<?php

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->get('/', function ($request, $response, $args) {
    phpinfo();
    $response->getBody()->write("Hello from SlimPHP, now with debug!");
    return $response;
});

$app->get('/taiga/projects', function ($request, $response, $args) {
    $taigaUrl = getenv('TAIGA_API_URL') ?: 'http://localhost:8080/api/v1/projects';
    $token = getenv('TAIGA_API_TOKEN');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $taigaUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $response->getBody()->write($result);
    return $response->withHeader('Content-Type', 'application/json')->withStatus($httpCode);
});

$app->get('/github/user', function ($request, $response, $args) {
    $githubUrl = 'https://api.github.com/user';
    $token = getenv('GITHUB_PAT');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $githubUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'User-Agent: taiga-git-bridge',
        'Accept: application/vnd.github.v3+json'
    ]);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $response->getBody()->write($result);
    return $response->withHeader('Content-Type', 'application/json')->withStatus($httpCode);
});

$app->get('/routes', function ($request, $response, $args) use ($app) {
    $routes = [];
    foreach ($app->getRouteCollector()->getRoutes() as $route) {
        $routes[] = [
            'pattern' => $route->getPattern(),
            'methods' => $route->getMethods(),
            'name' => $route->getName(),
        ];
    }
    $response->getBody()->write(json_encode($routes, JSON_PRETTY_PRINT));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->map(['GET', 'POST'], '/webhook', function ($request, $response, $args) {
    $data = json_decode($request->getBody()->getContents(), true);
    $response->getBody()->write('Webhook received where the new story is: ' . $data["by"]["username"]);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
