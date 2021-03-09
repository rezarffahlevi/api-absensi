<style>
    .pt-7 {
        padding-top: 7rem !important;
    }
</style>

<div class="card" id="heading-discussion">
    <div class="card-header remove-padding">
        <h5 class="mb-0">
            <button class="btn btn-link accordion-header" data-toggle="collapse" data-target="#collapse-discussion" aria-expanded="true" aria-controls="collapse-discussion">
                PEMBAHASAN
            </button>
        </h5>
    </div>

    <div id="collapse-discussion" class="collapse <?= $current_step == 3 ? 'show' : ''; ?>" aria-labelledby="heading-discussion" data-parent="#accordion">
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="meetingDate" class="col-md-3 col-form-label">Tanggal Rapat</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="meetingDate" readonly value="<?=isset($discussion['meeting_date']) ? $discussion['meeting_date'] : ''?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="time" class="col-md-3 col-form-label">Waktu</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="time" readonly value="<?=isset($discussion['meeting_time']) ? $discussion['meeting_time'] : ''?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location" class="col-md-3 col-form-label">Lokasi</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="location" readonly value="<?=isset($discussion['location']) ? $discussion['location'] : ''?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <h6>Pembahasan</h6>
                <?php
                if (!empty($discussion_detail)) {
                    foreach ($discussion_detail as $k => $v) {
                        $id = $v['id'];
                        $discussion = $v['discussion'];
                        $add_html = '
                        <div class="row">
                            <div class="col-md-9">
                                <textarea name="notes[]" class="form-control border border-dark notes_input" id="note_'.$id.'" data-id="'.$id.'" rows="5">'.$discussion.'</textarea>
                            </div>
                            <div class="col-md-2">
                                <button type="button" id="btn-add-note" class="btn btn-primary">Tambah</button>
                            </div>
                        </div>';
                        if ($k > 0) {
                            $add_html = '
                            <div class="row note-show mt-4">
                                <div class="col-md-9">
                                    <textarea name="notes[]" class="form-control border border-dark notes-input" id="note_'.$id.'" data-id="'.$id.'" rows="5">'.$discussion.'</textarea>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-danger btn-delete-note" id="delete_'.$id.'">Hapus</button>
                                </div>
                            </div>';
                        }
                        echo $add_html;
                    }
                } else {
                    echo '
                    <div class="row">
                        <div class="col-md-9">
                            <textarea name="notes[]" class="form-control border border-dark notes_input" id="note_0" rows="5"></textarea>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="btn-add-note" class="btn btn-primary">Tambah</button>
                        </div>
                    </div>';
                }
                ?>
                <span id="formNote"></span>
            </div>
            <div class="mt-4">
                <h6>Absensi</h6>
                <div class="row" id="formAbsent">
                    <div class="col-md-3">
                        <center>
                            <img class="profile-user-img img-fluid img-circle" src="<?= asset_url('img/avatar5.png'); ?>" alt="User profile picture">
                        </center>
                        <input type="text" class="form-control mt-2" placeholder="Institusi">
                        <input type="text" class="form-control mt-2" placeholder="Nama Pejabat">
                        <input type="text" class="form-control mt-2" placeholder="Jabatan">
                    </div>
                    <div class="col-md-3 pt-7">
                        <button type="button" class="btn btn-primary mb-1 d-block" data-toggle="modal" data-target="#addAbsentModal">Tambah</button>
                        <button type="button" id="btn-absent-link" class="btn btn-secondary">Link Absensi</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" id="btn-invitation" class="btn btn-success float-right" onclick="updateStep()">LANJUT</button>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addAbsentModal" tabindex="-1" role="dialog" aria-labelledby="addAbsentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addAbsentModalLabel">Tambah Peserta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="new_attendee_instance">Institusi</label>
            <input type="text" class="form-control" id="new_attendee_instance" placeholder="Institusi" value="" />
        </div>
        <div class="form-group">
            <label for="new_attendee_name">Nama Pejabat</label>
            <input type="text" class="form-control" id="new_attendee_name" placeholder="Nama Pejabat" value="" />
        </div>
        <div class="form-group">
            <label for="new_attendee_position">Jabatan</label>
            <input type="text" class="form-control" id="new_attendee_position" placeholder="Jabatan" value="" />
        </div>
        <div class="form-group">
            <label for="new_attendee_phone">No. Telp</label>
            <input type="text" class="form-control" id="new_attendee_phone" placeholder="No. Telp" value="" />
        </div>
        <div class="form-group">
            <label for="new_attendee_email">Email</label>
            <input type="text" class="form-control" id="new_attendee_email" placeholder="Email" value="" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="btn-add-absent" data-dismiss="modal">Tambah</button>
      </div>
    </div>
  </div>
</div>