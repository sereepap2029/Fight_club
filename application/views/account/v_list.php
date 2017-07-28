<?
$ci =& get_instance();
?>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Account Dashboard</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                <div class="span12 no-margin-left">
                                  <div class="span2 no-margin-left">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Start Date</label>
                                            <input class="form-control datepicker" type="text" id="project_start" value="<?=$ci->m_time->unix_to_datepicker(time())?>">
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">End Date</label>
                                            <input class="form-control datepicker" type="text" id="project_end" value="<?=$ci->m_time->unix_to_datepicker(time())?>">
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">โชวเฉพาะรับ/จ่าย</label>
                                            <select id="only_p">
                                              <option value="n">โชว์เฉพาะรับ/จ่าย</option>
                                              <option value="c">โชว์เฉพาะค้างรับ/จ่าย</option>
                                              <option value="y">โชว์ทั้งหมด</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Accounting Unit</label>
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
                                    </div>
                                </div>
                                   <div class="table-toolbar">
                                      <div class="btn-group">
                                         <a href="javascript:bill_search();"><button class="btn btn-info">Search</button></a>                                         
                                         
                                      </div> 

                                      <div class="btn-group">
                                         <a href="javascript:bill_report();"><button class="btn btn-info">Billing Report</button></a>                                         
                                         
                                      </div>  
                                      <div class="btn-group">
                                         
                                         <a href="javascript:payment_report();"><button class="btn btn-info">Payment Report</button></a>
                                      </div>                                      
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example2">
                                        <thead>
                                            <tr>
                                              <th>#</th>
                                              <th>Project Name</th>
                                              <th>OC Number</th>
                                              <th>Billing Status</th>
                                              <th>Billing Schedule</th>
                                              <th>Project value</th>
                                              <th>Start-Finish</th>
                                              <th>Work Status</th>
                                              <th>Outsource</th>
                                              <th>Payment Schedule</th>
                                              <th>Note</th>
                                              <th>Project Manager</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                           <?
                                           $countnum=0;
                                            foreach ($project_list as $key => $value) {
                                              $countnum+=1;
                                              $bill_schd = array();
                                              $company=$ci->m_company->get_company_by_id($value->project_client);
                                              $company_bu=$ci->m_company->get_bu_by_id($value->project_bu);
                                              $oc_list=$ci->m_oc->get_all_oc_by_project_id($value->project_id);
                                              $oc_sum_value=0;
                                              $oc_sum_billed=0;
                                              $outsource_bil=$ci->m_project->get_sum_outsource_bill_by_project_id($value->project_id);
                                              $sum_allocate=$ci->m_project->get_sum_allocate_budget_by_project_id($value->project_id);
                                             ?>
                                             <tr>
                                                  <td><? echo $countnum; ?></td>
                                                  <td><? echo $value->project_name; ?><br><?=$company->name." ".$company_bu->bu_name?></td>
                                                  <td style="width:180px;"><br><?
                                                  foreach ($oc_list as $oc_key => $oc_value) {
                                                    $bill_schd[$oc_value->id]=null;
                                                    $ins_sch=true;
                                                    $oc_bil=$ci->m_oc->get_oc_bill_by_oc_id($oc_value->id);
                                                    echo $oc_value->oc_no."<span class=\"badge badge-success\">".count($oc_bil)."</span><br>";
                                                    $oc_sum_value+=(int)$oc_value->oc_amount;
                                                    foreach ($oc_bil as $oc_bil_k => $oc_bil_v) {
                                                      if ($oc_bil_v->collected=="y") {
                                                        $oc_sum_billed+=(int)$oc_bil_v->paid_amount;
                                                      }else if($ins_sch&&$oc_bil_v->collected=="n"){
                                                        $bill_schd[$oc_value->id]=$oc_bil_v;
                                                        $ins_sch=false;
                                                      }                                                      
                                                    }
                                                  }
                                                  ?></td>
                                                  <td>
                                                    <?=$value->status_bill?>
                                                  </td>
                                                  <td>
                                                  <a href="<?=site_url("account/poso/".$value->project_id)?>" class="fancybox" data-fancybox-type="iframe"><i class="icon-pencil"></i></a><br>
                                                    <?
                                                    foreach ($bill_schd as $skey => $svalue) {
                                                      if (isset($svalue->time)) {
                                                        echo $ci->m_time->unix_to_datepicker($svalue->time)."<br>";
                                                      }else{
                                                        echo "<br>";
                                                      }
                                                    }
                                                    ?>
                                                  </td>
                                                  <td><?
                                                  echo number_format($oc_sum_value)."<br>(".number_format($oc_sum_billed).")";
                                                  ?></td>
                                                  <td><? echo $ci->m_time->unix_to_datepicker($value->project_start)." - ".$ci->m_time->unix_to_datepicker($value->project_end); ?></td>
                                                  
                                                  <td><? echo $value->status; ?></td>
                                                  <td><?
                                                  echo number_format($outsource_bil['sum_all'])."<br>(".number_format($outsource_bil['sum_paid']).")";
                                                  ?></td>
                                                  <td>
                                                    <a href="<?=site_url("account/poqp/".$value->project_id)?>" class="fancybox" data-fancybox-type="iframe"><i class="icon-pencil"></i></a><br>
                                                    <?
                                                    foreach ($outsource_bil['payment'] as $opkey => $opvalue) {
                                                      if (isset($opvalue->time)) {
                                                        echo $ci->m_time->unix_to_datepicker($opvalue->time)."<br>";
                                                      }else{
                                                        echo "<br>";
                                                      }
                                                    }
                                                    ?>
                                                  </td>
                                                  <td>
                                                    <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/project_note/".$value->project_id)?>">
                                                      <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/project_note.png')?>">
                                                    </a>
                                                    <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/finan_note/".$value->project_id)?>">
                                                      <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/finance_note.png')?>">
                                                    </a>
                                                  </td>
                                                  <td>                                                            
                                                  <?
                                                  $manager=$ci->m_user->get_user_by_login_name($value->project_cs);
                                                  ?>                       
                                                  <?=$manager->nickname?>
                                                    
                                                  </td>
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
            </footer>
        </div>
        <!--/.fluid-container-->
        <script type="text/javascript">
        $(function() {
          $(".fancybox").fancybox({
              fitToView   : false,
              width       : '95%',
              height      : '90%',
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
        function bill_report(){
          myform = document.createElement("form");
          $(myform).attr("action","<?=site_url("account/bill_report/")?>");   
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="only_p" value="'+$("#only_p").val()+'"><input type="text" name="account_unit_id" value="'+$("#account_unit_id").val()+'"><input type="text" name="business_unit_id" value="'+$("#business_unit_id").val()+'">')
          document.body.appendChild(myform);
          myform.submit();
          $(myform).remove();
        }
        function bill_search(){
          myform = document.createElement("form");
          $(myform).attr("action","<?=site_url("account")?>");   
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="only_p" value="'+$("#only_p").val()+'"><input type="text" name="account_unit_id" value="'+$("#account_unit_id").val()+'"><input type="text" name="business_unit_id" value="'+$("#business_unit_id").val()+'">')
          document.body.appendChild(myform);
          myform.submit();
          $(myform).remove();
        }
        function payment_report(){
          myform = document.createElement("form");
          $(myform).attr("action","<?=site_url("account/payment_report/")?>");   
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="only_p" value="'+$("#only_p").val()+'"><input type="text" name="account_unit_id" value="'+$("#account_unit_id").val()+'"><input type="text" name="business_unit_id" value="'+$("#business_unit_id").val()+'">')
          document.body.appendChild(myform);
          myform.submit();
          $(myform).remove();
        }
        </script>