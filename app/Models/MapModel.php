<?php

namespace app\Models;

use PDO;

class MapModel extends Model
{
    public function getMap()
    {
        $req = $this->db()->prepare("SELECT * FROM maps WHERE id = :id");
        $req->bindValue(':id', 1); // Assuming you have a map with id 1
        $req->execute();

        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public function getEnemies()
    {
        $req = $this->db()->prepare("SELECT * FROM enemies");
        $req->execute();

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}