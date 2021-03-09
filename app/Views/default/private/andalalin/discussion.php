<?= $this->extend(env('theme.name') . '/' . env('theme.private')) ?>

<!-- content_header -->
<?= $this->section('content_header') ?>
<div class="content-header">
    <div class="card bg-transparent shadow-none">
        <div class="card-header d-flex p-0">
            <h3 class="p-3 m-0">Pembahasan Andalalin - <?= $andalalin['name']; ?></h3>
        </div>
    </div>
</div>
<?= $this->endSection('content_header') ?>

<!-- content -->
<?= $this->section('content') ?>
<div id="accordion">
    <?= $this->include(env('theme.name') . '/private/andalalin/form/submission_disabled'); ?>
    <?php if (!empty($step)) : ?>
        <?php foreach ($step as $row) : ?>
            <?php if ($row['show'] == true && isset($row['template'])) : ?>
                <?= $this->include(env('theme.name') . $row['template']); ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection('content') ?>

<!-- stylesheet -->
<?= $this->section('stylesheet') ?>
<link rel="stylesheet" href="<?= asset_url('plugins/select2/css/select2.min.css'); ?>" />
<link rel="stylesheet" href="<?= asset_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
<!-- Leaflet -->
<link rel="stylesheet" href="<?= asset_url('plugins/leaflet/leaflet.css'); ?>" />
<link rel="stylesheet" href="<?= asset_url('plugins/leaflet/draw/leaflet.draw.css'); ?>" />
<link rel="stylesheet" href="<?= asset_url('plugins/leaflet/esri/geocoder/esri-leaflet-geocoder.css'); ?>" />
<!-- DataTables -->
<link rel="stylesheet" href="<?= asset_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= asset_url('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= asset_url('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= asset_url('plugins/datatables-select/css/select.bootstrap4.min.css'); ?>">
<style>
    .tooltip.show p {
        text-align: left;
    }

    #mapid {
        height: 525px;
    }
</style>
<?= $this->endSection('stylesheet') ?>

