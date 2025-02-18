<?php include_once 'partials/editor_top_tpl.php'; ?>

<link rel="stylesheet" href="<?= HOST ?>/public/css/map.css">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <!-- ... existing sidebar code ... -->
        </div>

        <!-- Map Container -->
        <div class="col-md-9">
            <div id="map-container" style="position: relative; width: 100%; height: 600px;  margin-left: 350px;">        
                <img id="map-image" src="<?= $data['map']['image'] ?>" style="width: 100%; height: 100%;">
                
                <?php
                $mapObjectModel = new \app\Models\MapObjectModel();
                $objects = $mapObjectModel->getAllObjects();
                foreach ($objects as $object): ?>
                <div class="draggable-container" 
                    style="position: absolute; left: <?= $object['positionX'] ?>px; top: <?= $object['positionY'] ?>px;"
                    data-x="<?= $object['positionX'] ?>" 
                    data-y="<?= $object['positionY'] ?>">
                    <img src="<?= $object['image_url'] ?>" 
                        class="draggable" 
                        data-id="<?= $object['id'] ?>" 
                        style="width: 100%; height: 100%;">
                    <div class="position-text"><?= $object['positionX'] ?>, <?= $object['positionY'] ?></div>
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
        // Get map container offset
        const mapContainer = document.getElementById('map-container');
        const mapRect = mapContainer.getBoundingClientRect();
        const mapOffset = { left: mapRect.left, top: mapRect.top };

        // WebSocket connection
        const ws = new WebSocket('ws://localhost:8080');

        // Handle incoming WebSocket messages
        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            if (data.action === 'positionUpdated') {
                document.querySelectorAll('.draggable-container').forEach(container => {
                    const draggable = container.querySelector('.draggable');
                    if (draggable.dataset.id == data.id) {
                        container.style.transform = `translate(${data.x}px, ${data.y}px)`;
                        container.setAttribute('data-x', data.x);
                        container.setAttribute('data-y', data.y);
                        container.querySelector('.position-text').textContent = 
                            `${Math.round(data.x)}, ${Math.round(data.y)}`;
                    }
                });
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

            const positionText = target.querySelector('.position-text');
            positionText.textContent = `${Math.round(x)}, ${Math.round(y)}`;

            // Send position via WebSocket
            const id = target.querySelector('.draggable').dataset.id;
            ws.send(JSON.stringify({
                action: 'updatePosition',
                id: id,
                x: x,
                y: y
            }));
        }

        // Draggable configuration
        function setupDraggable(element) {
            interact(element).draggable({
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
                                offset: { x: mapOffset.left, y: mapOffset.top }
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
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>