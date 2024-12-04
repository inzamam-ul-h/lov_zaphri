<?php
$AUTH_USER = Auth::user();
$seeker_id = 0;
if($AUTH_USER)
{
  $seeker_id = $AUTH_USER->refer_id;  
}
$link_url = url('/job/'.$job->rec_no);
?>
<div class="col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-4 pxp-jobs-card-1-container">
    
    <div class="pxp-jobs-card-1 pxp-has-border">
        
        <div class="pxp-jobs-card-1-top">
            
            <div class="row">
                
                <a href="{{ $link_url }}" class="col-10 col-sm-10 pxp-jobs-card-1-category">
                    
                    <div class="pxp-jobs-card-1-category-icon">
                        <span class="fa fa-bullhorn"></span>
                    </div>
                    
                    @if(isset($job->sub_cat_id) && (isset($sub_categories_array[$job->sub_cat_id])) && $job->sub_cat_id !=null && $job->sub_cat_id != '' && (key_exist($job->sub_cat_id,$sub_categories_array)))
                        <div class="pxp-jobs-card-1-category-label">
                            {{ $sub_categories_array[$job->sub_cat_id] }}
                        </div>
                    @endif
                </a>
                
                <div class="col-2 col-sm-2 pxp-single-job-options pxp_custom_fvrt_div" id="div_save_job_{{$job->id}}">
                    @if($AUTH_USER)
                        <?php echo show_job_favorite_button($job->id, 'user', $seeker_id, get_lang_field_data($job, 'title'));?>
                    @else
                        <?php echo show_job_favorite_button($job->id, 'guest', $seeker_id, get_lang_field_data($job, 'title'));?>
                    @endif
                </div>
                
            </div>
            
            <a href="{{ $link_url }}" class="pxp-jobs-card-1-title">
                <?=get_lang_field_data($job, 'title');?>
            </a>
            
            <div class="pxp-jobs-card-1-details">
                
                @if(isset($job->city_id) && (isset($cities_array[$job->city_id]))  && (key_exist($job->city_id,$cities_array)) && $job->city_id !=null && $job->city_id != '')
                    <a href="{{ $link_url }}" class="pxp-jobs-card-1-location">
                        <span class="fa fa-globe"></span>
                        {{ $cities_array[$job->city_id] }}
                    </a>
                @endif
                
                @if(isset($job->period_id) && (isset($periods_array[$job->period_id])) && (key_exist($job->period_id,$periods_array)) && $job->period_id !=null && $job->period_id != '')
                    <div class="pxp-jobs-card-1-type">
                        {{ $periods_array[$job->period_id] }}
                    </div>
                @endif
                
            </div>
            
        </div>
        
        <div class="pxp-jobs-card-1-bottom">
            
            <div class="pxp-jobs-card-1-bottom-left">
                
                <div class="pxp-jobs-card-1-date pxp-text-light">
                    <?php echo rephraseTime($job->created_at, 0);?> <?=translate_it('by')?>
                </div>
                
                <a href="{{ $link_url }}" class="pxp-jobs-card-1-company">
                    {{ $employers_array[$job->employer_id] }}
                </a>
                
            </div>
            
            <div class="pxp-single-job-options" id="ajax_apply_show_button_{{$job->id}}"></div>
            
            <div class="pxp-single-job-options" id="ajax_apply_hide_button_{{$job->id}}">
                @if($AUTH_USER)
                    <?php echo show_job_apply_button($job->id, 'user', $seeker_id, get_lang_field_data($job, 'title'));?>
                @else
                    <?php echo show_job_apply_button($job->id, 'guest', $seeker_id, get_lang_field_data($job, 'title'));?>
                @endif
            </div>
            
        </div>
        
    </div>
    
</div>