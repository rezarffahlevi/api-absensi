<?= $this->extend(env('theme.name') . '/' . env('theme.private')) ?>

<?= $this->section('stylesheet') ?>
<link rel="stylesheet" href="<?= asset_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
<!-- Select2 -->
<link rel="stylesheet" href="<?= asset_url('plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= asset_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<?= $this->endSection('stylesheet') ?>

<!-- content_header -->
<?= $this->section('content_header') ?>
<div class="content-header">
    <div class="card bg-transparent shadow-none">
        <div class="card-header d-flex p-0">
            <h3 class="p-3 m-0">Daftar Absensi <?= $kelas ?></h3>
        </div>
    </div>
    <a href="javascript:;" class="btn btn-primary" style="margin:2px 7px;" onclick="call_modal('add')">Tambah Absen</i></a>
    <a href="javascript:;" class="btn btn-success" style="margin:2px 7px;" onclick="call_modal('export')">Export Absen</i></a>
</div>
<?= $this->endSection('content_header') ?>

<?= $this->section('javascript') ?>
<!-- Select2 -->
<script src="<?= asset_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= asset_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= asset_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
<script>
    let dt = id = '';
    let id_kelas = <?= $id ?>;
    let token = '<?= csrf_token() ?>';
    let hash = '<?= csrf_hash() ?>';
    let student = <?= json_encode($student); ?>;
    let selected_date = new Date().toISOString().substr(0, 10);
    let selected_status = 'semua';

    $(document).ready(function() {
        dt = $('#table-absent').DataTable({
            stateSave: true,
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= admin_url('absent/ajax_absent') ?>",
                type: 'POST',
                data: function(d) {
                    d[token] = hash;
                    d['id'] = id_kelas;
                    d['selected_date'] = selected_date;
                    d['selected_status'] = selected_status;
                },
            },
            drawCallback: function(settings) {
                hash = settings.json[token];
            }
        });

        $('#select2-nis').select2({
            theme: 'bootstrap4'
        });
        $('#selected_date').val(selected_date);
    });

    $('#select2-nis').on('change', function(e) {
        let value = e.currentTarget.value;
        let nama = student.find(fd => fd.nis == value).nama;
        $('#nama').val(nama);
        console.log(e.currentTarget.value, student)
    });

    $('#selected_date').on('change', function(e) {
        let value = e.currentTarget.value;
        selected_date = value;
        dt.ajax.reload();
        console.log(e.currentTarget.value)
    });

    $('#selected_status').on('change', function(e) {
        let value = e.currentTarget.value;
        selected_status = value;
        dt.ajax.reload();
        console.log(e.currentTarget.value)
    });

    $('#btn-submit').click(async function() {
        let id_absent = $('#id_absent').val();
        let nis = $('#select2-nis').val();
        let status = $('#status').val();
        let tgl = $('#tgl').val();
        let keterangan = $('#keterangan').val();

        let data = {
            id: id_absent,
            nis,
            date: new Date(tgl).toISOString().slice(0, 19).replace('T', ' '),
            status,
            notes: keterangan
        };

        data[token] = hash;

        if (id) {
            data.id = id_absent;
        }

        $.ajax({
            url: '<?= admin_url('absent/ajax_save_absent') ?>',
            type: 'post',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(json) {
                json = JSON.parse(json);
                hash = json[token];

                if (typeof json.errors !== 'undefined') {
                    for (let prop in json.errors) {
                        $(`#validation_${prop}`).text(json.errors[prop]);
                    }
                } else {
                    dt.ajax.reload();
                    $('#absentModal').modal('hide');
                }
            }
        });
    });

    $('#btn-delete').click(async function() {
        let data = {
            id
        };
        data[token] = hash;

        $.ajax({
            url: '<?= admin_url('absent/ajax_delete_absent') ?>',
            type: 'post',
            data,
            success: function(json) {
                json = JSON.parse(json);
                hash = json[token];

                dt.ajax.reload();
                $('#deleteAbsentModal').modal('hide');
            }
        });
    });

    function call_modal(type, param) {
        $("#form-absent")[0].reset();
        $('#id').val('');
        $('.text-validation').text('');

        let row = param && JSON.parse(atob(param));
        console.log(row)
        if (type == 'add') {
            id = '';
            $('#tgl').val((new Date().toLocaleString("sv-SE") + '').replace(' ', 'T'));
            $('#select2-nis').prop('disabled', false);
            $('#nama').prop('readonly', true);
            $('#absentModal').modal('show');
            $('#id_absent').val('');
        } else if (type == 'edit') {
            id = row.nis;
            $('#select2-nis').prop('disabled', true);
            $('#nama').prop('readonly', true);

            $('#id_absent').val(row.id);
            $('#select2-nis').val(row.nis);
            $('#nama').val(row.nama);
            $('#tgl').val((new Date(row.tgl).toLocaleString("sv-SE") + '').replace(' ', 'T'));
            $('#status').val(row.status);
            $('#keterangan').val(row.notes);

            $('#absentModal').modal('show');
        } else if (type == 'delete') {
            id = row;
            $('#deleteAbsentModal').modal('show');
        } else if (type == 'export') {
            id = '';
            $('#month').val(new Date().getMonth() + 1);
            $('#year').val(new Date().getFullYear());
            $('#exportAbsentModal').modal('show');
        } else {
            $('#pict').removeAttr('src');
            $('#dtl_nis').val(row.nis);
            $('#dtl_nama').val(row.nama);
            $('#dtl_tgl').val(row.tgl);
            $('#dtl_kelas').val(row.kelas);
            $('#dtl_status').val(row.status);
            $('#dtl_keterangan').val(row.notes);
            $('#dtl_longlat').val(row.longitude + ', ' + row.latitude);
            $('#dtl_alamat').val(row.alamat);
            $('#pict').attr('src', "<?= base_url('home/img?name=') ?>" + row.pict).css('max-height', '300px');

            $('#absentDetailModal').modal('show');
        }
    }

    $('#btn-export').click(async function() {
        let month = $('#month').val();
        let year = $('#year').val();


        let data = {
            date: year + '-' + month + '-10',
            id_kelas: id_kelas,
            kelas: '<?= $kelas ?>',
        };

        data[token] = hash;

        console.log(data);
        await window.open("<?= admin_url('absent/export') ?>" + '?' + $.param(data));
        $('#exportAbsentModal').modal('hide');
    });
