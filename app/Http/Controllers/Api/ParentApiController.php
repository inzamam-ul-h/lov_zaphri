<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
use App\Models\ParentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use DateTime;
use File;
use App\Models\AuthKey;
use App\Models\User;
use App\Models\UserProfessional;

class ParentApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            switch ($action) {
                case 'my_members': {
                        return $this->my_members($request, $User);
                    }
                    break;

                case 'send_invite': {
                        return $this->send_invite($request, $User);
                    }
                    break;

                case 'invite_action': {
                        return $this->invite_action($request, $User);
                    }
                    break;

                case 'invite_listing': {
                        return $this->invite_listing($request, $User);
                    }
                    break;

                case 'parent_remove': {
                        return $this->parentRemove($request, $User);
                    }
                    break;

                case 'child_remove': {
                        return $this->childRemove($request, $User);
                    }
                    break;

                default: {
                        return $this->sendError('Invalid Request');
                    }
                    break;
            }
        }
        else{
            return $this->sendError($result['message']);
        }
    }

    private function my_members(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PARENT_USER) {
            $users = array();
            $parent_user_ids = $this->get_parent_user_ids($User);
            foreach ($parent_user_ids as $userid) {
                $UserData = User::find($userid);
                if (!empty($UserData)) {
                    $users[] = $this->get_user_array($UserData, FALSE);
                }
            }

            $response = [
                'members' => $users
            ];
            return $this->sendResponse($response, 'Successfully Returned Data');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function send_invite(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PARENT_USER) {
            if (isset($request->email) && ltrim(rtrim($request->email))) {
                $email = $request->email;
                $user_model = User::where('email', $email)->first();
                if ($user_model == null) {
                    return $this->sendError("User does not exist");
                }
                elseif ($user_model->parent_id != null && $user_model->parent_id != 0) {
                    return $this->sendError("User already associated");
                }
                $req_user_id = $user_model->id;
                $request_model = ParentRequest::where('parent_id', $user_id)->where('user_id', $req_user_id)->where('status', 0)->first();

                if ($request_model != null) {
                    return $this->sendError("Request already sent");
                }
                $model = new ParentRequest();
                $model->user_id = $req_user_id;
                $model->parent_id = $user_id;
                $model->save();
            }
            else {
                return $this->sendError("Missing fields");
            }

            return $this->sendSuccess('Successfully sent request');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function invite_action(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PLAYER_USER) {
            if (isset($request->invite_id) && ltrim(rtrim($request->invite_id)) && isset($request->accept) && ltrim(rtrim($request->accept))) {
                $invite_id = $request->invite_id;
                $invite_model = ParentRequest::where('id', $invite_id)->where('user_id', $user_id)->first();
                if ($invite_model == null) {
                    return $this->sendError("Invite does not exist");
                }

                $message = '';

                if ($User->parent_id != null) {
                    $this->sendError('User already associated with parent');
                }

                if ($request->accept == 2) {
                    $invite_model->status = 2;
                    $message = 'Request rejected successfully';
                }
                elseif ($request->accept == 1) {
                    $invite_model->status = 1;
                    $User->parent_id = $invite_model->parent_id;
                    $User->save();
                    $message = 'Request accept successfully';
                }

                $invite_model->save();

                return $this->sendSuccess($message);
            }
            else {
                return $this->sendError("Missing fields");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function parentRemove(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PLAYER_USER) {
            if (($User->parent_id ?? NULL) == null || ($User->parent_id ?? NULL) == 0) {
                return $this->sendError('No parent associated');
            }
            else {
                $User = User::find($user_id);
                $User->parent_id = 0;
                $User->save();
                return $this->sendSuccess('Successfully removed parent');
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function childRemove(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PARENT_USER) {
            if (isset($request->child_id) && ltrim(rtrim($request->child_id))) {
                $child_id = $request->child_id;
                $child_model = User::where('id', $child_id)->where('parent_id', $user_id)->first();
                if ($child_model == null) {
                    return $this->sendError("Child does not exist");
                }

                $child_model->parent_id = null;
                $child_model->save();

                return $this->sendSuccess('Successfully removed child');
            }
            else {
                return $this->sendError("Missing fields");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function invite_listing(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PLAYER_USER || $user_type == $this->_PARENT_USER) {
            $invites = array();

            if ($user_type == $this->_PLAYER_USER) {
                $parent_invites = ParentRequest::where('user_id', $user_id)->get();
            }
            elseif ($user_type == $this->_PARENT_USER) {
                $parent_invites = ParentRequest::where('parent_id', $user_id)->get();
            }
            foreach ($parent_invites as $invite) {
                $array = array();

                $array["invite_id"] = $invite->id;
                $array["user_id"] = $invite->user_id;
                $array["parent_id"] = $invite->parent_id;
                $array["status"] = $invite->status;
                $array["status_text"] = parent_invite_status($invite->status);

                if ($user_type == $this->_PLAYER_USER) {
                    $array["email"] = get_user_data('email', $invite->parent_id);
                    $array["phone_no"] = get_user_data('phone', $invite->parent_id);
                    $array["user_name"] = get_user_name($invite->parent_id);
                }
                elseif ($user_type == $this->_PARENT_USER) {
                    $array["email"] = get_user_data('email', $invite->user_id);
                    $array["phone_no"] = get_user_data('phone', $invite->user_id);
                    $array["user_name"] = get_user_name($invite->user_id);
                }


                $invites[] = $array;
            }

            $response = [
                'invites' => $invites
            ];
            return $this->sendResponse($response, 'Successfully Returned Data');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

}
