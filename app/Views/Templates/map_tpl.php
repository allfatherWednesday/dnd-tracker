<?php include_once 'partials/editor_top_tpl.php'; ?>

<link rel="stylesheet" href="<?= HOST ?>/public/css/maps.css">

<?php $data['grid-size']=37?>

<div class="container-fluid no-select" style="padding-top: 4px;">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-2 col-md-2 sidebar_left sidebar_custom">
            <h3>Grid Settings</h3>
				<div class="mb-3">
					<label for="grid-size-input" class="form-label">Grid Size (px)</label>
					<input type="number" class="form-control" id="grid-size-input" 
						   name="grid-size" value="<?= $data['grid-size'] ?>">
				</div>
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
                    <div class="draggable-container" style="position: absolute; width: <?= $data['grid-size']?>px; height: <?= $data['grid-size']?>px; left: <?= $object['positionX'] ?>px; top: <?= $object['positionY'] ?>px;" data-id="<?= $object['id'] ?>">
                        <img src="<?= $object['image_url'] ?>" 
                             class="draggable" 
                             data-id="<?= $object['id'] ?>" 
                             style="width: 100%; height: 100%;">
                        <div class="position-text" style="position: absolute; top: 0; left: 0; width: 100%; text-align: center; color: white; background: rgba(0, 0, 0, 0.5); font-size: 10px;">
                            <?= $object['positionX'] ?>, <?= $object['positionY'] ?>
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
		let gridSize = <?= $data['grid-size'] ?>;
		let selectedObjectId = null;
		
		adjustMapSize(<?= $data['grid-size']?>);
		// window.addEventListener('resize', takeMaxSpaceWithoutCropping);
		const mapContainer = document.getElementById('map-container');
		var mapRect = mapContainer.getBoundingClientRect();
        var mapOffset = {
            left: mapRect.left,
            top: mapRect.top
        };
		initializeDraggables(<?= $data['grid-size']?>);
		
		// Handle grid size input changes
        $('#grid-size-input').on('input', function() {
            const newSize = parseInt($(this).val(), 10);
            if (!isNaN(newSize) && newSize > 0) {
                gridSize = newSize;
                updateGridSize(gridSize);
            }
        });
		
		
        function dragMoveListener(event) {
            var target = event.target;
            var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
            var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

            target.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
			
			// Update the position text relative to map-container
			var positionText = target.querySelector('.position-text');
			var originalLeft = parseFloat(target.style.left) || 0;
			var originalTop = parseFloat(target.style.top) || 0;
			var currentX = originalLeft + x;
			var currentY = originalTop + y;
			positionText.textContent = Math.round(currentX) + ', ' + Math.round(currentY);
        }
		
		
		function adjustMapSize(gridSize){
			// We make the container take maximum available space within parent and be the multiple of the grid
			const mapContainer = document.getElementById('map-container');
			mapContainer.style.width = '100%';
			mapContainer.style.height = 'calc(100vh - 60px)';
			makeElementAMultipleOfGridSize(mapContainer, gridSize);
			
			//We set image to take the maximum space within the container, without cropping it or stretching it, the whole image is within the container, plus white margins only on L+R or T+B
			const mapImage = document.getElementById('map-image');
			mapImage.style.objectFit = "contain";
			takeMaxSpaceWithoutCropping(mapImage);
			
			// Get dimensions of the map-image contained in map-container
			const mapImageRect = mapImage.getBoundingClientRect();
			// Adjust the container to hug the image from all sides, the smaller of the width and height might not be the multiple of the grid-size
			mapContainer.style.width = mapImageRect.width+'px';
			mapContainer.style.height = mapImageRect.height+'px';
			// Adjust the container and crop the image to be multiple of the grid size
			makeElementAMultipleOfGridSize(mapContainer, gridSize);
			// Remove white margins from the Image
			mapImage.style.objectFit="cover";
		}
		
		// Take the images natural W and H,
		//set image to take the maximum space within the container, without cropping it or stretching it, the whole image is within the container, plus white margins only on L+R or only T+B
		function takeMaxSpaceWithoutCropping(mi) {

			const imageWidth = mi.naturalWidth;
			const imageHeight = mi.naturalHeight;

			if (imageHeight > imageWidth) {
				// Container is wider than the image
				mi.style.height = '100%';
				mi.style.width = 'auto';
			} else {
				// Container is taller than the image
				mi.style.width = '100%';
				mi.style.height = 'auto';
			}
		}	
		
		function makeElementAMultipleOfGridSize(container, gridSize){
			// Adjust the container to be the multiple of grid-size, by shrinking it
			var mapRect = container.getBoundingClientRect();
			container.style.width = roundDownToMultiple(mapRect.width, gridSize)+'px';
			container.style.height = roundDownToMultiple(mapRect.height, gridSize)+'px';
		}
		
		/*function updateGridSize(gridSize) {
			// Update grid overlay
			$('.grid-overlay').css('background-size', `${gridSize}px ${gridSize}px`);
			
			// Resize draggable containers
			$('.draggable-container').css({ width: `${gridSize}px`, height: `${gridSize}px` });

			// Adjust map container and recalculate offset
			adjustMapSize(gridSize);
			const mc = document.getElementById('map-container');
			const mapRect = mc.getBoundingClientRect();
			mapOffset = { left: mapRect.left, top: mapRect.top };

			// BROKEN FEATURE - THIS UNSETS nteract('.draggable-container').on('dragend', function(event)
			// Reinitialize draggables with new grid size and adjust the position of objects to a new grid size
			interact('.draggable-container').unset();
			initializeDraggables(gridSize);
			updateDraggableObjects(gridSize);
			window.dragMoveListener = dragMoveListener;
		}*/
		
		
		// BROKEN FEATURE - THIS UNSETS nteract('.draggable-container').on('dragend', function(event)
		// Modified updateGridSize function
		function updateGridSize(newSize) {
			gridSize = newSize;
			$('.grid-overlay').css('background-size', `${gridSize}px ${gridSize}px`);
			$('.draggable-container').css({ width: `${gridSize}px`, height: `${gridSize}px` });
			adjustMapSize(gridSize);
			const mc = document.getElementById('map-container');
			const mapRect = mc.getBoundingClientRect();
			mapOffset = { left: mapRect.left, top: mapRect.top };

			interact('.draggable-container').unset();	
			if (selectedObjectId !== null) {
				initializeDraggables(gridSize, `.draggable-container[data-id="${selectedObjectId}"]`);
			} else {
				initializeDraggables(gridSize);
			}
			updateDraggableObjects(gridSize);
			window.dragMoveListener = dragMoveListener;
		}
		
		// Modified initializeDraggables function
		function initializeDraggables(gridSize, selector = '.draggable-container') {
			interact(selector).draggable({
				inertia: false,
				modifiers: [
					interact.modifiers.restrictRect({
						restriction: 'parent',
						endOnly: true
					}),
					interact.modifiers.snap({
						targets: [
							interact.createSnapGrid({ 
								x: gridSize, 
								y: gridSize,
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
					move: dragMoveListener,
					end: function(event) { // Added end listener here
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
							positionX: newLeft,
							positionY: newTop
						}));

						// Update local position
						target.style.left = newLeft + 'px';
						target.style.top = newTop + 'px';
						target.style.transform = 'none';
						target.setAttribute('data-x', 0);
						target.setAttribute('data-y', 0);
						target.querySelector('.position-text').textContent = 
							`${Math.round(newLeft)}, ${Math.round(newTop)}`;
					}
				}
			});
		}
		
		function roundDownToMultiple(num, mutlipleOf){
			return (Math.floor(num/mutlipleOf)*mutlipleOf);
		}
		
		function updateDraggableObjects(gridSize){
			document.querySelectorAll('.draggable-container').forEach(container => {
				const left = parseFloat(container.style.left) || 0;
				const top = parseFloat(container.style.top) || 0;
				
				const snappedLeft = roundDownToMultiple(left, gridSize);
				const snappedTop = roundDownToMultiple(top, gridSize);
				
				// Update position (swap X/Y due to existing structure)
				container.style.left = snappedLeft + 'px';
				container.style.top =  snappedTop + 'px';
				
				// Update position text
				const text = container.querySelector('.position-text');
				text.textContent = `${snappedLeft}, ${snappedTop}`;
				
				// Reset transform
				container.style.transform = 'none';
				container.setAttribute('data-x', 0);
				container.setAttribute('data-y', 0);
				
			});
		}
		
		
		
		
		
		
		
        

        // Add click handler to load objects onto the map
        /*$('#object-list li').on('click', function() {
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
                                x: <?= $data['grid-size']?>,
                                y: <?= $data['grid-size']?>,
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
            });
        });
    */
	
		// Add click handler to lock all but one object from moving 
		$('#object-list li').on('click', function() {
			const objectId = $(this).data('id');
			const isSelected = $(this).hasClass('selected');

			if (isSelected) {
				// Deselect
				selectedObjectId = null;
				$('#object-list li').removeClass('selected');
				$('.draggable-container').removeClass('selected');
				interact('.draggable-container').draggable(true);
				initializeDraggables(gridSize);
			} else {
				// Select
				selectedObjectId = objectId;
				$('#object-list li').removeClass('selected');
				$(this).addClass('selected');
				$('.draggable-container').removeClass('selected');
				const selectedContainer = $(`.draggable-container[data-id="${objectId}"]`);
				selectedContainer.addClass('selected');
				interact('.draggable-container').draggable(false);
				initializeDraggables(gridSize, `.draggable-container[data-id="${objectId}"]`);
			}
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