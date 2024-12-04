<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
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
use App\Events\UserEvent;
use App\Models\ChatRequestModel;
use App\Models\Conversation;
use App\Exceptions\ApiOperationFailedException;
use App\Http\Requests\SendMessageRequest;
use App\Repositories\ChatRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CreateUserStatusRequest;
use App\Http\Requests\UpdateUserNotificationRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\ArchivedUser;
use App\Models\BlockedUser;
use App\Models\Group;
use App\Models\UserDevice;
use App\Repositories\UserDeviceRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class ChatAPIController
 */
class ChatApiController extends BaseController {

    /** @var ChatRepository */
    private $chatRepository;
    private $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  ChatRepository  $chatRepository
     */
    public function __construct(ChatRepository $chatRepository, UserRepository $userRepository) {
        $this->chatRepository = $chatRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            switch ($action) {

                case 'getUsersList': {
                        return $this->getUsersList($request, $User);
                    }
                    break;

                case 'getUsers': {
                        return $this->getUsers($request, $User);
                    }
                    break;

                case 'getProfile': {
                        return $this->getProfile($request, $User);
                    }
                    break;

                case 'removeProfileImage': {
                        return $this->removeProfileImage($request, $User);
                    }
                    break;

                case 'clearUserCustomStatus': {
                        return $this->clearUserCustomStatus($request, $User);
                    }
                    break;

                case 'myContacts': {
                        return $this->myContacts($request, $User);
                    }
                    break;

                case 'updateLastSeen': {
                        return $this->updateLastSeen($request, $User);
                    }
                    break;

                case 'updatePlayerId': {
                        return $this->updatePlayerId($request, $User);
                    }
                    break;

                case 'archiveChat': {
                        return $this->archiveChat($request, $User);
                    }
                    break;

                case 'unArchiveChat': {
                        return $this->unArchiveChat($request, $User);
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

    /**
     * @return JsonResponse
     */
    public function getUsersList($request, $User) {
        /* $myContactIds = $this->userRepository->myContactIds($User);
          $userIds = BlockedUser::orwhere('blocked_by', $this->login_user_id)
          ->orWhere('blocked_to', $this->login_user_id)
          ->pluck('blocked_by', 'blocked_to')
          ->toArray();

          $userIds = array_unique(array_merge($userIds, array_keys($userIds)));
          $userIds = array_unique(array_merge($userIds, $myContactIds));

          $users = User::whereNotIn('id', $userIds)
          ->orderBy('name', 'asc')
          ->select(['id', 'name'])//'is_online', 'gender', 'photo_url',
          ->limit(50)
          ->get()
          ->except($this->login_user_id); */

        $users = array();
        $parent_user_ids = $this->get_club_user_ids($User);
        foreach ($parent_user_ids as $userid) {
            if ($userid != $User->id) {
                $UserData = User::find($userid);
                if (!empty($UserData)) {
                    $users[] = $this->get_user_array($UserData, FALSE);
                }
            }
        }

        return $this->sendResponse(['users' => $users], 'Users retrieved successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function getUsers($request, $User) {
        $users = User::orderBy('name', 'asc')->get()->except($this->login_user_id);

        return $this->sendResponse(['users' => $users], 'Users retrieved successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function getProfile($request, $User) {
        $authUser = getLoggedInUser();
        $authUser->roles;
        $authUser = $authUser->apiObj();

        return $this->sendResponse(['user' => $authUser], 'Users retrieved successfully.');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateLastSeen($request, $User) {
        /** @var User $user */
        $user = $request->user();

        $lastSeen = ($request->has('status') && $request->get('status') > 0) ? null : Carbon::now();

        $user->update(['last_seen' => $lastSeen, 'is_online' => $request->get('status')]);

        return $this->sendResponse(['user' => $user], 'Last seen updated successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function removeProfileImage($request, $User) {
        /** @var User $user */
        $user = Auth::user();

        $user->deleteImage();

        return $this->sendSuccess('Profile image deleted successfully.');
    }

    /**
     * @param $ownerId
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function archiveChat($request, $User) {
        $ownerId = $request->ownerId;
        $archivedUser = ArchivedUser::whereOwnerId($ownerId)->whereArchivedBy($this->login_user_id)->first();
        if (is_string($ownerId) && !is_numeric($ownerId)) {
            $ownerType = Group::class;
        }
        else {
            $ownerType = User::class;
        }

        if (empty($archivedUser)) {
            ArchivedUser::create([
                'owner_id'    => $ownerId,
                'owner_type'  => $ownerType,
                'archived_by' => $this->login_user_id,
            ]);
        }

        return $this->sendSuccess('Chat archived successfully.');
    }

    /**
     * @param $ownerId
     * @return JsonResponse
     */
    public function unArchiveChat($request, $User) {
        $ownerId = $request->ownerId;
        $archivedUser = ArchivedUser::whereOwnerId($ownerId)->whereArchivedBy($this->login_user_id)->first();
        $archivedUser->delete();

        return $this->sendSuccess('Chat unarchived successfully.');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updatePlayerId($request, $User) {
        $playerId = $request->get('player_id');
        $input['user_id'] = $login_user_id;
        $input['player_id'] = $playerId;

        /** @var UserDeviceRepository $deviceRepo */
        $deviceRepo = App::make(UserDeviceRepository::class);
        $deviceRepo->store($input);

        $myPlayerIds = UserDevice::whereUserId($login_user_id)->get();

        return $this->sendResponse($myPlayerIds, 'Player updated successfully.');
    }

    /**
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function clearUserCustomStatus($request, $User) {
        $this->userRepository->clearUserCustomStatus();

        return $this->sendSuccess('Your status cleared successfully.');
    }

    /*
     * @return JsonResponse
     */

    public function myContacts($request, $User) {
        $myContactIds = $this->userRepository->myContactIds();

        $users = User::with(['userStatus' => function (HasOne $query) {
                        $query->select(['status', 'emoji']);
                    }])
                ->whereIn('id', $myContactIds)
                ->select(['id', 'name', 'photo_url', 'is_online'])
                ->orderBy('name', 'asc')
                ->limit(100)
                ->get();

        return $this->sendResponse([
                    'users'        => $users,
                    'myContactIds' => $myContactIds,
                        ], 'Users retrieved successfully.');
    }

    /**
     * @param  UpdateUserProfileRequest  $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateUserProfileRequest $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            try {
                $this->userRepository->updateProfile($request->all());

                return $this->sendSuccess('Profile updated successfully.');
            }
            catch (Exception $e) {
                return $this->sendError($e->getMessage());
            }
        }
        else{
            return $this->sendError($result['message']);
        }
    }

    /**
     * @param  ChangePasswordRequest  $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            /** @var User $user */
            //$User = Auth::user();
            $input = $request->all();

            $input['password'] = Hash::make($input['password']);

            $User->update(['password' => $input['password']]);

            return $this->sendSuccess('Password updated successfully.');
        }
        else{
            return $this->sendError($result['message']);
        }
    }

    /**
     * @param  UpdateUserNotificationRequest  $request
     * @return JsonResponse
     */
    public function updateNotification(UpdateUserNotificationRequest $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            $input = $request->all();
            $input['is_subscribed'] = ($input['is_subscribed'] == 'true') ? true : false;

            $this->userRepository->storeAndUpdateNotification($input);

            return $this->sendSuccess('Notification updated successfully.');
        }
        else{
            return $this->sendError($result['message']);
        }
    }

    /**
     * @param  CreateUserStatusRequest  $request
     * @return JsonResponse
     */
    public function setUserCustomStatus(CreateUserStatusRequest $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            $input = $request->only(['emoji', 'status', 'emoji_short_name']);

            $userStatus = $this->userRepository->setUserCustomStatus($input);

            return $this->sendResponse($userStatus, 'Your status set successfully.');
        }
        else{
            return $this->sendError($result['message']);
        }
    }

}
