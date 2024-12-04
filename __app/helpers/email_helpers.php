<?php

use App\Models\Template;

if (!function_exists('get_email_template_body')) {

    function get_email_template_body($id) {
        //$Model_Data = Template::find($id);
        //$description = $Model_Data->description;

        $Model_Data = Template::select('subject', 'description')->where('id', $id)->first();

        return $Model_Data;
    }

}



if (!function_exists('get_email_template')) {

    function get_email_template() {
        $template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
    /* Base */

body,
body *:not(html):not(style):not(br):not(tr):not(code) {
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif,
        \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\';
    position: relative;
}

body {
    -webkit-text-size-adjust: none;
    background-color: #ffffff;
    color: #718096;
    height: 100%;
    line-height: 1.4;
    margin: 0;
    padding: 0;
    width: 100% !important;
}

p,
ul,
ol,
blockquote {
    line-height: 1.4;
    text-align: left;
}

a {
    color: #3869d4;
}

a img {
    border: none;
}

/* Typography */

h1 {
    color: #3d4852;
    font-size: 18px;
    font-weight: bold;
    margin-top: 0;
    text-align: left;
}

h2 {
    font-size: 16px;
    font-weight: bold;
    margin-top: 0;
    text-align: left;
}

h3 {
    font-size: 14px;
    font-weight: bold;
    margin-top: 0;
    text-align: left;
}

p {
    font-size: 16px;
    line-height: 1.5em;
    margin-top: 0;
    text-align: left;
}

p.sub {
    font-size: 12px;
}

img {
    max-width: 100%;
}

/* Layout */

.wrapper {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: #edf2f7;
    margin: 0;
    padding: 0;
    width: 100%;
}

.content {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 0;
    padding: 0;
    width: 100%;
}

/* Header */

.header {
    padding: 25px 0;
    text-align: center;
}

.header a {
    color: #3d4852;
    font-size: 19px;
    font-weight: bold;
    text-decoration: none;
}

/* Logo */

.logo {
    height: 75px;
    max-height: 75px;
    width: 75px;
}

/* Body */

.body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: #edf2f7;
    border-bottom: 1px solid #edf2f7;
    border-top: 1px solid #edf2f7;
    margin: 0;
    padding: 0;
    width: 100%;
}

.inner-body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    background-color: #ffffff;
    border-color: #e8e5ef;
    border-radius: 2px;
    border-width: 1px;
    box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
    margin: 0 auto;
    padding: 0;
    width: 570px;
}

/* Subcopy */

.subcopy {
    border-top: 1px solid #e8e5ef;
    margin-top: 25px;
    padding-top: 25px;
}

.subcopy p {
    font-size: 14px;
}

/* Footer */

.footer {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    margin: 0 auto;
    padding: 0;
    text-align: center;
    width: 570px;
}

.footer p {
    color: #b0adc5;
    font-size: 12px;
    text-align: center;
}

.footer a {
    color: #b0adc5;
    text-decoration: underline;
}

/* Tables */

.table table {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 30px auto;
    width: 100%;
}

.table th {
    border-bottom: 1px solid #edeff2;
    margin: 0;
    padding-bottom: 8px;
}

.table td {
    color: #74787e;
    font-size: 15px;
    line-height: 18px;
    margin: 0;
    padding: 10px 0;
}

.content-cell {
    max-width: 100vw;
    padding: 32px;
}

/* Buttons */

.action {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 30px auto;
    padding: 0;
    text-align: center;
    width: 100%;
}

.button {

    -webkit-text-size-adjust: none;
    border-radius: 4px;
    color: #fff;
    display: inline-block;
    overflow: hidden;
    text-decoration: none;
}

.button-blue,
.button-primary {
    background-color: #2d3748;
    border-bottom: 8px solid #2d3748;
    border-left: 18px solid #2d3748;
    border-right: 18px solid #2d3748;
    border-top: 8px solid #2d3748;
}

.button-green,
.button-success {
    background-color: #48bb78;
    border-bottom: 8px solid #48bb78;
    border-left: 18px solid #48bb78;
    border-right: 18px solid #48bb78;
    border-top: 8px solid #48bb78;
}

.button-red,
.button-error {
    background-color: #e53e3e;
    border-bottom: 8px solid #e53e3e;
    border-left: 18px solid #e53e3e;
    border-right: 18px solid #e53e3e;
    border-top: 8px solid #e53e3e;
}

/* Panels */

.panel {
    border-left: #2d3748 solid 4px;
    margin: 21px 0;
}

.panel-content {
    background-color: #edf2f7;
    color: #718096;
    padding: 16px;
}

