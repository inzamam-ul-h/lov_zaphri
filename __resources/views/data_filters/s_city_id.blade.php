<div class="col-sm-3 mb-1">
    <p class="mg-b-10">City</p>
    <select id="s_city_id" name="s_city_id" class="form-control select2 filters_dt_select_cls">
        <option value="">Any</option>
        @foreach($cities_array as $id => $name)
            <option value="{{$id}}">
                {{$name}}
            </option>
        @endforeach
    </select>
</div>