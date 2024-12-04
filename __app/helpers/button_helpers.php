<?php

use Carbon\Carbon;

if (!function_exists('no_records_available')) {

    function no_records_available() {
        $str = '<p style="text-align:center; font-weight:bold; padding:25px;">' . translate_it('No Records Available') . '</p>';

        return $str;
    }

}


if (!function_exists('add_button_for_modal')) {

    function add_button_for_modal($modal_id) {
        $str = '<a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#' . $modal_id . '">
                    <i class="fa fa-plus-square fa-lg"></i>' . translate_it('Add New') . '
                </a>';

        return $str;
    }

}

if (!function_exists('dashboard_greetings')) {

    function dashboard_greetings($User) {
        $User_name = $User->name;
        $str = '';
        $time = date('H');

        /* If the time is less than 1200 hours, show good morning */
        if ($time < "12") {
            $str = "Good morning " . $User_name;
        }
        /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
        elseif ($time >= "12" && $time < "17") {
            $str = "Good afternoon " . $User_name;
        }
        /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
        elseif ($time >= "17") {
            $str = "Good evening " . $User_name;
        }

        return $str;
    }

}

if (!function_exists('dashboard_buttons')) {

    function dashboard_buttons($User) {
        $str = '';
        if ($User->profile_status == 0) {
            $str = '<a class="btn btn-primary sub-btn" href="' . url('/manage/users/' . $User->id) . '">Complete Your Profile</a>';
        }
        else {
            switch ($User->user_type) {
                case 0:
                    break;

                case 1:
                    $str = '<a class="btn btn-primary sub-btn" href="' . url('/manage/availability') . '">Create Your Session</a>';
                    break;

                case 2:
                    $str = '<a class="btn btn-primary sub-btn" href="#" data-toggle="modal" data-target="#upModal" title="Assess">Book Your Training</a>';
                    break;

                case 3:
                    break;

                case 4:
                    break;

                default:
                    break;
            }
        }

        return $str;
    }

}

if (!function_exists('filter_button')) {

    function filter_button($id = 'datatable_filters') {
        $str = '<a class="btn btn-primary" data-toggle="collapse" data-target="#' . $id . '" aria-expanded="false" aria-controls="collapseExample">
                        Filters
                </a>';

        return $str;
    }

}


if (!function_exists('apply_filter_button')) {

    function apply_filter_button() {
        $str = '<input class="btn btn-primary datatable_apply_filters" type="submit" value="Apply Filters">';

        return $str;
    }

}


if (!function_exists('cancel_filter_button')) {

    function cancel_filter_button($id = 'datatable_filters') {
        $str = '<a class="btn btn-info" data-toggle="collapse" data-target="#' . $id . '" aria-expanded="true" aria-controls="collapseExample">
                        Cancel
                </a>';

        return $str;
    }

}


if (!function_exists('reset_filter_button')) {

    function reset_filter_button($class = 'btn_reset_datatable_filters') {
        $str = '<input class="btn btn-danger ' . $class . '" type="button" value="Reset Filters">';

        return $str;
    }

}


if (!function_exists('datatable_helpers')) {

    function datatable_helpers() {
        $array = array();

        $array['pageLength'] = 100;

        $array['lengthMenu'] = "[
                                    [10, 25, 50, 100, 200, 500, -1],
                                    [10, 25, 50, 100, 200, 500, 'All'],
                                ]";

        $array['processing'] = 'true';

        $array['serverSide'] = 'true';

        $array['stateSave'] = 'true';

        $array['searching'] = 'false';

        $array['Filter'] = 'true';

        $array['dom'] = 'Blfrtip';

        $array['autoWidth'] = 'false';

        $array['buttons_excel'] = true;
        $array['buttons_pdf'] = true;
        $array['buttons_print'] = true;
        $array['buttons_colvis'] = true;

        return $array;
    }

}


if (!function_exists('add_modal_header')) {

    function add_modal_header($title = 'Add New') {
        $str = '<h6 class="modal-title m-4">' . $title . '</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close"></button>';

        return $str;
    }

}

