<div class="card card-primary card-outline" id="heading-preparation">
    <div class="card-header p-0">
        <h5 class="card-title w-100">
            <a class="d-block w-100 collapsed p-4" data-toggle="collapse" href="#collapse-preparation" aria-expanded="false">
                UNDANGAN
                <i class="fa fa-angle-down float-right"></i>
            </a>
        </h5>
    </div>

    <div id="collapse-preparation" class="collapse <?= $current_step == 4 ? 'show' : ''; ?>" aria-labelledby="heading-preparation" data-parent="#accordion">
        <?= form_open(admin_url("andalalin/save/{$andalalin['id']}"), 'class="validate"') ?>
        <input type="hidden" name="step" value="<?= $current_step; ?>" />
        <input type="hidden" name="id_andalalin" value="<?= $andalalin['id'] ?>" />
        <input type="hidden" name="id_discussion" value="<?= $discussion['id'] ?>" />
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tanggal Rapat</label>
                        <input type="text" class="form-control" placeholder="Tanggal Rapat" value="<?= format_date($discussion['meeting']); ?>" readonly />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Waktu Rapat</label>
                        <input type="text" class="form-control" placeholder="Waktu Rapat" value="<?= date('H:i', strtotime($discussion['meeting'])); ?>" readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Lokasi Rapat</label>
                        <input type="text" class="form-control" placeholder="Lokasi Rapat" value="<?= $discussion['location'] ?>" readonly />
                    </div>
                </div>
                <div class="col-md-12">
                    <label>List Peserta</label>
                    <table class="table table-bordered" id="table-attendee">
                        <thead>
                            <tr>
                                <th>Institusi</th>
                                <th>Nama Pejabat</th>
                                <th>No. Telp</th>
                                <th>Email</th>
                                <th style="width: 40px">Konfirmasi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($attendee)): ?>
                            <?php foreach($attendee as $row): ?>
                            <tr>
                                <td><?= $row['instance']; ?></td>
                                <td><?= $row['name']; ?></td>
                                <td><?= $row['phone']; ?></td>
                                <td><?= $row['email']; ?></td>
                                <td class="text-center" style="width: 40px">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="confirmation[<?= $row['id']; ?>]" 
                                            class="custom-control-input" id="switch-<?= $row['id']; ?>" 
                                            onclick="toggleConfirm(<?= $row['id']; ?>)"
                                            <?= $row['confirmation'] == "1" ? "checked" : ''; ?> />
                                        <label class="custom-control-label" for="switch-<?= $row['id']; ?>"></label>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control" id="attendee_instance" placeholder="Institusi" value="" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control" id="attendee_name" placeholder="Nama Pejabat" value="" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control" id="attendee_phone" placeholder="No. Telp" value="" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control" id="attendee_email" placeholder="Email" value="" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <button class="btn btn-primary" id="attendee_submit" type="button" onclick="addAttendee()">Tambah</button>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" id="btn-invitation" class="btn btn-success float-right" onclick="updateStep()">LANJUT</button>
        </div>
        <?= form_close() ?>
    </div>
</div>