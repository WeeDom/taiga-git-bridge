<?php
namespace App\BridgeHandlers;

class StoryHandler {
    public static function dispatch($action, $data) {
        switch ($action) {
            case 'create':
                return 'Story created: ' . ($data['subject'] ?? '');
            case 'update':
                return 'Story updated: ' . ($data['subject'] ?? '');
            case 'delete':
                return 'Story deleted: ' . ($data['subject'] ?? '');
            default:
                return 'Unknown story action';
        }
    }
}