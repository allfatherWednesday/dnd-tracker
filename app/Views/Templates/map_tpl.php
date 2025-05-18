<?php include_once 'partials/editor_top_tpl.php'; 

// Initialize controller and data
$mapObjectController = new \app\Controllers\MapObjectController();
$objects = $mapObjectController->getObjects();
$gridSize = $data['map']['grid_size'] ?? 37;
$mapImage = $data['map']['image'] ?? '';
$mapId = $data['map']['id'] ?? 0;
?>



<link rel="stylesheet" href="<?= HOST ?>/public/css/maps.css">

<div class="container-fluid no-select" style="padding-top: 4px;">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-2 col-md-2 sidebar_left sidebar_custom">
            <h3>Grid Settings</h3>
				<div class="mb-3">
					<label for="grid-size-input" class="form-label">Grid Size (px)</label>
					<input type="number" class="form-control" id="grid-size-input" 
						   name="grid-size" value="<?= $gridSize ?>">
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
				<img id="map-image" src="<?= $mapImage ?>" >
				
                <!-- Grid Overlay Added Here -->
                <div class="grid-overlay" style="background-size: <?= $gridSize ?>px <?= $gridSize ?>px;"></div>
                
				<!-- Draggable Objects -->
                <?php foreach ($objects as $object): ?>
                    <div class="draggable-container" style="position: absolute; width: <?= $gridSize?>px; height: <?= $gridSize?>px; left: <?= $object['positionX'] ?>px; top: <?= $object['positionY'] ?>px;" data-id="<?= $object['id'] ?>">
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
				
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script>
let mapId = <?= $mapId ?>;
let gridSize = <?= $gridSize ?>;
		
		

		
    $(document).ready(function() {
		
		let selectedObjectId = null;
		
		adjustMapSize(gridSize);
		// window.addEventListener('resize', takeMaxSpaceWithoutCropping);
		const mapContainer = document.getElementById('map-container');
		var mapRect = mapContainer.getBoundingClientRect();
        var mapOffset = {
            left: mapRect.left,
            top: mapRect.top
        };
		initializeDraggables(gridSize);
		
		$('#grid-size-input').on('input', function() {
			const newSize = parseInt($(this).val(), 10);
			if (!isNaN(newSize) && newSize > 0 && newSize !== gridSize) {
				gridSize = newSize;
				updateGridSize(gridSize, true); // Pass true to send updates
				websocket.send(JSON.stringify({
					action: 'updateGridSize',
					mapId: mapId,
					gridSize: newSize
				}));
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
		
		//Ensures map container dimensions are multiples of grid size
		//Handles image fitting using contain/cover strategies
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
		
		// BROKEN FEATURE - THIS UNSETS nteract('.draggable-container').on('dragend', function(event)
		// Modified updateGridSize function
		// Reinitialize all draggables unconditionally, then adjust for selection.
		function updateGridSize(newSize, sendUpdates = true) {
			gridSize = newSize;
			$('.grid-overlay').css('background-size', `${gridSize}px ${gridSize}px`);
			$('.draggable-container').css({ width: `${gridSize}px`, height: `${gridSize}px` });
			adjustMapSize(gridSize);
			const mc = document.getElementById('map-container');
			const mapRect = mc.getBoundingClientRect();
			mapOffset = { left: mapRect.left, top: mapRect.top };

			interact('.draggable-container').off('dragmove dragend');
			initializeDraggables(gridSize);
			
			if (selectedObjectId !== null) {
				interact('.draggable-container').draggable(false);
				interact(`.draggable-container[data-id="${selectedObjectId}"]`).draggable(true);
			}
			updateDraggableObjects(gridSize, sendUpdates);
		}
		
	
		// Modified initializeDraggables function
		//The function always targets all .draggable-container elements unless specifically handling a selection (managed separately in click handler).
		function initializeDraggables(gridSize) {
			interact('.draggable-container').draggable({
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
		
		function updateDraggableObjects(gridSize, sendUpdates) {
			document.querySelectorAll('.draggable-container').forEach(container => {
				const left = parseFloat(container.style.left) || 0;
				const top = parseFloat(container.style.top) || 0;
				
				const snappedLeft = roundDownToMultiple(left, gridSize);
				const snappedTop = roundDownToMultiple(top, gridSize);
				
				container.style.left = snappedLeft + 'px';
				container.style.top = snappedTop + 'px';
				
				const text = container.querySelector('.position-text');
				text.textContent = `${snappedLeft}, ${snappedTop}`;
				
				container.style.transform = 'none';
				container.setAttribute('data-x', 0);
				container.setAttribute('data-y', 0);
				
				if (sendUpdates) {
					const objectId = container.getAttribute('data-id');
					websocket.send(JSON.stringify({
						action: 'updatePosition',
						objectId: objectId,
						positionX: snappedLeft,
						positionY: snappedTop
					}));
				}
			});
		}
		
		
		
		
		
		
		
	
		// Add click handler to lock all but one object from moving 
		// When selecting an object, disable others after all are initialized.
		$('#object-list li').on('click', function() {
			const objectId = $(this).data('id');
			const isSelected = $(this).hasClass('selected');

			if (isSelected) {
				// Deselect
				$('#object-list li').removeClass('selected');
				$('.draggable-container').removeClass('selected');
				interact('.draggable-container').draggable(true);
			} else {
				// Select: Disable all except selected
				selectedObjectId = objectId;
				$('#object-list li').removeClass('selected');
				$(this).addClass('selected');
				$('.draggable-container').removeClass('selected');
				const selectedContainer = $(`.draggable-container[data-id="${objectId}"]`);
				selectedContainer.addClass('selected');
				
				interact('.draggable-container').draggable(false);
				interact(`.draggable-container[data-id="${objectId}"]`).draggable(true);
			}
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
		}else if (data.action === 'gridSizeUpdated') {
			if (data.gridSize !== gridSize) {
				gridSize = data.gridSize;
				$('#grid-size-input').val(gridSize);
				updateGridSize(gridSize, false); // Pass false to prevent sending updates
			}
		}
	};

	

	$('#grid-size-input').on('input', function() {
		const newSize = parseInt($(this).val(), 10);
		if (!isNaN(newSize) && newSize > 0 && newSize !== gridSize) {
			gridSize = newSize;
			updateGridSize(gridSize);
			// Send WebSocket message
			console.log(gridSize);
			websocket.send(JSON.stringify({
				action: 'updateGridSize',
				mapId: mapId,
				gridSize: newSize
			}));
		}
	});
});
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>