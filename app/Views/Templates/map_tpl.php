<?php include_once 'partials/editor_top_tpl.php'; ?>

<link rel="stylesheet" href="<?= HOST ?>/public/css/map.css">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <h3>Map Objects</h3>
            <form action="<?= HOST ?>/add-object" method="post" class="mb-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Object Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="image_url" class="form-label">Image URL</label>
                    <input type="url" class="form-control" id="image_url" name="image_url" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Object</button>
            </form>

            <h4>Loaded Objects</h4>
            <ul id="object-list" class="list-group">
                <?php
                $mapObjectController = new \app\Controllers\MapObjectController();
                $objects = $mapObjectController->getObjects();
                foreach ($objects as $object): ?>
                    <li class="list-group-item" data-id="<?= $object['id'] ?>" data-url="<?= $object['image_url'] ?>">
                        <?= $object['name'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Map Container -->
        <div class="col-md-9">
            <div id="map-container" style="position: relative; width: 100%; height: 600px; border: 1px solid #000; margin-left: 350px;">        
				<img id="map-image" src="<?= $data['map']['image'] ?>" style="width: 100%; height: 100%;">
                
                <!-- Draggable Objects -->
                <?php foreach ($objects as $object): ?>
                    <img src="<?= $object['image_url'] ?>" 
                         class="draggable" 
                         data-id="<?= $object['id'] ?>" 
                         style="position: absolute; width: 50px; height: 50px;">
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script>
    $(document).ready(function() {
        // Make objects draggable
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

        // Add click handler to load objects onto the map
        $('#object-list li').on('click', function() {
            const imageUrl = $(this).data('url');
            const objectId = $(this).data('id');

            // Create a new draggable image
            const newImage = $('<img>')
                .attr('src', imageUrl)
                .addClass('draggable')
                .attr('data-id', objectId)
                .css({
                    position: 'absolute',
                    width: '50px',
                    height: '50px',
                    top: '50%',
                    left: '50%'
                });

            // Add the image to the map container
            $('#map-container').append(newImage);

            // Make the new image draggable
            interact(newImage[0]).draggable({
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
        });
    });
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>