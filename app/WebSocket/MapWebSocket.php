<?php

namespace app\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use app\Models\MapObjectModel;
use app\Models\MapModel;

class MapWebSocket implements MessageComponentInterface {
    protected $clients;
	protected $mapObjectModel;
	protected $mapModel;
    
	public function __construct() {
        $this->clients = new \SplObjectStorage;
		$this->mapObjectModel = new MapObjectModel();
		$this->mapModel = new MapModel();
	}

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }
	
	// Options for messages: Load New Map, update position of an object, Update GridSize
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
				
		switch((string)$data['action']){
			case 'firstFetch':
				$allObjects = $this->mapObjectModel->getAllObjects();
				$allMaps = $this->mapModel->getAllMaps();
				foreach ($this->clients as $client)
				{
					if($client == $from)
						$client->send(
							json_encode([
								'action' => 'firstFetchReturn',
								'objects' => $allObjects,
								'maps'=> $allMaps
								//$objects = $mapObjectController->getObjects();
								// $gridSize = $data['map']['grid_size'] ?? 37;
								// $mapImage = $data['map']['image'] ?? '';
								// $mapId = $data['map']['id'] ?? 0;
							])
						);
				}
				break;
			case 'LoadNewMap':
				//...MapModel\getMapById
				break;
			case 'updatePosition':
				$objectId = $data['objectId'];
				$positionX = $data['positionX'];
				$positionY = $data['positionY'];

				// Update database using MapModel
				$this->mapObjectModel->updatePosition($objectId, $positionX, $positionY);

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
				break;
			case 'updateGridSize':
				$mapId = $data['mapId'];
				$gridSize = $data['gridSize'];

				// Update database using MapModel
				$this->mapModel->updateGridSize($mapId, $gridSize);

				// Broadcast new grid size to all clients except sender
				foreach ($this->clients as $client) {
					if ($client !== $from) {
						$client->send(json_encode([
							'action' => 'gridSizeUpdated',
							'gridSize' => $gridSize
						]));
					}
				}
				break;
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