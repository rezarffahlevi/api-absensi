<?= $this->extend(env('theme.name') . '/' . env('theme.private')) ?>

<!-- content_header -->
<?= $this->section('content_header') ?>
<div class="content-header">
    <div class="card bg-transparent shadow-none">
        <div class="card-header d-flex p-0">
            <h3 class="p-3 m-0">Pengajuan Andalalin : <?= $andalalin['name']; ?></h3>
        </div>
    </div>
</div>
<?= $this->endSection('content_header') ?>

<!-- content -->
<?= $this->section('content') ?>
<div id="accordion">
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
<link rel="stylesheet" href="<?= asset_url('plugins/fontawesome-free/css/all.min.css'); ?>">
<link rel="stylesheet" href="<?= asset_url('plugins/select2/css/select2.min.css'); ?>" />

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

    .remove-padding {
        padding: 0px !important;
    }

    .accordion-header {
        padding: 1.25rem 1.25rem;
        text-align: left;
        width: 100%;
    }
</style>
<?= $this->endSection('stylesheet') ?>

<!-- javascript -->
<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'); ?>"></script>
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

    // search
    var searchControl = L.esri.Geocoding.geosearch().addTo(map);
    var results = L.layerGroup().addTo(map);

    searchControl.on('results', function(data) {
        results.clearLayers();
        for (var i = data.results.length - 1; i >= 0; i--) {
            let longlat = data.results[i].latlng;

            longitude.value = longlat.lng;
            latitude.value = longlat.lat;

            results.addLayer(L.marker(data.results[i].latlng));
        }
    });

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
            remove: true
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


    // === SUBMISSION === //

    let token = '<?= csrf_token() ?>';
    let hash = '<?= csrf_hash() ?>';

    function checkNIB() {
        let oss_name = $('#oss_name');
        let oss_email = $('#oss_email');
        let oss_phone = $('#oss_phone');

        let data = {
            nib: $('#nib').val()
        };
        data[token] = hash;

        $.ajax({
            url: '<?= admin_url('andalalin/ajax_check_nib'); ?>',
            data: data,
            method: 'post',
            success: function(res) {
                let result = JSON.parse(res);
                hash = result[token];

                if (result.acknowledge == true) {
                    oss_name.val(result.data.oss_name);
                    oss_email.val(result.data.oss_email);
                    oss_phone.val(result.data.oss_phone);
                } else {
                    oss_name.val('');
                    oss_email.val('');
                    oss_phone.val('');
                    toastr.warning(result.message);
                }
            },
            error: function(request, status, error) {
                toastr.danger(request.responseText);
            }
        });
    }

    function call_modal() {
        $('#search-modal').modal('show');
    }

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        $('#select-consultant').hide();
        let tableOrganizer = id = '';

        let id_andalalin = $('input[name="id_andalalin"]');
        let no_andalalin = $('input[name="no_andalalin"]');
        let id_oss = $('input[name="id_oss"]');
        let step = $('input[name="step"]');

        let email = $('#email');
        let nib = $('#nib');
        let id_consultant = $('#id_consultant');
        let id_consultant_temp = $('#id_consultant_temp');
        let oss_name = $('#oss_name');
        let oss_email = $('#oss_email');
        let oss_phone = $('#oss_phone');

        let category = $('#category');
        let sub_category = $('#sub_category');
        let capacity = $('#capacity');
        let classification = $('#classification');
        let project_name = $('#project_name');
        let project_address = $('#project_address');

        let polygon = $('#coordinate');
        let latitude = $('#latitude');
        let longitude = $('#longitude');

        let billing_code = $('input[name="billing_code"]');
        let payment_status = $('input[name="payment_status"]');
        let payment_expired = $('input[name="payment_expired"]');

        tableOrganizer = $('#table-select-consultant').DataTable({
            stateSave: true,
            ordering: false,
            processing: true,
            serverSide: true,
            select: 'single',
            ajax: {
                url: "<?= admin_url('andalalin/ajax_organizer') ?>",
                type: 'POST',
                data: function(d) {
                    d[token] = hash;
                },
            },
            drawCallback: function(settings) {
                hash = settings.json[token];
            }
        });

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
                    d['id_consultant'] = id_consultant.val()
                },
            },
            drawCallback: function(settings) {
                hash = settings.json[token];
            }
        });

        let tableConsultantTemp = $('#table-consultant-temp').DataTable({
            stateSave: true,
            ordering: false,
            processing: true,
            serverSide: true,
            paging: false,
            ordering: false,
            info: false,
            searching: false,
            select: 'single',
            ajax: {
                url: "<?= site_url('home/ajax_andalalin') ?>",
                type: 'POST',
                data: function(d) {
                    d[token] = hash;
                    d['id_consultant'] = id_consultant_temp.val()
                },
            },
            drawCallback: function(settings) {
                hash = settings.json[token];
            }
        });

        tableOrganizer.on('select', function(e, dt, type, indexes) {
            var rowData = dt.rows(indexes).data().toArray();
            $('#id_consultant_temp').val(rowData[0][0]);
            $('#consultant_name_temp').text(rowData[0][2]);
            tableConsultantTemp.ajax.reload();
        })

        tableOrganizer.on('deselect', function(e, dt, type, indexes) {
            var rowData = dt.rows(indexes).data().toArray();
            $('#id_consultant_temp').val("");
            $('#consultant_name_temp').text("Consultant Name");
            tableConsultantTemp.ajax.reload();
        });

        $('#btn-choose-consultant').click(function() {
            $('#id_consultant').val($('#id_consultant_temp').val());
            $('#consultant_name').text($('#consultant_name_temp').text());
            tableConsultant.ajax.reload();

            $('#select-consultant').hide();
            $('#change-consultant').show();
            $('#search-modal').modal('hide');
        })


        // save submission
        $('#save-submission').click(function() {
            $('#save-submission').prop('disabled', true);

            if ($("#submission-form").valid()) {
                let data = {
                    step            : step.val(),
                    id_andalalin    : id_andalalin.val(),
                    id_oss          : id_oss.val(),
                    email           : email.val(),
                    id_consultant   : id_consultant.val(),
                    no_andalalin    : no_andalalin.val(),
                    nib             : nib.val(),
                    oss_name        : oss_name.val(),
                    oss_email       : oss_email.val(),
                    oss_phone       : oss_phone.val(),
                    category        : category.val(),
                    sub_category    : sub_category.val(),
                    capacity        : capacity.val(),
                    classification  : classification.val(),
                    project_name    : project_name.val(),
                    project_address : project_address.val(),
                    polygon         : polygon.val(),
                    latitude        : latitude.val(),
                    longitude       : longitude.val(),
                };
                console.log(data)
                data[token] = hash;

                $.ajax({
                    url: '<?= admin_url('andalalin/ajax_update_andalalin'); ?>',
                    data: data,
                    method: 'post',
                    success: function(res) {
                        let result = JSON.parse(res);
                        hash = result[token];

                        if (result.acknowledge == true) {
                            toastr.success(result.message);
                            setTimeout(() => {
                                window.location = result.data.redirect
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

            } else {
                $('#save-submission').prop('disabled', false);
            }
        })

        // save payment
        $('#save-payment').click(function() {
            $('#save-payment').prop('disabled', true);

            if ($("#payment-form").valid()) {
                let data = {
                    step: step.val(),
                    id_andalalin: id_andalalin.val(),
                    id_consultant: id_consultant.val(),
                    id_oss: id_oss.val(),
                    no_andalalin: no_andalalin.val(),
                    billing_code: billing_code.val(),
                    payment_status: payment_status.val(),
                    payment_expired: payment_expired.val(),
                };

                data[token] = hash;

                $.ajax({
                    url: '<?= admin_url('andalalin/ajax_submit_payemnt'); ?>',
                    data: data,
                    method: 'post',
                    success: function(res) {
                        let result = JSON.parse(res);
                        hash = result[token];

                        if (result.acknowledge == true) {
                            toastr.success(result.message);
                            setTimeout(() => {
                                $('#save-payment').prop('disabled', false);
                                window.location = result.data.redirect;
                            }, 1000);
                        } else {
                            for (const [key, value] of Object.entries(result.errors)) {
                                toastr.error(value);
                                $('#save-payment').prop('disabled', false);
                            }
                        }
                    },
                    error: function(request, status, error) {
                        toastr.danger(request.responseText);
                    }
                });

            } else {
                $('#save-payment').prop('disabled', false);
            }
        })

        // set validation
        $('#submission-form').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                username: {
                    required: true,
                },
                nib: {
                    required: true,
                },
                oss_name: {
                    required: true,
                },
                oss_email: {
                    required: true,
                },
                oss_phone: {
                    required: true,
                },
                category: {
                    required: true,
                },
                sub_category: {
                    required: true,
                },
                capacity: {
                    required: true,
                },
                classification: {
                    required: true,
                },
                project_name: {
                    required: true,
                },
                project_address: {
                    required: true,
                },
            },
            messages: {
                nib: {
                    required: null,
                },
            }
        });

        $('#payment-form').validate({
            rules: {
                payment_status: {
                    required: true,
                },
                payment_expired: {
                    required: true,
                },
                billing_code: {
                    required: true,
                },
            }
        })
    });
</script>
<?= $this->endSection('javascript') ?>