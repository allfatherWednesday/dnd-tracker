<?php

namespace app\Models;

use PDO;
use app\Controllers\NotFoundException;
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
        $stmt = $this->db()->prepare("SELECT * FROM maps WHERE id = :id");
        $stmt->bindValue(':id', $mapId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        // Return null if no result, otherwise the array
        return $result ?: null;
    } catch (PDOException $e) {
        error_log("Error in getMapById: " . $e->getMessage());
        return null;
    }
}
	
	public function getAllMaps(): ?array
	{
        $req = $this->db()->prepare("SELECT * FROM maps ORDER BY id DESC");
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function addMap($name, $image, $grid_size){
		$DB1 = $this->db();
        $req1 = $DB1->prepare("INSERT INTO maps (name, image, grid_size) VALUES (:name, :image, :grid_size)");
        $req1->bindValue(':name', $name);
        $req1->bindValue(':image', $image);
        $req1->bindValue(':grid_size', $grid_size);
		$success = $req1->execute();    
		if (!$success) {
			error_log("Database error: " . print_r($req1->errorInfo(), true));
			return false;
		}
		
		// return the inserted row with id
		$id = $DB1->lastInsertId();
		$req2 = $DB1->prepare("SELECT * FROM maps WHERE id = :id");
		$req2->bindValue(':id', $id);
		$req2->execute();
		$result = $req2->fetch(PDO::FETCH_ASSOC);
		return $result;
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

    /**
     * Delete a map by ID
     * @param int $id
     * @return bool
     */
    public function deleteMap(int $id): bool
    {
    try {
        $stmt = $this->db()->prepare("DELETE FROM maps WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error deleting map: " . $e->getMessage());
        return false;
    }
    }

}
