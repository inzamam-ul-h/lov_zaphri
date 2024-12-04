<a id="deleteModalId" style="display:none;" href="#" data-toggle="modal" data-target="#deleteModal">
    <i class="fa fa-plus-square fa-lg"></i> Delete Modal
</a>
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog"
     aria-labelledby="deleteModal" aria-hidden="true">
</div>
<a id="loginModalId" style="display:none;" href="#" data-toggle="modal" data-target="#loginModal">
    <i class="fa fa-plus-square fa-lg"></i> Login Modal
</a>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog"
     aria-labelledby="loginModal" aria-hidden="true">
</div>
<?php
$datatable = datatable_helpers();
?>
<script>
    jQuery(document).ready(function (e)
    {
        if (jQuery('.btn_reset_datatable_filters'))
        {
            jQuery('.btn_reset_datatable_filters').click(function (e) {
                $('.filters_dt_cls').val('');
                $('.filters_dt_select_cls').val('');

                if (jQuery('.datatable_apply_filters')) {
                    jQuery('.datatable_apply_filters').trigger('click');
                }
            });
        }
        call_sessions_datatable();
    });
    function deleteModal(id)
    {
        $('#deleteModal').html($('#m-' + id).html());
        $('#deleteModalId').click();
    }
    function loginModal(id)
    {
        $('#loginModal').html($('#lm-' + id).html());
        $('#loginModalId').click();

        if (jQuery('.btn-lm-close'))
        {
            jQuery('.btn-lm-close').click(function (e) {
                $('#loginModal').hide();
            });
        }
    }
    
    function call_sessions_datatable()
    {
        if($('.call_coach_datatable').length > 0){
            $('.dropdown-toggle').dropdown();
            $(".call_coach_datatable").each(function (i, e) {
                var obj = $(this);
                call_coach_datatable(obj);
            }
        }
        if($('.call_player_datatable').length > 0){
            $('.dropdown-toggle').dropdown();
            $(".call_player_datatable").each(function (i, e) {
                var obj = $(this);
                call_player_datatable(obj);
            }
        }
    }
    
    function call_coach_datatable(obj)
    {
        var table_id = obj.data('table_id');
        var ajax_url = obj.data('ajax_url');
        
        $('#'+table_id).DataTable({
            "lengthChange": false,
            "paging": false,
            pageLength:  <?= $datatable['pageLength']; ?>,
            lengthMenu:  <?= $datatable['lengthMenu']; ?>,
            processing:  <?= $datatable['processing']; ?>,
            serverSide:  <?= $datatable['serverSide']; ?>,
            stateSave:   <?= $datatable['stateSave']; ?>,
            searching:   <?= $datatable['searching']; ?>,
            Filter:      <?= $datatable['Filter']; ?>,
            dom :       ' <?= $datatable['dom']; ?>',
            autoWidth:   <?= $datatable['autoWidth']; ?>,
            buttons:
            [
                @if ($datatable['buttons_excel'])
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                @endif

                @if ($datatable['buttons_pdf'])
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                @endif

                @if ($datatable['buttons_print'])
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                @endif

                @if ($datatable['buttons_colvis'])
                    'colvis'
                @endif
            ],
            columnDefs:
            [
                {
                    targets: - 1,
                    visible: true
                }
            ],
            ajax:
            {
                url: ajax_url,
            },
            columns:
            [
                {
                    data: 'sr_no',
                    name: 'sr_no',
                    render: function (data, type, row, meta)
                    {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'session_date',
                    name: 'session_date'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'player_name',
                    name: 'player_name'
                },
                {
                    data: 'payment_status',
                    name: 'payment_status'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],
        });
    }
    
    function call_player_datatable(obj)
    {
        var table_id = obj.data('table_id');
        var ajax_url = obj.data('ajax_url');
        
        $('#'+table_id).DataTable({
            "lengthChange": false,
            "paging": false,
            pageLength:  <?= $datatable['pageLength']; ?>,
            lengthMenu:  <?= $datatable['lengthMenu']; ?>,
            processing:  <?= $datatable['processing']; ?>,
            serverSide:  <?= $datatable['serverSide']; ?>,
            stateSave:   <?= $datatable['stateSave']; ?>,
            searching:   <?= $datatable['searching']; ?>,
            Filter:      <?= $datatable['Filter']; ?>,
            dom :       ' <?= $datatable['dom']; ?>',
            autoWidth:   <?= $datatable['autoWidth']; ?>,
            buttons:
            [
                @if ($datatable['buttons_excel'])
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                @endif

                @if ($datatable['buttons_pdf'])
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                @endif

                @if ($datatable['buttons_print'])
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                @endif

                @if ($datatable['buttons_colvis'])
                    'colvis'
                @endif
            ],
            columnDefs:
            [
                {
                    targets: - 1,
                    visible: true
                }
            ],
            ajax:
            {
                url: ajax_url,
            },
            columns:
            [
                {
                    data: 'sr_no',
                    name: 'sr_no',
                    render: function (data, type, row, meta)
                    {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'session_date',
                    name: 'session_date'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'coach_name',
                    name: 'coach_name'
                },
                {
                    data: 'payment_status',
                    name: 'payment_status'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],
        });
    }

</script>
<script src="{{ portal_managed_url('libs/jquery/datatables/datatables.min.js') }}"></script>
<script src="{{ portal_managed_url('libs/jquery/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ portal_managed_url('js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
