<?php

use App\Models\General;

if (!function_exists('Site_Settings')) {

    function Site_Settings($Settings, $field, $lang = 'en') {
        $str = '';
        if ($lang == 'en') {
            $str = trim(Settings_en($Settings, $field));
        }

        return $str;
    }

}

if (!function_exists('Settings_en')) {

    function Settings_en($Settings, $field) {
        $str = '';
        switch ($field) {
            case 'site_url': {
                    $str = trim($Settings->site_url);
                    if ($str == '')
                        $str = env('APP_URL', '');
                }
                break;

            case 'site_title': {
                    $str = trim($Settings->site_title_en);
                    if ($str == '')
                        $str = env('APP_NAME', 'Zaphri');
                }
                break;

            case 'meta_description': {
                    $str = trim($Settings->site_desc_en);
                    if ($str == '')
                        $str = env('APP_NAME', 'Zaphri');
                }
                break;

            case 'meta_keywords': {
                    $str = trim($Settings->site_keywords_en);
                    if ($str == '')
                        $str = env('APP_NAME', 'Zaphri');
                }
                break;

            case 'address': {
                    $address_1 = trim($Settings->address);
                    if ($address_1 != '') {
                        $str = $address_1;
                    }
                }
                break;

            case 'about_zaphry': {
                    $str = $Settings->about_zaphry;
                }
                break;

            case 'phone': {
                    $str = $Settings->phone;
                }
                break;

            case 'mobile': {
                    $str = $Settings->mobile;
                }
                break;

            case 'fax': {
                    $str = $Settings->fax;
                }
                break;

            case 'email': {
                    $str = $Settings->email;
                }
                break;

            case 'working_time': {
                    $str = $Settings->working_time_en;
                }
                break;

            case 'facebook': {
                    $str = $Settings->facebook;
                }
                break;

            case 'twitter': {
                    $str = $Settings->twitter;
                }
                break;

            case 'google': {
                    $str = $Settings->google_link;
                }
                break;

            case 'linkedin': {
                    $str = $Settings->linkdin;
                }
                break;

            case 'youtube': {
                    $str = $Settings->youtube;
                }
                break;

            case 'instagram': {
                    $str = $Settings->instagram_link;
                }
                break;

            case 'pinterest': {
                    $str = $Settings->pinterest_link;
                }
                break;

            case 'tumbler': {
                    $str = $Settings->tumbler_link;
                }
                break;

            case 'flicker': {
                    $str = $Settings->flicker_link;
                }
                break;

            case 'whatsapp': {
                    $str = $Settings->whatsapp;
                }
                break;

            case 'dribble': {
                    $str = $Settings->dribble;
                }
                break;

            default:
                break;
        }

        return $str;
    }

}

if (!function_exists('GeneralSiteSettings')) {

    function GeneralSiteSettings($var) {
        $Setting = ContactDetail::find(1);
        return $Setting->$var;
    }

}

if (!function_exists('getGeneralData')) {

    function getGeneralData($var) {
        $Setting = General::find(1);
        return $Setting->$var;
    }

}

if (!function_exists('getBrowser')) {

    function getBrowser() {
        // check if IE 8 - 11+
        preg_match('/Trident\/(.*)/', $_SERVER['HTTP_USER_AGENT'], $matches);
        if ($matches) {
            $version = intval($matches[1]) + 4;     // Trident 4 for IE8, 5 for IE9, etc
            return 'Internet Explorer ' . ($version < 11 ? $version : $version);
        }

        preg_match('/MSIE (.*)/', $_SERVER['HTTP_USER_AGENT'], $matches);
        if ($matches) {
            return 'Internet Explorer ' . intval($matches[1]);
        }

        // check if Firefox, Opera, Chrome, Safari
        foreach (array('Firefox', 'OPR', 'Chrome', 'Safari') as $browser) {
            preg_match('/' . $browser . '/', $_SERVER['HTTP_USER_AGENT'], $matches);
            if ($matches) {
                return str_replace('OPR', 'Opera',
                        $browser);   // we don't care about the version, because this is a modern browser that updates itself unlike IE
            }
        }
    }

}


if (!function_exists('getOS')) {

    function getOS() {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $os_platform = "unknown";

        $os_array = array(
            '/windows nt 6.3/i'     => 'Windows 8.1',
            '/windows nt 6.2/i'     => 'Windows 8',
            '/windows nt 6.1/i'     => 'Windows 7',
            '/windows nt 6.0/i'     => 'Windows Vista',
            '/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     => 'Windows XP',
            '/windows xp/i'         => 'Windows XP',
            '/windows nt 5.0/i'     => 'Windows 2000',
            '/windows me/i'         => 'Windows ME',
            '/win98/i'              => 'Windows 98',
            '/win95/i'              => 'Windows 95',
            '/win16/i'              => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i'        => 'Mac OS 9',
            '/linux/i'              => 'Linux',
            '/ubuntu/i'             => 'Ubuntu',
            '/iphone/i'             => 'iPhone',
            '/ipod/i'               => 'iPod',
            '/ipad/i'               => 'iPad',
            '/android/i'            => 'Android',
            '/blackberry/i'         => 'BlackBerry',
            '/webos/i'              => 'Mobile'
        );

        foreach ($os_array as $regex => $value) {

            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        return $os_platform;
    }

}
?>
