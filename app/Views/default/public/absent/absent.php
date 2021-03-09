<?= $this->extend(env('theme.name') . '/' . env('theme.public')) ?>
<?= $this->section('stylesheet') ?>
<link rel="stylesheet" href="<?= asset_url('plugins/select2/css/select2.min.css'); ?>" />
<link rel="stylesheet" href="<?= asset_url('plugins/camshoot/jquery.camshoot.min.css'); ?>" />
<style>
    #signature-pad {
        width: 100%;
    }

    #cameraContainer,
    #cameraContainer video,
    #cameraContainer .snap_thumb canvas {
        width: 100% !important;
        height: auto !important;
    }

    #cameraContainer .snap_thumb img {
        width: 50% !important;
    }
</style>
<?= $this->endSection('stylesheet') ?>

<?= $this->section('content') ?>
<div style="background-image: url('<?= asset_url('img/bg-transjakarta.jpg'); ?>'); background-size: cover; height: 100%; width:100%; position: fixed; overflow:auto;">
    <div class="hold-transition" style="background: transparent!important;">
        <div class="container">
            <div class="login-logo border-bottom border-white">
                <div class="row">
                    <div class="col-2">
                        <img src="<?= asset_url('img/icon.png'); ?>" style="width: 80px; padding-left: .6rem; margin-top: 15px;" />
                    </div>
                    <div class="col-10 text-left">
                        <a href="<?= site_url(); ?>" class="text-white" style="font-size: 60px;"><b><?= env('site.name') ?></b></a>
                        <p class="text-white text-sm" style="margin-top: -15px;">Sistem Informasi Analisa Dampak Lalu Lintas</p>
                    </div>
                </div>
            </div>
            <!-- /.login-logo -->
            <div class="card" style="background: transparent!important;">
                <?= form_open('/', 'class="validate" id="absent-form"') ?>
                <div class="card-body login-card-body p-1" style="background: transparent!important;">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-indigo mb-5">
                                <div class="card-header">
                                    <h3 class="card-title">Attendance Form</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /.card-tools -->
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">

                                            <!-- <div class="form-group">
                                                <div class="text-center mb-3" style="max-height:300px">
                                                    <img id="temp-img" class="profile-user-img img-fluid" style="object-fit: contain; max-height:500px; min-width:200px; margin-bottom:20px" src="<?= asset_url('img/upload.png'); ?>" alt="User profile picture" onclick="$('#upload-img').click()">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Upload Photo</label>
                                                <input type="file" id="upload-img" class="btn btn-block bg-gradient-info btn-lg" onchange="readURL(this);" accept="image/x-png,image/gif,image/jpeg" />
                                            </div> -->
                                            <div class="form-group">
                                                <label>Take photo</label>
                                                <div class="card">
                                                    <div class="card-body p-0" style="display: block;">
                                                        <div id="cameraContainer"></div>
                                                    </div>

                                                    <div class="card-footer">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group">
                                                <label for="kategori">Email</label>
                                                <div class="input-group mb-3">
                                                    <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="<?= $absent['email'] ?>">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text bg-white">
                                                            <span class="fas fa-file"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Signature</label>
                                                <div class="card" style="box-shadow: 0 2px 4px rgba(0,0,0,.5) !important;">

                                                    <div class="card-body p-0" style="display: block;">
                                                        <canvas id="signature-pad" class="signature-pad" width=400 height=400></canvas>
                                                    </div>

                                                    <div class="card-footer">
                                                        <!-- <button type="button" id="save" class="btn btn-primary float-right"><i class="fas fa-save"></i></button> -->
                                                        <button type="button" id="clear" class="btn btn-primary float-right mr-1"><i class="fas fa-chalkboard"></i> &nbsp; Clear</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" id="submit" class="btn btn-success float-right">SIMPAN</button>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content') ?>

<?= $this->section('javascript') ?>
<script src="<?= asset_url('plugins/camshoot/jquery.camshoot.min.js'); ?>"></script>
<script src="<?= asset_url('plugins/signature_pad/signature_pad.min.js'); ?>"></script>
<script>
    let token = '<?= csrf_token() ?>';
    let hash = '<?= csrf_hash() ?>';

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#temp-img')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(200);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $(function() {
        // camera
        var container = $("#cameraContainer");

        let params = {};
        params[token] = hash;
        params['id_ref'] = <?= $absent['id'] ?>;
        $("#cameraContainer").camshoot({
            height: 490,
            width: 600,
            sound: true,
            imagetype: 'jpg',
            filename: 'photo_' + <?= $absent['id'] ?> + '_' + new Date().getTime(),
            flasheffect: true,
            services: '<?= site_url('home/ajax_capture') ?>',
            params: params,
            _token: hash,
            callback: (e) => {
                console.log('dadsa', e)
            }
        });


        //signature
        var canvas = document.getElementById('signature-pad');

        // Adjust canvas coordinate space taking into account pixel ratio,
        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }

        window.onresize = resizeCanvas;
        resizeCanvas();

        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
        });

        document.getElementById('clear').addEventListener('click', function() {
            signaturePad.clear();
        });

        let email = $('#email');

        $('#submit').click(function() {
            if (typeof $('.snap_thumb img').attr('src') == 'undefined') {
                return toastr.error("Please capture photo first.");
            }
            if (signaturePad.isEmpty()) {
                return toastr.error("Please provide a signature first.");
            }
            if (!$('#absent-form').valid())
                return;


            $('#save').click();
            var signature = signaturePad.toDataURL('image/png');
            // console.log(data);
            // $('.show-signature').html('<img class="img-fluid" src="' + data + '" />');
            let data = {
                id: <?= $absent['id'] ?>,
                email: email.val(),
                signature: signature,
            };
            data[token] = hash;

            $.ajax({
                url: '<?= site_url('home/ajax_absent'); ?>',
                data: data,
                method: 'post',
                success: function(res) {
                    let data = JSON.parse(res);
                    hash = data[token];

                    if (data.result) {
                        toastr.success('Success');
                    }

                    if ('errors' in data) {
                        for (const [key, value] of Object.entries(data.errors)) {
                            // console.log('key : ' + key, 'value : ' + value);
                            toastr.error(value);
                        }
                    }
                },
                error: function(e){
                    toastr.success('Refresh halaman');
                }
            });

        });
        $('#absent-form').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
            }
        })
    });
</script>
<?= $this->endSection('javascript') ?>