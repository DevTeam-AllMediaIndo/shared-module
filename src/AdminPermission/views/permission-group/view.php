<div class="row">
    <div class="col-md-4 mb-3">
        <?php require_once __DIR__ . "/create.php"; ?>
    </div>
    <div class="col mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-priamry">Daftar Grup</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="table-group">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th width="10%">Icon</th>
                                <th width="10%">#</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            let table;
            $(document).ready(function() {
                table = $('#table-group').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[0, 'desc']],
                    ajax: {
                        url: "/ajax/datatable/developer/group/view",
                    },
                })
            })
        </script>
    </div>
</div>

<?php require_once __DIR__ . "/update.php"; ?>
<?php require_once __DIR__ . "/delete.php"; ?>