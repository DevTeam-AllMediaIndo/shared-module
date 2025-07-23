<?php if($isAllowToCreate) : ?>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-priamry">Create Module</h5>
            </div>
            <div class="card-body">
                <form action="" method="post" id="form-add-module">
                    <div class="form-group mb-3">
                        <label for="m_group" class="form-control-label">Group</label>
                        <select name="m_group" id="m_group" class="form-control">
                            <?php foreach($availableGroups as $_grup) : ?>
                                <option value="<?= md5(md5($_grup['id'])) ?>"><?= $_grup['group']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="m_name" class="form-control-label">Name</label>
                        <input type="text" name="m_name" id="m_name" class="form-control" placeholder="Module Name, dont use space">
                    </div>
                    <div class="form-group mb-3">
                        <label for="m_visibility" class="form-control-label">Visibility</label>
                        <select name="m_visibility" id="m_visibility" class="form-control">
                            <option value="-1">Show</option>
                            <option value="0">Hidden</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-end" name="create_module">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#form-add-module').on('submit', function(event) {
                event.preventDefault();

                let data = Object.fromEntries(new FormData(this).entries());
                $.post("/ajax/post/developer/module/create", data, function(resp) {
                    Swal.fire(resp.alert).then(() => {
                        if(resp.success) {
                            location.reload();
                        }
                    })
                }, 'json')
            })
        })
    </script>
<?php endif; ?>