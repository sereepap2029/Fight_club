<? $ci=& get_instance(); ?>
<style type="text/css">
  td.purple{
    background-color: rgb(96,73,112)!important;
    color: white;
  }
  td.ad_hour{
    text-align: center;
    vertical-align: middle;
  }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">HOD Pending Approval</div>
                    </div>
                    <div class="block-content collapse in ">
                        <div class="span12">
                        <div class="span12 no-margin-left">
                                  <div class="span2 no-margin-left">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Start Date</label>
                                            <input class="form-control datepicker" type="text" id="project_start" value="<?=$ci->m_time->unix_to_datepicker($filter['start_date'])?>">
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">End Date</label>
                                            <input class="form-control datepicker" type="text" id="project_end" value="<?=$ci->m_time->unix_to_datepicker($filter['end_date'])?>">
                                        </div>
                                    </div>
                                    <div class="span2"><?/*
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Project CS</label>
                                            <select id="project_cs">
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
                                        $("#project_cs").val("<?=$filter['project_cs']?>");
                                        </script>*/?>
                                    </div>
                                </div>
                                   <div class="table-toolbar">
                                      <div class="btn-group">
                                         <a href="javascript:hod_search();"><button class="btn btn-info">Search</button></a> 
                                      </div>         
                                      <div class="btn-group">
                                        <a href="javascript:export_excel();"><button class="btn btn-info">Excel</button></a>       
                                      </div>                             
                                   </div>
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
                                        <li class="active"><a href="#tab_one" data-toggle="tab"></a></li>
                                    </ul>
                                    <div id="myTabContent" class="tab-content">
                                        <div class="tab-pane active in" id="tab_one">
                                            <fieldset>
                                                <div class="table-toolbar">
                                                </div>
                                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" >
                                                    <thead>
                                                        <tr>
                                                          <th>Resource/Project</th>
                                                          <th>Available Hours</th>
                                                          <th colspan="3">Allocation Hour</th>
                                                          <th colspan="3">Spent</th>
                                                          <th colspan="3">Utilization (%)</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?
                                                    foreach ($all_child as $key => $value) {
                                                      ?>
                                                      <tr><td class="purple ad_hour"><?=$value->nickname?></td>
                                                        <td class="purple">Available Hours</td>
                                                          <td class="purple">TOTAL</td>
                                                          <td class="purple">BUDGET</td>
                                                          <td class="purple">OVER</td>
                                                          <td class="purple">TOTAL</td>
                                                          <td class="purple">on BUDGET</td>
                                                          <td class="purple">on TOTAL</td>
                                                          <td class="purple">by Budget</td>
                                                          <td class="purple">by Spent</td>
                                                          <td class="purple">GAP</td>
                                                      </tr>
                                                      <?
                                                      $flag_first=true;
                                                      foreach ($value->projects as $key2 => $value2) {
                                                        ?>
                                                        <tr>
                                                          <td><?=$value2->project_name?></td>
                                                          <?
                                                          if ($flag_first) {
                                                            ?>
                                                            <td class="ad_hour" rowspan="<?=count($value->projects)?>"><?=$value->available_Hours?></td>
                                                            <?
                                                          }
                                                          ?>
                                                          
                                                          <td><?=$value2->hour_amount?></td>
                                                          <td><?=$value2->budget?></td>
                                                          <td><?=$value2->hour_over?></td>
                                                          <td><?=$value2->spend_amount?></td>
                                                          <td><?
                                                          if ($value2->budget==0) {
                                                            echo "0";
                                                          }else{
                                                            echo number_format(($value2->spend_amount/$value2->budget)*100)."%";
                                                          }
                                                          ?></td>
                                                          <td><?
                                                          if ($value2->hour_amount==0) {
                                                            echo "0";
                                                          }else{
                                                            echo number_format(($value2->spend_amount/$value2->hour_amount)*100)."%";
                                                          }
                                                          ?></td>
                                                          <?
                                                          if ($flag_first) {
                                                            $gap1=0;
                                                            $gap2=0;
                                                            ?>
                                                            <td class="ad_hour" rowspan="<?=count($value->projects)?>"><?
                                                            if ($value->available_Hours==0) {
                                                              echo "0";
                                                            }else{
                                                              $gap1=number_format(($value->report->budget/$value->available_Hours)*100);
                                                              echo $gap1."%";
                                                            }
                                                            ?></td>
                                                            <td class="ad_hour" rowspan="<?=count($value->projects)?>"><?
                                                            if ($value->available_Hours==0) {
                                                              echo "0";
                                                            }else{
                                                              $gap2=number_format(($value->report->spend_amount/$value->available_Hours)*100);
                                                              echo $gap2."%";
                                                            }
                                                            ?></td>
                                                            <td class="ad_hour" rowspan="<?=count($value->projects)?>"><?
                                                              echo number_format($gap2-$gap1)."%";
                                                            ?></td>
                                                            <?
                                                          }
                                                          ?>
                                                      </tr>
                                                        <?
                                                        $flag_first=false;
                                                      }
                                                      ?>
                                                      <tr>
                                                          <td class="purple"><?=$value->nickname?> Total</td>
                                                          <td class="purple"><?=$lenght_time?> Days</td>
                                                          <td class="purple"><?=$value->report->hour_amount?></td>
                                                          <td class="purple"><?=$value->report->budget?></td>
                                                          <td class="purple"><?=$value->report->hour_over?></td>
                                                          <td class="purple"><?=$value->report->spend_amount?></td>
                                                          <td class="purple"><?
                                                          if ($value->report->budget==0) {
                                                            echo "0";
                                                          }else{
                                                            echo number_format(($value->report->spend_amount/$value->report->budget)*100)."%";
                                                          }
                                                          ?></td>
                                                          <td class="purple"><?
                                                          if ($value->report->hour_amount==0) {
                                                            echo "0";
                                                          }else{
                                                            echo number_format(($value->report->spend_amount/$value->report->hour_amount)*100)."%";
                                                          }
                                                          ?></td>
                                                          <td class="purple"></td>
                                                          <td class="purple"></td>
                                                          <td class="purple"></td>
                                                      </tr>
                                                      <?
                                                    }
                                                    ?>
                                                    </tbody>
                                                </table>
                                            </fieldset>
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
<script type="text/javascript">
    function approvol(link){
        if (confirm("Confirm Approve Project")) {
            window.open(link,"_self")
        };
    }
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
   
    function hod_search(){
          myform = document.createElement("form");
          $(myform).attr("action","<?=site_url("hod/utilization_reportv2")?>");   
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="project_cs" value="'+$("#project_cs").val()+'">')
          document.body.appendChild(myform);
          myform.submit();
          $(myform).remove();
        }    
    function export_excel(){
          myform = document.createElement("form");
          $(myform).attr("action","<?=site_url("hod/utilization_report_excelv2")?>");   
          $(myform).attr("method","post");
          $(myform).html('<input type="text" name="start_time" value="'+$("#project_start").val()+'"><input type="text" name="end_time" value="'+$("#project_end").val()+'"><input type="text" name="project_cs" value="'+$("#project_cs").val()+'">')
          document.body.appendChild(myform);
          myform.submit();
          $(myform).remove();
        }        
</script>