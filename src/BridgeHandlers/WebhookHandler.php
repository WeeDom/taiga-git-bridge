<?php
namespace App\BridgeHandlers;

class WebhookHandler {
    public static function handle($request, $response, $args) {
        $data = json_decode($request->getBody()->getContents(), true);
        $resource = isset($data['resource']) ? $data['resource'] : null;
        $action = isset($data['action']) ? $data['action'] : null;
        $result = '';
        switch ($resource) {
            case 'story':
                require_once __DIR__ . '/StoryHandler.php';
                $result = \App\BridgeHandlers\StoryHandler::dispatch($action, $data);
                break;
            case 'project':
                require_once __DIR__ . '/ProjectHandler.php';
                $result = \App\BridgeHandlers\ProjectHandler::dispatch($action, $data);
                break;
            case 'issue':
                require_once __DIR__ . '/IssueHandler.php';
                $result = \App\BridgeHandlers\IssueHandler::dispatch($action, $data);
                break;
            case 'task':
                require_once __DIR__ . '/TaskHandler.php';
                $result = \App\BridgeHandlers\TaskHandler::dispatch($action, $data);
                break;
            default:
                $result = 'Unknown resource';
        }
        $response->getBody()->write(json_encode(['result' => $result]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
