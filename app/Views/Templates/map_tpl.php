<?php include_once 'partials/editor_top_tpl.php'; ?>


<link rel="stylesheet" href="<?= HOST ?>/public/css/maps.css">

<div class="container-fluid no-select" style="padding-top: 4px;">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-2 col-md-2 sidebar_left sidebar_custom">
            <h3>Grid Settings</h3>
				<div class="mb-3">
					<label for="grid-size-input" class="form-label">Grid Size (px)</label>
					<input type="number" class="form-control" id="grid-size-input" 
						   name="grid-size">
				</div>
				
			<h3>Map Objects</h3>
            <form action="" method="post" class="mb-4">
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
                
            </ul>
        </div>

        <!-- Map Container -->
        <div class="col-md-8 col-lg-8" style="padding: 0">
            <div id="map-container">        
				<img id="map-image">
				
                <!-- Grid Overlay Added Here -->
                <div class="grid-overlay"></div>
                
            </div>
        </div>
	
		
		<!-- Right Sidebar -->
        <div class="col-md-2 col-md-2 sidebar_right sidebar_custom">
			<div id="status-effects-container" style="display: none;">
				<h4>Status Effects</h4>
			</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script>
const statusEffects = [
    { name: 'Acid', icon: 'https://cdn0.iconfinder.com/data/icons/poison-symbol/66/22-512.png' },
    { name: 'Bleeding', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Bleeding%20Out.png' },
    { name: 'Blind', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Blinded.png' },
    { name: 'Burning', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/On%20Fire.png' },
    { name: 'Electrocuted', icon: 'https://cdn-icons-png.flaticon.com/512/10746/10746618.png' }, // Add actual URL
    { name: 'Freezing', icon: 'https://cdn-icons-png.flaticon.com/512/1796/1796536.png' }, // Add actual URL
    { name: 'Necrosis', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Ancenstral%20Protectors.png' },
    { name: 'Petrified', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Petrified.png' },
    { name: 'Poison', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Poisoned.png' },
    { name: 'Prone', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Prone.png' },
    { name: 'Radiant', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Highlighted.png' },
    { name: 'Stunned', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Stunned.png' },
    { name: 'Thunder', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Blur.png' },
    { name: 'Wet', icon: 'https://static.thenounproject.com/png/2287944-200.png' },
    { name: 'Confused', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Confused.png' },
    { name: 'Frightened', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Frightened.png' },
    { name: 'Possessed', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Possessed.png' },
    { name: 'Unconscious', icon: 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Unconcious.png' }
];		
				
let gridSize;
let mapId;
let mapImage;
let allObjects = [];		
let selectedObjectId = null;
var mapRect;
var mapOffset;
		
		
    $(document).ready(function() {
		
		// Connect to WebSocket
		const websocket = new WebSocket('ws://localhost:8080');
		//const websocket = new WebSocket('ws://YOUR_LOCAL_IP:8080'); if on LAN
		

		websocket.onmessage = function(event) {
			const data = JSON.parse(event.data);
			switch (data.action){
				case 'firstFetchReturn':
					console.log(data);
					
					//Set variables to the new values
					//change to select the map with a flag of active map, and to have a dropdown for all maps
					gridSize = data['maps'][0].grid_size;
					mapId = data['maps'][0].id;
					mapImage = data['maps'][0].image;
					allObjects = data['objects'];
					
					redrawMap();
					redrawAllObjects();
					break;
				case 'positionUpdated':
					const containers = document.querySelectorAll('.draggable-container');
					containers.forEach(container => {
						const img = container.querySelector('img');
						if (img.dataset.id === data.objectId.toString()) {
							// Update position (swap X/Y due to existing structure)
							container.style.left = data.positionX + 'px';
							container.style.top = data.positionY + 'px';
							// Reset transform
							container.style.transform = 'none';
							container.setAttribute('data-x', 0);
							container.setAttribute('data-y', 0);
					}
					});
					break;
				case 'gridSizeUpdated':
					if (data.gridSize !== gridSize) {
						gridSize = data.gridSize;
						$('#grid-size-input').val(gridSize);
						updateGridSize(gridSize, false); // Pass false to prevent sending updates
					}
					break;
				default:
					//do nothing
			}
		};
		
		websocket.onopen = () => websocket.send(JSON.stringify({
					action: 'firstFetch',
				}));
		
		
		function redrawMap(){
			$("#grid-size-input").val(gridSize);
			$("#map-image").attr("src",mapImage);
			$(".grid-overlay").css("background-size", gridSize+"px " +gridSize + "px");
			
			adjustMapSize(gridSize);
			// window.addEventListener('resize', takeMaxSpaceWithoutCropping);
			const mapContainer = document.getElementById('map-container');
			mapRect = mapContainer.getBoundingClientRect();
			mapOffset = {
				left: mapRect.left,
				top: mapRect.top
			};
		}
		
		
		function redrawAllObjects(){
							
			// Creating a list of divs for objects
			var currentIndex = 0;
			while (currentIndex<allObjects.length) {
				document.getElementById('object-list').innerHTML += '<li class="list-group-item"" data-id="'+allObjects[currentIndex].id +'" data-url="'+allObjects[currentIndex].image_url+'">'+allObjects[currentIndex].name+'</li>';
				
				document.getElementById('map-container').innerHTML += '<div class="draggable-container" style="position: absolute; width:'+gridSize+'px; height: '+gridSize+'px; left: '+allObjects[currentIndex].positionX+'px; top: '+allObjects[currentIndex].positionY+'px;" data-id="'+allObjects[currentIndex].id+'"><img src="'+allObjects[currentIndex].image_url+'" class="draggable" data-id="'+allObjects[currentIndex].id+'" style="width: 100%; height: 100%;"></div>';
				currentIndex += 1;
			
			}
			
			
			initializeDraggables(gridSize);
			
			
			// handler for grid size change
			$('#grid-size-input').on('input', function() {
				const newSize = parseInt($(this).val(), 10);
				if (!isNaN(newSize) && newSize > 0 && newSize !== gridSize) {
					gridSize = newSize;
					updateGridSize(gridSize);
					const selectedId = $('#object-list li.selected').data('id');
					initializeDraggables(gridSize, `.draggable-container[data-id="${selectedId }"]`);
					
					// Send WebSocket message
					console.log(gridSize);
					websocket.send(JSON.stringify({
						action: 'updateGridSize',
						mapId: mapId,
						gridSize: newSize
					}));
				}
			});
			
			// Add click handler to lock all but one object from moving 
			// When selecting an object, disable others after all are initialized.
			$('#object-list li').on('click', function() {
				const objectId = $(this).data('id');
				const clickedObjAlreadySelected = $(this).hasClass('selected');

				if (clickedObjAlreadySelected) {
					// Deselect
					$('#object-list li').removeClass('selected');
					$('.draggable-container').removeClass('selected');
					//REMEMBER the selector for interact must match when setting it to false or true
					interact(`.draggable-container[data-id="${objectId}"]`).draggable(false);
					interact('.draggable-container').draggable(true);
					$('#status-effects-container').css('display', 'none');
				} else {				
					
					
					//if anything is already selected by
					//checking if any $('#object-list li'). has a class selected
					//switch the selection to another object
					if ($('#object-list li').hasClass('selected')){
						interact(`.draggable-container[data-id="${selectedObjectId}"]`).draggable(false);
						$('#object-list li').removeClass('selected');
						$('.draggable-container').removeClass('selected');
						
					}else{
						interact('.draggable-container').draggable(false);
					}
					selectedObjectId = objectId;
					interact(`.draggable-container[data-id="${objectId}"]`).draggable(true);
					$(`.draggable-container[data-id="${objectId}"]`).addClass('selected');
					$(this).addClass('selected');
					$('#status-effects-container').css('display', 'block');
					
					initializeDraggables(gridSize, `.draggable-container[data-id="${objectId}"]`);
				}
			});
		}
		
		
		
		
		
		// generate a grid with status effect in status-effects-list div
		// <div class="square" style="background-image: url('https://cdn0.iconfinder.com/data/icons/poison-symbol/66/22-512.png')"> </div>
		// status-effects-list
		
		function appendHtmlFromArray(container, arr) {
			var currentIndex = 0;
			while (currentIndex<arr.length) {
				container.innerHTML += '<div class="square" id="effect-'+arr[currentIndex].name +'" style="background-image: url('+arr[currentIndex].icon+')" title="'+arr[currentIndex].name+'"></div>';
				currentIndex += 1;
			
			}
		}
		appendHtmlFromArray(document.getElementById('status-effects-container'), statusEffects)
		
		// add an event listener for each of the effects to be implemented on the selected:
		// $('#status-effects-container div').on('click', function() {
		// identify the selected object, check if the selected  check if it already has effects, if number of effect is 0-2 add another effect div, if it's 3, add a "...", if more than 3, do nothing.
		
		
		
		//later change:
		//$('.draggable-container').addClass('locked');
		//$(`.draggable-container[data-id="${objectId}"]`).removeClass('locked');
		//if (event.target.classList.contains('locked')) return;
        function dragMoveListener(event) {
            var target = event.target;
            var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
            var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

            target.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
			
			// Update the position text relative to map-container
			// var positionText = target.querySelector('.position-text');
			// var originalLeft = parseFloat(target.style.left) || 0;
			// var originalTop = parseFloat(target.style.top) || 0;
			// var currentX = originalLeft + x;
			// var currentY = originalTop + y;
			// positionText.textContent = Math.round(currentX) + ', ' + Math.round(currentY);
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
		function initializeDraggables(gridSize, filterSelector ='.draggable-container') {
			console.log("gridSize:"+gridSize+" filterSelector:"+filterSelector);
			interact(filterSelector).draggable({
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
						//target.querySelector('.position-text').textContent = 
						//	`${Math.round(newLeft)}, ${Math.round(newTop)}`;
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
				
				//const text = container.querySelector('.position-text');
				//text.textContent = `${snappedLeft}, ${snappedTop}`;
				
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
		

	});
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>