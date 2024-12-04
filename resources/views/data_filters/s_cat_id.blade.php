<div class="col-sm-3 mb-1">
    <p class="mg-b-10">Category</p>
    <select id="s_cat_id" name="s_cat_id" class="form-control select2 filters_dt_select_cls">
        <option value="">Any</option>
        @foreach($categories_array as $id => $name)
            <option value="{{$id}}">
                {{$name}}
            </option>
        @endforeach
    </select>
</div>