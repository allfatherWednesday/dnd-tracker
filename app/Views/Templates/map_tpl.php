<?php include_once 'partials/editor_top_tpl.php'; ?>

<link rel="stylesheet" href="<?= HOST ?>/public/css/maps.css">

<div class="container-fluid no-select" style="padding-top: 4px;">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-2 col-md-2 sidebar_left sidebar_custom">
            <h3>Grid Settings</h3>
			<div class="mb-3">
				<label for="grid-size-input" class="form-label">Vertical Grid Cell Number</label>
				<input type="number" class="form-control" id="grid-size-input" name="grid-size">
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
					<ul id="object-list" class="list-group"></ul>
					<button id="show-bin-btn" class="btn btn-info mt-3">Show Bin</button>
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
					<ul id="maps-list" class="list-group"></ul>
                    <button id="show-map-bin-btn" class="btn btn-info mt-3">Show Map Bin</button>
				</div>
			</div>
        </div>

        <!-- Map Container -->
        <div class="col-md-8 col-lg-8" id="map-parent-container" style="padding: 0">
            <div id="map-container">        
				<img id="map-image">
                <div class="grid-overlay"></div>
            </div>
        </div>
	
		<!-- Right Sidebar -->
        <div class="col-md-2 col-md-2 sidebar_right sidebar_custom">
			<div id="status-effects-container" style="display: none;"><h4>Status Effects</h4></div>
			<div id="delete-button-container" style="display: none;">
				<button id="remove-object-btn" class="btn btn-danger mt-2">Remove Object</button>
			</div>
			<div id="size-button-container" style="display: none;">
				<button id="increase-size-btn" class="btn btn-danger mt-2">Size+</button>
				<button id="decrease-size-btn" class="btn btn-danger mt-2">Size-</button>
			</div> 
			<div id="rotation-button-container" style="display: none;">
				<button id="rotate-left-btn" class="btn btn-danger mt-2">Rotate -90°</button>
				<button id="rotate-right-btn" class="btn btn-danger mt-2">Rotate +90°</button>
			</div>
			<div id="counter-container" style="display: none;">
				<h4>Duplicate Counter</h4>
				<div class="counter-controls">
					<button id="decrease-counter-btn" class="btn btn-secondary mt-2">-</button>
					<span id="counter-display" class="mx-3" style="font-size: 1.5rem;">1</span>
					<button id="increase-counter-btn" class="btn btn-secondary mt-2">+</button>
				</div>
			</div>


            <div id="reset-view-container" style="display: block; margin-top: 20px;">
                <button id="reset-view-btn" class="btn btn-primary">Reset View</button>
            </div>
		</div>
	</div>

	<!-- Bin Modal -->
	<div class="modal fade" id="binModal" tabindex="-1" aria-labelledby="binModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="binModalLabel">Binned Objects</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<button id="restore-selected-bin" class="btn btn-success">Restore Selected</button>
						<button id="delete-selected-bin" class="btn btn-danger">Delete Selected</button>
						<button id="select-all-bin" class="btn btn-secondary">Select All</button>
					</div>
					<table class="table table-striped">
						<thead>
							<tr><th><input type="checkbox" id="select-all-bin-checkbox"></th><th>Name</th><th>Position (X, Y)</th><th>Size</th><th>Duplicate Count</th></tr>
						</thead>
						<tbody id="bin-list-body"></tbody>
					20~</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

    <!-- Map Bin Modal -->
