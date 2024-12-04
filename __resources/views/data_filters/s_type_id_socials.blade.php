<div class="col-sm-3 mb-1">
    <p class="mg-b-10">Type</p>
    <select id="s_type_id" name="s_type_id" class="form-control select2 filters_dt_select_cls">
        <option value="">Any</option>
        @foreach($social_types as $id => $name)
            <option value="{{$id}}">
                {{$name}}
            </option>
        @endforeach
    </select>
</div>