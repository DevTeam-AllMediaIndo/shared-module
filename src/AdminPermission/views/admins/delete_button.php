<?php if($isAllowToUpdate) : ?>
    <script type="text/javascript">
        $(document).ready(function() {
            if(table) {
                table.on('draw.dt', function(evt) {
                    $.each($('#table tbody tr'), (i, tr) => {
                        let td = $(tr).find('td').eq(5);
                        if(td) {
                            let actionArea = td.find('.action');
                            if(actionArea && !actionArea.find('.btn-delete').length) {
                                let id = actionArea.data('id');
                                actionArea.append(`<a class="btn btn-danger btn-sm text-white btn-delete me-1" data-id="${id}"><i class="fas fa-trash"></i></a>`)
                            }
                        }
                    })

                    $('.btn-delete').on('click', function(event) {
                        let element = $(event.currentTarget);
                        if(element && element.data('id')) {
                            Swal.fire({
                                title: "Nonaktifkan Admin?",
                                text: "Apakah anda yakin ingin menonaktifkan admin ini?",
                                icon: "question",
                                showCancelButton: true,
                                reverseButtons: true
                            }).then((result) => {
                                if(result.isConfirmed) {
                                    Swal.fire({
                                        text: "Loading...",
                                        allowOutsideClick: false,
                                        didOpen: function() {
                                            Swal.showLoading();
                                        }
                                    })
    
                                    $.post("/ajax/post/admin/delete", {id: element.data('id')}, (resp) => {
                                        Swal.fire(resp.alert).then(() => {
                                            if(resp.success) {
                                                location.reload();
                                            }
                                        })
                                    }, 'json')
                                }
                            })
                        }
                    })
                })
            }
        })
    </script>
<?php endif; ?>