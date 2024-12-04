<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Illuminate\Broadcasting\BroadcastController as BroadcastWebApiController;
use App\Http\Controllers\Chat\API\UserAPIController as UserWebApiController;
use App\Http\Controllers\Chat\API\GroupAPIController as GroupWebApiController;
use App\Http\Controllers\Chat\API\ChatAPIController as ChatWebApiController;

use App\Http\Controllers\Api\UserApiController as UserController;
use App\Http\Controllers\Api\GroupApiController as GroupController;
use App\Http\Controllers\Api\ChatApiController as ChatController;
use App\Http\Controllers\Api\ConversationApiController as ConversationController;
use App\Http\Controllers\Api\GuestApiController as GuestController;
use App\Http\Controllers\Api\GeneralApiController as GeneralController;
use App\Http\Controllers\Api\SessionApiController as SessionController;
use App\Http\Controllers\Api\PaymentApiController as PaymentController;
use App\Http\Controllers\Api\FeedbackApiController as FeedbackController;
use App\Http\Controllers\Api\TrainingPlanApiController as TrainingPlanController;
use App\Http\Controllers\Api\TrainingProgramApiController as TrainingProgramController;
use App\Http\Controllers\Api\VideoApiController as VideoController;
use App\Http\Controllers\Api\ClubApiController as ClubController;
use App\Http\Controllers\Api\CoachApiController as CoachController;
use App\Http\Controllers\Api\PlayerApiController as PlayerController;
use App\Http\Controllers\Api\EventApiController as EventController;
use App\Http\Controllers\Api\TeamApiController as TeamController;
use App\Http\Controllers\Api\ParentApiController as ParentController;
use App\Http\Controllers\Api\ProfileApiController as ProfileController;

Route::post('groups/remove/{action}', [GroupController::class, 'index5']);
Route::post('groups/update/{action}', [GroupController::class, 'index4']);
Route::post('groups/newGroup/{action}', [GroupController::class, 'index3']);
Route::post('groups/group/{action}', [GroupController::class, 'index2']);
Route::post('groups/{action}', [GroupController::class, 'index']);

/** Chats * */
Route::post('chats/{action}', [ChatController::class, 'index']);
Route::post('conversations/action/{action}', [ConversationController::class, 'index3']);
Route::post('conversations/messages/{action}', [ConversationController::class, 'index2']);
Route::post('conversations/{action}', [ConversationController::class, 'index']);

// -------- Guest APIs ----------- //
Route::post('guest/{action}', [GuestController::class, 'index']);

// -------- General APIs ----------- //
Route::post('general/{action}', [GeneralController::class, 'index']);

// --------- User APIs ------------- //
Route::post('user/{action}', [UserController::class, 'index']);

// --------- Session APIs ------------- //
Route::post('session/{action}', [SessionController::class, 'index']);

// --------- Payment APIs ------------- //
Route::post('payment/{action}', [PaymentController::class, 'index']);

// --------- Feedback APIs ------------- //
Route::post('feedback/{action}', [FeedbackController::class, 'index']);

// --------- Training Plan APIs ------------- //
Route::post('training-plan/{action}', [TrainingPlanController::class, 'index']);

// --------- Training Plan APIs ------------- //
Route::post('training-program/{action}', [TrainingProgramController::class, 'index']);

// --------- video APIs ------------- //
//need  testing
Route::post('video/{action}', [VideoController::class, 'index']);

// --------- event APIs ------------- //
Route::post('event/{action}', [EventController::class, 'index']);

// --------- Club APIs ------------- //
Route::post('club/{action}', [ClubController::class, 'index']);

// --------- Profile APIs ------------- //
Route::post('profile/{action}', [ProfileController::class, 'index']);

// --------- Coach APIs ------------- //
Route::post('coach/{action}', [CoachController::class, 'index']);

// --------- Player APIs ------------- //
Route::post('player/{action}', [PlayerController::class, 'index']);

// --------- Teams APIs ------------- //
Route::post('team/{action}', [TeamController::class, 'index']);

// --------- Parent APIs ------------- //
Route::post('parent/{action}', [ParentController::class, 'index']);

// update user
/*
  Route::middleware('auth:sanctum')->post('/update-user', [AppUserController::class, 'updateUser']);

  Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
  });
 */

// --------- file upload & return URL ------------- //
//Route::post('/upload-file', [FileUploadController::class, 'store']);
// --------- validate user ------------- //
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth'])->group(function () {
    Route::post('broadcasting/auth', [BroadcastWebApiController::class, 'authenticate']);

    //get all user list for chat
    Route::get('users-list', [UserWebApiController::class, 'getUsersList']);
    Route::post('update-last-seen', [UserWebApiController::class, 'updateLastSeen']);

    Route::post('send-message', [ChatWebApiController::class, 'sendMessage'])->name('conversations.store');
    Route::get('users/{id}/conversation', [UserWebApiController::class, 'getConversation']);
    Route::get('conversations', [ChatWebApiController::class, 'getLatestConversations']);
    Route::post('read-message', [ChatWebApiController::class, 'updateConversationStatus']);
    Route::post('file-upload', [ChatWebApiController::class, 'addAttachment'])->name('file-upload');
    Route::get('conversations/{userId}/delete', [ChatWebApiController::class, 'deleteConversation']);

    /** Update Web-push */
    Route::put('update-web-notifications', [UserWebApiController::class, 'updateNotification']);

    /** create group **/
    Route::post('groups', [GroupWebApiController::class, 'create'])->name('create-group');
});

Route::any('{url?}/{sub_url?}', function () {
    return response()->json([
        'responseCode'  => '404',
        'responseState' => 'Error',
        'responseText'  => 'Invalid Request',
            ], 404);
});
