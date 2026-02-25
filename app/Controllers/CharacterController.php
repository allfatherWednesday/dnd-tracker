<?php

namespace app\Controllers;

use app\Models\CharacterModel;
use app\Models\OnlineModel;

class CharacterController extends Controller
{
    private $model;
    public function __construct()
    {
        parent::__construct();
        $characterModel = new CharacterModel();
        $this->model = $characterModel;
    }

    private array $subRoutes = [
        'hp' => 'editHP',
        'abilities-and-modifiers' => 'editAbilitiesModifiers',
        'about' => 'editAbout',
        'spells' => 'editSpells',
        'skills' => 'editSkills',
        'inventory' => 'editInventory',
        'other' => 'editOther',
    ];
    public function login($data)
    {
        // 1. find all characters and serve them
        $characters = $this->model->getAll();

        // 2. if theres a request, create session
        $onlineModel = new OnlineModel();
        if (isset($_GET['character']) &&
            array_key_exists($_GET['character'], $characters)
            //&& !$onlineModel->checkIfOnline($_GET['character'])
        )
        {
            $character = $characters[$_GET['character']];
            $_SESSION['character'] = $character;
            // set online
            $onlineModel->setOnline($character->getId(), $character->getName());
            // TODO: log
            $this->redirect('');
        }

        $data = ['characters' => $characters];
        $this->view->load('login', $data);
    }

    public function logout()
    {
        session_destroy();
        // set offline
        if (isset($_SESSION['character'])) {
            $onlineModel = new OnlineModel();
            $onlineModel->setOffline($_SESSION['character']->getId());
            unset($_SESSION);
        }
        if (isset($_GET['id'])) {
            $onlineModel = new OnlineModel();
            $onlineModel->setOffline($_GET['id']);
        }

        // TODO: log
        $this->redirect('');
    }

    public function edit($parameters) {
        if (isset($_SESSION['admin']) && $this->model->userIdExists($parameters[0])) {
            // edit/{user_id}/{subroute}/{params}
            // TODO: admin_log
            var_dump($parameters);
        } else if (isset($_SESSION['character']) && array_key_exists($parameters[0], $this->subRoutes)) {
            // edit/{subroute}/{params}
            // TODO: admin_log: (here or in every submethod?)
            $subroute = $parameters[0];
            unset($parameters[0]);
            $parameters = array_values($parameters);
            $this->{$this->subRoutes[$subroute]}($parameters);
        } else if (!isset($_SESSION['character'])) {
            $this->redirect('');
        } else {
            $this->redirect('404');
        }
    }

    public function setCharModifiers(string $key, array $content)
    {
        $myMods = $_SESSION['character']->getCharModifiers();
        $myMods[$key] = $content;

        $_SESSION['character']->setCharModifiers($myMods);
        $this->model->setCharModifiers($_SESSION['character']->getId(), json_encode($myMods));
    }

    public function editHP()
    {
        $current = $_SESSION['character']->getCurHealth();
        $max = $_SESSION['character']->getMaxHealth();

        if (isset($_POST['hp'])) {
            $current = $_POST['hp']['current'];
            $max = $_POST['hp']['max'];

            if ($this->model->editHP($_SESSION['character']->getId(), $current, $max))
            {
                $_SESSION['character']->setHP($current, $max);
            }
            // TODO log
            // TODO: return success message
        }

        $possibleColors = ['success', 'warning', 'danger'];
        $percent = $current/$max *100;
        $currentColor = $possibleColors[0];
        if ($percent < 50 && $percent > 10) {
            $currentColor = $possibleColors[1];
        } else if ($percent <= 10) {
            $currentColor = $possibleColors[2];
        }

        $this->view->load('edit_hp', [
            'current' => $current,
            'max' => $max,
            'percent' => $percent,
            'currentColor' => $currentColor,
        ]);
    }

    public function editAbilitiesModifiers()
    {
        if (isset($_POST['ability'])) {
            $ability = $_POST['ability'];
            $this->setCharModifiers('abilities', $ability);
            // TODO log
            // TODO: return success message
        }

        $abilities = $modifiers = [];
        if (isset($_SESSION['character']) && array_key_exists('abilities', $_SESSION['character']->getCharModifiers())) {
            $abilities = $_SESSION['character']->getCharModifiers()['abilities'];
            $modifiersMap = [
                1=>'-5', 2=>'-4', 3=> '-4', 4 => '-3',5=> '-3',6=>'-2',7=> '-2',8=> '-1',9=> '-1',10=> '0',
                11=> '0',12=> '+1',13=> '+1',14=> '+2',15=> '+2',16=> '+3',17=> '+3',18=> '+4',19=> '+4',20=>'+5',21=>'+5',22=>'+6'
            ];

            foreach ($abilities as $key => $ability) {
                $modifiers[$key] = $modifiersMap[$ability];
            }
        }

        $this->view->load('edit_abilities-and-modifiers', [
            'abilities' => $abilities,
            'modifiers' => $modifiers
        ]);
    }

