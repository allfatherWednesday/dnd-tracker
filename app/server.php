<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\WebSocketHandler;

require dirname(__FILE__) . '/vendor/autoload.php';
require __DIR__ . '/app/Models/MapObjectModel.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketHandler()
        )
    ),
    8080
);

$server->run();