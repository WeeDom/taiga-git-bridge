<?php
namespace App\BridgeHandlers;

class IssueHandler {
    public static function dispatch($action, $data) {
        switch ($action) {
            case 'create':
                return 'Issue created: ' . ($data['subject'] ?? '');
            case 'update':
                return 'Issue updated: ' . ($data['subject'] ?? '');
            case 'delete':
                return 'Issue deleted: ' . ($data['subject'] ?? '');
            default:
                return 'Unknown issue action';
        }
    }
}