<div class="card card-primary card-outline" id="heading-submission">
    <div class="card-header p-0">
        <h5 class="card-title w-100">
            <a class="d-block w-100 collapsed p-4" data-toggle="collapse" href="#collapse-submission" aria-expanded="false">
                DATA PENGAJUAN
                <i class="fa fa-angle-down float-right"></i>
            </a>
        </h5>
    </div>

    <div id="collapse-submission" class="collapse <?= $current_step == 1 ? 'show' : ''; ?>" aria-labelledby="heading-submission" data-parent="#accordion">
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
                                <input type="text" id="nib" name="nib" class="form-control" value="<?= $andalalin['nib'] ?>" />
                                <span class="input-group-append">
                                    <button type="button" id="check_nib" class="btn btn-primary" onclick="checkNIB()">Check via OSS</button>
                                </span>
                            </div>
                            <h4 class="card-title w-100 border-bottom pb-2 mb-2">
                                DATA DARI OSS
                            </h4>
                            <div class="form-group">
                                <label for="oss_name">Nama Pengusaha</label>
                                <input type="text" class="form-control" id="oss_name" name="oss_name" placeholder="Nama Pengusaha" value="<?= $andalalin['oss_name'] ?>" readonly />
                            </div>
                            <div class="form-group">
                                <label for="oss_phone">Telepon</label>
                                <input type="text" class="form-control" id="oss_phone" name="oss_phone" placeholder="Telepon" value="<?= $andalalin['oss_phone'] ?>" readonly />
                            </div>
                            <div class="form-group">
                                <label for="oss_email">Email</label>
                                <input type="email" class="form-control" id="oss_email" name="oss_email" placeholder="Email" value="<?= $andalalin['oss_email'] ?>" readonly />
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
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= $andalalin['email']; ?>" readonly />
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
                            <div class="row" id="select-consultant">
                                <div class="col-md-4 offset-md-4 box-profile">
                                    <div class="text-center mb-2" style="padding-bottom: 30px;">
                                        <img class="profile-user-img img-fluid img-circle" src="<?= asset_url('img/avatar5.png'); ?>" alt="User profile picture">
                                    </div>
                                    <button type="button" class="btn btn-primary btn-block" onclick="call_modal()"><b>Pilih Konsultan</b></button>
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
                                    <button type="button" class="btn btn-primary btn-block" onclick="call_modal()"><b>Ganti Konsultan</b></button>
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
                                        <input type="text" class="form-control" id="classification" name="classification" placeholder="Klasifikasi" value="<?= $andalalin['classification'] ?>" readonly/>
                                    </div>
                                    <div class="form-group">
                                        <label for="project_name">Nama Proyek</label>
                                        <input type="text" class="form-control" id="project_name" name="project_name" value="<?= $andalalin['name'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="project_address">Alamat Proyek</label>
                                        <textarea id="project_address" name="project_address" class="form-control" rows="3" placeholder="Enter ..."><?= $andalalin['address'] ?></textarea>
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
            <button type="button" id="save-submission" class="btn btn-success float-right">LANJUT</button>
        </div>
        <?= form_close(); ?>
    </div>
</div>
<!-- Form Pengajuan -->

<div class="modal fade" id="search-modal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Pilih Konsultan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-8">
                        <table id="table-select-consultant" style="width: 100% !important;" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="col-4">
                        <div class="col-12 box-profile">
                            <div class="text-center mb-3">
                                <img class="profile-user-img img-fluid img-circle" src="<?= asset_url('img/avatar5.png'); ?>" alt="User profile picture">
                            </div>

                            <input type="hidden" id="id_consultant_temp" name="id_consultant_temp" value="" />
                            <h3 class="profile-username text-center mb-3" id="consultant_name_temp">Consultant Name</h3>
                        </div>
                        <div class="col-12 table-responsive p-0" style="height: 220px;">
                            <table class="table table-head-fixed" id="table-consultant-temp" style="width: 100% !important;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Year</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-choose-consultant">Choose</button>
            </div>
        </div>
    </div>
</div>