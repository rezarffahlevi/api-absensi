<?= $this->extend(env('theme.name').'/'.env('theme.private')) ?>

<?= $this->section('stylesheet') ?>
<link rel="stylesheet" href="<?= asset_url('plugins/leaflet/leaflet.css'); ?>" />
<link rel="stylesheet" href="<?= asset_url('plugins/leaflet/draw/leaflet.draw.css'); ?>" />
<link rel="stylesheet" href="<?= asset_url('plugins/leaflet/esri/geocoder/esri-leaflet-geocoder.css'); ?>" />
<style>
    #mapid { height: 400px; }
</style>
<?= $this->endSection('stylesheet') ?>


<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Leaflet Maps</h5>
            </div>

            <div class="card-body p-0" style="display: block;">
                <div id="mapid"></div>
            </div>

            <div class="card-footer">
                <input type="hidden" id="coordinate" name="coordinate" value='{"type":"Feature","properties":{},"geometry":{"type":"Polygon","coordinates":[[[106.769916,-6.367333],[106.76999,-6.36727],[106.770691,-6.36759],[106.770573,-6.367832],[106.769843,-6.367531],[106.769916,-6.367333]]]}}' />
                <input type="hidden" id="longitude" name="longitude" value='' />
                <input type="hidden" id="latitude" name="latitude" value='' />
            </div>
        </div>
    </div><!-- col-md-7 -->
</div>
<?= $this->endSection('content') ?>

<?= $this->section('javascript') ?>
<script src="<?= asset_url('plugins/leaflet/leaflet.js'); ?>"></script>
<script src="<?= asset_url('plugins/leaflet/esri/esri-leaflet.js'); ?>"></script>
<script src="<?= asset_url('plugins/leaflet/esri/geocoder/esri-leaflet-geocoder.js'); ?>"></script>
<script src="<?= asset_url('plugins/leaflet/draw/leaflet.draw.js'); ?>"></script>
<script>
    var accessToken     = `pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw`;
    var map             = L.map('mapid').setView([-2.4,125], 4);
    var coordinate      = document.getElementById('coordinate');
    var longitude       = document.getElementById('longitude');
    var latitude        = document.getElementById('latitude');

    // Initialize mapbox layer
    L.tileLayer(`https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=${accessToken}`, {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
    }).addTo(map);

    // search
    var searchControl = L.esri.Geocoding.geosearch().addTo(map);
    var results = L.layerGroup().addTo(map);

    searchControl.on('results', function (data) {
        results.clearLayers();
        for (var i = data.results.length - 1; i >= 0; i--) {
            let longlat = data.results[i].latlng;
            
            longitude.value = longlat.lng;
            latitude.value  = longlat.lat;

            results.addLayer(L.marker(data.results[i].latlng));
        }
    });

    // add layer
    var editableLayers = new L.FeatureGroup();
    map.addLayer(editableLayers);

    // set polygon options
    var polygon     = {
            allowIntersection: false, // Restricts shapes to simple polygons
            drawError: {
                color   : '#ff4c52', // Color the shape will turn when intersects
                message : '<strong>Polygon draw does not allow intersections!<strong> (allowIntersection: false)' // Message that will show when intersect
            },
            shapeOptions: {
                color   : '#3e8ef7',
                weight  : 2
            }
        };
    
    if (coordinate.value != '') {
        polygon     = false;

        var value   = JSON.parse(coordinate.value);
        L.geoJSON(value, { onEachFeature: onEachFeature }).addTo(map);
    }

    var drawPluginOptions = {
        draw: {
            polyline    : false,
            polygon     : polygon,
            circle      : false,
            rectangle   : false,
            marker      : false,
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
        map.locate({setView: true, maxZoom: 20});
    } else {
        map.setView([latitude.value, longitude.value], 20);
        // results.clearLayers();
        results.addLayer(L.marker([latitude.value, longitude.value]));
    }

    map.on('draw:created', function(e) {
        // Remove draw polygon options
        drawControl.setDrawingOptions({
            polygon:false
        });
        map.removeControl(drawControl);
        map.addControl(drawControl);

        var type    = e.layerType,
            layer   = e.layer;

        editableLayers.addLayer(layer);
        
        // set value
        coordinate.value    = JSON.stringify(layer.toGeoJSON());
    });

    map.on('draw:deleted', function(e) {
        // Add draw polygon options
        drawControl.setDrawingOptions({
            polygon:true
        });
        map.removeControl(drawControl);
        map.addControl(drawControl);

        // set value
        coordinate.value    = '';
    })

    // set layer on feature
    function onEachFeature(feature, layer) {
        editableLayers.addLayer(layer);
    }
</script>
<?= $this->endSection('javascript') ?>