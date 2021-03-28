<?= $this->extend(env('theme.name') . '/' . env('theme.private')) ?>

<?= $this->section('stylesheet') ?>
<link rel="stylesheet" href="<?= asset_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
<?= $this->endSection('stylesheet') ?>

<!-- content_header -->
<?= $this->section('content_header') ?>
<div class="content-header">
    <div class="card bg-transparent shadow-none">
        <div class="card-header d-flex p-0">
            <h3 class="p-3 m-0">Daftar Kelas</h3>
        </div>
    </div>
    <a href="javascript:;" class="btn btn-primary" style="margin:2px 7px;" onclick="call_modal('add')">Tambah Kelas</i></a>
</div>
<?= $this->endSection('content_header') ?>

<?= $this->section('javascript') ?>
<script src="<?= asset_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= asset_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
<script>
    let dt = id = '';
    let token = '<?= csrf_token() ?>';
    let hash = '<?= csrf_hash() ?>';

    $(document).ready(function() {
        dt = $('#table-absent').DataTable({
            stateSave: true,
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= admin_url('grade/ajax_grade') ?>",
                type: 'POST',
                data: function(d) {
                    d[token] = hash;
                },
            },
            drawCallback: function(settings) {
                hash = settings.json[token];
            }
        });
    });

    $('#btn-submit').click(async function() {
        let id_kelas = $('#id_kelas').val();
        let kelas = $('#kelas').val();

        let data = {
            id_kelas,
            kelas
        };

        data[token] = hash;

        if (id) {
            data.id = id_absent;
        }

        $.ajax({
            url: '<?= admin_url('grade/ajax_save_grade') ?>',
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
                    window.location.reload()
                    $('#gradeModal').modal('hide');
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
            url: '<?= admin_url('grade/ajax_delete_grade') ?>',
            type: 'post',
            data,
            success: function(json) {
                json = JSON.parse(json);
                hash = json[token];

                dt.ajax.reload();
                $('#deleteGradeModal').modal('hide');
                window.location.reload();
            }
        });
    });

    function call_modal(type, param = null) {
        $("#form-absent")[0].reset();
        $('#id').val('');
        $('.text-validation').text('');

        let row = param && JSON.parse(atob(param));
        console.log(row)
        if (type == 'add') {
            id = '';
            $('#gradeModal').modal('show');
        } else if (type == 'edit') {
            id = row.nis;
            $('#id_kelas').val(row.id_kelas);
            $('#kelas').val(row.kelas);

            $('#gradeModal').modal('show');
        } else if (type == 'delete') {
            id = row;
            $('#deleteGradeModal').modal('show');
        }
    }
</script>
<?= $this->endSection('javascript') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12 col-sm-12">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Kelas</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Informasi</a>
                    </li> -->
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                        <div class="box box-primary">
                            <div class="box-body">
                                <!-- <a href="javascript:;" class="btn btn-default mb-4" onclick="call_modal('add')"><i class="nav-icon fas fa-plus"></i> New User</a> -->
                                <div class="col-md-12 table-responsive p-0">
                                    <table id="table-absent" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="20">No</th>
                                                <th>Kelas</th>
                                                <th width="200">Aksi</th>
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

<div class="modal fade" id="gradeModal" tabindex="-1" role="dialog" aria-labelledby="gradeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeModalLabel">Form Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form-absent">
                    <div class="form-group">
                        <!-- <label for="id_kelas">ID Kelas:</label> -->
                        <input type="hidden" name="id_kelas" id="id_kelas" class="form-control" readonly>
                        <span class="text-danger text-validation" id="validation_id_kelas"></span>
                    </div>
                    <div class="form-group">
                        <label for="kelas">Kelas:</label>
                        <input type="text" name="kelas" id="kelas" class="form-control">
                        <span class="text-danger text-validation" id="validation_kelas"></span>
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

<div class="modal fade" id="deleteGradeModal" tabindex="-1" role="dialog" aria-labelledby="deleteGradeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteGradeModalLabel">Delete Absent</h5>
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
<?= $this->endSection('content') ?>