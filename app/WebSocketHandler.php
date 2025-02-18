<?php
namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use app\Models\MapObjectModel;

class WebSocketHandler implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if ($data['action'] === 'updatePosition') {
            $model = new MapObjectModel();
            $model->updatePosition($data['id'], $data['x'], $data['y']);
            foreach ($this->clients as $client) {
                $client->send(json_encode([
                    'action' => 'positionUpdated',
                    'id' => $data['id'],
                    'x' => $data['x'],
                    'y' => $data['y']
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}