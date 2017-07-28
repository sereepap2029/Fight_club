<?
$ci =& get_instance();
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
  .table-striped tbody tr.h1 td{
    color: #FFFFFF;
    background-color: #000000;
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
                                <div class="muted pull-left">ประมาณการจ่ายเงิน</div>
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
                                    <div class="span2">
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
                                </div>
                                   <div class="table-toolbar">

                                      <div class="btn-group">
                                         <a href="javascript:search();"><button class="btn btn-info">Search</button></a>                                         
                                         
                                      </div>
                                      <div class="btn-group">
                                         <a href="javascript:export_forcast();"><button class="btn btn-info">Export Excel</button></a>                                         
                                         
                                      </div>                                      
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="3">Month-></th>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        if ($cur_month!=date("n",$current_time)) {
                                                            $cur_month=date("n",$current_time);
                                                            $cur_day=date("j",$current_time);
                                                            $numday=date("t",$current_time);
                                                            
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
                                                <td colspan="3">Forcast</td>
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
                                                              $month_amount+=$value2->outsource_value;
                                                            }
                                                          }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?
                                            foreach ($forcast_report as $key => $value) {
                                              ?>
                                              <tr class="ae-name">
                                                <td colspan="3"><?=$value->firstname." ".$value->lastname?></td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);         
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              $month_amount+=$value2->outsource_value;
                                                            }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                              </tr>
                                              <?
                                              foreach ($value->forcast_list as $key2 => $value2) {
                                                foreach ($value2 as $key3 => $value3) {
                                                  ?>
                                                  <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$value3->project_name?></td>
                                                    <td><?=$company[$value3->project_client]->name." ".$bu[$value3->project_bu]->bu_name?></td>
                                                    <td></td>
                                                    <?
                                                      $current_time=$start_time;
                                                          $cur_month=1000;
                                                          $month_amount=0;
                                                          $sum_row=0;
                                                          while ($current_time<=$end_carlendar_unix) {
                                                              $cur_day=date("j",$current_time);
                                                              $numday=date("t",$current_time);         
                                                                if ($current_time==$value3->project_end_credit_term) {                                       
                                                                    $month_amount+=$value3->outsource_value;
                                                                }
                                                              if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                                  $cur_month=date("n",$current_time);                                                  
                                                                  ?>
                                                                  <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                                  <?
                                                                  $sum_row+=$month_amount;
                                                                  $month_amount=0;
                                                              }                                                        
                                                             $current_time+=(60*60*24);
                                                          }
                                                      ?> 
                                                      <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                                  </tr>
                                                  <?
                                                }
                                              }
                                            }








                                            ///////////////////////////////////////////////////////// PCE Section //////////////////////////////




                                            ?>                                           
                                            <tr class="h1">
                                                <td colspan="3">Outsource (PCE)</td>
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
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?
                                            foreach ($outsource_report as $key => $value) {
                                              ?>
                                              <tr class="ae-name">
                                                <td colspan="3"><?=$value->firstname." ".$value->lastname?></td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);         
                                                          if (isset($value->forcast_list[$current_time])) {                                       
                                                            foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                                                              foreach ($value2->outsource as $key3 => $value3) {
                                                                $month_amount+=$value3->qt_cost;
                                                              }
                                                            }
                                                        }
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                              </tr>
                                              <?
                                              foreach ($value->forcast_list as $key2 => $value2) {
                                                foreach ($value2 as $key3 => $value3) {
                                                  ?>
                                                  <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$value3->project_name?></td>
                                                    <td><?=$company[$value3->project_client]->name." ".$bu[$value3->project_bu]->bu_name?></td>
                                                    <td><?
                                                                    foreach ($value3->outsource as $key4 => $value4) {
                                                                      echo $value4->qt_no."<br>";
                                                                    }
                                                    ?></td>
                                                    <?
                                                      $current_time=$start_time;
                                                          $cur_month=1000;
                                                          $month_amount=0;
                                                          $sum_row=0;
                                                          while ($current_time<=$end_carlendar_unix) {
                                                              $cur_day=date("j",$current_time);
                                                              $numday=date("t",$current_time);         
                                                                if ($current_time==$key2) {   
                                                                    foreach ($value3->outsource as $key4 => $value4) {
                                                                      $month_amount+=$value4->qt_cost;
                                                                    }
                                                                }
                                                              if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                                  $cur_month=date("n",$current_time);                                                  
                                                                  ?>
                                                                  <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                                  <?
                                                                  $sum_row+=$month_amount;
                                                                  $month_amount=0;
                                                              }                                                        
                                                             $current_time+=(60*60*24);
                                                          }
                                                      ?> 
                                                      <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                                  </tr>
                                                  <?
                                                }
                                              }
                                            }









                                            ///////////////////////////////////////////////////////// Target Bill Section //////////////////////////////





                                            ?>
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                      
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"></td>
                                                            <?
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="3">Outsource (OC)</td>
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
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?
                                            foreach ($target_bill_report as $key => $value) {
                                              ?>
                                              <tr class="ae-name">
                                                <td colspan="3"><?=$value->firstname." ".$value->lastname?></td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);         
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=($value4->amount-$value4->paid_amount);
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
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                              </tr>
                                              <?
                                              foreach ($value->forcast_list as $key2 => $value2) {
                                                 ?>
                                                  <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$value2->project_name?></td>
                                                    <td><?=$company[$value2->project_client]->name." ".$bu[$value2->project_bu]->bu_name?></td>
                                                    <td><?
                                                                    foreach ($value2->outsource as $key3 => $value3) {
                                                                      echo $value3->qt_no."<br>";
                                                                    }
                                                    ?></td>
                                                    <?                                                 
                                                      $current_time=$start_time;
                                                          $cur_month=1000;
                                                          $month_amount=0;
                                                          $sum_row=0;
                                                          while ($current_time<=$end_carlendar_unix) {
                                                              $cur_day=date("j",$current_time);
                                                              $numday=date("t",$current_time);            
                                                                    foreach ($value2->outsource as $key3 => $value3) {
                                                                      if (isset($value3->bill[$current_time])) {
                                                                        foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                          $month_amount+=($value4->amount-$value4->paid_amount);
                                                                        }
                                                                      }                                                              
                                                                    }
                                                                
                                                              if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                                  $cur_month=date("n",$current_time);                                                  
                                                                  ?>
                                                                  <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                                  <?
                                                                  $sum_row+=$month_amount;
                                                                  $month_amount=0;
                                                              }                                                        
                                                             $current_time+=(60*60*24);
                                                          }
                                                      ?> 
                                                      <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                                  </tr>
                                                  <?
                                                }                                              
                                            }






