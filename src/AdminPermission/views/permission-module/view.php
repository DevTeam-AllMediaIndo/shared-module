<div class="row">
    <?php require_once __DIR__ . "/create.php" ?>
    <div class="col mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-priamry">Daftar Module</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="table-module">
                        <thead>
                            <tr>
                                <th>Last Update</th>
                                <th>Grup</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>#</th>
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
                table = $('#table-module').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[0, 'desc']],
                    ajax: {
                        url: "/ajax/datatable/developer/module/view",
                    }
                })
            })
        </script>
    </div>
</div>