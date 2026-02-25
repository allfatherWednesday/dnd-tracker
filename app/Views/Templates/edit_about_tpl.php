<?php include_once 'partials/editor_top_tpl.php'; ?>
<div class="content container text-center">
    <div class="float-left d-flex">
        <a href="<?= HOST ?>/" class="btn btn-success"> << Return</a>
    </div>
    <div class="title px-5 mb-4">
        <h2>About</h2>
    </div>
    <div class="about editor">
        <form method="post" class="pb-5">
            <div class="armor-initiative-speed flex-nowrap m-auto pe-1 pt-4 row">
                <div class="col">
                    <input type="text" class="col" name="about[armor]" value="<?= $data['about']['armor'] ?? 0 ?>">
                </div>
                <div class="col">
                    <input type="text" class="col" name="about[initiative]" value="<?= $data['about']['initiative'] ?? 0 ?>">
                </div>
                <div class="col">
                    <input type="text" class="col" name="about[speed]" value="<?= $data['about']['speed'] ?? 0 ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="char_race">Race</label><br>
                    <input type="text" name="about[char_race]" class="w-100 text-center" value="<?= $data['about']['char_race'] ?? '' ?>"/>
                </div>
                <div class="col-md-6">
                    <label for="char_race">Exp</label><br>
                    <input type="text" name="about[char_exp]" class="w-100 text-center" value="<?= $data['about']['char_exp'] ?? 0 ?>"/>
                </div>
            </div>
            <div class="row mb-3">
                <?php foreach ($data['about']['char_class'] as $key => $charClass) { ?>
                    <div class="col-6">
                        <label for="char_class">Class</label><br>
                        <select name="about[char_class][<?= $key ?>][index]" class="w-100 text-center class-selector">
                            <option value="">--- Select a class ---</option>
                            <?php foreach ($data['classes'] as $class) { ?>
                                <option value="<?= $class['index'] ?>"
                                    <?php if (isset($data['about']['char_class'][$key]['index']) && $data['about']['char_class'][$key]['index'] === $class['index']) { echo 'selected'; } ?>
                                ><?= $class['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Level</label><br>
                        <input type="number" name="about[char_class][<?= $key ?>][lvl]" class="w-100 text-center" value="<?= $data['about']['char_class'][$key]['lvl'] ?? 0 ?>"/>
                    </div>
                <?php } ?>

                <div class="btn add-class-btn border-secondary col m-2 mx-3 text-center">
                    Add new class
                </div>

                <template id="add-class-template">
                    <div class="col-6">
                        <label for="char_class">Class</label><br>
                        <select name="about[char_class][0][index]" class="w-100 text-center class-selector">
                            <option value="">--- Select a class ---</option>
                            <?php foreach ($data['classes'] as $class) { ?>
                                <option value="<?= $class['index'] ?>"><?= $class['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Level</label><br>
                        <input type="number" name="about[char_class][0][lvl]" class="w-100 text-center" value="0"/>
                    </div>
                </template>
            </div>
            <div class="mb-3">
                <label for="char_bg">Background</label><br>
                <textarea name="about[char_bg]" class="w-100 resizable" cols="30" rows="5"><?= $data['about']['char_bg'] ?? '' ?></textarea>
            </div>
            <div class="mb-3">
                <label for="char_appearance">Appearance</label><br>
                <textarea name="about[char_appearance]" class="w-100 resizable" cols="30" rows="5"><?= $data['about']['char_appearance'] ?? '' ?></textarea>
            </div>
            <div class="mb-3">
                <label for="char_backstory">Back story</label><br>
                <textarea name="about[char_backstory]" class="w-100 resizable" cols="30" rows="5"><?= $data['about']['char_backstory'] ?? '' ?></textarea>
            </div>
            <button type="submit" class="btn btn-success w-100">Save</button>
        </form>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addClassBtn = document.querySelector('.add-class-btn');
            const template = document.querySelector('#add-class-template');
            let classCount = document.querySelectorAll('.class-selector').length -1; // Don't count the template

            addClassBtn.addEventListener('click', function() {
                // Clone the template
                const clone = document.importNode(template.content, true);

                // Update field keys
                classCount++;
                clone.querySelector('select').name = `about[char_class][${classCount}][index]`;
                clone.querySelector('input').name = `about[char_class][${classCount}][lvl]`;

                // Insert clone before the add button
                addClassBtn.parentNode.insertBefore(clone, addClassBtn);
            });
        });

    </script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>