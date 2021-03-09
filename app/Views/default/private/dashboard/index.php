<?= $this->extend(env('theme.name').'/'.env('theme.private')) ?>

<!-- content_header -->
<?= $this->section('content_header') ?>
<div class="content-header">
    <div class="card bg-transparent shadow-none">
        <div class="card-header d-flex p-0">
            <h3 class="p-3 m-0">Absensi</h3>
        </div>
    </div>
</div>
<?= $this->endSection('content_header') ?>

<!-- content -->
<?= $this->section('content') ?>
<div class="row">
    <?php if (!empty($data)): ?>
        <?php foreach($data as $row): ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title"><?=$row['no_andalalin'];?></h3>
                    <!-- <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div> -->
                </div>
                <div class="card-body pl-0 pr-0 pt-0">
                    <?php if(!empty($row['payment_status'])): ?>
                    <div class="ribbon-wrapper ribbon-lg">
                        <div class="ribbon bg-indigo">
                            <?= $row['payment_status'] == 'pending' ? 'Belum lunas' : ($row['payment_status'] == 'finish' ? 'Lunas' : $row['payment_status']); ?>  
                        </div>
                    </div>
                    <?php endif; ?>
                    <table class="table">
                        <tr>
                            <td class="pl-4">Pengajuan</td>
                            <td class="pr-4"><?=$row['category'];?></td>
                        </tr>
                        <tr>
                            <td class="pl-4">Obyek</td>
                            <td class="pr-4"><?=$row['sub_category'];?></td>
                        </tr>
                        <tr>
                            <td class="pl-4">Lokasi</td>
                            <td class="pr-4">Jakarta</td>
                        </tr>
                        <tr>
                            <td class="pl-4">Tagihan</td>
                            <td class="pr-4">Belum Lunas</td>
                        </tr>
                        <tr>
                            <td class="pl-4">Konsultan</td>
                            <td class="pr-4"><?=$row['organizer_name'];?></td>
                        </tr>
                        <tr>
                            <td class="pl-4">Konsultan</td>
                            <td class="pr-4"><?=$row['organizer_phone'];?></td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <button class="btn btn-warning d-inline float-right" type="button" onclick="window.location='<?=admin_url('andalalin/update/'.$row['id'])?>'">Detail</button>
                </div>
            </div>
        </div>

        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection('content') ?>

<!-- stylesheet -->
<?= $this->section('stylesheet') ?>
<link rel="stylesheet" href="<?= base_url('assets/plugins/fontawesome-free/css/all.min.css'); ?>">
<?= $this->endSection('stylesheet') ?>

<!-- javascript -->
<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'); ?>"></script>
<?= $this->endSection('javascript') ?>
