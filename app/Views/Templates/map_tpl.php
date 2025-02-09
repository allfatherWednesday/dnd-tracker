<?php include_once 'partials/editor_top_tpl.php'; ?>

<link rel="stylesheet" href="<?= HOST ?>/public/css/map.css">

<div class="content container text-center">
    <div class="float-left d-flex">
        <a href="<?= HOST ?>/" class="btn btn-success"> << Return</a>
    </div>
    <div class="title px-5 mb-4">
        <h2>Map</h2>
    </div>
    <div id="map-container">
        <img id="map-image" src="./uploads/<?= $data['map']['image'] ?>">
        
        <?php foreach ($data['characters'] as $character): ?>
            <img src="./uploads/<?= $character->getImage() ?>" 
                 class="draggable" 
                 data-id="<?= $character->getId() ?>" 
                 style="position: absolute; width: 50px; height: 50px;">
        <?php endforeach; ?>

        <?php foreach ($data['enemies'] as $enemy): ?>
            <img src="./uploads/<?= $enemy['image'] ?>" 
                 class="draggable" 
                 data-id="<?= $enemy['id'] ?>" 
                 style="position: absolute; width: 50px; height: 50px;">
        <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script>
    $(document).ready(function() {
        interact('.draggable').draggable({
            inertia: true,
            modifiers: [
                interact.modifiers.restrictRect({
                    restriction: 'parent',
                    endOnly: true
                })
            ],
            autoScroll: true,
            listeners: {
                move: dragMoveListener
            }
        });

        function dragMoveListener(event) {
            var target = event.target;
            var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
            var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

            target.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
        }

        window.dragMoveListener = dragMoveListener;
    });
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>