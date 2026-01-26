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
		print_r("Running Socket \n");
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
				
				foreach ($allObjects as $key => $value) {
					
					
					if (is_null($allObjects[$key]['statusEffects'])) {
						$allObjects[$key]['statusEffects'] = [];
					} else {
						$allObjects[$key]['statusEffects'] = unserialize($allObjects[$key]['statusEffects']);
					}
				
				}
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
				print_r("firstFetch \n");
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
			case 'updateEffects':
				$objectId = $data['objectId'];
				$statusEffects = $data['statusEffects'];
				
				// Update database using MapModel
				$this->mapObjectModel->updateEffects($objectId, $statusEffects);
				
				// Broadcast new grid size to all clients except sender
				foreach ($this->clients as $client) {
					if ($client !== $from) {
						$client->send(json_encode([
							'action' => 'effectsUpdated',
							'objectId' => $objectId,
							'statusEffects' => $statusEffects
						]));
					}
				}
				break;
			case 'updateSize':
				$objectId = $data['objectId'];
				$newSize = $data['newSize'];
				
				// Update database using MapModel
				$this->mapObjectModel->updateSize($objectId, $newSize);
				
				// Broadcast new grid size to all clients except sender
				foreach ($this->clients as $client) {
					if ($client !== $from) {
						$client->send(json_encode([
							'action' => 'sizeUpdated',
							'objectId' => $objectId,
							'newSize' => $newSize
						]));
					}
				}
				break;	
			case 'addObject':
				$name = $data['name'] ?? null;
				$imageUrl = $data['image_url'] ?? null;
				$positionX = 0;
				$positionY = 0;
				
				if ($name && $imageUrl) {
					
					$newObject = $this->mapObjectModel->addObject($name, $imageUrl, $positionX, $positionY);
											
					if (is_null($newObject['statusEffects'])) {
							$newObject['statusEffects'] = [];
						} else {
							$newObject['statusEffects'] = unserialize($newObject['statusEffects']);
						}
					
					// Broadcast new object to all clients
					foreach ($this->clients as $client) {
						$client->send(json_encode([
							'action' => 'objectAdded',
							'object' => $newObject
						]));
					}
				}
				break;
			case "removeObject":
				/*if (!isset($data['id'])) {
					$from->send(json_encode([
						"action" => "error",
						"message" => "Missing object ID for removal"
					]));
					break;
				}*/

				$id = $data['id'];
				print_r("Requesting to delete {$id}\n");


				$newObject = $this->mapObjectModel->removeObject($id);

				if ($newObject) {
					//broadcast to all clients about removal
					foreach ($this->clients as $client) {
						$client->send(json_encode([
							"action" => "ObjectRemoved",
							"id" => $id
						]));
					}
					
				} else {
					//fail handling
					$from->send(json_encode([
						"action" => "error",
						"message" => "Failed to remove object {$id}"
					]));
				}
				break;
			case "addMap":
				$added_map_name = $data['name'];
				$added_map_image_url = $data['image_url'];
				$grid_size = $data['grid_size'];
				print_r("Added map: {$added_map_name}\n");
				
				$newMap = $this->mapModel->addMap($added_map_name, $added_map_image_url, $grid_size);
				
				// Broadcast new object to all clients
				foreach ($this->clients as $client) {
					$client->send(json_encode([
						'action' => 'MapAdded',
						'map1' => $newMap
					]));
				}
				break;
			case "switchMap":
				$selected_map_id = $data['selectedId'];
				foreach ($this->clients as $client) {
					$client->send(json_encode([
						'action' => 'mapSwitched',
						'selected_map_id' => $selected_map_id
					]));
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