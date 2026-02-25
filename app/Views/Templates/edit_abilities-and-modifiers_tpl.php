<?php include_once 'partials/editor_top_tpl.php'; ?>
<div class="content container text-center">
    <div class="float-left d-flex">
        <a href="<?= HOST ?>/" class="btn btn-success"> << Return</a>
    </div>
    <div class="title px-5 mb-4">
        <h2>Abilities and modifiers</h2>
    </div>
    <div class="ability-scores editor">
        <form method="post" class="pb-5">
            <div class="row flex-nowrap justify-content-around mb-3">
                <div class="col-4 col-str">
                    <b class="mb-2">Str</b>
                    <input type="number" name="ability[str]" data-type="str" value="<?= $data['abilities']['str'] ?? '10' ?>" min="1" max="20" class="text-center m-3 mt-2" />
                    <span class="modifier"><?= $data['modifiers']['str'] ?? '0' ?></span>
                </div>
                <div class="col-4 col-dex">
                    <b class="mb-2">Dex</b>
                    <input type="number" name="ability[dex]" data-type="dex" value="<?= $data['abilities']['dex'] ?? '10' ?>" min="1" max="20" class="text-center m-3 mt-2" />
                    <span class="modifier"><?= $data['modifiers']['dex'] ?? '0' ?></span>
                </div>
                <div class="col-4 col-con">
                    <b class="mb-2">Con</b>
                    <input type="number" name="ability[con]" data-type="con" value="<?= $data['abilities']['con'] ?? '10' ?>" min="1" max="20" class="text-center m-3 mt-2" />
                    <span class="modifier"><?= $data['modifiers']['con'] ?? '0' ?></span>
                </div>
            </div>
            <div class="row flex-nowrap justify-content-around mb-3">
                <div class="col-4 col-int">
                    <b class="mb-2">Int</b>
                    <input type="number" name="ability[int]" data-type="int" value="<?= $data['abilities']['int'] ?? '10' ?>" min="1" max="20" class="text-center m-3 mt-2" />
                    <span class="modifier"><?= $data['modifiers']['int'] ?? '0' ?></span>
                </div>
                <div class="col-4 col-wis">
                    <b class="mb-2">Wis</b>
                    <input type="number" name="ability[wis]" data-type="wis" value="<?= $data['abilities']['wis'] ?? '10' ?>" min="1" max="20" class="text-center m-3 mt-2" />
                    <span class="modifier"><?= $data['modifiers']['wis'] ?? '0' ?></span>
                </div>
                <div class="col-4 col-cha">
                    <b class="mb-2">Cha</b>
                    <input type="number" name="ability[cha]" data-type="cha" value="<?= $data['abilities']['cha'] ?? '10' ?>" min="1" max="20" class="text-center m-3 mt-2" />
                    <span class="modifier"><?= $data['modifiers']['cha'] ?? '0' ?></span>
                </div>
            </div>
            <button type="submit" class="btn btn-success w-100">Save</button>
        </form>
    </div>
</div>

<script>
    document.querySelector('.ability-scores.editor').addEventListener('change', e => {
        if (e.target.matches('input')) {
            const type = e.target.dataset.type;
            const value = parseInt(e.target.value, 10);
            const modifierMap = {
                1: '-5', 2: '-4', 3: '-4', 4: '-3', 5: '-3', 6: '-2', 7: '-2', 8: '-1', 9: '-1', 10: '0',
                11: '0', 12: '+1', 13: '+1', 14: '+2', 15: '+2', 16: '+3', 17: '+3', 18: '+4', 19: '+4', 20: '+5', 21: '+5', 22: '+6'
            };

            document.querySelector(`.col-${type} .modifier`).textContent = modifierMap[value] ?? 'not found';
        }
    });
</script>

<?php include_once 'partials/editor_bottom_tpl.php'; ?>