if (!function_exists('dispaly_date_in_table')) {

    function dispaly_date_in_table($record) {
        if ($record != null && $record != '') {
            $str = $record->format('d-m-Y');
        }
        else {
            $str = '00-00-0000';
        }
        return $str;
    }

}

if (!function_exists('dispaly_carbon_date_in_table')) {

    function dispaly_carbon_date_in_table($record, $returnType = 'Full') {
        $format = 'Y-m-d H:i:s';
        if ($returnType == 'date') {
            $format = 'Y-m-d';
        }
        elseif ($returnType == 'time') {
            $format = 'h:i:s a';
        }
        $str = '';
        if ($record != null && $record != '') {
            // Create a Carbon instance from the timestamp
            $dateTime = Carbon::createFromTimestamp($record);
            // Define the target timezone for New York (America/New_York)
            $newTimezone = new DateTimeZone('America/New_York');
            // Set the target timezone for the Carbon instance
            $dateTime->setTimezone($newTimezone);
            // Format the converted date and time as a string with AM/PM indicator
            $str = $dateTime->format($format);
        }
        return $str;
    }

}


if (!function_exists('add_modal_footer')) {

    function add_modal_footer($str_1 = 'Save', $class_1 = 'primary', $str_2 = 'Cancel', $class_2 = 'secondary') {
        $str = '<input class="btn btn-' . $class_1 . '" type="submit" value="' . $str_1 . '">';
        $str .= '<button type="button" class="btn btn-' . $class_2 . '" data-bs-dismiss="modal" aria-label="Close">
                        ' . $str_2 . '
                </button>';

        return $str;
    }

}


if (!function_exists('add_modal_footer_ajax')) {

    function add_modal_footer_ajax($str_1 = 'Save', $class_1 = 'primary', $str_2 = 'Cancel', $class_2 = 'secondary') {
        $str = '<input class="btn btn-' . $class_1 . '" type="buton" value="' . $str_1 . '">';
        $str .= '<button type="button" class="btn btn-' . $class_2 . '" data-bs-dismiss="modal" aria-label="Close">
                        ' . $str_2 . '
                </button>';

        return $str;
    }

}


if (!function_exists('delete_modal_header')) {

    function delete_modal_header() {
        $str = '<h6 class="modal-title">' . trans('backLang.confirmation') . '</h6>
                <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                        <span aria-hidden="true">×</span>
                </button>';

        return $str;
    }

}


if (!function_exists('delete_modal_footer')) {

    function delete_modal_footer($route, $id) {
        $str = '<input class="btn btn-danger" type="submit" value="Yes">';
        $str .= '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">
                        No
                </button>';

        return $str;
    }

}


if (!function_exists('add_button')) {

    function add_button($route, $id = 0) {
        $str = '';
        if ($id == 0) {
            $str = '<a class="btn btn-primary" href="' . route($route) . '">
                            <i class="fa fa-plus-square fa-lg"></i> Add New
                    </a>';
        }
        else {
            $str = '<a class="btn btn-primary" href="' . route($route, $id) . '">
                            <i class="fa fa-plus-square fa-lg"></i> Add New
                    </a>';
        }

        return $str;
    }

}


if (!function_exists('save_button')) {

    function save_button() {
        $str = '<input class="btn btn-primary" type="submit" value="Save">';

        return $str;
    }

}


if (!function_exists('edit_button')) {

    function edit_button($route, $id) {
        $str = '<a href="' . route($route, $id) . '" class="btn btn-primary">
                        Edit
                </a>';

        return $str;
    }

}


if (!function_exists('update_button')) {

    function update_button() {
        $str = '<input class="btn btn-primary" type="submit" value="Save Changes">';

        return $str;
    }

}


if (!function_exists('cancel_modal_button')) {

    function cancel_modal_button() {
        $str = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">
                        Cancel
                </button>';

        return $str;
    }

}


