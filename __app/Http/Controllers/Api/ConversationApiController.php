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
use App\Http\Requests\CreateUserStatusRequest;
use App\Http\Requests\UpdateUserNotificationRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\ArchivedUser;
use App\Models\BlockedUser;
use App\Models\Group;
use App\Exceptions\ApiOperationFailedException;
use App\Http\Requests\SendMessageRequest;
use App\Repositories\ChatRepository;
use App\Repositories\UserDeviceRepository;
use App\Models\UserDevice;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class ConversationAPIController
 */
class ConversationApiController extends BaseController {

    /** @var ChatRepository */
    private $chatRepository;
    private $userRepository;
    private $_group_id;

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
                case 'getLatestConversations': {
                        return $this->getLatestConversations($request, $User);
                    }
                    break;

                case 'userConversation': {
                        return $this->getConversation($request, $User);
                    }
                    break;

                case 'getArchiveConversations': {
                        return $this->getArchiveConversations($request, $User);
                    }
                    break;

                case 'acceptChatRequest': {
                        return $this->acceptChatRequest($request, $User);
                    }
                    break;

                case 'declineChatRequest': {
                        return $this->declineChatRequest($request, $User);
                    }
                    break;

                case 'updateConversationStatus': {
                        return $this->updateConversationStatus($request, $User);
                    }
                    break;

                case 'addAttachment': {
                        return $this->addAttachment($request, $User);
                    }
                    break;

                case 'imageUpload': {
                        return $this->imageUpload($request, $User);
                    }
                    break;

