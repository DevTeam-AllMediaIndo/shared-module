<?php 
if(!$isAllowToCreate) {
    die("<script>location.href = '/admin/view'; </script>");
}
?>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Tambah Admin</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Tambah</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Form Tambah Admin</h5>
            </div>
            <div class="card-body">
                <form action="" method="post" id="form-create-admin">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="add-fullname" class="form-label">Fullname</label>
                                <input type="text" class="form-control" id="add-fullname" name="add-fullname" placeholder="Fullname" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="add-username" name="add-username" placeholder="Username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="add-password" name="add-password" placeholder="Password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-country" class="form-label">Country</label>
                                <select name="add-country" id="add-country" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                    <?php foreach($countries as $country) : ?>
                                        <option value="<?= $country['COUNTRY_NAME'] ?>"><?= $country['COUNTRY_NAME'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add-level" class="form-label">Level</label>
                                <select name="add-level" id="add-level" class="form-control">
                                    <option value="" disabled selected>Select</option>
                                    <?php foreach($adminRoles as $role) : ?>
                                        <option value="<?= $role['ID_ADMROLE'] ?>"><?= $role['ADMROLE_NAME'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary" data-original-text="Submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#form-create-admin').on('submit', function(el) {
            el.preventDefault();
            let button = $(this).find('button[type="submit"]'), 
                data = $(this).serialize();
                
            button.addClass('loading');
            $.post("/ajax/post/admin/create", data, (resp) => {
                button.removeClass('loading');
                Swal.fire(resp.alert).then(() => {
                    if(resp.success) {
                        location.href = resp.data.readirect;
                    }
                })
            }, 'json');
        })
    })
</script>