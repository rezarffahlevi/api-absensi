<?= $this->extend(env('theme.name') . '/' . env('theme.private')) ?>

<?= $this->section('stylesheet') ?>
<link rel="stylesheet" href="<?= asset_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
<?= $this->endSection('stylesheet') ?>

<!-- content_header -->
<?= $this->section('content_header') ?>
<div class="content-header">
    <div class="card bg-transparent shadow-none">
        <div class="card-header d-flex p-0">
            <h3 class="p-3 m-0">Daftar Absensi</h3>
        </div>
    </div>
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
                url: "<?= admin_url('absent/ajax_absent') ?>",
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
        let name = $('#name').val();
        let email = $('#email').val();
        let address = $('#address').val();

        let data = {
            name,
            email,
            address
        };
        data[token] = hash;

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: '<?= admin_url('employee/ajax_submit_user') ?>',
            type: 'post',
            data,
            success: function(json) {
                json = JSON.parse(json);
                hash = json[token];

                if (typeof json.errors !== 'undefined') {
                    for (let prop in json.errors) {
                        $(`#validation_${prop}`).text(json.errors[prop]);
                    }
                } else {
                    dt.ajax.reload();
                    $('#employeeModal').modal('hide');
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
            url: '<?= admin_url('employee/ajax_delete_user') ?>',
            type: 'post',
            data,
            success: function(json) {
                json = JSON.parse(json);
                hash = json[token];

                dt.ajax.reload();
                $('#deleteEmployeeModal').modal('hide');
            }
        });
    });

    function call_modal(...param) {
        $("#form-employee")[0].reset();
        $('#id').val('');
        $('.text-validation').text('');

        if (param[0] == 'add') {
            id = '';
            $('#employeeModal').modal('show');
        } else if (param[0] == 'edit') {
            id = param[1];
            $('#name').val(param[2]);
            $('#email').val(param[3]);
            $('#address').val(param[4]);

            $('#employeeModal').modal('show');
        } else {
            id = param[1];
            $('#deleteEmployeeModal').modal('show');
        }

    }
</script>
<?= $this->endSection('javascript') ?>

<?= $this->section('content') ?>
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
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Form Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form-employee">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="name" name="name" id="name" class="form-control">
                        <span class="text-danger text-validation" id="validation_name"></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="form-control">
                        <span class="text-danger text-validation" id="validation_email"></span>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea name="address" id="address" class="form-control"></textarea>
                        <span class="text-danger text-validation" id="validation_address"></span>
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

<div class="modal fade" id="deleteEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="deleteEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEmployeeModalLabel">Delete Employee</h5>
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