<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Country;
use App\Http\Controllers\Auth\RegisteredUserController as RegisteredUserController;
use App\Http\Controllers\Auth\SocialLoginController as SocialLoginController;
use App\Http\Controllers\Backend\HomeController as HomeController;
use App\Http\Controllers\Backend\Setups\CategoryController as CategoryController;
use App\Http\Controllers\Backend\Setups\GeneralController as GeneralSettingController;
use App\Http\Controllers\Backend\setups\TraningController;
use App\Http\Controllers\Backend\ModuleController as ModuleController;
use App\Http\Controllers\Backend\RoleController as RoleController;
use App\Http\Controllers\Backend\User\UserController as UserController;
use App\Http\Controllers\Backend\User\ProfileController as ProfileController;
use App\Http\Controllers\Backend\SubscriberController as SubscriberController;
use App\Http\Controllers\Backend\ContactRequestController as ContactRequestController;
use App\Http\Controllers\Backend\ClubController as ClubController;
use App\Http\Controllers\Backend\CoachController as CoachController;
use App\Http\Controllers\AjaxController as AjaxController;
use App\Http\Controllers\Backend\ParentController as ParentController;
use App\Http\Controllers\Backend\SessionTypeController as SessionTypeController;
use App\Http\Controllers\Backend\SessionController as SessionController;
use App\Http\Controllers\Backend\BookingController as BookingController;
use App\Http\Controllers\Backend\PaymentController as PaymentController;
use App\Http\Controllers\Backend\FeedbackController as FeedbackController;
use App\Http\Controllers\Backend\EventController as EventController;
use App\Http\Controllers\Backend\VideoController as VideoController;
use App\Http\Controllers\Backend\TrainingPlanController as TrainingPlanController;
use App\Http\Controllers\Backend\TrainingProgramController as TrainingProgramController;
use App\Http\Controllers\Backend\TeamController as TeamController;
use App\Http\Controllers\Frontend\FrontendHomeController as FrontHomeController;
use App\Http\Controllers\Frontend\ProfileController as FrontProfileController;
use App\Http\Controllers\Frontend\SessionController as FrontSessionController;
use App\Http\Controllers\Frontend\PaymentController as FrontPaymentController;
use App\Http\Controllers\Frontend\EventController as FrontEventController;

use App\Http\Controllers\Chat\API;
use App\Http\Controllers\Chat\ChatController;

require __DIR__ . '/auth.php';

