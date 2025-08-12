<?php if($isAllowToCreate) : ?>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-priamry">Buat Grup 1</h5>
            </div>
            <div class="card-body">
                <form action="" method="post" id="form-post-group">
                    <div class="form-group mb-3">
                        <label for="group_name" class="form-control-label">Nama</label>
                        <input type="text" name="group_name" id="group_name" class="form-control" placeholder="Group name, gunakan - jangan spasi">
                    </div>
                    <div class="form-group mb-3">
                        <label for="group_icon" class="form-control-label">Icon</label>
                        <input type="text" name="group_icon" id="group_icon" class="form-control" placeholder="HTML Icon">
                    </div>
                    <div class="form-group mb-3">
                        <label for="group_type" class="form-control-label">Tipe</label>
                        <select name="group_type" id="group_type" class="form-control">
                            <option value="dropdown">dropdown</option>
                            <option value="single">single</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-end" name="create_group">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#form-post-group').on('submit', function(event) {
                event.preventDefault();
                let button = $(this).find('button[type="submit"]')
                let data = Object.fromEntries(new FormData(this).entries());
                button.addClass('loading')
                $.post("/ajax/post/developer/group/create", data, function(resp) {
                    button.removeClass('loading')
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