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
                    <div class="draggable-container" style="position: absolute; width: 50px; height: 50px; top: 0; left: 0;">
                        <img src="<?= $object['image_url'] ?>" 
                             class="draggable" 
                             data-id="<?= $object['id'] ?>" 
                             style="width: 100%; height: 100%;">
                        <div class="position-text" style="position: absolute; top: 0; left: 0; width: 100%; text-align: center; color: white; background: rgba(0, 0, 0, 0.5); font-size: 10px;">
                            0, 0
                        </div>
                    </div>
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
        interact('.draggable-container').draggable({
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
			
			// Update the position text
            var positionText = target.querySelector('.position-text');
            positionText.textContent = Math.round(x) + ', ' + Math.round(y);
        }
		
        window.dragMoveListener = dragMoveListener;

        // Add click handler to load objects onto the map
        $('#object-list li').on('click', function() {
            const imageUrl = $(this).data('url');
            const objectId = $(this).data('id');

            // Create the image
            const newImage = $('<img>')
                .attr('src', imageUrl)
                .addClass('draggable')
                .attr('data-id', objectId)
                .css({
                    width: '100%',
                    height: '100%'
                });

            // Create the position text overlay
            const newPositionText = $('<div>')
                .addClass('position-text')
                .text('0, 0');

            // Append the image and text to the container
            newContainer.append(newImage).append(newPositionText);

            // Add the container to the map container
            $('#map-container').append(newContainer);

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