<?php

/*$action = true;
switch ($action) {

    case 'coach_dashboard':
        if ($user_type == 1) {

            if (isset($_REQUEST['listing']) && $_REQUEST['listing'] != "") {
                $listing = $_REQUEST['listing'];
            }
            else {
                $listing = "both";
            }

            if (isset($_REQUEST['order']) && $_REQUEST['order'] != "") {
                $order = $_REQUEST['order'];
            }
            else {
                $order = "desc";
            }

            $upcoming_listing = 0;
            $past_listing = 0;

            $session_start = strtotime(date('d-m-Y'));
            $_time_24 = (24 * 60 * 60);
            $session_end = ($session_start + $_time_24);
            $session_limit = (60 * 10);
            $current_time = time();

            if (isset($_REQUEST['page_no']) && $_REQUEST['page_no'] != "" && $_REQUEST['page_no'] != 0) {
                $page_no = $_REQUEST['page_no'];
            }
            else {
                $page_no = 1;
            }

            if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != "" && $_REQUEST['limit'] != 0) {
                $limit = $_REQUEST['limit'];
            }
            else {
                $limit = 5;
            }

            $offset = ($page_no - 1) * $limit;

            $upcoming_session_array = array();

            $upcoming_users = array();
            $upcoming_user_details = array();
            $upcoming_user_array = array();

            $all_session_array = array();
            $all_session_rows = array();

            $all_users = array();
            $all_user_details = array();
            $all_user_array = array();

            if ($listing == "upcoming" || $listing == "both") {

                $upcoming_listing = 1;

                $upcoming_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE session.time_start>='$session_start' AND time_end<='$session_end' and booking.user_id='$user_id' and booking.status<>'1' and booking.status<>'3' and booking.status<>'4' and booking.status<>'5' and booking.status<>'6'");

                $upcoming_total_records = mysqli_fetch_array($upcoming_result_count);
                $upcoming_total_records = $upcoming_total_records['total_records'];
                $upcoming_total_no_of_pages = ceil($upcoming_total_records / $limit);

                $upcoming_count = 0;

                $sql = "SELECT session.id, session.user_id, session.type, session.price, session.color, session.time_start, session.time_end, booking.id, booking.session_id, booking.req_user_id, booking.status

                                    FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE session.time_start>='$session_start' AND time_end<='$session_end' and booking.user_id='$user_id' and booking.status<>'1' and booking.status<>'3' and booking.status<>'4' and booking.status<>'5' and booking.status<>'6' LIMIT $limit OFFSET $offset ";

                $bookings = $database->query($sql);

                while ($booking = $database->fetch_assoc($bookings)) {

                    $upcoming_session_rows = array();

                    $booking_id = $booking['id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $booking_user_id = $booking['req_user_id'];

                    $session_color = stripslashes($booking['color']);
                    $session_price = stripslashes($booking['price']);
                    $session_type = $aval_type = get_session_type($booking['type']);
                    $session_user_id = stripslashes($booking['user_id']);

                    $time_start = $booking['time_start'];
                    $time_end = $booking['time_end'];

                    $time_to_go = ($time_start - $current_time);

                    $public_url = get_user_profile_data('meetinglink', $session_user_id);

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }

                    $upcoming_count++;

                    $upcoming_session_rows["session_id"] = $session_id;

                    $upcoming_session_rows["date"] = date('M d, Y', $time_start);

                    $upcoming_session_rows["start_time"] = date('h:i A', $time_start);

                    $upcoming_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        $upcoming_session_rows["session_type"] = $upcoming_session_rows["session_type"] . " - " . $aval_type;
                    }

                    $upcoming_session_rows["booked_by"] = get_user_name($booking_user_id);
                    $upcoming_session_rows["status"] = get_booking_status_details($status);
                    $upcoming_session_rows["session_price"] = $session_price;

                    $have_link = 0;

                    if ($status == 2) {
                        $show_link = 0;

                        if ($time_to_go <= $session_limit || ($time_start >= $current_time && $time_end <= $current_time)) {
                            $show_link = 1;
                        }
                        if ($time_start > $current_time) {
                            $upcoming_session_rows["action"] = get_expiry($time_start);
                        }
                        elseif ($show_link == 1) {
                            // echo " Session Started ";
                        }
                        elseif ($show_link == 0) {
                            $upcoming_session_rows["action"] = " Session Expired ";
                        }
                        if ($show_link == 1) {
                            $have_link = 1;
                        }
                    }
                    elseif ($time_start > $current_time) {
                        $upcoming_session_rows["action"] = get_expiry($time_start);
                    }
                    else {
                        $upcoming_session_rows["action"] = "Session Expired";
                    }

                    $upcoming_session_rows["have_link"] = $have_link;
                    if ($have_link == 1) {

                        $upcoming_session_rows["action"] = "Session Started";
                        $upcoming_session_rows["link"] = $public_url;
                    }




                    if (!in_array($booking_user_id, $upcoming_users) && $booking_user_id != 0) {
                        $upcoming_users[] = $booking_user_id;
                    }

                    $upcoming_session_array[] = $upcoming_session_rows;
                }

                foreach ($upcoming_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $upcoming_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $upcoming_personal_profile["f_name"] = $res['first_name'];
                        $upcoming_personal_profile["l_name"] = $res['last_name'];
                        $upcoming_personal_profile["conatc_number"] = $res['conatc_number'];
                        $upcoming_personal_profile["about_me"] = $res['about_me'];
                        $upcoming_personal_profile["zip_code"] = $res['zip_code'];
                        $upcoming_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $upcoming_user_array[] = $upcoming_personal_profile;
                    }
                }
            }



            if ($listing == "all" || $listing == "both") {

                $past_listing = 1;

                $past_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` where user_id = '$user_id' ");

                $past_total_records = mysqli_fetch_array($past_result_count);
                $past_total_records = $past_total_records['total_records'];
                $past_total_no_of_pages = ceil($past_total_records / $limit);

                $past_count = 0;

                if ($order == "desc") {

                    $availabilities = $database->query("select id, user_id, type, price, color, time_start, time_end, booked from `opt_sessions` where user_id = '$user_id' order by time_start desc LIMIT $limit OFFSET $offset ");
                }
                else {

                    $availabilities = $database->query("select id, user_id, type, price, color, time_start, time_end, booked from `opt_sessions` where user_id = '$user_id' order by time_start asc LIMIT $limit OFFSET $offset ");
                }


                while ($availability = $database->fetch_assoc($availabilities)) {
                    $time_start = $availability['time_start'];
                    $time_end = $availability['time_end'];

                    $session_id = $availability['id'];

                    $session_color = stripslashes($availability['color']);
                    $session_price = stripslashes($availability['price']);
                    $session_type = $aval_type = get_session_type($availability['type']);
                    $session_user_id = stripslashes($availability['user_id']);

                    $booking_id = 0;
                    $booking_user_id = 0;
                    $status = 0;

                    $booked = $availability['booked'];
                    if ($booked == 1) {
                        $bookings = $database->query("SELECT id, req_user_id, status FROM `opt_bookings` WHERE session_id='$session_id'");
                        while ($booking = $database->fetch_assoc($bookings)) {

                            $booking_id = $booking['id'];

                            $booking_user_id = $booking['req_user_id'];

                            $status = $booking['status'];
                        }
                    }

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }

                    $past_count++;

                    $all_session_rows["session_id"] = $session_id;

                    $all_session_rows["date"] = date('M d, Y', $time_start);

                    $all_session_rows["start_time"] = date('h:i A', $time_start);

                    $all_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        $all_session_rows["session_type"] = $all_session_rows["session_type"] . " - " . $aval_type;
                    }


                    if ($status == 1 || $status == 2) {

                        $all_session_rows["booked_by"] = get_user_name($booking_user_id);
                    }
                    else {

                        $all_session_rows["booked_by"] = '-';
                    }


                    $all_session_rows["status"] = get_booking_status_details($status);
                    $all_session_rows["price"] = $session_price;
                    if (!in_array($booking_user_id, $all_users) && $booking_user_id != 0) {
                        $all_users[] = $booking_user_id;
                    }

                    $all_session_array[] = $all_session_rows;
                }

                foreach ($all_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $all_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $all_personal_profile["f_name"] = $res['first_name'];
                        $all_personal_profile["l_name"] = $res['last_name'];
                        $all_personal_profile["conatc_number"] = $res['conatc_number'];
                        $all_personal_profile["about_me"] = $res['about_me'];
                        $all_personal_profile["zip_code"] = $res['zip_code'];
                        $all_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $all_user_array[] = $all_personal_profile;
                    }
                }
            }


            if ($upcoming_listing == 1 || $past_listing == 1) {

                $Response['responseCode'] = "201";
                $Response['responseState'] = "Success";
                $Response['responseText'] = "Successfully returned Sessions data";

                $Response['page_no'] = $page_no;
                $Response['limit'] = $limit;
                $Response['listing'] = $listing;
            }
            else {

                $Response['responseCode'] = "101";
                $Response['responseState'] = "Error";
                $Response['responseText'] = "Incorrect Listing value";
            }

            if ($upcoming_listing == 1) {

                $Response['upcoming_total_records'] = $upcoming_total_records;
                $Response['upcoming_current_count'] = $upcoming_count;
                $Response['upcoming_total_no_of_pages'] = $upcoming_total_no_of_pages;

                $Response['upcoming_session_details'] = $upcoming_session_array;
                $Response['upcoming_Player_details'] = $upcoming_user_array;
            }

            if ($past_listing == 1) {

                $Response['all_total_records'] = $past_total_records;
                $Response['all_current_count'] = $past_count;
                $Response['all_total_no_of_pages'] = $past_total_no_of_pages;

                $Response['all_session_details'] = $all_session_array;
                $Response['all_session_details'] = $all_session_array;
                $Response['all_Player_details'] = $all_user_array;
            }
        }
        else {

            $Response['responseCode'] = "101";
            $Response['responseState'] = "Error";
            $Response['responseText'] = "Incorrect User Type";
        }
        break;

    case 'player_dashboard':
        if ($user_type == 2) {

            if (isset($_REQUEST['listing']) && $_REQUEST['listing'] != "") {
                $listing = $_REQUEST['listing'];
            }
            else {
                $listing = "both";
            }

            if (isset($_REQUEST['order']) && $_REQUEST['order'] != "") {
                $order = $_REQUEST['order'];
            }
            else {
                $order = "desc";
            }

            $upcoming_listing = 0;
            $past_listing = 0;

            $session_start = strtotime(date('d-m-Y'));
            $_time_24 = (24 * 60 * 60);
            $session_end = ($session_start + $_time_24);
            $session_limit = (60 * 10);
            $current_time = time();

            if (isset($_REQUEST['page_no']) && $_REQUEST['page_no'] != "" && $_REQUEST['page_no'] != 0) {
                $page_no = $_REQUEST['page_no'];
            }
            else {
                $page_no = 1;
            }

            if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != "" && $_REQUEST['limit'] != 0) {
                $limit = $_REQUEST['limit'];
            }
            else {
                $limit = 5;
            }

            $offset = ($page_no - 1) * $limit;

            $upcoming_session_array = array();
            $upcoming_session_rows = array();

            $upcoming_users = array();
            $upcoming_user_details = array();
            $upcoming_user_array = array();

            $all_session_array = array();
            $all_session_rows = array();

            $all_users = array();
            $all_user_details = array();
            $all_user_array = array();

            if ($listing == "upcoming" || $listing == "both") {

                $upcoming_listing = 1;

                $upcoming_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE (session.time_start>='$session_start' AND time_end<='$session_end') and booking.req_user_id='$user_id' and booking.status<>'1' and booking.status<>'3' and booking.status<>'4' and booking.status<>'5' and booking.status<>'6'");

                $upcoming_total_records = mysqli_fetch_array($upcoming_result_count);
                $upcoming_total_records = $upcoming_total_records['total_records'];
                $upcoming_total_no_of_pages = ceil($upcoming_total_records / $limit);

                $upcoming_count = 0;

                $sql = "SELECT session.user_id, session.type, session.price, session.color, session.time_start, session.time_end, booking.id, booking.session_id, booking.req_user_id, booking.status

                                    FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE (session.time_start>='$session_start' AND time_end<='$session_end') and booking.req_user_id='$user_id' and booking.status<>'1' and booking.status<>'3' and booking.status<>'4' and booking.status<>'5' and booking.status<>'6'order by session.time_start asc  LIMIT $limit OFFSET $offset ";

                $bookings = $database->query($sql);

                while ($booking = $database->fetch_assoc($bookings)) {
                    $booking_id = $booking['id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $session_color = stripslashes($booking['color']);
                    $session_price = stripslashes($booking['price']);
                    $session_type = $aval_type = get_session_type($booking['type']);
                    $session_user_id = stripslashes($booking['user_id']);

                    $time_start = $booking['time_start'];
                    $time_end = $booking['time_end'];

                    $time_to_go = ($time_start - $current_time);

                    $public_url = get_user_profile_data('meetinglink', $session_user_id);

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }

                    $upcoming_count++;

                    $upcoming_session_rows["session_id"] = $session_id;

                    $upcoming_session_rows["date"] = date('M d, Y', $time_start);

                    $upcoming_session_rows["start_time"] = date('h:i A', $time_start);

                    $upcoming_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        $upcoming_session_rows["session_type"] = $upcoming_session_rows["session_type"] . " - " . $aval_type;
                    }

                    $upcoming_session_rows["coach"] = get_user_name($session_user_id);
                    $upcoming_session_rows["status"] = get_booking_status_details($status);
                    $upcoming_session_rows["session_price"] = $session_price;

                    $have_link = 0;

                    if ($status == 2) {
                        $show_link = 0;

                        if ($time_to_go <= $session_limit || ($time_start >= $current_time && $time_end <= $current_time)) {
                            $show_link = 1;
                        }
                        if ($time_start > $current_time) {
                            $upcoming_session_rows["action"] = get_expiry($time_start);
                        }
                        elseif ($show_link == 1) {
                            // echo " Session Started ";
                        }
                        elseif ($show_link == 0) {
                            $upcoming_session_rows["action"] = " Session Expired ";
                        }
                        if ($show_link == 1) {
                            $have_link = 1;
                        }
                    }
                    elseif ($time_start > $current_time) {
                        $upcoming_session_rows["action"] = get_expiry($time_start);
                    }
                    else {
                        $upcoming_session_rows["action"] = "Session Expired";
                    }

                    $upcoming_session_rows["have_link"] = $have_link;
                    if ($have_link == 1) {

                        $upcoming_session_rows["action"] = "Session Started";
                        $upcoming_session_rows["link"] = $public_url;
                    }


                    if (!in_array($session_user_id, $upcoming_users) && $session_user_id != 0) {
                        $upcoming_users[] = $session_user_id;
                    }

                    $upcoming_session_array[] = $upcoming_session_rows;
                }

                foreach ($upcoming_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $upcoming_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $upcoming_personal_profile["f_name"] = $res['first_name'];
                        $upcoming_personal_profile["l_name"] = $res['last_name'];
                        $upcoming_personal_profile["conatc_number"] = $res['conatc_number'];
                        $upcoming_personal_profile["about_me"] = $res['about_me'];
                        $upcoming_personal_profile["meetinglink"] = $res['meetinglink'];
                        $upcoming_personal_profile["zip_code"] = $res['zip_code'];
                        $upcoming_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $upcoming_user_array[] = $upcoming_personal_profile;
                    }
                }
            }



            if ($listing == "all" || $listing == "both") {

                $past_listing = 1;

                $past_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_bookings` WHERE req_user_id='$user_id' ");

                $past_total_records = mysqli_fetch_array($past_result_count);
                $past_total_records = $past_total_records['total_records'];
                $past_total_no_of_pages = ceil($past_total_records / $limit);

                $past_count = 0;

                if ($order == "desc") {
                    $sql = "SELECT booking.id, booking.session_id, booking.status, session.user_id, session.type, session.price, session.color, session.time_start, session.time_end
                                                FROM `opt_bookings` as booking 
                                                INNER JOIN `opt_sessions` as session ON booking.session_id = session.id
                                                WHERE booking.req_user_id='$user_id' 
                                                order by time_start desc 
                                                LIMIT $limit OFFSET $offset ";
                }
                else {
                    $sql = "SELECT booking.id, booking.session_id, booking.status, session.user_id, session.type, session.price, session.color, session.time_start, session.time_end
                                                FROM `opt_bookings` as booking 
                                                INNER JOIN `opt_sessions` as session ON booking.session_id = session.id
                                                WHERE booking.req_user_id='$user_id' 
                                                order by time_start asc 
                                                LIMIT $limit OFFSET $offset ";
                }


                $bookings = $database->query($sql);
                while ($booking = $database->fetch_assoc($bookings)) {

                    $booking_id = $booking['id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $session_color = '';
                    $session_price = 0;
                    $session_type = 0;
                    $session_user_id = 0;
                    $time_start = 0;
                    $time_end = 0;
                    $aval_type = 0;

                    $session_color = stripslashes($booking['color']);
                    $session_price = stripslashes($booking['price']);
                    $session_type = $aval_type = get_session_type($booking['type']);
                    $session_user_id = stripslashes($booking['user_id']);

                    $time_start = $booking['time_start'];
                    $time_end = $booking['time_end'];

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }


                    $past_count++;

                    $all_session_rows["session_id"] = $session_id;

                    $all_session_rows["date"] = date('M d, Y', $time_start);

                    $all_session_rows["start_time"] = date('h:i A', $time_start);

                    $all_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        $all_session_rows["session_type"] = $all_session_rows["session_type"] . " - " . $aval_type;
                    }

                    $all_session_rows["coach"] = get_user_name($session_user_id);

                    $all_session_rows["status"] = get_booking_status_details($status);
                    $all_session_rows["price"] = $session_price;
                    if (!in_array($session_user_id, $all_users) && $session_user_id != 0) {
                        $all_users[] = $session_user_id;
                    }

                    $all_session_array[] = $all_session_rows;
                }

                foreach ($all_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $all_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $all_personal_profile["f_name"] = $res['first_name'];
                        $all_personal_profile["l_name"] = $res['last_name'];
                        $all_personal_profile["conatc_number"] = $res['conatc_number'];
                        $all_personal_profile["about_me"] = $res['about_me'];
                        $all_personal_profile["meetinglink"] = $res['meetinglink'];
                        $all_personal_profile["zip_code"] = $res['zip_code'];
                        $all_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $all_user_array[] = $all_personal_profile;
                    }
                }
            }



            if ($upcoming_listing == 1 || $past_listing == 1) {

                $Response['responseCode'] = "201";
                $Response['responseState'] = "Success";
                $Response['responseText'] = "Successfully returned Sessions data";

                $Response['page_no'] = $page_no;
                $Response['limit'] = $limit;
                $Response['listing'] = $listing;
            }
            else {

                $Response['responseCode'] = "101";
                $Response['responseState'] = "Error";
                $Response['responseText'] = "Incorrect Listing value";
            }

            if ($upcoming_listing == 1) {

                $Response['upcoming_total_records'] = $upcoming_total_records;
                $Response['upcoming_current_count'] = $upcoming_count;
                $Response['upcoming_total_no_of_pages'] = $upcoming_total_no_of_pages;

                $Response['upcoming_session_details'] = $upcoming_session_array;
                $Response['upcoming_Coaches_details'] = $upcoming_user_array;
            }

            if ($past_listing == 1) {

                $Response['all_total_records'] = $past_total_records;
                $Response['all_current_count'] = $past_count;
                $Response['all_total_no_of_pages'] = $past_total_no_of_pages;

                $Response['all_session_details'] = $all_session_array;
                $Response['all_Coaches_details'] = $all_user_array;
            }
        }
        else {

            $Response['responseCode'] = "101";
            $Response['responseState'] = "Error";
            $Response['responseText'] = "Incorrect User Type";
        }
        break;

    case 'coach_my_sessions':
        if ($user_type == 1) {

            if (isset($_REQUEST['listing']) && $_REQUEST['listing'] != "") {
                $listing = $_REQUEST['listing'];
            }
            else {
                $listing = "both";
            }

            $upcoming_listing = 0;
            $past_listing = 0;

            $current_time = time();

            if (isset($_REQUEST['page_no']) && $_REQUEST['page_no'] != "" && $_REQUEST['page_no'] != 0) {
                $page_no = $_REQUEST['page_no'];
            }
            else {
                $page_no = 1;
            }

            if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != "" && $_REQUEST['limit'] != 0) {
                $limit = $_REQUEST['limit'];
            }
            else {
                $limit = 5;
            }

            $offset = ($page_no - 1) * $limit;

            $upcoming_session_array = array();

            $upcoming_users = array();
            $upcoming_user_details = array();
            $upcoming_user_array = array();

            $past_session_array = array();

            $past_users = array();
            $past_user_details = array();
            $past_user_array = array();

            if ($listing == "upcoming" || $listing == "both") {

                $upcoming_listing = 1;

                $upcoming_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session LEFT JOIN `opt_bookings` as booking ON session.id = booking.session_id

                                    WHERE session.time_start>='$current_time' and session.user_id='$user_id' AND (booking.status is NULL OR booking.status<>'5' ) order by time_start desc ");

                $upcoming_total_records = mysqli_fetch_array($upcoming_result_count);
                $upcoming_total_records = $upcoming_total_records['total_records'];
                $upcoming_total_no_of_pages = ceil($upcoming_total_records / $limit);

                $upcoming_count = 0;

                $sql = "SELECT session.id,session.user_id, session.type, session.price, session.color, session.time_start, session.time_end,  booking.session_id, booking.req_user_id, booking.status

                                    FROM `opt_sessions` as session LEFT JOIN `opt_bookings` as booking ON session.id = booking.session_id

                                    WHERE session.time_start>='$current_time' and session.user_id='$user_id' AND (booking.status is NULL OR booking.status<>'5' ) order by time_start desc LIMIT $limit OFFSET $offset ";

                $availabilities = $database->query($sql);
                while ($availability = $database->fetch_assoc($availabilities)) {

                    $upcoming_session_rows = array();

                    $time_start = stripslashes($availability['time_start']);
                    $time_end = stripslashes($availability['time_end']);

                    $session_id = $availability['id'];

                    $session_color = stripslashes($availability['color']);
                    $session_price = stripslashes($availability['price']);
                    $session_type = $aval_type = get_session_type($availability['type']);
                    $session_user_id = stripslashes($availability['user_id']);

                    $coach_id = $availability['user_id'];
                    $booking_user_id = 0;
                    $status = 0;

                    $booking_user_id = $availability['req_user_id'];

                    $status = $availability['status'];

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }

                    $upcoming_count++;

                    $upcoming_session_rows["session_id"] = $session_id;

                    $upcoming_session_rows["date"] = date('M d, Y', $time_start);

                    $upcoming_session_rows["start_time"] = date('h:i A', $time_start);

                    $upcoming_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        $upcoming_session_rows["session_type"] = $upcoming_session_rows["session_type"] . " - " . $aval_type;
                    }


                    if (!empty($booking_user_id)) {
                        $upcoming_session_rows["booked_by"] = get_user_name($booking_user_id);
                    }
                    else {
                        $upcoming_session_rows["booked_by"] = "-";
                    }

                    $upcoming_session_rows["status"] = get_booking_status_details($status);

                    $upcoming_session_rows["price"] = $session_price;

                    $current = strtotime(date('Y-m-d H:i:s'));

                    $current = $current_time + 86400;

                    $session_time = strtotime(date('Y-m-d', $time_start) . ' ' . date('H:i:s', $time_start));

                    if ($status != 1 and $status != 2) {

                        $upcoming_session_rows["action"] = "Delete";
                        $upcoming_session_rows["delete"] = 1;
                    }
                    else {
                        if ($session_time > $current) {

                            $upcoming_session_rows["action"] = "Cancel";
                            $upcoming_session_rows["cancel"] = 1;
                        }
                        else {

                            $upcoming_session_rows["action"] = "none";
                            $upcoming_session_rows["time_to_go"] = get_expiry($time_start);
                        }
                    }



                    if (!in_array($booking_user_id, $upcoming_users) && $booking_user_id != 0) {
                        $upcoming_users[] = $booking_user_id;
                    }

                    $upcoming_session_array[] = $upcoming_session_rows;
                }

                foreach ($upcoming_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $upcoming_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $upcoming_personal_profile["f_name"] = $res['first_name'];
                        $upcoming_personal_profile["l_name"] = $res['last_name'];
                        $upcoming_personal_profile["conatc_number"] = $res['conatc_number'];
                        $upcoming_personal_profile["about_me"] = $res['about_me'];
                        $upcoming_personal_profile["zip_code"] = $res['zip_code'];
                        $upcoming_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $upcoming_user_array[] = $upcoming_personal_profile;
                    }
                }
            }

            if ($listing == "past" || $listing == "both") {

                $past_listing = 1;

                $past_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id

                                    WHERE (session.time_start<='$current_time' OR booking.status='7' OR booking.status='8') and session.user_id='$user_id' order by time_start desc ");

                $past_total_records = mysqli_fetch_array($past_result_count);
                $past_total_records = $past_total_records['total_records'];
                $past_total_no_of_pages = ceil($past_total_records / $limit);

                $past_count = 0;

                $sql = "SELECT session.*, booking.*

                                    FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id

                                    WHERE (session.time_start<='$current_time' OR booking.status='7' OR booking.status='8') and session.user_id='$user_id' order by time_start desc LIMIT $limit OFFSET $offset ";

                $bookings = $database->query($sql);
                while ($booking = $database->fetch_assoc($bookings)) {

                    $booking_id = $booking['id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $booking_user_id = $booking['req_user_id'];

                    $session_color = stripslashes($booking['color']);
                    $session_price = stripslashes($booking['price']);
                    $session_type = $aval_type = get_session_type($booking['type']);
                    $session_user_id = stripslashes($booking['user_id']);

                    $time_start = stripslashes($booking['time_start']);
                    $time_end = stripslashes($booking['time_end']);

                    $past_count++;

                    $past_session_rows = array();

                    $coach_feedback = 0;
                    $player_feedback = 0;

                    if ($booking['coach_feedback'] != 0) {

                        $coach_feedback = $booking['coach_feedback'];
                    }

                    $coach_delivery = $booking['coach_delivery'];
                    $coach_rating = $booking['coach_rating'];
                    $coach_remarks = $booking['coach_remarks'];

                    if ($booking['player_feedback'] != 0) {

                        $player_feedback = $booking['player_feedback'];
                    }

                    $player_delivery = $booking['player_delivery'];
                    $player_rating = $booking['player_rating'];
                    $player_remarks = $booking['player_remarks'];

                    $past_session_rows["session_id"] = $session_id;

                    $past_session_rows["date"] = date('M d, Y', $time_start);

                    $past_session_rows["start_time"] = date('h:i A', $time_start);

                    $past_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        $past_session_rows["session_type"] = $past_session_rows["session_type"] . " - " . $aval_type;
                    }


                    $past_session_rows["booked_by"] = get_user_name($booking_user_id);

                    $past_session_rows["status"] = get_booking_status_details($status);
                    $past_session_rows["price"] = $session_price;

                    $past_session_rows['coach_feedback'] = $coach_feedback;
                    $past_session_rows['player_feedback'] = $player_feedback;

                    if ($status == 7 && $coach_feedback == 0) {
                        $past_session_rows['action'] = "Feedback";
                    }
                    else {
                        if ($coach_delivery > 0) {
                            $past_session_rows['action'] = "View Feedback";
                        }
                        else {
                            $past_session_rows['action'] = "Not Delivered";
                        }
                    }


                    if (!in_array($booking_user_id, $past_users) && $booking_user_id != 0) {
                        $past_users[] = $booking_user_id;
                    }

                    $past_session_array[] = $past_session_rows;
                }

                foreach ($past_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $past_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $past_personal_profile["f_name"] = $res['first_name'];
                        $past_personal_profile["l_name"] = $res['last_name'];
                        $past_personal_profile["conatc_number"] = $res['conatc_number'];
                        $past_personal_profile["about_me"] = $res['about_me'];
                        $past_personal_profile["zip_code"] = $res['zip_code'];
                        $past_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $past_user_array[] = $past_personal_profile;
                    }
                }
            }


            if ($upcoming_listing == 1 || $past_listing == 1) {

                $Response['responseCode'] = "201";
                $Response['responseState'] = "Success";
                $Response['responseText'] = "Successfully returned Sessions data";

                $Response['page_no'] = $page_no;
                $Response['limit'] = $limit;
                $Response['listing'] = $listing;
            }
            else {

                $Response['responseCode'] = "101";
                $Response['responseState'] = "Error";
                $Response['responseText'] = "Incorrect Listing value";
            }

            if ($upcoming_listing == 1) {

                $Response['upcoming_total_records'] = $upcoming_total_records;
                $Response['upcoming_current_count'] = $upcoming_count;
                $Response['upcoming_total_no_of_pages'] = $upcoming_total_no_of_pages;

                $Response['upcoming_session_details'] = $upcoming_session_array;
                $Response['upcoming_Player_details'] = $upcoming_user_array;
            }

            if ($past_listing == 1) {

                $Response['past_total_records'] = $past_total_records;
                $Response['past_current_count'] = $past_count;
                $Response['past_total_no_of_pages'] = $past_total_no_of_pages;

                $Response['past_session_details'] = $past_session_array;
                $Response['past_Player_details'] = $past_user_array;
            }
        }
        else {

            $Response['responseCode'] = "101";
            $Response['responseState'] = "Error";
            $Response['responseText'] = "Incorrect User Type";
        }
        break;

    case 'player_my_trainings':
        if ($user_type == 2) {

            if (isset($_REQUEST['listing']) && $_REQUEST['listing'] != "") {
                $listing = $_REQUEST['listing'];
            }
            else {
                $listing = "both";
            }

            $upcoming_listing = 0;
            $past_listing = 0;

            $current_time = time();
            $_24_time = (time() - (24 * 60 * 60));

            if (isset($_REQUEST['page_no']) && $_REQUEST['page_no'] != "" && $_REQUEST['page_no'] != 0) {
                $page_no = $_REQUEST['page_no'];
            }
            else {
                $page_no = 1;
            }

            if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != "" && $_REQUEST['limit'] != 0) {
                $limit = $_REQUEST['limit'];
            }
            else {
                $limit = 5;
            }

            $offset = ($page_no - 1) * $limit;

            $upcoming_session_array = array();

            $upcoming_users = array();
            $upcoming_user_details = array();
            $upcoming_user_array = array();

            $past_session_array = array();

            $past_users = array();
            $past_user_details = array();
            $past_user_array = array();

            if ($listing == "upcoming" || $listing == "both") {

                $upcoming_listing = 1;

                $upcoming_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id

                                    WHERE session.time_start>='$current_time' and booking.req_user_id='$user_id' AND booking.status<>'5'  order by time_start desc ");

                $upcoming_total_records = mysqli_fetch_array($upcoming_result_count);
                $upcoming_total_records = $upcoming_total_records['total_records'];
                $upcoming_total_no_of_pages = ceil($upcoming_total_records / $limit);

                $upcoming_count = 0;

                $sql = "SELECT session.*, booking.*

                                    FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id

                                    WHERE session.time_start>='$current_time' and booking.req_user_id='$user_id' AND booking.status<>'5'  order by time_start asc LIMIT $limit OFFSET $offset ";

                $bookings = $database->query($sql);
                while ($booking = $database->fetch_assoc($bookings)) {

                    $upcoming_session_rows = array();

                    $booking_id = $booking['id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $session_color = stripslashes($booking['color']);
                    $session_price = stripslashes($booking['price']);
                    $session_type = $aval_type = get_session_type($booking['type']);
                    $session_user_id = stripslashes($booking['user_id']);

                    $time_start = stripslashes($booking['time_start']);
                    $time_end = stripslashes($booking['time_end']);

                    $public_url = $SITE_URL . '/' . get_user_data('public_url', $session_user_id);

                    $upcoming_count++;

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }

                    $upcoming_session_rows["session_id"] = $session_id;

                    $upcoming_session_rows["date"] = date('M d, Y', $time_start);

                    $upcoming_session_rows["start_time"] = date('h:i A', $time_start);

                    $upcoming_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        $upcoming_session_rows["session_type"] = $upcoming_session_rows["session_type"] . " - " . $aval_type;
                    }


                    $upcoming_session_rows["coach"] = get_user_name($session_user_id);

                    $upcoming_session_rows["status"] = get_booking_status_details($status);

                    $upcoming_session_rows["price"] = $session_price;

                    $current = strtotime(date('Y-m-d H:i:s'));

                    $current = $current_time + 86400;

                    $session_time = strtotime(date('Y-m-d', $time_start) . ' ' . date('H:i:s', $time_start));

                    if ($session_time > $current) {

                        $upcoming_session_rows["action"] = "Cancel";
                        $upcoming_session_rows["cancel"] = 1;
                    }
                    else {

                        $upcoming_session_rows["action"] = "none";
                        $upcoming_session_rows["time_to_go"] = get_expiry($time_start);
                    }


                    if (!in_array($session_user_id, $upcoming_users) && $session_user_id != 0) {
                        $upcoming_users[] = $session_user_id;
                    }

                    $upcoming_session_array[] = $upcoming_session_rows;
                }


                foreach ($upcoming_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $upcoming_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $upcoming_personal_profile["f_name"] = $res['first_name'];
                        $upcoming_personal_profile["l_name"] = $res['last_name'];
                        $upcoming_personal_profile["conatc_number"] = $res['conatc_number'];
                        $upcoming_personal_profile["about_me"] = $res['about_me'];
                        $upcoming_personal_profile["meetinglink"] = $res['meetinglink'];
                        $upcoming_personal_profile["zip_code"] = $res['zip_code'];
                        $upcoming_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $upcoming_user_array[] = $upcoming_personal_profile;
                    }
                }
            }

            if ($listing == "past" || $listing == "both") {

                $past_listing = 1;

                $past_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id

                                    WHERE (session.time_start<='$current_time' OR booking.status='7' OR booking.status='8') and booking.req_user_id='$user_id' order by time_start desc ");

                $past_total_records = mysqli_fetch_array($past_result_count);
                $past_total_records = $past_total_records['total_records'];
                $past_total_no_of_pages = ceil($past_total_records / $limit);

                $past_count = 0;

                $sql = "SELECT session.*, booking.*

                                    FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id

                                    WHERE (session.time_start<='$current_time' OR booking.status='7' OR booking.status='8') and booking.req_user_id='$user_id' order by time_start desc LIMIT $limit OFFSET $offset ";

                $bookings = $database->query($sql);
                while ($booking = $database->fetch_assoc($bookings)) {
                    $proceed = 1;

                    $past_session_rows = array();

                    $booking_id = $booking['id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $session_color = '';
                    $session_price = 0;
                    $session_type = 0;
                    $session_user_id = 0;
                    $time_start = 0;
                    $time_end = 0;
                    $aval_type = 0;

                    $coach_feedback = 0;
                    $player_feedback = 0;

                    if ($booking['coach_feedback'] != 0) {

                        $coach_feedback = $booking['coach_feedback'];
                    }

                    $coach_delivery = $booking['coach_delivery'];
                    $coach_rating = $booking['coach_rating'];
                    $coach_remarks = $booking['coach_remarks'];

                    if ($booking['player_feedback'] != 0) {

                        $player_feedback = $booking['player_feedback'];
                    }

                    $player_delivery = $booking['player_delivery'];
                    $player_rating = $booking['player_rating'];
                    $player_remarks = $booking['player_remarks'];

                    $availabilities = $database->query("select * from `opt_sessions` where id = '$session_id'");
                    while ($availability = $database->fetch_assoc($availabilities)) {

                        $session_color = stripslashes($availability['color']);
                        $session_price = stripslashes($availability['price']);
                        $session_type = $aval_type = get_session_type($availability['type']);
                        $session_user_id = stripslashes($availability['user_id']);

                        $time_start = stripslashes($availability['time_start']);
                        $time_end = stripslashes($availability['time_end']);

                        if ($time_start >= $current_time) {
                            $proceed = 0;
                        }
                        if ($status == 7 || $status == 8) {
                            $proceed = 1;
                        }
                    }

                    if ($proceed) {

                        $past_count++;

                        $past_session_rows["session_id"] = $session_id;

                        $past_session_rows["date"] = date('M d, Y', $time_start);

                        $past_session_rows["start_time"] = date('h:i A', $time_start);

                        $past_session_rows["session_type"] = $session_type;

                        if ($session_type != $aval_type) {
                            $past_session_rows["session_type"] = $past_session_rows["session_type"] . " - " . $aval_type;
                        }


                        $past_session_rows["coach"] = get_user_name($session_user_id);

                        $past_session_rows["status"] = get_booking_status_details($status);
                        $past_session_rows["price"] = $session_price;

                        $past_session_rows['coach_feedback'] = $coach_feedback;
                        $past_session_rows['player_feedback'] = $player_feedback;

                        if ($status == 7 && $player_feedback == 0) {
                            $past_session_rows['action'] = "Feedback";
                        }
                        else {
                            if ($player_delivery > 0) {
                                $past_session_rows['action'] = "View Feedback";
                            }
                            else {
                                $past_session_rows['action'] = "Not Delivered";
                            }
                        }

                        if ($status == 8) {
                            
                        }






                        if (!in_array($session_user_id, $past_users) && $session_user_id != 0) {
                            $past_users[] = $session_user_id;
                        }

                        $past_session_array[] = $past_session_rows;
                    }
                }

                foreach ($past_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $past_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $past_personal_profile["f_name"] = $res['first_name'];
                        $past_personal_profile["l_name"] = $res['last_name'];
                        $past_personal_profile["conatc_number"] = $res['conatc_number'];
                        $past_personal_profile["about_me"] = $res['about_me'];
                        $past_personal_profile["zip_code"] = $res['zip_code'];
                        $past_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $past_user_array[] = $past_personal_profile;
                    }
                }
            }


            if ($upcoming_listing == 1 || $past_listing == 1) {

                $Response['responseCode'] = "201";
                $Response['responseState'] = "Success";
                $Response['responseText'] = "Successfully returned Sessions data";

                $Response['page_no'] = $page_no;
                $Response['limit'] = $limit;
                $Response['listing'] = $listing;
            }
            else {

                $Response['responseCode'] = "101";
                $Response['responseState'] = "Error";
                $Response['responseText'] = "Incorrect Listing value";
            }

            if ($upcoming_listing == 1) {

                $Response['upcoming_total_records'] = $upcoming_total_records;
                $Response['upcoming_current_count'] = $upcoming_count;
                $Response['upcoming_total_no_of_pages'] = $upcoming_total_no_of_pages;

                $Response['upcoming_session_details'] = $upcoming_session_array;
                $Response['upcoming_coach_details'] = $upcoming_user_array;
            }

            if ($past_listing == 1) {

                $Response['past_total_records'] = $past_total_records;
                $Response['past_current_count'] = $past_count;
                $Response['past_total_no_of_pages'] = $past_total_no_of_pages;

                $Response['past_session_details'] = $past_session_array;
                $Response['past_coach_details'] = $past_user_array;
            }
        }
        else {

            $Response['responseCode'] = "101";
            $Response['responseState'] = "Error";
            $Response['responseText'] = "Incorrect User Type";
        }
        break;

    case 'coach_my_payments':
        if ($user_type == 1) {

            if (isset($_REQUEST['listing']) && $_REQUEST['listing'] != "") {
                $listing = $_REQUEST['listing'];
            }
            else {
                $listing = "both";
            }

            $upcoming_listing = 0;
            $past_listing = 0;

            $current_time = (time() - (24 * 60 * 60));

            if (isset($_REQUEST['page_no']) && $_REQUEST['page_no'] != "" && $_REQUEST['page_no'] != 0) {
                $page_no = $_REQUEST['page_no'];
            }
            else {
                $page_no = 1;
            }

            if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != "" && $_REQUEST['limit'] != 0) {
                $limit = $_REQUEST['limit'];
            }
            else {
                $limit = 5;
            }

            $offset = ($page_no - 1) * $limit;

            $upcoming_session_array = array();
            $upcoming_session_rows = array();

            $upcoming_users = array();
            $upcoming_user_details = array();
            $upcoming_user_array = array();

            $past_session_array = array();
            $past_session_rows = array();

            $past_users = array();
            $past_user_details = array();
            $past_user_array = array();

            if ($listing == "upcoming" || $listing == "both") {

                $upcoming_listing = 1;

                $upcoming_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE session.time_start>='$current_time' and booking.user_id='$user_id' and booking.status=1 order by time_start desc");

                $upcoming_total_records = mysqli_fetch_array($upcoming_result_count);
                $upcoming_total_records = $upcoming_total_records['total_records'];
                $upcoming_total_no_of_pages = ceil($upcoming_total_records / $limit);

                $upcoming_count = 0;

                $bookings = $database->query("SELECT session.id,session.user_id, session.type, session.price, session.color, session.time_start, session.time_end,  booking.session_id, booking.req_user_id, booking.status

                                    FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE session.time_start>='$current_time' and booking.user_id='$user_id' and booking.status=1 order by time_start desc LIMIT $limit OFFSET $offset ");

                while ($booking = $database->fetch_assoc($bookings)) {
                    $booking_id = $booking['id'];

                    $player_id = $booking['req_user_id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $session_color = stripslashes($booking['color']);
                    $session_price = stripslashes($booking['price']);
                    $session_type = $aval_type = get_session_type($booking['type']);
                    $session_user_id = stripslashes($booking['user_id']);

                    $time_start = stripslashes($booking['time_start']);
                    $time_end = stripslashes($booking['time_end']);

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }

                    $upcoming_count++;

                    $upcoming_session_rows["session_id"] = $session_id;

                    $upcoming_session_rows["date"] = date('M d, Y', $time_start);

                    $upcoming_session_rows["start_time"] = date('h:i A', $time_start);

                    $upcoming_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        $upcoming_session_rows["session_type"] = $upcoming_session_rows["session_type"] . " - " . $aval_type;
                    }

                    $upcoming_session_rows["booked_by"] = get_user_name($player_id);

                    $upcoming_session_rows["status"] = get_booking_status_details($status);

                    $upcoming_session_rows["price"] = $session_price;

                    if (!in_array($player_id, $upcoming_users) && $player_id != 0) {
                        $upcoming_users[] = $player_id;
                    }

                    $upcoming_session_array[] = $upcoming_session_rows;
                }

                foreach ($upcoming_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $upcoming_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $upcoming_personal_profile["f_name"] = $res['first_name'];
                        $upcoming_personal_profile["l_name"] = $res['last_name'];
                        $upcoming_personal_profile["conatc_number"] = $res['conatc_number'];
                        $upcoming_personal_profile["about_me"] = $res['about_me'];
                        $upcoming_personal_profile["zip_code"] = $res['zip_code'];
                        $upcoming_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $upcoming_user_array[] = $upcoming_personal_profile;
                    }
                }
            }


            if ($listing == "past" || $listing == "both") {

                $past_listing = 1;

                $past_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE booking.user_id='$user_id' and (booking.status=2 OR booking.status=7 OR booking.status=8 OR booking.status=9) order by time_start desc ");

                $past_total_records = mysqli_fetch_array($past_result_count);
                $past_total_records = $past_total_records['total_records'];
                $past_total_no_of_pages = ceil($past_total_records / $limit);

                $past_count = 0;

                $bookings = $database->query("SELECT session.id,session.user_id, session.type, session.price, session.color, session.time_start, session.time_end,  booking.session_id, booking.req_user_id, booking.status, booking.payment_id

                                    FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE booking.user_id='$user_id' and (booking.status=2 OR booking.status=7 OR booking.status=8 OR booking.status=9) order by time_start desc LIMIT $limit OFFSET $offset ");

                while ($booking = $database->fetch_assoc($bookings)) {

                    $booking_id = $booking['id'];

                    $player_id = $booking['req_user_id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $payment_id = $booking['payment_id'];

                    $session_color = stripslashes($booking['color']);
                    $session_price = stripslashes($booking['price']);
                    $session_type = $aval_type = get_session_type($booking['type']);
                    $session_user_id = stripslashes($booking['user_id']);

                    $time_start = stripslashes($booking['time_start']);
                    $time_end = stripslashes($booking['time_end']);

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }

                    $past_count++;

                    $past_session_rows["session_id"] = $session_id;

                    $past_session_rows["date_time"] = date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end);

                    $past_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        echo " - $aval_type";
                    }

                    $past_session_rows["booked_by"] = get_user_name($player_id);

                    $past_session_rows["status"] = get_booking_status_details($status);

                    $transaction_id = '';
                    if (($status == 2 || $status == 7 || $status == 8 || $status == 9) && $payment_id != 0) {
                        $transaction_id = get_payment_data('transaction_id', $payment_id);
                    }

                    if (($status == 2 || $status == 7 || $status == 8 || $status == 9) && $transaction_id != '') {

                        $past_session_rows["payment_date"] = date('M d, Y', get_payment_data('pay_date', $payment_id));

                        $past_session_rows["transaction_id"] = get_payment_data('transaction_id', $payment_id);
                    }

                    $past_session_rows["price"] = $session_price;

                    if (!in_array($player_id, $past_users) && $player_id != 0) {
                        $past_users[] = $player_id;
                    }

                    $past_session_array[] = $past_session_rows;
                }

                foreach ($past_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $past_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $past_personal_profile["f_name"] = $res['first_name'];
                        $past_personal_profile["l_name"] = $res['last_name'];
                        $past_personal_profile["conatc_number"] = $res['conatc_number'];
                        $past_personal_profile["about_me"] = $res['about_me'];
                        $past_personal_profile["zip_code"] = $res['zip_code'];
                        $past_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $past_user_array[] = $past_personal_profile;
                    }
                }
            }


            if ($upcoming_listing == 1 || $past_listing == 1) {

                $Response['responseCode'] = "201";
                $Response['responseState'] = "Success";
                $Response['responseText'] = "Successfully returned Sessions data";

                $Response['page_no'] = $page_no;
                $Response['limit'] = $limit;
                $Response['listing'] = $listing;
            }
            else {

                $Response['responseCode'] = "101";
                $Response['responseState'] = "Error";
                $Response['responseText'] = "Incorrect Listing value";
            }

            if ($upcoming_listing == 1) {

                $Response['upcoming_total_records'] = $upcoming_total_records;
                $Response['upcoming_current_count'] = $upcoming_count;
                $Response['upcoming_total_no_of_pages'] = $upcoming_total_no_of_pages;

                $Response['upcoming_payment_details'] = $upcoming_session_array;
                $Response['upcoming_Player_details'] = $upcoming_user_array;
            }

            if ($past_listing == 1) {

                $Response['past_total_records'] = $past_total_records;
                $Response['past_current_count'] = $past_count;
                $Response['past_total_no_of_pages'] = $past_total_no_of_pages;

                $Response['past_payment_details'] = $past_session_array;
                $Response['past_Player_details'] = $past_user_array;
            }
        }
        else {

            $Response['responseCode'] = "101";
            $Response['responseState'] = "Error";
            $Response['responseText'] = "Incorrect User Type";
        }
        break;

    case 'player_my_payments':
        if ($user_type == 2) {

            if (isset($_REQUEST['listing']) && $_REQUEST['listing'] != "") {
                $listing = $_REQUEST['listing'];
            }
            else {
                $listing = "both";
            }

            $upcoming_listing = 0;
            $past_listing = 0;

            $current = time();
            $current_time = (time() - (24 * 60 * 60));

            if (isset($_REQUEST['page_no']) && $_REQUEST['page_no'] != "" && $_REQUEST['page_no'] != 0) {
                $page_no = $_REQUEST['page_no'];
            }
            else {
                $page_no = 1;
            }

            if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != "" && $_REQUEST['limit'] != 0) {
                $limit = $_REQUEST['limit'];
            }
            else {
                $limit = 5;
            }

            $offset = ($page_no - 1) * $limit;

            $upcoming_session_array = array();
            $upcoming_session_rows = array();

            $upcoming_users = array();
            $upcoming_user_details = array();
            $upcoming_user_array = array();

            $past_session_array = array();
            $past_session_rows = array();

            $past_users = array();
            $past_user_details = array();
            $past_user_array = array();

            if ($listing == "upcoming" || $listing == "both") {

                $upcoming_listing = 1;

                $upcoming_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE session.time_start>='$current_time' and booking.req_user_id='$user_id' and booking.status=1 order by time_start desc ");

                $upcoming_total_records = mysqli_fetch_array($upcoming_result_count);
                $upcoming_total_records = $upcoming_total_records['total_records'];
                $upcoming_total_no_of_pages = ceil($upcoming_total_records / $limit);

                $upcoming_count = 0;

                $bookings = $database->query("SELECT session.id,session.user_id, session.type, session.price, session.color, session.time_start, session.time_end,  booking.session_id, booking.req_user_id, booking.status

                                    FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE session.time_start>='$current_time' and booking.req_user_id='$user_id' and booking.status=1 order by time_start desc LIMIT $limit OFFSET $offset ");
                while ($booking = $database->fetch_assoc($bookings)) {
                    $booking_id = $booking['id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $session_color = '';
                    $session_price = 0;
                    $session_type = 0;
                    $session_user_id = 0;
                    $time_start = 0;
                    $time_end = 0;
                    $aval_type = 0;

                    $session_color = stripslashes($booking['color']);
                    $session_price = stripslashes($booking['price']);
                    $session_type = $aval_type = get_session_type($booking['type']);
                    $session_user_id = stripslashes($booking['user_id']);

                    $time_start = stripslashes($booking['time_start']);
                    $time_end = stripslashes($booking['time_end']);

                    $public_url = $SITE_URL . '/' . get_user_data('public_url', $session_user_id);

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }

                    $upcoming_count++;

                    $upcoming_session_rows["session_id"] = $session_id;

                    $upcoming_session_rows["date"] = date('M d, Y', $time_start);

                    $upcoming_session_rows["start_time"] = date('h:i A', $time_start);

                    $upcoming_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        $upcoming_session_rows["session_type"] = $upcoming_session_rows["session_type"] . " - " . $aval_type;
                    }

                    $upcoming_session_rows["coach"] = get_user_name($session_user_id);

                    $upcoming_session_rows["status"] = get_booking_status_details($status);

                    $upcoming_session_rows["price"] = $session_price;

                    if (!in_array($session_user_id, $upcoming_users) && $session_user_id != 0) {
                        $upcoming_users[] = $session_user_id;
                    }

                    $upcoming_session_array[] = $upcoming_session_rows;
                }

                foreach ($upcoming_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $upcoming_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $upcoming_personal_profile["f_name"] = $res['first_name'];
                        $upcoming_personal_profile["l_name"] = $res['last_name'];
                        $upcoming_personal_profile["conatc_number"] = $res['conatc_number'];
                        $upcoming_personal_profile["about_me"] = $res['about_me'];
                        $upcoming_personal_profile["meetinglink"] = $res['meetinglink'];
                        $upcoming_personal_profile["zip_code"] = $res['zip_code'];
                        $upcoming_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $upcoming_user_array[] = $upcoming_personal_profile;
                    }
                }
            }


            if ($listing == "past" || $listing == "both") {

                $past_listing = 1;

                $past_result_count = $database->query("SELECT COUNT(*) As total_records FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE booking.req_user_id='$user_id' and (booking.status=2 OR booking.status=7 OR booking.status=8 OR booking.status=9) order by time_start desc ");

                $past_total_records = mysqli_fetch_array($past_result_count);
                $past_total_records = $past_total_records['total_records'];
                $past_total_no_of_pages = ceil($past_total_records / $limit);

                $past_count = 0;

                $bookings = $database->query("SELECT session.id,session.user_id, session.type, session.price, session.color, session.time_start, session.time_end,  booking.session_id, booking.req_user_id, booking.status, booking.payment_id

                                    FROM `opt_sessions` as session INNER JOIN `opt_bookings` as booking ON session.id = booking.session_id 

                                    WHERE booking.req_user_id='$user_id' and (booking.status=2 OR booking.status=7 OR booking.status=8 OR booking.status=9) order by time_start desc LIMIT $limit OFFSET $offset ");

                while ($booking = $database->fetch_assoc($bookings)) {

                    $booking_id = $booking['id'];

                    $status = $booking['status'];

                    $session_id = $booking['session_id'];

                    $payment_id = $booking['payment_id'];

                    $session_color = '';
                    $session_price = 0;
                    $session_type = 0;
                    $session_user_id = 0;
                    $time_start = 0;
                    $time_end = 0;
                    $aval_type = 0;

                    $session_color = stripslashes($booking['color']);
                    $session_price = stripslashes($booking['price']);
                    $session_type = $aval_type = get_session_type($booking['type']);
                    $session_user_id = stripslashes($booking['user_id']);

                    $time_start = $booking['time_start'];
                    $time_end = $booking['time_end'];

                    if ($time_end <= time() && $status == 0) {
                        $status = 9;
                    }

                    $past_count++;

                    $past_session_rows["session_id"] = $session_id;

                    $past_session_rows["date_time"] = date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end);

                    $past_session_rows["session_type"] = $session_type;

                    if ($session_type != $aval_type) {
                        echo " - $aval_type";
                    }

                    $past_session_rows["coach"] = get_user_name($session_user_id);

                    $past_session_rows["status"] = get_booking_status_details($status);

                    $transaction_id = '';
                    if (($status == 2 || $status == 7 || $status == 8 || $status == 9) && $payment_id != 0) {
                        $transaction_id = get_payment_data('transaction_id', $payment_id);
                    }

                    if (($status == 2 || $status == 7 || $status == 8 || $status == 9) && $transaction_id != '') {

                        $past_session_rows["payment_date"] = date('M d, Y', get_payment_data('pay_date', $payment_id));

                        $past_session_rows["transaction_id"] = get_payment_data('transaction_id', $payment_id);
                    }

                    $past_session_rows["price"] = $session_price;

                    if (!in_array($session_user_id, $past_users) && $session_user_id != 0) {
                        $past_users[] = $session_user_id;
                    }

                    $past_session_array[] = $past_session_rows;
                }

                foreach ($past_users as $index) {
                    $query_str = "SELECT * FROM `opt_user_personal_profiles` WHERE userid = '$index' LIMIT 1";
                    // if login successfully done
                    $results = $database->query($query_str);

                    $past_personal_profile = array();

                    while ($res = $database->fetch_assoc($results)) {

                        $past_personal_profile["f_name"] = $res['first_name'];
                        $past_personal_profile["l_name"] = $res['last_name'];
                        $past_personal_profile["conatc_number"] = $res['conatc_number'];
                        $past_personal_profile["about_me"] = $res['about_me'];
                        $past_personal_profile["meetinglink"] = $res['meetinglink'];
                        $past_personal_profile["zip_code"] = $res['zip_code'];
                        $past_personal_profile["image"] = $SITE_URL . "/uploads/images/" . $res['coachpic'];

                        $past_user_array[] = $past_personal_profile;
                    }
                }
            }

            if ($upcoming_listing == 1 || $past_listing == 1) {

                $Response['responseCode'] = "201";
                $Response['responseState'] = "Success";
                $Response['responseText'] = "Successfully returned Sessions data";

                $Response['page_no'] = $page_no;
                $Response['limit'] = $limit;
                $Response['listing'] = $listing;
            }
            else {

                $Response['responseCode'] = "101";
                $Response['responseState'] = "Error";
                $Response['responseText'] = "Incorrect Listing value";
            }

            if ($upcoming_listing == 1) {

                $Response['upcoming_total_records'] = $upcoming_total_records;
                $Response['upcoming_current_count'] = $upcoming_count;
                $Response['upcoming_total_no_of_pages'] = $upcoming_total_no_of_pages;

                $Response['upcoming_payment_details'] = $upcoming_session_array;
                $Response['upcoming_coach_details'] = $upcoming_user_array;
            }

            if ($past_listing == 1) {

                $Response['past_total_records'] = $past_total_records;
                $Response['past_current_count'] = $past_count;
                $Response['past_total_no_of_pages'] = $past_total_no_of_pages;

                $Response['past_payment_details'] = $past_session_array;
                $Response['past_coach_details'] = $past_user_array;
            }
        }
        else {

            $Response['responseCode'] = "101";
            $Response['responseState'] = "Error";
            $Response['responseText'] = "Incorrect User Type";
        }
        break;

    default:
        break;
}
*/