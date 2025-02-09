<?php

namespace app\Controllers;

use app\Models\CharacterModel;
use app\Models\MapModel;

class MapController extends Controller
{
    public function displayMap()
    {
        // Fetch map, characters, and enemies data
        $mapModel = new MapModel();
        $characterModel = new CharacterModel();

        $map = $mapModel->getMap();
        $characters = $characterModel->getAll();
        $enemies = $mapModel->getEnemies();

        // Load the view with the data
        $this->view->load('map', [
            'map' => $map,
            'characters' => $characters,
            'enemies' => $enemies
        ]);
    }
}