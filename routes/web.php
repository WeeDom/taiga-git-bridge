<?php

use Slim\Factory\AppFactory;

$app = AppFactory::create();

use App\TaigaHandlers\ProjectsHandler;
use App\GitHubHandlers\UserHandler;
use App\BridgeHandlers\WebhookHandler;


$app->get('/', function ($request, $response, $args) {
    phpinfo();
    $response->getBody()->write("Hello from SlimPHP, now with debug!");
    return $response;
});

$app->group('/github', function ($group) {
    $group->get('/user', [UserHandler::class, 'getUser']);
    // $group->get('/repos', [UserHandler::class, 'getRepos']); // Example for future expansion
});

$app->group('/taiga', function ($group) {
    $group->get('/projects', [ProjectsHandler::class, 'getProjects']);
    // $group->get('/users', [ProjectsHandler::class, 'getUsers']); // Example for future expansion
});


$app->map(['GET', 'POST'], '/webhook', [WebhookHandler::class, 'handle']);

$app->group('/bridge', function ($group) {
    // $group->map(['GET', 'POST'], '/webhook', [WebhookHandler::class, 'handle']);
    // $group->get('/stats', [BridgeStatsHandler::class, 'getStats']); // Example for future expansion
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

$app->run();
