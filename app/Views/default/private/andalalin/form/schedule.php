<div class="card card-primary card-outline" id="heading-schedule">
    <div class="card-header p-0">
        <h5 class="card-title w-100">
            <a class="d-block w-100 collapsed p-4" data-toggle="collapse" href="#collapse-schedule" aria-expanded="false">
                RAPAT PEMBAHASAN
                <i class="fa fa-angle-down float-right"></i>
            </a>
        </h5>
    </div>

    <div id="collapse-schedule" class="collapse <?= $current_step == 4 ? 'show' : ''; ?>" aria-labelledby="heading-schedule" data-parent="#accordion">
        <?= form_open(admin_url("andalalin/save"), 'class="validate"') ?>
        <input type="hidden" name="step" value="2" />
        <input type="hidden" name="id_andalalin" value="<?= $andalalin['id'] ?>" />
        <input type="hidden" name="scheduled" value="" />
        <div class="card-body">
            <?php if (!empty($scheduled)): ?>
            <?php foreach($scheduled as $row): ?>
                <?php 
                $titletime      = @explode(':', $row['title']);
                $row['time']    = trim($titletime[0]).':'.trim($titletime[1]);
                $row['title']   = trim($titletime[2]);
                ?>
                    <a class="btn btn-light select-scheduled btn-block" <?= $info['user']['type'] == 'andalalin' ? 'href="' . admin_url("andalalin/discussion/{$andalalin['id']}") . '"' : ''; ?> data-discussion="<?= $row['id_discussion']; ?>">
                        <div class="info-box bg-transparent shadow-none mb-0">
                            <div class="info-box-content">
                                <span class="info-box-text text-center"><b><?= format_date($row['start']); ?>, <?= $row['time']; ?></b></span>
                            </div>
                        </div>
                    </a>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($add_schedule == true): ?>
                <a class="btn btn-primary" href="<?= admin_url("andalalin/schedule/{$andalalin['id']}"); ?>">
                    <div class="info-box bg-transparent shadow-none mb-0">
                        <div class="info-box-content">
                            <span class="info-box-text text-center text-white">Pilih Tanggal Rapat</span>
                        </div>
                    </div>
                </a>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <button type="submit" id="btn-schedule" class="btn btn-success float-right">SELESAI</button>
        </div>
    </div>
    <?= form_close() ?>
</div>