// app/server.php
<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\WebSocketHandler;

// Correct path to vendor autoload
require dirname(__DIR__) . '/vendor/autoload.php';

// Correct paths to model and handler
require __DIR__ . '/Models/MapObjectModel.php';
require __DIR__ . '/WebSocketHandler.php';

// Load .env from project root
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
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