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
use App\Models\Group;
use App\Models\GroupUser;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\API\CreateGroupRequest;
use App\Repositories\GroupRepository;
use App\Repositories\ChatRepository;
use App\Repositories\UserRepository;

class GroupApiController extends BaseController {

    /** @var GroupRepository */
    private $groupRepository;
    private $chatRepository;
    private $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  GroupRepository  $groupRepo
     */
    public function __construct(GroupRepository $groupRepo, ChatRepository $chatRepository, UserRepository $userRepository) {
        $this->groupRepository = $groupRepo;
        $this->chatRepository = $chatRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            switch ($action) {
                case 'listing': {
                        return $this->listing($request, $User);
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
     * @param  Request  $request
     * @return JsonResponse
     */
    public function listing(Request $request, $User) {
        $groups = $this->groupRepository->all($User);

        $groups = $groups->map(function ($group) {
            return [
        'id'        => $group->id,
        'name'      => $group->name,
        'photo_url' => $group->photo_url,
            ];
        });

        return $this->sendResponse($groups->toArray(), 'Groups retrieved successfully.');
    }

    //Group $group, Request $request, $action = 'listing'
    public function index2(Group $group, Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            $group_id = (!empty($request->group_id)) ? $request->group_id : 0;
            $group = Group::find($group_id);
            if (empty($group)) {
                return $this->sendError('Group Not Found');
            }

            $group_users = $group->users->pluck('id')->toArray();
            if (!in_array($User->id, $group_users)) {
                return $this->sendError('You are not member of the group.');
            }

            if (($action == 'update' || $action == 'addMembers') && !$this->groupRepository->isAuthUserGroupAdmin($group->id, $User)) {
                return $this->sendError('Only admin user can make changes in the group.');
            }

            switch ($action) {
                case 'show': {
                        return $this->show($group, $request, $User);
                    }
                    break;

                case 'members': {
                        return $this->members($group, $request, $User);
                    }
                    break;

                case 'conversations': {
                        return $this->conversations($group, $request, $User);
                    }
                    break;

                case 'update': {
                        return $this->update($group, $request, $User);
                    }
                    break;

                case 'addMembers': {
                        return $this->addMembers($group, $request, $User);
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
     * @param  Group  $group
     * @param  Request  $request
     * @return JsonResponse
     */
    public function show(Group $group, Request $request, $User) {
        $user_id = $this->_User_Id;

        $users = $this->get_members_array($group);
        $group = $group->toArray();
        $group['users'] = $users;

        return $this->sendResponse($group, 'Group retrieved successfully.');
    }

    public function members(Group $group, Request $request, $User) {
        $user_id = $this->_User_Id;
        $users = $this->get_members_array($group);
        $data = array();
        $data['users'] = $users;

        return $this->sendResponse($data, 'Group Members retrieved successfully.');
    }

    public function get_members_array(Group $group) {
        $users = array();
        $group_users = $group->users->pluck('id')->toArray();
        foreach ($group_users as $row) {
            $UserData = User::where('id', $row)->first();
            $users[] = $this->get_user_array($UserData, FALSE);
        }

        return $users;
    }

    public function conversations(Group $group, Request $request, $User) {
        $id = $group->id;
        $input = $request->all();
        $input['is_group'] = 1;
        $conversations = $this->userRepository->getConversation($id, $input, $User);
        $group = $conversations['group']->toArray();

        $data = array();
        $data['last_conversations'] = $group['last_conversations'];
        $data['oldest'] = $conversations['oldest'];
        $data['latest'] = $conversations['latest'];
        $data['conversations'] = $conversations['conversations'];

        return $this->sendResponse($data, 'Conversation retrieved successfully.');
    }

    /**
     * @param  Group  $group
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Group $group, Request $request, $User) {
        $user_id = $this->_User_Id;

        $request->validate([
            'photo' => 'mimes:png,jpeg,jpg',
        ]);

        /* if (!Auth::user()->hasRole('Admin') && !canMemberAddGroup()) {
          return $this->sendError('Sorry, you can not create group.');
          } */

        $input = $request->all();
        unset($input['users']);

        if ($group->my_role === GroupUser::ROLE_ADMIN) {
            $input['group_type'] = ($input['group_type'] == '1') ? Group::TYPE_OPEN : Group::TYPE_CLOSE;
            $input['privacy'] = ($input['privacy'] == '1') ? Group::PRIVACY_PUBLIC : Group::PRIVACY_PRIVATE;
        }
        else {
            unset($input['group_type']);
            unset($input['privacy']);
        }

        [$group, $conversation] = $this->groupRepository->update($input, $group->id, $User);

        return $this->sendResponse(
                        ['group' => $group->toArray(), 'conversation' => $conversation], 'Group details updated successfully.'
        );
    }

    /**
     * @param  Group  $group
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function addMembers(Group $group, Request $request, $User) {
        $user_id = $this->_User_Id;

        if ($group->privacy == Group::PRIVACY_PRIVATE && !$this->groupRepository->isAuthUserGroupAdmin($group->id, $User)) {
            return $this->sendError('Only admin user can add members to the group');
        }
        $users = $request->get('members');

        /** @var User $addedMembers */
        [$addedMembers, $conversation] = $this->groupRepository->addMembersToGroup($group, $users, 0, $User);
        $users = $this->get_members_array($group);
        $group = $group->toArray();
        $group['users'] = $users; //$addedMembers;

        return $this->sendResponse(['group' => $group, 'conversation' => $conversation], 'Members added successfully.');
    }

    public function index3(CreateGroupRequest $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            switch ($action) {
                case 'create': {
                        return $this->create($request, $User);
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
     * @param  CreateGroupRequest  $request
     * @return JsonResponse
     */
    public function create(CreateGroupRequest $request, $User) {
        /* if (!Auth::user()->hasRole('Admin') && !canMemberAddGroup()) {
          return $this->sendError('Sorry, you can not create group.');
          } */

        $input = $request->all();
        $input['group_type'] = ($input['group_type'] == '1') ? Group::TYPE_OPEN : Group::TYPE_CLOSE;
        $input['privacy'] = ($input['privacy'] == '1') ? Group::PRIVACY_PUBLIC : Group::PRIVACY_PRIVATE;
        $input['created_by'] = $User->id;

        $group = $this->groupRepository->store($input, $User);
        $group->append('group_created_by');

        return $this->sendResponse($group, 'Group has been created successfully.');
    }

    //Group $group, User $user, Request $request,  $action = 'listing'
    public function index4(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            $group_id = (!empty($request->group_id)) ? $request->group_id : 0;
            $group = Group::find($group_id);
            if (empty($group)) {
                return $this->sendError('Group Not Found');
            }

            $group_users = $group->users->pluck('id')->toArray();
            if (!in_array($User->id, $group_users)) {
                return $this->sendError('You are not member of the group.');
            }

            if (!$this->groupRepository->isAuthUserGroupAdmin($group->id, $User)) {
                return $this->sendError('Only admin user can make changes in the group.');
            }

            $req_user_id = (!empty($request->user_id)) ? $request->user_id : 0;
            $user = User::find($req_user_id);
            if (empty($user)) {
                return $this->sendError('User Not Found');
            }

            switch ($action) {
                case 'removeMembers': {
                        return $this->removeMembers($group, $user, $User);
                    }
                    break;

                case 'makeAdmin': {
                        return $this->makeAdmin($group, $user, $User);
                    }
                    break;

                case 'removeAdmin': {
                        return $this->removeAdmin($group, $user, $User);
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
     * @param  Group  $group
     * @param  User  $user
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function removeMembers(Group $group, User $user, $User) {
        $conversation = $this->groupRepository->removeMemberFromGroup($group, $user, $User);

        return $this->sendResponse($conversation, 'Member removed successfully.');
    }

    /**
     * @param  Group  $group
     * @param  User  $user
     * @return JsonResponse
     */
    public function makeAdmin(Group $group, User $user, $User) {
        $conversation = $this->groupRepository->makeMemberToGroupAdmin($group, $user, $User);

        return $this->sendResponse($conversation, $user->name . ' is now new admin.');
    }

    /**
     * @param  Group  $group
     * @param  User  $user
     * @return JsonResponse
     */
    public function removeAdmin(Group $group, User $user, $User) {
        $conversation = $this->groupRepository->dismissAsAdmin($group, $user, $User);

        return $this->sendResponse($conversation, $user->name . ' is dismissed from admin role successfully.');
    }

    //Group $group, Request $request,  $action = 'listing'
    public function index5(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            $group_id = (!empty($request->group_id)) ? $request->group_id : 0;
            $group = Group::find($group_id);
            if (empty($group)) {
                return $this->sendError('Group Not Found');
            }

            $group_users = $group->users->pluck('id')->toArray();
            if (!in_array($User->id, $group_users)) {
                return $this->sendError('You are not member of the group.');
            }

            switch ($action) {
                case 'leaveGroup': {
                        return $this->leaveGroup($group, $User);
                    }
                    break;

                case 'removeGroup': {
                        return $this->removeGroup($group, $User);
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
     * @param  Group  $group
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function leaveGroup(Group $group, $User) {
        $group->users;
        $conversation = $this->groupRepository->leaveGroup($group, $User->id, $User);

        return $this->sendResponse($conversation, 'You have successfully left this group.');
    }

    /**
     * @param  Group  $group
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function removeGroup(Group $group, $User) {
        if ($this->groupRepository->isAuthUserGroupAdmin($group->id, $User)) {
            $this->groupRepository->removeGroup($group, $User->id, $User);
            return $this->sendSuccess('You have successfully deleted this group.');
        }
        else {
            return $this->sendError('You do not have permission to delete this group.');
        }
    }

}