if (!function_exists('cancel_button')) {

    function cancel_button($route, $id = 0) {
        $str = '';
        if ($id == 0) {
            $str = '<a href="' . route($route) . '" class="btn btn-warning">
                        Cancel
                    </a>';
        }
        else {
            $str = '<a href="' . route($route, $id) . '" class="btn btn-warning">
                        Cancel
                    </a>';
        }

        return $str;
    }

}


if (!function_exists('back_button')) {

    function back_button($route, $id = 0) {
        $str = '';
        if ($id == 0) {
            $str = '<a href="' . route($route) . '" class="btn btn-warning">
                        Back
                    </a>';
        }
        else {
            $str = '<a href="' . route($route, $id) . '" class="btn btn-warning">
                        Back
                    </a>';
        }

        return $str;
    }

}


if (!function_exists('return_button')) {

    function return_button($route, $id = 0) {
        $str = '';
        if ($id == 0) {
            $str = '<a class="btn btn-secondary" href="' . route($route) . '">
                            <i class="fa fa-chevron-left mr-2"></i> Return to Listing
                    </a>';
        }
        else {
            $str = '<a class="btn btn-secondary" href="' . route($route, $id) . '">
                            <i class="fa fa-chevron-left mr-2"></i> Return to Listing
                    </a>';
        }

        return $str;
    }

}


if (!function_exists('dispaly_status_in_table')) {

    function dispaly_status_in_table($status) {
        $str = '';
        if ($status == 1) {
            //$str = '<a class="btn btn-success btn-sm">Active</a>';
            $str = '<i class="fa fa-check text-success inline"></i>';
        }
        else {
            //$str = '<a class="btn btn-danger btn-sm">Inactive</a>';
            $str = '<i class="fa fa-times text-danger inline"></i>';
        }

        return $str;
    }

}


if (!function_exists('dispaly_verified_in_table')) {

    function dispaly_verified_in_table($verified) {
        $str = '';
        if ($verified == 1) {
            //$str = '<a class="btn btn-success btn-sm">Yes</a>';
            $str = '<i class="fa fa-check text-success inline"></i>';
        }
        elseif ($verified == 2) {
            //$str = '<a class="btn btn-danger btn-sm">No</a>';
            $str = '<i class="fa fa-times text-danger inline"></i>';
        }

        return $str;
    }

}


if (!function_exists('dispaly_featured_in_table')) {

    function dispaly_featured_in_table($featured) {
        $str = '';
        if ($featured == 1) {
            //$str = '<a class="btn btn-success btn-sm">Yes</a>';
            $str = '<i class="fa fa-check text-success inline"></i>';
        }
        else {
            //$str = '<a class="btn btn-danger btn-sm">No</a>';
            $str = '<i class="fa fa-times text-danger inline"></i>';
        }

        return $str;
    }

}


if (!function_exists('setups_link_in_table')) {

    function setups_link_in_table($url, $setup) {
        $str = '<button data-bs-toggle="dropdown" class="btn btn-info btn-sm">
					<i class="fa fa-ellipsis-v"></i>
				</button>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="' . url('/' . $url) . '">
						<i class="fa fa-eye"></i> List of ' . $setup . '
					</a>
                </div>
				';

        return $str;
    }

}


if (!function_exists('view_link_in_table')) {

    function view_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item text-warning" href="' . route($route, $record_id) . '">
				<i class="fa fa-eye text-warning"></i> View
				</a>';

        return $str;
    }

}


if (!function_exists('edit_link_in_table')) {

    function edit_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item text-warning" href="' . route($route, $record_id) . '">
				<i class="fa fa-edit text-warning"></i> Edit Details
				</a>';

        return $str;
    }

}

if (!function_exists('invite_link_in_table')) {

    function invite_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item text-info" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off text-info "></i> Invite
				</a>';

        return $str;
    }

}
if (!function_exists('remove_invite_link_in_table')) {

    function remove_invite_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item text-info" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off text-info "></i> Remove Invite
				</a>';

        return $str;
    }

}

