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
                                <div class="muted pull-left">All Forcast</div>
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
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Account Unit</label>
                                            <select id="account_unit_id" class="chzn-select" name="account_unit_id">
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
                                          $("#account_unit_id").val("<?=$account_unit_id?>");
                                        </script>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Display Mode</label>
                                            <select id="mode" class="chzn-select" name="mode">
                                                <option value="1">By CS</option>
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
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="1">Month-></th>
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
                                                <td colspan="1">Forcast</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($forcast_report as $key2 => $value2) {                                                                    
                                                            if (isset($value2[$current_time])) {                                       
                                                              foreach ($value2[$current_time] as $key3 => $value3) {                                                                
                                                                  $month_amount+=$value3->project_value;
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
                                            foreach ($forcast_report as $key2 => $value2) {                                             
                                                ?>
                                                <tr>
                                                  <?
                                                  if ($mode==3) {
                                                    $str1=explode("_", $key2);
                                                    ?>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name?></td>
                                                    <?
                                                  }else{
                                                  ?>
                                                  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$company[$key2]->name?></td>
                                                  <?
                                                  }
                                                  ?>
                                                  
                                                  <?
                                                  $current_time=$start_time;
                                                      $cur_month=1000;
                                                      $month_amount=0;
                                                      $sum_row=0;
                                                      while ($current_time<=$end_carlendar_unix) {
                                                          $cur_day=date("j",$current_time);
                                                          $numday=date("t",$current_time);     
                                                          foreach ($value2 as $key3 => $value3) {                                                  
                                                            foreach ($value3 as $key4 => $value4) {    
                                                              if ($current_time==$value4->project_end) {                                       
                                                                  $month_amount+=$value4->project_value;
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








                                            ///////////////////////////////////////////////////////// PCE Section //////////////////////////////




                                            ?>
                                              <tr>
                                                <td colspan="1">&nbsp;</td>
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
                                                <td></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">PCE</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($pce_report as $key2 => $value2) {                                                            
                                                            if (isset($value2[$current_time])) {                                       
                                                              foreach ($value2[$current_time] as $key3 => $value3) {
                                                                foreach ($value3->pce as $key4 => $value4) {
                                                                  $month_amount+=$value4->pce_amount;
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
                                            foreach ($pce_report as $key2 => $value2) {                                              
                                              
                                                ?>
                                                <tr>
                                                    <?
                                                  if ($mode==3) {
                                                    $str1=explode("_", $key2);
                                                    ?>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name?></td>
                                                    <?
                                                  }else{
                                                  ?>
                                                  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$company[$key2]->name?></td>
                                                  <?
                                                  }
                                                  /*?>                                                    
                                                    <td>
                                                <?
                                                foreach ($value2 as $key3 => $value3) {
                                                      foreach ($value3 as $key4 => $value4) {
                                                        foreach ($value4->pce as $key5 => $value5) {
                                                          echo $value5->pce_no."<br>";
                                                        }
                                                      }                                                    
                                                  }
                                                  ?></td>
                                                    <?*/
                                                      $current_time=$start_time;
                                                          $cur_month=1000;
                                                          $month_amount=0;
                                                          $sum_row=0;
                                                          while ($current_time<=$end_carlendar_unix) {
                                                              $cur_day=date("j",$current_time);
                                                              $numday=date("t",$current_time);       
                                                              foreach ($value2 as $key3 => $value3) {
                                                                foreach ($value3 as $key4 => $value4) {
                                                                  if ($current_time==$value4->project_end) {
                                                                    foreach ($value4->pce as $key5 => $value5) {
                                                                      $month_amount+=$value5->pce_amount;
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
                                            }









                                            ///////////////////////////////////////////////////////// Target Bill Section //////////////////////////////





                                            ?>
                                            <tr>
                                                <td colspan="1">&nbsp;</td>
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
                                                <td></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Target Billing (OC)</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($target_bill_report as $key2 => $value2) {       
                                                            foreach ($value2 as $c_key => $c_value) {                                                           
                                                              foreach ($c_value->oc as $key3 => $value3) {
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
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?
                                            foreach ($target_bill_report as $key2 => $value2) {
                                                 ?>
                                                  <tr>
                                                    <?
                                                  if ($mode==3) {
                                                    $str1=explode("_", $key2);
                                                    ?>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name?></td>
                                                    <?
                                                  }else{
                                                  ?>
                                                  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$company[$key2]->name?></td>
                                                  <?
                                                  }
                                                  /*?>                                                    
                                                    <td><?          
                                                                  foreach ($value2 as $c_key => $c_value) {
                                                                    foreach ($c_value->oc as $key3 => $value3) {
                                                                      echo $value3->oc_no."<br>";
                                                                    }
                                                                  }
                                                    ?></td>
                                                    <?*/                                                 
                                                      $current_time=$start_time;
                                                          $cur_month=1000;
                                                          $month_amount=0;
                                                          $sum_row=0;
                                                          while ($current_time<=$end_carlendar_unix) {
                                                              $cur_day=date("j",$current_time);
                                                              $numday=date("t",$current_time);
                                                                  foreach ($value2 as $c_key => $c_value) {            
                                                                    foreach ($c_value->oc as $key3 => $value3) {
                                                                      if (isset($value3->oc_bill[$current_time])) {
                                                                        foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
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
                                            }






///////////////////////////////////////////////////////// Actual Bill Section //////////////////////////////





                                            ?>
                                            <tr>
                                                <td colspan="1">&nbsp;</td>
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
                                                <td></td>
                                            </tr>
                                            <tr class="h1">
                                                <td colspan="1">Actual Billing</td>
                                                <?
                                                $current_time=$start_time;
                                                    $cur_month=1000;
                                                    $month_amount=0;
                                                    $sum_row=0;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $cur_day=date("j",$current_time);
                                                        $numday=date("t",$current_time);
                                                        foreach ($actual_bill_report as $key2 => $value2) {       
                                                            foreach ($value2 as $c_key => $c_value) {
                                                              foreach ($c_value->oc as $key3 => $value3) {
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
                                                            $month_amount=0;
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?> 
                                                <td  style="text-align: center;"><?=number_format($sum_row)?></td>
                                            </tr>
                                            <?
                                            foreach ($actual_bill_report as $key2 => $value2) {
                                              
                                                 ?>
                                                  <tr>
                                                    <?
                                                    if ($mode==3) {
                                                      $str1=explode("_", $key2);
                                                      ?>
                                                      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name?></td>
                                                      <?
                                                    }else{
                                                    ?>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$company[$key2]->name?></td>
                                                    <?
                                                    }
                                                    /*?>                                                    
                                                    <td><?
                                                                  foreach ($value2 as $c_key => $c_value) {
                                                                    foreach ($c_value->oc as $key3 => $value3) {
                                                                      echo $value3->oc_no."<br>";
                                                                    }
                                                                  }
                                                    ?></td>
                                                    <?*/                                                 
                                                      $current_time=$start_time;
                                                          $cur_month=1000;
                                                          $month_amount=0;
                                                          $sum_row=0;
                                                          while ($current_time<=$end_carlendar_unix) {
                                                              $cur_day=date("j",$current_time);
                                                              $numday=date("t",$current_time);
                                                                  foreach ($value2 as $c_key => $c_value) {            
                                                                    foreach ($c_value->oc as $key3 => $value3) {
                                                                      if (isset($value3->oc_bill[$current_time])) {
                                                                        foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                          $month_amount+=$value4->paid_amount;
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
          $(myform).attr("action","<?=site_url("forcast/report/")?>");   
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="business_unit_id" value="'+$("#business_unit_id").val()+'"><input type="text" name="mode" value="'+$("#mode").val()+'">')
          document.body.appendChild(myform);
          myform.submit();
          $(myform).remove();
        }
        function export_forcast(){
          myform = document.createElement("form");
          if ($("#mode").val()=="1") {
            $(myform).attr("action","<?=site_url("forcast/forcest_excel/")?>");
          }else{
            $(myform).attr("action","<?=site_url("forcast/forcest_excel_client_mode/")?>");
          }             
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="business_unit_id" value="'+$("#business_unit_id").val()+'"><input type="text" name="mode" value="'+$("#mode").val()+'">')
          document.body.appendChild(myform);
          myform.submit();
          $(myform).remove();
        }
        </script>