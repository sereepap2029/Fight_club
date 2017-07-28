<?
$ci =& get_instance();
$total_horizon_array = array();
?>
<style type="text/css">
  .table-striped tbody>tr:nth-child(odd)>td, .table-striped tbody>tr:nth-child(odd)>th{
    background-color: #FFFFFF;
  }
  .green-f{
    color: green;
    font-weight: bolder;

  }
  .table td{
    white-space: nowrap;
  }
  .table th{
    white-space: nowrap;
  }
  .table-striped tbody tr.h1 td{
    vertical-align: middle;
    text-align: center;
  }
  
  .table-striped tbody tr.ae-name td{
    background-color: #E2E2E2;
  }
</style>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Operating GP Report</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="span12 no-margin-left">
                                  <div class="span2 no-margin-left">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Start Date</label>
                                            <input class="form-control datepicker" type="text" id="project_start" value="<?=$ci->m_time->unix_to_datepicker($start_time)?>">
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">End Date</label>
                                            <input class="form-control datepicker" type="text" id="project_end" value="<?=$ci->m_time->unix_to_datepicker($end_carlendar_unix)?>">
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Business Unit</label>
                                            <select id="business_unit_id" class="chzn-select" name="business_unit_id">
                                                <option value="all">All</option>
                                                <?
                                                foreach ($business_list as $key => $value) {
                                                    ?>
                                                    <option value="<?=$value->id?>"><?=$value->name?></option>
                                                    <?
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <script type="text/javascript">
                                          $("#business_unit_id").val("<?=$bus_unit?>");
                                        </script>
                                    </div>
                                    <div class="span3 no-margin-left">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Project CS</label>
                                            <select id="project_cs" class="chzn-select" name="project_cs">
                                                <option value="all">All</option>
                                                  <?
                                                  foreach ($cs as $key => $value) {
                                                    ?>
                                                    <option value="<?=$key?>"><?=$value->nickname?></option>
                                                    <?
                                                  }
                                                  ?>
                                            </select>
                                        </div>
                                        <script type="text/javascript">
                                          $("#project_cs").val("<?=$multi_usn?>");
                                        </script>
                                    </div>
                                    <div class="span2 no-margin-left">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Display Mode</label>
                                            <select id="mode" class="chzn-select" name="mode">
                                                <option value="1">Normal</option>
                                                <option value="2">Client</option>
                                                <option value="3">Client BU</option>
                                            </select>
                                        </div>
                                        <script type="text/javascript">
                                          $("#mode").val("<?=$mode?>");
                                        </script>
                                    </div>
                                </div>
                                   <div class="table-toolbar">

                                      <div class="btn-group">
                                         <a href="javascript:search();"><button class="btn btn-info">Search</button></a>                                         
                                         
                                      </div>
                                      <div class="btn-group">
                                         <a href="javascript:export_forcast();"><button class="btn btn-info">Export Excel</button></a>                                         
                                         
                                      </div>                                      
                                   </div>
                                   <div class="table-toolbar">
                                        <h2>GROSS PROFIT FORECAST</h2>                                  
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="3"></th>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        if ($cur_month!=date("n",$current_time)) {
                                                            $cur_month=date("n",$current_time);
                                                            $cur_day=date("j",$current_time);
                                                            $numday=date("t",$current_time);
                                                            $total_horizon_array[date("Yn",$current_time)]=0;
                                                            ?>
                                                            <th style="text-align: center;"><?=date("F",$current_time)?></th>
                                                            <?
                                                        }                                                        
                                                       $current_time+=(60*60*24*$numday);
                                                    }
                                                ?>
                                                <th>Total</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="h1">
                                                <td colspan="2" rowspan="2">Forcast</td>
                                                <td colspan="1" rowspan="1">Revenue</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($forcast_report as $key => $value) {          
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              $month_amount+=$value2->project_value;
                                                            }
                                                          }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Outsource</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($forcast_out_report as $key => $value) {          
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              $month_amount+=$value2->outsource_value;
                                                            }
                                                          }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);      
                                                            $month_amount*=-1;                                            
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?








                                            ///////////////////////////////////////////////////////// PCE Section //////////////////////////////




                                            ?>
                                            <tr class="h1">
                                                <td colspan="2" rowspan="2">PCE</td>
                                                <td colspan="1">Revenue</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($pce_report as $key => $value) {          
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              foreach ($value2->pce as $key3 => $value3) {
                                                                $month_amount+=$value3->pce_amount;
                                                              }
                                                              
                                                            }
                                                          }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Outsource</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($outsource_report as $key => $value) {          
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              foreach ($value2->outsource as $key3 => $value3) {
                                                                $month_amount+=$value3->qt_cost;
                                                              }
                                                              
                                                            }
                                                          }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);              
                                                            $month_amount*=-1;                                    
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?




                                            ///////////////////////////////////////////////////////// Target Bill Section //////////////////////////////





                                            ?>
                                            <tr class="h1">
                                                <td colspan="2" rowspan="2">OC</td>
                                                <td colspan="1">Revenue</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($target_bill_report as $key => $value) {       
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->oc as $key3 => $value3) {
                                                              if (isset($value3->oc_bill[$current_time])) {
                                                                foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->amount;
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Outsource</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($target_out_report as $key => $value) {       
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=($value4->amount-$value4->paid_amount);
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);    
                                                            $month_amount*=-1;                                              
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?




///////////////////////////////////////////////////////// Actual Bill Section //////////////////////////////





                                            ?>
                                            <tr class="h1">
                                                <td colspan="2" rowspan="2">Actual</td>
                                                <td colspan="1">Revenue</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($actual_bill_report as $key => $value) {       
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->oc as $key3 => $value3) {
                                                              if (isset($value3->oc_bill[$current_time])) {
                                                                foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->paid_amount;
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Outsource</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($actual_out_report as $key => $value) {       
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->amount;
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time); 
                                                            $month_amount*=-1;                                                 
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_horizon=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                      
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($total_horizon_array[date("Yn",$current_time)])?></td>
                                                            <?
                                                            $sum_horizon+=$total_horizon_array[date("Yn",$current_time)];
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_horizon)?></td>
                                            </tr>

                                        </tbody>
                                    </table>            














                                    <?
                        if (isset($user_data->prem['admin'])||isset($user_data->prem['account'])||isset($user_data->prem['csd'])||isset($user_data->prem['fc'])) {
                            ?>
                                    <div class="table-toolbar">
                                        <h2>OPERATING CASH FLOW FORECAST</h2>                                  
                                   </div>

                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="3"></th>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        if ($cur_month!=date("n",$current_time)) {
                                                            $cur_month=date("n",$current_time);
                                                            $cur_day=date("j",$current_time);
                                                            $numday=date("t",$current_time);
                                                            $total_horizon_array[date("Yn",$current_time)]=0;
                                                            ?>
                                                            <th style="text-align: center;"><?=date("F",$current_time)?></th>
                                                            <?
                                                        }                                                        
                                                       $current_time+=(60*60*24*$numday);
                                                    }
                                                ?>
                                                <th>Total</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="h1">
                                                <td colspan="2" rowspan="2">Forcast</td>
                                                <td colspan="1" rowspan="1">Revenue</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($forcast_report_cash as $key => $value) {          
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              $month_amount+=$value2->project_value;
                                                            }
                                                          }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Outsource</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($forcast_out_report_cash as $key => $value) {          
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              $month_amount+=$value2->outsource_value;
                                                            }
                                                          }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);            
                                                            $month_amount*=-1;                                      
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?








                                            ///////////////////////////////////////////////////////// PCE Section //////////////////////////////




                                            ?>
                                            <tr class="h1">
                                                <td colspan="2" rowspan="2">PCE</td>
                                                <td colspan="1">Receive</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($pce_report_cash as $key => $value) {          
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              foreach ($value2->pce as $key3 => $value3) {
                                                                $month_amount+=$value3->pce_amount;
                                                              }
                                                              
                                                            }
                                                          }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Paid</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($outsource_report_cash as $key => $value) {          
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              foreach ($value2->outsource as $key3 => $value3) {
                                                                $month_amount+=$value3->qt_cost;
                                                              }
                                                              
                                                            }
                                                          }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);       
                                                            $month_amount*=-1;                                           
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?




                                            ///////////////////////////////////////////////////////// Target Bill Section //////////////////////////////





                                            ?>
                                            <tr class="h1">
                                                <td colspan="2" rowspan="2">OC</td>
                                                <td colspan="1">Receive</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($target_bill_report_cash as $key => $value) {       
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->oc as $key3 => $value3) {
                                                              if (isset($value3->oc_bill[$current_time])) {
                                                                foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->amount;
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Paid</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($target_out_report_cash as $key => $value) {       
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=($value4->amount-$value4->paid_amount);
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);            
                                                            $month_amount*=-1;                                      
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?




///////////////////////////////////////////////////////// Actual Bill Section //////////////////////////////





                                            ?>
                                            <tr class="h1">
                                                <td colspan="2" rowspan="2">Actual</td>
                                                <td colspan="1">Receive</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($actual_bill_report_cash as $key => $value) {       
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->oc as $key3 => $value3) {
                                                              if (isset($value3->oc_bill[$current_time])) {
                                                                foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->paid_amount;
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Paid</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($actual_out_report_cash as $key => $value) {       
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->amount;
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);           
                                                            $month_amount*=-1;                                       
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_horizon=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                      
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($total_horizon_array[date("Yn",$current_time)])?></td>
                                                            <?
                                                            $month_amount=0;
                                                            $sum_horizon+=$total_horizon_array[date("Yn",$current_time)];
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_horizon)?></td>
                                            </tr>

                                        </tbody>
                                    </table>          
                                    <?
                                }// end if premission
                                    ?>             
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
        <script type="text/javascript">
        $(function() {
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
        $(".datepicker").datepicker({
              changeMonth: true,
              changeYear: true,
              dateFormat: "dd/mm/yy"
          });
        });
        function search(){
          myform = document.createElement("form");
          $(myform).attr("action","<?=site_url("forcast/operatingGPReport/")?>");   
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="business_unit_id" value="'+$("#business_unit_id").val()+'"><input type="text" name="mode" value="'+$("#mode").val()+'"><input type="text" name="multi_usn" value="'+$("#project_cs").val()+'">')
          document.body.appendChild(myform);
          myform.submit();
          $(myform).remove();
        }
        function export_forcast(){
          myform = document.createElement("form"); 
          if ($("#mode").val()=="1") {
            $(myform).attr("action","<?=site_url("forcast/operatingGPReport_excel/")?>");
          }else{
            $(myform).attr("action","<?=site_url("forcast/operatingGPReport_excel_client_mode/")?>");
          }         
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="business_unit_id" value="'+$("#business_unit_id").val()+'"><input type="text" name="mode" value="'+$("#mode").val()+'"><input type="text" name="multi_usn" value="'+$("#project_cs").val()+'">')
          document.body.appendChild(myform);
          myform.submit();
          $(myform).remove();
        }
        </script>