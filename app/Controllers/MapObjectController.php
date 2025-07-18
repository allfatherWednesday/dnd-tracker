<?php

namespace app\Controllers;

use app\Models\MapObjectModel;

class MapObjectController extends Controller
{
    public function addObject()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $imageUrl = $_POST['image_url'] ?? '';
			$positionX = $_POST['positionX'] ?? '';
			$positionY = $_POST['positionY'] ?? '';

            if (!empty($name) && !empty($imageUrl)) {
                $mapObjectModel = new MapObjectModel();
                $mapObjectModel->addObject($name, $imageUrl, $positionX, $positionY);
                $this->redirect('map');
            } else {
                echo "Name and image and positions URL are required.";
            }
        }
    }

    public function getObjects()
    {
        $mapObjectModel = new MapObjectModel();
        return $mapObjectModel->getAllObjects();
    } 
}