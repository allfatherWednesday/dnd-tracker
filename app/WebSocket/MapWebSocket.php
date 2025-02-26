<?php

namespace app\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use app\Models\MapObjectModel;

class MapWebSocket implements MessageComponentInterface {
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
            $objectId = $data['objectId'];
            $positionX = $data['positionX'];
            $positionY = $data['positionY'];

            // Update database
            $mapObjectModel = new MapObjectModel();
            $mapObjectModel->updatePosition($objectId, $positionX, $positionY);

            // Broadcast to all clients except sender
            foreach ($this->clients as $client) {
                if ($client !== $from) {
                    $client->send(json_encode([
                        'action' => 'positionUpdated',
                        'objectId' => $objectId,
                        'positionX' => $positionX,
                        'positionY' => $positionY
                    ]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}