if (!function_exists('approve_link_in_table')) {

    function approve_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item text-info" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off text-info "></i> Approve
				</a>';

        return $str;
    }

}


if (!function_exists('reject_link_in_table')) {

    function reject_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item text-info" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off text-info "></i> Reject
				</a>';

        return $str;
    }

}


if (!function_exists('active_link_in_table')) {

    function active_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item text-info" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off text-info "></i> Make Active
				</a>';

        return $str;
    }

}


if (!function_exists('inactive_link_in_table')) {

    function inactive_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item text-danger" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off"></i> Make Inactive
				</a>';

        return $str;
    }

}


if (!function_exists('verified_link_in_table')) {

    function verified_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off "></i> Make Verified
				</a>';

        return $str;
    }

}


if (!function_exists('notverified_link_in_table')) {

    function notverified_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off"></i> Make Not Verified
				</a>';

        return $str;
    }

}


if (!function_exists('add_feature_link_in_table')) {

    function add_feature_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off"></i> Add to Featured Listing
				</a>';

        return $str;
    }

}


if (!function_exists('remove_feature_link_in_table')) {

    function remove_feature_link_in_table($route, $record_id) {
        $str = '<a class="dropdown-item" href="' . route($route, $record_id) . '">
				<i class="fa fa-power-off "></i> Remove From Featured Listing
				</a>';

        return $str;
    }

}


if (!function_exists('delete_link_in_table')) {

    function delete_link_in_table($record_id) {
        $str = '<a class="dropdown-item" href="#" ui-toggle-class="bounce" ui-target="#animate" onclick="deleteModal(' . $record_id . ')">
				<i class="fa fa-trash"></i> Delete
				</a>';

        return $str;
    }

}

if (!function_exists('dispaly_rating_in_table')) {

    function dispaly_rating_in_table($rating) {

        $str = '';
        for ($i = 1; $i <= 5; $i++) {
            $class = 'fa fa-star';
            if ($rating >= $i)
                $class .= ' color-star';

            $str .= '<i class="' . $class . '"></i>';
        }
        $str .= '<span> (' . $rating . ')</span>';

        return $str;
    }

}


if (!function_exists('login_modal_in_table')) {

    function login_modal_in_table($route, $record_id, $record_name) {
        $str = '
		<div class="modal fade" id="lm-' . $record_id . '" role="dialog" aria-labelledby="loginModal" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<form action="' . route($route, $record_id) . '" method="GET">
					<div class="modal-content modal-content-demo">
						<div class="modal-header">
							<h2 class="modal-title">Login as ' . $record_name . '</h2>
						</div>
						<div class="modal-body">
							<p>
								Are you sure you want to Login as User?
								<br>
								<strong>[ ' . $record_name . ' ]</strong>
							</p>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-danger">Yes</button>
						</div>
					</div>
				</form>
			</div>
		</div>';
        /*
          <button aria-label="Close" class="close btn-lm-close" type="button">
          <span aria-hidden="true">×</span>
          </button>
          <button type="button" class="btn btn-primary btn-lm-close" aria-label="Close">
          No
          </button>
         */
        return $str;
    }

}


if (!function_exists('delete_modal_in_table')) {

    function delete_modal_in_table($route, $record_id, $record_name) {
        $str = '
		<div class="modal fade" id="m-' . $record_id . '" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<form action="' . route($route, $record_id) . '" method="POST">
					<input type="hidden" name="_method" value="DELETE">
					<input type="hidden" name="_token" value="' . csrf_token() . '">
					<div class="modal-content modal-content-demo">
						<div class="modal-header">
							<h6 class="modal-title">Confirm delete following record</h6>
							<button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
								<span aria-hidden="true">×</span>
							</button>
						</div>
						<div class="modal-body">
							<p>
								Are you sure to delete this record?
								<br>
								<strong>[ ' . $record_name . ' ]</strong>
							</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">
								No
							</button>
							<button type="submit" class="btn btn-danger">Yes</button>
						</div>
					</div>
				</form>
			</div>
		</div>';

        return $str;
    }

}
