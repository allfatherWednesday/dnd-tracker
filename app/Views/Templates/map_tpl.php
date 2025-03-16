<?php include_once 'partials/editor_top_tpl.php'; ?>

<link rel="stylesheet" href="<?= HOST ?>/public/css/maps.css">

<?php $data['grid-size']=37?>

<div class="container-fluid" style="padding-top: 4px;">
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
        <div class="col-md-8 col-lg-8" style="padding: 0">
            <div id="map-container">        
				<img id="map-image" src="<?= $data['map']['image'] ?>" >
                <!-- Grid Overlay Added Here -->
                <div class="grid-overlay" style="background-size: <?= $data['grid-size'] ?>px <?= $data['grid-size'] ?>px;"></div>
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
	
		
		<!-- Right Sidebar -->
        <div class="col-md-2 col-md-2 sidebar_right sidebar_custom">
				<!-- List of Characters -->
				<div class="sidebar-section mb-4">
					<h3>List of Chars</h3>
					<ul class="list-group">
						<?php /* Add PHP loop for characters here */ ?>
						<li class="list-group-item">Character 1</li>
						<li class="list-group-item">Character 2</li>
					</ul>
				</div>

				<!-- List of Enemies -->
				<div class="sidebar-section mb-4">
					<h3>List of Enemies</h3>
					<ul class="list-group">
						<?php /* Add PHP loop for enemies here */ ?>
						<li class="list-group-item">Enemy 1</li>
						<li class="list-group-item">Enemy 2</li>
					</ul>
				</div>

				<!-- List of Statuses -->
				<div class="sidebar-section">
					<h3>List of Statuses</h3>
					<ul class="list-group">
						<?php /* Add PHP loop for statuses here */ ?>
						<li class="list-group-item">Status 1</li>
						<li class="list-group-item">Status 2</li>
					</ul>
			</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script>
    $(document).ready(function() {
		// Get the map container's offset relative to the page
        const mapContainer = document.getElementById('map-container');
        const mapRect = mapContainer.getBoundingClientRect();
        const mapOffset = {
            left: mapRect.left,
            top: mapRect.top
        };
		
        // Make objects draggable
        interact('.draggable-container').draggable({
            inertia: true,
            modifiers: [
                interact.modifiers.restrictRect({
                    restriction: 'parent',
                    endOnly: true
                }),
				interact.modifiers.snap({
					targets: [
						interact.createSnapGrid({ 
							x: 50, 
							y: 50,
							offset: {
                                x: mapOffset.left,
                                y: mapOffset.top
                            }	
						})
					],
					range: Infinity,
					relativePoints: [ { x: 0, y: 0 } ]
				})
            ],
            autoScroll: true,
            listeners: {
                move: dragMoveListener
            }
        };

        // Drag move handler
        function handleDragMove(event) {
            const target = event.target;
            const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
            const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

            target.style.transform = `translate(${x}px, ${y}px)`;
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
                    }),
                    interact.modifiers.snap({
                        targets: [
                            interact.createSnapGrid({
                                x: 50,
                                y: 50,
                                offset: {
                                    x: mapOffset.left,
                                    y: mapOffset.top
                                }
                            })
                        ],
                        range: Infinity,
                        relativePoints: [{ x: 0, y: 0 }]
                    })
                ],
                autoScroll: true,
                listeners: { move: handleDragMove }
            });
        }

        // Initialize existing elements
        document.querySelectorAll('.draggable-container').forEach(setupDraggable);

        // Add new object handler
        $('#object-list li').on('click', function() {
            const imageUrl = $(this).data('url');
            const objectId = $(this).data('id');

            // Create new container
            const newContainer = $('<div>')
                .addClass('draggable-container')
                .css({
                    position: 'absolute',
                    width: '50px',
                    height: '50px',
                    top: '0',
                    left: '0'
                });

            // Create image and text elements
            newContainer.append(
                $('<img>')
                    .attr('src', imageUrl)
                    .addClass('draggable')
                    .data('id', objectId)
                    .css({ width: '100%', height: '100%' }),
                $('<div>')
                    .addClass('position-text')
                    .css({
                        position: 'absolute',
                        top: '0',
                        left: '0',
                        width: '100%',
                        textAlign: 'center',
                        color: 'white',
                        background: 'rgba(0, 0, 0, 0.5)',
                        fontSize: '10px'
                    })
                    .text('0, 0')
            );

            // Add to map and make draggable
            $('#map-container').append(newContainer);
            setupDraggable(newContainer[0]);
        });
    });
	// Connect to WebSocket
const websocket = new WebSocket('ws://localhost:8080');
//const websocket = new WebSocket('ws://YOUR_LOCAL_IP:8080'); if on LAN

websocket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.action === 'positionUpdated') {
        const containers = document.querySelectorAll('.draggable-container');
        containers.forEach(container => {
            const img = container.querySelector('img');
            if (img.dataset.id === data.objectId.toString()) {
                // Update position (swap X/Y due to existing structure)
                container.style.left = data.positionX + 'px';
                container.style.top = data.positionY + 'px';
                // Update position text
                const text = container.querySelector('.position-text');
                text.textContent = `${data.positionX}, ${data.positionY}`;
                // Reset transform
                container.style.transform = 'none';
                container.setAttribute('data-x', 0);
                container.setAttribute('data-y', 0);
            }
        });
    }
};

// Update interact.js dragend listener
interact('.draggable-container').on('dragend', function(event) {
    const target = event.target;
    const img = target.querySelector('img');
    const objectId = img.dataset.id;

    const originalLeft = parseFloat(target.style.left) || 0;
    const originalTop = parseFloat(target.style.top) || 0;
    const translateX = parseFloat(target.getAttribute('data-x')) || 0;
    const translateY = parseFloat(target.getAttribute('data-y')) || 0;

    const newLeft = originalLeft + translateX;
    const newTop = originalTop + translateY;

    // Send update to WebSocket server
    websocket.send(JSON.stringify({
        action: 'updatePosition',
        objectId: objectId,
        positionX: newLeft, // Existing code uses top as positionX
        positionY: newTop // Existing code uses left as positionY
    }));

    // Update local position immediately
    target.style.left = newLeft + 'px';
    target.style.top = newTop + 'px';
    target.style.transform = 'none';
    target.setAttribute('data-x', 0);
    target.setAttribute('data-y', 0);
    target.querySelector('.position-text').textContent = 
        `${Math.round(newLeft)}, ${Math.round(newTop)}`;
});
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>