<div class="modal fade" id="mapBinModal" tabindex="-1" aria-labelledby="mapBinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Binned Maps</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button id="restore-selected-map-bin" class="btn btn-success">Restore Selected</button>
                    <button id="delete-selected-map-bin" class="btn btn-danger">Delete Selected</button>
                    <button id="select-all-map-bin" class="btn btn-secondary">Select All</button>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr><th><input type="checkbox" id="select-all-map-bin-checkbox"></th><th>Name</th><th>Image URL</th></tr>
                    </thead>
                    <tbody id="map-bin-list-body"></tbody>
                20~
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script>
// ========================= CONSTANTS & GLOBALS =========================
const statusEffectsLinks = {
    'Acid': 'https://cdn0.iconfinder.com/data/icons/poison-symbol/66/22-512.png',
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

let activeSocket;
let gridSize, vGridCellNumber, mapId, mapImage;
let allMaps = new Map();
let allObjects = new Map();
let selectedObjectId = null;
let mapRect, mapOffset;
let scale = 1;
const minZoom = 0.5, maxZoom = 3, zoomStep = 0.1;
let offsetX = 0, offsetY = 0;

// ========================= HELPER FUNCTIONS =========================
// ---- Reset view to center the map ----
function resetView() {
    const parent = document.getElementById('map-parent-container');
    const mapContainer = document.getElementById('map-container');
    const mapImg = document.getElementById('map-image');
    if (!parent || !mapContainer) return;

    // Ensure the map image is fully loaded to get correct natural dimensions
    if (!mapImg.complete || mapImg.naturalWidth === 0) {
        mapImg.addEventListener('load', function onLoad() {
            resetView(); // retry after load
            mapImg.removeEventListener('load', onLoad);
        });
        return;
    }

    // Temporarily remove any transform to get the true, unmodified size of the container
    const oldTransform = mapContainer.style.transform;
    mapContainer.style.transform = 'none';

    // Get parent dimensions
    const parentRect = parent.getBoundingClientRect();
    // Get map container's natural (unscaled) dimensions after resetting transform
    const mapRect = mapContainer.getBoundingClientRect();

    // Reset scale to 1
    scale = 1;

    // Calculate offsets to center the map
    offsetX = (parentRect.width - mapRect.width) / 2;
    offsetY = (parentRect.height - mapRect.height) / 2;

    // Apply new transform (scale=1)
    mapContainer.style.transform = `translate(${offsetX}px, ${offsetY}px) scale(${scale})`;
    mapContainer.style.transformOrigin = '0 0';
}


function getActiveSocket() {
    if (!activeSocket || activeSocket.readyState === 3) {
        activeSocket = new WebSocket('ws://localhost:8080');
    }
    return activeSocket;
}

// ---- Selection management ----
function clearObjectSelection() {
    $('#object-list li').removeClass('selected');
    $('.draggable-container').removeClass('selected').css('z-index', 1);
    if (selectedObjectId) {
        interact(`.draggable-container[data-id="${selectedObjectId}"]`).draggable(false);
    }
    interact('.draggable-container').draggable(true);
    $('#status-effects-container, #delete-button-container, #size-button-container, #rotation-button-container, #counter-container')
        .css('display', 'none');
    $('#status-effects-container div').removeClass('selected-effects-box');
    selectedObjectId = null;
}

function selectObject(objectId) {
    if (selectedObjectId === objectId) return;
    clearObjectSelection();
    selectedObjectId = objectId;
    interact(`.draggable-container[data-id="${objectId}"]`).draggable(true);
    $(`.draggable-container[data-id="${objectId}"]`).addClass('selected').css('z-index', 9999);
    $(`#object-list li[data-id="${objectId}"]`).addClass('selected');
    $('#status-effects-container, #delete-button-container, #size-button-container, #rotation-button-container, #counter-container')
        .css('display', 'block');
    $('#counter-display').text(allObjects[objectId].duplicate_count || 1);
    // highlight active effects
    allObjects[objectId].statusEffects.forEach(effect => {
        $(`#effect-${effect}`).addClass('selected-effects-box');
    });
}

// ---- Rendering templates ----
function renderObjectListItem(obj) {
    return `<li class="list-group-item" data-id="${obj.id}" data-url="${obj.image_url}">
                ${obj.name}
                <span class="bin-object" data-id="${obj.id}" style="float:right; cursor:pointer;">🗑️</span>
            </li>`;
}

function renderObjectContainer(obj, gridSize) {
    return `<div class="draggable-container" style="position: absolute; width:${gridSize*obj.size}px; height:${gridSize*obj.size}px; left:${gridSize*obj.positionX}px; top:${gridSize*obj.positionY}px;" data-id="${obj.id}" id="${obj.id}">
                <img src="${obj.image_url}" class="draggable" data-id="${obj.id}" style="width:100%; height:100%; transform: rotate(${obj.rotation || 0}deg);">
                <div class="status-effects-indicator" style="position:absolute; bottom:100%; display:none; gap:5%; background:brown; justify-content:space-between;"></div>
                <div class="duplicate-counter" style="position:absolute; top:100%; left:0; right:0; text-align:center; background:rgba(0,0,0,0.7); color:white; font-size:12px; font-weight:bold; border-radius:0 0 5px 5px; display:none;">
                    x<span class="count">${obj.duplicate_count || 1}</span>
                </div>
            </div>`;
}

// ---- Grid & geometry ----
function roundDownToMultiple(num, multiple) {
    return Math.floor(num / multiple) * multiple;
}

function adjustMapSize() {
    const mapContainer = document.getElementById('map-container');
    const mapImg = document.getElementById('map-image');
    const parent = document.getElementById('map-parent-container');
    if (!mapImg.complete || mapImg.naturalWidth === 0) {
        mapImg.addEventListener('load', adjustMapSize, { once: true });
        return;
    }
    const parentW = parent.clientWidth, parentH = parent.clientHeight;
    const imgW = mapImg.naturalWidth, imgH = mapImg.naturalHeight;
    const imgAspect = imgW / imgH;
    let containerW, containerH;
    if (imgAspect > parentW / parentH) {
        containerW = parentW;
        containerH = containerW / imgAspect;
    } else {
        containerH = parentH;
        containerW = containerH * imgAspect;
    }
    const cellCountY = vGridCellNumber;
    let gridPx = Math.floor(containerH / cellCountY);
    if (gridPx < 1) gridPx = 1;
    containerH = gridPx * cellCountY;
    containerW = containerH * imgAspect;
    containerW = Math.floor(containerW / gridPx) * gridPx;
    mapContainer.style.width = containerW + 'px';
    mapContainer.style.height = containerH + 'px';
    mapImg.style.width = '100%';
    mapImg.style.height = '100%';
    mapImg.style.objectFit = 'cover';
    gridSize = gridPx;
    document.querySelector('.grid-overlay').style.backgroundSize = gridSize + 'px ' + gridSize + 'px';
    const rect = mapContainer.getBoundingClientRect();
    mapOffset = { left: rect.left, top: rect.top };
    document.querySelectorAll('.draggable-container').forEach(container => {
        const obj = allObjects[container.dataset.id];
        if (obj) {
            container.style.width = gridSize * obj.size + 'px';
            container.style.height = gridSize * obj.size + 'px';
        }
    });
    initializeDraggables(gridSize);
    snapAllObjectsToGrid(true);
}

function snapAllObjectsToGrid(sendUpdates = true) {
    const mapContainer = document.getElementById('map-container');
    const containerW = mapContainer.clientWidth, containerH = mapContainer.clientHeight;
    document.querySelectorAll('.draggable-container').forEach(container => {
        const id = container.dataset.id;
        if (!id || !allObjects[id]) return;
        const obj = allObjects[id];
        const sizeInCells = obj.size;
        let cellX = obj.positionX, cellY = obj.positionY;
        const maxCellX = Math.floor(containerW / gridSize) - sizeInCells;
        const maxCellY = Math.floor(containerH / gridSize) - sizeInCells;
        let newX = Math.min(Math.max(cellX, 0), maxCellX);
        let newY = Math.min(Math.max(cellY, 0), maxCellY);
        if (newX !== cellX || newY !== cellY) {
            obj.positionX = newX;
            obj.positionY = newY;
            if (sendUpdates) {
                getActiveSocket().send(JSON.stringify({
                    action: 'updatePosition',
                    objectId: id,
                    positionX: newX,
                    positionY: newY
                }));
            }
        }
        container.style.left = (newX * gridSize) + 'px';
        container.style.top = (newY * gridSize) + 'px';
        container.style.transform = 'none';
        container.setAttribute('data-x', 0);
        container.setAttribute('data-y', 0);
    });
}

function updateGridSize(newSize, sendUpdates = true) {
    gridSize = newSize;
    allMaps[mapId].grid_size = newSize;
    $('.grid-overlay').css('background-size', `${gridSize}px ${gridSize}px`);
    document.querySelectorAll('.draggable-container').forEach(container => {
        const id = parseInt(container.id);
        container.style.width = gridSize * allObjects[id].size + "px";
        container.style.height = gridSize * allObjects[id].size + "px";
    });
    adjustMapSize();
    const mc = document.getElementById('map-container');
    mapOffset = { left: mc.getBoundingClientRect().left, top: mc.getBoundingClientRect().top };
    snapAllObjectsToGrid(sendUpdates);
    interact('.draggable-container').off('dragmove dragend');
    const mapImg = document.getElementById('map-image');
    if (mapImg.complete && mapImg.naturalWidth !== 0) {
        initializeDraggables(gridSize);
    } else {
        $("#map-image").one("load", () => initializeDraggables(gridSize));
    }
    if (selectedObjectId !== null) {
        interact('.draggable-container').draggable(false);
        interact(`.draggable-container[data-id="${selectedObjectId}"]`).draggable(true);
    }
}

// ---- Draggable setup ----
function dragMoveListener(event) {
    const target = event.target;
    let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx / scale;
    let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy / scale;
    target.style.transform = `translate(${x}px, ${y}px)`;
    target.setAttribute('data-x', x);
    target.setAttribute('data-y', y);
}

function initializeDraggables(gridSize, filterSelector = '.draggable-container') {
    interact(filterSelector).draggable({
        inertia: false,
        modifiers: [
            interact.modifiers.restrictRect({ restriction: 'parent', endOnly: true }),
            interact.modifiers.snap({
                targets: [(x, y) => {
                    const rect = document.getElementById('map-container').getBoundingClientRect();
                    const spacing = gridSize * scale;
                    const snapX = Math.round((x - rect.left) / spacing) * spacing + rect.left;
                    const snapY = Math.round((y - rect.top) / spacing) * spacing + rect.top;
                    return { x: snapX, y: snapY };
                }],
                range: Infinity,
                relativePoints: [{ x: 0, y: 0 }]
            })
        ],
        autoScroll: true,
        listeners: {
            move: dragMoveListener,
            end: function(event) {
                const target = event.target;
                const img = target.querySelector('img');
                const objectId = img.dataset.id;
                const origLeft = parseFloat(target.style.left) || 0;
                const origTop = parseFloat(target.style.top) || 0;
                const transX = parseFloat(target.getAttribute('data-x')) || 0;
                const transY = parseFloat(target.getAttribute('data-y')) || 0;
                const newLeft = origLeft + transX;
                const newTop = origTop + transY;
                const snappedLeft = Math.round(newLeft / gridSize) * gridSize;
                const snappedTop = Math.round(newTop / gridSize) * gridSize;
                const cellX = Math.round(snappedLeft / gridSize);
                const cellY = Math.round(snappedTop / gridSize);
                getActiveSocket().send(JSON.stringify({
                    action: 'updatePosition',
                    objectId: objectId,
                    positionX: cellX,
                    positionY: cellY
                }));
                target.style.left = snappedLeft + 'px';
                target.style.top = snappedTop + 'px';
                target.style.transform = 'none';
                target.setAttribute('data-x', 0);
                target.setAttribute('data-y', 0);
                if (allObjects[objectId]) {
                    allObjects[objectId].positionX = cellX;
                    allObjects[objectId].positionY = cellY;
                }
            }
        }
    });
}

// ---- Status effects UI ----
function updateEffectsLegend(objectId, updateAll = false) {
    const updateOne = (id) => {
        const effects = allObjects[id].statusEffects;
        const $indicator = $(`.draggable-container#${id} div.status-effects-indicator`);
        if (effects.length === 0) {
            $indicator.hide().empty();
            return;
        }
        $indicator.show().css({
            width: effects.length === 1 ? '30%' : effects.length === 2 ? '65%' : '100%',
            height: '30%',
            left: effects.length === 1 ? '35%' : effects.length === 2 ? '17%' : '0%'
        });
        const icons = effects.slice(0, 3).map(e => `<div style="flex:1; height:100%; background-size:contain; background-repeat:no-repeat; background-position:center; background-image:url(${statusEffectsLinks[e]});"></div>`);
        if (effects.length > 3) {
            icons.push(`<div style="flex:1; display:flex; justify-content:center; align-items:center; font-size:${Math.trunc(gridSize*0.2)}px;">...</div>`);
        }
        $indicator.html(icons.join(''));
        $indicator.find('div').height(Math.trunc(gridSize*0.3)).width(Math.trunc(gridSize*0.3));
    };
    if (updateAll) Object.keys(allObjects).forEach(updateOne);
    else updateOne(objectId);
}

function updateCounterDisplay(objectId, count) {
    $(`#${objectId} .duplicate-counter .count`).text(count);
    const $container = $(`#${objectId} .duplicate-counter`);
    $container.css('display', count > 1 ? 'block' : 'none');
    if (objectId === selectedObjectId) $('#counter-display').text(count);
}

// ---- WebSocket message handlers (mapped) ----
function handleFirstFetch(data) {
    console.log('firstFetchReturn', data);
    vGridCellNumber = data.maps[0].grid_size;
    mapId = data.maps[0].id;
    mapImage = data.maps[0].image;
    allMaps = Object.fromEntries(data.maps.map(m => [m.id, { image: m.image, name: m.name, grid_size: m.grid_size }]));
    allObjects = Object.fromEntries(data.objects.map(obj => [obj.id, {
        image_url: obj.image_url, name: obj.name, positionX: obj.positionX, positionY: obj.positionY,
        id: obj.id, statusEffects: obj.statusEffects, rotation: obj.rotation || 0,
        size: obj.size, duplicate_count: obj.duplicate_count || 1
    }]));
    redrawMap();
    adjustMapSize();
    redrawAllObjects();
}

function handlePositionUpdate(data) {
    document.querySelectorAll('.draggable-container').forEach(container => {
        if (container.querySelector('img').dataset.id === data.objectId.toString()) {
            container.style.left = (gridSize * data.positionX) + 'px';
            container.style.top = (gridSize * data.positionY) + 'px';
            container.style.transform = 'none';
            container.setAttribute('data-x', 0);
            container.setAttribute('data-y', 0);
            if (allObjects[data.objectId]) {
                allObjects[data.objectId].positionX = data.positionX;
                allObjects[data.objectId].positionY = data.positionY;
            }
        }
    });
}

function handleGridSizeUpdate(data) {
    if (data.gridSize !== vGridCellNumber) {
        vGridCellNumber = data.gridSize;
        $('#grid-size-input').val(vGridCellNumber);
        updateGridSize(vGridCellNumber, false);
    }
}

function handleSizeUpdate(data) {
    allObjects[data.objectId].size = data.newSize;
    $(`.draggable-container[id="${data.objectId}"]`).css({ width: gridSize*data.newSize + 'px', height: gridSize*data.newSize + 'px' })
        .css({ transform: 'none' }).attr({ 'data-x': 0, 'data-y': 0 });
}

function handleRotationUpdate(data) {
    allObjects[data.objectId].rotation = data.newRotation;
    $(`#${data.objectId} img`).css('transform', `rotate(${data.newRotation}deg)`);
}

function handleDuplicateUpdate(data) {
    allObjects[data.objectId].duplicate_count = data.duplicateCount;
    updateCounterDisplay(data.objectId, data.duplicateCount);
}

function handleObjectAdded(data) {
    const obj = data.object;
    allObjects[obj.id] = {
        id: obj.id, name: obj.name, image_url: obj.image_url,
        positionX: obj.positionX || 0, positionY: obj.positionY || 0,
        statusEffects: obj.statusEffects || [], size: obj.size || 1,
        duplicate_count: obj.duplicate_count || 1, rotation: obj.rotation || 0
    };
    $('#object-list').append(renderObjectListItem(obj));
    $('#map-container').append(renderObjectContainer(obj, gridSize));
    addClickHandlersOnObjectList();
    initializeDraggables(gridSize, `.draggable-container[data-id="${obj.id}"]`);
}

function handleObjectRemoved(data) {
    $(`.list-group-item[data-id=${data.id}]`).remove();
    $(`.draggable-container[data-id=${data.id}]`).remove();
    delete allObjects[data.id];
    clearObjectSelection();
}

function handleEffectsUpdate(data) {
    allObjects[data.objectId].statusEffects = data.statusEffects;
    updateEffectsLegend(data.objectId);
}

function handleMapAdded(data) {
    const map = data.map1;
    allMaps[map.id] = { image: map.image, name: map.name, grid_size: map.grid_size };
    gridSize = map.grid_size;
    mapId = map.id;
    mapImage = map.image;
    redrawMap();
    updateGridSize(gridSize);
}

function handleMapDeleted(data) {
    const delId = data.id;
    delete allMaps[delId];
    $(`#maps-list li[data-id="${delId}"]`).remove();
    if (delId == mapId) {
        const remaining = Object.keys(allMaps);
        if (remaining.length) {
            getActiveSocket().send(JSON.stringify({ action: 'switchMap', selectedId: remaining[0] }));
        } else {
            $('#map-image').attr('src', '');
            $('.grid-overlay').css('background-size', '0 0');
            $('#grid-size-input').val('');
            mapId = null; mapImage = ''; gridSize = 0;
        }
    }
}

function handleMapSwitched(data) {
    mapId = data.selected_map_id;
    gridSize = allMaps[mapId].grid_size;
    mapImage = allMaps[mapId].image;
    redrawMap();
    updateGridSize(gridSize);
}

function handleBinList(data) {
    const tbody = $('#bin-list-body').empty();
    data.objects.forEach(item => {
        const state = item.object_state;
        tbody.append(`<tr data-bin-id="${item.id}">
            <td><input type="checkbox" class="bin-select" value="${item.id}"></td>
            <td>${state.name}</td><td>(${state.positionX}, ${state.positionY})</td>
            <td>${state.size}</td><td>${state.duplicate_count}</td>
        </tr>`);
    });
    $('#binModal').modal('show');
}

function handleMapBinList(data) {
    const tbody = $('#map-bin-list-body').empty();
    data.maps.forEach(item => {
        tbody.append(`
            <tr data-bin-id="${item.id}">
                <td><input type="checkbox" class="map-bin-select" value="${item.id}"></td>
                <td>${escapeHtml(item.name)}</td>
                <td><a href="${item.image}" target="_blank">${escapeHtml(item.image.substring(0, 60))}...</a></td>
            </tr>
        `);
    });
    $('#mapBinModal').modal('show');
}

// ---- Redraw functions ----
function redrawMap() {
    $("#grid-size-input").val(vGridCellNumber);
    $("#map-image").attr("src", mapImage);
    $('#maps-list').empty();
    for (const [key, val] of Object.entries(allMaps)) {
        const selectedClass = (mapId == key) ? 'selected' : '';
        $('#maps-list').append(`
            <li class="list-group-item ${selectedClass}" data-id="${key}" data-url="${val.image}">
                ${escapeHtml(val.name)}
                <span class="bin-map" data-id="${key}" style="float:right; cursor:pointer;">🗑️</span>
            </li>
        `);
    }
    $("#map-image").off("load.redraw").on("load.redraw", () => {
        adjustMapSize();
        const rect = document.getElementById('map-container').getBoundingClientRect();
        mapRect = rect;
        mapOffset = { left: rect.left, top: rect.top };
    });
    addClickHandlersOnMapsList(); // already handles map switching
}

function redrawAllObjects() {
    // Clear only the object list and existing draggable containers (keep map image and grid overlay)
    $('#object-list').empty();
    $('.draggable-container').remove();
    for (const obj of Object.values(allObjects)) {
        $('#object-list').append(renderObjectListItem(obj));
        $('#map-container').append(renderObjectContainer(obj, gridSize));
    }
    $("#map-image").off("load.objects").on("load.objects", () => {
        const rect = document.getElementById('map-container').getBoundingClientRect();
        mapOffset = { left: rect.left, top: rect.top };
        initializeDraggables(gridSize);
        updateEffectsLegend(0, true);
    });
    $('#grid-size-input').off('input').on('input', function() {
        const newVal = parseInt($(this).val(), 10);
        if (!isNaN(newVal) && newVal > 0 && newVal !== vGridCellNumber) {
            vGridCellNumber = newVal;
            adjustMapSize();
            snapAllObjectsToGrid(true);
            getActiveSocket().send(JSON.stringify({ action: 'updateGridSize', mapId, gridSize: vGridCellNumber }));
        }
    });
    addClickHandlersOnObjectList();
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// ---- Click handlers ----
function addClickHandlersOnObjectList() {
    $('#object-list li').off('click').on('click', function() {
        const id = $(this).data('id');
        if ($(this).hasClass('selected')) clearObjectSelection();
        else selectObject(id);
    });
}

function addClickHandlersOnMapsList() {
    $('#maps-list li').off('click').on('click', function() {
        const id = $(this).data('id');
        if (!$(this).hasClass('selected')) {
            getActiveSocket().send(JSON.stringify({ action: 'switchMap', selectedId: id }));
        }
    });
}

// ---- Zoom & Pan (encapsulated) ----
function initZoomPan() {
    const zoomElement = document.getElementById("map-container");
    const zoomContainer = document.getElementById("map-parent-container");
    zoomContainer.addEventListener("wheel", function(e) {
        e.preventDefault();
        const rect = zoomContainer.getBoundingClientRect();
        const mouseX = e.clientX - rect.left, mouseY = e.clientY - rect.top;
        const direction = e.deltaY > 0 ? -1 : 1;
        const newScale = scale * (1 + direction * zoomStep);
        if (newScale < minZoom || newScale > maxZoom) return;
        const mouseXUnscaled = (mouseX - offsetX) / scale;
        const mouseYUnscaled = (mouseY - offsetY) / scale;
        offsetX = mouseX - mouseXUnscaled * newScale;
        offsetY = mouseY - mouseYUnscaled * newScale;
        scale = newScale;
        zoomElement.style.transform = `translate(${offsetX}px, ${offsetY}px) scale(${scale})`;
        zoomElement.style.transformOrigin = '0 0';
    });
    let isPanning = false, startX, startY, initOffX, initOffY;
    zoomContainer.addEventListener('mousedown', (e) => {
        if (e.target.closest('.draggable-container') || e.target.closest('.draggable')) return;
        isPanning = true;
        startX = e.clientX; startY = e.clientY;
        initOffX = offsetX; initOffY = offsetY;
        zoomContainer.style.cursor = 'grabbing';
    });
    window.addEventListener('mousemove', (e) => {
        if (!isPanning) return;
        offsetX = initOffX + (e.clientX - startX);
        offsetY = initOffY + (e.clientY - startY);
        zoomElement.style.transform = `translate(${offsetX}px, ${offsetY}px) scale(${scale})`;
    });
    window.addEventListener('mouseup', () => {
        if (isPanning) {
            isPanning = false;
            zoomContainer.style.cursor = 'grab';
        }
    });
}

// ========================= DOCUMENT READY =========================
$(document).ready(function() {
    // Setup WebSocket message router
    const wsHandlers = {
        firstFetchReturn: handleFirstFetch,
        positionUpdated: handlePositionUpdate,
        gridSizeUpdated: handleGridSizeUpdate,
        sizeUpdated: handleSizeUpdate,
        rotationUpdated: handleRotationUpdate,
        duplicateCountUpdated: handleDuplicateUpdate,
        objectAdded: handleObjectAdded,
        ObjectRemoved: handleObjectRemoved,
        effectsUpdated: handleEffectsUpdate,
        MapAdded: handleMapAdded,
        mapDeleted: handleMapDeleted,
        mapSwitched: handleMapSwitched,
        binList: handleBinList,
        mapBinList: handleMapBinList  
    };
    getActiveSocket().onmessage = (event) => {
        const data = JSON.parse(event.data);
        const handler = wsHandlers[data.action];
        if (handler) handler(data);
    };
    getActiveSocket().onopen = () => getActiveSocket().send(JSON.stringify({ action: 'firstFetch' }));

    // Build status effects palette
    const effectsContainer = document.getElementById('status-effects-container');
    for (const [name, url] of Object.entries(statusEffectsLinks)) {
        effectsContainer.innerHTML += `<div class="square-effects-menu" id="effect-${name}" style="background-image: url(${url})" title="${name}"></div>`;
    }
    $('#status-effects-container div').on('click', function() {
        const effectId = this.id.replace("effect-", "");
        if (!selectedObjectId) return;
        const obj = allObjects[selectedObjectId];
        if (obj.statusEffects.includes(effectId)) {
            obj.statusEffects = obj.statusEffects.filter(e => e !== effectId);
            $(this).removeClass('selected-effects-box');
        } else {
            obj.statusEffects.push(effectId);
            $(this).addClass('selected-effects-box');
        }
        getActiveSocket().send(JSON.stringify({ action: 'updateEffects', objectId: selectedObjectId, statusEffects: obj.statusEffects }));
        updateEffectsLegend(selectedObjectId);
    });

    // Sidebar menu toggle
    function toggleMenu(menu) {
        const isObjects = menu === 'objects';
        $('#objects-menu-button').toggleClass('selected-blue-button unselected-blue-button', isObjects);
        $('#maps-menu-button').toggleClass('selected-blue-button unselected-blue-button', !isObjects);
        $('#objects-menu-body').css('display', isObjects ? 'block' : 'none');
        $('#maps-menu-body').css('display', isObjects ? 'none' : 'block');
    }
    $('#objects-menu-button').on('click', () => toggleMenu('objects'));
    $('#maps-menu-button').on('click', () => toggleMenu('maps'));
    toggleMenu('objects');

    // Forms
    $('#addObjectForm').on('submit', function(e) {
        e.preventDefault();
        getActiveSocket().send(JSON.stringify({
            action: 'addObject',
            name: $('#form_object_name').val(),
            image_url: $('#form_object_image_url').val()
        }));
        this.reset();
    });
    $('#addMapForm').on('submit', function(e) {
        e.preventDefault();
        getActiveSocket().send(JSON.stringify({
            action: 'addMap',
            name: $('#form_map_name').val(),
            image_url: $('#form_map_image_url').val(),
            grid_size: gridSize
        }));
        this.reset();
    });

    // Object manipulation buttons
    $('#remove-object-btn').on('click', () => {
        if (selectedObjectId) {
            getActiveSocket().send(JSON.stringify({ action: 'removeObject', id: selectedObjectId }));
        }
    });
    $('#increase-size-btn').on('click', () => {
        if (selectedObjectId) {
            const newSize = allObjects[selectedObjectId].size + 1;
            allObjects[selectedObjectId].size = newSize;
            getActiveSocket().send(JSON.stringify({ action: 'updateSize', objectId: selectedObjectId, newSize }));
            $(`.draggable-container[data-id="${selectedObjectId}"]`).css({ width: `${gridSize*newSize}px`, height: `${gridSize*newSize}px` });
        }
    });
    $('#decrease-size-btn').on('click', () => {
        if (selectedObjectId) {
            const newSize = allObjects[selectedObjectId].size - 1;
            if (newSize < 1) return;
            allObjects[selectedObjectId].size = newSize;
            getActiveSocket().send(JSON.stringify({ action: 'updateSize', objectId: selectedObjectId, newSize }));
            $(`.draggable-container[data-id="${selectedObjectId}"]`).css({ width: `${gridSize*newSize}px`, height: `${gridSize*newSize}px` });
        }
    });
    $('#rotate-left-btn').on('click', () => {
        if (selectedObjectId) {
            const newRot = (allObjects[selectedObjectId].rotation - 90) % 360;
            allObjects[selectedObjectId].rotation = newRot;
            getActiveSocket().send(JSON.stringify({ action: 'updateRotation', objectId: selectedObjectId, newRotation: newRot }));
            $(`#${selectedObjectId} img`).css('transform', `rotate(${newRot}deg)`);
        }
    });
    $('#rotate-right-btn').on('click', () => {
        if (selectedObjectId) {
            const newRot = (allObjects[selectedObjectId].rotation + 90) % 360;
            allObjects[selectedObjectId].rotation = newRot;
            getActiveSocket().send(JSON.stringify({ action: 'updateRotation', objectId: selectedObjectId, newRotation: newRot }));
            $(`#${selectedObjectId} img`).css('transform', `rotate(${newRot}deg)`);
        }
    });
    $('#increase-counter-btn').on('click', () => {
        if (selectedObjectId) {
            const newCount = (allObjects[selectedObjectId].duplicate_count || 1) + 1;
            allObjects[selectedObjectId].duplicate_count = newCount;
            getActiveSocket().send(JSON.stringify({ action: 'updateDuplicateCount', objectId: selectedObjectId, duplicateCount: newCount }));
            updateCounterDisplay(selectedObjectId, newCount);
        }
    });
    $('#decrease-counter-btn').on('click', () => {
        if (selectedObjectId) {
            let newCount = (allObjects[selectedObjectId].duplicate_count || 1) - 1;
            if (newCount < 1) return;
            allObjects[selectedObjectId].duplicate_count = newCount;
            getActiveSocket().send(JSON.stringify({ action: 'updateDuplicateCount', objectId: selectedObjectId, duplicateCount: newCount }));
            updateCounterDisplay(selectedObjectId, newCount);
        }
    });

    // Bin interactions
    $('#object-list').on('click', '.bin-object', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        if (confirm('Move this object to the bin?')) {
            getActiveSocket().send(JSON.stringify({ action: 'binObject', id }));
        }
    });
    $('#show-bin-btn').on('click', () => getActiveSocket().send(JSON.stringify({ action: 'fetchBinList' })));
    $('#select-all-bin-checkbox').on('change', function() { $('.bin-select').prop('checked', $(this).is(':checked')); });
    $('#bin-list-body').on('change', '.bin-select', function() {
        const allChecked = $('.bin-select:checked').length === $('.bin-select').length;
        $('#select-all-bin-checkbox').prop('checked', allChecked);
    });
    $('#select-all-bin').on('click', function() {
        const check = !$('#select-all-bin-checkbox').is(':checked');
        $('.bin-select').prop('checked', check);
        $('#select-all-bin-checkbox').prop('checked', check);
    });
    $('#restore-selected-bin').on('click', function() {
        const ids = $('.bin-select:checked').map((_, el) => $(el).val()).get();
        if (ids.length && confirm(`Restore ${ids.length} object(s)?`)) {
            getActiveSocket().send(JSON.stringify({ action: 'restoreBinObjects', ids }));
            $('#binModal').modal('hide');
        }
    });
    $('#delete-selected-bin').on('click', function() {
        const ids = $('.bin-select:checked').map((_, el) => $(el).val()).get();
        if (ids.length && confirm(`Permanently delete ${ids.length} object(s)?`)) {
            getActiveSocket().send(JSON.stringify({ action: 'deleteBinObjects', ids }));
            ids.forEach(id => $(`tr[data-bin-id="${id}"]`).remove());
        }
    });
    $('#maps-list').on('click', '.delete-map', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        if (confirm('Delete this map permanently?')) {
            getActiveSocket().send(JSON.stringify({ action: 'deleteMap', id }));
        }
    });

    // --- Map Bin actions (move to bin, permanent delete) ---
$('#maps-list').on('click', '.bin-map', function(e) {
    e.stopPropagation();
    const id = $(this).data('id');
    if (confirm('Move this map to the bin? It can be restored later.')) {
        getActiveSocket().send(JSON.stringify({ action: 'binMap', id }));
    }
});


// --- Show Map Bin button ---
$('#show-map-bin-btn').on('click', () => {
    getActiveSocket().send(JSON.stringify({ action: 'fetchMapBinList' }));
});

// --- Map Bin Modal handlers ---
$('#select-all-map-bin-checkbox').on('change', function() {
    $('.map-bin-select').prop('checked', $(this).is(':checked'));
});
$('#map-bin-list-body').on('change', '.map-bin-select', function() {
    const allChecked = $('.map-bin-select:checked').length === $('.map-bin-select').length;
    $('#select-all-map-bin-checkbox').prop('checked', allChecked);
});
$('#select-all-map-bin').on('click', function() {
    const check = !$('#select-all-map-bin-checkbox').is(':checked');
    $('.map-bin-select').prop('checked', check);
    $('#select-all-map-bin-checkbox').prop('checked', check);
});
$('#restore-selected-map-bin').on('click', function() {
    const ids = $('.map-bin-select:checked').map((_, el) => $(el).val()).get();
    if (ids.length && confirm(`Restore ${ids.length} map(s)? They will reappear in the Maps list.`)) {
        getActiveSocket().send(JSON.stringify({ action: 'restoreMapBinObjects', ids }));
        $('#mapBinModal').modal('hide');
    }
});
$('#delete-selected-map-bin').on('click', function() {
    const ids = $('.map-bin-select:checked').map((_, el) => $(el).val()).get();
    if (ids.length && confirm(`Permanently delete ${ids.length} map(s) from the bin? This cannot be undone.`)) {
        getActiveSocket().send(JSON.stringify({ action: 'deleteMapBinObjects', ids }));
        ids.forEach(id => $(`tr[data-bin-id="${id}"]`).remove());
        // Refresh the modal list after deletion
        getActiveSocket().send(JSON.stringify({ action: 'fetchMapBinList' }));
    }
});

$('#reset-view-btn').on('click', function() {
    resetView();
});

    initZoomPan();
});
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>