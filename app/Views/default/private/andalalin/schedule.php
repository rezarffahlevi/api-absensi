<?= $this->extend(env('theme.name') . '/' . env('theme.private')) ?>

<!-- content_header -->
<?= $this->section('content_header') ?>
<div class="content-header">
    <div class="card bg-transparent shadow-none">
        <div class="card-header d-flex p-0">
            <h3 class="p-3 m-0">Pengajuan Andalalin - <?= $andalalin['name']; ?></h3>
        </div>
    </div>
</div>
<?= $this->endSection('content_header') ?>

<!-- content -->
<?= $this->section('content') ?>
<div class="card" id="heading-schedule">
    <div class="card-header remove-padding">
    <h5 class="mb-0">JADWAL RAPAT</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-9">
                <div class="card border shadow-none">
                    <div class="card-body pt-0">
                        <!-- THE CALENDAR -->
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border shadow-none">
                    <div class="card-header">
                        <h3 class="card-title">Catatan</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($holidays)) : ?>
                            <ul class="nav nav-pills flex-column">
                                <?php foreach ($holidays as $row) : ?>
                                    <li class="nav-item">
                                        <a class="nav-link">
                                            <i class="far fa-circle text-danger mr-2"></i>
                                            <?= date('d M Y', strtotime($row['start'])); ?> - <?= empty($row['title']) ? 'Tidak terdefinisi' : $row['title']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p class="text-center m-0 p-3">Tidak ada libur</p>
                        <?php endif; ?>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= form_open(admin_url("andalalin/save/{$andalalin['id']}"), 'id="next"') ?>
            <input type="hidden" id="step" name="step" value="2" />
            <input type="hidden" id="id_andalalin" name="id_andalalin" value="<?= $andalalin['id'] ?>" />
            <button type="submit" id="btnPengajuan" class="btn btn-success float-right">LANJUT</button>
        <?= form_close() ?>  
    </div>
</div>

<div class="modal fade" id="modal-tgl-rapat" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="overlay d-flex justify-content-center align-items-center">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>
            <?= form_open(admin_url('andalalin/schedule'), 'id="loginForm"') ?>
            <div class="modal-header">
                <h4 class="modal-title">Default Modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Data tidak dapat dimuat.
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="setSchedule()">Simpan</button>
            </div>
            <?= form_close() ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<?= $this->endSection('content') ?>


<!-- stylesheet -->
<?= $this->section('stylesheet') ?>
<!-- fullCalendar 2.2.5 -->
<link rel="stylesheet" href="<?= asset_url('plugins/fullcalendar/main.css'); ?>" />
<?= $this->endSection('stylesheet') ?>

<!-- javascript -->
<?= $this->section('javascript') ?>
<!-- fullCalendar 2.2.5 -->
<script src="<?= asset_url('plugins/moment/moment.min.js'); ?>"></script>
<script src="<?= asset_url('plugins/fullcalendar/main.js'); ?>"></script>
<script>

let token   = '<?= csrf_token() ?>';
let hash    = '<?= csrf_hash() ?>';
let csrf    = $('input[name="'+ token +'"]');

// calendar
let date = new Date()
let d = date.getDate(),
    m = date.getMonth(),
    y = date.getFullYear()

let Calendar    = FullCalendar.Calendar;
let calendarEl  = document.getElementById('calendar');
let calendar    = new Calendar(calendarEl, {
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
    },
    themeSystem: 'bootstrap',
    //Random default events
    events: <?= json_encode($scheduled); ?>,
    editable: true,
    droppable: false,
    eventClick: function(info) {
        let props = info.event.extendedProps;
        // has event
        if (typeof props.id_discussion != 'undefined') {
            getSchedule(props.id_discussion);
        }
    },
    dateClick: function(info) {
        let todayAndFuture  = moment().format('YYYY-MM-DD') <= moment(info.dateStr).format('YYYY-MM-DD');
        let limit30days     = moment().add(30, 'days').format('YYYY-MM-DD') >= moment(info.dateStr).format('YYYY-MM-DD')

        if (todayAndFuture && limit30days) {
            checkSchedule(info.dateStr);
        }
    }
});

calendar.render();

function checkSchedule(selectedDate) {
    let modal   = $('#modal-tgl-rapat');

    modal.find('.overlay').removeAttr("style");
    modal.find('.modal-title').text(moment(selectedDate).format('D MMM YYYY'));
    modal.modal('show');

    let data = {
        date: selectedDate
    };
    data[token] = hash;

    $.ajax({
        url: '<?= admin_url('andalalin/ajax_check_schedule') ?>',
        type: 'post',
        data,
        success: function(res) {
            let result  = JSON.parse(res);
                hash    = result[token];
                $('input[name="'+ token +'"]').val(hash);
            if (result.acknowledge == true) {
                modal.find('.modal-body').html(result.data.schedule);

                setTimeout(() => {
                    // hide overlay
                    modal.find('.overlay').attr("style", "display: none !important");
                }, 500);
            } else {
                toastr.warning(result.message);
            }
        },
        error: function(request, status, error) {
            toastr.danger(request.responseText);
        }
    });
}

function selectSchedule(el) {
    if (!el.hasClass('disabled')) {
        $('.schedule').not('.disabled').removeClass('btn-indigo active')
            .addClass('btn-outline-indigo');

        if (!el.hasClass('active')) {
            el.removeClass('btn-outline-indigo')
                .addClass('btn-indigo active');

            $('input[name="schedule"]').val(el.data('session'));
        }
    }
}

function setSchedule() {
    let schedule        = $('input[name="schedule"]').val();
    let selected_date   = $('input[name="selected_date"]').val();
    let location        = $('textarea[name="location"]').val();
    let schedules       = ['08:00', '10:00', '13:00', '15:00'];
    let validation      = true;

    // validation
    if (schedule == '') {
        validation = false;
        toastr.warning('Jam rapat belum di pilih');
    }
    if (location == '') {
        validation = false;
        toastr.warning('Tentukan lokasi rapat');
    }
    
    if (validation == true) {
        let data = {
            id_andalalin: '<?=$andalalin['id'];?>',
            date: selected_date,
            session: schedule,
            location: location
        };
        data[token] = csrf.val();

        $.ajax({
            url: '<?= admin_url('andalalin/ajax_save_schedule') ?>',
            type: 'post',
            data,
            success: function(res) {
                let result  = JSON.parse(res);
                    hash    = result[token];
                    csrf.val(hash);

                if (result.acknowledge == true) {
                    let event           = [{
                        title: schedules[schedule] + ' <?=$andalalin['name']; ?>',
                        start: selected_date,
                        // backgroundColor: '#f56954', //red
                    }];
                    calendar.addEventSource(event);
                    calendar.refetchEvents();

                    toastr.success(result.message);
                    setTimeout(() => {
                        window.location = result.data.redirect
                    }, 1000);
                } else {
                    for (const [key, value] of Object.entries(result.errors)) {
                        toastr.error(value);
                    }
                }
                $('#modal-tgl-rapat').modal('hide');
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
                $('#modal-tgl-rapat').modal('hide');
            }
        });
    }
}
</script>
<?= $this->endSection('javascript') ?>