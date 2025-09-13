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
			<div style="display: flex; justify-content: start;">
				<div class="selected-blue-button" id="objects-menu-button"><h4>Objects</h4></div>
				<div class="unselected-blue-button" id="maps-menu-button"><h4>Maps</h4></div>
			</div>
			<div style="display: flex">
				<div id="objects-menu-body">
					<h3>Map Objects</h3>
					<form id="addObjectForm" class="mb-4">
						<div class="mb-3">
							<label for="name" class="form-label">Object Name</label>
							<input type="text" class="form-control" id="form_object_name" name="form_object_name" required>
						</div>
						<div class="mb-3">
							<label for="form_object_image_url" class="form-label">Object Image URL</label>
							<input type="url" class="form-control" id="form_object_image_url" name="form_object_image_url" required>
						</div>
						<button class="btn btn-primary">Add Object</button>
					</form>

					<h4>Loaded Objects</h4>
					<ul id="object-list" class="list-group">		
					</ul>
				</div>
				<div id="maps-menu-body">
					<h3>Map Backgrounds</h3>
					<form id="addMapForm" class="mb-4">
						<div class="mb-3">
							<label for="name" class="form-label">Map Name</label>
							<input type="text" class="form-control" id="form_map_name" name="form_map_name" required>
						</div>
						<div class="mb-3">
							<label for="form_map_image_url" class="form-label">Map Image URL</label>
							<input type="url" class="form-control" id="form_map_image_url" name="form_map_image_url" required>
						</div>
						<button class="btn btn-primary">Add Map</button>
					</form>

					<h4>Loaded Maps</h4>
					<ul id="maps-list" class="list-group">		
					</ul>
				</div>
			</div>
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
			<div id="delete-button-container" style="display: none;">
				<button id="remove-object-btn" class="btn btn-danger mt-2" >
					Remove Object
				</button>
			</div>
			<div id="visual-changes-container" style="display: none; ">
				<div id="vc-rotate-left" class="vc-menu-item" style="background-image: url('https://uxwing.com/wp-content/themes/uxwing/download/arrow-direction/rotate-left-arrow-icon.png')"></div>
				<div id="vc-rotate-right" class="vc-menu-item; background-image: url('')"></div>
				<div id="vc-increase-scale" class="vc-menu-item; background-image: url('')"></div>
				<div id="vc-decrease-scale" class="vc-menu-item; background-image: url('')"></div>
				<div id="vc-make-wider" class="vc-menu-item; background-image: url('')"></div>
				<div id="vc-make-taller" class="vc-menu-item; background-image: url('')"></div>
			</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script>
const statusEffectsLinks =
    { 'Acid': 'https://cdn0.iconfinder.com/data/icons/poison-symbol/66/22-512.png',
    'Bleeding': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Bleeding%20Out.png',
    'Blind': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Blinded.png',
    'Burning': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/On%20Fire.png',
    'Electrocuted': 'https://cdn-icons-png.flaticon.com/512/10746/10746618.png',
    'Freezing': 'https://cdn-icons-png.flaticon.com/512/1796/1796536.png',
    'Necrosis': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Ancenstral%20Protectors.png',
    'Petrified': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Petrified.png',
    'Poison': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Poisoned.png',
    'Prone': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Prone.png',
    'Radiant': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Highlighted.png',
    'Stunned': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Stunned.png',
    'Thunder': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Blur.png',
    'Wet': 'https://static.thenounproject.com/png/2287944-200.png',
    'Confused': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Confused.png',
    'Frightened': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Frightened.png',
    'Possessed': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Possessed.png',
    'Unconscious': 'https://raw.githubusercontent.com/orangetruth/dnd5e-status-icons/refs/heads/main/Conditions/Unconcious.png' 
};		
		
