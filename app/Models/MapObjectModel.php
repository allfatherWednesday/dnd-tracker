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

    public function addObject($name, $imageUrl)
    {
        $req = $this->db()->prepare("INSERT INTO map_objects (name, image_url, positionX, positionY) VALUES (:name, :image_url, 0, 0)");
        $req->bindValue(':name', $name);
        $req->bindValue(':image_url', $imageUrl);
        return $req->execute();
    }

    public function updatePosition($id, $x, $y) {
        $req = $this->db()->prepare("UPDATE map_objects SET positionX = :x, positionY = :y WHERE id = :id");
        $req->bindValue(':x', $x);
        $req->bindValue(':y', $y);
        $req->bindValue(':id', $id);
        return $req->execute();
    }
}