<?php

namespace App\Repositories;

use App\Events\GroupEvent;
use App\Events\UserEvent;
use App\Models\Conversation;
use App\Models\Group;
use App\Models\GroupMessageRecipient;
use App\Models\GroupUser;
use App\Models\LastConversation;
use App\Models\User;
use App\Traits\ImageTrait;
use Arr;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class GroupRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name', 'description', 'photo_url', 'group_type', 'privacy',
    ];
    protected $_AuthUser = NULL;

    public function setAuthUser($User) {
        $this->_AuthUser = $User;
    }

    public function getAuthUser() {
        return $this->_AuthUser;
    }

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable() {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     * */
    public function model() {
        return Group::class;
    }

    /**
     * @param  array  $search
     * @param  int|null  $skip
     * @param  int|null  $limit
     * @param  array  $columns
     * @return Group[]|Collection
     */
    public function all($User = NULL, $search = [], $skip = null, $limit = null, $columns = ['*']) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);

        $query = Group::with('users')->whereHas('users', function (Builder $query) {
            $query->where('user_id', $this->getAuthUser()->id);
        });

        return $query->orderBy('name')->get();
    }

    /**
     * @param  array  $input
     * @return Group
     */
    public function store($input, $User = NULL) {
        try {
            if ($User === NULL) {
                $User = Auth::user();
            }
            $this->setAuthUser($User);

            if (!empty($input['photo'])) {
                $input['photo_url'] = ImageTrait::makeImage($input['photo'], Group::$PATH);
            }

            /** @var Group $group */
            $group = Group::create($input);
            $group_id = $group->id;

            $users = $input['users'];
            $users[] = $this->getAuthUser()->id;
            $this->addMembersToGroup($group, $users, false, $User);

            $userIds = $group->fresh()->users->pluck('id')->toArray();
            $broadcastData = $this->prepareDataForMemberAddedToGroup($group, $User);
            broadcast(new UserEvent($broadcastData, $userIds))->toOthers();

            $msgInput = [
                'to_id'        => $group->id,
                'message'      => $this->getAuthUser()->name . ' created group "' . $group->name . '"',
                'is_group'     => true,
                'message_type' => Conversation::MESSAGE_TYPE_BADGES,
            ];
            $this->sendMessage($msgInput, $User);
            $group = Group::findOrFail($group_id);
            return $group;
        }
        catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return array
     */
    public function update($input, $id, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);

        $conversation = null;
        /** @var Group $group */
        $group = Group::findOrFail($id);

        try {
            if (!empty($input['photo'])) {
                $input['photo_url'] = ImageTrait::makeImage($input['photo'], Group::$PATH);
            }

            unset($input['created_by']);
            $group->update($input);
            $changes = $group->getChanges();
            if (!empty($changes)) {
                $msgInput = [
                    'to_id'        => $group->id,
                    'message'      => 'Group details updated by ' . $this->getAuthUser()->name,
                    'is_group'     => true,
                    'message_type' => Conversation::MESSAGE_TYPE_BADGES,
                ];
                $conversation = $this->sendMessage($msgInput, $User);
            }

            if (!empty($input['users'])) {
                $this->addMembersToGroup($group, $input['users'], $User);
            }

            $broadcastData = $this->prepareDataBroadcastWhenGroupUpdated(['group' => $group]);
            broadcast(new GroupEvent($broadcastData))->toOthers();

            return [$group, $conversation];
        }
        catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  Group  $group
     * @param  array  $users
     * @param  bool  $fireEvent
     * @return array|void
     *
     * @throws Exception
     */
    public function addMembersToGroup($group, $users, $fireEvent = true, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        else {
            $users = explode(',', implode(',', $users));
        }
        $this->setAuthUser($User);

        $groupUsers = $group->users->pluck('id')->toArray();
        $newAddedUsers = [];
        $newUserNames = '';
        $users = array_unique($users);

        $userRecords = User::whereIn('id', $users)->get()->keyBy('id');
        GroupUser::withTrashed()->whereIn('user_id', $users)->where('group_id', $group->id)->forceDelete();
        LastConversation::whereIn('user_id', $users)->where('group_id', $group->id)->delete();

        foreach ($users as $userId) {
            if (in_array($userId, $groupUsers)) { // if already in group
                continue;
            }

            if (!isset($userRecords[$userId])) {
                continue;
            }

            /** @var User $user */
            $user = $userRecords[$userId];
            $newAddedUsers[] = $user->toArray();
            $newUserNames .= $user->name . ', ';

            GroupUser::create([
                'user_id'  => $user->id,
                'group_id' => $group->id,
                'added_by' => $this->getAuthUser()->id,
                'role'     => $group->created_by == $user->id ? GroupUser::ROLE_ADMIN : GroupUser::ROLE_MEMBER,
            ]);

            if ($fireEvent) {
                $broadCastData = $this->prepareDataForMemberAddedToGroup($group, $User);
                broadcast(new UserEvent($broadCastData, $user->id))->toOthers();
            }
        }

        if (!$fireEvent) {
            return;
        }

        $newUserNames = substr($newUserNames, 0, strlen($newUserNames) - 2);
        $msgInput = [
            'to_id'        => $group->id,
            'message'      => "$newUserNames added to Group", //$this->getAuthUser()->name." added : $newUserNames",
            'is_group'     => true,
            'message_type' => Conversation::MESSAGE_TYPE_BADGES,
            'add_members'  => true,
        ];
        $conversation = $this->sendMessage($msgInput, $User);

        $broadcastData = $this->prepareDataBroadcastWhenGroupUpdated(
                ['group' => $group, 'users' => $newAddedUsers], Group::GROUP_NEW_MEMBERS_ADDED
        );
        broadcast(new GroupEvent($broadcastData))->toOthers();

        return [$newAddedUsers, $conversation];
    }

    /**
     * @param  Group  $group
     * @return mixed
     */
    public function prepareDataForMemberAddedToGroup($group, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);

        $groupArr = $group->toArray();
        $groupArr['group_created_by'] = $group->group_created_by;
        $groupArr['type'] = User::ADDED_TO_GROUP;
        unset($groupArr['users']);
        unset($groupArr['created_by_user']);
        unset($groupArr['users_with_trashed']);

        return $groupArr;
    }

    /**
     * @param  Group  $group
     * @param  User  $user
     * @return Conversation
     *
     * @throws Exception
     */
    public function removeMemberFromGroup($group, $user, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);
        /** @var User $authUser */
        $authUser = $this->getAuthUser();
        $groupUser = GroupUser::whereGroupId($group->id)->whereUserId($user->id);

        if ($this->getAuthUser()->id != $group->created_by && $user->id == $group->created_by) {
            throw new UnprocessableEntityHttpException('You can not remove group owner.');
        }

        $message = $authUser->name . " removed $user->name.";

        $msgInput = [
            'to_id'        => $group->id,
            'message'      => $message,
            'is_group'     => true,
            'message_type' => Conversation::MESSAGE_TYPE_BADGES,
        ];
        $conversation = $this->sendMessage($msgInput, $User);

        $broadcastData = $this->prepareDataBroadcastWhenGroupUpdated(
                ['group' => $group, 'user_id' => $user->id], Group::GROUP_MEMBER_REMOVED
        );
        broadcast(new GroupEvent($broadcastData))->toOthers();

        $groupUser->update(['removed_by' => $this->getAuthUser()->id, 'deleted_at' => Carbon::now()]);

        // Store last group details info when user leave the group
        $groupDetails = $group->toArray();
        $groupDetails['removed_from_group'] = true;
        $groupDetails['users'] = $group->fresh()->users->toArray();
        LastConversation::create([
            'conversation_id' => $conversation->id, 'group_id'        => $group->id, 'user_id'         => $user->id,
            'group_details'   => $groupDetails,
        ]);

        return $conversation;
    }

    /**
     * @param  array  $groupUsers
     * @param  int  $messageId
     * @param  int  $groupId
     * @return bool
     */
    public function addRecordsToGroupMessageRecipients($groupUsers, $messageId, $groupId, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);

        $users = Arr::except($groupUsers, $this->getAuthUser()->id);

        $inputs = [];
        foreach ($users as $userId) {
            $inputs[] = [
                'user_id'         => $userId,
                'conversation_id' => $messageId,
                'group_id'        => $groupId,
            ];
        }

        GroupMessageRecipient::insert($inputs);

        return true;
    }

    /**
     * @param  Group  $group
     * @param  int  $userId
     * @return Conversation
     *
     * @throws Exception
     */
    public function leaveGroup($group, $userId, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);

        $msgInput = [
            'to_id'        => $group->id,
            'message'      => $this->getAuthUser()->name . ' left the group',
            'is_group'     => true,
            'message_type' => Conversation::MESSAGE_TYPE_BADGES,
        ];

        $conversation = $this->sendMessage($msgInput, $User);
        GroupUser::whereGroupId($group->id)->whereUserId($userId)->delete();

        // Store last group details info when user leave the group
        $group->append('group_created_by');
        $groupDetails = $group->toArray();
        $groupDetails['removed_from_group'] = true;
        $groupDetails['users'] = $group->fresh()->users->toArray();
        LastConversation::create([
            'conversation_id' => $conversation->id, 'group_id'        => $group->id, 'user_id'         => $userId,
            'group_details'   => $groupDetails,
        ]);

        $broadcastData = $this->prepareDataBroadcastWhenGroupUpdated(
                ['group' => $group, 'user_id' => $userId], Group::GROUP_MEMBER_REMOVED
        );
        broadcast(new GroupEvent($broadcastData))->toOthers();

        return $conversation;
    }

    /**
     * @param  Group  $group
     * @param  int  $userId
     * @return bool
     *
     * @throws Exception
     */
    public function removeGroup($group, $userId, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);

        if ($group->created_by != $userId) {
            GroupUser::whereGroupId($group->id)->whereUserId($userId)->forceDelete();

            return true;
        }

        $msgInput = [
            'to_id'        => $group->id,
            'message'      => $this->getAuthUser()->name . ' deleted this group',
            'is_group'     => true,
            'message_type' => Conversation::MESSAGE_TYPE_BADGES,
        ];
        $conversation = $this->sendMessage($msgInput, $User);

        // broadcast event for all group members
        $broadcastData = $this->prepareDataBroadcastWhenGroupUpdated(
                ['group' => $group], Group::GROUP_DELETED_BY_OWNER
        );
        broadcast(new GroupEvent($broadcastData))->toOthers();

        $userIds = $group->users->pluck('id', 'id')->except($group->created_by)->toArray();
        // All members of group should leaved the group
        GroupUser::whereGroupId($group->id)->whereIn('user_id', $userIds)->delete();
        // Group deleted for owner of group
        GroupUser::whereGroupId($group->id)->where('user_id', $group->created_by)->forceDelete();

        // Store last group details info when user leave the group
        $group->append('');
        $groupDetails = $group->toArray();
        $groupDetails['group_created_by'] = $group->group_created_by;
        $groupDetails['removed_from_group'] = true;
        $groupDetails['group_deleted_by_owner'] = true;
        $groupDetails['users'] = $group->fresh()->users->toArray();
        foreach ($group->users as $user) {
            LastConversation::create([
                'conversation_id' => $conversation->id, 'group_id'        => $group->id, 'user_id'         => $user->id,
                'group_details'   => $groupDetails,
            ]);
        }

        return true;
    }

    /**
     * @param  Group  $group
     * @param  User  $member
     * @return Conversation
     */
    public function makeMemberToGroupAdmin($group, $member, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);

        $memberIds = $group->users->pluck('id', 'id')->except($group->created_by)->toArray();

        $this->assignRole($group->id, $member->id, GroupUser::ROLE_ADMIN);

        $broadcastData = $this->prepareDataBroadcastWhenGroupUpdated(
                [
                    'group'    => $group, 'user_id'  => $member->id, 'is_admin' => true, 'userIds'  => $memberIds,
                ], Group::GROUP_MEMBER_ROLE_UPDATED
        );
        broadcast(new GroupEvent($broadcastData))->toOthers();

        $msgInput = [
            'to_id'        => $group->id,
            'message'      => $member->name . " made Admin", //$this->getAuthUser()->name.' assigned admin role to '.$member->name,
            'is_group'     => true,
            'message_type' => Conversation::MESSAGE_TYPE_BADGES,
        ];

        return $this->sendMessage($msgInput, $User);
    }

    /**
     * @param  Group  $group
     * @param  User  $member
     * @return Conversation
     */
    public function dismissAsAdmin($group, $member, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);

        if ($group->created_by == $member->id) {
            throw new UnprocessableEntityHttpException('You can not change group owner role.');
        }

        $memberIds = $group->users->pluck('id', 'id')->except($group->created_by)->toArray();
        $this->assignRole($group->id, $member->id, GroupUser::ROLE_MEMBER);

        $broadcastData = $this->prepareDataBroadcastWhenGroupUpdated(
                [
                    'group'    => $group, 'user_id'  => $member->id, 'is_admin' => false, 'userIds'  => $memberIds,
                ], Group::GROUP_MEMBER_ROLE_UPDATED
        );
        broadcast(new GroupEvent($broadcastData))->toOthers();

        $msgInput = [
            'to_id'        => $group->id,
            'message'      => $member->name . " removed as admin", //$this->getAuthUser()->name." dismissed $member->name from admin",
            'is_group'     => true,
            'message_type' => Conversation::MESSAGE_TYPE_BADGES,
        ];

        return $this->sendMessage($msgInput, $User);
    }

    /**
     * @param  array  $input
     * @return Conversation
     */
    public function sendMessage($input, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);

        /** @var ChatRepository $chatRepo */
        $chatRepo = app(ChatRepository::class);
        $conversation = $chatRepo->sendGroupMessage($input, $User);

        return $conversation;
    }

    /**
     * @param  int  $groupId
     * @param  int  $memberId
     * @param  int  $role
     * @return bool
     */
    public function assignRole($groupId, $memberId, $role) {
        GroupUser::whereGroupId($groupId)->whereUserId($memberId)->update(['role' => $role]);

        return true;
    }

    /**
     * @param  string  $groupId
     * @return bool
     */
    public function isAuthUserGroupAdmin($groupId, $User = NULL) {
        if ($User === NULL) {
            $User = Auth::user();
        }
        $this->setAuthUser($User);
        /** @var GroupUser $groupUser */
        $groupUser = GroupUser::whereGroupId($groupId)->whereUserId($this->getAuthUser()->id)->first();

        return ($groupUser->role === GroupUser::ROLE_ADMIN) ? true : false;
    }

    /**
     * @param  array  $data
     * @param  int  $type
     * @return mixed
     */
    public function prepareDataBroadcastWhenGroupUpdated($data, $type = Group::GROUP_DETAILS_UPDATED) {
        $result['group'] = $data['group']->toArray();
        unset($result['group']['users_with_trashed']);
        $result['type'] = $type;

        if (isset($data['user_id'])) {
            $result['user_id'] = $data['user_id'];
        }

        if (isset($data['is_admin'])) {
            $result['is_admin'] = $data['is_admin'];
        }

        if (isset($data['userIds'])) {
            $result['userIds'] = $data['userIds'];
        }

        $users = [];
        if (isset($data['users'])) {
            $users = $this->prepareUsersData($data['users']);
        }

        $result['group']['users'] = $users;

        return $result;
    }

    public function getGroupMembersIds($group) {
        return $group->users->pluck('id')->toArray();
    }

    /**
     * @param  array  $users
     * @return array
     */
    public function prepareUsersData($users) {
        $result = [];
        foreach ($users as $user) {
            $data['id'] = $user['id'];
            $data['name'] = $user['name'];
            $data['email'] = $user['email'];
            $data['photo_url'] = $user['photo_url'];

            $result[] = $data;
        }

        return $result;
    }

}
