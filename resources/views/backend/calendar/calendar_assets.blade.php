

<style>
    .loader {

        position: fixed;
        /* Stay in place */
        z-index: 2199;
        /* Sit on top */
        left: 40%;
        top: 40%;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        /*        background-color: rgb(0, 0, 0);*/
        /* Fallback color */
        /*        background-color: rgba(0, 0, 0, 0.4);*/
        /* Black w/ opacity */


        border: 16px solid #808080;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite;
        /* Safari */
        animation: spin 2s linear infinite;

    }

    /* Safari */
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

</style>


<style>
    ol,
    ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .colors-list li {
        position: relative;
        display: inline-block;
        margin: 0 7px 0 0;
        width: 32px;
        height: 32px;
        border-radius: 50%/50%;
        cursor: pointer;
        background: no-repeat center center;
    }

    .colors-list li:last-child {
        margin-right: 0;
    }

    .colors-list li.current {
        cursor: default;
    }

    .colors-list li.current:before {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        line-height: 32px;
        font-size: 25px;
        color: #fff;
        content: "\f00c";
        text-align: center;
    }

    .colors-list li.current[data-color="#ffffff"]:before {
        color: #666A73;
    }

    .colors-list.square li {
        border-radius: 5px;
    }

    .colors-list.bordered li {
        box-shadow: inset rgba(0, 0, 0, 0.7) 0 0 1px;
    }

    .publicpart1 {
        width: 60%;
        float: left;
        display: inline-block;
    }

    .publicpart2 {
        width: 40%;
        float: right;
        display: inline-block;
    }

    #public_url_message {
        padding: 10px;
    }

    #btn_myModal4 {
        display: none;
    }

    #btn_myModal8 {
        display: none;
    }

    td.avldays_cell {
        width: 14.5%;
        height: 40px;
        border: 1px solid #f9c303;
        text-align: center;
        text-transform: capitalize;
        cursor: pointer;
        background-color: transparent;
    }

    td.avldays_active {
        background-color: #f9c303;
        color: #FFFFFF;
    }

</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>



<link href="{{ portal_managed_url('fullcalendar/css/fullcalendar.css') }}" rel="stylesheet" />
<link href="{{ portal_managed_url('fullcalendar/css/fullcalendar.print.css') }}" rel="stylesheet" media="print" />
<style>
.fc_day {
	background-color: mediumspringgreen;
}
/*.fc_inactive {
	background-color: grey;
	color:white;
}*/

#external-events {
	float: left;
	width: 150px;
	padding: 0 10px;
	text-align: left;
}

#external-events h4 {
	font-size: 16px;
	margin-top: 0;
	padding-top: 1em;
}

.external-event {
	margin: 10px 0;
	padding: 2px 4px;
	background: #3366CC;
	color: #fff;
	font-size: .85em;
	cursor: pointer;
}

#external-events p {
	margin: 1.5em 0;
	font-size: 11px;
	color: #666;
}

#external-events p input {
	margin: 0;
	vertical-align: middle;
}

#calendar {
	margin: 0 auto;
	width: 100%;
	background-color: #FFFFFF;
	border-radius: 6px;
	box-shadow: 0 1px 2px #C3C3C3;
}
.cal_slot_red{
	background-color: red;
	color:white;
	min-height:30px;
	padding:5px;
}
.cal_slot_red, .fc-event-title{
	color:white;
}
.cal_slot_green{
	background-color: green;
	color:white;
	min-height:30px;
	padding:5px;
}
.cal_slot_green, .fc-event-title{
	color:white;
}
.cal_slot_blue{
	background-color: blue;
	color:white;
	min-height:30px;
	padding:5px;
}
.cal_slot_blue, .fc-event-title{
	color:white;
}
.cal_slot_yellow{
	background-color: yellow;
	color:black;
	min-height:30px;
	padding:5px;
}
.cal_slot_yellow, .fc-event-title{
	color:black;
}

.fc-past {
	background-color: #CCCCCC;
}

.fc-future {
	background-color: #FFF;
}
</style>

<script src="{{ portal_managed_url('fullcalendar/js/fullcalendar.js') }}" type="text/javascript"></script>
<script src="{{ portal_managed_url('fullcalendar/js/popper.min.js') }}" type="text/javascript"></script>
<script src="{{ portal_managed_url('fullcalendar/js/tooltip.min.js') }}" type="text/javascript"></script>
<?php /*?><script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.4.2/main.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.4.2/main.min.js" type="text/javascript"></script>

<script src="https://unpkg.com/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
<script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js" type="text/javascript"></script><?php */?>

<!-- Moment js -->
<script src="{{ portal_managed_url('fullcalendar/js/moment.min.js') }}"></script>
<script src="{{ portal_managed_url('fullcalendar/js/moment-timezone.min.js') }}"></script>


