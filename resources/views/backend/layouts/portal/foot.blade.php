<a id="deletePlayerModalId" style="display:none;" href="#" data-toggle="modal" data-target="#deletePlayerModal">
    <i class="fa fa-plus-square fa-lg"></i> Delete Player Modal
</a>
<div class="modal fade" id="deletePlayerModal" tabindex="-1" role="dialog"
     aria-labelledby="deletePlayerModal" aria-hidden="true">
</div>

<a id="deleteCoachModalId" style="display:none;" href="#" data-toggle="modal" data-target="#deleteCoachModal">
    <i class="fa fa-plus-square fa-lg"></i> Delete Coach Modal
</a>
<div class="modal fade" id="deleteCoachModal" tabindex="-1" role="dialog"
     aria-labelledby="deleteCoachModal" aria-hidden="true">
</div>

<a id="deleteMemberModalId" style="display:none;" href="#" data-toggle="modal" data-target="#deleteMemberModal">
    <i class="fa fa-plus-square fa-lg"></i> Delete Member Modal
</a>
<div class="modal fade" id="deleteMemberModal" tabindex="-1" role="dialog"
     aria-labelledby="deleteMemberModal" aria-hidden="true">
</div>

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

<!-- Tags Input -->
<script src="{{ portal_managed_url('js/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

<!-- Parsley -->
<script src="{{ asset_url('js/parsley.min.js') }}"></script>

<script src="{{ asset_url('js/jquery.validate.min.js') }}"></script>

<script src="{{ portal_managed_url('js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>

<script src="{{ asset_url('select2/select2.full.min.js') }}"></script>
<script>
jQuery(document).ready(function (e) {

    call_select2();
    delete_files();
});

function call_select2()
{
    if ($('.form-select'))
    {
        $('.form-select').select2({
            width: '100%',
            placeholder: "Select Any Option",
            allowClear: true
        });
    }
}

function delete_files()
{
    if ($('.delete-confirm'))
    {
        $('.delete-confirm').click(function ()
        {
            if (confirm("Are you sure you want to delete this file?")) {


                var delete_url = $(this).data('url');
                var parent_div = $(this).data('parent');
                var file_name = $(this).data('file_name');

                ajax_csrf_token();
                $.ajax({
                    url: delete_url,
                    data: "file_name=" + file_name,
                    type: "POST",
                    success: function (response) {

                        status = response.status;

                        if (status == 'true' || status == true) {
                            $('#' + parent_div).html(response.messages);
                            setTimeout(function () {
                                $('#' + parent_div).remove();
                            }, 500);
                        }
                        else {

                            alert(response.messages);
                        }
                    }
                });
            }
            return false;
        });
    }
    if ($('.delete-image'))
    {
        $('.delete-image').click(function ()
        {
            if (confirm("Are you sure you want to delete this file?")) {


                var delete_url = $(this).data('url');
                var parent_div = $(this).data('parent');
                var file_name = $(this).data('file_name');

                ajax_csrf_token();
                $.ajax({
                    url: delete_url,
                    data: "file_name=" + file_name,
                    type: "POST",
                    success: function (response) {

                        status = response.status;

                        if (status == 'true' || status == true) {
                            $('#' + parent_div).html(response.messages);
                            setTimeout(function () {
                                $('#' + parent_div).remove();
                            }, 500);
                        }
                        else {

                            alert(response.messages);
                        }
                    }
                });
            }
            return false;
        });
    }
}
function ajax_csrf_token() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });
}
</script>


<script>
    function deletePlayerModal(id)
    {
        $('#deletePlayerModal').html($('#' + id).html());
        $('#deletePlayerModalId').click();
    }
    function deleteCoachModal(id)
    {
        $('#deleteCoachModal').html($('#' + id).html());
        $('#deleteCoachModalId').click();
    }
    function deleteMemberModal(id)
    {
        $('#deleteMemberModal').html($('#' + id).html());
        $('#deleteMemberModalId').click();
    }
</script>
@yield('js_after')

@stack('scripts')
