<?php
$can_remove = (isset($can_remove)) ? $can_remove : 0;
?>
<div class="{{ $parent_classes }}">
    <div class="ibox">
        <div class="ibox-title">
            <a href="{{ url('/manage/users/'.$user_profile->id) }}" style = "color: blue;"><h5><?php echo $user_profile->name; ?></h5></a>
        </div>
        <div class="ibox-content col-lg-12 col-md-12 col-sm-12">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <a href="{{ url('/manage/users/'.$user_profile->id) }}">
                    <img src="{{ user_profile_image_path($user_profile->id) }}" style="width: 100%" alt="User Profile">
                </a>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12">
                <?php
                if (isset($profile_heading)) {
                    ?>
                    <label class="control-label"> <?php echo $profile_heading; ?> </label><br>
                    <?php
                }
                ?>
                <label class="control-label">Name :  </label> {{ $user_profile->name }}<br>
                <label class="control-label">Email : </label> {{ $user_profile->email }}<br>
                <label class="control-label">Phone : </label> {{ $user_profile->phone }}<br>
                <p style="text-align: center" class="mt-10">
                    <a href="{{ url('/manage/users/'.$user_profile->id) }}" class="btn btn-xs btn-primary" target="_blank" title="View Profile">
                        View
                    </a>
                    <?php if ($can_remove == 1): ?> &nbsp;
                        <a class="btn btn-xs btn-danger" title="Remove" ui-toggle-class="bounce" ui-target="#animate" onclick="deleteMemberModal('delete_record_{{ $user_profile->id }}')">
                            <i class="fa fa-trash fa-lg"></i> Remove
                        </a>
                    <?php endif; ?>
                </p>

            </div>
        </div>
    </div>
    <?php if ($can_remove == 1):
        $remove_url = (isset($remove_url)) ? url($remove_url) : '#';
        ?>
        <div class="modal fade" id="delete_record_{{ $user_profile->id }}" role="dialog" aria-labelledby="loginModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="{{ $remove_url }}" method="GET">
                    <div class="modal-content modal-content-demo">
                        <div class="modal-header">
                            <h2 class="modal-title">{{ $remove_title }}</h2>
                        </div>
                        <div class="modal-body">
                            <p>
                                Are you sure you want to Remove?
                                <br>
                                <strong>[ {{ $user_profile->name }} ]</strong>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Yes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
