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
		$DB1 = $this->db();
		$req1 = $DB1->prepare("INSERT INTO map_objects (name, image_url, positionX, positionY, statusEffects, size, rotation) VALUES (:name, :image_url, 0, 0, NULL, 1, 0)");
		$req1->bindValue(':name', $name);
		$req1->bindValue(':image_url', $imageUrl);
		$success = $req1->execute();    
		if (!$success) {
			error_log("Database error: " . print_r($req1->errorInfo(), true));
			return false;
		}
		
		// return the inserted row with id
		$id = $DB1->lastInsertId();
		$req2 = $DB1->prepare("SELECT * FROM map_objects WHERE id = :id");
		$req2->bindValue(':id', $id);
		$req2->execute();
		$result = $req2->fetch(PDO::FETCH_ASSOC);
		return $result;
    }
	
	public function removeObject($id) {
		$req = $this->db()->prepare("DELETE FROM map_objects WHERE id = :id");
		$req->bindValue(':id', $id, PDO::PARAM_INT);
		$success = $req->execute();

		if (!$success) {
			error_log("Database error while removing object: " . print_r($req->errorInfo(), true));
			return false;
		}

		return true;
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
	public function updateRotation($id, $rotation) {
    $req = $this->db()->prepare("UPDATE map_objects SET rotation = :rotation WHERE id = :id");
    $req->bindValue(':rotation', $rotation);
    $req->bindValue(':id', $id);
    return $req->execute();
}
	public function updateSize($id, $size) {
		$req = $this->db()->prepare("UPDATE map_objects SET size = :size WHERE id = :id");
		$req->bindValue(':size', $size);
		$req->bindValue(':id', $id);
		return $req->execute();
	}
}