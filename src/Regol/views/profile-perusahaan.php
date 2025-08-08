<form method="post" id="form-profile-perusahaan">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-25"><h5>PROFIL PERUSAHAAN PIALANG BERJANGKA</h5></div>
                    <div class="table-responsive">
                        <table class="table table-hover" style="border-collapse: separate; word-break: break-word;" width="100%">
                            <tr>
                                <td width="20%" class="top-align">Nama</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $company['name']; ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">Alamat</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $company['address'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">No. Telepon</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $company['phone'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">No Fax</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $company['fax'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">E-Mail</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $company['email'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">Home Page</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?php echo $company['homepage'] ?></td>
                            </tr>
                        </table>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-2">Susunan Pengurus Perusahaan</h6>
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td width="20%" class="top-align">Komisaris Utama</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?= $komisaris_utama ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">Komisaris</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?= $komisaris ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">Direktur Utama</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?= $profile['PROF_DEWAN_DIREKSI'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">Direktur Kepatuhan</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?= $profile['PROF_DIREKTUR'] ?></td>
                            </tr>
                            <tr>
                                <td width="20%" class="top-align">Direktur Operasional</td>
                                <td width="3%" class="top-align">:</td>
                                <td class="top-align text-start"><?= $profile['PROF_OPERATIONAL'] ?></td>
                            </tr>
                        </tbody>    
                    </table>
                    <hr>
                    <div class="d-flex flex-column mb-3">
                        <h6 class="fw-bold">Susunan Pemegang Saham Perusahaan</h6>
                        <ol>
                            <li><?= explode(",", $profile['PROF_PEMEGANG_SAHAM'])[0] ?></li>
                            <li><?= explode(",", $profile['PROF_PEMEGANG_SAHAM'])[1] ?></li>
                        </ol>
                    </div>

                    <div class="d-flex flex-column mb-3">
                        <h6 class="fw-bold">Nomor dan Tanggal Izin Usaha dari Bappebti :</h6>
                        <p class="ms-4 fs-15">- <?= $profile['PROF_NO_IZIN_USAHA'] ?> Tanggal: <?= default_date($profile['PROF_TGL_IZIN_USAHA'], "Y-m-d"); ?></p>
                    </div>

                    <div class="d-flex flex-column mb-3">
                        <h6 class="fw-bold">Nomor dan Tanggal Keanggotaan Bursa Berjangka :</h6>
                        <p class="ms-4 fs-15">- <?= $profile['PROF_NO_KEANGGOTAAN_BURSA'] ?> Tanggal: <?= default_date($profile['PROF_TGL_KEANGGOTAAN_BURSA'], "Y-m-d"); ?></p>
                    </div>

                    <div class="d-flex flex-column mb-3">
                        <h6 class="fw-bold">Nomor dan Tanggal Keanggotaan Lembaga Kliring Berjangka :</h6>
                        <p class="ms-4 fs-15">- <?= $profile['PROF_NO_KEANGGOTAAN_LEMBAGA'] ?> Tanggal: <?= default_date($profile['PROF_TGL_KEANGGOTAAN_LEMBAGA'], "Y-m-d"); ?></p>
                    </div>

                    <div class="d-flex flex-column mb-3">
                        <h6 class="fw-bold">Nomor dan Tanggal Persetujuan sebagai Peserta Sistem Perdagangan Alternatif :</h6>
                        <p class="ms-4 fs-15">- <?= $profile['PROF_NO_PERSETUJUAN_PESERTA'] ?> Tanggal: <?= default_date($profile['PROF_TGL_PERSETUJUAN_PESERTA'], "Y-m-d"); ?></p>
                    </div>

                    <div class="d-flex flex-column mb-3">
                        <h6 class="fw-bold">Nama Penyelenggara Sistem Perdagangan Alternatif :</h6>
                        <p class="ms-4 fs-15">- <?= $profile['FOREX_SYS'] ?></p>
                    </div>

                    <?php foreach($profile['PRODUCTS'] as $prd) : ?>
                        <div class="d-flex flex-column mb-3">
                            <h6 class="fw-bold"><?= $prd['name'] ?></h6>

                            <?php if($prd['type'] != "sub") : ?>
                                <ol>
                                    <?php foreach($prd['detail'] as $detail) : ?>
                                        <li><?= $detail['text'] ?></li>
                                    <?php endforeach; ?>
                                </ol>

                            <?php else : ?>
                                <div class="table-responsive">
                                    <table class="w-100 table-bordered" style="table-layout:fixed;word-break:break;">
                                        <tbody>
                                            <?php foreach($prd['detail'] as $detail) : ?>
                                                <tr>
                                                    <td class="p-1" width="15%"><b><?= $detail['text'] ?></b></td>
                                                    <td class="p-1"><?= implode(", ", $detail['sublist']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="row">
                        <div class="col-12">
                            <strong>Nama-Nama Wakil Pialang Berjangka yang Bekerja di Perusahaan Pialang Berjangka</strong> :
                            <div class="row">
                                <?php $list_wpb_satu = list_wpb(); ?>
                                <?php foreach($list_wpb_satu as $wpb) : ?>
                                    <div class="col-6">
                                        <ol>
                                            <?php foreach($wpb as $w) : ?>
                                                <li><?php echo $w['WPB_NAMA'] ?></li>
                                            <?php endforeach; ?>
                                        </ol>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <strong>Nama-Nama Wakil Pialang Berjangka yang secara khusus ditunjuk oleh Pialang Berjangka untuk melakukan verifikasi dalam rangka penerimaan Nasabah elektronik online</strong> :
                            <div class="row">
                                <?php $list_wpb_dua = list_wpb(2); ?>
                                <?php foreach($list_wpb_dua as $wpb) : ?>
                                    <div class="col-6">
                                        <ol>
                                            <?php foreach($wpb as $w) : ?>
                                                <li><?php echo $w['WPB_NAMA'] ?></li>
                                            <?php endforeach; ?>
                                        </ol>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <strong>Nomor Rekening Terpisah (Segregated Account) Perusahaan Pialang Berjangka </strong>
                            <div class="row">
                                <div class="col-12">
                                    <ol>
                                        <?php $sql_get_bankadm = mysqli_query($db, "SELECT * FROM tb_bankadm"); ?>
                                        <?php if($sql_get_bankadm) : ?>
                                            <?php while($bkadm = mysqli_fetch_assoc($sql_get_bankadm)) : ?>
                                                <li>
                                                    <div class="d-flex flex-column">
                                                        <p class="mb-0"><?= $bkadm['BKADM_NAME'] ?></p>
                                                        <p class="mb-0"><?= $bkadm['BKADM_ACCOUNT'] ?> (<?= $bkadm['BKADM_CURR'] ?>)</p>
                                                        <hr>
                                                    </div>
                                                </li>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <h5>PERNYATAAN TELAH MEMBACA PROFIL PERUSAHAAN PIALANG BERJANGKA</h5>
                            <p>
                                Dengan mengisi kolom “YA” di bawah ini, saya menyatakan bahwa saya telah membaca dan menerima informasi
                                <strong>PROFIL PERUSAHAAN PIALANG BERJANGKA</strong>, mengerti dan memahami isinya.
                            </p>
                        </div>
                        <div class="col-6 mt-3">
                            Pernyataan menerima/tidak<br>
                            <input type="radio" name="aggree" value="Ya" class="form-check-input radio_css" style="margin-top: 10px;" required <?php echo $realAccount['ACC_F_PROFILE'] ? 'checked' : NULL ?>>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Ya</label>
                            <input type="radio" name="aggree" value="Tidak" class="form-check-input radio_css" style="margin-top: 10px;" required>
                            <label style="top: 0.5rem;position: relative;margin-bottom: 0;vertical-align: top;margin-right:1.5rem;">Tidak</label>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="text-cemter">Menerima pada Tanggal</div>
                            <input type="text" name="agg_date" readonly required value="<?= $realAccount['ACC_F_PROFILE_DATE'] ?? date("Y-m-d H:i:s"); ?>" class="form-control text-center mb-3 <?= (empty($realAccount['ACC_F_PROFILE']))? "realtime-date" : ""; ?>">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex flex-row justify-content-end align-items-center gap-2 mt-25">
                        <a href="<?= ($prevPage['page'])? ("/create-acc/".$prevPage['page']) : "javascript:void(0)"; ?>" class="btn btn-secondary">Previous</a>
                        <button type="submit" class="btn btn-primary">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        $('#form-profile-perusahaan').on('submit', function(event) {
            event.preventDefault();
            let data = Object.fromEntries(new FormData(this).entries());
            Swal.fire({
                text: "Please wait...",
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            })

            $.post("/ajax/regol/profilePerusahaan", data, function(resp) {
                Swal.fire(resp.alert).then(function() {
                    console.log(resp);
                    if(resp.success) {
                        location.href = resp.redirect
                    }
                })
            }, 'json')
        })
    })
</script>