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
			case 'updateRotation':
				$objectId = $data['objectId'];
				$newRotation = $data['newRotation'];
				
				$this->mapObjectModel->updateRotation($objectId, $newRotation);
				
				foreach ($this->clients as $client) {
						if ($client !== $from) {
								$client->send(json_encode([
										'action' => 'rotationUpdated',
										'objectId' => $objectId,
										'newRotation' => $newRotation
								]));
						}
				}
				break;
				case 'updateDuplicateCount':
						$objectId = $data['objectId'];
						$duplicateCount = $data['duplicateCount'];
						
						// Update database
						$this->mapObjectModel->updateDuplicateCount($objectId, $duplicateCount);
						
						// Broadcast to all clients except sender
						foreach ($this->clients as $client) {
								if ($client !== $from) {
										$client->send(json_encode([
												'action' => 'duplicateCountUpdated',
												'objectId' => $objectId,
												'duplicateCount' => $duplicateCount
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
			case 'binMap':
    $mapId = $data['id'] ?? null;
    if ($mapId && $this->mapModel->moveMapToBin($mapId)) {
        // Broadcast removal to all clients
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'action' => 'mapDeleted',
                'id' => $mapId
            ]));
        }
    } else {
        $from->send(json_encode(['action' => 'error', 'message' => 'Failed to bin map']));
    }
    break;

case 'fetchMapBinList':
    $binned = $this->mapModel->getBinnedMaps();
    $from->send(json_encode([
        'action' => 'mapBinList',
        'maps' => $binned
    ]));
    break;

case 'restoreMapBinObjects':
    $binIds = $data['ids'] ?? [];
    // You may want to get the current default grid size from somewhere.
    // For simplicity, we'll use a fixed value; adapt as needed.
    $defaultGridSize = 40; // or retrieve from global state
    $restored = [];
    foreach ($binIds as $binId) {
        $map = $this->mapModel->restoreMapFromBin($binId, $defaultGridSize);
        if ($map) {
            $restored[] = $map;
            foreach ($this->clients as $client) {
                $client->send(json_encode([
                    'action' => 'MapAdded',
                    'map1' => $map
                ]));
            }
        }
    }
    $from->send(json_encode([
        'action' => 'mapBinRestoreComplete',
        'count' => count($restored)
    ]));
    break;

case 'deleteMapBinObjects':
    $binIds = $data['ids'] ?? [];
    $deleted = 0;
    foreach ($binIds as $binId) {
        if ($this->mapModel->deleteMapFromBin($binId)) $deleted++;
    }
    $from->send(json_encode([
        'action' => 'mapBinDeleteComplete',
        'count' => $deleted
    ]));
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
			case 'deleteMap':
				$mapId = $data['id'] ?? null;
				if ($mapId) {
						$success = $this->mapModel->deleteMap($mapId);
						if ($success) {
								// Broadcast map deletion to all clients
								foreach ($this->clients as $client) {
										$client->send(json_encode([
												'action' => 'mapDeleted',
												'id' => $mapId
										]));
								}
						} else {
								$from->send(json_encode([
										'action' => 'error',
										'message' => "Failed to delete map {$mapId}"
								]));
						}
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
			case 'binObject':
				$id = $data['id'] ?? null;
				if ($id) {
						$success = $this->mapObjectModel->moveObjectToBin($id);
						if ($success) {
								// Broadcast removal to all clients (including sender – sender will handle it)
								foreach ($this->clients as $client) {
										$client->send(json_encode([
												'action' => 'ObjectRemoved',
												'id' => $id
										]));
								}
						} else {
								// Send error only to the requesting client
								$from->send(json_encode([
										'action' => 'error',
										'message' => "Failed to bin object {$id}"
								]));
						}
				}
				break;

			case 'fetchBinList':
					$binned = $this->mapObjectModel->getBinnedObjects();
					$from->send(json_encode([
							'action' => 'binList',
							'objects' => $binned
					]));
					break;
			case 'restoreBinObjects':
					$binIds = $data['ids'] ?? [];
					$restoredObjects = [];
					foreach ($binIds as $binId) {
							$obj = $this->mapObjectModel->restoreFromBin($binId);
							if ($obj) {
									$restoredObjects[] = $obj;
									// Broadcast each restored object to all clients
									foreach ($this->clients as $client) {
											$client->send(json_encode([
													'action' => 'objectAdded',
													'object' => $obj
											]));
									}
							}
					}
					// Send confirmation to the requesting client (optional)
					$from->send(json_encode([
							'action' => 'binRestoreComplete',
							'count' => count($restoredObjects)
					]));
					break;

				case 'deleteBinObjects':
						$binIds = $data['ids'] ?? [];
						$deletedCount = 0;
						foreach ($binIds as $binId) {
								if ($this->mapObjectModel->deleteFromBin($binId)) {
										$deletedCount++;
								}
						}
						// Notify the client to refresh the bin list
						$from->send(json_encode([
								'action' => 'binDeleteComplete',
								'count' => $deletedCount
						]));
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