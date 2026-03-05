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
	public function updateDuplicateCount($id, $duplicateCount) {
        $req = $this->db()->prepare("UPDATE map_objects SET duplicate_count = :duplicateCount WHERE id = :id");
        $req->bindValue(':duplicateCount', $duplicateCount);
        $req->bindValue(':id', $id);
        return $req->execute();
    }

/**
 * Move object to bin (save state, then delete)
 * @param int $id
 * @return bool true on success, false on failure
 */
public function moveObjectToBin($id)
{
    $db = $this->db();

    // 1. Fetch the object
    $stmt = $db->prepare("SELECT * FROM map_objects WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $object = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$object) {
        error_log("Bin error: Object $id not found");
        return false;
    }

    // 2. Build state array – only keep essential fields
    $state = [
        'name'           => $object['name'],
        'image_url'      => $object['image_url'],
        'positionX'      => $object['positionX'],
        'positionY'      => $object['positionY'],
        'size'           => $object['size'],
        'duplicate_count'=> $object['duplicate_count'] ?? 1,
    ];
    $stateJson = json_encode($state);

    // 3. Insert into bin table
    $insert = $db->prepare("INSERT INTO map_objects_bin (original_object_id, object_state) VALUES (:oid, :state)");
    $insert->bindValue(':oid', $id, PDO::PARAM_INT);
    $insert->bindValue(':state', $stateJson);
    if (!$insert->execute()) {
        error_log("Bin insert error: " . print_r($insert->errorInfo(), true));
        return false;
    }

    // 4. Delete from map_objects
    $delete = $db->prepare("DELETE FROM map_objects WHERE id = :id");
    $delete->bindValue(':id', $id, PDO::PARAM_INT);
    if (!$delete->execute()) {
        error_log("Bin delete error: " . print_r($delete->errorInfo(), true));
        return false;
    }

    return true;
}

/**
 * Get all binned objects with restored state
 * @return array
 */
public function getBinnedObjects()
{
    $stmt = $this->db()->prepare("SELECT * FROM map_objects_bin ORDER BY id DESC");
    $stmt->execute();
    $bins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($bins as &$bin) {
        $bin['object_state'] = json_decode($bin['object_state'], true);
    }
    return $bins;
}
    
/**
 * Restore a binned object back to the map
 * @param int $binId The ID in map_objects_bin
 * @return array|false The restored map object (as from addObject), or false on failure
 */
public function restoreFromBin($binId)
{
    $db = $this->db();

    // Fetch bin entry
    $stmt = $db->prepare("SELECT * FROM map_objects_bin WHERE id = :id");
    $stmt->bindValue(':id', $binId, PDO::PARAM_INT);
    $stmt->execute();
    $bin = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$bin) return false;

    $state = json_decode($bin['object_state'], true);
    if (!$state) return false;

    // Step 1: create base object using addObject (inserts with position 0,0, size=1, rotation=0)
    $newObject = $this->addObject($state['name'], $state['image_url'], 0, 0);
    if (!$newObject) {
        error_log("Restore: addObject failed for " . $state['name']);
        return false;
    }

    $newId = $newObject['id'];

    // Step 2: update position, size, duplicate_count
    $update = $db->prepare("
        UPDATE map_objects 
        SET positionX = :px, positionY = :py, size = :size, duplicate_count = :dc 
        WHERE id = :id
    ");
    $update->bindValue(':px', $state['positionX']);
    $update->bindValue(':py', $state['positionY']);
    $update->bindValue(':size', $state['size'] ?? 1);
    $update->bindValue(':dc', $state['duplicate_count'] ?? 1);
    $update->bindValue(':id', $newId, PDO::PARAM_INT);
    $update->execute();

    // Step 3: delete bin entry
    $delete = $db->prepare("DELETE FROM map_objects_bin WHERE id = :id");
    $delete->bindValue(':id', $binId, PDO::PARAM_INT);
    $delete->execute();

    // Step 4: fetch final object
    $select = $db->prepare("SELECT * FROM map_objects WHERE id = :id");
    $select->bindValue(':id', $newId, PDO::PARAM_INT);
    $select->execute();
    $newObject = $select->fetch(PDO::FETCH_ASSOC);
    $newObject['statusEffects'] = []; // addObject sets it to NULL

    return $newObject;
}
/**
 * Permanently delete a bin entry
 * @param int $binId
 * @return bool
 */
public function deleteFromBin($binId)
{
    $stmt = $this->db()->prepare("DELETE FROM map_objects_bin WHERE id = :id");
    $stmt->bindValue(':id', $binId, PDO::PARAM_INT);
    return $stmt->execute();
}


    
}