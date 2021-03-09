<?= $this->extend(env('theme.name').'/'.env('theme.private')) ?>

<?= $this->section('stylesheet') ?>
<link rel="stylesheet" href="<?= asset_url('plugins/camshoot/jquery.camshoot.min.css'); ?>" />
<style>
    #cameraContainer,
    #cameraContainer video,
    #cameraContainer .snap_thumb canvas  {
        width: 100%     !important;
        height: auto    !important;
    }
    #cameraContainer .snap_thumb img {
        width: 50%      !important;
    }
</style>
<?= $this->endSection('stylesheet') ?>

<?= $this->section('javascript') ?>
<script src="<?= asset_url('plugins/camshoot/jquery.camshoot.min.js'); ?>"></script>
<script>
$(function(){
    var container = $("#cameraContainer");
    
    $("#cameraContainer").camshoot({
        height:600,
        width: 600,
        sound:true,
        imagetype: 'jpg',
        filename: 'testing',
        flasheffect: true,
        services: 'save'
    });
});
</script>
<?= $this->endSection('javascript') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Take photo</h5>
            </div>

            <div class="card-body p-0" style="display: block;">
                <div id="cameraContainer"></div>
            </div>

            <div class="card-footer">
            </div>
        </div>
    </div><!-- col-md-7 -->
</div>
<?= $this->endSection('content') ?>