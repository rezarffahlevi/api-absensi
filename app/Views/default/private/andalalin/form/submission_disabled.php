<div class="card card-primary card-outline" id="heading-submission">
    <div class="card-header p-0">
        <h5 class="card-title w-100">
            <a class="d-block w-100 collapsed p-4" data-toggle="collapse" href="#collapse-submission" aria-expanded="false">
                DATA PENGAJUAN
                <i class="fa fa-angle-down float-right"></i>
            </a>
        </h5>
    </div>

    <div id="collapse-submission" class="collapse" aria-labelledby="heading-submission" data-parent="#accordion">
    <?= form_open(admin_url("andalalin/save"), 'class="" id="submission-form"') ?>
        <input type="hidden" name="step" value="<?= $current_step; ?>" />
        <input type="hidden" name="id_andalalin" value="<?= $andalalin['id'] ?>" />
        <input type="hidden" name="id_oss" value="<?= $andalalin['id_oss'] ?>" />
        <input type="hidden" name="no_andalalin" value="<?= $andalalin['no_andalalin'] ?>" />

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card border shadow-none">
                        <div class="card-header">
                            <h4 class="card-title">
                                Data Pengusaha
                            </h4>
                        </div>
                        <div class="card-body">
                            <label for="">NIB</label>
                            <div class="input-group mb-5">
                                <input type="text" id="nib" name="nib" class="form-control" value="<?= $andalalin['nib'] ?>" disabled />
                                <span class="input-group-append">
                                    <button type="button" id="check_nib" class="btn btn-primary disabled" >Check via OSS</button>
                                </span>
                            </div>
                            <h4 class="card-title w-100 border-bottom pb-2 mb-2">
                                DATA DARI OSS
                            </h4>
                            <div class="form-group">
                                <label for="oss_name">Nama Pengusaha</label>
                                <input type="text" class="form-control" id="oss_name" name="oss_name" placeholder="Nama Pengusaha" value="<?= $andalalin['oss_name'] ?>" disabled />
                            </div>
                            <div class="form-group">
                                <label for="oss_phone">Telepon</label>
                                <input type="text" class="form-control" id="oss_phone" name="oss_phone" placeholder="Telepon" value="<?= $andalalin['oss_phone'] ?>" disabled />
                            </div>
                            <div class="form-group">
                                <label for="oss_email">Email</label>
                                <input type="email" class="form-control" id="oss_email" name="oss_email" placeholder="Email" value="<?= $andalalin['oss_email'] ?>" disabled />
                            </div>
                        </div>
                    </div>
                    <!-- card data pengusaha -->
                </div>
                <!-- col -->

                <div class="col-md-6">
                    <div class="card border shadow-none">
                        <div class="card-header">
                            <h4 class="card-title">
                                Data Pemohon
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= $andalalin['email']; ?>" disabled />
                            </div>
                        </div>
                    </div>
                    <!-- card data pemohon -->

                    <div class="card border shadow-none" style="min-height: 324px;">
                        <div class="card-header">
                            <h4 class="card-title">
                                Data Konsultan
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row d-none" id="select-consultant">
                                <div class="col-md-4 offset-md-4 box-profile">
                                    <div class="text-center mb-2" style="padding-bottom: 30px;">
                                        <img class="profile-user-img img-fluid img-circle" src="<?= asset_url('img/avatar5.png'); ?>" alt="User profile picture">
                                    </div>
                                    <button type="button" class="btn btn-primary btn-block" disabled><b>Pilih Konsultan</b></button>
                                </div>
                                <div class="col-md-4"></div>
                            </div>

                            <div class="row" id="change-consultant">
                                <div class="col-md-4 box-profile">
                                    <input type="hidden" id="id_consultant" name="id_consultant" value="<?= $andalalin['id_consultant']; ?>" />

                                    <div class="text-center mb-3">
                                        <img class="profile-user-img img-fluid img-circle" src="<?= asset_url('img/avatar5.png'); ?>" alt="User profile picture">
                                    </div>

                                    <h3 class="profile-username text-center mb-3" id="consultant_name"><?= $andalalin['organizer_name']; ?></h3>
                                    <button type="button" class="btn btn-primary btn-block" disabled><b>Ganti Konsultan</b></button>
                                </div>
                                <div class="col-md-8 table-responsive p-0" style="height: 220px;">
                                    <table class="table table-head-fixed" id="table-consultant" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Tahun</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- card data konsultan -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card border shadow-none">
                        <div class="card-header">
                            <h4 class="card-title w-100">
                                Data Pengajuan Andalalin
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Kategori</label>
                                        <select class="form-control" disabled id="category" name="category">
                                            <option value="<?= $andalalin['category'] ?>"><?= $andalalin['category'] ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sub_category">Sub Kategori</label>
                                        <select class="form-control" disabled id="sub_category" name="sub_category">
                                            <option value="<?= $andalalin['category'] ?>"><?= $andalalin['sub_category'] ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="capacity">Ukuran/Kapasitas</label>
                                        <select class="form-control" disabled id="capacity" name="capacity">
                                            <option value="<?= $andalalin['category'] ?>"><?= $andalalin['capacity'] ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="classification">
                                            Klasifikasi
                                            <i class="fas fa-info-circle text-warning" data-toggle="tooltip" data-placement="right" data-html="true" title="<?= $info['classification'] ?>"></i>
                                        </label>
                                        <input type="text" class="form-control" id="classification" name="classification" placeholder="Klasifikasi" value="<?= $andalalin['classification'] ?>" disabled/>
                                    </div>
                                    <div class="form-group">
                                        <label for="project_name">Nama Proyek</label>
                                        <input type="text" class="form-control" id="project_name" name="project_name" value="<?= $andalalin['name'] ?>" disabled/>
                                    </div>
                                    <div class="form-group">
                                        <label for="project_address">Alamat Proyek</label>
                                        <textarea id="project_address" name="project_address" class="form-control" rows="3" placeholder="Enter ..." disabled><?= $andalalin['address'] ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Lokasi dan Peta Proyek</label>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="mapid"></div>
                                                <input type="hidden" id="coordinate" name="coordinate" value='<?= $andalalin['polygon'] ?>' />
                                                <input type="hidden" id="latitude" name="latitude" value='<?= $andalalin['latitude'] ?>' />
                                                <input type="hidden" id="longitude" name="longitude" value='<?= $andalalin['longitude'] ?>' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- card data pengajuan -->
                </div>
            </div>
        </div>
        <!-- card body -->
        <div class="card-footer">
           
        </div>
        <?= form_close(); ?>
    </div>
</div>
<!-- Form Pengajuan -->