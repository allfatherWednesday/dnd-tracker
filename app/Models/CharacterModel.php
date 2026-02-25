<?php

namespace app\Models;

use app\Models\Entities\Character;
use Exception;
use PDO;

class CharacterModel extends Model
{
    private $defaultImage = 'image';

    public function getAll($order = 'name')
    {
        $wherePlayers = '';
        if ($order === 'name') $wherePlayers = ' WHERE type = "player"';
        if ($order === 'initiative') $wherePlayers = ' WHERE initiative <> 0';
        $req = $this->db()->prepare("
            SELECT * FROM characters $wherePlayers
            ORDER BY $order
        ");
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $characters = [];
        foreach ($results as $result) {
            $characters[$result['id']] = new Character(
                $result['id'],
                $result['name'],
                $result['image'] ?? $this->defaultImage,
                $result['data'],
                $result['max_health'],
                $result['cur_health'],
                $result['char_modifiers'],
                $result['initiative'],
                $result['role'],
                $result['type'],
                $result['owner'],
            );
        }

        return $characters;
    }

    public function getInitiativeList()
    {
        $req = $this->db()->prepare("
            SELECT id, initiative, name, image FROM characters
            ORDER BY initiative DESC
        ");
        $req->execute();
        return $req->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

    public function userIdExists(int $id)
    {
        $req = $this->db()->prepare("
            SELECT COUNT(*) FROM characters
            WHERE id = :id
        ");
        $req->bindValue(':id', $id);
        $req->execute();

        return (bool)$req->fetch(PDO::FETCH_ASSOC)['COUNT'];
    }

    /**
     * @param int $id user_id
     * @param string $charModifiers is an array transformed to json
     * @return mixed
     */
    public function setCharModifiers(int $id, string $charModifiers)
    {
        $req = $this->db()->prepare("
            UPDATE characters
            set char_modifiers = :char_modifiers
            WHERE id = :id
        ");
        $req->bindValue(':id', $id);
        $req->bindValue(':char_modifiers', $charModifiers);
        return $req->execute();
    }

    public function setInitiative(int $id, int $initiative)
    {
        $req = $this->db()->prepare("
            UPDATE characters
            set initiative = :initiative
            WHERE id = :id
        ");
        $req->bindValue(':id', $id);
        $req->bindValue(':initiative', $initiative);
        return $req->execute();
    }

    public function getApiData(string $key)
    {
        $url = 'https://www.dnd5eapi.co/api/' . $key;
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            // TODO: log admin
            throw new Exception("Error to get to the API: " . error_get_last()['message']);
        }

        return json_decode($response, true);
    }

    public function findSpellByName($query)
    {
        $req = $this->db()->prepare("
            SELECT id, name, details, level FROM spells
            WHERE name LIKE :query
        ");
        $req->bindValue(':query', '%' . $query . '%');
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSpellById(int $id)
    {
        $columns = (isset(CONF['spell_columns']['conf_value'])) ? ', ' . CONF['spell_columns']['conf_value'] : '';
        $req = $this->db()->prepare("
            SELECT id, name $columns FROM spells
            WHERE id = :id
        ");
        $req->bindValue(':id', $id);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public function setCharData(int $id, string $data)
    {
        $req = $this->db()->prepare("
            UPDATE characters
            set data = :data
            WHERE id = :id
        ");
        $req->bindValue(':id', $id);
        $req->bindValue(':data', $data);
        return $req->execute();
    }

    public function editHP(int $id, int $currentHP, int $maxHP)
    {
        $req = $this->db()->prepare("
            UPDATE characters
            set cur_health = :cur_health, max_health = :max_health
            WHERE id = :id
        ");
        $req->bindValue(':id', $id);
        $req->bindValue(':cur_health', $currentHP);
        $req->bindValue(':max_health', $maxHP);
        return $req->execute();
    }

    public function getInventoryFromCharacter(int $characterId)
    {
        $req = $this->db()->prepare("
            SELECT inventory FROM characters
            WHERE id = :id
        ");
        $req->bindValue(':id', $characterId);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC)['inventory'] ?? '';
        return json_decode($result, true);
    }

    public function setCharInventory(int $id, string $inventory)
    {
        $req = $this->db()->prepare("
            UPDATE characters
            set inventory = :inventory
            WHERE id = :id
        ");
        $req->bindValue(':id', $id);
        $req->bindValue(':inventory', $inventory);
        return $req->execute();
    }

}