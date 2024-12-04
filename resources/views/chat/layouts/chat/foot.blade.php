<script src="{{ portal_managed_url('js/jquery-3.1.1.min.js') }}"></script>

<!-- slick carousel-->
<script src="{{ portal_managed_url('js/plugins/slick/slick.min.js') }}"></script>

<script src="{{ portal_managed_url('lightbox/js/lightbox.js') }}"></script>

<script src="{{ portal_managed_url('js/html-duration-picker.min.js') }}"></script>
<script src="{{ portal_managed_url('js/bootstrap.min.js') }}"></script>
<script src="{{ portal_managed_url('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>

<script src="{{ portal_managed_url('js/inspinia.js') }}"></script>
<script src="{{ portal_managed_url('js/plugins/pace/pace.min.js') }}"></script>


<!-- Chosen -->
<script src="{{ portal_managed_url('js/plugins/chosen/chosen.jquery.js') }}"></script>

<!-- Data picker -->
<script src="{{ portal_managed_url('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>

<!-- MENU -->
<script src="{{ portal_managed_url('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>

<script src="{{ portal_managed_url('js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
<script>
function ajax_csrf_token() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });
}
</script>
<!-- CoreUI and necessary plugins-->
<script src="{{ chat_asset_url('assets/js/jquery.min.js') }}"></script>
<script src="{{ chat_asset_url('assets/js/popper.min.js') }}"></script>
<script src="{{ chat_asset_url('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ chat_asset_url('assets/js/coreui.min.js') }}"></script>
<script src="{{ chat_asset_url('assets/js/perfect-scrollbar.min.js') }}"></script>
<script src="{{ chat_asset_url('assets/js/jquery.toast.min.js') }}"></script>
<script src="{{ chat_asset_url('assets/js/auth-forms.js') }}"></script>
<script src="{{ chat_asset_url('assets/js/custom.js') }}"></script>
<script src="{{ url('sw.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.alert').delay(4000).slideUp(300)
    })
    if (!navigator.serviceWorker.controller) {
        navigator.serviceWorker.register("sw.js").then(function (reg) {
            console.log("Service worker has been registered for scope: " + reg.scope);
        });
    }
</script>
@yield('page_js')
@yield('scripts')
@yield('js_after')
@stack('scripts')
