<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => env('APP_NAME'),
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<strong>' . env('APP_NAME') . '</strong>',
    'logo_img' => '',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => '',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => false,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => '',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'admin/dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:

        [
            'text' => '',
            'icon' => 'fas fa-moon',
            'topnav_right' => true,
            'url' => '#',
            'data' => [
                'dark-mode-toggle' => 'true',
            ],
        ],
        [
            'type' => 'navbar-search',
            'text' => 'Cari',
            'topnav_right' => false,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => false,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'Cari',
        ],

        [
            'text' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'fas fa-fw fa-home',
        ],
        [
            'text' => 'Semua Pemesanan',
            'route' => 'appointments.index',
            'icon' => 'fas fa-calendar-check',
            'can'  => 'appointments.view',
        ],
        [
            'text' => 'Transaksi',
            'icon' => 'fas fa-receipt',
            'submenu' => [
                [
                    'text' => 'Lihat Semua',
                    'icon' => 'fas fa-fw fa-eye',
                    'route' => 'transactions.index',
                ],
            ],
        ],

        [
            'text' => 'Hasil Foto',
            'icon' => 'fas fa-camera-retro',
            'submenu' => [
                [
                    'text' => 'Kelola Hasil',
                    'icon' => 'fas fa-images',
                    'route' => 'photo-results.index',
                ],
            ],
        ],

        [
            'text' => 'Kategori',
            'icon' => 'fas fa-fw fa-folder',
            'url'  => 'category*',
            'can'  => 'categories.view | categories.create | categories.edit | categories.delete',
            'submenu' => [
                [
                    'text' => 'Tambah Baru',
                    'icon' => 'fas fa-fw fa-plus',
                    'route' => 'category.create',
                    'can'   => 'categories.create'
                ],
                [
                    'text' => 'Lihat Semua',
                    'icon' => 'fas fa-fw fa-eye',
                    'route' => 'category.index',
                    'can'   => 'categories.view'
                ],
            ],
        ],


        [
            'text'    => 'Pengguna',
            'url'  => 'user*',
            'icon'    => 'fas fa-fw fa-users',
            'can'  => 'users.view | users.create | users.edit | users.delete',
            'submenu' => [
                [
                    'text' => 'Tambah Pengguna',
                    'icon' => 'fas fa-fw fa-plus',
                    'route' => 'user.create',
                    'can'  => 'users.create',
                ],
                [
                    'text' => 'Semua Pengguna',
                    'icon' => 'fas fa-users',
                    'route' => 'user.index',
                ],
                [
                    'text' => 'Tempat Sampah',
                    'icon' => 'fas fa-fw fa-trash',
                    'route' => 'user.trash',
                    'can'  => 'users.delete',
                ],
            ],
        ],

        [
            'text'    => 'Grup Slot',
            'url'     => 'slot-group*',
            'icon'    => 'fas fa-fw fa-clock',
            'can'     => 'slot-groups.view | slot-groups.create | slot-groups.edit | slot-groups.delete',
            'active'  => ['slot-group.*'],
            'submenu' => [
                [
                    'text'  => 'Tambah Baru',
                    'icon'  => 'fas fa-fw fa-plus',
                    'route' => 'slot-group.create',
                    'can'   => 'slot-groups.create',
                ],
                [
                    'text'  => 'Lihat Semua',
                    'icon'  => 'fas fa-fw fa-eye',
                    'route' => 'slot-group.index',
                    'can'   => 'slot-groups.view',
                ],
            ],
        ],

        [
            'text'  => 'Layanan',
            'icon'  => 'fas fa-fw fa-briefcase',
            'can'   => 'services.view | services.create | services.edit | services.delete',
            'active' => ['service.*'],
            'submenu' => [
                [
                    'text'  => 'Tambah Layanan',
                    'icon'  => 'fas fa-fw fa-plus',
                    'route' => 'service.create',
                    'can'   => 'services.create',
                ],
                [
                    'text'  => 'Lihat Semua',
                    'icon'  => 'fas fa-fw fa-eye',
                    'route' => 'service.index',
                    'can'   => 'services.view',
                ],
                [
                    'text'  => 'Tempat Sampah',
                    'icon'  => 'fas fa-fw fa-trash',
                    'route' => 'service.trash',
                    'can'   => 'services.view',
                ],
            ],
        ],

        [
            'text' => 'Layanan Tambahan',
            'icon' => 'fas fa-puzzle-piece',
            'can'  => 'addons.view | addons.create | addons.edit | addons.delete',
            'active' => ['addons.*'],
            'submenu' => [
                [
                    'text' => 'Tambah Layanan Tambahan',
                    'icon' => 'fas fa-plus',
                    'route' => 'addons.create',
                    'can'  => 'addons.create',
                ],
                [
                    'text' => 'Lihat Semua',
                    'icon' => 'fas fa-eye',
                    'route' => 'addons.index',
                    'can'  => 'addons.view',
                ],
            ],
        ],

        [
            'text' => 'Kupon',
            'icon' => 'fas fa-ticket-alt',
            'can'  => 'coupons.view | coupons.create | coupons.edit | coupons.delete',
            'active' => ['coupons.*'],
            'submenu' => [
                [
                    'text' => 'Tambah Kupon',
                    'icon' => 'fas fa-plus',
                    'route' => 'coupons.create',
                    'can'  => 'coupons.create',
                ],
                [
                    'text' => 'Lihat Semua',
                    'icon' => 'fas fa-eye',
                    'route' => 'coupons.index',
                    'can'  => 'coupons.view',
                ],
            ],
        ],

        [
            'text' => 'Latar Layanan',
            'icon' => 'fas fa-palette',
            'can'  => 'service-backgrounds.view | service-backgrounds.create | service-backgrounds.edit | service-backgrounds.delete',
            'active' => ['service-backgrounds.*'],
            'submenu' => [
                [
                    'text' => 'Tambah Latar',
                    'icon' => 'fas fa-plus',
                    'route' => 'service-backgrounds.create',
                    'can'  => 'service-backgrounds.create',
                ],
                [
                    'text' => 'Lihat Semua',
                    'icon' => 'fas fa-eye',
                    'route' => 'service-backgrounds.index',
                    'can'  => 'service-backgrounds.view',
                ],
            ],
        ],

        [
            'text' => 'Galeri',
            'icon' => 'fas fa-images',
            'can'  => 'gallery.view | gallery.create | gallery.edit | gallery.delete',
            'active' => ['gallery.*'],
            'submenu' => [
                [
                    'text' => 'Tambah Galeri',
                    'icon' => 'fas fa-plus',
                    'route' => 'gallery.create',
                    'can'  => 'gallery.create',
                ],
                [
                    'text' => 'Lihat Semua',
                    'icon' => 'fas fa-eye',
                    'route' => 'gallery.index',
                    'can'  => 'gallery.view',
                ],
            ],
        ],

        [
            'text'   => 'Studio',
            'icon'   => 'fas fa-camera',
            'can'    => 'studio.view | studio.create | studio.edit | studio.delete',
            'active' => ['studio.*'],
            'submenu' => [
                [
                    'text'  => 'Tambah Studio',
                    'icon'  => 'fas fa-plus',
                    'route' => 'studio.create',
                    'can'   => 'studio.create',
                ],
                [
                    'text'  => 'Lihat Semua',
                    'icon'  => 'fas fa-eye',
                    'route' => 'studio.index',
                    'can'   => 'studio.view',
                ],
            ],
        ],

        [
            'text' => 'Profil',
            'route' => 'profile',
            'icon' => 'fas fa-fw fa-user',
        ],
        [
            'text' => 'Pengaturan',
            'route'  => 'setting',
            'icon' => 'fas fa-fw fa-cog',
            'can'  => 'setting update',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
