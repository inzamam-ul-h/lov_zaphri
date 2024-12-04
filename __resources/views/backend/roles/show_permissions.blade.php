<?php
$AUTH_USER = Auth::user();
?> 
<div class="row mt-3">
    <div class="col-md-12">
        <div class="table-responsive">

            <div class="table-container">

                <table class="table table-striped table-hover">

                    <thead>

                        <tr role="row" class="heading">

                            <th class="cell_1_width">Module</th>

                            <th class="cell_2_width">View Listing</th>

                            <th class="cell_2_width">Add</th>

                            <th class="cell_2_width">Update</th>

                            <th class="cell_2_width">View Details</th>

                            <th class="cell_2_width">Status</th>

                            <th class="cell_2_width">Delete</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($Modules_1 as $Module)

                            <?php
                            $Module_id = $Module->id;
                            ?>

                            <tr role="row" class="heading">

                                <td>

                                    {{ ucwords($Module->module_name) }}

                                </td>

                                <td>
                                    <?php
                                    $status = $list_array[$Module_id];

                                    $str='';
                                    if($status == 1)
                                    {
                                        $str='<button class="btn btn-success btn-sm"><i class="fa fa-check"></i></button>';
                                    }
                                    else
                                    {
                                        $str='<button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>';
                                    }
                                    echo $str;
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $status = $add_array[$Module_id];

                                    $str='';
                                    if($status == 1)
                                    {
                                        $str='<button class="btn btn-success btn-sm"><i class="fa fa-check"></i></button>';
                                    }
                                    else
                                    {
                                        $str='<button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>';
                                    }
                                    echo $str;
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $status = $edit_array[$Module_id];

                                    $str='';
                                    if($status == 1)
                                    {
                                        $str='<button class="btn btn-success btn-sm"><i class="fa fa-check"></i></button>';
                                    }
                                    else
                                    {
                                        $str='<button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>';
                                    }
                                    echo $str;
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $status = $view_array[$Module_id];

                                    $str='';
                                    if($status == 1)
                                    {
                                        $str='<button class="btn btn-success btn-sm"><i class="fa fa-check"></i></button>';
                                    }
                                    else
                                    {
                                        $str='<button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>';
                                    }
                                    echo $str;
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $status = $status_array[$Module_id];

                                    $str='';
                                    if($status == 1)
                                    {
                                        $str='<button class="btn btn-success btn-sm"><i class="fa fa-check"></i></button>';
                                    }
                                    else
                                    {
                                        $str='<button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>';
                                    }
                                    echo $str;
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $status = $delete_array[$Module_id];

                                    $str='';
                                    if($status == 1)
                                    {
                                        $str='<button class="btn btn-success btn-sm"><i class="fa fa-check"></i></button>';
                                    }
                                    else
                                    {
                                        $str='<button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>';
                                    }
                                    echo $str;
                                    ?>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>
    </div>
</div>             