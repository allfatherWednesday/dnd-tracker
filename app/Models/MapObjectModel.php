<?php

namespace app\Models;

use PDO;

class MapObjectModel extends Model
{
    public function getAllObjects()
    {
        $req = $this->db()->prepare("SELECT * FROM map_objects ORDER BY created_at DESC");
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addObject($name, $imageUrl, $positionX, $positionY)
    {
        $req = $this->db()->prepare("INSERT INTO map_objects (name, image_url, positionX, positionY) VALUES (:name, :image_url, 0, 0)");
        $req->bindValue(':name', $name);
        $req->bindValue(':image_url', $imageUrl);
		$req->bindValue(':positionX', $positionX);
		$req->bindValue(':positionY', $positionY);
        return $req->execute();
    }
	
	public function updatePosition($id, $positionX, $positionY) {
    $req = $this->db()->prepare("UPDATE map_objects SET positionX = :positionX, positionY = :positionY WHERE id = :id");
    $req->bindValue(':positionX', $positionX);
    $req->bindValue(':positionY', $positionY);
    $req->bindValue(':id', $id);
    return $req->execute();
	}
}