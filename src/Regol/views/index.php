<link rel="stylesheet" href="/assets/css/regol.css">
<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="mt-2 mb-2 part text-center step-wizard" id="nav-tab" role="tablist">
                <ul class="step-wizard-list">
                    <?php foreach($steps as $key => $step) : ?>
                        <?php $i = $key + 1; ?>
                        <li class="step-wizard-item">
                            <span class="progress-count">
                                <button type="button" onclick="location.href = `{$pageLink}`" class="btn btn-sm text-dark btn-outline-primary" id="tab1" aria-selected="false" title="test">
                                    <?= $i ?>
                                </button>
                            </span>
                            <span class="progress-label"><?= $step->pageTitle ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="panel-body">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>