<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DnD Tracker - Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?=HOST?>/public/css/online-home.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a class="navbar-brand text-light fw-bold" href="<?= HOST ?>/">D&D Tracker</a>
        <div class="" id="navbarNav">
            <ul class="navbar-nav ms-auto d-flex flex-row">
                <li class="nav-item">
                    <a href="#" class="nav-link me-2 text-light" id="sidebarToggle" aria-label="Toggle navigation">
                        Sidebar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="<?= HOST ?>/logout?id=<?= $_SESSION['character']->getId() ?>">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="sidebar d-lg-block bg-light border-end p-1" id="sidebar">
    <div class="d-flex justify-content-end">
        <button id="closeSidebar" class="btn">
            <span>&times;</span>
        </button>
    </div>

    <a href="<?= HOST ?>/edit/abilities-and-modifiers" class="ability-scores">
        <div class="row flex-nowrap justify-content-center mb-2">
            <div class="col-4 col-str">
                <b class="mb-2">Str</b>
                <span class="text-center m-1 mb-2"><?= $data['abilities']['str'] ?? '10' ?></span>
                <span class="modifier"><?= $data['modifiers']['str'] ?? '0' ?></span>
            </div>
            <div class="col-4 col-dex">
                <b class="mb-2">Dex</b>
                <span class="text-center m-1 mb-2"><?= $data['abilities']['dex'] ?? '10' ?></span>
                <span class="modifier"><?= $data['modifiers']['dex'] ?? '0' ?></span>
            </div>
            <div class="col-4 col-con">
                <b class="mb-2">Con</b>
                <span class="text-center m-1 mb-2"><?= $data['abilities']['con'] ?? '10' ?></span>
                <span class="modifier"><?= $data['modifiers']['con'] ?? '0' ?></span>
            </div>
        </div>
        <div class="row flex-nowrap justify-content-center mb-2">
            <div class="col-4 col-int">
                <b class="mb-2">Int</b>
                <span class="text-center m-1 mb-2"><?= $data['abilities']['int'] ?? '10' ?></span>
                <span class="modifier"><?= $data['modifiers']['int'] ?? '0' ?></span>
            </div>
            <div class="col-4 col-wis">
                <b class="mb-2">Wis</b>
                <span class="text-center m-1 mb-2"><?= $data['abilities']['wis'] ?? '10' ?></span>
                <span class="modifier"><?= $data['modifiers']['wis'] ?? '0' ?></span>
            </div>
            <div class="col-4 col-cha">
                <b class="mb-2">Cha</b>
                <span class="text-center m-1 mb-2"><?= $data['abilities']['cha'] ?? '10' ?></span>
                <span class="modifier"><?= $data['modifiers']['cha'] ?? '0' ?></span>
            </div>
        </div>
    </a>


    <!---->
    <div class="accordion" id="my-data">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed text-light bg-opacity-75" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_0" aria-controls="collapse_0">
                    About
                </button>
            </h2>
            <div id="collapse_0" class="accordion-collapse collapse" data-bs-parent="#my-data">
                <div class="accordion-body">
                    <div class="float-end">
                        <a href="<?= HOST ?>/edit/about" class="text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="d-block">
                        <b>Player ID:</b> <?= $data['my_character']->getId() ?> <br>
                        <b>Armor Class:</b> <?= $data['about']['armor'] ?? '' ?> <br>
                        <b>Speed:</b> <?= $data['about']['speed'] ?? '' ?> <br>
                        <b>Class:</b>
                        <?php
                            if (isset($data['about']['char_class']) && is_array($data['about']['char_class'])) {
                                //echo '<br>';
                                foreach ($data['about']['char_class'] as $charClass) {
                                    echo '<br>- ' . $charClass['name'] . ' (Lvl ' . $charClass['lvl'] . ')';
                                }
                            } else {
                                echo $data['about']['char_class'] ?? '';
                            }

                        ?>
                        <br>
                        <b>Race:</b> <?= $data['about']['char_race'] ?? '' ?> <br>
                        <b>Exp:</b> <?= $data['about']['char_exp'] ?? '' ?> <br>
                        <!-- Background -->
                        <?php if (isset($data['about']['char_bg']) && $data['about']['char_bg'] !== '') { ?>
                        <b>Background</b> <br>
                        <?= $data['about']['char_bg'] ?? '' ?> <br>
                        <?php } ?>
                        <!-- Appearance -->
                        <?php if (isset($data['about']['char_appearance']) && $data['about']['char_appearance'] !== '') { ?>
                            <b>Apearance</b> <br>
                            <?= $data['about']['char_appearance'] ?? '' ?> <br>
                        <?php } ?>
                        <!-- Backstory -->
                        <?php if (isset($data['about']['char_backstory']) && $data['about']['char_backstory'] !== '') { ?>
                            <b>Backstory</b> <br>
                            <?= $data['about']['char_backstory'] ?? '' ?> <br>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed text-light bg-opacity-75 rounded-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_1" aria-controls="collapse_1">
                    Spells
                </button>
            </h2>
            <div id="collapse_1" class="accordion-collapse collapse" data-bs-parent="#my-data">
                <div class="accordion-body">
                    <div class="float-end mb-2">
                        <a href="<?= HOST ?>/edit/spells" class="text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="d-block">

                        <div class="accordion spells" id="spells">
                            <?php foreach ($data['spells_by_level'] as $levelName => $spells) { ?>
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button collapsed text-light bg-opacity-75 rounded-0"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#<?= $this->stringify($levelName) ?>"
                                                aria-controls="<?= $this->stringify($levelName) ?>">
                                            <?= $levelName ?>
                                        </button>
                                    </h3>
                                    <div id="<?= $this->stringify($levelName) ?>" class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <div class="d-block">
                                                <?php foreach ($spells as $spell) { ?>
                                                    - <span><?= $spell['name'] ?></span> <br>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed text-light bg-opacity-75" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_2" aria-controls="collapse_2">
                    Skills
                </button>
            </h2>
            <div id="collapse_2" class="accordion-collapse collapse" data-bs-parent="#my-data">
                <div class="accordion-body">
                    <div class="float-end">
                        <a href="<?= HOST ?>/edit/skills" class="text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="d-block">
                        <?php foreach ($data['skills'] as $skill) { ?>
                            - <a href="<?= $skill['link'] ?>" target="_blank"><?= $skill['name'] ?></a> <br>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed text-light bg-opacity-75" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_3" aria-controls="collapse_3">
                    Inventory
                </button>
            </h2>
            <div id="collapse_3" class="accordion-collapse collapse" data-bs-parent="#my-data">
                <div class="accordion-body">
                    <div class="float-end">
                        <a href="<?= HOST ?>/edit/inventory" class="text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="d-block">
                        <?php
                        if (!empty($data['inventory']) && !empty($data['inventory']['items'])) {  ?>
                            <?= nl2br($data['inventory']['items']) ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed text-light bg-opacity-75 rounded-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_4" aria-controls="collapse_4">
                    Other
                </button>
            </h2>
            <div id="collapse_4" class="accordion-collapse collapse" data-bs-parent="#my-data">
                <div class="accordion-body">
                    <div class="float-end">
                        <a href="<?= HOST ?>/edit/other" class="text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="d-block">
                        <?= nl2br($data['my_character']->getData()) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!---->



</div>

<div class="content content-home text-center">
    <div class="title px-5">
        <h2><?= $data['my_character']->getName() ?></h2>
        Max HP: <?= $data['hpData']['maxHealth'] ?>
        <?php $curHealth = $data['hpData']['curHealth']; ?>
        <a href="<?= HOST ?>/edit/hp" class="text-decoration-none">
            <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-striped bg-<?= $data['hpData']['currentColor'] ?> progress-bar-animated"
                     style="width: <?= $data['hpData']['percent'] ?>%"><?= $data['hpData']['curHealth'] ?></div>
            </div>
        </a>
    </div>
    <div class="container contains-image position-relative">
        <img src="./uploads/<?= $data['my_character']->getImage() ?>" class="character_image">
        <img src="./public/images/armor.png" class="btn btn-outline-secondary equipment-btn" data-bs-toggle="modal" data-bs-target="#equipmetModal">
    </div>

    <!-- Modal equipment -->
    <div class="modal fade" id="equipmetModal" tabindex="-1" aria-labelledby="equipmetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="equipmetModalLabel">Equipment
                        <a href="<?= HOST ?>/edit/inventory" class="text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                            </svg>
                        </a>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="text-start">
                        <?php
                        if (!empty($data['inventory']) && !empty($data['inventory']['equipment'])) {
                            foreach ($data['inventory']['equipment'] as $slot => $equipment) { ?>
                                <li>
                                    [<?= ucfirst($slot) ?>]
                                    <b><?= $equipment['name'] ?></b>:
                                    <?= $equipment['description'] ?>
                                </li>
                            <?php }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal equipment -->

    <div class="turns px-5">
        <div class="row d-flex flex-nowrap">
            <?php foreach (array_reverse($data['character_list']) as $character) { ?>
                <div class="col turn-<?= $character->getId() ?>">
                    <span>(<?= $character->getInitiative() ?>)</span>
                    <img src="./uploads/<?= $character->getImage() ?>"/>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('#sidebarToggle').click(function() {
            $('#sidebar').toggle();
            $('.content').toggleClass('ml-0');
        });

        $('#closeSidebar').click(function() {
            $('#sidebar').hide();
            $('.content').addClass('ml-0');
        });
    });
</script>

</body>
</html>