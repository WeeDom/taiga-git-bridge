<?php
namespace App\BridgeHandlers;

class TaskHandler {
    public static function dispatch($action, $data) {
        switch ($action) {
            case 'create':
                return 'Task created: ' . ($data['subject'] ?? '');
            case 'update':
                return 'Task updated: ' . ($data['subject'] ?? '');
            case 'delete':
                return 'Task deleted: ' . ($data['subject'] ?? '');
            default:
                return 'Unknown task action';
        }
    }
}