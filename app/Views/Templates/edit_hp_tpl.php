<?php include_once 'partials/editor_top_tpl.php'; ?>
    <div class="content container text-center">
        <div class="float-left d-flex">
            <a href="<?= HOST ?>/" class="btn btn-success"> << Return</a>
        </div>
        <div class="title px-5 mb-4">
            <h2>HP</h2>
        </div>
        <div class="other editor col-12">
            <div class="progress mb-5" role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-striped bg-<?= $data['currentColor'] ?> progress-bar-animated"
                     style="width: <?= $data['percent'] ?>%"><?php if ($data['percent'] >= 10) echo $data['current'] .'/'. $data['max']; ?></div>
            </div>
            <form method="post">
                <div class="row">
                    <div class="mb-3 col-6">
                        <label for="current" class="form-label">Current</label>
                        <input type="number" class="form-control text-center" id="current" autofocus
                            name="hp[current]" value="<?= $data['current'] ?>" min="0" max="<?= $data['max'] ?>">
                    </div>
                    <div class="mb-3 col-6">
                        <label for="max" class="form-label">Max</label>
                        <input type="number" class="form-control text-center" id="max"
                           name="hp[max]" value="<?= $data['max'] ?>" min="0">
                    </div>
                    <button type="submit" class="btn btn-success col-12 ml-3">Save</button>
                </div>
            </form>
        </div>
    </div>
<?php include_once 'partials/editor_bottom_tpl.php'; ?>