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


    /**
 * Move a map to the bin
 * @param int $id
 * @return bool
 */
public function moveMapToBin($id)
{
    $db = $this->db();
    // 1. Fetch the map
    $stmt = $db->prepare("SELECT * FROM maps WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $map = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$map) return false;

    // 2. Insert into maps_bin (only name and image, ignore original_map_id but we store it)
    $insert = $db->prepare("INSERT INTO maps_bin (original_map_id, name, image) VALUES (:oid, :name, :image)");
    $insert->bindValue(':oid', $id, PDO::PARAM_INT);
    $insert->bindValue(':name', $map['name']);
    $insert->bindValue(':image', $map['image']);
    if (!$insert->execute()) return false;

    // 3. Delete from maps
    $delete = $db->prepare("DELETE FROM maps WHERE id = :id");
    $delete->bindValue(':id', $id, PDO::PARAM_INT);
    return $delete->execute();
}

/**
 * Get all binned maps
 * @return array
 */
public function getBinnedMaps()
{
    $stmt = $this->db()->prepare("SELECT * FROM maps_bin ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Restore a binned map back to the maps table
 * @param int $binId
 * @param int $defaultGridSize Default grid size to assign (e.g., 40)
 * @return array|false Restored map record, or false on failure
 */
public function restoreMapFromBin($binId, $defaultGridSize = 40)
{
    $db = $this->db();
    // Fetch bin entry
    $stmt = $db->prepare("SELECT * FROM maps_bin WHERE id = :id");
    $stmt->bindValue(':id', $binId, PDO::PARAM_INT);
    $stmt->execute();
    $bin = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$bin) return false;

    // Re‑insert into maps table with default grid size
    $insert = $db->prepare("INSERT INTO maps (name, image, grid_size) VALUES (:name, :image, :grid_size)");
    $insert->bindValue(':name', $bin['name']);
    $insert->bindValue(':image', $bin['image']);
    $insert->bindValue(':grid_size', $defaultGridSize, PDO::PARAM_INT);
    if (!$insert->execute()) return false;

    $newId = $db->lastInsertId();

    // Delete the bin entry
    $delete = $db->prepare("DELETE FROM maps_bin WHERE id = :id");
    $delete->bindValue(':id', $binId, PDO::PARAM_INT);
    $delete->execute();

    // Return the restored map
    $select = $db->prepare("SELECT * FROM maps WHERE id = :id");
    $select->bindValue(':id', $newId, PDO::PARAM_INT);
    $select->execute();
    return $select->fetch(PDO::FETCH_ASSOC);
}

/**
 * Permanently delete a bin entry
 * @param int $binId
 * @return bool
 */
public function deleteMapFromBin($binId)
{
    $stmt = $this->db()->prepare("DELETE FROM maps_bin WHERE id = :id");
    $stmt->bindValue(':id', $binId, PDO::PARAM_INT);
    return $stmt->execute();
}

}
