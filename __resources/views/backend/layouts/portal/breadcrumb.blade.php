<?php
$AUTH_USER = Auth::user();
$show_breadcrumb = (isset($show_breadcrumb)) ? $show_breadcrumb : 0;
$show_title = (isset($show_title)) ? $show_title : 0;
$show_subtitle = (isset($show_subtitle)) ? $show_subtitle : 0;
$show_links = (isset($show_links)) ? $show_links : 0;
$show_buttons = (isset($show_buttons)) ? $show_buttons : ((isset($show_dashboard_buttons)) ? $show_dashboard_buttons : 0);
?>
<?php if ($show_breadcrumb): ?>
    <?php
    $col_css = 'col-lg-12 col-md-12 col-sm-12';
    if ($show_buttons)
        $col_css = 'col-lg-9 col-md-8 col-sm-12';
    ?>
    <div class="row wrapper border-bottom white-bg page-heading">

        <div class="{{ $col_css }}">

            <?php if ($show_title): ?>
                <h2><?php echo $title; ?></h2>
            <?php endif; ?>

            <?php if ($show_subtitle): ?>
                <p><?php echo $subtitle; ?></p>
            <?php endif; ?>

            <?php if ($show_links): ?>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <?php if (isset($b1_title)): ?>
                        <?php if (isset($b1_route)):
                            $url = (isset($b1_route)) ? route($b1_route) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b1_title; ?></a>
                            </li>
                        <?php elseif (isset($b1_url)):
                            $url = (isset($b1_url)) ? url($b1_url) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b1_title; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="active">
                                <strong><?php echo $b1_title; ?></strong>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($b2_title)): ?>
                        <?php if (isset($b2_route)):
                            $url = (isset($b2_route)) ? route($b2_route) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b2_title; ?></a>
                            </li>
                        <?php elseif (isset($b2_url)):
                            $url = (isset($b2_url)) ? url($b2_url) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b2_title; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="active">
                                <strong><?php echo $b2_title; ?></strong>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($b3_title)): ?>
                        <?php if (isset($b3_route)):
                            $url = (isset($b3_route)) ? route($b3_route) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b3_title; ?></a>
                            </li>
                        <?php elseif (isset($b3_url)):
                            $url = (isset($b3_url)) ? url($b3_url) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b3_title; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="active">
                                <strong><?php echo $b3_title; ?></strong>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($b4_title)): ?>
                        <?php if (isset($b4_route)):
                            $url = (isset($b4_route)) ? route($b4_route) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b4_title; ?></a>
                            </li>
                    <?php elseif (isset($b4_url)):
                        $url = (isset($b4_url)) ? url($b4_url) : '#';
                        ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b4_title; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="active">
                                <strong><?php echo $b4_title; ?></strong>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($b5_title)): ?>
                        <?php if (isset($b5_route)):
                            $url = (isset($b5_route)) ? route($b5_route) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b5_title; ?></a>
                            </li>
                        <?php elseif (isset($b5_url)):
                            $url = (isset($b5_url)) ? url($b5_url) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b5_title; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="active">
                                <strong><?php echo $b5_title; ?></strong>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($b6_title)): ?>
                        <?php if (isset($b6_route)):
                            $url = (isset($b6_route)) ? route($b6_route) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b6_title; ?></a>
                            </li>
                        <?php elseif (isset($b6_url)):
                            $url = (isset($b6_url)) ? url($b6_url) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b6_title; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="active">
                                <strong><?php echo $b6_title; ?></strong>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($b7_title)): ?>
                        <?php if (isset($b7_route)):
                            $url = (isset($b7_route)) ? route($b7_route) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b7_title; ?></a>
                            </li>
                        <?php elseif (isset($b7_url)):
                            $url = (isset($b7_url)) ? url($b7_url) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b7_title; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="active">
                                <strong><?php echo $b7_title; ?></strong>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($b8_title)): ?>
                        <?php if (isset($b8_route)):
                            $url = (isset($b8_route)) ? route($b8_route) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b8_title; ?></a>
                            </li>
                        <?php elseif (isset($b8_url)):
                            $url = (isset($b8_url)) ? url($b8_url) : '#';
                            ?>
                            <li>
                                <a href="{{ $url }}"><?php echo $b8_title; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="active">
                                <strong><?php echo $b8_title; ?></strong>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ol>
            <?php endif; ?>

        </div>

        <?php if ($show_buttons): ?>
            <div class="col-lg-3 col-md-4 col-sm-12 sm-action">
                <div class="title-action">

                    <?php if (isset($show_dashboard_buttons) && $show_dashboard_buttons): ?>
                        <?php echo dashboard_buttons($AUTH_USER); ?>
                    <?php endif; ?>

                    <?php if (isset($btn_filters) && $btn_filters): ?>
                        <?php echo filter_button(); ?>
                    <?php endif; ?>

                    <?php if (isset($btn_add_route) && !empty($btn_add_route)): ?>
                        <a href="{{ route($btn_add_route) }}" class="btn btn-primary" title="Add New Record">
                            <i class="fa fa-plus-square fa-lg"></i> Add New
                        </a>
                    <?php endif; ?>

                    <?php if (isset($btn_add_route_type) && !empty($btn_add_route_type)): ?>
                        <a href="{{ route($btn_add_route_type, $add_user_type) }}" class="btn btn-primary" title="Add New Record">
                            <i class="fa fa-plus-square fa-lg"></i> Add New
                        </a>
                    <?php endif; ?>

                    <?php if (isset($btn_edit_route) && !empty($btn_edit_route)): ?>
                        <a href="{{ route($btn_edit_route, $edit_record_id) }}" class="btn btn-primary" title="Edit Record">
                            <i class="fa fa-pencil fa-lg"></i> Edit
                        </a>
                    <?php endif; ?>

                    <?php if (isset($btn_back_route) && !empty($btn_back_route)): ?>
                        <a href="{{ route($btn_back_route) }}" class="btn btn-primary" title="Back to Listing">
                            Back
                        </a>
                    <?php elseif (isset($btn_back_url) && !empty($btn_back_url)): ?>
                        <a href="{{ url($btn_back_url) }}" class="btn btn-primary" title="Back to Listing">
                            Back
                        </a>
                    <?php endif; ?>

                    <?php if (isset($btn_dashboard_route) && $btn_dashboard_route): ?>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary" title="Back to Dashboard">
                            Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>


@include('flash::message')
@include('coreui-templates::common.errors')