///////////////////////////////////////////////////////// Actual Bill Section //////////////////////////////





                                            ?>
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                      
                                                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                            $cur_month=date("n",$current_time);                                                  
                                                            ?>
                                                            <td  style="text-align: center;"></td>
                                                            <?
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="3">Outsource (Paid)</td>
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
                                                            ?>
                                                            <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                            <?
                                                            $sum_row+=$month_amount;
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?
                                            foreach ($actual_bill_report as $key => $value) {
                                              ?>
                                              <tr class="ae-name">
                                                <td colspan="3"><?=$value->firstname." ".$value->lastname?></td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);         
                                                          foreach ($value->forcast_list as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->amount;
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
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                              </tr>
                                              <?
                                              foreach ($value->forcast_list as $key2 => $value2) {
                                                 ?>
                                                  <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$value2->project_name?></td>
                                                    <td><?=$company[$value2->project_client]->name." ".$bu[$value2->project_bu]->bu_name?></td>
                                                    <td><?
                                                                    foreach ($value2->outsource as $key3 => $value3) {
                                                                      echo $value3->qt_no."<br>";
                                                                    }
                                                    ?></td>
                                                    <?                                                 
                                                      $current_time=$start_time;
                                                          $cur_month=1000;
                                                          $month_amount=0;
                                                          $sum_row=0;
                                                          while ($current_time<=$end_carlendar_unix) {
                                                              $cur_day=date("j",$current_time);
                                                              $numday=date("t",$current_time);            
                                                                    foreach ($value2->outsource as $key3 => $value3) {
                                                                      if (isset($value3->bill[$current_time])) {
                                                                        foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                          $month_amount+=$value4->amount;
                                                                        }
                                                                      }                                                              
                                                                    }
                                                                
                                                              if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                                                                  $cur_month=date("n",$current_time);                                                  
                                                                  ?>
                                                                  <td  style="text-align: center;"><?=number_format($month_amount)?></td>
                                                                  <?
                                                                  $sum_row+=$month_amount;
                                                                  $month_amount=0;
                                                              }                                                        
                                                             $current_time+=(60*60*24);
                                                          }
                                                      ?> 
                                                      <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                                  </tr>
                                                  <?
                                                }                                              
                                            }
                                            ?>

                                        </tbody>
                                    </table>                                   
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                </div>
            </div>
            <hr>
            <footer>
            <?
            //print_r($pce_report);
            ?>
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
          $(myform).attr("action","<?=site_url("account/report_outsource_paid/")?>");   
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="business_unit_id" value="'+$("#business_unit_id").val()+'">')
          myform.submit();
          $(myform).remove();
        }
        function export_forcast(){
          myform = document.createElement("form");
          $(myform).attr("action","<?=site_url("account/report_outsource_paid_excel/")?>");   
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="business_unit_id" value="'+$("#business_unit_id").val()+'">')
          myform.submit();
          $(myform).remove();
        }
        </script>