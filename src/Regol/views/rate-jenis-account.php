
<style>
    .account-card.active .file-manager-card {
        border: 2px solid var(--bs-primary-color);
    }


    input[type="radio"] {
        appearance: none;
        display: none;
    }

    input[type="radio"]:checked + label > div.file-manager-card {
        border: 2px solid var(--bs-primary-color);
    }

    .select-type {
        cursor: pointer;
    }

</style>
<div class="row">
    <div class="col-md-8 mx-auto">
        <form method="post" id="form-account-type">
            <input type="hidden" name="csrf_token" value="">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Demo Account</label>
                        <input type="text" class="form-control" value="<?= $demo_login ?? ""; ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Type</label>
                        <select id="acc-type" class="form-select mb-3">
                            <?php foreach($categories as $key => $category) : ?>
                                <option data-type="<?= strtolower($key) ?>" value="<?= strtoupper($key) ?>" <?= strtoupper($real_category) == strtoupper($key)? "selected" : ""; ?>><?= strtoupper($key); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <?php foreach($categories as $type_as => $types) : ?>
                            <?php $lowerType = strtolower($type_as) ?>
                            <div class="tab-categories" id="nav-<?= $lowerType ?>" <?= $lowerType != strtolower($real_category)? "style='display: none;'" : ""; ?>>
                                <nav class="mb-3">
                                    <?php if($lowerType == "multilateral") : ?>
                                        <div class="alert alert-warning">
                                            <p>Hubungi CS Kami untuk mendaftar akun <b>Multilateral</b></p>
                                        </div>
                                    <?php endif; ?>

                                    <div class="btn-box d-flex flex-wrap gap-2" id="nav-tab" role="tablist">
                                        <?php foreach($types as $key => $category) : ?>
                                            <button class="btn btn-sm btn-outline-primary <?= $key == 0? "active" : ""; ?>" id="<?= $lowerType.$category['type'] ?>-tab" data-bs-toggle="tab" data-bs-target="#tab-<?= $lowerType.$category['type'] ?>" type="button" role="tab" aria-controls="tab-<?= $lowerType.$category['type'] ?>" aria-selected="<?= $key == 0? "true" : "false"; ?>"><?= strtoupper($category['type']) ?></button>
                                        <?php endforeach; ?>
                                    </div>
                                </nav>
    
                                <div class="tab-content profile-edit-tab">
                                    <?php foreach($types ?? [] as $key => $category) : ?>
                                        <div class="tab-pane fade <?= $key == 0? "show active" : ""; ?>" id="tab-<?= $lowerType.$category['type'] ?>" role="tabpanel" aria-labelledby="tab-<?= $lowerType.$category['type'] ?>" tabindex="0">
                                            <div class="row">
                                                <?php foreach($category['products'] as $accType) : ?>
                                                    <?php if(strtoupper($accType['type_as']) == strtoupper($type_as)) : ?>
                                                        <div class="col-md-4">
                                                            <input type="radio" name="account-type" id="<?= $accType['suffix'] ?>" value="<?= $accType['suffix'] ?>" data-category="<?= $category['type'] ?>" <?= ($real_rtype == $accType['type'])? "checked" : ""; ?>>
                                                            <label for="<?= $accType['suffix'] ?>" class="w-100 h-100 select-type">
                                                                <div class="file-manager-card">
                                                                    <div class="top">
                                                                        <div class="part-icon">
                                                                            <span><?= strtoupper($accType['name']) ?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="bottom">
                                                                        <div class="left">
                                                                            <a class="folder-name"><?= $accType['name'] ?></a>
                                                                            <span class="file-quantity mb-1"><?= ($accType['currency'] == "IDR")? "Rate IDR " . $accType['rate'] : "Floating"; ?></span>
                                                                            <span class="file-quantity mb-1">Leverage: <?= $accType['leverage'] ?></span>
                                                                            <span class="file-quantity">Commission: $<?= $accType['commission'] ?></span>
                                                                        </div>
                                                                        <div class="right">
                                                                            <span class="storage-used"><?= $accType['currency'] ?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex flex-row justify-content-end align-items-center gap-2 mt-25">
                        <a href="<?= $pagePrev ?>" class="btn btn-secondary">Previous</a>
                        <button type="submit" class="btn btn-primary">Next</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        if($('input[name="account-type"]:checked').length) {
            $(`button#${$('input[name="account-type"]:checked').data('category')}-tab`).click()
        }

        $('#acc-type').on('change', function() {
            let type = $(this).find('option:selected').data('type');
            $('input[name="account-type"]').prop('checked', false)
            $('.tab-categories').hide();
            $(`#nav-${type}`).show();
        }).change();

        $('#form-account-type').on('submit', function(event){
            event.preventDefault();
            let data = Object.fromEntries(new FormData(this).entries());
            Swal.fire({
                text: "Please wait...",
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            })

            $.post("/ajax/post/account/regol/rate-jenis-account", data, function(resp) {
                Swal.fire(resp.alert).then(function() {
                    if(resp.success) {
                        location.href = resp.data.redirect
                    }
                })
            }, 'json')
        })
    })
</script>