@extends('backend.layouts.portal')

@section('content')
<?php
$AUTH_USER = Auth::user();
$user_id = $AUTH_USER->id;
$MANAGE_URL = env('APP_URL').'/manage';
$cal_user_id = $user_id;
$user_type = $AUTH_USER->user_type;
$status = $AUTH_USER->profile_status;
$Link = "";
if(!empty($UserPersonal)) {
    $link = $UserPersonal->meetinglink;
}

$approval_status = 0;
if ($user_type == 1 && !empty($UserProfessional)) {
    $approval_status = $UserProfessional->club_authentication;
}
$associated = 0;
if ($approval_status == 1) {
    $associated = 1;
}
$SITE_URL = env('APP_URL');

$data = [
    'show_breadcrumb' => 1,
    'show_title' => 1,
    'title'      => 'Set Availability'
];
?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')

@include('backend.layouts.portal.content_middle')

<div class="row mt-10">
    <div class="col-lg-12 row mt-10">
        <div class="col-lg-12 mt-10">
            <h3 class="font-bold">Note: Please click on calendar bellow to create session</h3>
            <hr>

            <div class="row">
                <div class="col-lg-12">
                    <div class="content-group">
                        <div class="row">
                            <div class="col-sm-offset-1 col-sm-10">


                                <div class="form-group row">

                                    <div class="col-sm-12">
                                        <div id="calendar"></div>
                                    </div>



                                    <button type="button" id="btn_myModal4" class="btn btn-primary" data-toggle="modal" data-target="#myModal4">
                                        Add Availability
                                    </button>

                                    <button type="button" id="btn_myModal8" class="btn btn-primary" data-toggle="modal" data-target="#myModal8">
                                        Change Availability
                                    </button>

                                    @include('backend.calendar.calendar_settings')

                                    <div class="circular_loader1"></div>

                                    <div class="modal inmodal" id="myModal4" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content animated fadeIn">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span aria-hidden="true">&times;</span>
                                                        <span class="sr-only">Cancel</span>
                                                    </button>
                                                    <h4 class="modal-title"><i class="fa fa-clock-o"></i> <span id="session_date">Availability</span></h4>
                                                </div>
                                                <div class="modal-body" id="modal-body">

                                                    <div class="circular_loader"></div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary btn_save">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="modal_date" value="" />

                                    <div class="modal inmodal" id="myModal8" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content animated fadeIn">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span aria-hidden="true">&times;</span>
                                                        <span class="sr-only">Cancel</span>
                                                    </button>
                                                    <h4 class="modal-title"><i class="fa fa-clock-o"></i> <span id="modal_session_date">Availability</span></h4>
                                                </div>
                                                <div class="modal-body" id="update_modal-body">
                                                    <div class="circular_loader2"></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger btn_delete">Delete Session</button>
                                                    <button type="button" class="btn btn-primary btn_change">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="modal_slot_id" value="" />


                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row mt-60">
                        <div class="col-sm-12">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('backend.layouts.portal.content_lower')

@include('backend.layouts.portal.content_bottom')

@endsection