    public function editAbout()
    {
        $clases = $this->model->getApiData('classes')['results'];
        if (isset($_POST['about'])) {
            $classByIndex = [];
            foreach ($clases as $clase) {
                $classByIndex[$clase['index']] = $clase;
            }
            $ability = $_POST['about'];
            if (!array_key_exists('char_class', $ability)) {
                $ability['char_class'] = [];
            }
            foreach ($ability['char_class'] as $key => $charClass) {
                if ($charClass['index'] === '') {
                    unset($ability['char_class'][$key]);
                } else {
                    $ability['char_class'][$key]['name'] = $classByIndex[$charClass['index']]['name'];
                }
            }
            $ability['char_class'] = array_values($ability['char_class']);
            $this->setCharModifiers('about', $ability);

            $initiative = $ability['initiative'] ? $ability['initiative'] : 0;
            $_SESSION['character']->setInitiative($initiative);
            $this->model->setInitiative($_SESSION['character']->getId(), $initiative);
            // TODO: log
            // TODO: return success message
        }

        $about = [];
        if (isset($_SESSION['character']) && array_key_exists('about', $_SESSION['character']->getCharModifiers())) {
            $about = $_SESSION['character']->getCharModifiers()['about'];
        }
        if (!array_key_exists('char_class', $about) || !is_array($about['char_class'])) $about['char_class'] = []; // temp

        $this->view->load('edit_about', [
            'classes' => $clases,
            'about' => $about
        ]);
    }

    public function editSpells()
    {
        $spellsIds = [];
        if (isset($_SESSION['character']) && array_key_exists('spells', $_SESSION['character']->getCharModifiers())) {
            $spellsIds = $_SESSION['character']->getCharModifiers()['spells'];
            sort($spellsIds);
        }
        if (isset($_POST['spell'])) {
            $spellId = $_POST['spell']['find'];
            if ('' !== $spellId && !in_array($spellId, $spellsIds)) {
                $spellsIds[] = $spellId;
            }

            ///////////// temp fix
            $tempSpellsArr = [];
            foreach ($spellsIds as $id) {
                if ('' !== $id && !in_array($id, $tempSpellsArr)) { // get rid of doubles and empty values
                    $tempSpellsArr[] = $id;
                }
            }
            $spellsIds = $tempSpellsArr;
            ///////////// end temp fix

            $this->setCharModifiers('spells', $spellsIds);
            // TODO log
            // TODO: return success message
        }

        $spellsByLevel = [];
        foreach ($spellsIds as $spellId) {
            $thisSpell = $this->model->getSpellById((int) $spellId);
            $level = ($thisSpell['level'] === 0) ? 'Cantrips' : 'Level '.$thisSpell['level'];
            $spellsByLevel[$level][] = $thisSpell;
        }


        $this->view->load('edit_spells', [
            'spells_by_level' => $spellsByLevel
        ]);
    }

    public function editSkills()
    {
        $skills = [];
        if (isset($_SESSION['character']) && array_key_exists('skills', $_SESSION['character']->getCharModifiers())) {
            $skills = $_SESSION['character']->getCharModifiers()['skills'];
            //sort($spellsIds);
        }
        if (isset($_POST['skill'])) {
            $skill = [
                'name' => $_POST['skill']['name'],
                'link' => $_POST['skill']['link']
            ];
            $skills[] = $skill;
            $this->setCharModifiers('skills', $skills);
            // TODO log
            // TODO: return success message
        }

        $this->view->load('edit_skills', [
            'skills' => $skills
        ]);
    }

    public function editInventory()
    {
        // Equipment
        $equipmentSlots = [
            'head', 'face', 'earrings', 'neck', 'armor', 'back', 'left-hand', 'right-hand',
            'hands', 'ring-1', 'ring-2', 'ring-3', 'wrist-1', 'wrist-2', 'feet'];

        $inventory = $this->model->getInventoryFromCharacter($_SESSION['character']->getId());
        $usedSlots = $inventory['equipment'] ?? [];
        if (isset($_POST['inventory'])) {
            $inventory = $_POST['inventory'];
            if ($inventory['equipment']['name'] !== '') {
                $usedSlots[$inventory['equipment']['type']] = [
                    'name' => $inventory['equipment']['name'],
                    'description' => $inventory['equipment']['description'],
                ];
                unset($equipmentSlots[array_search($inventory['equipment']['type'], $equipmentSlots)]);
            }
            $inventory['equipment'] = $usedSlots;
            $this->model->setCharInventory($_SESSION['character']->getId(), json_encode($inventory));
        }

        if (isset($_POST['unset'])) {
            foreach ($_POST['unset'] as $item) {
                unset($inventory['equipment'][$item]);
            }
            $this->model->setCharInventory($_SESSION['character']->getId(), json_encode($inventory));
        }

        $this->view->load('edit_inventory', [
            'inventory' => $inventory,
            'equipment_slots' => $equipmentSlots
        ]);
    }

    public function editOther()
    {
        // Get data
        $data = [];
        if (isset($_SESSION['character'])) {
            $data = $_SESSION['character']->getData();
        }
        if (isset($_POST['data'])) {
            $data = $_POST['data'];
            $_SESSION['character']->setData($data);
            $this->model->setCharData($_SESSION['character']->getId(), $data);
            // TODO log
            // TODO: return success message
        }

        $this->view->load('edit_other', [
            'data' => $data
        ]);
    }
}
