<div class="col-sm-3 mb-1">
    <p class="mg-b-10">Employement Condition</p>
    <select id="s_condition_id" name="s_condition_id" class="form-control select2 filters_dt_select_cls">
        <option value="">Any</option>
        @foreach($conditions_array as $id => $name)
            <option value="{{$id}}">
                {{$name}}
            </option>
        @endforeach
    </select>
</div>