<?php

namespace app\Models;

use PDO;

class MapModel extends Model
{
    /*public function getMap()
    {
        $req = $this->db()->prepare("SELECT * FROM maps WHERE id = :id");
        $req->bindValue(':id', 1); // Assuming you have a map with id 1
        $req->execute();

        return $req->fetch(PDO::FETCH_ASSOC);
    }
	
	public function updateGridSize($mapId, $gridSize) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE maps SET grid_size = ? WHERE id = ?");
        $stmt->bind_param("ii", $gridSize, $mapId);
        $stmt->execute();
        $stmt->close();
    }*/
	
	
    public function getMapById(int $mapId): ?array
    {
        try {
            $stmt = $this->db()->prepare("
                SELECT * 
                FROM maps 
                WHERE id = :id
            ");
            
            $stmt->bindValue(':id', $mapId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Log error here if needed
            throw $e;
        }
    }
	
	public function getAllMaps(): ?array
	{
        $req = $this->db()->prepare("SELECT * FROM maps ORDER BY id DESC");
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
	}
	
    public function updateGridSize(int $mapId, int $gridSize): bool
    {
        try {
            $stmt = $this->db()->prepare("
                UPDATE maps 
                SET grid_size = :gridSize 
                WHERE id = :mapId
            ");
            
            $stmt->bindValue(':gridSize', $gridSize, PDO::PARAM_INT);
            $stmt->bindValue(':mapId', $mapId, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            // Log error here if needed
            throw $e;
        }
    }

}
