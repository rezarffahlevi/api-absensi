<?= $this->extend(env('theme.name').'/'.env('theme.private')) ?>

<?= $this->section('stylesheet') ?>
<style>
    #signature-pad {
        width: 100%;
    }
</style>
<?= $this->endSection('stylesheet') ?>

<?= $this->section('javascript') ?>
<script src="<?= asset_url('plugins/signature_pad/signature_pad.min.js'); ?>"></script>
<script>
$(function(){
    var canvas  = document.getElementById('signature-pad');

    // Adjust canvas coordinate space taking into account pixel ratio,
    function resizeCanvas() {
        var ratio       = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width    = canvas.offsetWidth * ratio;
        canvas.height   = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
    }

    window.onresize = resizeCanvas;
    resizeCanvas();

    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
    });

    document.getElementById('save').addEventListener('click', function () {
        if (signaturePad.isEmpty()) {
            return alert("Please provide a signature first.");
        }
        
        var data = signaturePad.toDataURL('image/png');
        console.log(data);
        $('.show-signature').html('<img class="img-fluid" src="' + data + '" />');
    });

    document.getElementById('clear').addEventListener('click', function () {
        signaturePad.clear();
    });
});
</script>
<?= $this->endSection('javascript') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title">Signature Pad</h5>
            </div>

            <div class="card-body p-0" style="display: block;">
                <canvas id="signature-pad" class="signature-pad" width=400 height=800></canvas>
            </div>

            <div class="card-footer">
                <button type="button" id="save" class="btn btn-primary float-right"><i class="fas fa-save"></i></button>
                <button type="button" id="clear" class="btn btn-primary float-right mr-1"><i class="fas fa-chalkboard"></i></button>
            </div>
        </div>
    </div><!-- col-md-7 -->

    <div class="col-md-5 show-signature">
        
    </div><!-- col-md-5 -->
</div>
<?= $this->endSection('content') ?>