<?php include_once 'partials/editor_top_tpl.php'; ?>
    <div class="content container text-center">
        <div class="float-left d-flex">
            <a href="<?= HOST ?>/" class="btn btn-success"> << Return</a>
        </div>
        <div class="title px-5 mb-4">
            <h2>Inventory</h2>
        </div>
        <div class="inventory editor">
            <form method="post">
                <div class="justify-content-around mb-3">
                    <div class="mb-3">
                        <label for="char_bg">Equipment</label><br>
                        <ul class="text-start" id='equipment-list'>
                            <?php
                            if (!empty($data['inventory']) && !empty($data['inventory']['equipment'])) {
                                foreach ($data['inventory']['equipment'] as $slot => $equipment) { ?>
                                        <li class="eq-<?= $slot ?>">
                                            [<?= ucfirst($slot) ?>]
                                            <b><?= $equipment['name'] ?></b>:
                                            <?= $equipment['description'] ?>
                                            <span class="text-danger" data-key="<?= $slot ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                </svg>
                                            </span>
                                        </li>
                                <?php }
                            }
                            ?>
                        </ul>
                        <div class="char_equipment flex-nowrap m-auto pe-1 pt-4 row">
                            <div class="col-3 px-0">
                                <label>Type</label><br>
                                <select name="inventory[equipment][type]" class="w-100 text-center">
                                    <option value="">--- Select a slot ---</option>
                                    <?php foreach ($data['equipment_slots'] as $slot) { ?>
                                        <option><?= $slot ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-3">
                                Name <br>
                                <input type="text" class="w-100" name="inventory[equipment][name]" >
                            </div>
                            <div class="col-6 px-0">
                                Description <br>
                                <input type="text" class="w-100" name="inventory[equipment][description]" >
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="char_bg">Items</label><br>
                        <textarea name="inventory[items]"
                                  placeholder="- item 1 (qty) &#10;- item 2 (qty) &#10;- item 3 (qty)"
                                  class="w-100 resizable" cols="30" rows="5"><?= $data['inventory']['items'] ?? '' ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Save</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.querySelector('#equipment-list');

            container.addEventListener('click', async (event) => {
                const target = event.target.closest('.text-danger');
                if (!target) return;

                const slot = target.dataset.key;
                const parentDiv = target.closest(`.eq-${slot}`);

                if (parentDiv) {
                    // Eliminar el div padre
                    parentDiv.remove();

                    // Crear y agregar el input hidden
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `unset[${slot}]`;
                    hiddenInput.value = slot;

                    // Agregar el input hidden fuera del foreach
                    const outsideContainer = document.querySelector('#equipment-list');
                    outsideContainer.appendChild(hiddenInput);
                }
                alert('Please, don\'t forget to save.')
            });
        });

    </script>
<?php include_once 'partials/editor_bottom_tpl.php'; ?>