<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= getTitle() ?></title>

    <?= $this->renderSection('meta') ?>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic" />
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= asset_url('css/adminlte.min.css'); ?>" />
    <link rel="stylesheet" href="<?= asset_url('css/alt/adminlte.components.min.css'); ?>" />
    <!-- Plugins -->
    <link rel="stylesheet" href="<?= asset_url('plugins/fontawesome-free/css/all.min.css'); ?>" />
    <link rel="stylesheet" href="<?= asset_url('plugins/overlayScrollbars/css/OverlayScrollbars.min.css'); ?>" />
    <link rel="stylesheet" href="<?= asset_url('plugins/toastr/toastr.min.css'); ?>" />
    <?= $this->renderSection('stylesheet') ?>
</head>
<body class="layout-footer-fixed">
    <div class="wrapper">
        <?= $this->renderSection('content') ?>
        
        <!-- Footer -->
        <footer class="main-footer text-center m-0 bg-warning border-top-0">
            <strong>© <?= env('site.start_year'); ?> - <?= date('Y'); ?> <?= env('site.name'); ?></strong>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="<?= asset_url('plugins/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= asset_url('plugins/jquery-ui/jquery-ui.min.js'); ?>"></script>
    <script>$.widget.bridge('uibutton', $.ui.button)</script>

    <!-- Bootstrap 4 -->
    <script src="<?= asset_url('plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= asset_url('js/adminlte.js'); ?>"></script>
    <!-- Plugins -->
    <script src="<?= asset_url('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'); ?>"></script>
    <script src="<?= asset_url('plugins/toastr/toastr.min.js'); ?>"></script>
    <!-- jquery-validation -->
    <script src="<?= asset_url('plugins/jquery-validation/jquery.validate.min.js') ?>"></script>
    <script src="<?= asset_url('plugins/jquery-validation/additional-methods.min.js') ?>"></script>
    <script>
        $(document).ready(function() {
            <?php if(session()->has('notif')) { ?>
                toastr['<?=session('notif')['type']?>']('<?=session('notif')['msg']?>');
            <?php } ?>
        });
        jQuery.validator.setDefaults({
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.input-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    </script>
    <?= $this->renderSection('javascript') ?>
</body>
</html>