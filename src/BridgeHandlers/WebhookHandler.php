<?php
namespace App\BridgeHandlers;

class WebhookHandler {
    public static function handle($request, $response, $args) {
        $data = json_decode($request->getBody()->getContents(), true);
            $type = $data['type'] ?? $data['resource'] ?? null;
            $action = $data['action'] ?? null;
            $result = '';
            switch ($type) {
                case 'userstory':
                    require_once __DIR__ . '/StoryHandler.php';
                    $result = \App\BridgeHandlers\StoryHandler::dispatch($action, $data);
                    break;
                case 'milestone':
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
                case 'wikipage':
                    $result = 'Wiki page event received'; // Add WikiPageHandler if needed
                    break;
                case 'test':
                    $result = 'Test event received';
                    break;
                default:
                    $result = 'Unknown type: ' . $type;
            }
        $response->getBody()->write(json_encode(['result' => $result]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
