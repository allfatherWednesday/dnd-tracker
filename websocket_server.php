<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use app\WebSocket\MapWebSocket;

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize database connection for models
require __DIR__ . '/app/Router.php'; // Load your application's bootstrap
$router = new app\Router();
$router->globalVars(); // This will define your database constants

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new MapWebSocket()
        )
    ),
    8080
);

$server->run();