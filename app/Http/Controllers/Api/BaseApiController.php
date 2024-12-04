<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\MainController as MainController;
use Illuminate\Support\Facades\DB;
use App\Models\AuthKey;
use App\Models\User;

class BaseApiController extends MainController {

    protected $_Token = NULL;
    protected $login_user_id = '';
    protected $login_user_type = '';

    public function getKey() {
        // return $_key;
    }

    protected function base_authentication($request, $action) {
        $auth_key = (!empty($request->header('token'))) ? $request->header('token') : NULL;

        $this->_Token = $auth_key;
        $user_id = 0;
        $array = array();
        $array['status'] = FALSE;
        $array['message'] = 'You are not authorized.';

        if (empty($auth_key) || $auth_key == NULL) {
            $array['message'] = 'Session is not active. Please Login again';
        }
        else {
            $Verifications = AuthKey::where('auth_key', $auth_key)->first();
            if (empty($Verifications)) {
                $array['message'] = 'Session is not active. Please Login again';
            }
            elseif ($action == 'close') {
                $this->expire_session();
            }
            else {
                $this->_User_Id = $user_id = $Verifications->user_id;
                $User = User::find($user_id);

                if (empty($User)) {
                    $this->expire_session();
                    $array['message'] = 'User Not Found!';
                }
                elseif ($User->status == 0) {
                    $this->expire_session();
                    $array['message'] = 'Your Account is Inactive/Suspended by Admin.';
                }
                elseif ($User->verified == 0) {
                    $this->expire_session();
                    $array['message'] = 'Please verify your email first.';
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                    $this->expire_session();
                    $array['message'] = 'Approval pending from Admin.';
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                    $this->expire_session();
                    $array['message'] = 'Your Account is rejected by Admin.';
                }
                else {
                    $this->login_user_id = $User->id;
                    $this->login_user_type = $User->user_type;

                    $array['status'] = TRUE;
                    $array['message'] = 'You are authorized.';
                }
            }
        }
        return $array;
    }

    protected function expire_session($user_id = 0) {
        $user_id = ($user_id == 0) ? $this->_User_Id : $user_id;
        $auth_key = $this->_Token;
        $auth_key = time() . '-Expired';

        DB::table('auth_keys')->where('user_id', $user_id)->where('auth_key', $auth_key)->update([
            'auth_key' => $auth_key
        ]);
    }

