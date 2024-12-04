const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.copyDirectory('resources/assets/images', 'assets/images');
mix.copyDirectory('resources/assets/fonts', 'assets/fonts');
mix.copyDirectory('resources/assets/icons', 'assets/icons');

//mix.copy('node_modules/video.js/dist/video-js.css', 'assets/css/video-js.css');
//mix.copy('node_modules/@coreui/coreui/dist/css/coreui.min.css', 'assets/css/coreui.min.css');
mix.copy('node_modules/bootstrap/dist/css/bootstrap.min.css', 'assets/css/bootstrap.min.css');
//mix.copy('node_modules/simple-line-icons/css/simple-line-icons.css', 'assets/css/simple-line-icons.css');
//mix.copy('node_modules/jquery-toast-plugin/dist/jquery.toast.min.css', 'assets/css/jquery.toast.min.css');

mix.copy('node_modules/jquery/dist/jquery.min.js', 'assets/js/jquery.min.js');
//mix.copy('node_modules/video.js/dist/video.min.js', 'assets/js/video.min.js');
mix.copy('node_modules/popper.js/dist/umd/popper.min.js', 'assets/js/popper.min.js');
//mix.copy('node_modules/@coreui/coreui/dist/js/coreui.min.js', 'assets/js/coreui.min.js');
//mix.copy('node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js', 'assets/js/perfect-scrollbar.min.js');
mix.copy('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', 'assets/js/bootstrap.bundle.min.js');
//mix.copy('node_modules/jquery-toast-plugin/dist/jquery.toast.min.js', 'assets/js/jquery.toast.min.js');
//mix.copy('node_modules/emojione/lib/js/emojione.min.js', 'assets/js/emojione.min.js');
//mix.copy('node_modules/sweetalert2/dist/sweetalert2.all.min.js', 'assets/js/sweetalert2.all.min.js');
//mix.copy('node_modules/icheck/', 'assets/icheck/');
mix.copy('resources/assets/js/moment-with-locales.js',
    'assets/js/moment-with-locales.min.js')

mix.js('resources/assets/js/app.js', 'assets/js').
    js('resources/assets/js/chat.js', 'assets/js').
    js('resources/assets/js/notification.js', 'assets/js').
    js('resources/assets/js/set_user_status.js', 'assets/js').
    js('resources/assets/js/profile.js', 'assets/js').
    js('resources/assets/js/custom.js', 'assets/js').
    js('resources/assets/js/auth-forms.js', 'assets/js').
    js('resources/assets/js/set-user-on-off.js', 'assets/js').
    js('resources/assets/js/admin/users/user.js',
        'assets/js/admin/users').
    js('resources/assets/js/admin/meetings/meetings.js',
        'assets/js/admin/meetings').
    js('resources/assets/js/admin/meetings/meeting_index.js',
        'assets/js/admin/meetings').
    js('resources/assets/js/admin/meetings/member_meeting_index.js',
        'assets/js/admin/meetings').
    js('resources/assets/js/admin/users/edit_user.js',
        'assets/js/admin/users').
    js('resources/assets/js/admin/roles/role.js',
        'assets/js/admin/roles').
    js('resources/assets/js/admin/roles/create_edit_role.js',
        'assets/js/admin/roles').
    js('resources/assets/js/admin/reported_users/reported_users.js',
        'assets/js/admin/reported_users').
    js('resources/assets/js/admin/front_cms/front-cms.js',
        'assets/js/admin/front_cms').
    js('resources/assets/js/custom-datatables.js',
        'assets/js/custom-datatables.js');

mix.sass('resources/assets/sass/style.scss', 'assets/css').
    sass('resources/assets/sass/font-awesome.scss', 'assets/css').
    sass('resources/assets/sass/admin_panel.scss', 'assets/css').
    sass('resources/assets/landing-page-scss/scss/landing-page-style.scss', 'assets/css').
    sass('resources/assets/sass/new-conversation.scss', 'assets/css').
    sass('resources/assets/sass/custom-style.scss', 'assets/css').
    version();
/*

mix.babel('assets/js/app.js', 'assets/js/app.js').
    babel('assets/js/chat.js', 'assets/js/chat.js').
    babel('assets/js/notification.js',
        'assets/js/notification.js')
    .babel('assets/js/set_user_status.js',
        'assets/js/set_user_status.js')
   .babel('assets/js/profile.js', 'assets/js/profile.js')
   .babel('assets/js/custom.js', 'assets/js/custom.js')
   .babel('assets/js/set-user-on-off.js', 'assets/js/set-user-on-off.js')
   .babel('assets/js/auth-forms.js', 'assets/js/auth-forms.js').version();

mix.babel('assets/js/jquery.min.js', 'assets/js/jquery.min.js')
   .babel('assets/js/video.min.js', 'assets/js/video.min.js')
   .babel('assets/js/popper.min.js', 'assets/js/popper.min.js')
   .babel('assets/js/coreui.min.js', 'assets/js/coreui.min.js')
   .babel('assets/js/perfect-scrollbar.min.js', 'assets/js/perfect-scrollbar.min.js')
   .babel('assets/js/bootstrap.min.js', 'assets/js/bootstrap.min.js')
   .babel('assets/js/jquery.toast.min.js', 'assets/js/jquery.toast.min.js')
   .babel('assets/js/emojione.min.js', 'assets/js/emojione.min.js')
   .babel('assets/js/sweetalert2.all.min.js', 'assets/js/sweetalert2.all.min.js');

mix.babel('assets/css/video-js.css', 'assets/css/video-js.css')
   .babel('assets/css/coreui.min.css', 'assets/css/coreui.min.css')
   .babel('assets/css/bootstrap.min.css', 'assets/css/bootstrap.min.css')
   .babel('assets/css/simple-line-icons.css', 'assets/css/simple-line-icons.css')
   .babel('assets/css/jquery.toast.min.css', 'assets/css/jquery.toast.min.css');
*/
