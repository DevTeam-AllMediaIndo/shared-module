<?php if($isAllowToDelete) : ?>
    <script type="text/javascript">
        $(document).ready(function() {
            if(table) {
                table.on('draw.dt', async function(evt) {
                    await $.each($('#table-module tbody tr'), (i, tr) => {
                        let td = $(tr).find('td').eq(4);
                        if(td) {
                            let actionArea = td.find('.action');
                            if(actionArea && !actionArea.find('.btn-delete').length) {
                                let id = actionArea.data('id');
                                actionArea.append(`<a class="btn btn-danger btn-sm text-white btn-delete" data-id="${id}"><i class="fas fa-trash"></i></a>`)
                            }
                        }
                    })

                    await $('.btn-delete').on('click', async function(evt) {
                        let element = $(evt.currentTarget);
                        if(element && element.data('id')) {
                            
                            const {value: password} = await Swal.fire({
                                title: "Konfirmasi Hapus Module",
                                input: "password",
                                inputLabel: "Password",
                                inputPlaceholder: "Masukkan Password Anda",
                                inputAttributes: {
                                    autocapitalize: "off",
                                    autocorrect: "off"
                                }
                            });

                            if(password) {
                                Swal.fire({
                                    text: "Loading...",
                                    allowOutsideClick: false,
                                    didOpen: function() {
                                        Swal.showLoading();
                                    }
                                });

                                let data = {
                                    delete_m_id: element.data('id'),
                                    password: password
                                };

                                $.post("/ajax/post/developer/module/delete", data, function(resp) {
                                    Swal.fire(resp.alert).then(() => {
                                        if(resp.success) {
                                            location.reload();
                                        }
                                    })
                                }, 'json')
                            }
                        }
                    })
                })
            }
        })
    </script>
<?php endif; ?>
