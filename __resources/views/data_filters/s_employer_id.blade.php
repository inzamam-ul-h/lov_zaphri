<?php
$AUTH_USER = Auth::user();
?>
@if($AUTH_USER->user_type == 'admin')
    <div class="col-sm-3 mb-1">
        <p class="mg-b-10">Employer</p>
        <select id="s_employer_id" name="s_employer_id" class="form-control select2 filters_dt_select_cls">
            <option value="">Any</option>
            @foreach($employers_array as $id => $name)
                <option value="{{$id}}">
                    {{$name}}
                </option>
            @endforeach
        </select>
    </div>
@endif