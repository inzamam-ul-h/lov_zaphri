<div class="col-sm-3 mb-1">
    <p class="mg-b-10">Sub Category</p>
    <select id="s_sub_cat_id" name="s_sub_cat_id" class="form-control select2 filters_dt_select_cls">
        <option value="">Any</option>
        @foreach($sub_categories_array as $id => $name)
            <option value="{{$id}}">
                {{$name}}
            </option>
        @endforeach
    </select>
</div>