@push('scripts')
@include('backend.calendar.calendar_assets')
<script type="text/javascript">
/*$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});*/
</script>
<script>
    $(document).ready(function () {

        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        $('#curr_day').val(d);
        $('#curr_month').val(m + 1);
        $('#curr_year').val(y);

        var calendar;

        call_data(date, 1);

    });

    function call_new_time() {

        $('.select_week_months').hide();
        $('.multiple_days').hide();



        if ($("#display_date")) {
            $("#session_date").html($("#display_date").val());
        }
        if ($("#modal_display_date")) {
            $("#modal_session_date").html($("#display_date").val());
        }

        if ($(".colors_list_li")) {
            jQuery('.colors_list_li').off();
            jQuery('.colors_list_li').each(function (index, element) {
                $(this).click(function (e) {
                    jQuery('.colors_list_li').removeClass('current');
                    jQuery('.colors_list_li').removeClass('fa');
                    $(this).addClass('fa');
                    $(this).addClass('current');

                    var attr = $(this).attr('data-color');
                    jQuery('#selected_color').val(attr);

                    var dclass = $(this).attr('data-class');
                    jQuery('#slot_color').val(dclass);
                });
            });
        }

        if ($(".unavailable")) {
            $(".unavailable").off();
            $(".unavailable").click(function () {
                $(".unavailable").removeClass('hide');
                $(".available").removeClass('hide');
                $(".unavailable").hide();
                $(".available").show();

                if ($('.times_row_Avl').hasClass('hide')) {
                    $('.times_row_Avl').removeClass('hide');

                    $('.times_row_nAvl').removeClass('hide');
                    $('.times_row_nAvl').addClass('hide');

                    $("#availability").val(1);

                }
                else {
                    $('.times_row_Avl').removeClass('hide');
                    $('.times_row_Avl').addClass('hide');

                    $('.times_row_nAvl').removeClass('hide');

                    $("#availability").val(0);
                }
            });
        }

        if ($(".available")) {
            $(".available").off();
            $(".available").click(function () {
                $(".unavailable").removeClass('hide');
                $(".available").removeClass('hide');
                $(".available").hide();
                $(".unavailable").show();

                if ($('.times_row_Avl').hasClass('hide')) {
                    $('.times_row_Avl').removeClass('hide');

                    $('.times_row_nAvl').removeClass('hide');
                    $('.times_row_nAvl').addClass('hide');

                    $("#availability").val(1);

                }
                else {
                    $('.times_row_Avl').removeClass('hide');
                    $('.times_row_Avl').addClass('hide');

                    $('.times_row_nAvl').removeClass('hide');

                    $("#availability").val(0);
                }
            });
        }

        if ($(".apply_to_single_day")) {
            $(".apply_to_single_day").off();
            $(".apply_to_single_day").click(function () {

                $("#days_selection").val(1);

                $(".btn-out").removeClass('btn-outline');
                $(".btn-out").addClass('btn-outline');
                $(this).removeClass('btn-outline');

                $('.select_week_months').hide();

                $('.multiple_days').hide();
            });
        }

        if ($(".apply_to_recurring_day")) {
            $(".apply_to_recurring_day").off();
            $(".apply_to_recurring_day").click(function () {

                $("#days_selection").val(2);

                $(".btn-out").removeClass('btn-outline');
                $(".btn-out").addClass('btn-outline');
                $(this).removeClass('btn-outline');

                $("#w_m_selection").val('1W');
                $(".btn-wm").removeClass('btn-outline');
                $(".btn-wm").addClass('btn-outline');
                $(".apply_to_one_week").removeClass('btn-outline');
                $('.select_week_months').show();

                $('.multiple_days').hide();

            });
        }

        if ($(".apply_to_multiple_dates")) {
            $(".apply_to_multiple_dates").off();
            $(".apply_to_multiple_dates").click(function () {

                $("#days_selection").val(3);

                $(".btn-out").removeClass('btn-outline');
                $(".btn-out").addClass('btn-outline');
                $(this).removeClass('btn-outline');

                $("#w_m_selection").val('1W');
                $(".btn-wm").removeClass('btn-outline');
                $(".btn-wm").addClass('btn-outline');
                $(".apply_to_one_week").removeClass('btn-outline');
                $('.select_week_months').show();

                $('.multiple_days').show();
                call_multiple_days();
            });
        }

        if ($(".apply_to_one_week")) {
            $(".apply_to_one_week").off();
            $(".apply_to_one_week").click(function () {
                $("#w_m_selection").val('1W');

                $(".btn-wm").removeClass('btn-outline');
                $(".btn-wm").addClass('btn-outline');
                $(this).removeClass('btn-outline');
            });
        }

        if ($(".apply_to_two_week")) {
            $(".apply_to_two_week").off();
            $(".apply_to_two_week").click(function () {
                $("#w_m_selection").val('2W');

                $(".btn-wm").removeClass('btn-outline');
                $(".btn-wm").addClass('btn-outline');
                $(this).removeClass('btn-outline');
            });
        }

        if ($(".apply_to_one_month")) {
            $(".apply_to_one_month").off();
            $(".apply_to_one_month").click(function () {
                $("#w_m_selection").val('1M');

                $(".btn-wm").removeClass('btn-outline');
                $(".btn-wm").addClass('btn-outline');
                $(this).removeClass('btn-outline');
            });
        }

        if ($(".apply_to_two_month")) {
            $(".apply_to_two_month").off();
            $(".apply_to_two_month").click(function () {
                $("#w_m_selection").val('2M');

                $(".btn-wm").removeClass('btn-outline');
                $(".btn-wm").addClass('btn-outline');
                $(this).removeClass('btn-outline');
            });
        }

        if ($(".btn_save")) {
            $(".btn_save").off();
            $(".btn_save").click(function () {
                $('#set_avl_msg_error').html('');
                $('#set_avl_msg_error').hide();
                $('#set_avl_msg_success').html('');
                $('#set_avl_msg_success').hide();

                var cal_user_id = jQuery('#cal_user_id').val();

                var date_selected = jQuery('#modal_date').val();

                var availability = $("#availability").val();

                var from_string = '';
                var mins_string = '';

                if (availability == 1) {
                    $(".available_from").each(function (index, element) {
                        var index_value = parseInt($(this).val());
                        from_string = index_value;
                    });
                    $(".available_mins").each(function (index, element) {
                        var index_value = parseInt($(this).val());
                        mins_string = index_value;
                    });
                }

                var ses_type = jQuery('#ses_type').val();
                var ses_name = '';
                var ses_description = jQuery('#ses_description').val();
                var ses_price = jQuery('#ses_price').val();
                var slot_color = jQuery('#slot_color').val();


                var days_selection = $("#days_selection").val();
                var w_m_selection = $("#w_m_selection").val();


                var sunday_rec = 0;
                var monday_rec = 0;
                var tuesday_rec = 0;
                var wednesday_rec = 0;
                var thursday_rec = 0;
                var friday_rec = 0;
                var saturday_rec = 0;

                if (days_selection == 3) {
                    sunday_rec = $("#sunday_rec").val();
                    monday_rec = $("#monday_rec").val();
                    tuesday_rec = $("#tuesday_rec").val();
                    wednesday_rec = $("#wednesday_rec").val();
                    thursday_rec = $("#thursday_rec").val();
                    friday_rec = $("#friday_rec").val();
                    saturday_rec = $("#saturday_rec").val();
                }

                $(".circular_loader").addClass("loader");
                $('#set_avl_msg_success').html('<div class="circular_loader loader"></div>');
                $('#set_avl_msg_success').show();

                $.post("{!! route('ajax.calendar_calls') !!}", {
                    type: 'set_availability',

                    user_id: cal_user_id,

                    date_selected: date_selected,
                    available_from: from_string,
                    available_mins: mins_string,

                    ses_type: ses_type,
                    ses_name: ses_name,
                    ses_description: ses_description,
                    ses_price: ses_price,
                    slot_color: slot_color,

                    availability: availability,
                    days_selection: days_selection,
                    w_m_selection: w_m_selection,

                    sunday_rec: sunday_rec,
                    monday_rec: monday_rec,
                    tuesday_rec: tuesday_rec,
                    wednesday_rec: wednesday_rec,
                    thursday_rec: thursday_rec,
                    friday_rec: friday_rec,
                    saturday_rec: saturday_rec
                },
                function (response) {
                    //console.log(response);
                    data = $.parseJSON(response);
                    status = data.response_Status;

                    error_messages = data.error_messages;
                    count_error = data.count_error;

                    success_messages = data.success_messages;
                    count_success = data.count_success;

                    //$('#set_avl_msg_success').html('');
                    //$('#set_avl_msg_success').hide();

                    if (status == 'success') {
                        $(".circular_loader").removeClass("loader");
                        //$('#set_avl_msg_success').html(data.response_Text);
                        //$('#set_avl_msg_success').show();
                    }
                    else if (status == 'error') {
                        $(".circular_loader").removeClass("loader");
                        $('#set_avl_msg_error').html(data.response_Text);
                        $('#set_avl_msg_error').show();
                    }

                    /*var string_html_success = '';
                    for (var i = 0; i < count_success; i++) {
                        string_html_success += '<p>' + success_messages[i].text + '</p>';
                    }
                    $('#set_avl_msg_success').html(string_html_success);
                    $('#set_avl_msg_success').show();

                    var string_html_error = '';
                    for (var i = 0; i < count_error; i++) {
                        string_html_error += '<p>' + error_messages[i].text + '</p>';
                    }
                    $('#set_avl_msg_error').html(string_html_error);
                    $('#set_avl_msg_error').show();*/

                    {
                        window.location.reload();
                    }
                });
            });
        }

        if ($(".btn_change")) {
            $(".btn_change").off();
            $(".btn_change").click(function () {

                $('#set_avl_msg_error').html('');
                $('#set_avl_msg_error').hide();
                $('#set_avl_msg_success').html('');
                $('#set_avl_msg_success').hide();

                var cal_user_id = jQuery('#cal_user_id').val();

                var modal_slot_id = jQuery('#modal_slot_id').val();

                var date_selected = jQuery('#modal_display_date').val();

                var availability = $("#availability").val();

                var from_string = '';
                var mins_string = '';

                if (availability == 1) {
                    $(".available_from").each(function (index, element) {
                        var index_value = parseInt($(this).val());
                        from_string = index_value;
                    });
                    $(".available_mins").each(function (index, element) {
                        var index_value = parseInt($(this).val());
                        mins_string = index_value;
                    });
                }

                var ses_type = jQuery('#ses_type').val();
                var ses_name = '';
                var ses_description = jQuery('#ses_description').val();
                var ses_price = jQuery('#ses_price').val();
                var slot_color = jQuery('#slot_color').val();


                var days_selection = $("#days_selection").val();
                var w_m_selection = $("#w_m_selection").val();


                var sunday_rec = 0;
                var monday_rec = 0;
                var tuesday_rec = 0;
                var wednesday_rec = 0;
                var thursday_rec = 0;
                var friday_rec = 0;
                var saturday_rec = 0;

                if (days_selection == 3) {
                    sunday_rec = $("#sunday_rec").val();
                    monday_rec = $("#monday_rec").val();
                    tuesday_rec = $("#tuesday_rec").val();
                    wednesday_rec = $("#wednesday_rec").val();
                    thursday_rec = $("#thursday_rec").val();
                    friday_rec = $("#friday_rec").val();
                    saturday_rec = $("#saturday_rec").val();
                }


                $("#circular_loader2").addClass("loader");
                $('#set_avl_msg_success').html('<div class="circular_loader2 loader"></div>');
                $('#set_avl_msg_success').show();


                $.post("{!! route('ajax.calendar_calls') !!}", {
                    type: 'update_availability',

                    user_id: cal_user_id,

                    slot_id: modal_slot_id,
                    date_selected: date_selected,
                    available_from: from_string,
                    available_mins: mins_string,

                    ses_type: ses_type,
                    ses_name: ses_name,
                    ses_description: ses_description,
                    ses_price: ses_price,
                    slot_color: slot_color,

                    availability: availability,
                    days_selection: days_selection,
                    w_m_selection: w_m_selection,

                    sunday_rec: sunday_rec,
                    monday_rec: monday_rec,
                    tuesday_rec: tuesday_rec,
                    wednesday_rec: wednesday_rec,
                    thursday_rec: thursday_rec,
                    friday_rec: friday_rec,
                    saturday_rec: saturday_rec
                },
                function (response) {
                    //console.log(response);
                    data = $.parseJSON(response);
                    status = data.response_Status;

                    error_messages = data.error_messages;
                    count_error = data.count_error;

                    success_messages = data.success_messages;
                    count_success = data.count_success;

                    //$('#set_avl_msg_success').html('');
                    //$('#set_avl_msg_success').hide();

                    if (status == 'success') {
                        $("#circular_loader2").removeClass("loader");
                        //$('#set_avl_msg_success').html(data.response_Text);
                        //$('#set_avl_msg_success').show();
                    }
                    else if (status == 'error') {
                        $("#circular_loader2").removeClass("loader");
                        $('#set_avl_msg_error').html(data.response_Text);
                        $('#set_avl_msg_error').show();
                    }

                    /*var string_html_success = '';
                    for (var i = 0; i < count_success; i++) {
                        string_html_success += '<p>' + success_messages[i].text + '</p>';
                    }
                    $('#set_avl_msg_success').html(string_html_success);
                    $('#set_avl_msg_success').show();

                    var string_html_error = '';
                    for (var i = 0; i < count_error; i++) {
                        string_html_error += '<p>' + error_messages[i].text + '</p>';
                    }
                    $('#set_avl_msg_error').html(string_html_error);
                    $('#set_avl_msg_error').show();*/

                    {
                        window.location.reload();
                    }
                });
            });
        }

        if ($(".btn_delete")) {
            $(".btn_delete").off();
            $(".btn_delete").click(function () {
                $(".row_delete").removeClass('hide');
            });
        }


    }

    function call_multiple_days() {
        jQuery('.avldays_cell').off();
        jQuery('.avldays_cell').each(function (index, element) {
            var td_obj = $(this);
            td_obj.click(function (e) {
                var day = td_obj.attr('data_day_attr');
                var field = day + '_rec';
                if (td_obj.hasClass('avldays_active')) {
                    td_obj.removeClass('avldays_active');
                    jQuery('#' + field).val(0);
                }
                else {
                    td_obj.addClass('avldays_active');
                    jQuery('#' + field).val(1);
                }
            });
        });
    }

    function call_data(calldate, flag) {
        var d = calldate.getDate();
        var m = calldate.getMonth();
        var y = calldate.getFullYear();

        $.post("{!! route('ajax.calendar_calls') !!}", {
            type: 'json_month',
            user_id: <?php echo $user_id; ?>
        },
        function (response_data) {
            var Data = JSON.parse(response_data);
            if (Data.response_Code == '1') {

                var date_time = Data.date_time;

                $('#time_zone').val(date_time.time_zone);


                var total_slots = Data.total_slots;

                var time_slots = Data.time_slots;
                //console.log(time_slots);

                var new_cust_events = [];
                for (var i = 0; i < total_slots; i++) {
                    var obj_ = [];
                    obj_.title = time_slots[i].available_str;
                    obj_.start = get_simple_date(time_slots[i].start);
                    obj_.end = get_simple_date(time_slots[i].end);
                    obj_.className = time_slots[i].className;
                    obj_.description = time_slots[i].description;
                    new_cust_events.push(obj_);
                    console.log(obj_);
                }

                call_calendar(calldate, new_cust_events);
            }
            else {
                alert('error');
            }
        });
    }

    function get_simple_date(date_object) {
        /*var current_date = new Date(date_object);
        var s_y = current_date.getFullYear();
        var s_m = current_date.getMonth();
        var s_d = current_date.getDate();
        var s_h = current_date.getHours();
        var s_i = current_date.getMinutes();
        var s_s = current_date.getSeconds();
        var new_date = s_y + ' ' + s_m + ' ' + s_d + ' ' + s_h + ' ' + s_i + ' ' + s_s;*/
        var time_zone = $('#time_zone').val();
        var m = moment.tz(date_object, time_zone);
        var new_date = m.format('LLLL');
        return new_date;
    }

    function call_calendar(calldate, new_cust_events) {

        var date = calldate;
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        var cust_events = new_cust_events;
        //console.log(cust_events);

        var time_zone = $('#time_zone').val();

        var my = m + 1;
        var date_last = new Date(y, my, 0);
        var d_last = date_last.getDate();

        var calendar = $('#calendar').fullCalendar({
            header: {
                left: 'month', //agendaDay,agendaWeek,
                center: 'title',
                right: 'prev,next today'
            },
            timeZone: time_zone.toString(),
            editable: false,
            firstDay: 0, //  1(Monday) this can be changed to 0(Sunday) for the USA system
            selectable: true,
            defaultView: 'month',

            axisFormat: 'h:mm',
            columnFormat: {
                month: 'ddd', // Mon
                week: 'ddd d', // Mon 7
                day: 'dddd M/d', // Monday 9/7
                agendaDay: 'dddd d'
            },
            titleFormat: {
                month: 'MMMM yyyy', // September 2009
                week: "MMMM yyyy", // September 2009
                day: 'MMMM yyyy' // Tuesday, Sep 8, 2009
            },
            allDaySlot: false,
            selectHelper: false,
            select: function (start, end, allDay) {

                <?php
                if ($status == 0) {
                    ?>
                    alert("Please complete your profile to create session");
                    window.location = "<?php echo $MANAGE_URL.'/user/'.$user_id ?>";
                    <?php
                }
                elseif (($link == "") or (empty($link))) {
                    ?>
                    alert("Please provide your meeting link first");
                    window.location = "<?php echo $MANAGE_URL.'/user/'.$user_id ?>";
                    <?php
                }
                elseif (($associated != 1)) {
                    ?>
                    alert("You are not authorize from any club");
                    window.location = "<?php echo $MANAGE_URL.'/user/'.$user_id ?>";
                    <?php
                }
                ?>

                var current_date = new Date();
                current_day = current_date.getDate();
                current_month = current_date.getMonth();
                current_month = (current_month + 1);

                var modal_date = new Date(start);
                var modal_d = modal_date.getDate();
                var modal_m = modal_date.getMonth();
                modal_m = (modal_m + 1);
                var modal_y = modal_date.getFullYear();
                var date_str = modal_y + '-' + modal_m + '-' + modal_d;
                //var timeZoneOffset = modal_date.getTimezoneOffset();
                //var timeZoneOffsetHours = timeZoneOffset / 60;
                //console.log('Your time zone offset is ' + timeZoneOffsetHours + ' hours from UTC');
                console.log(current_day);
                console.log(modal_d);
                console.log(current_month);
                console.log(modal_m);
                // if (current_month = modal_m) {

                // }
                if (current_month == modal_m && current_day > modal_d)
                {
                    alert("Can not select old date");
                }
                else if (current_month == modal_m && current_day == modal_d)
                {
                    alert("Please select future date");
                }
                else {

                    jQuery('#modal_date').val(date_str);

                    var cal_user_id = jQuery('#cal_user_id').val();

                    $(".circular_loader1").addClass("loader");


                    jQuery('#btn_myModal4').click();

                    $.post("{!! route('ajax.calendar_calls') !!}", {
                        type: 'add_availability',
                        date_str: date_str,
                        user_id: cal_user_id
                    },
                    function (response) {
                        if (response != '') {
                            $('#modal-body').html(response);

                            $(".circular_loader1").removeClass("loader");

                            call_new_time();
                        }
                    });
                }


                calendar.fullCalendar('unselect');
            },
            events: cust_events
        });

        call_slots_();

        call_prev_data(calendar);

        call_next_data(calendar);



    }


    function call_prev_data(calendar) {
        jQuery('.fc-button-prev').click(function (e) {

            var d = $('#curr_day').val();
            var m = $('#curr_month').val();
            var y = $('#curr_year').val();

            $.post("{!! route('ajax.calendar_calls') !!}", {
                type: 'json_month',
                user_id: <?php echo $user_id; ?>
            },
            function (response_data) {
                var Data = JSON.parse(response_data);

                if (Data.response_Code == '1') {

                    var date_time = Data.date_time;

                    $('#time_zone').val(date_time.time_zone);

                    var total_slots = Data.total_slots;
                    var time_slots = Data.time_slots;

                    var cust_events = [];
                    for (var i = 0; i < total_slots; i++) {
                        var obj_ = [];
                        obj_.title = time_slots[i].available_str;
                        obj_.start = get_simple_date(time_slots[i].start);
                        obj_.end = get_simple_date(time_slots[i].end);
                        obj_.className = time_slots[i].className;
                        obj_.description = time_slots[i].description;
                        cust_events.push(obj_);
                    }

                    calendar.prev();

                    if (m === 0) {
                        m = 11;
                        y--;
                    }
                    else {
                        m--;
                    }

                    var prev_date = new Date();
                    prev_date.setDate(d);
                    prev_date.setMonth(m);
                    prev_date.setFullYear(y);

                    $('#curr_day').val(d);
                    $('#curr_month').val(m);
                    $('#curr_year').val(y);

                    call_slots_();
                }
                else {
                    alert('error');
                }
            });
        });
    }

    function call_next_data(calendar) {
        jQuery('.fc-button-next').click(function (e) {

            var d = $('#curr_day').val();
            var m = $('#curr_month').val();
            var y = $('#curr_year').val();

            $.post("{!! route('ajax.calendar_calls') !!}", {
                type: 'json_month',
                user_id: <?php echo $user_id; ?>
            },
            function (response_data) {
                var Data = JSON.parse(response_data);
                if (Data.response_Code == '1') {

                    var date_time = Data.date_time;

                    $('#time_zone').val(date_time.time_zone);


                    var total_slots = Data.total_slots;
                    var time_slots = Data.time_slots;

                    var cust_events = [];
                    for (var i = 0; i < total_slots; i++) {
                        var obj_ = [];
                        obj_.title = time_slots[i].available_str;
                        obj_.start = get_simple_date(time_slots[i].start);
                        obj_.end = get_simple_date(time_slots[i].end);
                        obj_.className = time_slots[i].className;
                        obj_.description = time_slots[i].description;
                        cust_events.push(obj_);
                    }

                    calendar.next();


                    if (m === 11) {
                        m = 1;
                        y++;
                    }
                    else {
                        m++;
                    }

                    var next_date = new Date();
                    next_date.setDate(d);
                    next_date.setMonth(m);
                    next_date.setFullYear(y);

                    $('#curr_day').val(d);
                    $('#curr_month').val(m);
                    $('#curr_year').val(y);

                    call_slots_();
                }
                else {
                    alert('error');
                }
            });
        });
    }

    function call_slots_() {
        if ($(".cust_cli")) {
            jQuery('.cust_cli').off();
            jQuery('.cust_cli').each(function (index, element) {
                $(this).click(function (e) {
                    var class_name = $(this).attr('class');

                    var cal_user_id = jQuery('#cal_user_id').val();
                    $.post("{!! route('ajax.calendar_calls') !!}", {
                        type: 'check_slot',
                        user_id: cal_user_id,
                        class_name: class_name
                    },
                    function (response) {
                        //alert(response);
                        if (response != '' && response != '0') {
                            var slot_id = response;
                            jQuery('#modal_slot_id').val(slot_id);
                            var cal_user_id = jQuery('#cal_user_id').val();
                            $(".circular_loader1").addClass("loader");
                            $.post("{!! route('ajax.calendar_calls') !!}", {
                                type: 'change_availability',
                                slot_id: slot_id,
                                user_id: cal_user_id
                            },
                            function (response) {
                                //alert(response);
                                if (response != '') {
                                    $('#modal-body').html('');
                                    $('#update_modal-body').html(response);
                                    $(".circular_loader1").removeClass("loader");
                                    call_new_time();

                                    jQuery('#btn_myModal8').click();
                                }
                            });
                        }
                    });
                });
            });
        }
    }

</script>
@endpush
