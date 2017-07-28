<? $ci=& get_instance(); ?>
<style type="text/css">
.row-fluid .no-margin-left {
    margin-left: 0px;
}
hr{
    min-height: 2px;
    background-color: #CCCCCC;
    width: 100%;
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
.range_task{
    background-color: #cccccc;
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
   text-align: center;
   position: relative;
}
thead tr .head_th{
   white-space:nowrap;
   vertical-align: middle;
   text-align: center;
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
    text-align: left;
    position: absolute;
    height: 100%;
    width: 100%;
    padding-left: 20px;
    vertical-align: middle;
}
.text_in_bar_right{
    color: white;
    font-weight: bolder;
    text-align: right;
    position: absolute;
    height: 100%;
    width: 70%;
    vertical-align: middle;
}
.text_percent{
    color: white;
    font-weight: bolder;
    text-align: right;
    position: absolute;
    font-size: 24px;
    top: 18px;
    height: 100%;
    width: 95%;

}
.text_for_task{
    color: white;
    font-weight: bolder;
    text-align: right;
    position: absolute;
    font-size: 24px;
    top: 12px;
    height: 100%;
    width: 95%;

}
.progress{
    min-width: 100px
}
.no-minwidth{
    min-width: 0px
}
.head_td .rel{
    position: relative;
}
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">Traffic control</div>
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
                                        <?
                                        $first_p=true;
                                        foreach ($dat as $key => $value) {
                                            if ($first_p) {
                                                ?>
                                                <li class="active"><a href="#tab_<?=$value->id?>" data-toggle="tab"><?=$value->name?></a></li>
                                                <?
                                                $first_p=false;
                                            }else{
                                                 ?>                                                    
                                                <li><a href="#tab_<?=$value->id?>" data-toggle="tab"><?=$value->name?></a></li>
                                                <?   
                                            }
                                            
                                        }
                                        ?>
                                        
                                    </ul>
                                    <div id="myTabContent" class="tab-content">
                                        <?
                                        $first_p=true;
                                        foreach ($dat as $bkey => $bvalue) {
                                            if ($first_p) {
                                                ?>
                                                <div class="tab-pane active in" id="tab_<?=$bvalue->id?>">
                                                <?
                                                $first_p=false;
                                            }else{
                                                 ?>                                                    
                                                <div class="tab-pane fade" id="tab_<?=$bvalue->id?>">
                                                <?
                                            }
                                                ?>
                                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="example2">
                                                        <thead>
                                                            <tr>
                                                              <th rowspan="2" class="head_th">Project Name</th>
                                                              <th rowspan="2" class="">Client Service</th>
                                                              <th rowspan="2" class="head_th">Budget</th>
                                                              <th rowspan="2" class="head_th">Start to finish</th>
                                                              <th rowspan="2" class="head_th">Task</th>
                                                              <th rowspan="2" class="head_th">Resource</th> 
                                                              <th rowspan="2" class="head_th">Budget</th>
                                                              <?
                                                                    $current_day_time=$ci->m_time->datepicker_to_unix(date("d/m/Y"));
                                                                    $start_car_time=$current_day_time-(60*60*24*7);
                                                                    $end_car_time=$current_day_time+(60*60*24*40);
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
                                                            foreach ($bvalue->project as $pkey => $pvalue) {
                                                                $manager=$ci->m_user->get_user_by_login_name($pvalue->project_cs);
                                                                $global_row_span=3;
                                                                if ($pvalue->num_task>$pvalue->num_res) {
                                                                    $global_row_span=$pvalue->num_task;
                                                                }else{
                                                                    $global_row_span=$pvalue->num_res;
                                                                }
                                                             $is_enter_wvalue=true;
                                                             $global_flag=true;
                                                             $work_flag=true;
                                                             foreach ($pvalue->work_list as $wkey => $wvalue) {
                                                                $task_span=1;
                                                                if (count($wvalue->assign_detail)>1) {
                                                                    $task_span=count($wvalue->assign_detail);
                                                                }
                                                                $work_flag=true;
                                                                $is_enter_wvalue=false;
                                                                foreach ($wvalue->assign_detail as $as_key => $as_value) {
                                                                    ?>
                                                                     <tr class="outer_tr">
                                                                     <?
                                                                    if ($global_flag) {
                                                                        ?>
                                                                          <td class="head_td" rowspan="<?=$global_row_span?>"><? echo $pvalue->project_name; ?></td>
                                                                          <td class="head_td" rowspan="<?=$global_row_span?>"><? echo $manager->nickname; ?></td>
                                                                          <td class="head_td" rowspan="<?=$global_row_span?>">
                                                                          <div class="span12 rel">
                                                                              <div class="progress progress-striped active gray-bg">
                                                                                                  <div style="width: 100%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$pvalue->budget?></div>
                                                                                                </div>
                                                                                                <div class="progress progress-striped progress-warning active gray-bg">
                                                                                                  <div style="width: <?=($pvalue->budget_allocate/$pvalue->budget)*100?>%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$pvalue->budget_allocate?></div>
                                                                                                </div>
                                                                                                <div class="progress progress-striped progress-danger active gray-bg">
                                                                                                  <div style="width: <?=($pvalue->budget_spend/$pvalue->budget)*100?>%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$pvalue->budget_spend?></div>
                                                                                                </div>
                                                                                                <div class="text_percent"><?=number_format(($pvalue->budget_spend/$pvalue->budget)*100, 0, '.', ',')?>%</div>
                                                                          </div>
                                                                          
                                                                          </td>
                                                                          <td class="head_td" rowspan="<?=$global_row_span?>">
                                                                              <div class="no-margin-left" style="min-width:50px;height:40px;display:inline-block">
                                                                                    <?
                                                                                    $due_dat=($current_day_time-$pvalue->project_start)/($pvalue->project_end-$pvalue->project_start)*100;
                                                                                    ?>
                                                                                  <div class="progress progress-striped active gray-bg" style="min-width:50px;height:40px;">
                                                                                                  <div style="width: 100%;" class="bar"></div>
                                                                                                  <div class="text_in_bar" style="margin:10px 3% 3% 10px;padding:0px;"><?=number_format($due_dat, 0, '.', ',')?>%</div>
                                                                                                </div>
                                                                              </div>
                                                                              <div class="no-margin-left" style="min-width:120px;display:inline-block">
                                                                                  <?
                                                                                  echo $ci->m_time->unix_to_datepicker($pvalue->project_start)."<br>".$ci->m_time->unix_to_datepicker($pvalue->project_end);
                                                                                  ?>
                                                                              </div>
                                                                          </td>
                                                                            <?
                                                                            $global_flag=false;
                                                                    }
                                                                    if ($work_flag) {
                                                                         ?>
                                                                             <td class="head_td" rowspan="<?=$task_span?>"><?=$wvalue->work_name?></td>
                                                                         <?
                                                                     }

                                                                     ?>
                                                                     <td>
                                                                     <?
                                                                     $assign_this_time=$ci->m_work_sheet->get_res_assign_by_usn_and_time($as_value->username,$current_day_time);                                                                     
                                                                     $h_allocate=0;
                                                                     if (isset($as_value->assign_list->list[$current_day_time])) {
                                                                         $h_allocate=$as_value->assign_list->list[$current_day_time]->hour;
                                                                     }
                                                                     $hremain=(8-$assign_this_time->hour_amount)-$h_allocate;
                                                                     ?>
                                                                         <?=$as_value->nickname?>
                                                                                                <div class="progress progress-striped progress-success active gray-bg">
                                                                                                  <div style="width: <?=($h_allocate/8)*100?>%;" class="bar"></div>
                                                                                                  <div style="width: <?=($hremain/8)*100?>%;background-color: #9F66B7;" class="bar"></div>
                                                                                                  <div class="text_in_bar" style="padding-left:5px;"><?=$h_allocate?></div>
                                                                                                  <div class="text_in_bar_right"><?=8-$assign_this_time->hour_amount?></div>
                                                                                                </div>
                                                                     </td>
                                                                     <?
                                                                     if ($work_flag) {
                                                                         ?>
                                                                             <td class="head_td" rowspan="<?=$task_span?>">
                                                                                <div class="span12 rel">
                                                                                    <div class="progress progress-striped active gray-bg">
                                                                                                  <div style="width: 100%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$wvalue->budget_allocate?></div>
                                                                                                </div>
                                                                                                <?
                                                                                                 $spend_sub_alo=0;
                                                                                                if ($wvalue->budget_allocate!=0) {
                                                                                                    $spend_sub_alo=($wvalue->budget_spend/$wvalue->budget_allocate)*100;
                                                                                                }
                                                                                                
                                                                                                ?>
                                                                                                <div class="progress progress-striped progress-warning active gray-bg">
                                                                                                  <div style="width: <?=$spend_sub_alo?>%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$wvalue->budget_spend?></div>
                                                                                                </div>
                                                                                                <div class="text_for_task"><?=number_format($spend_sub_alo, 0, '.', ',')?>%</div>
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
                                                                                            if ($current_time==$current_day_time) {
                                                                                                $current_cls="current_day";
                                                                                            }
                                                                                            if ($wvalue->start<=$current_time&&$wvalue->end>=$current_time&&$holiday=="") {
                                                                                                $in_range='range_task';
                                                                                            }
                                                                                           ?>
                                                                                           <td rowspan="<?=$task_span?>" class="<?=$current_cls?> <?=$holiday?> show_click <?=$over_cls?> <?=$in_range?> " time="<?=$current_time?>">
                                                                                           
                                                                                           </td>
                                                                                           <?
                                                                                           $current_time+=(60*60*24);
                                                                                        }// end while
                                                                         $work_flag=false;
                                                                     }
                                                                     ?>
                                                                     </tr>
                                                                     <?
                                                                } // end ass list
                                                                
                                                             }// end work list
                                                             if ($is_enter_wvalue||$work_flag) {
                                                                 if (!$is_enter_wvalue) {
                                                                    $global_flag=true;
                                                                    foreach ($pvalue->work_list as $wkey => $wvalue) {
                                                                        $task_span=1;
                                                                        if (count($wvalue->assign_detail)>1) {
                                                                            $task_span=count($wvalue->assign_detail);
                                                                        }
                                                                        $work_flag=true;
                                                                            ?>
                                                                             <tr class="outer_tr">
                                                                             <?
                                                                            if ($global_flag) {
                                                                                ?>
                                                                                  <td class="head_td" rowspan="<?=$global_row_span?>"><? echo $pvalue->project_name; ?></td>
                                                                                  <td class="head_td" rowspan="<?=$global_row_span?>"><? echo $manager->nickname; ?></td>
                                                                                  <td class="head_td" rowspan="<?=$global_row_span?>">
                                                                                        <div class="span12 rel">
                                                                                                <div class="progress progress-striped active gray-bg">
                                                                                                  <div style="width: 100%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$pvalue->budget?></div>
                                                                                                </div>
                                                                                                <div class="progress progress-striped progress-warning active gray-bg">
                                                                                                  <div style="width: <?=($pvalue->budget_allocate/$pvalue->budget)*100?>%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$pvalue->budget_allocate?></div>
                                                                                                </div>
                                                                                                <div class="progress progress-striped progress-danger active gray-bg">
                                                                                                  <div style="width: <?=($pvalue->budget_spend/$pvalue->budget)*100?>%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$pvalue->budget_spend?></div>
                                                                                                </div>
                                                                                                <div class="text_percent"><?=number_format(($pvalue->budget_allocate/$pvalue->budget)*100, 0, '.', ',')?>%</div>
                                                                                        </div>
                                                                                  </td>
                                                                                  <td class="head_td" rowspan="<?=$global_row_span?>">
                                                                                      <div class="no-margin-left" style="min-width:50px;height:40px;display:inline-block">
                                                                                            <?
                                                                                            $due_dat=($current_day_time-$pvalue->project_start)/($pvalue->project_end-$pvalue->project_start)*100;
                                                                                            ?>
                                                                                          <div class="progress progress-striped active gray-bg" style="min-width:50px;height:40px;">
                                                                                                          <div style="width: 100%;" class="bar"></div>
                                                                                                          <div class="text_in_bar" style="margin:10px 3% 3% 10px;padding:0px;"><?=number_format($due_dat, 0, '.', ',')?>%</div>
                                                                                                        </div>
                                                                                      </div>
                                                                                      <div class="no-margin-left" style="min-width:120px;display:inline-block">
                                                                                          <?
                                                                                          echo $ci->m_time->unix_to_datepicker($pvalue->project_start)."<br>".$ci->m_time->unix_to_datepicker($pvalue->project_end);
                                                                                          ?>
                                                                                      </div>
                                                                                  </td>
                                                                                    <?
                                                                                    $global_flag=false;
                                                                            }
                                                                            if ($work_flag) {
                                                                                 ?>
                                                                                     <td class="head_td" rowspan="<?=$task_span?>"><?=$wvalue->work_name?></td>
                                                                                 <?
                                                                             }

                                                                             ?>
                                                                             <td class="head_td" rowspan="<?=$task_span?>">
                                                                                 No current Resource
                                                                             </td>
                                                                             <?
                                                                             if ($work_flag) {
                                                                                 ?>
                                                                                     <td class="head_td" rowspan="<?=$task_span?>">
                                                                                         <div class="span12 rel">
                                                                                            <div class="progress progress-striped active gray-bg">
                                                                                                          <div style="width: 100%;" class="bar"></div>
                                                                                                          <div class="text_in_bar"><?=$wvalue->budget_allocate?></div>
                                                                                                        </div>
                                                                                                        <?
                                                                                                        $spend_sub_alo=0;
                                                                                                        if ($wvalue->budget_allocate!=0) {
                                                                                                            $spend_sub_alo=($wvalue->budget_spend/$wvalue->budget_allocate)*100;
                                                                                                        }
                                                                                                        
                                                                                                        ?>
                                                                                                        <div class="progress progress-striped progress-warning active gray-bg">
                                                                                                          <div style="width: <?=$spend_sub_alo?>%;" class="bar"></div>
                                                                                                          <div class="text_in_bar"><?=$wvalue->budget_spend?></div>
                                                                                                        </div>
                                                                                                        <div class="text_for_task"><?=number_format($spend_sub_alo, 0, '.', ',')?>%</div>
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
                                                                                                    if ($current_time==$current_day_time) {
                                                                                                        $current_cls="current_day";
                                                                                                    }
                                                                                                    if ($wvalue->start<=$current_time&&$wvalue->end>=$current_time&&$holiday=="") {
                                                                                                        $in_range='range_task';
                                                                                                    }
                                                                                                   ?>
                                                                                                   <td rowspan="<?=$task_span?>" class="<?=$current_cls?> <?=$holiday?> show_click <?=$over_cls?> <?=$in_range?> " time="<?=$current_time?>">
                                                                                                   
                                                                                                   </td>
                                                                                                   <?
                                                                                                   $current_time+=(60*60*24);
                                                                                                }// end while
                                                                                 $work_flag=false;
                                                                             }
                                                                             ?>
                                                                             </tr>
                                                                             <?                                                                        
                                                                     }// end work list in if global flag
                                                                 }else{
                                                                            ?>
                                                                             <tr class="outer_tr">
                                                                                  <td class="head_td" rowspan="<?=$global_row_span?>"><? echo $pvalue->project_name; ?></td>
                                                                                  <td class="head_td" rowspan="<?=$global_row_span?>"><? echo $manager->nickname; ?></td>
                                                                                  <td class="head_td" rowspan="<?=$global_row_span?>">
                                                                                        <div class="span12 rel">
                                                                                                <div class="progress progress-striped active gray-bg">
                                                                                                  <div style="width: 100%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$pvalue->budget?></div>
                                                                                                </div>
                                                                                                <div class="progress progress-striped progress-warning active gray-bg">
                                                                                                    <?
                                                                                                    $percent_val1=0;
                                                                                                    $percent_val2=0;
                                                                                                    $percent_val3=0;
                                                                                                    if ($pvalue->budget!=0) {
                                                                                                        $percent_val1=($pvalue->budget_allocate/$pvalue->budget)*100;
                                                                                                        $percent_val2=($pvalue->budget_spend/$pvalue->budget)*100;
                                                                                                    }
                                                                                                    ?>
                                                                                                  <div style="width: <?=$percent_val1?>%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$pvalue->budget_allocate?></div>
                                                                                                </div>
                                                                                                <div class="progress progress-striped progress-danger active gray-bg">
                                                                                                  <div style="width: <?=$percent_val2?>%;" class="bar"></div>
                                                                                                  <div class="text_in_bar"><?=$pvalue->budget_spend?></div>
                                                                                                </div>
                                                                                                <div class="text_percent"><?=number_format($percent_val1, 0, '.', ',')?>%</div>
                                                                                        </div>
                                                                                  </td>
                                                                                  <td class="head_td" rowspan="<?=$global_row_span?>">
                                                                                      <div class="no-margin-left" style="min-width:50px;height:40px;display:inline-block">
                                                                                            <?
                                                                                            $due_dat=($current_day_time-$pvalue->project_start)/($pvalue->project_end-$pvalue->project_start)*100;
                                                                                            ?>
                                                                                          <div class="progress progress-striped active gray-bg" style="min-width:50px;height:40px;">
                                                                                                          <div style="width: 100%;" class="bar"></div>
                                                                                                          <div class="text_in_bar" style="margin:10px 3% 3% 10px;padding:0px;"><?=number_format($due_dat, 0, '.', ',')?>%</div>
                                                                                                        </div>
                                                                                      </div>
                                                                                      <div class="no-margin-left" style="min-width:120px;display:inline-block">
                                                                                          <?
                                                                                          echo $ci->m_time->unix_to_datepicker($pvalue->project_start)."<br>".$ci->m_time->unix_to_datepicker($pvalue->project_end);
                                                                                          ?>
                                                                                      </div>
                                                                                  </td>
                                                                                     <td class="head_td" rowspan="<?=$global_row_span?>">No current Task</td>
                                                                              
                                                                             <td class="head_td" rowspan="<?=$global_row_span?>">
                                                                                 No current Resource
                                                                             </td>
                                                                                    <td class="head_td" rowspan="1">
                                                                                         <div class="span12 rel">
                                                                                            <div class="progress progress-striped active gray-bg">
                                                                                                          <div style="width: 0%;" class="bar"></div>
                                                                                                          <div class="text_in_bar">0</div>
                                                                                                        </div>
                                                                                                        <div class="progress progress-striped progress-warning active gray-bg">
                                                                                                          <div style="width: 0%;" class="bar"></div>
                                                                                                          <div class="text_in_bar">0</div>
                                                                                                        </div>
                                                                                                        <div class="text_for_task"><?=number_format(0, 0, '.', ',')?>%</div>
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
                                                                                                    if ($current_time==$current_day_time) {
                                                                                                        $current_cls="current_day";
                                                                                                    }
                                                                                                   ?>
                                                                                                   <td rowspan="<?=$global_row_span?>" class="<?=$current_cls?> <?=$holiday?> show_click <?=$over_cls?> <?=$in_range?> " time="<?=$current_time?>">
                                                                                                   
                                                                                                   </td>
                                                                                                   <?
                                                                                                   $current_time+=(60*60*24);
                                                                                                }// end while
                                                                             ?>
                                                                             </tr>
                                                                    <?

                                                                 }// end else glo flag
                                                             }// $global_flag||$work_flag
                                                             
                                                            }// end pvalue
                                                            ?>                                                                         
                                                        </tbody>
                                                    </table>
                                                </div>
                                            
                                            <?
                                        }
                                        ?>
                                        

                                        <!-- tab2 -->
                                        
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