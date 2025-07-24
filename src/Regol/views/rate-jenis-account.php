
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
                        <input type="text" class="form-control" value="<?= $demoAccount['ACC_LOGIN'] ?? ""; ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Type</label>
                        <select id="acc-type" class="form-select mb-3">
                            <?php foreach($accountCategories as $accType) : ?>
                                <option data-type="<?= strtolower($accType) ?>" value="<?= strtoupper($accType) ?>" <?= strtoupper($realAccount['RTYPE_TYPE_AS']) == strtoupper($accType)? "selected" : ""; ?>><?= $accType; ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <?php foreach($accountCategories as $type) : ?>
                            <?php $lowerType = strtolower($type) ?>
                            <div class="tab-categories" id="nav-<?= $lowerType ?>" <?= $lowerType != strtolower($realAccount['RTYPE_TYPE_AS'])? "style='display: none;'" : ""; ?>>
                                <nav class="mb-3">
                                    <?php if($lowerType == "multilateral") : ?>
                                        <div class="alert alert-warning">
                                            <p>Hubungi CS Kami untuk mendaftar akun <b>Multilateral</b></p>
                                        </div>
                                    <?php endif; ?>

                                    <div class="btn-box d-flex flex-wrap gap-2" id="nav-tab" role="tablist">
                                        <?php foreach($accountTypes[ $lowerType ] ?? [] as $key => $category) : ?>
                                            <button class="btn btn-sm btn-outline-primary <?= $key == 0? "active" : ""; ?>" id="<?= $lowerType.$category['type'] ?>-tab" data-bs-toggle="tab" data-bs-target="#tab-<?= $lowerType.$category['type'] ?>" type="button" role="tab" aria-controls="tab-<?= $lowerType.$category['type'] ?>" aria-selected="<?= $key == 0? "true" : "false"; ?>"><?= strtoupper($category['type']) ?></button>
                                        <?php endforeach; ?>
                                    </div>
                                </nav>
    
                                <div class="tab-content profile-edit-tab">
                                    <?php foreach($accountTypes[ $lowerType ] ?? [] as $key => $category) : ?>
                                        <div class="tab-pane fade <?= $key == 0? "show active" : ""; ?>" id="tab-<?= $lowerType.$category['type'] ?>" role="tabpanel" aria-labelledby="tab-<?= $lowerType.$category['type'] ?>" tabindex="0">
                                            <div class="row">
                                                <?php foreach($category['products'] as $accType) : ?>
                                                    <?php if(strtoupper($accType['RTYPE_TYPE_AS']) == strtoupper($type)) : ?>
                                                        <div class="col-md-4">
                                                            <input type="radio" name="account-type" id="<?= $accType['RTYPE_SUFFIX'] ?>" value="<?= $accType['RTYPE_SUFFIX'] ?>" data-category="<?= $category['type'] ?>" <?= ($realAccount['ACC_TYPE'] == $accType['ID_RTYPE'])? "checked" : ""; ?>>
                                                            <label for="<?= $accType['RTYPE_SUFFIX'] ?>" class="w-100 h-100 select-type">
                                                                <div class="file-manager-card">
                                                                    <div class="top">
                                                                        <div class="part-icon">
                                                                            <span><?= strtoupper($accType['RTYPE_TYPE']) ?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="bottom">
                                                                        <div class="left">
                                                                            <a class="folder-name"><?= $accType['RTYPE_NAME'] ?></a>
                                                                            <span class="file-quantity mb-1"><?= ($accType['RTYPE_CURR'] == "IDR")? "Rate IDR " . $accType['RTYPE_RATE'] : "Floating"; ?></span>
                                                                            <span class="file-quantity mb-1">Leverage: <?= $accType['RTYPE_LEVERAGE'] ?></span>
                                                                            <span class="file-quantity">Commission: $<?= $accType['RTYPE_KOMISI'] ?></span>
                                                                        </div>
                                                                        <div class="right">
                                                                            <span class="storage-used"><?= $accType['RTYPE_CURR'] ?></span>
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
                        <a href="<?= ($prevPage['page'])? ("/create-acc/".$prevPage['page']) : "javascript:void(0)"; ?>" class="btn btn-secondary">Previous</a>
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
        });

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

            $.post("/ajax/regol/accountType", data, function(resp) {
                Swal.fire(resp.alert).then(function() {
                    if(resp.success) {
                        location.href = resp.redirect
                    }
                })
            }, 'json')
        })
    })
</script>