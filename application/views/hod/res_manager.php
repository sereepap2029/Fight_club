<? $ci=& get_instance(); ?>
<link rel="stylesheet" href="<?echo site_url();?>assets/work_sheet.css">
<style type="text/css">
.row-fluid .no-margin-left {
    margin-left: 0px;
}
hr{
    min-height: 2px;
    background-color: #CCCCCC;
    width: 100%;
}
.range_task{
    background-color: #cccccc;
}
.w-wrap{
    word-wrap: break-word;
}
.carlendar-res{
    overflow-y:auto;
}
.show_click{
    cursor: pointer;
}

.normal_allocate{
    background-color: #CCCC00;
}
.over_allocate{
    background-color: #E68A00;
}

.holiday_td{
    background-color: rgb(10,15,76);
    color: white;
}
.current_day{
    background-color: #51ABDB;
}
.job_d_click{
    cursor: pointer;
}
.outer_tr .head_td{
   white-space:nowrap;
   vertical-align: middle;
}
thead tr .head_th{
   white-space:nowrap;
   vertical-align: middle;
}

.gray-bg{
    position: relative;
    background: rgba(46,43,46,1);
background: -moz-linear-gradient(left, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
background: -webkit-gradient(left top, right top, color-stop(0%, rgba(46,43,46,1)), color-stop(35%, rgba(89,89,89,1)), color-stop(100%, rgba(71,71,71,1)));
background: -webkit-linear-gradient(left, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
background: -o-linear-gradient(left, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
background: -ms-linear-gradient(left, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
background: linear-gradient(to right, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#2e2b2e', endColorstr='#474747', GradientType=1 );
    text-align: center;
}
.text_in_bar{
    color: white;
    font-weight: bolder;
    text-align: center;
    position: absolute;
    height: 100%;
    width: 100%;
}
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">Resource Manager</div>
                    </div>
                    <div class="block-content collapse in ">
                        <div class="span12">
                            <!-- Tab -->
                            
                                <div class="well">
                              <!-- dropdown -->
                                <div class="right_ul">
                                    <ul class="nav" id="btn_near_tab">
                                        <li id="li_btn_near_tab">
                                            <div class="dropdown">
                                                <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">Tutorials
                                                    <span class="caret"></span></button>
                                                <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                                                    <li id="li_btn_near_tab" role="presentation"><a role="menuitem" tabindex="-1" href="#">1</a></li>
                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">2</a></li>
                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">3</a></li>
                                                    <li role="presentation" class="divider"></li>
                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">4</a></li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li id="li_btn_near_tab">
                                            <div class="dropdown">
                                                <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">Tutorials
                                                    <span class="caret"></span></button>
                                                <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">1</a></li>
                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">2</a></li>
                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">3</a></li>
                                                    <li role="presentation" class="divider"></li>
                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">4</a></li>
                                                </ul>
                                            </div>
                                        </li>
                                      </ul>
                                    </div>
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab_one" data-toggle="tab">Overall View</a></li>
                                        <li><a href="#tab_two" data-toggle="tab">Detail View</a></li>
                                    </ul>
                                    <div id="myTabContent" class="tab-content">
                                        <div class="tab-pane active in" id="tab_one">
                                            <div class="span12 no-margin-left carlendar-res" >
                                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                                                                <thead>
                                                                     <tr>    
                                                                     <th class="head_th" rowspan="2">Resource</th>                                                 
                                                                     <th colspan="2" style="text-align: center;">Util</th>
                                                                    <?
                                                                    $current_day_time=$ci->m_time->datepicker_to_unix(date("d/m/Y"));
                                                                    $start_car_time=$current_day_time-(60*60*24*7);
                                                                    $end_car_time=$current_day_time+(60*60*24*60);
                                                                    $current_time=$start_car_time;
                                                                        $cur_month=1000;
                                                                        while ($current_time<=$end_car_time) {
                                                                            if ($cur_month!=date("n",$current_time)) {
                                                                                $cur_month=date("n",$current_time);
                                                                                $cur_day=date("j",$current_time);
                                                                                $numday=date("t",$current_time);
                                                                                $colspan1=0;
                                                                                if ($cur_month==date("n",$end_car_time)) {
                                                                                    $numday=date("j",$end_car_time);
                                                                                    $colspan1=(int)$numday-(int)$cur_day+1;
                                                                                }else{
                                                                                    $colspan1=(int)$numday-(int)$cur_day+1;
                                                                                }
                                                                                
                                                                                ?>
                                                                                <th colspan="<?=$colspan1?>" style="text-align: center;"><?=date("M",$current_time)?></th>
                                                                                <?
                                                                            }                                                        
                                                                           $current_time+=(60*60*24);
                                                                        }
                                                                    ?>
                                                                    </tr>
                                                                    <tr>                                                                    
                                                                    <th class="head_th">Last 10 day</th>
                                                                    <th class="head_th">next 10 day</th>
                                                                    <?
                                                                    
                                                                    $current_time=$start_car_time;
                                                                        while ($current_time<=$end_car_time) {
                                                                            $holiday="";
                                                                            if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                                                $holiday='class="holiday_td"';
                                                                            }
                                                                           ?>
                                                                           <th <?=$holiday?>><?=date("d",$current_time)?></th>
                                                                           <?
                                                                           $current_time+=(60*60*24);
                                                                        }
                                                                    ?>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                   
                                                                    <?
                                                                    $start_util_time=$current_day_time-(60*60*24*10);
                                                                    foreach ($all_child as $ckey => $cvalue) {
                                                                        $sumalocate_l_10=0;
                                                                        $sumspen_l_10=0;
                                                                        $sumalocate_n_10=0;
                                                                        $sumspen_n_10=0;
                                                                        foreach ($cvalue->daily_dat->list as $listkey => $listvalue) {
                                                                            if ($listkey<=$current_day_time) {
                                                                                foreach ($listvalue as $tkey => $tvalue) {
                                                                                    $sumalocate_l_10+=$tvalue->hour;
                                                                                    $sumspen_l_10+=$tvalue->spend;
                                                                                }
                                                                            }else if ($listkey>$current_day_time&&$listkey<=($current_day_time+(60*60*24*10))) {
                                                                                foreach ($listvalue as $tkey => $tvalue) {
                                                                                    $sumalocate_n_10+=$tvalue->hour;
                                                                                    $sumspen_n_10+=$tvalue->spend;
                                                                                }
                                                                            }else{
                                                                                break;
                                                                            }                                                                           
                                                                             
                                                                        }
                                                                       ?>
                                                                                <tr class="outer_tr">
                                                                                    <td id="<?=$cvalue->username?>" class="head_td" >
                                                                                        <?=$cvalue->nickname?>
                                                                                    </td>
                                                                                    <td class="head_td" >
                                                                                    <div class="progress progress-striped progress-warning active gray-bg">
                                                                                                  <div style="width: <?=($sumalocate_l_10/80*100)?>%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=($sumalocate_l_10/80*100)?>%</div>
                                                                                                </div>
                                                                                        
                                                                                    </td>
                                                                                    
                                                                                    <td class="head_td">
                                                                                        <div class="progress progress-striped progress-warning active gray-bg">
                                                                                                  <div style="width: <?=($sumalocate_n_10/80*100)?>%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=($sumalocate_n_10/80*100)?>%</div>
                                                                                                </div>
                                                                                    </td><?

                                                                                    $current_time=$start_car_time;
                                                                                        while ($current_time<=$end_car_time) {
                                                                                            $holiday="";
                                                                                            $in_range="";
                                                                                            $day_click="";
                                                                                            $over_cls="";
                                                                                            $current_cls="";
                                                                                            $event_letter="";
                                                                                            if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                                                                $holiday='holiday_td';
                                                                                            }
                                                                                            $sum_al_hour=0;     
                                                                                            $sum_sp_hour=0;            
                                                                                            if (isset($cvalue->daily_dat->list[$current_time])) { 
                                                                                                foreach ($cvalue->daily_dat->list[$current_time] as $timekey => $timevalue) {
                                                                                                     $sum_al_hour+=$timevalue->hour;
                                                                                                     $sum_sp_hour+=$timevalue->spend;
                                                                                                }
                                                                                                   
                                                                                            }
                                                                                            if ($sum_al_hour>=8) {
                                                                                                $over_cls="over_allocate";
                                                                                            }else if($sum_al_hour==0){
                                                                                                $over_cls="";
                                                                                            }else{
                                                                                                $over_cls="normal_allocate";
                                                                                            }
                                                                                            if ($current_time==$current_day_time) {
                                                                                                $current_cls="current_day";
                                                                                            }
                                                                                            if (isset($holiday_obj[$current_time])&&$holiday_obj[$current_time]->is_holiday=="y") {
                                                                                                $holiday='s_holiday_td';
                                                                                                $event_letter="H";
                                                                                            }
                                                                                            if (isset($user_leave_obj[$current_time][$cvalue->username])) {
                                                                                                $holiday='leave_td';
                                                                                                $event_letter="L";
                                                                                            }
                                                                                           ?>
                                                                                           <td class="<?=$current_cls?> <?=$holiday?> show_click <?=$over_cls?> " time="<?=$current_time?>">
                                                                                           <?
                                                                                           if ($sum_al_hour!=0) {
                                                                                               echo $sum_sp_hour."/".$sum_al_hour;
                                                                                           }else{
                                                                                                echo $event_letter;
                                                                                           }
                                                                                           ?>
                                                                                           </td>
                                                                                           <?
                                                                                           $current_time+=(60*60*24);
                                                                                        }// end while
                                                                                    ?>

                                                                                    </tr>
                                                                                    <?                                                                   
                                                                    }// end out
                                                                    ?>
                                                                </tbody>
                                                        </table>
                                                </div>
                                        </div>




















                                        <!-- tab2 -->
                                        <div class="tab-pane fade" id="tab_two">
                                            <div class="span12 no-margin-left carlendar-res" >
                                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                                                                <thead>
                                                                     <tr>                                                                     
                                                                     <th colspan="4" style="text-align: center;">Month -></th>
                                                                    <?
                                                                    $current_day_time=$ci->m_time->datepicker_to_unix(date("d/m/Y"));
                                                                    $start_car_time=$current_day_time-(60*60*24*7);
                                                                    $end_car_time=$current_day_time+(60*60*24*60);
                                                                    $current_time=$start_car_time;
                                                                        $cur_month=1000;
                                                                        while ($current_time<=$end_car_time) {
                                                                            if ($cur_month!=date("n",$current_time)) {
                                                                                $cur_month=date("n",$current_time);
                                                                                $cur_day=date("j",$current_time);
                                                                                $numday=date("t",$current_time);
                                                                                $colspan1=0;
                                                                                if ($cur_month==date("n",$end_car_time)) {
                                                                                    $numday=date("j",$end_car_time);
                                                                                    $colspan1=(int)$numday-(int)$cur_day+1;
                                                                                }else{
                                                                                    $colspan1=(int)$numday-(int)$cur_day+1;
                                                                                }
                                                                                
                                                                                ?>
                                                                                <th colspan="<?=$colspan1?>" style="text-align: center;"><?=date("M",$current_time)?></th>
                                                                                <?
                                                                            }                                                        
                                                                           $current_time+=(60*60*24);
                                                                        }
                                                                    ?>
                                                                    </tr>
                                                                    <tr>
                                                                    <th>Resource</th>
                                                                    <th>Project Name</th>
                                                                    <th>Task name</th>
                                                                    <th>Allocate/Spent</th>
                                                                    <?
                                                                    
                                                                    $current_time=$start_car_time;
                                                                        while ($current_time<=$end_car_time) {
                                                                            $holiday="";
                                                                            if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                                                $holiday='class="holiday_td"';
                                                                            }
                                                                           ?>
                                                                           <th <?=$holiday?>><?=date("d",$current_time)?></th>
                                                                           <?
                                                                           $current_time+=(60*60*24);
                                                                        }
                                                                    ?>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                   
                                                                    <?
                                                                    $project_name_flag=true;
                                                                    $res_name_flag=true;
                                                                    foreach ($all_child as $ckey => $cvalue) {
                                                                       $num_res_span=0;
                                                                       $wip_count=0;
                                                                       $task_count=0;
                                                                       $have_project=false;
                                                                       $have_work=false;
                                                                       $count_project=count($cvalue->project_wip);
                                                                        foreach ($cvalue->project_wip as $key => $value) {
                                                                            $have_project=true;
                                                                            $task_inner_count=0;
                                                                            foreach ($value->work_sheet as $wkey => $wvalue) {    
                                                                            if (count($wvalue->assign_obj->list)>0) {  
                                                                                $have_work=true;
                                                                                $task_inner_count+=1;                                                
                                                                                ?>
                                                                                <tr class="outer_tr">
                                                                                <?
                                                                                if ($res_name_flag) {
                                                                                    ?>
                                                                                    <td id="<?=$cvalue->username?>_detail" class="head_td" rowspan="1">
                                                                                        <?=$cvalue->nickname?>
                                                                                    </td>
                                                                                    <?
                                                                                    $res_name_flag=false;
                                                                                }
                                                                                if ($project_name_flag) {
                                                                                    $wip_count+=1;
                                                                                    ?>
                                                                                    <td id="<?=$cvalue->username?>_<?=$value->project_id?>_p_detail" class="head_td" rowspan="1">
                                                                                        <?=$value->project_name?>
                                                                                    </td>
                                                                                    <?
                                                                                    $project_name_flag=false;
                                                                                }
                                                                                $spend_percent=0;
                                                                                if ($wvalue->assign_obj->hour_amount!=0) {
                                                                                    $spend_percent=($wvalue->assign_obj->spend_amount/$wvalue->assign_obj->hour_amount)*100;
                                                                                }
                                                                                
                                                                                ?>
                                                                                    
                                                                                    <td class="head_td">
                                                                                        <?=$wvalue->work_name?>
                                                                                    </td>
                                                                                    <td class="head_td">
                                                                                        <div class="progress progress-striped progress-warning active gray-bg">
                                                                                          <div style="width: <?=$spend_percent?>%;" class="bar"></div>
                                                                                          <div class="text_in_bar"><?=$wvalue->assign_obj->spend_amount?>/<?=$wvalue->assign_obj->hour_amount?></div>
                                                                                        </div>
                                                                                    </td>
                                                                                <?
                                                                                    $current_time=$start_car_time;
                                                                                        while ($current_time<=$end_car_time) {
                                                                                            $holiday="";
                                                                                            $in_range="";
                                                                                            $day_click="";
                                                                                            $over_cls="";
                                                                                            $current_cls="";
                                                                                            if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                                                                $holiday='holiday_td';
                                                                                            }
                                                                                            $sum_al_hour=0;            
                                                                                            $sum_al_spend=0;
                                                                                            if (isset($wvalue->assign_obj->list[$current_time])) { 
                                                                                                    $sum_al_hour+=$wvalue->assign_obj->list[$current_time]->hour;
                                                                                                    $sum_al_spend+=$wvalue->assign_obj->list[$current_time]->spend;
                                                                                            }
                                                                                            if ($sum_al_hour>=8) {
                                                                                                $over_cls="over_allocate";
                                                                                            }else if($sum_al_hour==0){
                                                                                                $over_cls="";
                                                                                            }else{
                                                                                                $over_cls="normal_allocate";
                                                                                            }
                                                                                            if ($current_time==$current_day_time) {
                                                                                                $current_cls="current_day";
                                                                                            }
                                                                                            if ($wvalue->start<=$current_time&&$wvalue->end>=$current_time&&$holiday=="") {
                                                                                                $in_range='range_task';
                                                                                            }
                                                                                           ?>
                                                                                           <td class="<?=$current_cls?> <?=$holiday?> show_click <?=$over_cls?> <?=$in_range?> " time="<?=$current_time?>">
                                                                                           <?
                                                                                           if ($sum_al_hour!=0) {
                                                                                               echo $sum_al_spend."/".$sum_al_hour;
                                                                                           }
                                                                                           ?>
                                                                                           </td>
                                                                                           <?
                                                                                           $current_time+=(60*60*24);
                                                                                        }// end while
                                                                                    ?>

                                                                                    </tr>
                                                                                    <?
                                                                                    }// end if list
                                                                                   
                                                                            }// end work
                                                                            $task_count+=$task_inner_count;
                                                                            if ($task_inner_count==0) {
                                                                                $task_inner_count=1;
                                                                            }
                                                                            
                                                                            ?>
                                                                            <script type="text/javascript">
                                                                            $(function() {
                                                                                    $("#<?=$cvalue->username?>_<?=$value->project_id?>_p_detail").attr("rowspan","<?=$task_inner_count?>");
                                                                                });
                                                                            </script>
                                                                            <?
                                                                            $project_name_flag=true;
                                                                            
                                                                           
                                                                        }// end_wip
                                                                        $res_name_flag=true;



                                                                        if (!$have_project||!$have_work) {
                                                                            

                                                                        }// end if !$have_project||!$have_work
                                                                        if ($wip_count<$task_count) {
                                                                            $num_res_span+=$task_count;
                                                                        }else{
                                                                            $num_res_span+=$wip_count;
                                                                        }
                                                                        ?>
                                                                        <script type="text/javascript">            
                                                                        $(function() {       
                                                                            //<?=$task_count?>                                                     
                                                                            //<?=$wip_count?>     
                                                                            $("#<?=$cvalue->username?>_detail").attr("rowspan","<?=$num_res_span?>");
                                                                        });
                                                                        </script>
                                                                        <?
                                                                    }// end out
                                                                    ?>
                                                                </tbody>
                                                        </table>
                                                </div>
                                             
                                        </div>
                                    </div>
                                </div>
                           
                            <!-- /Tab -->
                        </div>
                    </div>
                </div>
                <!-- /block -->
            </div>
        </div>
    </div>
    <hr>
    <footer>
    </footer>
</div>
<!--/.fluid-container-->