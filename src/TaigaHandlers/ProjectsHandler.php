<?php
namespace App\TaigaHandlers;

class ProjectsHandler {
	public static function getProjects($request, $response, $args) {
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
	}
}
