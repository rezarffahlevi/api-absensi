<?= $this->extend(env('theme.name') . '/' . env('theme.private')) ?>

<!-- stylesheet -->
<?= $this->section('stylesheet') ?>
<style>
    .custom-file, .custom-file-input, .custom-file-label {
        height: calc(2.0625rem + 2px);
    }
    
    .actions {
      margin: 1em 0;
    }

    /* Hide the progress bar when finished */
    .dz-previews .file-row.dz-success .progress {
      opacity: 0;
      transition: opacity 0.3s linear;
      display: none;
      transition: display 0.3s linear;
    }

    /* Hide the delete button initially */
    .dz-previews .file-row .delete {
      display: none;
    }

    /* Hide the start and cancel buttons and show the delete button */

    .dz-previews .file-row.dz-success .start,
    .dz-previews .file-row.dz-success .cancel {
      display: none;
    }

    .dz-previews .file-row.dz-success .delete {
      display: block;
    }
</style>
<?= $this->endSection('stylesheet') ?>

<!-- content_header -->
<?= $this->section('content_header') ?>
<div class="content-header">
    <div class="card bg-transparent shadow-none">
        <div class="card-header d-flex p-0">
            <h3 class="p-3 m-0">Pengajuan Andalalin - Dokumen</h3>
        </div>
    </div>
