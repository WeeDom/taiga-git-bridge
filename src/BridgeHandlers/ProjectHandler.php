<?php
namespace App\BridgeHandlers;

class ProjectHandler {
    public static function dispatch($action, $data) {
        switch ($action) {
            case 'create':
                return 'Project created: ' . ($data['name'] ?? '');
            case 'update':
                return 'Project updated: ' . ($data['name'] ?? '');
            case 'delete':
                return 'Project deleted: ' . ($data['name'] ?? '');
            default:
                return 'Unknown project action';
        }
    }
}