Route::get('social-auth/{provider}/callback', [SocialLoginController::class, 'providerCallback']);
Route::get('social-auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('social.redirect');

Route::middleware(['auth'])->prefix('chat')->group(function () {
    //view routes
    Route::get('/conversations',
            [ChatController::class, 'index'])->name('conversations');

    //get all user list for chat
    Route::get('users-list', [API\UserAPIController::class, 'getUsersList']);
    Route::get('get-users', [API\UserAPIController::class, 'getUsers'])->name('get-users')->name('get-users');
    Route::delete('remove-profile-image',
            [API\UserAPIController::class, 'removeProfileImage'])->name('remove-profile-image');
    /** Change password */
    Route::post('change-password', [API\UserAPIController::class, 'changePassword'])->name('change-password');
    Route::get('conversations/{ownerId}/archive-chat', [API\UserAPIController::class, 'archiveChat'])->name('conversations.archive-chat');
    Route::get('conversations/{ownerId}/un-archive-chat', [API\UserAPIController::class, 'unArchiveChat'])->name('conversations.un-archive-chat');

    Route::get('get-profile', [API\UserAPIController::class, 'getProfile']);
    Route::post('profile', [API\UserAPIController::class, 'updateProfile'])->name('update.profile');
    Route::post('update-last-seen', [API\UserAPIController::class, 'updateLastSeen'])->name('update-last-seen');

    Route::post('send-message',
            [API\ChatAPIController::class, 'sendMessage'])->name('conversations.store')->middleware('sendMessage');
    Route::get('users/{id}/conversation', [API\UserAPIController::class, 'getConversation'])->name('users.conversation');
    Route::get('conversations-list', [API\ChatAPIController::class, 'getLatestConversations'])->name('conversations-list');
    Route::get('archive-conversations', [API\ChatAPIController::class, 'getArchiveConversations'])->name('archive-conversations');
    Route::post('read-message', [API\ChatAPIController::class, 'updateConversationStatus'])->name('read-message');
    Route::post('file-upload', [API\ChatAPIController::class, 'addAttachment'])->name('file-upload');
    Route::post('image-upload', [API\ChatAPIController::class, 'imageUpload'])->name('image-upload');
    Route::get('conversations/{userId}/delete', [API\ChatAPIController::class, 'deleteConversation'])->name('conversations.destroy');
    Route::post('conversations/message/{conversation}/delete', [API\ChatAPIController::class, 'deleteMessage'])->name('conversations.message-conversation.delete');
    Route::post('conversations/{conversation}/delete', [API\ChatAPIController::class, 'deleteMessageForEveryone']);
    Route::get('/conversations/{conversation}', [API\ChatAPIController::class, 'show']);
    Route::post('send-chat-request', [API\ChatAPIController::class, 'sendChatRequest'])->name('send-chat-request');
    Route::post('accept-chat-request',
            [API\ChatAPIController::class, 'acceptChatRequest'])->name('accept-chat-request');
    Route::post('decline-chat-request',
            [API\ChatAPIController::class, 'declineChatRequest'])->name('decline-chat-request');

    /** Web Notifications */
    Route::put('update-web-notifications', [API\UserAPIController::class, 'updateNotification'])->name('update-web-notifications');

    /** BLock-Unblock User */
    Route::put('users/{user}/block-unblock', [API\BlockUserAPIController::class, 'blockUnblockUser'])->name('users.block-unblock');
    Route::get('blocked-users', [API\BlockUserAPIController::class, 'blockedUsers']);

    /** My Contacts */
    Route::get('my-contacts', [API\UserAPIController::class, 'myContacts'])->name('my-contacts');

    /** Groups API */
    Route::post('groups', [API\GroupAPIController::class, 'create'])->name('groups.create');
    Route::post('groups/{group}', [API\GroupAPIController::class, 'update'])->name('groups.update');
    Route::get('groups', [API\GroupAPIController::class, 'index'])->name('groups.index');
    Route::get('groups/{group}', [API\GroupAPIController::class, 'show'])->name('group.show');
    Route::put('groups/{group}/add-members', [API\GroupAPIController::class, 'addMembers'])->name('groups-group.add-members');
    Route::delete('groups/{group}/members/{user}', [API\GroupAPIController::class, 'removeMemberFromGroup'])->name('group-from-member-remove');
    Route::delete('groups/{group}/leave', [API\GroupAPIController::class, 'leaveGroup'])->name('groups.leave');
    Route::delete('groups/{group}/remove', [API\GroupAPIController::class, 'removeGroup'])->name('group-remove');
    Route::put('groups/{group}/members/{user}/make-admin', [API\GroupAPIController::class, 'makeAdmin'])->name('groups.members.make-admin');
    Route::put('groups/{group}/members/{user}/dismiss-as-admin', [API\GroupAPIController::class, 'dismissAsAdmin'])->name('groups.members.dismiss-as-admin');
    Route::get('users-blocked-by-me', [API\BlockUserAPIController::class, 'blockUsersByMe']);

    Route::get('notification/{notification}/read', [API\NotificationController::class, 'readNotification'])->name('notification.read-notification');
    Route::get('notification/read-all', [API\NotificationController::class, 'readAllNotification'])->name('read-all-notification');

    Route::put('update-player-id', [API\UserAPIController::class, 'updatePlayerId'])->name('update-player-id');
    //set user custom status route
    Route::post('set-user-status', [API\UserAPIController::class, 'setUserCustomStatus'])->name('set-user-status');
    Route::get('clear-user-status', [API\UserAPIController::class, 'clearUserCustomStatus'])->name('clear-user-status');

    //report user
    Route::post('report-user', [API\ReportUserController::class, 'store'])->name('report-user.store');
});

Route::middleware(['auth'])->prefix('manage')->group(function () {

    Route::get('/cache-clear', function () {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        return redirect()->route('dashboard');
    })->name('cacheClear');

    //Dashboard
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    //Availability
    Route::get('/availability', [CoachController::class, 'availability'])->name('availability');
    Route::get('/delete-availability/{id}', [CoachController::class, 'delete_availability'])->name('delete_availability');

    // Dashboard Admin
    {
        Route::get('/dashboard/admin/clubs/approval/datatable', [UserController::class, 'club_approve_datatable'])->name('dashboard.club_approve_datatable');

        Route::get('/dashboard/admin/sessions/all/datatable', [HomeController::class, 'admin_all_sessions_datatable'])->name('dashboard.admin_all_sessions_datatable');
    }

    //Dashboard Coach
    {
        Route::get('/dashboard/coach/sessions/upcomming/datatable', [HomeController::class, 'coach_upc_sessions_datatable'])->name('dashboard.coach_upc_sessions_datatable');

        Route::get('/dashboard/coach/sessions/all/datatable', [HomeController::class, 'coach_all_sessions_datatable'])->name('dashboard.coach_all_sessions_datatable');
    }

    //Dashboard Club
    {
        Route::get('clubs/member/coaches', [ClubController::class, 'member_coaches'])->name('clubs.member_coaches');
        Route::get('clubs/member/players', [ClubController::class, 'member_players'])->name('clubs.member_players');

        Route::get('/dashboard/club/requests/all/datatable', [HomeController::class, 'club_all_requests_databale'])->name('dashboard.club_all_requests_databale');

        Route::get('clubs/requests/{request_id}/approve', [ClubController::class, 'approve'])->name('clubs.request_approve');

        Route::get('clubs/requests/{request_id}/reject', [ClubController::class, 'reject'])->name('clubs.request_reject');

        Route::get('clubs/requests/{user_id}/remove', [ClubController::class, 'remove'])->name('clubs.request_remove');
    }
    //Dashboard Player
    {
        Route::get('/dashboard/player/sessions/upcomming/datatable', [HomeController::class, 'player_upc_sessions_datatable'])->name('dashboard.player_upc_sessions_datatable');

        Route::get('/dashboard/player/sessions/all/datatable', [HomeController::class, 'player_all_sessions_datatable'])->name('dashboard.player_all_sessions_datatable');
    }

    //Dashbaord Parent
    {
        Route::get('parent/index', [ParentController::class, 'index'])->name('parents.index');

        Route::get('parent/search', [ParentController::class, 'search_datatable'])->name('parents.search_datatable');

        Route::get('parent/history', [ParentController::class, 'history_index'])->name('parents.history_index');

        Route::get('parent/history/datables', [ParentController::class, 'history_datatables'])->name('parents.history_datatables');

        Route::get('parent/member/players', [ParentController::class, 'member_player'])->name('parent.member_player');

        Route::get('player/member/parent', [ParentController::class, 'member_parent'])->name('player.member_parent');

        Route::get('parent/requests/{request_id}/invite', [ParentController::class, 'invite'])->name('parent.invite');

        Route::get('parent/requests/{request_id}/remove', [ParentController::class, 'remove'])->name('parent.remove');

        Route::get('player/association/history/{user_id}/approve', [ParentController::class, 'approve'])->name('player.approve');

        Route::get('player/association/history{user_id}/reject', [ParentController::class, 'reject'])->name('player.deactivate');
    }

    // file delete
    {
        Route::post('file-delete/training-programs/{id}/{type}', [TrainingProgramController::class, 'file_delete'])->name('training-programs.file_delete');

        Route::post('file-delete/videos/{id}/{type}', [VideoController::class, 'file_delete'])->name('videos.file_delete');

        Route::post('file-delete/events/{id}/{type}', [EventController::class, 'file_delete'])->name('events.file_delete');

        Route::post('file-delete/teams/{id}/{type}', [TeamController::class, 'file_delete'])->name('teams.file_delete');

        Route::post('file-delete/users/{id}/{type}', [UserController::class, 'file_delete'])->name('users.file_delete');
    }

    //session-types
    {
        Route::get('session-types/datatable', [SessionTypeController::class, 'datatable'])->name('session-types.datatable');

        Route::get('session-types/deactivate/{id}', [SessionTypeController::class, 'makeInActive'])->name('session-types.deactivate');

        Route::get('session-types/activate/{id}', [SessionTypeController::class, 'makeActive'])->name('session-types.activate');

        Route::resource('session-types', SessionTypeController::class);
    }

    //sessions
    {
        Route::get('sessions/history/datatable', [SessionController::class, 'historyDatatable'])->name('sessions.history.datatable');

        Route::get('sessions/history', [SessionController::class, 'history'])->name('sessions.history');

        Route::get('sessions/upcoming', [SessionController::class, 'upcoming'])->name('sessions.upcoming');

        Route::get('sessions/upcoming/datatable', [SessionController::class, 'upcomingdatatable'])->name('sessions.upcoming.datatable');

        Route::get('sessions/deactivate/{id}', [SessionController::class, 'makeInActive'])->name('sessions.deactivate');

        Route::get('sessions/activate/{id}', [SessionController::class, 'makeActive'])->name('sessions.activate');

        // Route::resource('sessions', SessionController::class);
    }

    //bookings
    {
        Route::get('bookings/datatable/history', [BookingController::class, 'historyDatatable'])->name('bookings.history.datatable');

        Route::get('bookings/datatable/upcoming', [BookingController::class, 'datatable'])->name('bookings.upcoming.datatable');

        Route::get('bookings/upcoming', [BookingController::class, 'upcoming'])->name('bookings.upcoming');

        Route::get('bookings/history', [BookingController::class, 'history'])->name('bookings.histroy');

        Route::get('bookings/datatable', [BookingController::class, 'datatable'])->name('bookings.datatable');

        Route::get('bookings/deactivate/{id}', [BookingController::class, 'makeInActive'])->name('bookings.deactivate');

        Route::get('bookings/activate/{id}', [BookingController::class, 'makeActive'])->name('bookings.activate');

        // Route::resource('bookings', BookingController::class);
    }

    //payments
    {

        // Route::get('payments/checkout', [PaymentController::class, 'index'])->name('payments.checkout');
        Route::get('payments/pending', [PaymentController::class, 'index'])->name('payments.pending');

        Route::get('payments/datatable/pending', [PaymentController::class, 'pendingdatatable'])->name('payments.pending.datatable');

        Route::get('payments/history', [PaymentController::class, 'index'])->name('payments.histroy');

        Route::get('payments/datatable/history', [PaymentController::class, 'historydatatable'])->name('payments.history.datatable');

        Route::get('payments/deactivate/{id}', [PaymentController::class, 'makeInActive'])->name('payments.deactivate');

        Route::get('payments/activate/{id}', [PaymentController::class, 'makeActive'])->name('payments.activate');

        Route::resource('payments', PaymentController::class);
    }

    //feedbacks
    {
        Route::get('feedbacks/datatable', [FeedbackController::class, 'datatable'])->name('feedbacks.datatable');

        Route::get('feedbacks/deactivate/{id}', [FeedbackController::class, 'makeInActive'])->name('feedbacks.deactivate');

        Route::get('feedbacks/activate/{id}', [FeedbackController::class, 'makeActive'])->name('feedbacks.activate');

        Route::resource('feedbacks', FeedbackController::class);
    }

    //training-plans
    {

        Route::get('training-plans/datatable', [TrainingPlanController::class, 'datatable'])->name('training-plans.datatables');

        Route::get('training-plans/deactivate/{id}', [TrainingPlanController::class, 'makeInActive'])->name('training-plans.deactivate');

        Route::get('training-plans/activate/{id}', [TrainingPlanController::class, 'makeActive'])->name('training-plans.activate');

        Route::resource('training-plans', TrainingPlanController::class);
    }

    //training-program
    {
        Route::get('training-programs/datatable', [TrainingProgramController::class, 'datatable'])->name('training-programs.datatables');

        Route::get('training-programs/deactivate/{id}', [TrainingProgramController::class, 'makeInActive'])->name('training-programs.deactivate');

        Route::get('training-programs/activate/{id}', [TrainingProgramController::class, 'makeActive'])->name('training-programs.activate');

        Route::resource('training-programs', TrainingProgramController::class);
    }

    //videos
    {
        Route::get('videos/datatable', [VideoController::class, 'datatable'])->name('videos.datatable');

        Route::get('videos/deactivate/{id}', [VideoController::class, 'makeInActive'])->name('videos.deactivate');

        Route::get('videos/activate/{id}', [VideoController::class, 'makeActive'])->name('videos.activate');

        Route::resource('videos', VideoController::class);
    }

    //events
    {
        Route::get('events/datatable', [EventController::class, 'datatable'])->name('events.datatable');

        Route::get('events/deactivate/{id}', [EventController::class, 'makeInActive'])->name('events.deactivate');

        Route::get('events/activate/{id}', [EventController::class, 'makeActive'])->name('events.activate');

        Route::resource('events', EventController::class);
    }

    //teams
    {

        Route::get('teams/remove/coach/{team_id}/{user_id}', [TeamController::class, 'remove_coach'])->name('teams.remove_coach');

        Route::get('teams/remove/player/{team_id}/{user_id}', [TeamController::class, 'remove_player'])->name('teams.remove_player');

        Route::get('teams/datatable', [TeamController::class, 'datatable'])->name('teams.datatable');

        Route::get('teams/deactivate/{id}', [TeamController::class, 'makeInActive'])->name('teams.deactivate');

        Route::get('teams/activate/{id}', [TeamController::class, 'makeActive'])->name('teams.activate');

        Route::resource('teams', TeamController::class);
    }

    //Categories
    {
        Route::get('categories/datatable', [CategoryController::class, 'datatable'])->name('categories.datatable');

        Route::get('categories/deactivate/{id}', [CategoryController::class, 'makeInActive'])->name('categories.deactivate');

        Route::get('categories/activate/{id}', [CategoryController::class, 'makeActive'])->name('categories.activate');

        Route::resource('categories', CategoryController::class);
    }

    //General
    {
        Route::resource('general', GeneralSettingController::class);
    }

    // Profile
    {
        Route::get('users/profile', [ProfileController::class, 'show'])->name('users.profile');
        //Route::post('/update', [ProfileController::class, 'update'])->name('users.update');

        Route::post('users/upload-document', [ProfileController::class, 'upload_document'])->name('users.upload_document');
        Route::post('users/update-social', [ProfileController::class, 'update_social'])->name('users.update_social');

        Route::post('users/add-skill', [ProfileController::class, 'add_skill'])->name('users.add_skill');
        Route::post('users/add-education', [ProfileController::class, 'add_education'])->name('users.add_education');
        Route::post('users/add-experience', [ProfileController::class, 'add_experience'])->name('users.add_experience');
        Route::post('users/add-training', [ProfileController::class, 'add_training'])->name('users.add_training');
        Route::post('/add-certification', [ProfileController::class, 'add_certification'])->name('users.add_certification');

        Route::post('users/update-education', [ProfileController::class, 'update_education'])->name('users.update_education');
        Route::post('users/update-experience', [ProfileController::class, 'update_experience'])->name('users.update_experience');
        Route::post('users/update-training', [ProfileController::class, 'update_training'])->name('users.update_training');
        Route::post('users/update-certification', [ProfileController::class, 'update_certification'])->name('users.update_certification');

        Route::post('users/remove-document', [ProfileController::class, 'remove_document'])->name('users.remove_document');
        Route::post('users/remove-skill', [ProfileController::class, 'remove_skill'])->name('users.remove_skill');
        Route::post('users/remove-educations', [ProfileController::class, 'remove_education'])->name('users.remove_education');
        Route::post('users/remove-experience', [ProfileController::class, 'remove_experience'])->name('users.remove_experience');
        Route::post('users/remove-training', [ProfileController::class, 'remove_training'])->name('users.remove_training');
        Route::post('users/remove-certification', [ProfileController::class, 'remove_certification'])->name('users.remove_certification');

        Route::get('users/remove-cover-image', [ProfileController::class, 'remove_cover_image'])->name('users.remove_cover_image');
        Route::get('users/remove-profile-image', [ProfileController::class, 'remove_profile_image'])->name('users.remove_profile_image');
    }

    //clubs
    {
        
    }

    //coaches
    {
        
    }

    //players
    {
        
    }

    //parents
    {
        
    }



    //Modules
    {
        Route::get('modules/datatable', [ModuleController::class, 'datatable'])->name('modules.datatable');

        Route::resource('modules', ModuleController::class);
    }

    //Roles
    {
        Route::get('roles/datatable', [RoleController::class, 'datatable'])->name('roles.datatable');

        Route::post('roles/permissions/{id}', [RoleController::class, 'permission_update'])->name('permissions_update');

        Route::resource('roles', RoleController::class);
    }

    // Users
    {
        Route::get('users/clubs/{user_type}', [UserController::class, 'index'])->name('users.clubs_listing');
        Route::get('users/coaches/{user_type}', [UserController::class, 'index'])->name('users.coaches_listing');
        Route::get('users/players/{user_type}', [UserController::class, 'index'])->name('users.players_listing');
        Route::get('users/parents/{user_type}', [UserController::class, 'index'])->name('users.parents_listing');
        Route::get('users/admins/{user_type}', [UserController::class, 'index'])->name('users.admin_listing');

        Route::get('users/login-as/{user_id}', [UserController::class, 'login_as_user'])->name('users.login_as_user');

        Route::get('users/create/{user_type}', [UserController::class, 'create_user_by_type'])->name('users.create_user_by_type');

        Route::get('users/change-password', [UserController::class, 'changePassword'])->name('users.changePassword');

        Route::post('users/update-password', [UserController::class, 'updatePassword'])->name('users.updatePassword');

        Route::get('users/datables', [UserController::class, 'datatable'])->name('users.datatables');

        Route::get('asscoiationRequest/datatable', [HomeController::class, 'asscoiationRequestDatatable'])->name('asscoiationRequest.datatable');

        Route::get('users/{user_id}/approve', [UserController::class, 'approve'])->name('users.approve');

        Route::get('users/{user_id}/reject', [UserController::class, 'reject'])->name('users.reject');

        Route::get('users/show-application/{id}', [UserController::class, 'show_application'])->name('users_show_application');

        Route::get('users/deactivate/{id}', [UserController::class, 'makeInActive'])->name('users.deactivate');

        Route::get('users/activate/{id}', [UserController::class, 'makeActive'])->name('users.activate');

        Route::resource('users', UserController::class);
    }



    //Users Contact Form
    Route::get('/contacts', [ContactRequestController::class, 'index'])->name('contact_requests.index');
    Route::get('/contacts/datatable', [ContactRequestController::class, 'datatable'])->name('contact_requests.datatables');

    // Subscriber
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
    Route::get('/subscribers/datatable', [SubscriberController::class, 'datatable'])->name('subscribers.datatables');

    // Training
    Route::get('/admin/training/programs', [TraningController::class, 'index'])->name('admin.training');
    Route::get('/datatable/admin/training', [TraningController::class, 'datatable'])->name('admin.training.datatable');
});

// .. End of Backend Routes
// Frontend Routes

Route::get('/country/numbers.json', function () {
    $countries = Country::select('id', 'name', 'tel')->where('status', 1)->get();
    $countriesArray = [];
    foreach ($countries as $key => $country) {
        $array = [];
        $array['name'] = $country->name;
        $array['tel'] = $country->tel;
        $countriesArray[] = $array;
    }
    return $countriesArray;
});

Route::get('/', [FrontHomeController::class, 'HomePage'])->name('login');
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
});