</div>
<?= $this->endSection('content_header') ?>
<!-- content -->
<?= $this->section('content') ?>
<?php
$input_dokumen = [
    [ 'name' => 'cover', 'title' => 'A. COVER DAN JUDUL DOKUMEN', 'description' => 'Ukuran file Maksimal 1MB, Format PDF'],
    [ 'name' => 'kata_pengantar', 'title' => 'B. KATA PENGANTAR', 'description' => 'Ukuran file Maksimal 100KB, Format PDF'],
    [ 'name' => 'daftar_isi', 'title' => 'C. DAFTAR ISI', 'description' => 'Ukuran file Maksimal 100KB, Format PDF'],
    [ 'name' => 'daftar_tabel', 'title' => 'D. DAFTAR TABEL', 'description' => 'Ukuran file Maksimal 100KB, Format PDF'],
    [ 'name' => 'daftar_gambar', 'title' => 'E. DAFTAR GAMBAR', 'description' => 'Ukuran file Maksimal 100KB, Format PDF'],
    [ 'name' => 'pendahuluan', 'title' => 'F. BAB 1 - PENDAHULUAN', 'description' => 'Ukuran file Maksimal 5MB, Format PDF'],
    [ 'name' => 'perencanaan', 'title' => 'G. BAB 2 - PERENCANAAN DAN METODOLOGI ANDALALIN', 'description' => 'Ukuran file Maksimal 4mb, Format PDF'],
    [ 'name' => 'analisis', 'title' => 'H. BAB 5 - REKOMENDASI PENANGANAN DAMPAK LALU LINTAS', 'description' => 'Ukuran file Maksimal 15MB, Format PDF'],
    [ 'name' => 'simulasi', 'title' => 'I. BAB 4 - SIMULASI KINERJA LALULINTAS', 'description' => 'Ukuran file Maksimal 10MB, Format PDF'],
    [ 'name' => 'rekomendasi', 'title' => 'J. COVER DAN JUDUL DOKUMEN', 'description' => 'Ukuran file Maksimal 15MB, Format PDF'],
    [ 'name' => 'penutup', 'title' => 'K. BAB 6 PENUTUP', 'description' => 'Ukuran file Maksimal 5MB, Format PDF'],
    [ 'name' => 'lampiran_gambar', 'title' => 'LAMPIRAN GAMBAR (WAJIB A3)', 'description' => 'Ukuran file Maksimal 50MB, Format PDF'],
    [ 'name' => 'perizinan', 'title' => 'LAMPIRAN PERIZINAN II (ASPEK LEGALITAS)', 'description' => 'Ukuran file Maksimal 10MB, Format PDF'],
    [ 'name' => 'lampiran_sertifikat', 'title' => 'LAMPIRAN III (SERTIFIKAT DAN SK KOMPETENSI PENYUSUN ANDALALIN', 'description' => 'Ukuran file Maksimal 5MB, Format PDF'],
    [ 'name' => 'persetujuan', 'title' => 'LAMPIRAN PERIZINAN IV (SCAN SK PERSETUJUAN YANG SUDAH TERBIT', 'description' => 'Ukuran file Maksimal 5MB, Format PDF'],
];
foreach ($input_dokumen as $k => $v) {
    $search_key = array_search($v['name'], array_column($data, 'name'));
    $id = 0;
    if ($search_key !== false) {
        $id = $data[$search_key]['id'];
    }
    $input_dokumen[$k]['file_id'] = $id;
}
?>
<div class="row">
    <?php foreach (array_chunk($input_dokumen, 8) as $key => $value) { ?>
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Upload Dokumen</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php foreach ($value as $k => $v) { ?>
                    <div class="card border shadow-none">
                        <div class="card-body">
                            <label for=""><?=$v['title']?></label>
                            <div class="form-group mb-0">
                                <label for=""><?=$v['description']?></label>
                                <div class="row" id="div_<?=$v['name']?>">
                                    <?php 
                                        $search_file_data = array_search($v['name'], array_column($data, 'name'));
                                    ?>
                                    <div class="row actions">
                                        <div class="col-lg-12">
                                            <!-- The fileinput-button span is used to style the file input field as button -->
                                            <button type="button" class="btn btn-success <?=$search_file_data !== false ? 'd-none' : ''?> add" id="fileinput_button_<?=$v['name']?>" onclick="add_button('<?=$v['name']?>', '<?=$v['title']?>')">
                                                <i class="fa fa-plus"></i>
                                                <span>Add File</span>
                                            </button>
                                            <button type="button" class="btn btn-primary d-none start" id="upload_button_<?=$v['name']?>" onclick="upload_button('<?=$v['name']?>', '<?=$v['title']?>')">
                                                <i class="fa fa-upload"></i>
                                                <span>Start Upload</span>
                                            </button>
                                            <button type="reset" class="btn btn-danger <?=$search_file_data !== false ? '' : 'd-none'?> cancel" id="delete_button_<?=$v['name']?>" onclick="delete_button('<?=$v['name']?>', '<?=$v['title']?>')">
                                                <i class="fa fa-trash"></i>
                                                <span>Delete File</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table table-striped files dz-previews" id="previews_<?=$v['name']?>">
                                        <?php
                                        if ($search_file_data !== false):
                                            $file_size = (float)$data[$search_file_data]['file_size'];
                                            $file_name = explode('_-_', $data[$search_file_data]['file_name']);
                                            $file_name = $file_name[1];
                                        ?>
                                        <div id="" class="file-row dz-processing dz-success dz-complete">
                                            <div>
                                                <p class="name" data-dz-name=""><?=$file_name?></p>
                                                <strong class="error text-danger" data-dz-errormessage=""></strong>
                                            </div>
                                            <div>
                                                <p data-dz-size=""><strong><?=round($file_size, 2, PHP_ROUND_HALF_UP)?></strong> MB</p>
                                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                    <div class="progress-bar progress-bar-success" style="width: 100%;" data-dz-uploadprogress=""></div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="col-md-12">
        <button type="submit" id="btn-simpan" class="btn btn-success float-right" disabled>SIMPAN</button>
    </div>
</div>

<div class="table table-striped files" id="previews">
    <div id="template" class="file-row">
        <div>
            <p class="name" data-dz-name></p>
            <strong class="error text-danger" data-dz-errormessage></strong>
        </div>
        <div>
            <p data-dz-size></p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content') ?>

<!-- javascript -->
<?= $this->section('javascript') ?>
<script src="<?= asset_url('plugins/dropzone/min/dropzone.min.js'); ?>"></script>
<script src="<?= asset_url('plugins/bs-custom-file-input/bs-custom-file-input.min.js'); ?>"></script>
<script>
    let token   = '<?= csrf_token() ?>';
    let hash    = '<?= csrf_hash() ?>';
    let dzVar = {};
    let key_name = '';
    let title_name = '';
    let listVar = <?=json_encode($input_dokumen)?>;

    (function() {
        bsCustomFileInput.init();

        // Get the template HTML and remove it from the doument
        var previewNode = document.querySelector("#template");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        listVar.forEach(val => {
            dzVar[val.name] = new Dropzone('div#div_'+val.name, {
                url: "<?=admin_url('andalalin/ajax_upload_dokumen')?>",
                init: function() { 
                    // this.hiddenFileInput.setAttribute("webkitdirectory", true); // Upload by folder
                    this.on('addedfile', function(file, message, a) {
                        if (this.files.length > 0) {
                            $('#upload_button_'+key_name).removeClass('d-none');
                        } else {
                            $('#upload_button_'+key_name).addClass('d-none');
                        }
                        
                        if (this.files.length > 1) {
                            this.removeFile(this.files[0]);
                        }
                        dzVar[key_name].file_name = file.name
                    });
                    this.on('reset', function() {
                        // alert('asdasdasd')
                    });
                    this.on("sending", function(file, xhr, formData) {
                        formData.append('id_andalalin', <?=$andalalin['id'];?>);
                        formData.append('type', key_name);
                        formData.append('title', title_name);
                        formData.append(token, hash);
                    });
                    this.on('success', function (file, xhr) {
                        let result = JSON.parse(xhr);

                        token = result.token
                        hash = result.hash
                        dzVar[key_name].file_id = result.id
                        enableButton()
                    })
                },
                dictFileTooBig: 'File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB',
                maxFilesize: val.max_size,
                acceptedFiles: 'application/pdf',
                previewTemplate: previewTemplate,
                autoQueue: false,
                previewsContainer: '#previews_'+val.name,
                clickable: '#fileinput_button_'+val.name,
            });
            dzVar[val.name].file_id = val.file_id
        });
        enableButton()
    })();

    function enableButton() {
        let enableSubmit = true
        for (let elem in dzVar) {
            if (enableSubmit == true && dzVar[elem].file_id < 1) {
                enableSubmit = false
            }
        }

        if (enableSubmit) {
            $('#btn-simpan').prop('disabled', false)
        } else {
            $('#btn-simpan').prop('disabled', true)
        }
    }
        
    function add_button(key, title) {
        key_name = key
        title_name = title
    }
    
    function upload_button(key, title) {
        key_name = key
        title_name = title
        dzVar[key_name].enqueueFiles(dzVar[key_name].getFilesWithStatus(Dropzone.ADDED));
        $('#fileinput_button_'+key_name).addClass('d-none');
        $('#upload_button_'+key_name).addClass('d-none');
        $('#delete_button_'+key_name).removeClass('d-none');
    }
    
    function delete_button(key, title) {
        key_name = key
        title_name = title
        let data    = { file_id: dzVar[key_name].file_id };
        data[token] = hash;

        $.ajax({
            url: '<?= admin_url('andalalin/ajax_delete_dokumen') ?>',
            type: 'post',
            data,
            success: function(json) {
                $('#previews_'+key_name).html('')
                json    = JSON.parse(json)
                token   = json.token
                hash    = json.hash
                dzVar[key_name].removeAllFiles(true);
                dzVar[key_name].file_id = 0
                
                $('#fileinput_button_'+key_name).removeClass('d-none');
                $('#upload_button_'+key_name).addClass('d-none');
                $('#delete_button_'+key_name).addClass('d-none');
                enableButton()
            }
        });
    }

    $('#btn-simpan').click(function() {
        let data    = { step: <?=$current_step;?>, id_andalalin: <?=$andalalin['id'];?> };
        data[token] = hash;

        $.ajax({
            url: '<?= admin_url('andalalin/ajax_submit_documents') ?>',
            type: 'post',
            data,
            success: function(res) {
                let result = JSON.parse(res);
                    hash = result[token];

                if (result.acknowledge == true) {
                    toastr.success(result.message);
                    setTimeout(() => {
                        window.location = "<?=admin_url('andalalin/update/'.$andalalin['id'])?>";
                    }, 1000);
                } else {
                    for (const [key, value] of Object.entries(result.errors)) {
                        toastr.error(value);
                        $('#save-submission').prop('disabled', false);
                    }
                }
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
            }
        });
    })
</script>
<?= $this->endSection('javascript') ?>