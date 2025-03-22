<?php include_once 'partials/editor_top_tpl.php'; ?>

<link rel="stylesheet" href="<?= HOST ?>/public/css/maps.css">
<style>
    .draggable-container.selected {
        outline: 3px solid #007bff;
        z-index: 1000;
        cursor: move;
    }
    .draggable-container:not(.selected) {
        cursor: not-allowed;
    }
    .list-group-item.selected {
        background: #007bff !important;
        color: white !important;
    }
</style>

<?php $data['grid-size'] = 37 ?>

<div class="container-fluid no-select" style="padding-top: 4px;">
    <div class="row">
        <div class="col-md-2 sidebar_left sidebar_custom">
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
                    <li class="list-group-item selectable-object" 
                        data-id="<?= $object['id'] ?>"
                        data-url="<?= $object['image_url'] ?>">
                        <?= $object['name'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col-md-8" style="padding: 0">
            <div id="map-container">
                <img id="map-image" src="<?= $data['map']['image'] ?>">
                <div class="grid-overlay" style="background-size: <?= $data['grid-size'] ?>px <?= $data['grid-size'] ?>px;"></div>
                
                <?php foreach ($objects as $object): ?>
                    <div class="draggable-container" 
                         style="position: absolute; 
                                width: <?= $data['grid-size']?>px; 
                                height: <?= $data['grid-size']?>px; 
                                left: <?= $object['positionX'] ?>px; 
                                top: <?= $object['positionY'] ?>px;"
                         data-container-id="<?= $object['id'] ?>">
                        <img src="<?= $object['image_url'] ?>" 
                             class="draggable" 
                             data-id="<?= $object['id'] ?>"
                             style="width: 100%; height: 100%;">
                        <div class="position-text">
                            <?= $object['positionX'] ?>, <?= $object['positionY'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-2 sidebar_right sidebar_custom">
            <div class="sidebar-section mb-4">
                <h3>List of Chars</h3>
                <ul class="list-group">
                    <li class="list-group-item">Character 1</li>
                    <li class="list-group-item">Character 2</li>
                </ul>
            </div>
            <div class="sidebar-section mb-4">
                <h3>List of Enemies</h3>
                <ul class="list-group">
                    <li class="list-group-item">Enemy 1</li>
                    <li class="list-group-item">Enemy 2</li>
                </ul>
            </div>
            <div class="sidebar-section">
                <h3>List of Statuses</h3>
                <ul class="list-group">
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
        let selectedObjectId = null;

        $('#object-list').on('click', '.selectable-object', function() {
            const objectId = $(this).data('id');
            $('.selected').removeClass('selected');
            selectedObjectId = objectId;
            $(this).addClass('selected');
            const container = $(`.draggable-container[data-container-id="${objectId}"]`);
            container.addClass('selected');
        });

        const mapContainer = document.getElementById('map-container');
        const mapImage = document.getElementById('map-image');
        
        function adjustMapImageSize(mi) {
            const imageWidth = mi.naturalWidth;
            const imageHeight = mi.naturalHeight;
            mi.style.objectFit = "cover";
            mi.style.height = imageHeight > imageWidth ? '100%' : 'auto';
            mi.style.width = imageHeight > imageWidth ? 'auto' : '100%';
        }

        function setMapContainerSize(container, gridSize) {
            const mapRect = container.getBoundingClientRect();
            container.style.width = (Math.floor(mapRect.width/gridSize)*gridSize)+'px';
            container.style.height = (Math.floor(mapRect.height/gridSize)*gridSize)+'px';
        }

        setMapContainerSize(mapContainer, <?= $data['grid-size']?>);
        adjustMapImageSize(mapImage);

        interact('.draggable-container').draggable({
            enabled: function(event) {
                return event.target.closest('.draggable-container').classList.contains('selected');
            },
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
                                x: mapContainer.getBoundingClientRect().left,
                                y: mapContainer.getBoundingClientRect().top
                            }	
                        })
                    ],
                    range: Infinity,
                    relativePoints: [{ x: 0, y: 0 }]
                })
            ],
            listeners: {
                start: function(event) {
                    if (!event.target.closest('.draggable-container').classList.contains('selected')) {
                        event.stop();
                        event.preventDefault();
                    }
                },
                move: function(event) {
                    const container = event.target.closest('.draggable-container');
                    const x = (parseFloat(container.getAttribute('data-x')) || 0) + event.dx;
                    const y = (parseFloat(container.getAttribute('data-y')) || 0) + event.dy;

                    container.style.transform = `translate(${x}px, ${y}px)`;
                    container.setAttribute('data-x', x);
                    container.setAttribute('data-y', y);

                    const positionText = container.querySelector('.position-text');
                    const originalLeft = parseFloat(container.style.left) || 0;
                    const originalTop = parseFloat(container.style.top) || 0;
                    positionText.textContent = 
                        `${Math.round(originalLeft + x)}, ${Math.round(originalTop + y)}`;
                }
            }
        });

        const websocket = new WebSocket('ws://localhost:8080');
        
        websocket.onmessage = function(event) {
            const data = JSON.parse(event.data);
            if (data.action === 'positionUpdated') {
                const container = document.querySelector(
                    `.draggable-container[data-container-id="${data.objectId}"]`
                );
                if (container) {
                    container.style.left = `${data.positionX}px`;
                    container.style.top = `${data.positionY}px`;
                    container.querySelector('.position-text').textContent = 
                        `${data.positionX}, ${data.positionY}`;
                    container.style.transform = 'none';
                    container.setAttribute('data-x', 0);
                    container.setAttribute('data-y', 0);
                }
            }
        };

        interact('.draggable-container').on('dragend', function(event) {
            const container = event.target.closest('.draggable-container');
            if (!container.classList.contains('selected')) return;

            const objectId = container.dataset.containerId;
            const translateX = parseFloat(container.getAttribute('data-x')) || 0;
            const translateY = parseFloat(container.getAttribute('data-y')) || 0;
            const newX = parseFloat(container.style.left) + translateX;
            const newY = parseFloat(container.style.top) + translateY;

            websocket.send(JSON.stringify({
                action: 'updatePosition',
                objectId: objectId,
                positionX: newX,
                positionY: newY
            }));

            container.style.left = `${newX}px`;
            container.style.top = `${newY}px`;
            container.style.transform = 'none';
            container.setAttribute('data-x', 0);
            container.setAttribute('data-y', 0);
        });

        $('#object-list').on('click', 'li:not(.selectable-object)', function() {
            const imageUrl = $(this).data('url');
            const objectId = $(this).data('id');
            const container = $(`<div class="draggable-container" 
                style="position: absolute; 
                       width: ${<?= $data['grid-size']?>}px; 
                       height: ${<?= $data['grid-size']?>}px; 
                       left: 0; 
                       top: 0;"
                data-container-id="${objectId}">
                <img src="${imageUrl}" 
                     class="draggable" 
                     data-id="${objectId}"
                     style="width: 100%; height: 100%;">
                <div class="position-text">0, 0</div>
            </div>`);

            $('#map-container').append(container);
            
            interact(container[0]).draggable({
                enabled: function(event) {
                    return event.target.closest('.draggable-container').classList.contains('selected');
                },
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
                                    x: mapContainer.getBoundingClientRect().left,
                                    y: mapContainer.getBoundingClientRect().top
                                }	
                            })
                        ],
                        range: Infinity,
                        relativePoints: [{ x: 0, y: 0 }]
                    })
                ],
                listeners: {
                    move: function(event) {
                        const container = event.target.closest('.draggable-container');
                        const x = (parseFloat(container.getAttribute('data-x')) || 0) + event.dx;
                        const y = (parseFloat(container.getAttribute('data-y')) || 0) + event.dy;

                        container.style.transform = `translate(${x}px, ${y}px)`;
                        container.setAttribute('data-x', x);
                        container.setAttribute('data-y', y);

                        const positionText = container.querySelector('.position-text');
                        const originalLeft = parseFloat(container.style.left) || 0;
                        const originalTop = parseFloat(container.style.top) || 0;
                        positionText.textContent = 
                            `${Math.round(originalLeft + x)}, ${Math.round(originalTop + y)}`;
                    }
                }
            });
        });
    });
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>