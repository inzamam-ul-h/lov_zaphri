<?php
$AUTH_USER = Auth::user();
?> 
<?php
$count=0;
?> 
    <div class="row mt-3">
        <div class="col-md-12">                            
            <div class="table-responsive">
                <div class="table-container">
                    <table class="table table-striped table-hover">

                        <thead>


                            <tr role="row" class="heading">

                                <th class="cell_1_width">Module</th>                                 

                                <th class="cell_2_width">All</th>                              

                                <th class="cell_2_width">Listing</th>

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

                                        if($Module->mod_list==1 && $Module->mod_add==1 && $Module->mod_edit==1 && $Module->mod_view==1 && $Module->mod_status==1 && $Module->mod_delete==1)

                                        {
											$is_mod = 1;
											if($list_array[$Module_id] == 0 || $add_array[$Module_id] == 0 || $edit_array[$Module_id] == 0 || $view_array[$Module_id] == 0 || $status_array[$Module_id] == 0 || $delete_array[$Module_id] == 0)
											{
												$is_mod = 0;
											}
											?>
											<div class="btn-group radioBtnAll">
												<?php
												$class = 'notActive';
												if($is_mod == 1)
												{
													$class = 'active';
												}
												?>
												<a class="btn btn-success btn-sm <?php echo $class;?>" data-toggle="<?php echo $count;?>" data-title="1"><i class="fa fa-check fa-lg"></i></a>
	
												<?php
												$class = 'notActive';
												if($is_mod == 0)
												{
													$class = 'active';
												}
												?>
												<a class="btn btn-danger btn-sm <?php echo $class;?>" data-toggle="<?php echo $count;?>" data-title="0"><i class="fa fa-times fa-lg"></i></a>
											</div>
	
											<?php

                                        }

                                        else

                                        {

                                        ?>

                                        -

                                        <?php

                                        }

                                        ?>

                                    </td>

                                    

                                    <td>


                                        <?php

                                        if($Module->mod_list==1)

                                        {
                                        $is_mod = $list_array[$Module_id];
                                        $field = 'id_list_'.$count;
                                        ?>
                                        <div class="btn-group radioBtn">
                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 1)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-success btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="1"><i class="fa fa-check fa-lg"></i></a>

                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 0)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-danger btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="0"><i class="fa fa-times fa-lg"></i></a>
                                        </div>
                                        <input type="hidden" name="list_module[<?php echo $count;?>]" id="<?php echo $field;?>" value="<?php echo $is_mod;?>">

                                        <?php

                                        }

                                        else

                                        {

                                        ?>

                                        -

                                        <?php

                                        }

                                        ?>

                                    </td>

                                    <td>

                                        <?php

                                        if($Module->mod_add==1)

                                        {
                                        $is_mod = $add_array[$Module_id];
                                        $field = 'id_add_'.$count;
                                        ?>
                                        <div class="btn-group radioBtn">
                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 1)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-success btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="1"><i class="fa fa-check fa-lg"></i></a>

                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 0)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-danger btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="0"><i class="fa fa-times fa-lg"></i></a>
                                        </div>
                                        <input type="hidden" name="add_module[<?php echo $count;?>]" id="<?php echo $field;?>" value="<?php echo $is_mod;?>">

                                        <?php

                                        }

                                        else

                                        {

                                        ?>

                                        -

                                        <?php

                                        }

                                        ?>

                                    </td>

                                    <td>

                                        <?php

                                        if($Module->mod_edit==1)


                                        {
                                        $is_mod = $edit_array[$Module_id];
                                        $field = 'id_edit_'.$count;
                                        ?>
                                        <div class="btn-group radioBtn">
                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 1)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-success btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="1"><i class="fa fa-check fa-lg"></i></a>

                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 0)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-danger btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="0"><i class="fa fa-times fa-lg"></i></a>
                                        </div>
                                        <input type="hidden" name="edit_module[<?php echo $count;?>]" id="<?php echo $field;?>" value="<?php echo $is_mod;?>">

                                        <?php

                                        }

                                        else

                                        {

                                        ?>

                                        -

                                        <?php

                                        }

                                        ?>

                                    </td>

                                    <td>

                                        <?php

                                        if($Module->mod_view==1)

                                        {
                                        $is_mod = $view_array[$Module_id];
                                        $field = 'id_view_'.$count;
                                        ?>
                                        <div class="btn-group radioBtn">
                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 1)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-success btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="1"><i class="fa fa-check fa-lg"></i></a>

                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 0)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-danger btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="0"><i class="fa fa-times fa-lg"></i></a>
                                        </div>
                                        <input type="hidden" name="view_module[<?php echo $count;?>]" id="<?php echo $field;?>" value="<?php echo $is_mod;?>">

                                        <?php

                                        }

                                        else

                                        {

                                        ?>

                                        -

                                        <?php

                                        }

                                        ?>

                                    </td>

                                    <td>

                                        <?php

                                        if($Module->mod_status==1)

                                        {
                                        $is_mod = $status_array[$Module_id];
                                        $field = 'id_status_'.$count;

                                        ?>
                                        <div class="btn-group radioBtn">
                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 1)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-success btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="1"><i class="fa fa-check fa-lg"></i></a>

                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 0)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-danger btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="0"><i class="fa fa-times fa-lg"></i></a>
                                        </div>
                                        <input type="hidden" name="status_module[<?php echo $count;?>]" id="<?php echo $field;?>" value="<?php echo $is_mod;?>">

                                        <?php

                                        }

                                        else

                                        {

                                        ?>

                                        -

                                        <?php

                                        }

                                        ?>

                                    </td>

                                    <td>

                                        <?php

                                        if($Module->mod_delete==1)

                                        {
                                        $is_mod = $delete_array[$Module_id];
                                        $field = 'id_delete_'.$count;
                                        ?>
                                        <div class="btn-group radioBtn">
                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 1)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-success btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="1"><i class="fa fa-check fa-lg"></i></a>

                                            <?php
                                            $class = 'notActive';
                                            if($is_mod == 0)
                                            {
                                                $class = 'active';
                                            }
                                            ?>
                                            <a class="btn btn-danger btn-sm <?php echo $class;?>" data-toggle="<?php echo $field;?>" data-title="0"><i class="fa fa-times fa-lg"></i></a>
                                        </div>
                                        <input type="hidden" name="delete_module[<?php echo $count;?>]" id="<?php echo $field;?>" value="<?php echo $is_mod;?>">

                                        <?php

                                        }

                                        else

                                        {

                                        ?>

                                        -

                                        <?php

                                        }

                                        ?>

                                    </td>

                                </tr>

                                <?php

                                $count++;

                                ?>

                            @endforeach

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="total_modules" value="<?php echo $count;?>" />
