<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\FirebaseSyncService();
$token = (new ReflectionClass($service))->getMethod('getAccessToken')->setAccessible(true)->invoke($service);
$baseUrl = "https://firestore.googleapis.com/v1/projects/" . env('FIREBASE_PROJECT_ID', 'syscarnet') . "/databases/(default)/documents/afiliados/881-9118311-1";

$client = new \GuzzleHttp\Client();
$response = $client->get($baseUrl, ['headers' => ['Authorization' => "Bearer $token"]]);
$raw = json_decode($response->getBody()->getContents(), true);

echo "RAW FIELDS FOR updated_at:\n";
print_r($raw['fields']['updated_at'] ?? 'NOT FOUND');
