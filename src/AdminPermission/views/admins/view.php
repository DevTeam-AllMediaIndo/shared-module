<div class="card custom-card overflow-hidden">
    <div class="card-header">
        <div class="d-flex justify-content-between mb-2">
            <h5 class="card-title">List Admin</h5>
            <?php if($isAllowToCreate) : ?>
                <a href="/admin/create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Create Admin</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table" class="table table-bordered table-striped table-hover key-buttons text-nowrap w-100">
                <thead>
                    <tr class="text-center">
                        <th>Date</th>
                        <th>User</th>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th width="15%">#</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    let table;
    $(document).ready(function() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            order: [[0, 'desc']],
            ajax: {
                url: "/ajax/datatable/admin/view",
                contentType: "application/json",
                type: "GET",
            },
            lengthMenu: [
                [10, 50, 100, -1],
                [10, 50, 100, "All"]
            ]
        });
    });
</script>

<?php require_once __DIR__ . "/update_button.php"; ?>
<?php require_once __DIR__ . "/permission_button.php"; ?>