<!-- javascript -->
<?= $this->section('javascript') ?>
<!-- Select2 -->
<script src="<?= asset_url('plugins/select2/js/select2.full.min.js') ?>"></script>
<!-- Leaflet -->
<script src="<?= asset_url('plugins/leaflet/leaflet.js'); ?>"></script>
<script src="<?= asset_url('plugins/leaflet/draw/leaflet.draw.js'); ?>"></script>
<script src="<?= asset_url('plugins/leaflet/esri/esri-leaflet.js'); ?>"></script>
<script src="<?= asset_url('plugins/leaflet/esri/geocoder/esri-leaflet-geocoder.js'); ?>"></script>
<!-- DataTables -->
<script src="<?= asset_url('plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= asset_url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= asset_url('plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= asset_url('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?= asset_url('plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= asset_url('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= asset_url('plugins/datatables-select/js/dataTables.select.min.js'); ?>"></script>
<script>
    let token = '<?= csrf_token() ?>';
    let hash = '<?= csrf_hash() ?>';
    let csrf = $('input[name="' + token + '"]');

    //===== LEAFLET =====//
    var map = L.map('mapid').setView([-2.4, 125], 4);
    var coordinate = document.getElementById('coordinate');
    var longitude = document.getElementById('longitude');
    var latitude = document.getElementById('latitude');

    // Initialize mapbox layer
    L.tileLayer(`https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png`, {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
        maxZoom: 20,
        tileSize: 512,
        zoomOffset: -1,
    }).addTo(map);

    // add layer
    var editableLayers = new L.FeatureGroup();
    map.addLayer(editableLayers);

    // set polygon options
    var polygon = {
        allowIntersection: false, // Restricts shapes to simple polygons
        drawError: {
            color: '#ff4c52', // Color the shape will turn when intersects
            message: '<strong>Polygon draw does not allow intersections!<strong> (allowIntersection: false)' // Message that will show when intersect
        },
        shapeOptions: {
            color: '#3e8ef7',
            weight: 2
        }
    };

    if (coordinate.value != '') {
        polygon = false;

        var value = JSON.parse(coordinate.value);
        L.geoJSON(value, {
            onEachFeature: onEachFeature
        }).addTo(map);
    }

    var drawPluginOptions = {
        draw: {
            polyline: false,
            polygon: polygon,
            circle: false,
            rectangle: false,
            marker: false,
            circlemarker: false
        },
        edit: {
            featureGroup: editableLayers, //REQUIRED!!
            remove: false
        }
    };

    var drawControl = new L.Control.Draw(drawPluginOptions);
    map.addControl(drawControl);

    // Get current location
    if (latitude.value == '') {
        map.locate({
            setView: true,
            maxZoom: 20
        });
    } else {
        map.setView([latitude.value, longitude.value], 20);
        // results.clearLayers();
        results.addLayer(L.marker([latitude.value, longitude.value]));
    }

    map.on('draw:created', function(e) {
        // Remove draw polygon options
        drawControl.setDrawingOptions({
            polygon: false
        });
        map.removeControl(drawControl);
        map.addControl(drawControl);

        var type = e.layerType,
            layer = e.layer;

        editableLayers.addLayer(layer);

        // set value
        coordinate.value = JSON.stringify(layer.toGeoJSON());
    });

    map.on('draw:deleted', function(e) {
        // Add draw polygon options
        drawControl.setDrawingOptions({
            polygon: true
        });
        map.removeControl(drawControl);
        map.addControl(drawControl);

        // set value
        coordinate.value = '';
    })

    // set layer on feature
    function onEachFeature(feature, layer) {
        editableLayers.addLayer(layer);
    }
    //===== LEAFLET =====//

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        let tableConsultant = $('#table-consultant').DataTable({
            stateSave: true,
            ordering: false,
            processing: true,
            serverSide: true,
            paging: false,
            ordering: false,
            info: false,
            select: 'single',
            ajax: {
                url: "<?= admin_url('andalalin/ajax_andalalin') ?>",
                type: 'POST',
                data: function(d) {
                    d[token] = hash;
                    d['id_consultant'] = <?= $andalalin['id_consultant']; ?>
                },
            },
            drawCallback: function(settings) {
                hash = settings.json[token];
            }
        });
        if (<?= !empty($step[5]['show']) ? $step[5]['show'] : 0 ?>) {
            interval_func();
        }
    });

    function addAttendee() {
        let step = $('input[name="step"]');
        let id_andalalin = $('input[name="id_andalalin"]');
        let id_discussion = $('input[name="id_discussion"]');
        let attendee_instance = $('#attendee_instance');
        let attendee_name = $('#attendee_name');
        let attendee_phone = $('#attendee_phone');
        let attendee_email = $('#attendee_email');

        let data = {
            step: step.val(),
            id_andalalin: id_andalalin.val(),
            id_discussion: id_discussion.val(),
            instance: attendee_instance.val(),
            name: attendee_name.val(),
            phone: attendee_phone.val(),
            email: attendee_email.val(),
        };
        data[token] = csrf.val();

        $.ajax({
            url: '<?= admin_url('andalalin/ajax_submit_attendee'); ?>',
            data: data,
            method: 'post',
            success: function(res) {
                let result = JSON.parse(res);
                hash = result[token];
                csrf.val(hash);

                if (result.acknowledge == true) {
                    html = `<tr>
                        <td>` + result.data.instance + `</td>
                        <td>` + result.data.name + `</td>
                        <td>` + result.data.phone + `</td>
                        <td>` + result.data.email + `</td>
                        <td class="text-center" style="width: 40px">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="confirmation[` + result.data.id + `]" class="custom-control-input" id="switch-` + result.data.id + `" onclick="toggleConfirm(` + result.data.id + `)" />
                                <label class="custom-control-label" for="switch-` + result.data.id + `"></label>
                            </div>
                        </td>
                    </tr>`;
                    $('#table-attendee').find('tbody').append(html);

                    attendee_instance.val('');
                    attendee_name.val('');
                    attendee_phone.val('');
                    attendee_email.val('');

                    toastr.success(result.message);
                } else {
                    for (const [key, value] of Object.entries(result.errors)) {
                        toastr.error(value);
                    }
                }
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
            }
        });
    }

    function toggleConfirm(id_absent) {
        let step = $('input[name="step"]');
        let id_andalalin = $('input[name="id_andalalin"]');
        let id_discussion = $('input[name="id_discussion"]');
        let attendee_confirmation = $('input[name="confirmation[' + id_absent + ']"]:checked');

        let data = {
            step: step.val(),
            id_andalalin: id_andalalin.val(),
            id_discussion: id_discussion.val(),
            id_absent: id_absent,
            confirmation: attendee_confirmation.length
        };
        data[token] = hash;

        $.ajax({
            url: '<?= admin_url('andalalin/ajax_toggle_confirm_attendee'); ?>',
            data: data,
            method: 'post',
            success: function(res) {
                let result = JSON.parse(res);
                hash = result[token];
                csrf.val(hash);

                if (result.acknowledge == true) {
                    attendee_confirmation.prop('checked', result.data.confirmation == 1 ? true : false);

                    toastr.success(result.message);
                } else {
                    for (const [key, value] of Object.entries(result.errors)) {
                        toastr.error(value);
                    }
                }
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
            }
        });
    }

    function updateStep(id_absent) {
        let step = $('input[name="step"]');
        let id_andalalin = $('input[name="id_andalalin"]');

        let data = {
            step: step.val(),
            id_andalalin: id_andalalin.val(),
        };
        data[token] = hash;

        $.ajax({
            url: '<?= admin_url('andalalin/ajax_update_discussion_step'); ?>',
            data: data,
            method: 'post',
            success: function(res) {
                let result = JSON.parse(res);
                hash = result[token];
                csrf.val(hash);

                if (result.acknowledge == true) {
                    toastr.success(result.message);
                    setTimeout(() => {
                        window.location = result.data.redirect
                    }, 1000);
                } else {
                    for (const [key, value] of Object.entries(result.errors)) {
                        toastr.error(value);
                    }
                }
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
            }
        });
    }


    // === DISCUSSION === //
    let ordered_number = 1
    $('#btn-add-note').click(function() {
        let add_html = `
    <div class="row note-show mt-4">
        <div class="col-md-9">
            <textarea name="notes[]" class="form-control border border-dark notes-input" id="note_${ordered_number}" rows="5"></textarea>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-danger btn-delete-note" id="delete_${ordered_number}">Hapus</button>
        </div>
    </div>
    <span id="formNote"></span>
    `;
        $('#formNote').replaceWith(add_html);
        ordered_number++;
    });

    $(document).on('click', '.btn-delete-note', function() {
        let this_delete = $(this);
        let id_andalalin_discussion_detail = this_delete.attr('id');
        id_andalalin_discussion_detail = id_andalalin_discussion_detail.split('_');
        id_andalalin_discussion_detail = id_andalalin_discussion_detail[1];
        let data = {
            id_andalalin_discussion_detail,
        };

        data[token] = hash;
        $.ajax({
            url: '<?= site_url('admin/andalalin/ajax_delete_note'); ?>',
            data: data,
            method: 'post',
            success: function(res) {
                let result = JSON.parse(res);
                hash = result[token];
                csrf.val(hash);

                this_delete.parents(".note-show").fadeOut();
                this_delete.parents(".note-show").remove();
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
            }
        });
    });

    $(document).on('click', '#btn-absent-link', function() {
        let id_discussion = $('input[name="id_discussion"]');
        let data = {
            id_andalalin_discussion: id_discussion.val(),
        };
        data[token] = hash;

        $.ajax({
            url: '<?= site_url('admin/andalalin/ajax_send_absent_link'); ?>',
            data: data,
            method: 'post',
            success: function(res) {
                let result = JSON.parse(res);
                hash = result[token];
                csrf.val(hash);

                let copyText = result.link;

                var textArea = document.createElement("textarea");
                textArea.value = copyText;

                // Avoid scrolling to bottom
                textArea.style.top = "0";
                textArea.style.left = "0";
                textArea.style.position = "fixed";

                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'berhasil' : 'gagal';
                    console.log('Fallback: Copying text command was ' + msg);
                    alert('Copy link absensi ' + msg)
                } catch (err) {
                    console.error('Fallback: Oops, unable to copy', err);
                }

                document.body.removeChild(textArea);
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
            }
        });
    });

    $('#btn-add-absent').click(function() {
        let step = $('input[name="step"]');
        let id_andalalin = $('input[name="id_andalalin"]');
        let id_discussion = $('input[name="id_discussion"]');
        let attendee_instance = $('#new_attendee_instance');
        let attendee_name = $('#new_attendee_name');
        let attendee_position = $('#new_attendee_position');
        let attendee_phone = $('#new_attendee_phone');
        let attendee_email = $('#new_attendee_email');
        let data = {
            step: step.val(),
            id_andalalin: id_andalalin.val(),
            id_discussion: id_discussion.val(),
            instance: attendee_instance.val(),
            name: attendee_name.val(),
            position: attendee_position.val(),
            phone: attendee_phone.val(),
            email: attendee_email.val(),
        };
        data[token] = csrf.val();

        $.ajax({
            url: '<?= admin_url('andalalin/ajax_submit_attendee'); ?>',
            data: data,
            method: 'post',
            success: function(res) {
                let result = JSON.parse(res);
                hash = result[token];
                csrf.val(hash);

                if (result.acknowledge == true) {
                    attendee_instance.val('');
                    attendee_name.val('');
                    attendee_position.val('');
                    attendee_phone.val('');
                    attendee_email.val('');

                    toastr.success(result.message);
                } else {
                    for (const [key, value] of Object.entries(result.errors)) {
                        toastr.error(value);
                    }
                }
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
            }
        });
    })

    function interval_func() {
        setInterval(load_absent, 20000);
        setInterval(submit_notes, 20000);
    }

    function load_absent() {
        let id_discussion = $('input[name="id_discussion"]');
        let data = {
            id_andalalin_discussion: id_discussion.val(),
        };

        data[token] = hash;

        $.ajax({
            url: '<?= site_url('admin/andalalin/ajax_load_absent'); ?>',
            data: data,
            method: 'post',
            success: function(res) {
                let result = JSON.parse(res);
                hash = result[token];
                csrf.val(hash);

                if (result.data.length > 0) {
                    let add_html = '';
                    result.data.forEach(val => {
                        add_html += `
                        <div class="col-md-3">
                            <center>
                                <img class="profile-user-img img-fluid img-circle" src="<?= asset_url('img/avatar5.png'); ?>" alt="User profile picture">
                            </center>
                            <input type="text" class="form-control mt-2" placeholder="Institusi" value="${val.instance}">
                            <input type="text" class="form-control mt-2" placeholder="Nama Pejabat" value="${val.name}">
                            <input type="text" class="form-control mt-2" placeholder="Jabatan" value="${val.position}">
                        </div>
                    `
                    });
                    add_html += `
                    <div class="col-md-3 pt-7">
                        <button type="button" class="btn btn-primary mb-1 d-block" data-toggle="modal" data-target="#addAbsentModal">Tambah</button>
                        <button type="button" id="btn-absent-link" class="btn btn-secondary">Link Absensi</button>
                    </div>
                `;

                    $('#formAbsent').html(add_html);
                }
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
            }
        });
    }

    function submit_notes() {
        let notes_data = {};
        let run_submit = false;
        let get_notes = $("textarea[name='notes[]']").map(function() {
            let val_note = $(this).val();
            let id_note = $(this).attr('id');
            if (val_note) {
                run_submit = true;
                let obj = {
                    val_note
                };
                if ($(this).data('id')) {
                    obj.id = $(this).data('id');
                }
                notes_data[id_note] = obj;
            }
        })

        let id_discussion = $('input[name="id_discussion"]');
        let data = {
            id_andalalin_discussion: id_discussion.val(),
            notes_data
        };

        data[token] = hash;

        if (run_submit) {
            $.ajax({
                url: '<?= site_url('admin/andalalin/ajax_submit_notes'); ?>',
                data: data,
                method: 'post',
                success: function(res) {
                    let result = JSON.parse(res);
                    hash = result[token];
                    csrf.val(hash);

                    result.notes_data.forEach(v => {
                        $('#' + v.from).data('id', v.id_andalalin_discussion_detail);
                        $('#' + v.from).attr('id', v.to);
                        $('#delete_' + v.key).attr('id', '#delete_' + v.id_andalalin_discussion_detail);
                    })
                },
                error: function(request, status, error) {
                    toastr.danger(request.responseText);
                }
            });
        }
    }
    // === DISCUSSION === //

    function modal_finish() {
        $('#modal-finish').modal('show');
    }
</script>
<?= $this->endSection('javascript') ?>