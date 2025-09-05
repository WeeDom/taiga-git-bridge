<?php
namespace App\GitHubHandlers;

class UserHandler {
    public static function getUser($request, $response, $args) {
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
    }
}
