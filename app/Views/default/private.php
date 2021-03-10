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
    <link rel="stylesheet" href="<?= asset_url('css/component.min.css'); ?>" />
    <!-- Plugins -->
    <link rel="stylesheet" href="<?= asset_url('plugins/fontawesome-free/css/all.min.css'); ?>" />
    <link rel="stylesheet" href="<?= asset_url('plugins/overlayScrollbars/css/OverlayScrollbars.min.css'); ?>" />
    <link rel="stylesheet" href="<?= asset_url('plugins/toastr/toastr.min.css'); ?>" />
    <?= $this->renderSection('stylesheet') ?>
  
</head>
<body class="hold-transition sidebar-mini layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?= $this->include(env('theme.name').'/template/navbar'); ?>

        <!-- Main Sidebar Container -->
        <?= $this->include(env('theme.name').'/template/sidebar'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <?= $this->renderSection('content_header') ?>

            <!-- Content. Contains page content -->
            <section class="content" style="padding-bottom: 40px;">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <?= $this->include(env('theme.name').'/template/footer'); ?>
    </div> <!-- end wrapper -->

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
    
    <?= $this->renderSection('javascript'); ?>
    <script>
        $(document).ready(function() {
            <?php if(session()->has('notif')): ?>
            <?php foreach(session('notif') as $row): ?>
                toastr['<?=$row['type']?>']('<?=$row['msg']?>');
            <?php endforeach; ?>
            <?php endif; ?>
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
</body>
</html>