<div class="card-header p-0 mb-3">
    <h4 class="card-title w-100">
        <div class="ribbon-wrapper">
            <div class="ribbon bg-indigo text-xs">
                <?= $andalalin['payment_status'] == 'pending' ? 'Belum lunas' : ($andalalin['payment_status'] == 'finish' ? 'Lunas' : $andalalin['payment_status']); ?>  
            </div>
        </div>
        <button class="btn accordion-header btn-warning text-center p-8" data-toggle="modal" data-target="#modal-payment" style="font-size: 1.1rem;">
            PEMBAYARAN PNBP
        </button>
    </h4>
</div>


<div class="modal fade" id="modal-payment" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">PEMBAYARAN PNBP</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?= form_open(admin_url("andalalin/save"), 'class="" id="payment-form"') ?>
            <input type="hidden" name="step" value="<?= $current_step; ?>" />
            <input type="hidden" name="id_andalalin" value="<?= $andalalin['id'] ?>" />
            <input type="hidden" name="id_oss" value="<?= $andalalin['id_oss'] ?>" />
            <input type="hidden" name="payment_status" value="<?= $andalalin['payment_status'] ?>" />
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="no_andalalin">No. Andalalin</label>
                    <input type="text" class="form-control" name="no_andalalin" value="<?= $andalalin['no_andalalin'] ?>" readonly/>
                </div>
                <div class="form-group">
                    <label for="billing_code">Kode Billing</label>
                    <input type="text" class="form-control" id="billing_code" name="billing_code" value="<?= $andalalin['billing_code'] ?>" readonly/>
                </div>
                <div class="form-group">
                    <label for="billing_code">Berlaku Hingga</label>
                    <input type="text" class="form-control" id="payment_expired" name="payment_expired" value="<?= $andalalin['payment_expired'] ?>" readonly/>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="save-payment">Simpan</button>
            </div>
        <?= form_close() ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>