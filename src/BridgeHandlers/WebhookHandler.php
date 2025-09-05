<?php
namespace App\BridgeHandlers;

class WebhookHandler {
    public static function handle($request, $response, $args) {
        $data = json_decode($request->getBody()->getContents(), true);
        $username = isset($data["by"]["username"]) ? $data["by"]["username"] : 'unknown';
        $response->getBody()->write('Webhook received where the new story is: ' . $username);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
