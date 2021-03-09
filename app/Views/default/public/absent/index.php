<?= $this->extend(env('theme.name') . '/' . env('theme.public')) ?>
<?= $this->section('stylesheet') ?>
<link rel="stylesheet" href="<?= asset_url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
<link rel="stylesheet" href="<?= asset_url('plugins/datatables-select/css/select.bootstrap4.min.css'); ?>">
<style>
    #canvas {
        width: 100%;
    }
</style>
<?= $this->endSection('stylesheet') ?>

<?= $this->section('content') ?>
<div style="background-image: url('<?= asset_url('img/bg-transjakarta.jpg'); ?>'); background-size: cover; height: 100%; width:100%; position: fixed; overflow:auto;">
    <div class="hold-transition" style="background: transparent!important;">
        <div class="container">
            <div class="login-logo border-bottom border-white">
                <div class="row">
                    <div class="col-2">
                        <img src="<?= asset_url('img/icon.png'); ?>" style="width: 80px; padding-left: .6rem; margin-top: 15px;" />
                    </div>
                    <div class="col-10 text-left">
                        <a href="<?= site_url(); ?>" class="text-white" style="font-size: 60px;"><b><?= env('site.name') ?></b></a>
                        <p class="text-white text-sm" style="margin-top: -15px;">Sistem Informasi Analisa Dampak Lalu Lintas</p>
                    </div>
                </div>
            </div>
            <!-- /.login-logo -->
            <div class="card" style="background: transparent!important;">
                <?= form_open('/', 'class="validate" id="absent-form"') ?>
                <div class="card-body login-card-body p-1" style="background: transparent!important;">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-indigo mb-5">
                                <div class="card-header">
                                    <h3 class="card-title">Attendance Form</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /.card-tools -->
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title"><span class="page_label"></span> <span class="page_num"></span><span class="page_count"></span></h5>

                                                    <div class="card-tools">
                                                        <button class="btn btn-tool prev" title="Previous"><i class="fas fa-chevron-left"></i></a>
                                                            <button class="btn btn-tool next" title="Next"><i class="fas fa-chevron-right"></i></a>
                                                    </div>
                                                </div>

                                                <div class="card-body p-0" style="display: block;">
                                                    <canvas id="canvas"></canvas>
                                                </div>

                                                <div class="overlay d-none"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

                                                <div class="card-footer">
                                                    <div class="float-right">
                                                        <button class="btn btn-tool prev" title="Previous"><i class="fas fa-chevron-left"></i></a>
                                                            <button class="btn btn-tool next" title="Next"><i class="fas fa-chevron-right"></i></a>
                                                    </div>
                                                    <span class="page_label"></span> <span class="page_num"></span><span class="page_count"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>List Peserta</label>
                                            <table class="table table-bordered" id="table-attendee">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Institusi</th>
                                                        <th>Nama Pejabat</th>
                                                        <th>No. Telp</th>
                                                        <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" id="btn-absen" class="btn btn-success float-right">ABSEN</button>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content') ?>

<?= $this->section('javascript') ?>
<script src="<?= asset_url('plugins/pdfjs/pdf.js'); ?>"></script>
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
    let idAbsent = null;



    var url = 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/web/compressed.tracemonkey-pldi-09.pdf';

    // Loaded via <script> tag, create shortcut to access PDF.js exports.
    var pdfjsLib = window['pdfjs-dist/build/pdf'];

    // The workerSrc property shall be specified.
    pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= asset_url('plugins/pdfjs/pdf.worker.js'); ?>';

    var pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.2,
        canvas = document.getElementById('canvas'),
        ctx = canvas.getContext('2d');

    /**
     * Get page info from document, resize canvas accordingly, and render page.
     * @param num Page number.
     */
    function renderPage(num) {
        pageRendering = true;
        // Using promise to fetch the page
        pdfDoc.getPage(num).then(function(page) {
            var viewport = page.getViewport({
                scale: scale
            });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Render PDF page into canvas context
            var renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            var renderTask = page.render(renderContext);

            // Wait for rendering to finish
            renderTask.promise.then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    // New page rendering is pending
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });

        // Update page counters
        var page_num = document.getElementsByClassName('page_num');
        for (i = 0; i < page_num.length; i++) {
            page_num[i].textContent = num;
        }
    }

    /**
     * If another page rendering in progress, waits until the rendering is
     * finised. Otherwise, executes rendering immediately.
     */
    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    /**
     * Displays previous page.
     */
    function onPrevPage() {
        if (pageNum <= 1) {
            return;
        }
        pageNum--;
        queueRenderPage(pageNum);
    }
    var prev = document.getElementsByClassName('prev');
    for (i = 0; i < prev.length; i++) {
        prev[i].addEventListener('click', onPrevPage);
    }

    /**
     * Displays next page.
     */
    function onNextPage() {
        if (pageNum >= pdfDoc.numPages) {
            return;
        }
        pageNum++;
        queueRenderPage(pageNum);
    }
    var next = document.getElementsByClassName('next');
    for (i = 0; i < next.length; i++) {
        next[i].addEventListener('click', onNextPage);
    }

    /**
     * show loading
     */
    function showLoading(param = false) {
        // paging
        var label = param == false ? 'Page : ' : '';
        var page_label = document.getElementsByClassName('page_label');
        for (i = 0; i < page_label.length; i++) {
            page_label[i].textContent = label;
        }

        // next
        var next = document.getElementsByClassName('next');
        for (i = 0; i < next.length; i++) {
            next[i].classList.toggle('d-none');
        }

        // prev
        var prev = document.getElementsByClassName('prev');
        for (i = 0; i < prev.length; i++) {
            prev[i].classList.toggle('d-none');
        }

        // overlay loading
        var overlay = document.getElementsByClassName('overlay');
        for (i = 0; i < overlay.length; i++) {
            overlay[i].classList.toggle('d-none');
        }
    }
    /**
     * Asynchronously downloads PDF.
     */
    showLoading(true);
    pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        var page_count = document.getElementsByClassName('page_count');
        for (i = 0; i < page_count.length; i++) {
            page_count[i].textContent = `/${pdfDoc.numPages}`;
        }

        showLoading(false);
        // Initial/first page rendering
        renderPage(pageNum);
    });


    $(document).ready(function() {
        let tableAttendee = $('#table-attendee').DataTable({
            stateSave: true,
            ordering: false,
            processing: true,
            serverSide: true,
            select: 'single',
            ajax: {
                url: "<?= site_url('home/ajax_absent_list') ?>",
                type: 'POST',
                data: function(d) {
                    d[token] = hash;
                    d['id_andalalin_discussion'] = <?= $attendee['id_andalalin_discussion'] ?>;
                },
            },
            drawCallback: function(settings) {
                hash = settings.json[token];
            }
        });

        tableAttendee.on('select', function(e, dt, type, indexes) {
            var rowData = dt.rows(indexes).data().toArray();

            idAbsent = rowData[0][0];
        });

        tableAttendee.on('deselect', function(e, dt, type, indexes) {
            var rowData = dt.rows(indexes).data().toArray();
            idAbsent = null;
        });

        $('#btn-absen').click(function() {
            if (idAbsent == null) {
                toastr.error('Silahkan pilih dulu absen anda.')
            } else {
                window.location = '<?= site_url('home/absent/') ?>' + idAbsent;
            }
        })
    });
</script>
<?= $this->endSection('javascript') ?>