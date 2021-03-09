<footer class="main-footer bg-warning border-bottom-0">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> <?= env('site.version') . (env('CI_ENVIRONMENT') == 'production' ? '' : '-dev'); ?>
    </div>
    <strong>Copyright © <?= env('site.start_year'); ?> - <?= date('Y'); ?> <a href="<?= site_url(); ?>"><?= env('site.name'); ?></a>.</strong> All rights reserved.
</footer>