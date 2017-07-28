<?
$ci =& get_instance();
?>
<!DOCTYPE html>
<html class="no-js">
    
    <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Admin Home Page</title>
        <!-- Bootstrap -->
    <link href="<?echo site_url();?>bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/DT_bootstrap.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/jquery.mCustomScrollbar.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/css/jquery-ui.css" rel="stylesheet" />
    <link href="<?echo site_url();?>assets/css/jquery.datetimepicker.css" rel="stylesheet" />
    <link href="<?echo site_url();?>css/jquery.fancybox.css" rel="stylesheet" />
    <link href="<?echo site_url();?>assets/styles.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/styles_atom.css" rel="stylesheet" media="screen">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    <script src="<?echo site_url();?>vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    <script src="<?echo site_url();?>assets/js/jquery-1.10.2.js"></script>
    <script src="<?echo site_url();?>assets/js/jquery-ui.js"></script>
    <script src="<?echo site_url();?>assets/js/jquery.datetimepicker.js"></script>
    <script src="<?echo site_url();?>js/jquery.mCustomScrollbar.js"></script>
    <script src="<?echo site_url();?>js/jquery.fancybox.js"></script>
    <link href="<?echo site_url();?>vendors/chosen.min.css" rel="stylesheet" media="screen">
    <script src="<?echo site_url();?>vendors/chosen.jquery.min.js"></script>
    <script src="<?echo site_url();?>js/angular.js"></script> 
    <script type="text/javascript">
    /**
     * Number.prototype.format(n, x, s, c)
     * 
     * @param integer n: length of decimal
     * @param integer x: length of whole part
     * @param mixed   s: sections delimiter
     * @param mixed   c: decimal delimiter
     */
    Number.prototype.format = function(n, x, s, c) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = this.toFixed(Math.max(0, ~~n));

        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
    };
    function cal_outSource(id){
        var all_row=$("#table_out_"+id).find("tr");
        var sum_cost=0;
        var sum_charge=0;
        console.log(" -"+id );
        for (var i = 0; i <all_row.length-1; i++) {
            var cur_coll=all_row.eq(i).find("td");
            cost=parseInt(cur_coll.eq(3).find("input.out_change").val());
            charge=parseInt(cur_coll.eq(4).find("input.out_change").val());
            sum_cost+=cost;
            sum_charge+=charge;
            console.log(" -"+id );
        };
        var last_row=all_row.eq(all_row.length-1).find("td");
        last_row.eq(3).html(""+sum_cost.format(2, 3, ',', '.'));
        last_row.eq(4).html(""+sum_charge.format(2, 3, ',', '.'));
        var margin=(sum_charge-sum_cost)/sum_charge*100;
        last_row.eq(5).html(""+margin.format(2, 3, ',', '.')+"%");

        $("#out_but_"+id).html("Out source("+sum_cost.format(2, 3, ',', '.')+")");

    }
    </script>
    <style type="text/css">
    /*.navbar-inner div{
            background-color:#3366FF;
            color: #FFFFFF;
        }*/
    
    .white-nav-bar {
        background-image: -moz-linear-gradient(top, #fff, #f2f2f2);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#fff), to(#f2f2f2));
        background-image: -webkit-linear-gradient(top, #fff, #f2f2f2);
        background-image: -o-linear-gradient(top, #fff, #f2f2f2);
        background-image: linear-gradient(to bottom, #fff, #f2f2f2);
    }
    </style>
    <link rel="stylesheet" href="<?echo site_url();?>css/style.css">
</head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner white-nav-bar">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<? echo site_url('admin');?>">Admin Panel</a>
                    <div class="nav-collapse collapse">
                        <ul class="nav pull-right">
                            <li class="dropdown">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user"></i> <?echo $user_data->firstname." ".$user_data->lastname;?> <i class="caret"></i>

                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="<? echo site_url('profile');?>">Profile</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a tabindex="-1" href="<? echo site_url('main/logout');?>">Logout</a>
                                    </li>
                                </ul>
                            </li>
                            
                        </ul>
                        <ul class="nav">   
                                <?
                            if (isset($user_data->prem['admin'])||isset($user_data->prem['hr'])) {
                                ?>
                                    <li class="dropdown">
                                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">Admin Account <b class="caret"></b>

                                        </a>
                                        <ul class="dropdown-menu" id="menu1">                                            
                                            <li>
                                                <a href="<? echo site_url('admin/user_add')?>">เพิ่ม User</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/user_list/')?>">User list</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/user_structure')?>">Business Structure</a>
                                            </li>
                                            <?
                                            if (isset($user_data->prem['admin'])) {
                                            ?>
                                            <li>
                                                <a href="<? echo site_url('admin/add_prem_group')?>">เพิ่ม Group User</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/prem_group_list/')?>">Group User list</a>
                                            </li>
                                            <?
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                     <?
                                 }
                            if (isset($user_data->prem['admin'])) {
                                ?>
                                    <li class="dropdown">
                                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">Service Item <b class="caret"></b>

                                        </a>
                                        <ul class="dropdown-menu" id="menu1">                                            
                                            <li>
                                                <a href="<? echo site_url('admin/hour_rate_add')?>">เพิ่ม Service Item</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/hour_rate_list/')?>">Service Item list</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/pos_rate_chart/')?>">Position to Service Item Chart</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/rate_pos_chart/')?>">Service to position Chart</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/rate_pos_chart_table/')?>">Service VS position Table</a>
                                            </li>

                                        </ul>
                                    </li>
                                    <?
                                }
                            if (isset($user_data->prem['admin'])||isset($user_data->prem['account'])) {
                                ?>
                                    <li class="dropdown">
                                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">Client <b class="caret"></b>

                                        </a>
                                        <ul class="dropdown-menu" id="menu1">                                            
                                            <li>
                                                <a href="<? echo site_url('admin/company_add')?>">เพิ่ม Client</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/company_list/')?>">Client list</a>
                                            </li>

                                        </ul>
                                    </li>
                                    <?
                                 }
                            if (isset($user_data->prem['admin'])) {
                                ?>
                                    <li class="dropdown">
                                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">Company <b class="caret"></b>

                                        </a>
                                        <ul class="dropdown-menu" id="menu1">                                            
                                            <li>
                                                <a href="<? echo site_url('admin/business')?>">Business Unit</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/department')?>">Department</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/position')?>">Position</a>
                                            </li>

                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">Traffic control<b class="caret"></b>

                                                    </a>
                                        <ul class="dropdown-menu" id="menu_help">
                                            <li >
                                                <a href="<? echo site_url('admin/traffic_control');?>">Traffic control</a>
                                            </li> 
                                            <li>
                                                <a href="<? echo site_url('admin/res_manager_overall');?>">Overall view</a>
                                            </li>
                                            <li>
                                                <a href="<? echo site_url('admin/res_manager_detail');?>">Detail View</a>
                                            </li>
                                        </ul>
                                    </li>
                                        
                                <?
                            }
                                ?>  
                                <li >
                                    <a href="<? echo site_url('admin/project_cancel_list');?>">Canceled Project</a>
                                </li>   
                                <li >
                                    <a href="<? echo site_url('gate');?>">Back to Main</a>
                                </li>                            
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>