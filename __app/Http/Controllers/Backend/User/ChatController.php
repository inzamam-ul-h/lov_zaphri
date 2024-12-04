<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\MainController as MainController;
use Auth;
use File;
use Flash;
use Response;
use Attribute;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Setting;
use App\Models\Seeker;
use App\Models\Job;
use App\Models\Chat;
use App\Models\Conversation;

class ChatController extends MainController {

    private $lang = "en";
    private $uploads_root = "uploads";
    private $uploads_path = "uploads/users/";
    private $views_path = "users";
    private $home_route = "dashboard";
    private $view_route = "users.profile";
    private $msg_updated = "User details updated successfully.";
    private $msg_not_found = "User not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same User name";

    public function inbox(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $chats = Chat::leftjoin('employers', 'employers.id', '=', 'chats.sender_id')->select('chats.*', 'employers.name_en')->where('receiver_id', $seeker_id)->orderby('chats.id', 'desc')->get();

        $Settings = ContactDetail::find(1);

        return view('users.inbox.listing', compact(
                        'Settings',
                        'chats'
        ));
    }

    public function chatDetailsByAjax(Request $request) {
        $Auth_User = Auth::user();

        $conversation = Conversation::where('chat_id', $request->chat_id)->where('user_type', '!=', 'seeker')->get();
        foreach ($conversation as $updateconversation) {
            if (!empty($updateconversation)) {
                $updateconversation->read_status = 1;
                $updateconversation->read_time = time();
                $updateconversation->updated_by = $Auth_User->refer_id;
                $updateconversation->save();
            }
        }
        $conversations = Conversation::where('chat_id', $request->chat_id)->get();

        $chats = Chat::where('id', $request->chat_id)->first();
        ?>


        <?php
        foreach ($conversations as $conversation) {
            ?>

            <?php
            if ($conversation->user_type == 'seeker') {
                ?>
                <div class="pxp-dashboard-inbox-messages-item">
                    <div class="row justify-content-end">
                        <div class="col-7">
                            <div class="pxp-dashboard-inbox-messages-item-header flex-row-reverse">
                                <div class="pxp-dashboard-inbox-messages-item-avatar pxp-cover" style="background-image: url(<?php echo seeker_profile_image_path($Model_Data = "", $chats->receiver_id); ?>);"></div>

                                <div class="pxp-dashboard-inbox-messages-item-time pxp-text-light me-3"><?= $conversation->created_at->format('H:i'); ?></div>
                            </div>
                            <div class="pxp-dashboard-inbox-messages-item-message pxp-is-other mt-2">
                                <?= $conversation->message ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            else {
                ?>

                <div class="pxp-dashboard-inbox-messages-item mt-4">
                    <div class="row">
                        <div class="col-7">
                            <div class="pxp-dashboard-inbox-messages-item-header">
                                <div class="pxp-dashboard-inbox-messages-item-avatar pxp-cover" style="background-image: url(<?php echo user_profile_image_path($conversation->user_id); ?>);"></div>

                                <div class="pxp-dashboard-inbox-messages-item-time pxp-text-light ms-3"><?= $conversation->created_at->format('H:i'); ?></div>
                            </div>
                            <div class="pxp-dashboard-inbox-messages-item-message pxp-is-self mt-2">
                                <?= $conversation->message ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
        }
        ?>
        <input type="hidden" name="" id="current_chat_id" value="<?= $request->chat_id ?>">
        <?php
    }

    public function sendMessage(Request $request) {
        $Auth_User = Auth::user();
        $chats = Chat::where('id', $request->chat_id)->first();

        $seeker_info = Seeker::select('id', 'name_en')->where('id', $Auth_User->refer_id)->first();

        $conversation = new Conversation();
        $conversation->chat_id = $request->chat_id;
        $conversation->user_type = $chats->receiver_type;
        $conversation->user_id = $chats->receiver_id;
        $conversation->message = $request->message;
        $conversation->message_time = time();
        $conversation->chat_id = $request->chat_id;
        $conversation->created_by = $Auth_User->refer_id;
        $conversation->save();

        $created_at = $conversation->created_at;
        ?>
        <div class="pxp-dashboard-inbox-messages-item">
            <div class="row justify-content-end">
                <div class="col-7">
                    <div class="pxp-dashboard-inbox-messages-item-header flex-row-reverse">
                        <div class="pxp-dashboard-inbox-messages-item-avatar pxp-cover" style="background-image: url(<?php echo seeker_profile_image_path($Model_Data = "", $seeker_info->id); ?>);"></div>
                       <!--  <div class="pxp-dashboard-inbox-messages-item-name me-2"><?= $seeker_info->name_en ?></div> -->
                        <div class="pxp-dashboard-inbox-messages-item-time pxp-text-light me-3"><?= $created_at->format('H:i'); ?></div>
                    </div>
                    <div class="pxp-dashboard-inbox-messages-item-message pxp-is-other mt-2">
                        <?= $request->message ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}