.panel-content p {
    color: #718096;
}

.panel-item {
    padding: 0;
}

.panel-item p:last-of-type {
    margin-bottom: 0;
    padding-bottom: 0;
}

/* Utilities */

.break-all {
    word-break: break-all;
}

@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>
</head>

<body>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">';

        $template .= get_template_header();

        $template .= get_template_body();

        $template .= get_template_footer();

        $template .= '
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        return $template;
    }

}



if (!function_exists('get_template_header')) {

    function get_template_header() {
        $header = '  
        <tr>
            <td class="header">
                <a href="#" style="display: inline-block;">                    
                    <img src="' . asset_url('img/brand/logo.png') . '" alt=' . config('app.name') . '">
                </a>
            </td>
        </tr>'; //img class="logo" 

        return $header;
    }

}



if (!function_exists('get_template_body')) {

    function get_template_body() {
        $body = '
        <!-- Email Body -->
        <tr>
            <td class="body" width="100%" cellpadding="0" cellspacing="0">
                <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Body content -->
                    <tr>
                        <td class="content-cell">  
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td>
                                        [MESSAGEBODY]
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>'; //inner body table class="subcopy"

        return $body;
    }

}



if (!function_exists('get_template_footer')) {

    function get_template_footer() {
        $footer = ' 
        <tr>
            <td>
                <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td class="content-cell" align="center">
                            © ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>';

        return $footer;
    }

}



if (!function_exists('get_template_action_button')) {

    function get_template_action_button($url, $title) {
        $button = '
        <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td align="center">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td align="center">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                                    <tr>
                                        <td>
                                            <a href="' . $url . '" class="button button-primary" target="_blank" rel="noopener">
                                                ' . $title . '
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';

        return $button;
    }

}




if (!function_exists('get_email_content_in_template')) {

    function get_email_content_in_template($subject, $message) {
        $subject = trim($subject);
        $message = trim($message);

        $template = get_email_template();

        $template = str_replace('[SUBJECT]', $subject, $template);

        $template = str_replace('[MESSAGEBODY]', $message, $template);

        return $template;
    }

}

if (!function_exists('new_custom_mail')) {

    function new_custom_mail($email_data) {
        $response = array();
        $mail_receiver_email = (isset($email_data['mail_receiver_email '])) ? $email_data['mail_receiver_email '] : '';
        if ($mail_receiver_email == '') {
            $response['responseStatus'] = FALSE;
            $response['responseText'] = 'Email address missing';
        }
        else {
            $mail_receiver_name = (isset($email_data['mail_receiver_name'])) ? $email_data['mail_receiver_name'] : '';
            $subject = (isset($email_data['subject'])) ? $email_data['subject'] : '';
            $email_message = (isset($email_data['message'])) ? $email_data['message'] : '';
            $success_msg = (isset($email_data['success_msg'])) ? $email_data['success_msg'] : 'Email Sent Successfully';
            $error_msg = (isset($email_data['error_msg'])) ? $email_data['error_msg'] : 'Could not send email';

            $SITENAME = env('APP_NAME');
            $subject = str_replace('[SITENAME]', $SITENAME, $subject);
            $email_message = str_replace('[SITENAME]', $SITENAME, $email_message);

            $SITEURL = env('APP_URL');
            $subject = str_replace('[SITEURL]', $SITEURL, $subject);
            $email_message = str_replace('[SITEURL]', $SITEURL, $email_message);

            $email_message = get_email_content_in_template($subject, $email_message);

            $mail_sender_name = $SITENAME;
            $mail_sender_email = env('MAIL_FROM_ADDRESS');

            $data = [
                'subject '             => $subject,
                'email_message'        => $email_message,
                'mail_receiver_name'   => $mail_receiver_name,
                'mail_receiver_email ' => $mail_receiver_email,
                'mail_sender_name'     => $mail_sender_name,
                'mail_sender_email'    => $mail_sender_email
            ];

            try {
                Mail::send('emails.new_test_email', $data, function ($message) use ($subject, $mail_receiver_name, $mail_receiver_email, $mail_sender_name, $mail_sender_email) {
                    $message->to($mail_receiver_email, $mail_receiver_name);
                    $message->subject($subject);
                    $message->from($mail_sender_email, $mail_sender_name);
                });

                $response['responseStatus'] = TRUE;
                $response['responseText'] = $success_msg;
            }
            catch (\Throwable $th) {
                $response['responseStatus'] = FALSE;
                $response['responseText'] = $error_msg;
            }
        }

        return $response;
    }

}