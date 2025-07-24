<div class="row">
    <div class="col-2"></div>
    <div class="col-8">
        <div class="panel rounded-0">
            <div class="panel-body invoice" id="invoiceBody">
                <div class="invoice-header mb-25">
                    <div class="row justify-content-between align-items-end">
                        <div class="col-xl-4 col-lg-5 col-sm-6">
                            <div class="shop-address">
                                <div class="logo mb-20">
                                    <img src="/assets/images/logo-black-new.png" alt="Logo">
                                </div>
                                <div class="part-txt">
                                    <p class="mb-1">Nama: <?= $fullname; ?></p>
                                    <p class="mb-1">Email: <?= $email ?></p>
                                    <p class="mb-1">No.Telp: <?= $phone ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex gap-xl-4 gap-3 status-row">
                                <div class="w-50">
                                    <div class="payment-status">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" value="<?= date('Y-m-d', strtotime($date_create_demo)) ?>" readonly class="form-control text-center">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="invoice-body">
                    <div class="info-card-wrap mb-25">
                        <div class="row">
                            <div class="col-md-2 col-sm-6"></div>
                            <div class="col-md-8 col-sm-6">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="profile-sidebar">
                                            <div class="bottom">
                                                <h6 class="profile-sidebar-subtitle">Informasi Demo Account</h6>
                                                <ul>
                                                    <li>Login Meta: <span id="login"><?= $demo['login'] ?? "" ?></span></li>
                                                    <li>Password Meta: <span id="passw"><?= $demo['master'] ?? "" ?></span></li>
                                                    <li>Investor Meta: <span id="invst"><?= $demo['investor'] ?? "" ?></span></li>
                                                    <li>Phone Meta: <span id="phone"><?= $demo['phone'] ?? "" ?></span></li>
                                                </ul>
                                                <h6 class="profile-sidebar-subtitle">Note</h6>
                                                <p id="nte"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(empty($demo['login'])) : ?>
                <div class="panel-body border-top">
                    <div class="btn-box d-flex justify-content-end gap-2">
                        <form action="" id="createDemo">
                            <button type="submit" class="btn btn-sm btn-primary">Buat Demo Account <i class="fa-light fa-plus"></i></button>
                        </form>
                    </div>
                </div>
            <?php else : ?>
                <div class="panel-footer">
                    <div class="text-end mt-25">
                        <a href="<?= ($nextPage['page'])? ("/create-acc/".$nextPage['page']) : "javascript:void(0)"; ?>" class="btn btn-primary">Berikutnya</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-2"></div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#createDemo').on('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                text: "Please wait...",
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            })
            
            let object = Object.fromEntries(new FormData(this).entries())
            $.post("/ajax/regol/createDemo", object, function(resp) {
                if(!resp.success) {
                    Swal.fire("Failed", resp.error, "error");
                    return false;
                }

                Swal.fire("Success", resp.message, "success")
                $('#login').text(resp.data.login);
                $('#passw').text(resp.data.passw);
                $('#invst').text(resp.data.invst);
                $('#phone').text(resp.data.phone);
                $('#nte').text(resp.data.mails);
            }, 'json')
        })
    })
</script>