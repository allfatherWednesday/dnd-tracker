<?php

namespace app\Controllers;

use app\Models\CharacterModel;

class HomeController extends Controller
{
    public function home()
    {
        $homeTpl = 'offline-home';
        if (isset($_SESSION['character'])) {
            $homeTpl = 'online-home';
        } else if (CONF['allow_login']['conf_value'] === '1') {
            $this->redirect('login');
        }

        // Get players list (ordered by initiative)
        $characterModel = new CharacterModel();
        $characterList = $characterModel->getAll('initiative');
        // Get current turn
        $turn = $this->getCurrentTurn();

        // Get my character image and description
        $myCharacter = $abilities = $modifiers = $about = $spellsByLevel = $skills = $inventory = [];
        if (isset($_SESSION['character'])) {
            $myCharacter = $_SESSION['character'];

            // HP
            $curHealth = $myCharacter->getCurHealth();
            $maxHealth = $myCharacter->getMaxHealth();
            $possibleColors = ['success', 'warning', 'danger'];
            $percent = $curHealth/$maxHealth *100;
            $currentColor = $possibleColors[0];
            if ($percent < 50 && $percent > 10) {
                $currentColor = $possibleColors[1];
            } else if ($percent <= 10) {
                $currentColor = $possibleColors[2];
            }
            $hpData = [
                'curHealth' => $curHealth,
                'maxHealth' => $maxHealth,
                'percent' => $percent,
                'currentColor' => $currentColor,
            ];

            // Abilities and modifiers
            $abilities = (array_key_exists('abilities', $myCharacter->getCharModifiers())) ? $myCharacter->getCharModifiers()['abilities'] : [];
            $modifiersMap = [
                1=>'-5', 2=>'-4', 3=> '-4', 4 => '-3',5=> '-3',6=>'-2',7=> '-2',8=> '-1',9=> '-1',10=> '0',
                11=> '0',12=> '+1',13=> '+1',14=> '+2',15=> '+2',16=> '+3',17=> '+3',18=> '+4',19=> '+4',20=>'+5'
            ];
            foreach ($abilities as $key => $ability) {
                $modifiers[$key] = $modifiersMap[$ability];
            }

            // About
            $about = (array_key_exists('about', $myCharacter->getCharModifiers())) ? $myCharacter->getCharModifiers()['about'] : [];
            // Spells
            $spellsList = (array_key_exists('spells', $myCharacter->getCharModifiers())) ? $myCharacter->getCharModifiers()['spells'] : [];
            $spellsByLevel = [];
            foreach ($spellsList as $spellId) {
                $thisSpell = $characterModel->getSpellById((int) $spellId);
                $level = ($thisSpell['level'] === 0) ? 'Cantrips' : 'Level '.$thisSpell['level'];
                $spellsByLevel[$level][] = $thisSpell;
            }
            // Skills
            $skills = (array_key_exists('skills', $myCharacter->getCharModifiers())) ? $myCharacter->getCharModifiers()['skills'] : [];

            // Inventory
            $inventory = $characterModel->getInventoryFromCharacter($_SESSION['character']->getId());
        }

        $this->view->load($homeTpl, [
            'character_list' => $characterList,
            'turn' => $turn,
            'my_character' => $myCharacter,
            'hpData' => $hpData,
            'abilities' => $abilities,
            'modifiers' => $modifiers,
            'about' => $about,
            'spells_by_level' => $spellsByLevel,
            'skills' => $skills,
            'inventory' => $inventory,
        ]);

        //-Aside fields:

        //-Stats
        // Ability scores + modifiers(str,dex,con,int,wis,cha),

        // About:(class,race,bg,alignment), appearance, backstory,
        // Spells: (cantrip, lvl1, lvl2),

        // skills,
        // features (by class) + feats
        // "other proficiencies & languages",

        //-Backpack:
        // inventory,

        //-Sword
        // equipment+armor,

    }

    public function getCurrentTurn()
    {
        $characterModel = new CharacterModel();
        return $characterModel->getInitiativeList();
    }

}