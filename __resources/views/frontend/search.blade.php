@extends('frontend.layouts.guest')

@section('content')
<?php
$SITE_URL = env('APP_URL');
$MANAGE_URL = env('APP_URL').'/manage';
$AUTH_USER = Auth::user();
$user_id = 0;
if (Auth::user()) {
    $user_id = $AUTH_USER->id;
}
$logged_in_user_id = $user_id;
$start = date('M d, Y', $start);
$end = date('M d, Y', $end);
$records_exists = 0;
$search_count = count($availabilities);
if($search_count > 0){
    $records_exists = 1;
}
?>
<div class="ritekhela-subheader">

    <div class="container">

        <div class="row">

            <div class="col-md-12">
                <h1>Search</h1>
                <ul class="ritekhela-breadcrumb">
                    <li><a href="<?php echo $SITE_URL; ?>">Home</a></li>
                    <li>Search</li>
                </ul>
            </div>

        </div>

    </div>

</div>

<div class="ritekhela-main-content">

    <div class="ritekhela-main-section ritekhela-fixture-list-full">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <div class="ritekhela-fixture ritekhela-modren-fixture">

                        <div class="ritekhela-main-section ritekhela-fixture-list-full">

                            <div class="container">

                                <div class="row">

                                    <div class="col-md-12">
            <div class="ritekhela-fixture ritekhela-modren-fixture">

                <div class="ritekhela-main-section ritekhela-fixture-list-full">
                    <div class="container">

                        <div class="row examplerow">

                            <div class="col-md-12">
                                <h3>Showing Results between <?php echo $start; ?> and <?php echo $end; ?></h3>
                            </div>

                            <?php
                            if ($logged_in_user_id > 0) {
                                ?>


                                <button type="button" id="btn_myModal4"
                                        class="btn btn-primary hide" data-toggle="modal"
                                        data-target="#myModal4">
                                    Make a Booking
                                </button>


                                <div class="modal inmodal" id="myModal4" tabindex="-1"
                                     role="dialog" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content animated fadeIn">
                                            <div class="modal-header">
                                                <button type="button" class="close"
                                                        data-dismiss="modal">
                                                    <span aria-hidden="true">&times;</span>
                                                    <span class="sr-only">Cancel</span>
                                                </button>
                                                <h4 class="modal-title"><i
                                                        class="fa fa-clock-o"></i> Make a
                                                    Booking</h4>
                                                <p id="log_message"></p>
                                            </div>
                                            <div class="modal-body" id="modal-body">

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-white"
                                                        data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="modal_date" value="" />

                                <input type="hidden" id="cal_user_id"
                                       value="<?php echo $logged_in_user_id; ?>" />
                                <input type="hidden" id="selected_count" value="0" />
                                
                                <div class="col-sm-5 mt-50 hide" id="slide_div">
                                    <div class="hide" id="slots_selected_div">
                                        <h4 class="modal-title"><i class="fa fa-clock-o"></i> My
                                            Booking (<p style="display: inline;" id="checks">
                                            </p>)</h4>
                                        <div class="cal_slot_selected noclick hide"
                                             id="selected_label">Selected</div>
                                        <p id="log_message"></p>
                                        <div id="selection-body"></div>
                                        <div id="selected_slots" class="hide"></div>
                                    </div>
                                    <div class="hide" id="booking_details_div">
                                        <h4 class="modal-title"><i class="fa fa-clock-o"></i>
                                            Booking Details</h4>
                                        <div id="booking-body"></div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            $cols = 'col-sm-12';
                            if ($logged_in_user_id == 0) {
                                $cols = 'col-sm-12';
                            }
                            ?>
                            <div class="<?php echo $cols; ?>" id="calendardata">

                                <?php
                                if ($search_count <= 0) {
                                    ?>
                                    <div class="row">
                                        <h3 class="text-center">No data to display</h3>
                                        &emsp;
                                        <a class="btn btn-success sub-btn" href="#"
                                           data-toggle="modal"
                                           data-target="#ritekhelamodalsearch">Search again</a>
                                    </div>
                                    <?php
                                }
                                else {
                                    ?>

                                    <table id="example2" class="display nowrap col-lg-12">

                                        <thead>

                                            <tr>

                                                <th class="table_heading">Date</th>


                                                <th class="table_heading">Time</th>


                                                <th class="table_heading">Type</th>


                                                <th class="table_heading">Coach</th>


                                                <th class="table_heading">Rating</th>


                                                <th class="table_heading">Price</th>


                                                <th class="table_heading">Actions</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php
                                            $search_count = 0;
                                            $current_time = time();
                                            $user_public_url = "";
                                            foreach ($availabilities as $availability) {
                                                $search_count++;
                                                $proceed = 1;

                                                $time_start = stripslashes($availability->time_start);
                                                $time_end = stripslashes($availability->time_end);

                                                $session_id = $availability->id;
                                                $coach_id = $availability->user_id;

                                                $session_color = stripslashes($availability->color);
                                                $session_price = stripslashes($availability->price);
                                                $session_type = $aval_type = get_session_type($availability->type);
                                                $session_user_id = stripslashes($availability->user_id);

                                                $status = stripslashes($availability->booked);

                                                if ($proceed) {
                                                    $user_public_url = '#';
                                                    $session_color = 'cust_cli ' . $session_id . ' ' . $session_color;
                                                    ?>



                                                    <tr>


                                                        <td class="<?php echo $session_color; ?>">
                                                            <?php echo date('M d, Y', $time_start); ?></td>


                                                        <td class="<?php echo $session_color; ?>">
                                                            <?php echo date('h:i A', $time_start); ?></td>

                                                        <td class="<?php echo $session_color; ?>">
                                                            <?php echo $session_type; ?>
                                                            <?php
                                                            if ($session_type != $aval_type) {
                                                                echo " - $aval_type";
                                                            }
                                                            ?>
                                                        </td>


                                                        <td class="<?php echo $session_color; ?>"><a
                                                                href="<?php echo $SITE_URL; ?>/profile/<?php echo $coach_id; ?>"
                                                                target="_blank"
                                                                style="color:blue"><?php echo get_user_name($coach_id); ?></a>
                                                        </td>

                                                        <td class="<?php echo $session_color; ?> "
                                                            style="text-align: center">
                                                            <?php echo get_user_rating($coach_id); ?></td>

                                                        <td class="<?php echo $session_color; ?>">
                                                            <?php echo $session_price; ?></td>

                                                        <td class="<?php echo $session_color; ?>">
                                                            <a class=""
                                                               href="#<?php echo $user_public_url; ?>"
                                                               style="color:blue">
                                                                Book Session
                                                            </a>
                                                        </td>

                                                    </tr>

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                        ?>
                    </div>
                </div>

            </div>
        </div>


                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@if($records_exists == 1)

@section('headerInclude')
    @include('datatables.css')
@endsection

@section('footerInclude')
    @include('datatables.js')
@endsection

@endif

@push('scripts')

<script>
    jQuery(document).ready(function(e) {
        call_slots_();

        <?php
        if( $search_count > 10) 
        {
            ?>
            if (jQuery('#example2')) {
                jQuery('#example2').DataTable({

                    "drawCallback": function( settings ) {
                        call_slots_();
                    },

                    "aaSorting": [],

                    autoWidth: false,

                    dom: 'Bfrtip',

                    "lengthMenu": [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],

                    buttons: [
                        <?php /*?> 'copy', 'csv', 'excel', 'print', 'pdf'
                        <?php */?>
                    ]
                });
            }
            <?php
        }
        ?>
    });

</script>


<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    function booking(public_url) {
        <?php
        if($logged_in_user_id == 0)
        {
            ?>
            jQuery('#id_modalLogin').click();
        <?php
        }
        else
        {
            ?>
            window.location = public_url;
            <?php
        }
        ?>
    }
    
    function checks() {
    }

    function call_slots_() {
        if ($(".cust_cli")) {
                jQuery('.cust_cli').off();
                jQuery('.cust_cli').each(function(index, element) {
                    $(this).click(function(e) {

                        <?php
                        if($logged_in_user_id == 0)
                        {
                            ?>
                            alert('Please Login to Zaphri in order to Book Sessions');
                            <?php
                        }
                        elseif(get_user_profile_status($logged_in_user_id) == 0)
                        {
                            ?>
                            alert('Please Complete your Profile before booking Sessions');
                            window.location = "<?php echo $MANAGE_URL.'/user/'.$user_id; ?>";
                            <?php
                        }
                        else
                        {
                            ?>
                            //console.log('clicked');	
                            var class_name = $(this).attr('class');
                            //console.log(class_name);

                            var cal_user_id = jQuery('#cal_user_id').val();
                            $.post("{!! route('ajax.calendar_calls') !!}", {
                                    type: 'check_slot',
                                    user_id: cal_user_id,
                                    class_name: class_name
                                },
                                function(response) {
                                    console.log(response);
                                    if (response != 0) {
                                        call_add_rem_slots(response);
                                    }
                                });
                            <?php
                        }
                        ?>
                    });
                });
            }
    }
</script>
<?php
if($logged_in_user_id > 0)
{
    ?>
<script>

    function call_add_rem_slots(attr_value) {
        var selected_count = jQuery('#selected_count').val();
        selected_count++;
        jQuery('#selected_count').val(selected_count);

        var exists = 0;

        if (selected_count == 0) {
            jQuery('#selected_label').html('Selected');
        } else {
            jQuery('#selected_label').html(selected_count + ' Selected');
        }
        update_to_cart(attr_value, exists);
    }

    function update_to_cart(id, exists) {
        $('#booking_details_div').hide();
        $('#booking-body').html('');
        $('#slots_selected_div').removeClass('hide');
        $('#slots_selected_div').show();
        var selected_slots = $('#selected_slots').html();
        //alert(selected_slots);
        var cal_user_id = jQuery('#cal_user_id').val();
        $.post("{!! route('ajax.calendar_calls') !!}", {
            type: 'update_slot_selection',
            user_id: cal_user_id,
            exists: exists,
            id: id,
            selected_slots: selected_slots
        },
        function(response_data) {
            // console.log(response_data);

            if (response_data != '') {
                $('#selection-body').html(response_data);
                if ($('#calendardata').hasClass('col-sm-12')) {
                    $('#calendardata').removeClass('col-sm-12');
                    $('#calendardata').addClass('col-sm-7');

                    $('#slide_div').show();
                }
                // $('html, body').animate({
                //     scrollTop: $("#selection-body").offset().top
                // }, 2000);
                selected_slots = $('#selected_slots_ajax').html();

                $('#selected_slots').html(selected_slots);
                //alert(selected_slots);
                call_checks();
            } else {
                $('#selection-body').html(response_data);

                $('#calendardata').removeClass('col-sm-7');
                $('#calendardata').addClass('col-sm-12');
                $('#slide_div').hide();

                $('#selected_slots').html('');
            }
        });
    }

    function call_checks() {
        
        if($("#checks").length > 0){
            $("#checks").html($(".times_row").length);
            if($(".times_row").length === 0){
                $('#calendardata').removeClass('col-sm-9');
                $('#calendardata').addClass('col-sm-12');
                $('#slide_div').hide();
            }
        }
        
        jQuery('.btn_book').off();
        jQuery('.avl_slots').off();
        jQuery('.btn_book').click(function(e) {
            //alert('error');
            var slots = '';
            var book_types = '';
            jQuery('.avl_slots').each(function(index, element) {
                if ($(this).is(":checked")) {
                    var value = parseInt($(this).val());
                    if (slots == '') {
                        slots = value;
                    } else {
                        slots += ',' + value;
                    }

                    var value = parseInt($('#ses_type_' + value).val());
                    if (book_types == '') {
                        book_types = value;
                    } else {
                        book_types += ',' + value;
                    }
                }
            });

            //console.log('slots = '+slots);

            if (slots == '') {
                jQuery('#book_message').html('Please Select atleast One time slot<br><br>');
            } else {
                var selected_slots = $('#selected_slots').html();
                var cal_user_id = jQuery('#cal_user_id').val();
                $.post("{!! route('ajax.calendar_calls') !!}", {
                    type: 'booking_create',
                    slots_str: slots,
                    book_types: book_types,
                    user_id: cal_user_id
                },
                function(response) {
                    //console.log(response);
                    var Data = JSON.parse(response);
                    var count_msg = Data.count;
                    var messages = Data.messages;

                    var reponse_str = '';
                    for (var i = 0; i < count_msg; i++) {
                        var message = messages[i];
                        if (reponse_str == '') {
                            reponse_str = message;
                        } else {
                            reponse_str += '<br>' + message;
                        }
                    }
                    reponse_str += '<br><br>';
                    jQuery('#book_message').html(reponse_str);
                    jQuery('.row_checkout').removeClass('hide');
                    jQuery('.row_book').addClass('hide');
                    jQuery('.row_delete').addClass('hide');
                    jQuery('.select_type_id').attr('disabled', 'disabled');

                    call_checkout();

                });
            }
        });

    }

    function call_rem_slots(attr_value) {

        var selected_count = jQuery('#selected_count').val();
        selected_count--;
        jQuery('#selected_count').val(selected_count);

        var exists = 1;

        if (selected_count == 0) {
            jQuery('#selected_label').html('Selected');
        } else {
            jQuery('#selected_label').html(selected_count + ' Selected');
        }
        update_to_cart(attr_value, exists);
    }

    function call_checkout() {

        if (jQuery('.btn_checkout')) {
            jQuery('.btn_checkout').off();
            jQuery('.btn_checkout').click(function(e) {
                var selected_slots = $('#selected_slots').html();
                jQuery('#textarea_slots').val(selected_slots);
                jQuery('#proceed_payment').submit();
            });
        }
    }

    function call_booked_slots() {

        jQuery('.btn-cancelbook').off();
        jQuery('.btn-cancelbook').each(function(index, element) {
            var td_obj = $(this);
            td_obj.click(function(e) {
                if (!td_obj.hasClass('noclick')) {
                    var attr_value = td_obj.attr('id');
                    show_booking_details(attr_value, 1);
                }
            });
        });

        jQuery('.btn-reschedulebook').off();
        jQuery('.btn-reschedulebook').each(function(index, element) {
            var td_obj = $(this);
            td_obj.click(function(e) {
                if (!td_obj.hasClass('noclick')) {
                    var attr_value = td_obj.attr('id');
                    show_booking_details(attr_value, 2);
                }
            });
        });
    }

    function show_booking_details(id, option) {
        //console.log(id+' '+option);
        $('#slots_selected_div').hide();
        $('#selection-body').html('');
        $('#booking_details_div').removeClass('hide');
        $('#booking_details_div').show();

        var cal_user_id = jQuery('#cal_user_id').val();

        $.post("{!! route('ajax.calendar_calls') !!}", {
            type: 'show_booking_details',
            user_id: cal_user_id,
            id: id,
            option: option
        },
        function(response_data) {
            $('#booking-body').html(response_data);
            if (response_data != '') {
                if ($('#calendardata').hasClass('col-sm-12')) {
                    $('#calendardata').removeClass('col-sm-12');
                    $('#calendardata').addClass('col-sm-9');

                    $('#slide_div').show();
                }
                call_booking_actions();
            } else {
                $('#calendardata').removeClass('col-sm-9');
                $('#calendardata').addClass('col-sm-12');
                $('#slide_div').hide();
            }
        });
    }

    function call_booking_actions() {
        jQuery('.btn_cancel_slot').off();
        jQuery('.btn_cancel_slot').click(function(e) {

            jQuery('.btn_reschedule_slot').removeClass('btn-outline');
            jQuery('.btn_cancel_slot').addClass('btn-outline');

            //alert('Cancel');

            jQuery('#reschedule_slot_div').hide();
            jQuery('#cancel_slot_div').show();

        });

        jQuery('.btn_reschedule_slot').off();
        jQuery('.btn_reschedule_slot').click(function(e) {

            jQuery('.btn_cancel_slot').removeClass('btn-outline');
            jQuery('.btn_reschedule_slot').addClass('btn-outline');

            //alert('Reschedule');

            jQuery('#cancel_slot_div').hide();
            jQuery('#reschedule_slot_div').show();

        });
    }

</script>
    <?php
}
?>
@endpush
