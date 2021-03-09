<?= $this->extend(env('theme.name').'/'.env('theme.private')) ?>

<?= $this->section('stylesheet') ?>
<style>
#canvas{
    width: 100%;
}
</style>
<?= $this->endSection('stylesheet') ?>

<?= $this->section('javascript') ?>
<script src="<?= asset_url('plugins/pdfjs/pdf.js'); ?>"></script>
<script>
var url = 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/web/compressed.tracemonkey-pldi-09.pdf';

// Loaded via <script> tag, create shortcut to access PDF.js exports.
var pdfjsLib = window['pdfjs-dist/build/pdf'];

// The workerSrc property shall be specified.
pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= asset_url('plugins/pdfjs/pdf.worker.js'); ?>';

var pdfDoc          = null,
    pageNum         = 1,
    pageRendering   = false,
    pageNumPending  = null,
    scale           = 1.2,
    canvas          = document.getElementById('canvas'),
    ctx             = canvas.getContext('2d');

/**
 * Get page info from document, resize canvas accordingly, and render page.
 * @param num Page number.
 */
function renderPage(num) {
    pageRendering = true;
    // Using promise to fetch the page
    pdfDoc.getPage(num).then(function(page) {
        var viewport    = page.getViewport({scale: scale});
        canvas.height   = viewport.height;
        canvas.width    = viewport.width;

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
    var label       = param == false ? 'Page : ' : ''; 
    var page_label  = document.getElementsByClassName('page_label');
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
</script>
<?= $this->endSection('javascript') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-7">
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
    </div><!-- col-md-7 -->
</div>
<?= $this->endSection('content') ?>