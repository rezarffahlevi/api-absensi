<?php
$uri            = service('uri');
$segments       = $uri->getSegments();
$menu_active    = "";
$submenu_active = "";

if ($uri->getTotalSegments() > 0) {
    $menu_active    = $uri->getPath();
    $submenu_active = $uri->getPath();
}

$menu   = [
    [
        'title'     => 'Home',
        'link'      => 'home',
        'icon'      => 'fa-home',
        'submenu'   => []
    ],
    [
        'title'     => 'Absensi',
        'link'      => 'absent',
        'icon'      => 'fa-university',
        'submenu'   => []
    ],
    [
        'title'     => 'Master Data',
        'link'      => 'components',
        'icon'      => 'fa-chart-pie',
        'submenu'   => [

            [
                'title'     => 'Data Siswa',
                'link'      => 'siswa',
            ],
            [
                'title'     => 'Data Kelas',
                'link'      => 'siswa',
            ],
            [
                'title'     => 'Data User',
                'link'      => 'siswa',
            ],
        ]
    ]
    // [
    //     'title'     => 'Komponen',
    //     'link'      => 'components',
    //     'icon'      => 'fa-chart-pie',
    //     'submenu'   => [
    //         [
    //             'title'     => 'PDF Viewer',
    //             'link'      => 'components/pdfjs',
    //         ],
    //         [
    //             'title'     => 'Leaflet Maps',
    //             'link'      => 'components/leaflet',
    //         ],
    //         [
    //             'title'     => 'Signature Pad',
    //             'link'      => 'components/signature',
    //         ],
    //         [
    //             'title'     => 'Take Photo',
    //             'link'      => 'components/photo',
    //         ]
    //     ]
    // ],
];
?>
<aside class="main-sidebar sidebar-dark-warning elevation-1 bg-indigo">
    <!-- Brand Logo -->
    <a href="<?= admin_url(); ?>" class="brand-link navbar-indigo">
        <img src="<?= asset_url('img/AdminLTELogo.png'); ?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light text-white"><?= env('site.name'); ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex border-bottom-0">
            <div class="image">
                <img src="<?= asset_url('img/avatar5.png'); ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= session('user')['nama']?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php if (!empty($menu)) : ?>
                <ul class="nav nav-pills nav-sidebar flex-column nav-legacy" data-widget="treeview" role="menu" data-accordion="false">
                    <?php foreach ($menu as $i => $row) : ?>
                        <?php
                        $is_menu_active = preg_match('/' . str_replace('/', '\/', $row['link']) . '/', $menu_active);
                        ?>


                        <li class="nav-item <?= $is_menu_active == true && !empty($row['submenu']) ? 'menu-open' : ''; ?>">
                            <a href="<?= !empty($row['submenu']) ? '#' : admin_url($row['link']); ?>" class="nav-link <?= $is_menu_active == true ? 'active' : ''; ?>">
                                <i class="nav-icon fas <?= $row['icon']; ?>"></i>
                                <p>
                                    <?= $row['title']; ?>

                                    <?php if (!empty($row['submenu'])) : ?>

                                        <i class="right fas fa-angle-left"></i>
                                    <?php endif; ?>
                                </p>
                            </a>

                            <?php if (!empty($row['submenu'])) : ?>
                                <ul class="nav nav-treeview">
                                    <?php foreach ($row['submenu'] as $j => $sub) : ?>
                                        <?php $is_submenu_active = preg_match('/' . str_replace('/', '\/', $sub['link']) . '/', $submenu_active); ?>
                                        <li class="nav-item">
                                            <a href="<?= admin_url($sub['link']); ?>" class="nav-link <?= $is_submenu_active == true ? 'active' : ''; ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p><?= $sub['title']; ?></p>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

        </nav>
    </div>
</aside>