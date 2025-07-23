<?php if($isAllowToDelete) : ?>
    <script type="text/javascript">
        $(document).ready(function() {
            if(table) {
                table.on('draw.dt', function(evt) {
                    $.each($('#table-group tbody tr'), (i, tr) => {
                        let td = $(tr).find('td').eq(2);
                        if(td) {
                            let actionArea = td.find('.action');
                            if(actionArea && !actionArea.find('.btn-delete').length) {
                                let id = actionArea.data('id');
                                actionArea.append(`<a class="btn btn-danger btn-sm text-white btn-delete" data-id="${id}"><i class="fas fa-trash"></i></a>`)
                            }
                        }
                    })

                    $('.btn-delete').on('click', function(evt) {
                        if(evt.currentTarget) {
                            Swal.fire({
                                title: "Hapus Group",
                                text: "Apakah anda yakin ingin menghapus group ini?",
                                icon: "question",
                                showCancelButton: true,
                                reverseButtons: true,
                            }).then((result) => {
                                if(result.isConfirmed) {
                                    Swal.fire({
                                        text: "Loading...",
                                        allowOutsideClick: false,
                                        didOpen: function() {
                                            Swal.showLoading();
                                        }
                                    });

                                    $.post("/ajax/post/developer/group/delete", {id: $(evt.currentTarget).data('id')}, (resp) => {
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