<?= $this->extend(env('theme.name').'/'.env('theme.private')) ?>

<?= $this->section('stylesheet') ?>
    <link rel="stylesheet" href="<?= asset_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
<?= $this->endSection('stylesheet') ?>

<?= $this->section('javascript') ?>
    <script src="<?= asset_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?= asset_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
    <script>
        let token = '<?= csrf_hash() ?>';
        $(document).ready(function() {
            $('#table-user').DataTable({
                stateSave: true,
                ordering: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?= admin_url('profile/ajax_user') ?>",
                    type:'POST',
                    data: function ( d ) {
                        d.<?= csrf_token() ?> = token;
                    },
                },
                drawCallback: function (settings) {
                    token = settings.json.<?= csrf_token() ?>;
                }
            });
        });
    </script>
<?= $this->endSection('javascript') ?>

<?= $this->section('content') ?>
<div class="box box-primary">
    <div class="box-body">
        <a href="<?=admin_url('profile/user')?>" class="btn btn-default mb-4"><i class="nav-icon fas fa-plus"></i> New User</a>
        <table id="table-user" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<?= $this->endSection('content') ?>
