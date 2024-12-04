<?php

use App\Models\Payment;

if (!function_exists('get_payment_data')) {

    function get_payment_data($field, $id) {
        $str = '';
        $result = Payment::where('id', $id)->pluck($field)->first();
        if ($result) {
            $str = stripslashes($result);
            return $str;
        }
        return $str;
    }

}