    public function sendResponse($data, $message) {
        $response = [
            'responseCode'  => '201',
            'responseState' => 'Success',
            'responseText'  => $message,
            'data'          => $data
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $responseCode = '101', $data = NULL, $code = 404) {
        $response = [
            'responseCode'  => strval($responseCode),
            'responseState' => 'Error',
            'responseText'  => $error,
            'data'          => $data
        ];

        return response()->json($response, $code);
    }

    public function sendSuccess($message, $data = NULL, $code = 200) {
        $response = [
            'responseCode'  => '201',
            'responseState' => 'Success',
            'responseText'  => $message,
            'data'          => $data
        ];

        return response()->json($response, $code);
    }

    public function sendData($data, $message) {
        return sendResponse($data, $message); //Response::json($data, 200);
    }

    public function convertExceptionToArray(Exception $e, $response = FALSE) {
        if (!config('app.debug')) {
            $statusCode = $e->getStatusCode();

            switch ($statusCode) {
                case 401:
                    $response['responseText'] = 'Unauthorized';
                    break;

                case 403:
                    $response['responseText'] = 'Forbidden';
                    break;

                case 404:
                    $response['responseText'] = 'Resource Not Found';
                    break;

                case 405:
                    $response['responseText'] = 'Method Not Allowed';
                    break;

                case 422:
                    $response['responseText'] = 'Request unable to be processed';
                    break;

                default:
                    $response['responseText'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $e->getMessage();
                    break;
            }
        }

        return parent::convertExceptionToArray($e, $response);
    }

}









// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\MainController as MainController;
// use Illuminate\Support\Facades\DB;
// use App\Models\AuthKey;
// use App\Models\User;

// class BaseApiController extends MainController {

//     protected $_Token = NULL;
//     protected $login_user_id = '';
//     protected $login_user_type = '';

//     public function getKey() {
//         // return $_key;
//     }

//     protected function base_authentication($request, $action) {
//         $auth_key = (!empty($request->header('token'))) ? $request->header('token') : NULL;

//         $this->_Token = $auth_key;
//         $user_id = 0;
//         $array = array();
//         $array['status'] = FALSE;
//         $array['message'] = 'You are not authorized.';

//         if (empty($auth_key) || $auth_key == NULL) {
//             $array['message'] = 'Session is not active. Please Login again';
//         }
//         else {
//             $Verifications = AuthKey::where('auth_key', $auth_key)->first();
//             if (empty($Verifications)) {
//                 $array['message'] = 'Session is not active. Please Login again';
//             }
//             elseif ($action == 'close') {
//                 $this->expire_session();
//             }
//             else {
//                 $this->_User_Id = $user_id = $Verifications->user_id;
//                 $User = User::find($user_id);

//                 if (empty($User)) {
//                     $this->expire_session();
//                     $array['message'] = 'User Not Found!';
//                 }
//                 elseif ($User->status == 0) {
//                     $this->expire_session();
//                     $array['message'] = 'Your Account is Inactive/Suspended by Admin.';
//                 }
//                 elseif ($User->verified == 0) {
//                     $this->expire_session();
//                     $array['message'] = 'Please verify your email first.';
//                 }
//                 elseif ($User->user_type == 3 && $User->admin_approved == 0) {
//                     $this->expire_session();
//                     $array['message'] = 'Approval pending from Admin.';
//                 }
//                 elseif ($User->user_type == 3 && $User->admin_approved == 2) {
//                     $this->expire_session();
//                     $array['message'] = 'Your Account is rejected by Admin.';
//                 }
//                 else {
//                     $this->login_user_id = $User->id;
//                     $this->login_user_type = $User->user_type;

//                     $array['status'] = TRUE;
//                     $array['message'] = 'You are authorized.';
//                 }
//             }
//         }
//         return $array;
//     }

//     protected function expire_session($user_id = 0) {
//         $user_id = ($user_id == 0) ? $this->_User_Id : $user_id;
//         $auth_key = $this->_Token;
//         $auth_key = time() . '-Expired';

//         DB::table('auth_keys')->where('user_id', $user_id)->where('auth_key', $auth_key)->update([
//             'auth_key' => $auth_key
//         ]);
//     }

//     public function sendResponse($data, $message) {
//         $response = [
//             'responseCode'  => '201',
//             'responseState' => 'Success',
//             'responseText'  => $message,
//             'data'          => $data
//         ];

//         return response()->json($response, 200, [], JSON_NUMERIC_CHECK);
//     }

//     public function sendError($error, $responseCode = '101', $data = NULL, $code = 404) {
//         $response = [
//             'responseCode'  => strval($responseCode),
//             'responseState' => 'Error',
//             'responseText'  => $error,
//             'data'          => $data
//         ];

//         return response()->json($response, $code, [], JSON_NUMERIC_CHECK);
//     }

//     public function sendSuccess($message, $data = NULL, $code = 200) {
//         $response = [
//             'responseCode'  => '201',
//             'responseState' => 'Success',
//             'responseText'  => $message,
//             'data'          => $data
//         ];

//         return response()->json($response, $code, [], JSON_NUMERIC_CHECK);
//     }

//     public function sendData($data, $message) {
//         return sendResponse($data, $message);
//     }

//     public function convertExceptionToArray(Exception $e, $response = FALSE) {
//         if (!config('app.debug')) {
//             $statusCode = $e->getStatusCode();

//             switch ($statusCode) {
//                 case 401:
//                     $response['responseText'] = 'Unauthorized';
//                     break;

//                 case 403:
//                     $response['responseText'] = 'Forbidden';
//                     break;

//                 case 404:
//                     $response['responseText'] = 'Resource Not Found';
//                     break;

//                 case 405:
//                     $response['responseText'] = 'Method Not Allowed';
//                     break;

//                 case 422:
//                     $response['responseText'] = 'Request unable to be processed';
//                     break;

//                 default:
//                     $response['responseText'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $e->getMessage();
//                     break;
//             }
//         }

//         return parent::convertExceptionToArray($e, $response);
//     }

// }
