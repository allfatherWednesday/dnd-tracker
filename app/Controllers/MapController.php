<?php

namespace app\Controllers;

use app\Models\CharacterModel;
use app\Models\MapModel;

class MapController extends Controller
{
	//TODO add params to it to select a different map
    /*public function displayMap()
    {
        // Fetch map
        $mapModel = new MapModel();

        $map = $mapModel->getMap();

        // Load the view with the data
        $this->view->load('map', [
            'map' => $map,
        ]);
    }*/
	
	public function displayMap(array $parameters = []): void
    {
        try {
            // Extract mapId from parameters with named key fallback to default
            $mapId = (int)($parameters['mapId'] ?? $parameters[0] ?? 1);
            
            $mapModel = new MapModel();
            $map = $mapModel->getMapById($mapId);

            if (!$map) {
                throw new NotFoundException("Map with ID {$mapId} not found");
            }

            $this->view->load('map', [
                'map' => $map,
                'gridSize' => $map['grid_size'] ?? 37,
            ]);
            
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $this->view->load('error', ['message' => 'Failed to load map data']);
        }
    }
	/*public function addMap(id)
    {
		TODO
    }*/
}