                case 'deleteConversation': {
                        return $this->deleteConversation($request, $User);
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
     * This function return latest conversations of users.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getLatestConversations(Request $request, $User) {
        $input = $request->all();
        $listing = strtolower((isset($request->listing_type)) ? $request->listing_type : 'both');
        if ($listing == 'group')
            $conversations = $this->chatRepository->getLatestGroupConversations($input, $User, $listing);
        else
            $conversations = $this->chatRepository->getLatestConversations($input, $User, $listing);

        return $this->sendResponse(['conversations' => $conversations], 'Conversations retrieved successfully.');
    }

    /**
     * This function return latest conversations of users.
     *
     * @return JsonResponse
     */
    public function getArchiveConversations(Request $request, $User) {
        $input = $request->all();
        $input['isArchived'] = 1;
        $conversations = $this->chatRepository->getLatestConversations($input, $User);

        return $this->sendResponse(['conversations' => $conversations], 'Conversations retrieved successfully.');
    }

    /**
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getConversation($request, $User) {
        $id = $request->user_id;
        $input = $request->all();
        $data = $this->userRepository->getConversation($id, $input, $User);

        return $this->sendResponse($data, 'Conversation retrieved successfully.');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function acceptChatRequest(Request $request, $User) {
        $chatRequestModel = ChatRequestModel::whereId($request->id)->first();
        $chatRequestModel->status = ChatRequestModel::STATUS_ACCEPTED;
        $chatRequestModel->save();

        $input = $chatRequestModel->toArray();
        $input['message'] = $chatRequestModel->receiver->name . ' has accepted your chat request.';
        $this->chatRepository->sendAcceptDeclineChatRequestNotification($input, User::CHAT_REQUEST_ACCEPTED, $User);

        return $this->sendResponse($chatRequestModel, 'Chat request accepted successfully.');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function declineChatRequest(Request $request, $User) {
        $chatRequestModel = ChatRequestModel::find($request->id);
        $chatRequestModel->status = ChatRequestModel::STATUS_DECLINE;
        $chatRequestModel->save();

        Conversation::whereFromId($chatRequestModel->from_id)->whereToId($chatRequestModel->owner_id)->update(['status' => 1]);

        $input = $chatRequestModel->toArray();
        $input['message'] = $chatRequestModel->receiver->name . ' has declined your chat request.';
        $this->chatRepository->sendAcceptDeclineChatRequestNotification($input, 0, $User);

        return $this->sendResponse($chatRequestModel, 'You have declined given user request !');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateConversationStatus(Request $request, $User) {
        $data = $this->chatRepository->markMessagesAsRead($request->all(), $User);

        return $this->sendResponse($data, 'Status updated successfully.');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws ApiOperationFailedException
     */
    public function addAttachment(Request $request, $User) {
        $files = $request->file('file');

        foreach ($files as $file) {
            $fileData['attachment'] = $this->chatRepository->addAttachment($file, $User);
            $extension = $file->getClientOriginalExtension();
            $fileData['message_type'] = $this->chatRepository->getMessageTypeByExtension($extension, $User);
            $fileData['file_name'] = $file->getClientOriginalName();
            $fileData['unique_code'] = uniqid();
            $data['data'][] = $fileData;
        }
        $data['success'] = true;

        return $this->sendData($data, 'Attachment done successfully');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws ApiOperationFailedException
     */
    public function imageUpload(Request $request, $User) {
        $input = $request->all();
        $images = $input['images'];
        unset($input['images']);
        $input['from_id'] = $User->id;
        $input['to_type'] = Conversation::class;
        $conversation = [];
        foreach ($images as $image) {
            $fileName = Conversation::uploadBase64Image($image, Conversation::PATH);
            $input['message'] = $fileName;
            $input['status'] = 0;
            $input['message_type'] = 1;
            $input['file_name'] = $fileName;
            $conversation[] = $this->chatRepository->sendMessage($input, $User);
        }

        return $this->sendResponse($conversation, 'File uploaded');
    }

    /**
     * @param  int|string  $id
     * @return JsonResponse
     */
    public function deleteConversation(Request $request, $User) {
        $id = (isset($request->user_id)) ? $request->user_id : $request->group_id;
        if (is_string($id) && !is_numeric($id)) {
            $this->chatRepository->deleteGroupConversation($id, $User);
        }
        else {
            $this->chatRepository->deleteConversation($id, $User);
        }

        return $this->sendSuccess('Conversation deleted successfully.');
    }

    public function index2(SendMessageRequest $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            switch ($action) {
                case 'sendChatRequest': {
                        return $this->sendChatRequest($request, $User);
                    }
                    break;

                case 'sendMessage': {
                        return $this->sendMessage($request, $User);
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
     * @param  SendMessageRequest  $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function sendChatRequest(SendMessageRequest $request, $User) {
        $isRequestSend = $this->chatRepository->sendChatRequest($request->all(), $User);
        if ($isRequestSend) {
            return $this->sendSuccess('Chat request send successfully.');
        }

        return $this->sendError('Chat request has already been sent.');
    }

    /**
     * @param  SendMessageRequest  $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function sendMessage(SendMessageRequest $request, $User) {
        $is_group = (!empty($request->is_group)) ? $request->is_group : 0;
        if ($is_group) {
            $group_id = (!empty($request->to_id)) ? $request->to_id : 0;
            $this->_group_id = $group_id;
            $group = Group::with('users')->whereHas('users', function (Builder $query) {
                        $query->where('user_id', $this->_User_Id);
                        $query->where('group_id', $this->_group_id);
                    })->orderBy('name')->first();
            if (empty($group)) {
                return $this->sendError('Group Not Found');
            }
        }
        $conversation = $this->chatRepository->sendMessage($request->all(), $User);

        return $this->sendResponse(['message' => $conversation], 'Message sent successfully.');
    }

    //Conversation $conversation, Request $request, $action = 'listing'
    public function index3(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            $conversation_id = (!empty($request->conversation_id)) ? $request->conversation_id : 0;
            $conversation = Conversation::find($conversation_id);
            if (empty($conversation)) {
                return $this->sendError('Conversation Not Found');
            }

            switch ($action) {
                case 'show': {
                        return $this->show($conversation, $request, $User);
                    }
                    break;

                case 'deleteMessage': {
                        return $this->deleteMessage($conversation, $request, $User);
                    }
                    break;

                case 'deleteMessageForEveryone': {
                        return $this->deleteMessageForEveryone($conversation, $request, $User);
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
     * @param  Conversation  $conversation
     * @return JsonResponse
     */
    public function show(Conversation $conversation, Request $request, $User) {
        return $this->sendResponse($conversation->toArray(), 'Conversation retrieved successfully');
    }

    /**
     * @param  Conversation  $conversation
     * @param  Request  $request
     * @return JsonResponse
     */
    public function deleteMessage(Conversation $conversation, Request $request, $User) {
        $deleteMessageTime = config('configurable.delete_message_time');
        if ($conversation->time_from_now_in_min > $deleteMessageTime) {
            return $this->sendError('You can not delete message older than ' . $deleteMessageTime . ' minutes.', 422);
        }

        if ($conversation->from_id != $this->_User_Id) {
            return $this->sendError('You can not delete this message.', 403);
        }

        $previousMessageId = $request->get('previousMessageId');
        $previousMessage = $this->chatRepository->find($previousMessageId);
        $this->chatRepository->deleteMessage($conversation->id, $User);

        return $this->sendResponse(['previousMessage' => $previousMessage], 'Message deleted successfully.');
    }

    /**
     * @param  Conversation  $conversation
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function deleteMessageForEveryone(Conversation $conversation, Request $request, $User) {
        $deleteMessageTime = config('configurable.delete_message_for_everyone_time');
        if ($conversation->time_from_now_in_min > $deleteMessageTime) {
            return $this->sendError('You can not delete message older than ' . $deleteMessageTime . ' minutes.', 422);
        }

        if ($conversation->from_id != $this->_User_Id) {
            return $this->sendError('You can not delete this message.', 403);
        }

        $conversation->delete();

        $previousMessageId = $request->get('previousMessageId');
        $previousMessage = $this->chatRepository->find($previousMessageId);
        unset($previousMessage->replayMessage);

        broadcast(new UserEvent(
                        [
                    'id'              => $conversation->id,
                    'type'            => User::MESSAGE_DELETED,
                    'from_id'         => $conversation->from_id,
                    'previousMessage' => $previousMessage,
                        ], $conversation->to_id))->toOthers();

        return $this->sendResponse(['previousMessage' => $previousMessage], 'Message deleted successfully.');
    }

}
