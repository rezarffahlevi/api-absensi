<div class="card card-primary card-outline" id="heading-ability">
    <div class="card-header p-0">
        <h5 class="card-title w-100">
            <a class="d-block w-100 collapsed p-4" data-toggle="collapse" href="#collapse-ability" aria-expanded="false">
                KESANGGUPAN
                <i class="fa fa-angle-down float-right"></i>
            </a>
        </h5>
    </div>

    <div id="collapse-ability" class="collapse <?= $current_step == 4 ? 'show' : ''; ?>" aria-labelledby="heading-ability" data-parent="#accordion">
        <?= form_open(admin_url("andalalin/save/{$andalalin['id']}"), 'class="validate"') ?>
        <input type="hidden" name="step" value="<?= $current_step; ?>" />
        <input type="hidden" name="id_andalalin" value="<?= $andalalin['id'] ?>" />
        <input type="hidden" name="id_discussion" value="<?= $discussion['id'] ?>" />
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Hasil Peninjauan</label>
                        <textarea name="result_review" class="form-control border border-dark notes_input" id="result_review" data-id="result_review'" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Asistensi </label>
                        <select class="form-control select2bs4" style="width: 100%;">
                            <option value="0">Tidak Ada</option>
                            <?php if (!empty($attendee)) : ?>
                                <?php foreach ($attendee as $row) : ?>
                                    <option value="0"><?= $row['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-success float-right" onclick="modal_finish()">SELESAI</button>
        </div>
        <?= form_close() ?>
    </div>
</div>

<div class="modal fade" id="modal-finish" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= form_open(admin_url("andalalin/save/{$andalalin['id']}")) ?>
            <div class="modal-header">
                <h4 class="modal-title">Kesanggupan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-outline-primary btn-block" style="height: 70px;" onclick="updateStep()">Rapat Lanjutan</button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-outline-primary btn-block" style="height: 70px;">Unggah Pernyaataan Kesanggupan</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <!-- <button type="button" class="btn btn-primary" onclick="setSchedule()">Simpan</button> -->
            </div>
            <?= form_close() ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>