//Ajax Calendar Calls
Route::post('/ajax/calendar', [AjaxController::class, 'calendar_calls'])->name('ajax.calendar_calls');

Route::get('/', [FrontHomeController::class, 'HomePage'])->name('home');

Route::get('/', [FrontHomeController::class, 'HomePage'])->name('Home');

Route::get('/home', [FrontHomeController::class, 'HomePage'])->name('HomePage');

Route::get('/player', [FrontHomeController::class, 'PlayersPage'])->name('PlayersPage');

Route::get('/coaches', [FrontHomeController::class, 'CoachesPage'])->name('CoachesPage');

Route::get('/club', [FrontHomeController::class, 'ClubsPage'])->name('ClubsPage');

Route::get('/contact-us', [FrontHomeController::class, 'ContactusPage'])->name('ContactusPage');

Route::post('/contact-us-submit', [FrontHomeController::class, 'ContactPageSubmit'])->name('ContactPageSubmit');

Route::post('/subscribe-news', [FrontHomeController::class, 'subscribeNews'])->name('subscribe.news');

Route::get('/events', [FrontEventController::class, 'listing'])->name('EventsPage');

Route::get('/events/{id}', [FrontEventController::class, 'detail'])->name('EventsDetail');

Route::get('/events/intrested/{id}', [FrontEventController::class, 'intrested'])->name('eventsIntrested');

Route::get('/events/not-intrested/{id}', [FrontEventController::class, 'notIntrested'])->name('eventsNotIntrested');

Route::post('/events/inquiry/{event_id}', [FrontEventController::class, 'inquery'])->name('eventsInquery');

Route::get('/profile/{rec_no}', [FrontProfileController::class, 'user_profile'])->name('user_profile');

Route::get('/search', [FrontSessionController::class, 'search'])->name('session_search');

Route::get('/pay/pay_cancel', [FrontPaymentController::class, 'pay_cancel'])->name('payments.pay_cancel');
Route::post('/pay/pay_checkout', [FrontPaymentController::class, 'pay_checkout'])->name('payments.pay_checkout');
Route::get('/pay/pay_notify', [FrontPaymentController::class, 'pay_notify'])->name('payments.pay_notify');
Route::get('/pay/pay_now/{payment_id}', [FrontPaymentController::class, 'pay_now'])->name('payments.pay_now');
Route::get('/pay/pay_return', [FrontPaymentController::class, 'pay_return'])->name('payments.pay_return');