</script>
<?= $this->endSection('javascript') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12 col-sm-12">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Daftar Kehadiran</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Informasi</a>
                    </li> -->
                </ul>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label for="nama">Tanggal:</label>
                            <input type="date" name="selected_date" id="selected_date" class="form-control">
                            <span class="text-danger text-validation" id="validation_tgl"></span>
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="form-group">
                            <label for="selected_status">Status:</label>
                            <select name="selected_status" id="selected_status" class="form-control">
                                <option value="semua">Semua</option>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                            </select>
                            <span class="text-danger text-validation" id="validation_tgl"></span>
                        </div>
                    </div>
                </div>
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                        <div class="box box-primary">
                            <div class="box-body">
                                <!-- <a href="javascript:;" class="btn btn-default mb-4" onclick="call_modal('add')"><i class="nav-icon fas fa-plus"></i> New User</a> -->
                                <div class="col-md-12 table-responsive p-0">
                                    <table id="table-absent" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nis</th>
                                                <th>Nama</th>
                                                <th>Waktu</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
                        Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<div class="modal fade" id="absentModal" tabindex="-1" role="dialog" aria-labelledby="absentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="absentModalLabel">Form Absen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form-absent">
                    <div class="form-group">
                        <label for="nis">NIS:</label>
                        <select class="js-data-example-ajax form-control" id="select2-nis" required>
                            <option value="">Pilih</option>
                            <?php foreach ($student as $std) : ?>
                                <option><?= $std['nis'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="nis" id="nis" class="form-control">
                        <input type="hidden" name="id_absent" id="id_absent" class="form-control" readonly>
                        <span class="text-danger text-validation" id="validation_name"></span>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama:</label>
                        <input type="text" name="nama" id="nama" class="form-control">
                        <span class="text-danger text-validation" id="validation_nama"></span>
                    </div>

                    <div class="form-group">
                        <label for="nama">Waktu:</label>
                        <input type="datetime-local" name="tgl" id="tgl" class="form-control" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}">
                        <span class="text-danger text-validation" id="validation_tgl"></span>
                    </div>

                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                        <span class="text-danger text-validation" id="validation_status"></span>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan:</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
                        <span class="text-danger text-validation" id="validation_keterangan"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-submit">Submit</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="absentDetailModal" tabindex="-1" role="dialog" aria-labelledby="absentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="absentDetailModalLabel">Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="dtl_nis">NIS:</label>
                            <input type="dtl_nis" name="dtl_nis" id="dtl_nis" class="form-control">
                            <span class="text-danger text-validation" id="validation_dtl_nis"></span>
                        </div>
                        <div class="form-group">
                            <label for="dtl_nama">Nama:</label>
                            <input type="dtl_nama" name="dtl_nama" id="dtl_nama" class="form-control">
                            <span class="text-danger text-validation" id="validation_dtl_nama"></span>
                        </div>
                        <div class="form-group">
                            <label for="dtl_tgl">Waktu:</label>
                            <input type="dtl_tgl" name="dtl_tgl" id="dtl_tgl" class="form-control">
                            <span class="text-danger text-validation" id="validation_dtl_tgl"></span>
                        </div>
                        <div class="form-group">
                            <label for="dtl_nama">Kelas:</label>
                            <input type="dtl_nama" name="dtl_nama" id="dtl_kelas" class="form-control">
                            <span class="text-danger text-validation" id="validation_dtl_nama"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <input type="text" name="status" id="dtl_status" class="form-control">
                            <span class="text-danger text-validation" id="validation_status"></span>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan:</label>
                            <textarea name="keterangan" id="dtl_keterangan" class="form-control"></textarea>
                            <span class="text-danger text-validation" id="validation_keterangan"></span>
                        </div>
                        <div class="form-group">
                            <label for="dtl_nis">Longitude Latitude:</label>
                            <input type="dtl_nis" name="dtl_nis" id="dtl_longlat" class="form-control">
                            <span class="text-danger text-validation" id="validation_dtl_nis"></span>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Alamat:</label>
                            <textarea name="keterangan" id="dtl_alamat" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 text-center">
                        <img id='pict' />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAbsentModal" tabindex="-1" role="dialog" aria-labelledby="deleteAbsentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAbsentModalLabel">Delete Absent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="btn-delete">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportAbsentModal" tabindex="-1" role="dialog" aria-labelledby="deleteAbsentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportAbsentModalLabel">Export Absent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="month">Bulan:</label>
                    <div class="row">
                        <div class="col-7">
                            <select name="month" id="month" class="form-control">
                                <?php
                                for ($i = 1; $i <= 12; $i++) {
                                    echo "<option value='" . $i . "'>" . date('F', mktime(11, 11, 10, $i, 12, date('Y'))) . "</option>";
                                } ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <input type="text" name="year" id="year" class="form-control">
                        </div>
                        <span class="text-danger text-validation" id="validation_month"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-export">Export</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content') ?>