<div class="col-sm-3 mb-1">
    <p class="mg-b-10">Employement Hour</p>
    <select id="s_hour_id" name="s_hour_id" class="form-control select2 filters_dt_select_cls">
        <option value="">Any</option>
        @foreach($hours_array as $id => $name)
            <option value="{{$id}}">
                {{$name}}
            </option>
        @endforeach
    </select>
</div>