let temp;		
let activeSocket;
let gridSize;
let mapId;
let mapImage;
let allMaps = new Map();
let allObjects = new Map();		
let selectedObjectId = null; //if it breaks use this instead  const selectedObject = $('#object-list li.selected')[0].getAttribute('data-id');
var mapRect;
var mapOffset;
		
		
    $(document).ready(function() {
		
		// Connect to WebSocket
		activeSocket = getActiveSocket();
		//const websocket = new WebSocket('ws://YOUR_LOCAL_IP:8080'); if on LAN
		function getActiveSocket (){
			if (!activeSocket || activeSocket.readyState === 3){
				activeSocket = new WebSocket('ws://localhost:8080');
			}
			return activeSocket;
		}
		

		getActiveSocket().onmessage = function(event) {
			const data = JSON.parse(event.data);
			switch (data.action){
				case 'firstFetchReturn':
					console.log('firstFetchReturn data: ');
					console.log(data);
					
					//Set variables to the new values
					//change to select the map with a flag of active map, and to have a dropdown for all maps
					gridSize = data['maps'][0].grid_size;
					mapId = data['maps'][0].id;
					mapImage = data['maps'][0].image;
					
					allMaps = Object.fromEntries(data['maps'].map(item => [item.id, {image: item.image, name: item.name,grid_size : item.grid_size}]));
					
					allObjects = Object.fromEntries(data['objects'].map(item => [item.id, {image_url: item.image_url, name: item.name, positionX : item.positionX, positionY : item.positionY, id : item.id, statusEffects: item.statusEffects}]));
										
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
				case 'objectAdded':
					const obj = data.object;
					console.log("New object:", obj);

					allObjects[obj.id] = {
						id: obj.id,
						name: obj.name,
						image_url: obj.image_url,
						positionX: obj.positionX || 0,
						positionY: obj.positionY || 0,
						statusEffects: obj.statusEffects || []
					};
					
					//sidebar
					document.getElementById('object-list').innerHTML += ('<li class="list-group-item" data-id="'+obj.id+'" data-url="'+obj.image_url+'">'+obj.name+'</li>');
					//on the map
					document.getElementById('map-container').innerHTML += ('<div class="draggable-container" style="position: absolute; width:'+gridSize+'px; height:'+gridSize+'px; left:'+obj.positionX+'px; top:'+obj.positionY+'px;" data-id="'+obj.id+'" id="'+obj.id+'"> <img src="'+obj.image_url+'" class="draggable" data-id="'+obj.id+'" style="width: 100%; height: 100%;"> <div class="status-effects-indicator" style="position: absolute;bottom: 100%;display: none;gap: 5%;background: brown; justify-content: space-between;"></div>	</div>');
					
					addClickHandlersOnObjectList();
					initializeDraggables(gridSize, `.draggable-container[data-id="${obj.id}"]`);
					break;
				
				case "ObjectRemoved":
					//removes both 
					console.log("Received confirmation of deletion");
					console.log(data);
					$(`.list-group-item[data-id=${data.id}]`).replaceWith(); 
					$(`.draggable-container[data-id=${data.id}]`).replaceWith(); 
					
					delete allObjects[data.id];
					
					//TODO should be put in a separate method later
					// Deselect
					$('#object-list li').removeClass('selected');
					$('.draggable-container').removeClass('selected').css('z-index', 1);
					//REMEMBER the selector for interact must match when setting it to false or true
					interact(`.draggable-container[data-id="${data.id}"]`).draggable(false);
					interact('.draggable-container').draggable(true);
					$('#status-effects-container').css('display', 'none');
					$('#delete-button-container').css('display', 'none');
					//remove all highlighted
					$('#status-effects-container div').removeClass('selected-effects-box');
					break;
				
				case "effectsUpdated":
					console.log(data);
					//'objectId' => $objectId,
					//'statusEffects' => $statusEffects
					
					
					allObjects[data.objectId].statusEffects = data.statusEffects;
					updateEffectsLegend(data.objectId);
					
					break;
				case "MapAdded":
					console.log(data);
					
					const map1 = data.map1;
					
					allMaps[map1.id] = {
						image: map1.image,
						name: map1.name,
						grid_size: map1.grid_size
					};
					
					gridSize = map1.grid_size;
					mapId = map1.id;
					mapImage = map1.image;
					
					redrawMap();
					updateGridSize(gridSize);
					
					// show the correct map
					//Recalc dimentions of the map
					
					break;
					
				case "mapSwitched":
					temp = data;
					mapId = data.selected_map_id;;
					gridSize = allMaps[mapId].grid_size;
					mapImage = allMaps[mapId].image;
					
					redrawMap();
					updateGridSize(gridSize);
				
					break;
				default:
					//do nothing
			}
		};
		
		getActiveSocket().onopen = () => getActiveSocket().send(JSON.stringify({
					action: 'firstFetch',
				}));
		
		
		function redrawMap(){
			$("#grid-size-input").val(gridSize);
			$("#map-image").attr("src",mapImage);
			$(".grid-overlay").css("background-size", gridSize+"px " +gridSize + "px");
			
			document.getElementById('maps-list').innerHTML = "";
			
			for (const key in allMaps) {
				
				if(mapId == key){
					document.getElementById('maps-list').innerHTML += '<li class="list-group-item selected" data-id="'+key +'" data-url="'+allMaps[key].image+'">'+allMaps[key].name+'</li>';
				}else{
					document.getElementById('maps-list').innerHTML += '<li class="list-group-item" data-id="'+key +'" data-url="'+allMaps[key].image+'">'+allMaps[key].name+'</li>';	
				}
			}
			
			const mapImageElement = document.getElementById('map-image');
			$("#map-image")
				.off("load.redraw") //makes sure that handler runs once
				.on("load.redraw", function() {
					adjustMapSize(gridSize);
					// window.addEventListener('resize', takeMaxSpaceWithoutCropping);
					const mapContainer = document.getElementById('map-container');
					mapRect = mapContainer.getBoundingClientRect();
					mapOffset = {
						left: mapRect.left,
						top: mapRect.top
					};
			});
			
			addClickHandlersOnMapsList();
		}
		
		
		function redrawAllObjects(){
							
			// Creating a list of divs for objects
			for (const key in allObjects) {
				
				document.getElementById('object-list').innerHTML += '<li class="list-group-item"" data-id="'+allObjects[key].id +'" data-url="'+allObjects[key].image_url+'">'+allObjects[key].name+'</li>';
				
				document.getElementById('map-container').innerHTML += '<div class="draggable-container" style="position: absolute; width:'+gridSize+'px; height: '+gridSize+'px; left: '+allObjects[key].positionX+'px; top: '+allObjects[key].positionY+'px;" data-id="'+allObjects[key].id+'" id="'+allObjects[key].id+'"><img src="'+allObjects[key].image_url+'" class="draggable" data-id="'+allObjects[key].id+'" style="width: 100%; height: 100%;"><div class="status-effects-indicator" style="position: absolute;bottom: 100%;display: flex;gap: 5%;background: brown; justify-content: space-between;"></div></div>';
				
			}
			
			$("#map-image")
				.off("load.objects") //makes sure that handler runs once
				.on("load.objects", function() {
					
				const mapContainer = document.getElementById('map-container');
				const rect = mapContainer.getBoundingClientRect();
				mapOffset = { left: rect.left, top: rect.top };	

				initializeDraggables(gridSize);
				updateEffectsLegend(0, updateAll=true);
			});
			
			// handler for grid size change
			$('#grid-size-input').on('input', function() {
				const newSize = parseInt($(this).val(), 10);
				if (!isNaN(newSize) && newSize > 0 && newSize !== gridSize) {
					gridSize = newSize;
					updateGridSize(gridSize);
					const selectedId = $('#object-list li.selected').data('id');
					initializeDraggables(gridSize, `.draggable-container[data-id="${selectedId }"]`);
					updateEffectsLegend(0, updateAll=true);
					
					// Send WebSocket message
					getActiveSocket().send(JSON.stringify({
						action: 'updateGridSize',
						mapId: mapId,
						gridSize: newSize
					}));
				}
			});
			
			addClickHandlersOnObjectList();
		}
		
		
		function addClickHandlersOnObjectList(){
			// Add click handler to lock all but one object from moving 
			// When selecting an object, disable others after all are initialized.
			$('#object-list li').off('click');
			$('#object-list li').on('click', function() {
				const objectId = $(this).data('id');
				const clickedObjAlreadySelected = $(this).hasClass('selected');

				if (clickedObjAlreadySelected) {
					// Deselect
					$('#object-list li').removeClass('selected');
					$('.draggable-container').removeClass('selected').css('z-index', 1);
					//REMEMBER the selector for interact must match when setting it to false or true
					interact(`.draggable-container[data-id="${objectId}"]`).draggable(false);
					interact('.draggable-container').draggable(true);
					$('#status-effects-container').css('display', 'none');
					$('#delete-button-container').css('display', 'none');
					//remove all highlighted
					$('#status-effects-container div').removeClass('selected-effects-box');
				
				} else {				
					
					
					//if anything is already selected by
					//checking if any $('#object-list li'). has a class selected
					//switch the selection to another object
					if ($('#object-list li').hasClass('selected')){
						interact(`.draggable-container[data-id="${selectedObjectId}"]`).draggable(false);
						$('#object-list li').removeClass('selected');
						$('.draggable-container').removeClass('selected').css('z-index', 1);
						
					}else{
						interact('.draggable-container').draggable(false);
					}
					selectedObjectId = objectId;
					interact(`.draggable-container[data-id="${objectId}"]`).draggable(true);
					$(`.draggable-container[data-id="${objectId}"]`).addClass('selected').css('z-index', 9999);
					$(this).addClass('selected');
					$('#status-effects-container').css('display', 'block');
					$('#delete-button-container').css('display', 'block');
					
					initializeDraggables(gridSize, `.draggable-container[data-id="${objectId}"]`);
					
					
					$('#status-effects-container div').removeClass('selected-effects-box');
					for (const effect of allObjects[selectedObjectId].statusEffects) {
						const nameOfEffect = '#effect-'+effect;
						$(nameOfEffect).addClass('selected-effects-box');
					}
					
				}
			});
		}
		
		function addClickHandlersOnMapsList(){
			$('#maps-list li').off('click');
			$('#maps-list li').on('click', function() {
				const selectedMapId = $(this).data('id');
				const clickedMapNotYetSelected = !($(this).hasClass('selected'));	
				
				
				if(clickedMapNotYetSelected){		
					
					//!@#$
					//Change to do so on the receiving websocket
					$('#maps-list li').removeClass('selected');
					console.log($(this).addClass('selected'));
					
					console.log(`Sending update - Map changed to ${allMaps[selectedMapId].name}`);
					
					// Send addObject request to WebSocket
					getActiveSocket().send(JSON.stringify({
						action: "switchMap",
						selectedId: selectedMapId
					}));
				
				}
			});
		}
		
		// generate a grid with status effect in status-effects-list div
		// <div class="square-effects-menu" style="background-image: url('https://cdn0.iconfinder.com/data/icons/poison-symbol/66/22-512.png')"> </div>
		// status-effects-list
		
		function appendHtmlFromArray(container, arr) {
			var currentIndex = 0;
			for (const key in arr){
				container.innerHTML += '<div class="square-effects-menu" id="effect-'+key +'" style="background-image: url('+arr[key]+')" title="'+key+'"></div>';
			}
			while (currentIndex<arr.length) {
				container.innerHTML += '<div class="square-effects-menu" id="effect-'+arr[currentIndex].name +'" style="background-image: url('+arr[currentIndex].icon+')" title="'+arr[currentIndex].name+'"></div>';
				currentIndex += 1;
			
			}
		}
		appendHtmlFromArray(document.getElementById('status-effects-container'), statusEffectsLinks)
		
		// add an event listener for each of the effects to be implemented on the selected:
		$('#status-effects-container div').on('click', function() {
			const clickedEffectId = (this).id.replace("effect-", "");
			
			console.log(selectedObjectId+ " " + clickedEffectId);
			
			if(allObjects[selectedObjectId].statusEffects.includes(clickedEffectId)){
				allObjects[selectedObjectId].statusEffects = allObjects[selectedObjectId].statusEffects.filter(e => e !== clickedEffectId);
				$(this).removeClass('selected-effects-box');
			}else{
				allObjects[selectedObjectId].statusEffects.push(clickedEffectId);
				$(this).addClass('selected-effects-box');
			}
			
			updateRemoteEffects(allObjects[selectedObjectId].statusEffects, selectedObjectId);
			updateEffectsLegend(selectedObjectId);
		});
		
		$('#remove-object-btn').on('click', function() {
			const objectId = selectedObjectId;
			if (!objectId) return;
			
			getActiveSocket().send(JSON.stringify({
				action: "removeObject",
				id: objectId
			}));
			console.log(`Deleted ${objectId}`);
			
			//TODO should be put in a separate method later
			// Deselect
			$('#object-list li').removeClass('selected');
			$('.draggable-container').removeClass('selected').css('z-index', 1);
			//REMEMBER the selector for interact must match when setting it to false or true
			interact(`.draggable-container[data-id="${objectId}"]`).draggable(false);
			interact('.draggable-container').draggable(true);
			$('#status-effects-container').css('display', 'none');
			$('#delete-button-container').css('display', 'none');
			//remove all highlighted
			$('#status-effects-container div').removeClass('selected-effects-box');
		});
		
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
		
		// Modified updateGridSize function
		// Reinitialize all draggables unconditionally, then adjust for selection.
		function updateGridSize(newSize, sendUpdates = true) {
			gridSize = newSize;
			allMaps[mapId].grid_size = newSize;
			$('.grid-overlay').css('background-size', `${gridSize}px ${gridSize}px`);
			$('.draggable-container').css({ width: `${gridSize}px`, height: `${gridSize}px` });
			adjustMapSize(gridSize);
			const mc = document.getElementById('map-container');
			const mapRect = mc.getBoundingClientRect();
			mapOffset = { left: mapRect.left, top: mapRect.top };

			interact('.draggable-container').off('dragmove dragend');
			
			const mapImageEl = document.getElementById('map-image');			
			if (mapImageEl.complete && mapImageEl.naturalWidth !== 0) {
				initializeDraggables(gridSize);
			} else {
				$("#map-image")
					.off("load.updateGrid") // prevent duplicate binding
					.one("load.updateGrid", function () {
						initializeDraggables(gridSize);
					});
			}
			
			if (selectedObjectId !== null) {
				interact('.draggable-container').draggable(false);
				interact(`.draggable-container[data-id="${selectedObjectId}"]`).draggable(true);
			}
			updateDraggableObjects(gridSize, sendUpdates);
		}
		
	
		// Modified initializeDraggables function
		//The function always targets all .draggable-container elements unless specifically handling a selection (managed separately in click handler).
		function initializeDraggables(gridSize, filterSelector ='.draggable-container') {
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
						getActiveSocket().send(JSON.stringify({
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
					getActiveSocket().send(JSON.stringify({
						action: 'updatePosition',
						objectId: objectId,
						positionX: snappedLeft,
						positionY: snappedTop
					}));
				}
			});
		}
		
		function updateRemoteEffects(statusEffects, objectId){	
				
			getActiveSocket().send(JSON.stringify({
				action: 'updateEffects',
				objectId: objectId,
				statusEffects: statusEffects
			}));
		}
		
		//todododod call this function from all needed places
		function updateEffectsLegend(selectedID, updateAll=false){
			let arrOfBoxes;
			if (updateAll){
				for (const id in allObjects){updateEffectsLegend(id);}
			} else {
				switch (allObjects[selectedID].statusEffects.length){
					case 0:
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.display = "none";
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].innerHTML = "";
						break;
						
					case 1:
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.display = "flex";
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.width = $(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.height = '30%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.left = '35%';
						//todododod
						//statusEffectsLinks[allObjects[selectedID].statusEffects[0]] gives a link to image
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].innerHTML = '<div style="width: 100%;height: 100%;background-size: contain;background-image: url('+statusEffectsLinks[allObjects[selectedID].statusEffects[0]]+');"></div>';
						arrOfBoxes = $(".draggable-container#"+selectedID+" div.status-effects-indicator div");
						arrOfBoxes.height(Math.trunc(gridSize*0.3)).width(Math.trunc(gridSize*0.3));
						break;
						
					case 2:
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.display = "flex";
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.width = '65%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.height = '30%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.left = '17%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].innerHTML = '<div style="width: 100%;height: 100%;background-size: contain;background-image: url('+statusEffectsLinks[allObjects[selectedID].statusEffects[0]]+');"></div><div style="width: 100%;height: 100%;background-size: contain;background-image: url('+statusEffectsLinks[allObjects[selectedID].statusEffects[1]]+');"></div>';
						arrOfBoxes = $(".draggable-container#"+selectedID+" div.status-effects-indicator div");
						arrOfBoxes.height(Math.trunc(gridSize*0.3)).width(Math.trunc(gridSize*0.3));
						break;
						
					case 3:
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.display = "flex";
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.width = '100%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.height = '30%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.left = '0%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].innerHTML = '<div style="width: 100%;height: 100%;background-size: contain;background-image: url('+statusEffectsLinks[allObjects[selectedID].statusEffects[0]]+');"></div><div style="width: 100%;height: 100%;background-size: contain;background-image: url('+statusEffectsLinks[allObjects[selectedID].statusEffects[1]]+');"></div><div style="width: 100%;height: 100%;background-size: contain;background-image: url('+statusEffectsLinks[allObjects[selectedID].statusEffects[2]]+');"></div>';
						arrOfBoxes = $(".draggable-container#"+selectedID+" div.status-effects-indicator div");
						arrOfBoxes.height(Math.trunc(gridSize*0.3)).width(Math.trunc(gridSize*0.3));
						break;
						
					default:
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.display = "flex";
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.width = '135%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.height = '30%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].style.left = '00%';
						$(".draggable-container#"+selectedID+" div.status-effects-indicator")[0].innerHTML = '<div style="width: 100%;height: 100%;background-size: contain;background-image: url('+statusEffectsLinks[allObjects[selectedID].statusEffects[0]]+');"></div><div style="width: 100%;height: 100%;background-size: contain;background-image: url('+statusEffectsLinks[allObjects[selectedID].statusEffects[1]]+');"></div><div style="width: 100%;height: 100%;background-size: contain;background-image: url('+statusEffectsLinks[allObjects[selectedID].statusEffects[2]]+');"></div><div style="fontSize:'+ Math.trunc(gridSize*0.2) + 'px">...</div>';
						arrOfBoxes = $(".draggable-container#"+selectedID+" div.status-effects-indicator div");
						arrOfBoxes.height(Math.trunc(gridSize*0.3)).width(Math.trunc(gridSize*0.3));
						break;
						
						//$($(".draggable-container#"+1+" div.status-effects-indicator div")[0]).width(100)
				}
			}	
		}
		
		// Function to toggle menu visibility
		function toggleMapObjectMenu(selectedMenu) {
			const isObjectsMenu = selectedMenu === 'objects';
			
			// Toggle button classes
			if (isObjectsMenu) {
				$('#objects-menu-button').removeClass('unselected-blue-button').addClass('selected-blue-button');
				$('#maps-menu-button').removeClass('selected-blue-button').addClass('unselected-blue-button');
			} else {
				$('#maps-menu-button').removeClass('unselected-blue-button').addClass('selected-blue-button');
				$('#objects-menu-button').removeClass('selected-blue-button').addClass('unselected-blue-button');
			}
			
			// Toggle menu visibility
			if (isObjectsMenu) {
				$('#objects-menu-body').css('display', 'block');
				$('#maps-menu-body').css('display', 'none');
			} else {
				$('#maps-menu-body').css('display', 'block');
				$('#objects-menu-body').css('display', 'none');
			}
		}
		
		
		$('#objects-menu-button').on('click', function() {
			toggleMapObjectMenu('objects');
		});

		$('#maps-menu-button').on('click', function() {
			toggleMapObjectMenu('maps');
		});

		toggleMapObjectMenu('objects');
	
		
		document.getElementById("addObjectForm").addEventListener("submit", function (e) {
			e.preventDefault();

			const name = document.getElementById("form_object_name").value;
			const imageUrl = document.getElementById("form_object_image_url").value;

			console.log("Sent");
			console.log(name);
			console.log(imageUrl);
			
			// Send addObject request to WebSocket
			getActiveSocket().send(JSON.stringify({
				action: "addObject",
				name: name,
				image_url: imageUrl
			}));

			// clear inputs
			this.reset();
		});
		
		document.getElementById("addMapForm").addEventListener("submit", function (e) {
			e.preventDefault();

			const name = document.getElementById("form_map_name").value;
			const imageUrl = document.getElementById("form_map_image_url").value;

			console.log(`Sent ${name}`);
			console.log(imageUrl);
			
			// Send addObject request to WebSocket
			getActiveSocket().send(JSON.stringify({
				action: "addMap",
				name: name,
				image_url: imageUrl,
				grid_size: gridSize
			}));

			// clear inputs
			this.reset();
		});

	});
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>