<? $ci=& get_instance(); 
$manager=$ci->m_user->get_user_by_login_name($project->project_cs);
?>
<style type="text/css">
.row-fluid .no-margin-left {
    margin-left: 0px;
}
hr{
    min-height: 2px;
    background-color: #CCCCCC;
    width: 100%;
}
.la-head{
  font-family: tahoma;
  font-weight: bold;
  font-size: 16px;
}
.la-head.center{
  text-align: center;
}
.la-detail{
  font-family: tahoma;
  font-weight: normal;
  font-size: 16px;
}
.td-middle{
  vertical-align: middle!important;
}
</style>
<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<link rel="stylesheet" href="<?echo site_url();?>css/style.css">
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
    <script src="<?echo site_url();?>js/upload/vendor/jquery.ui.widget.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="<?echo site_url();?>js/upload/jquery.iframe-transport.js"></script>
    <!-- The basic File Upload plugin -->
    <script src="<?echo site_url();?>js/upload/jquery.fileupload.js"></script>
<div class="container-fluid">
    <? $ci=& get_instance(); ?>
    <style type="text/css">
    .row-fluid .no-margin-left {
        margin-left: 0px;
    }
    </style>
    <link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
    <link rel="stylesheet" href="<?echo site_url();?>css/style.css">
    <div class="container-fluid">
        <style type="text/css">
        th {
            cursor: pointer;
        }
        
        .lowfont {
            font-size: 12px
        }
        
        .no-margin-left {
            margin-left: 0px!important;
        }

        </style>
        <div class="row-fluid">
            <div class="span12">
                <!-- block -->
                <div class="block">
                    <style type="text/css">
                    td[ng-click] {
                        cursor: pointer;
                    }
                    </style>
                    
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">
                            <div class="control-group">
                                <label class="la-head">PROJECT AND BUDGET DETAIL</label>
                            </div>
                        </div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12 no-margin-left">
                                    <div class="span6">
                                      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped lowfont">
                                        <tr>
                                          <td class="la-head">PROJECT NAME</td>
                                          <td class="la-detail"><?=$project->project_name?></td>
                                          </tr>
                                          <tr>
                                          <td class="la-head">JOB NUMBER</td>
                                          <td class="la-detail"><?
                                        $project_value=0;
                                        foreach ($pce_doc as $key => $pce) {
                                          $cur_time=$pce->id;
                                          $project_value+=$pce->pce_amount;
                                          $oc_1_list=$ci->m_oc->get_all_oc_by_pce_id($pce->id);
                                          foreach ($oc_1_list as $key => $oc) {
                                            echo $oc->oc_no." ";
                                          }
                                        }
                                        ?></td>
                                        </tr>
                                        <tr>
                                          <td class="la-head">CLIENT SERVICE</td>
                                          <td class="la-detail"><?=$manager->nickname?></td>
                                        </tr>
                                        <tr>
                                          <td class="la-head">PROJECT VALUE</td>
                                          <td class="la-detail"><?=number_format($project_value, 1, '.', ',')?></td>
                                        </tr>
                                        <tr>
                                          <td class="la-head">TOTAL BUDGET</td>
                                          <td class="la-detail"><?
                                        $total_budget=0;
                                        foreach ($r_sheet as $t_key => $task) {
                                          $total_budget+=$task->approve_budget;
                                        }
                                        echo number_format($total_budget, 1, '.', ',')." Hrs.";
                                        ?></td>
                                        </tr>
                                        <tr>
                                          <td class="la-head">TARGET DURATION</td>
                                          <td class="la-detail"><?
                                          $target_dutation=0;
                                          $actual_dutation=0;
                                                    $lenght_time=(int)(($project->project_end-$project->project_start)/(60*60*24));
                                                    if ($lenght_time==0) {
                                                      $lenght_time=1;
                                                    } 
                                                    $target_dutation=$lenght_time;                                              
                                                    echo $lenght_time." Days ";
                                                    ?></td>
                                        </tr>
                                        <tr>
                                          <td class="la-head">ACTUAL DURATION</td>
                                          <td class="la-detail"><?
                                                    $last_work_sheet=$ci->m_work_sheet->get_last_end_time_work_sheet_by_project_id($project->project_id);
                                                    if (isset($last_work_sheet->end)&&$last_work_sheet->end>0) {
                                                      $lenght_time=(int)(($last_work_sheet->end-$project->project_start)/(60*60*24));
                                                      if ($lenght_time==0) {
                                                        $lenght_time=1;
                                                      }    
                                                    }else{
                                                      $lenght_time=0;
                                                    }
                                                    $actual_dutation=$lenght_time;                                           
                                                    echo $lenght_time." Days ";
                                                    ?></td>
                                        </tr>
                                      </table>
                                    </div>
                                    <div class="span6">
                                      <label class="la-head">FINANCIAL NOTE :</label>
                                      <textarea disabled rows="10" style="width:80%"><?=$project->finan_note?></textarea>
                                    </div> 
                                </div>
                                <div class="span12 no-margin-left">
                                </div>
                                <div class="span12 no-margin-left">
                                  <div class="span8">
                                    <label class="la-head">BUDGET APPROVAL - INTERNAL :</label>
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered lowfont">
                                    <tr>
                                      <th>TASK TYPE</th>
                                      <th>RESOURCE</th>
                                      <th>BUDGET</th>
                                      <th>CHARGE</th>
                                    </tr>
                                        <?
                                        $total_hr = 0;
                                        $total_chg = 0;
                                        $countnum=0;
                                        foreach ($r_sheet as $t_key => $task) {
                                        $countnum+=1;    
                                        ?>
                                        <tr>
                                            <td>
                                                    
                                                    <?=$r_sheet[$t_key]->type_obj->name?>
                                                    <?$approve_budget=$r_sheet[$t_key]->approve_budget;?> 
                                                    <?$total_hr+=$approve_budget;?>
                                                    <?$total_chg+=$approve_budget*$r_sheet[$t_key]->type_obj->hour_rate;?>
                                                    
                                            </td>
                                            <td><?
                                                $allow_list=$ci->m_Rsheet->get_allow_list_by_r_id($task->r_id);
                                                $al_num=count($allow_list);
                                                foreach ($allow_list as $akey => $avalue) {
                                                   $auser=$this->m_user->get_user_by_login_name($avalue->resource_usn);
                                                   if ($al_num==$akey+1) {
                                                       echo $auser->nickname;
                                                   }else{
                                                        echo $auser->nickname.",";
                                                    }
                                                }
                                                ?></td>
                                            <td><p> <?=$r_sheet[$t_key]->approve_budget?></p></td>
                                            <td><?echo $approve_budget*$r_sheet[$t_key]->type_obj->hour_rate;?></td>
                                        </tr>
                                        <?
                                        }
                                        ?>
                                        <tr id="last_tr">
                                            <td colspan="2"></td>
                                            <td class="total"><?echo number_format($total_hr, 1, '.', ',');?></td>
                                            <td class="total"><?echo number_format($total_chg, 1, '.', ',');?></td>
                                        </tr>
                                      </table>
                                    </div>
                                </div>
                                <div class="span12 no-margin-left">
                                </div>
                                <div class="span12 no-margin-left">
                                  <div class="span8">
                                    <label class="la-head">BUDGET APPROVAL - OUTSOURCE :</label>
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered lowfont">
                                    <tr>
                                      <th colspan="2"></th>
                                      <th colspan="3" style="text-align: center;">BUDGET</th>
                                    </tr>
                                    <tr>
                                      <th></th>
                                      <th>Description</th>
                                      <th>Budget Cost</th>
                                      <th>Charge</th>
                                      <th>Margin</th>
                                    </tr>
                                        <?
                                        $total_cost = 0;
                                        $total_charge = 0;
                                        $countnum=0;
                                        foreach ($pce_doc as $key => $pce) {
                                          foreach ($pce->outsource as $outsource_key => $outsource) {
                                            $countnum+=1;    
                                            $total_cost+=$outsource->qt_cost;
                                            $total_charge+=$outsource->qt_charge;
                                            ?>
                                            <tr>
                                                <td><?=$countnum?></td>
                                                <td><?=$outsource->qt_no." ".$outsource->qt_des?></td>
                                                <td><?=number_format($outsource->qt_cost, 1, '.', ',')?></td>
                                                <td><?=number_format($outsource->qt_charge, 1, '.', ',')?></td>
                                                <td><?
                                                if ($outsource->qt_charge==0) {
                                                  echo "-100";
                                                }else{
                                                  echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_charge*100,2);
                                                }
                                                ?>%</td>
                                            </tr>
                                            <?
                                          }
                                        }
                                        ?>
                                        <tr id="last_tr">
                                            <td colspan="2"></td>
                                            <td class="total"><?echo number_format($total_cost, 1, '.', ',');?></td>
                                            <td class="total"><?echo number_format($total_charge, 1, '.', ',');?></td>
                                            <td class="total"><? 
                                            if ($total_charge==0) {
                                              echo "0";
                                            }else{
                                              echo number_format("".($total_charge-$total_cost)/$total_charge*100,2);
                                            }
                                            ?>%</td>
                                        </tr>
                                      </table>
                                    </div>
                                </div>
                                <div class="span12 no-margin-left">
                                </div>
                        
                        
                    </div>


                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">
                            <div class="control-group">
                                <label class="la-head">BUDGET MANAGEMENT PERFORMANCES</label>
                            </div>
                        </div>
                    </div>
                    <div class="block-content collapse in">                        
                                <div class="span12 no-margin-left">
                                  <div class="span10">
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered lowfont">
                                    <tr>
                                      <th class="la-head" colspan="3">RESOURCE SUMMARY</th>
                                      <th class="la-head center" colspan="3">ALLOCATION HOUR</th>
                                      <th class="la-head center" colspan="3">Spent</th>
                                    </tr>
                                    <tr>
                                      <th colspan="2"></th>
                                      <th>TITLE</th>
                                      <th>TOTAL</th>
                                      <th>BUDGET</th>
                                      <th>OVER</th>
                                      <th>TOTAL</th>
                                      <th>on BUDGET</th>
                                      <th>on TOTAL</th>
                                    </tr>
                                    <?
                                    $res_sumary_list = array();
                                    $countnum=0;
                                    foreach ($work_sheet as $key => $value) {
                                      
                                      foreach ($value->resource as $key2 => $value2) {
                                        if (!isset($res_sumary_list[$value2->username])) {
                                          $res_sumary_list[$value2->username]=new stdClass();
                                          $res_sumary_list[$value2->username]->nickname=$value2->nickname;
                                          if (isset($value2->position->name)) {
                                            $res_sumary_list[$value2->username]->position=$value2->position->name;
                                          }else{
                                            $res_sumary_list[$value2->username]->position="ไม่พบตำแหน่ง";
                                          }                                          
                                          $res_sumary_list[$value2->username]->hour_amount=0;
                                          $res_sumary_list[$value2->username]->over_amount=0;
                                          $res_sumary_list[$value2->username]->spend_amount=0;
                                          $res_sumary_list[$value2->username]->hour_amount_rate=0;
                                          $res_sumary_list[$value2->username]->over_amount_rate=0;
                                          $res_sumary_list[$value2->username]->spend_amount_rate=0;
                                          $res_sumary_list[$value2->username]->hour_amount+=$value2->assign_list->hour_amount;
                                          $res_sumary_list[$value2->username]->over_amount+=$value2->assign_list->over_amount;
                                          $res_sumary_list[$value2->username]->spend_amount+=$value2->assign_list->spend_amount;
                                          $res_sumary_list[$value2->username]->hour_amount_rate+=$value->type_obj->hour_rate*$value2->assign_list->hour_amount;
                                          $res_sumary_list[$value2->username]->over_amount_rate+=$value->type_obj->hour_rate*$value2->assign_list->over_amount;
                                          $res_sumary_list[$value2->username]->spend_amount_rate+=$value->type_obj->hour_rate*$value2->assign_list->spend_amount;
                                        }else{
                                          $res_sumary_list[$value2->username]->hour_amount+=$value2->assign_list->hour_amount;
                                          $res_sumary_list[$value2->username]->over_amount+=$value2->assign_list->over_amount;
                                          $res_sumary_list[$value2->username]->spend_amount+=$value2->assign_list->spend_amount;
                                          $res_sumary_list[$value2->username]->hour_amount_rate+=$value->type_obj->hour_rate*$value2->assign_list->hour_amount;
                                          $res_sumary_list[$value2->username]->over_amount_rate+=$value->type_obj->hour_rate*$value2->assign_list->over_amount;
                                          $res_sumary_list[$value2->username]->spend_amount_rate+=$value->type_obj->hour_rate*$value2->assign_list->spend_amount;
                                        }
                                        
                                      }
                                    }
                                    
                                    $charge_budget_allocate=0;
                                    $charge_budget_spent=0;
                                    $hour_budget_allocate=0;
                                    $hour_budget_spent=0;
                                    foreach ($res_sumary_list as $key2 => $value2) {
                                      $countnum+=1;
                                        ?>
                                        <tr>
                                          <td><?=$countnum?></td>
                                          <td><?=$value2->nickname?></td>
                                          <td><?=$value2->position?></td>
                                          <td><?=number_format($value2->hour_amount, 1, '.', ',')?></td>
                                          <td><?=number_format($value2->hour_amount-$value2->over_amount, 1, '.', ',')?></td>
                                          <td><?=number_format($value2->over_amount, 1, '.', ',')?></td>
                                          <td><?=number_format($value2->spend_amount, 1, '.', ',')?></td>
                                          <td><?
                                          if (($value2->hour_amount-$value2->over_amount)==0) {
                                            echo "0";
                                          }else{
                                            echo number_format($value2->spend_amount/($value2->hour_amount-$value2->over_amount)*100, 2, '.', ',');
                                          }
                                          ?>%</td>
                                          <td><?
                                          if (($value2->hour_amount)==0) {
                                            echo "0";
                                          }else{
                                            echo number_format($value2->spend_amount/($value2->hour_amount)*100, 2, '.', ',');
                                          }
                                          ?>%</td>
                                        </tr>
                                        <?
                                        $charge_budget_allocate+=$value2->hour_amount_rate;
                                        $charge_budget_spent+=$value2->spend_amount_rate;
                                        $hour_budget_allocate+=$value2->hour_amount;
                                        $hour_budget_spent+=$value2->spend_amount;
                                    }
                                    ?>
                                        
                                      </table>
                                    </div>
                                </div>
                                <div class="span12 no-margin-left">
                                </div>
                                <div class="span12 no-margin-left">
                                  <div class="span10">
                                    <label class="la-head">PROJECT BUDGET SUMMARY (FROM WORKSHEET) :</label>
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered lowfont">
                                    <tr>
                                      <th colspan="4"></th>
                                      <th class="la-head center" colspan="3">ALLOCATION HOUR</th>
                                      <th colspan="3"></th>
                                    </tr>
                                    <tr>
                                      <th>NO</th>
                                      <th>Task</th>
                                      <th>Type</th>
                                      <th>Resoruce</th>
                                      <th>TOTAL</th>
                                      <th>BUDGET</th>
                                      <th>OVER</th>
                                      <th>SPENT</th>
                                      <th>SPENT RATE</th>
                                      <th>EFFICIENCY</th>
                                    </tr>
                                    <?
                                    $countnum=0;
                                    $total_hour_amount=0;
                                    $total_hour_budget=0;
                                    $total_hour_spent=0;
                                    $total_hour_spent=0;
                                    $total_hour_over=0;
                                    foreach ($work_sheet as $key => $value) {
                                      $countnum+=1;
                                      $first=true;
                                      foreach ($value->resource as $key2 => $value2) {
                                        $total_hour_amount+=$value2->assign_list->hour_amount;
                                          $total_hour_budget+=$value2->assign_list->hour_amount-$value2->assign_list->over_amount;
                                          $total_hour_spent+=$value2->assign_list->spend_amount;
                                          $total_hour_over+=$value2->assign_list->over_amount;
                                        if ($first) {
                                          $first=false;
                                          
                                          ?>
                                          <tr>
                                            <td rowspan="<?=count($value->resource)?>" class="td-middle"><?=$countnum?></td>
                                            <td rowspan="<?=count($value->resource)?>" class="td-middle"><?=$value->work_name?></td>
                                            <td rowspan="<?=count($value->resource)?>" class="td-middle"><?=$value->type_obj->name?></td>
                                            <td><?=$value2->nickname?></td>
                                            <td><?=number_format($value2->assign_list->hour_amount, 1, '.', ',')?></td>
                                            <td><?=number_format($value2->assign_list->hour_amount-$value2->assign_list->over_amount, 1, '.', ',')?></td>
                                            <td><?=number_format($value2->assign_list->over_amount, 1, '.', ',')?></td>
                                            <td><?=number_format($value2->assign_list->spend_amount, 1, '.', ',')?></td>
                                            <td><?
                                            if (($value2->assign_list->hour_amount-$value2->assign_list->over_amount)==0) {
                                              echo "0";
                                            }else{
                                              echo number_format($value2->assign_list->spend_amount/($value2->assign_list->hour_amount-$value2->assign_list->over_amount)*100, 2, '.', ',');
                                            }
                                            ?>%</td>
                                            <td><?
                                            if ($value2->assign_list->hour_amount==0) {
                                              echo "0";
                                            }else{
                                              echo number_format($value2->assign_list->spend_amount/($value2->assign_list->hour_amount)*100, 2, '.', ',');
                                            }
                                            ?>%</td>
                                          </tr>
                                          <?
                                        }else{
                                          ?>
                                          <tr>
                                            <td><?=$value2->nickname?></td>
                                            <td><?=number_format($value2->assign_list->hour_amount, 1, '.', ',')?></td>
                                            <td><?=number_format($value2->assign_list->hour_amount-$value2->assign_list->over_amount, 1, '.', ',')?></td>
                                            <td><?=number_format($value2->assign_list->over_amount, 1, '.', ',')?></td>
                                            <td><?=number_format($value2->assign_list->spend_amount, 1, '.', ',')?></td>
                                            <td><?
                                            if (($value2->assign_list->hour_amount-$value2->assign_list->over_amount)==0) {
                                              echo "0";
                                            }else{
                                              echo number_format($value2->assign_list->spend_amount/($value2->assign_list->hour_amount-$value2->assign_list->over_amount)*100, 2, '.', ',');
                                            }
                                            ?>%</td>
                                            <td><?
                                            if ($value2->assign_list->hour_amount==0) {
                                              echo "0";
                                            }else{
                                              echo number_format($value2->assign_list->spend_amount/($value2->assign_list->hour_amount)*100, 2, '.', ',');
                                            }
                                            ?>%</td>
                                          </tr>
                                          <?
                                        }
                                        
                                      }
                                      

                                    }
                                    ?>
                                        <tr>
                                          <td></td>
                                          <td></td>
                                          <td></td>
                                          <td></td>
                                          <td><?=number_format($total_hour_amount, 1, '.', ',')?></td>
                                          <td><?=number_format($total_hour_budget, 1, '.', ',')?></td>
                                          <td><?=number_format($total_hour_over, 1, '.', ',')?></td>
                                          <td><?=number_format($total_hour_spent, 1, '.', ',')?></td>
                                          <td><?
                                            if (($total_hour_budget)==0) {
                                              echo "0";
                                            }else{
                                              echo number_format($total_hour_spent/($total_hour_budget)*100, 2, '.', ',');
                                            }
                                            ?>%</td>
                                          <td><?
                                            if ($total_hour_amount==0) {
                                              echo "0";
                                            }else{
                                              echo number_format($total_hour_spent/($total_hour_amount)*100, 2, '.', ',');
                                            }
                                            ?>%</td>
                                        </tr>
                                      </table>
                                    </div>
                                </div>
                                <div class="span12 no-margin-left">
                                </div>
                                <div class="span12 no-margin-left">
                                  <div class="span9">
                                    <label class="la-head">OUTSOURCE SUMMARY :</label>
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered lowfont">
                                    <tr>
                                      <th colspan="2"></th>
                                      <th colspan="3" style="text-align: center;">BUDGET</th>
                                      <th colspan="2" style="text-align: center;">ACTUAL</th>
                                    </tr>
                                    <tr>
                                      <th></th>
                                      <th>Description</th>
                                      <th>Budget Cost</th>
                                      <th>Charge</th>
                                      <th>Margin</th>
                                      <th>Paid</th>
                                      <th>Margin</th>
                                    </tr>
                                        <?
                                        $total_cost = 0;
                                        $total_charge = 0;
                                        $total_paid = 0;
                                        $countnum=0;
                                        foreach ($pce_doc as $key => $pce) {
                                          foreach ($pce->outsource as $outsource_key => $outsource) {
                                            $countnum+=1;    
                                            $total_cost+=$outsource->qt_cost;
                                            $total_charge+=$outsource->qt_charge;
                                            ?>
                                            <tr>
                                                <td><?=$countnum?></td>
                                                <td><?=$outsource->qt_no." ".$outsource->qt_des?></td>
                                                <td><?=number_format($outsource->qt_cost, 1, '.', ',')?></td>
                                                <td><?=number_format($outsource->qt_charge, 1, '.', ',')?></td>
                                                <td><?
                                                if ($outsource->qt_charge==0) {
                                                  echo "0";
                                                }else{
                                                  echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_charge*100,2);
                                                }
                                                ?>%</td>
                                                <?
                                                $paid_amount=0;
                                                foreach ($outsource->bill_paid as $paid_key => $paid_value) {
                                                  $paid_amount+=$paid_value->amount;
                                                }
                                                $total_paid+=$paid_amount;
                                                ?>
                                                <td><?=number_format($paid_amount, 1, '.', ',')?></td>
                                                <td><?
                                                if ($paid_amount==0) {
                                                  echo "0";
                                                }else{
                                                  echo number_format("".($outsource->qt_charge-$paid_amount)/$outsource->qt_charge*100,2);
                                                }
                                                ?>%</td>
                                            </tr>
                                            <?
                                          }
                                        }
                                        ?>
                                        <tr id="last_tr">
                                            <td colspan="2"></td>
                                            <td class="total"><?echo number_format($total_cost, 1, '.', ',');?></td>
                                            <td class="total"><?echo number_format($total_charge, 1, '.', ',');?></td>
                                            <td class="total"><? 
                                            if ($total_charge==0) {
                                              echo "0";
                                            }else{
                                              echo number_format("".($total_charge-$total_cost)/$total_charge*100,2);
                                            }
                                            ?>%</td>
                                            <td><?=number_format($total_paid, 1, '.', ',')?></td>
                                            <td class="total"><? 
                                            if ($total_paid==0) {
                                              echo "0";
                                            }else{
                                              echo number_format("".($total_charge-$total_paid)/$total_paid*100,2);
                                            }
                                            ?>%</td>
                                        </tr>
                                      </table>
                                    </div>
                                </div>
                                <div class="span12 no-margin-left">
                                </div>
                    </div>

                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">
                            <div class="control-group">
                                <label class="la-head">PROJECT SUMMARY</label>
                            </div>
                        </div>
                    </div>
                    <div class="block-content collapse in">     
                                <div class="span12 no-margin-left">
                                  <div class="span10">
                                    <label class="la-head">INTERNAL BUDGET SUMMARY</label>
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered lowfont">
                                    <tr>
                                      <th></th>
                                      <th>Charge Budget</th>
                                      <th>% to Budget</th>
                                      <th>Hour Budget</th>
                                      <th>% to Budget</th>
                                    </tr>
                                      <tr>
                                        <td>APPROVED BUDGET</td>
                                        <td><?=number_format($total_chg, 1, '.', ',')?></td>
                                        <td></td>
                                        <td><?=number_format($total_hr, 1, '.', ',')?></td>
                                        <td></td>
                                      </tr>
                                      <tr>
                                        <td>ALLOCATION</td>
                                        <td><?=number_format($charge_budget_allocate, 1, '.', ',')?></td>
                                        <td><?if($total_chg==0){
                                          echo "0";
                                          }else{
                                            echo number_format($charge_budget_allocate/$total_chg*100, 2, '.', ',');
                                          }
                                            ?>%</td>
                                        <td><?=number_format($hour_budget_allocate, 1, '.', ',')?></td>
                                        <td><?if($total_hr==0){
                                          echo "0";
                                          }else{
                                            echo number_format($hour_budget_allocate/$total_hr*100, 2, '.', ',');
                                          }
                                            ?>%</td>
                                      </tr>
                                      <tr>
                                        <td>SPENT</td>
                                        <td><?=number_format($charge_budget_spent, 1, '.', ',')?></td>
                                        <td><?if($total_chg==0){
                                          echo "0";
                                          }else{
                                            echo number_format($charge_budget_spent/$total_chg*100, 2, '.', ',');
                                          }
                                            ?>%</td>
                                        <td><?=number_format($hour_budget_spent, 1, '.', ',')?></td>
                                        <td><?if($total_hr==0){
                                          echo "0";
                                          }else{
                                            echo number_format($hour_budget_spent/$total_hr*100, 2, '.', ',');
                                          }
                                            ?>%</td>
                                      </tr>
                                        
                                      </table>
                                    </div>
                                </div>
                                <div class="span12 no-margin-left">
                                </div>
                                <div class="span12 no-margin-left">
                                  <div class="span4">
                                    <label class="la-head">PROJECT PERFORMANCE SUMMARY</label>
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered lowfont">
                                    <tr>
                                      <td>RESOURCE USAGE</td>
                                      <td><?=number_format(count($res_sumary_list))?></td>
                                      <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                      <td>BUDGET ALLOCATION</td>
                                      <td><?
                                          $on_budget=0;
                                          if($total_hr==0){
                                            $on_budget=0;
                                          }else{
                                            $on_budget=number_format((($hour_budget_allocate/$total_hr)-1)*100, 2, '.', ',');
                                          }
                                          if ($on_budget<0) {
                                            echo "On Budget";
                                          }else{
                                            echo "Over Budget";
                                          }
                                            ?></td>
                                          
                                      <td colspan="2"><?=number_format($on_budget, 2, '.', ',')?>%</td>
                                    </tr>
                                    <tr>
                                      <td>BUDGET SPENT</td>
                                      <td><?
                                          $on_spent=0;
                                          if($total_hr==0){
                                            $on_spent=0;
                                          }else{
                                            $on_spent=number_format((($hour_budget_spent/$total_hr)-1)*100, 2, '.', ',');
                                          }
                                          if ($on_spent<0) {
                                            echo "On Budget";
                                          }else{
                                            echo "Over Budget";
                                          }
                                            ?></td>
                                      <td colspan="2"><?=number_format($on_spent, 2, '.', ',')?>%</td>
                                    </tr>
                                    <tr>
                                      <td>EFFICIENCY - HRs</td>
                                      <td><?
                                          $on_ef_hr=0;
                                          if($hour_budget_spent==0){
                                            $on_ef_hr=0;
                                          }else{
                                            $on_ef_hr=number_format((($total_hr/$hour_budget_spent)-1)*100, 2, '.', ',');
                                          }
                                          if ($on_ef_hr<0) {
                                            echo "Inefficient";
                                          }else{
                                            echo "Efficient";
                                          }
                                            ?><input type="hidden" total-hr="<?=$total_hr?>/<?=$hour_budget_spent?>"></td>
                                      <td colspan="2"><?=$on_ef_hr?>%</td>
                                    </tr>
                                    <tr>
                                      <td>EFFICIENCY - FINANCE</td>
                                      <td><?
                                          $on_ef_fi=0;
                                          if($charge_budget_spent==0){
                                            $on_ef_fi=0;
                                          }else{
                                            $on_ef_fi=number_format((($total_chg/$charge_budget_spent)-1)*100, 2, '.', ',');
                                          }
                                          if ($on_ef_fi<0) {
                                            echo "Inefficient";
                                          }else{
                                            echo "Efficient";
                                          }
                                            ?></td>
                                      <td colspan="2"><?=$on_ef_fi?>%</td>
                                    </tr>
                                    <tr>
                                      <td>OUTSOURCE</td>
                                      <td><?
                                          $on_outsource=0;
                                          if($total_cost==0){
                                            $on_outsource=0;
                                          }else{
                                            $on_outsource=number_format(($total_paid/$total_cost)*100, 2, '.', ',');
                                          }
                                          if ($on_outsource<=100) {
                                            echo "On Budget";
                                          }else{
                                            echo "Over Budget";
                                          }
                                            ?></td>
                                      <td colspan="2"><?=$on_outsource?>%</td>
                                    </tr>
                                    <tr>
                                      <td>DURATION</td>
                                      <td><?
                                      $show_dutation=0;
                                      if (($actual_dutation-$target_dutation)>0) {
                                        echo "Delayed";
                                        $show_dutation=$actual_dutation-$target_dutation;
                                      }else{
                                        echo "On time";
                                        $show_dutation=$actual_dutation;
                                      }
                                      ?></td>
                                      <td><?=$show_dutation?></td>
                                      <td>Days</td>
                                    </tr>
                                       
                                      </table>
                                    </div>
                                </div>
                                <div class="span12 no-margin-left">
                                </div>
                    </div>




                </div>
                <!-- /block -->
            </div>
        </div>
    </div>
    <!--/.fluid-container-->
    
    <script>
    $(function() {
        $( "a" ).tooltip({});
        $(".fancybox").fancybox({
            maxWidth    : 800,
            maxHeight   : 600,
            fitToView   : false,
            width       : '70%',
            height      : '70%',
            autoSize    : false,
            closeClick  : false,
            openEffect  : 'none',
            closeEffect : 'none'
        });
        $("#project_value").html("<?=number_format($project_value)?>");
      });
    $(document).on("change", ".type_change", function() {
        var cur_row=$(this).parent().parent();
        var cur_select=cur_row.find("select.type_change");
        h_rate=parseInt(cur_select.find("option#"+cur_select.val()).attr("data"));
        a_buged=parseInt(cur_row.find("input.type_change").val());
        var chagre_hr=cur_row.find("td").eq(4);
        var chagre_total=cur_row.find("td").eq(5);
        chagre_hr.html(h_rate+"");
        chagre_total.html((h_rate*a_buged)+"");
        cal_rSheet();

    });
    function cal_rSheet(){
        var all_row=$("#res_t_body").find("tr");
        var sum_h=0;
        var sum_p=0;
        for (var i = 0; i <all_row.length-1; i++) {
            var cur_coll=all_row.eq(i).find("td");
            cur_coll.eq(0).html((i+1)+"");
            h_p=parseInt(cur_coll.eq(5).html());
            hr=parseInt(cur_coll.eq(3).find("input.type_change").val());
            sum_h+=hr;
            sum_p+=h_p;
            //console.log(sum_h+" -"+sum_p );
        };
        var last_row=all_row.eq(all_row.length-1).find("td");
        last_row.eq(3).html(""+sum_h);
        last_row.eq(5).html(""+sum_p);

    }

    $(document).on("change", ".out_change", function() {
        var cur_row=$(this).parent().parent();
        var cur_cost=cur_row.find("input.out_change").eq(0);
        var cur_charge=cur_row.find("input.out_change").eq(1);
        var cost=parseInt(cur_cost.val());
        var charge=parseInt(cur_charge.val());
        var margin=(charge-cost)/cost*100;
        cur_row.find(".out_margin").html(""+margin.toFixed(2)+"%");
        var id=cur_row.attr("iden");
        cal_outSource(id);

    });    
    $(document).on("click", ".toggle_outsource", function() {
        var id=$(this).attr("iden");
        if ($(this).attr("togStat")=="hide") {
            $("#outsource_"+id).show("slow");
            $(this).attr("togStat","show");
        }else{
            
            $("#outsource_"+id).slideUp();
            $(this).attr("togStat","hide");
        }

    });
function save_submit(){
    document.getElementById("pro_add_form").submit();
}
function approve_pce(type,pce_id){
    if (confirm(type+" Approve PCE#")) {
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/approve_pce"); ?>",
                data: {
                    "type": type,
                    "pce_id": pce_id
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    $("#"+type+"_a_"+pce_id).attr("href","javascript:;");
                    $("#"+type+"_a_"+pce_id).prepend('<i class="icon-ok icon-white"></i>');
                    $("#"+type+"_a_"+pce_id).attr("class","btn btn-inverse ");
                }else{
                    alert(data['flag']);
                }
                //$("#last_tr").before(data);
            });
    }
}

function approve_oc(oc_id){
    if (confirm(" Approve OC#")) {
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/approve_oc"); ?>",
                data: {
                    "oc_id": oc_id
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    $("#fc_oc_"+oc_id).attr("href","javascript:;");
                    $("#fc_oc_"+oc_id).prepend('<i class="icon-ok icon-white"></i>');
                    $("#fc_oc_"+oc_id).attr("class","btn btn-inverse ");
                }else{
                    alert(data['flag']);
                }
                //$("#last_tr").before(data);
            });
    }
}
    </script>


