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
        $req = $this->db()->prepare("INSERT INTO map_objects (name, image_url, positionX, positionY, statusEffects) VALUES (:name, :image_url, 0, 0, NULL)");
        $req->bindValue(':name', $name);
        $req->bindValue(':image_url', $imageUrl);
		$req->bindValue(':positionX', $positionX);
		$req->bindValue(':positionY', $positionY);
        return $req->execute();
		
		// return the inserted row with id
		$id = $this->db()->lastInsertId();
		$req = $this->db()->prepare("SELECT * FROM map_objects WHERE id = :id");
		$req->bindValue(':id', $id);
		$req->execute();
		return $req->fetch(PDO::FETCH_ASSOC);
    }
	
	public function updatePosition($id, $positionX, $positionY) {
    $req = $this->db()->prepare("UPDATE map_objects SET positionX = :positionX, positionY = :positionY WHERE id = :id");
    $req->bindValue(':positionX', $positionX);
    $req->bindValue(':positionY', $positionY);
    $req->bindValue(':id', $id);
    return $req->execute();
	}
	
	public function updateEffects($id, $statusEffects) {
		$serialStatusEffects = serialize($statusEffects);
		$req = $this->db()->prepare("UPDATE map_objects SET statusEffects = :serialStatusEffects WHERE id = :id");
		$req->bindValue(':serialStatusEffects', $serialStatusEffects);
		$req->bindValue(':id', $id);
		return $req->execute();
	}
}