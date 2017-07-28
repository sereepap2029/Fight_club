<?
$ci =& get_instance();
?>
<style type="text/css">
  .green-f{
    color: green;
    font-weight: bolder;
  }
</style>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Dashboard</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">
                                      <div class="btn-group">
                                         <a href="<? echo site_url('project/add')?>"><button class="btn btn-success">Create New Job <i class="icon-plus icon-white"></i></button></a>
                                      </div>                                      
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="example2">
                                        <thead>
                                            <tr>
                                              <th>#</th>
                                              <th>Job Name</th>
                                              <th>Duration</th>
                                              <th>Work Status</th>
                                              <th>Billing Status</th>
                                              <th>Budget</th>
                                              <th>Outsource Status</th>
                                              <th>OK</th>
                                              <th>Project Owner</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                           <?
                                            foreach ($project_list as $key => $value) {
                                              $company=$ci->m_company->get_company_by_id($value->project_client);
                                              $company_bu=$ci->m_company->get_bu_by_id($value->project_bu);
                                              $oc_list=$ci->m_oc->get_all_oc_by_project_id($value->project_id);
                                              $pce_list=$ci->m_pce->get_all_pce_by_project_id($value->project_id);
                                              $oc_sum_value=0;
                                              $oc_sum_billed=0;
                                              $outsource_bil=$ci->m_project->get_sum_outsource_bill_by_project_id($value->project_id);
                                              $sum_allocate=$ci->m_project->get_sum_allocate_budget_by_project_id($value->project_id);
                                             ?>
                                             <tr>
                                                  <td><? echo $key+1; ?></td>
                                                  <td style="word-wrap: normal;min-width:400px;">
                                                  <?
                                                  if ($value->pass) {
                                                    ?>
                                                    <a href="<? echo site_url('project/edit_oc/'.$value->project_id)?>" class=""><?=$value->project_name?><i class=" icon-pencil"></i></a>
                                                    <?
                                                  }else{
                                                    ?>
                                                    <a href="<? echo site_url('project/edit/'.$value->project_id)?>" class=""><?=$value->project_name?><i class=" icon-pencil"></i></a>
                                                    <?
                                                  }
                                                  ?><br>
                                                  <? echo $company->name." ".$company_bu->bu_name;
                                                  ?><br>
                                                  <div id="Modal_<?=$value->project_id?>" class="modal hide in" aria-hidden="false">
                                                    <div class="modal-header">
                                                      <button data-dismiss="modal" class="close" type="button">×</button>
                                                      <h3><? echo $value->project_name; ?> OC List </h3>
                                                    </div>
                                                    <div class="modal-body">
                                                      <table class="just-table">
                                                        <?
                                                        $pce_check_list = array();
                                                        foreach ($oc_list as $oc_key => $oc_value) {
                                                          $pce_check_list[$oc_value->pce_id]="yes";
                                                          $oc_bil=$ci->m_oc->get_oc_bill_by_oc_id($oc_value->id);
                                                          ?>
                                                          <tr>
                                                            <td><?echo $oc_value->oc_no;?></td>
                                                            <td>
                                                            <?
                                                            $so_arr = array();
                                                            $oc_sum_value+=(int)$oc_value->oc_amount;
                                                            foreach ($oc_bil as $oc_bil_k => $oc_bil_v) {
                                                              if ($oc_bil_v->collected=="y") {
                                                                if (!isset($so_arr[$oc_bil_v->so])) {
                                                                  $so_arr[$oc_bil_v->so]=$oc_bil_v->so;
                                                                  echo $oc_bil_v->so."<br>";
                                                                }
                                                                $oc_sum_billed+=(int)$oc_bil_v->paid_amount;
                                                              }                                                      
                                                            }
                                                            ?>
                                                            </td>
                                                          </tr>
                                                          <?
                                                        }
                                                          foreach ($pce_list as $pce_key => $pce_value) {
                                                            if (!isset($pce_check_list[$pce_value->id])) {
                                                              $oc_sum_value+=(int)$pce_value->pce_amount;
                                                            }                                                            
                                                          }
                                                        ?>
                                                      </table>
                                                    </div>
                                                  </div>
                                                  <table class="just-table">
                                                    <tr>
                                                      <td><?
                                                      echo number_format($oc_sum_value, 0, '.', ',')."(<font class='green-f'>".number_format($oc_sum_billed, 0, '.', ',')."</font>)";
                                                      ?></td>
                                                      <td style="text-align:right">
                                                        <a href="#Modal_<?=$value->project_id?>" data-toggle="modal">View OC</a>
                                                      </td>
                                                    </tr>
                                                  </table>
                                                  
                                                  </td>
                                                  <td><? echo $ci->m_time->unix_to_datepicker($value->project_start)." - ".$ci->m_time->unix_to_datepicker($value->project_end); ?>
                                                  <br>
                                                  <?
                                                    $lenght_time=(int)(($value->project_end-$value->project_start)/(60*60*24));
                                                    $until_now_time=(int)((time()-$value->project_start)/(60*60*24));
                                                    $cur_lenght=0;
                                                    if ($lenght_time==0) {
                                                      $lenght_time=1;
                                                    }
                                                    if ($until_now_time>=$lenght_time) {
                                                      $cur_lenght=100;
                                                    }else{
                                                      $cur_lenght=(int)(($until_now_time/$lenght_time)*100);
                                                    }                                                    
                                                    echo $lenght_time." Days ";
                                                    ?>
                                                    <div class="progress">
                                                      <div style="width: <?=$cur_lenght?>%;" class="bar"><?=$cur_lenght?>%</div>
                                                    </div>
                                                  </td>
                                                                                                   
                                                  <td><? 
                                                  if ($value->status=="Done") {
                                                    echo "Complete";
                                                  }else{
                                                    echo $value->status; 
                                                  }
                                                  ?>
                                                  <br>
                                                  <?
                                                    if($value->status=="Done"){
                                                          ?>
                                                          <a href="javascript:archive_job('<?=$value->project_id?>');" class="btn">Archive</a><br>
                                                          <?
                                                      }
                                                  ?>
                                                  </td>
                                                  <td><? echo $value->status_bill; ?></td>
                                                  <td >
                                                    <div class="progress ">
                                                      <div style="width: 100%;" class="bar">
                                                      <table class="just-table">
                                                        <tr>
                                                          <td style="width:120px">BUDGET</td>
                                                          <td style="width:60px"><?$sum_budget=$ci->m_project->get_sum_budget_by_project_id($value->project_id);echo $sum_budget;?></td>
                                                        </tr>
                                                      </table>                                                      
                                                      </div>
                                                    </div>
                                                    <?
                                                    if ($sum_budget<=0) {
                                                      $sum_budget=1;
                                                    }
                                                    $p_allocate=(int)(($sum_allocate['sum_all']/$sum_budget)*100);
                                                    $p_spend=(int)(($sum_allocate['sum_spend']/$sum_budget)*100);
                                                    if ($p_spend>100) {
                                                      $p_spend=100;
                                                    }
                                                    if ($p_allocate>100) {
                                                      $p_allocate=100;
                                                    }
                                                    ?>
                                                    <div class="progress progress-warning">
                                                      <div style="width: <?=$p_allocate?>%;" class="bar">
                                                      <table class="just-table">
                                                        <tr>
                                                          <td style="width:120px">ALLOCATE</td>
                                                          <td style="width:60px"><?echo $sum_allocate['sum_all'];?></td>
                                                        </tr>
                                                      </table>
                                                      </div>
                                                    </div>
                                                    <div class="progress progress-success">
                                                      <div style="width: <?=$p_spend?>%;" class="bar">
                                                      <table class="just-table">
                                                        <tr>
                                                          <td style="width:120px">SPENT</td>
                                                          <td style="width:60px"><?echo $sum_allocate['sum_spend'];?></td>
                                                        </tr>
                                                      </table>
                                                      
                                                      </div>
                                                    </div>
                                                  </td>
                                                  <td><?
                                                  echo number_format($outsource_bil['sum_all'], 0, '.', ',')."<br>(<font class='green-f'>".number_format($outsource_bil['sum_paid'], 0, '.', ',')."</font>)";
                                                  ?></td>
                                                  <td>
                                                    <?
                                                   if ($value->base_approve_stat['csd']===true) {                                                    
                                                        ?>
                                                        <p class="project-status-ok"><i class="icon-ok icon-white"></i>CSD</p>
                                                        <?
                                                    }else if($value->base_approve_stat['csd']=="reject"){
                                                        ?>
                                                        <p class="project-status-reject"><i class="icon-remove icon-white"></i>CSD </p>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <p class="project-status">CSD </p>
                                                        <?
                                                    }
                                                    if($value->base_approve_stat['hod']===true){
                                                        ?>
                                                        <p class="project-status-ok"><i class="icon-ok icon-white"></i>HOD </p>
                                                        <?
                                                    }else if($value->base_approve_stat['hod']=="reject"){
                                                        ?>
                                                        <p class="project-status-reject"><i class="icon-remove icon-white"></i>HOD </p>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <p class="project-status">HOD </p>
                                                        <?
                                                    }
                                                    
                                                    if ($value->base_approve_stat['fc']===true) {                                                    
                                                        ?>
                                                        <p class="project-status-ok"><i class="icon-ok icon-white"></i>FC </p>
                                                        <?
                                                    }else if($value->base_approve_stat['fc']=="reject"){
                                                        ?>
                                                        <p class="project-status-reject"><i class="icon-remove icon-white"></i>FC </p>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <p class="project-status">FC </p>
                                                        <?
                                                    }
                                                    if ($value->base_approve_stat['fc_oc']===true) {                                                    
                                                        ?>
                                                        <p class="project-status-ok"><i class="icon-ok icon-white"></i>OC </p>
                                                        <?
                                                    }else if($value->base_approve_stat['fc_oc']=="reject"){
                                                        ?>
                                                        <p class="project-status-reject"><i class="icon-remove icon-white"></i>OC </p>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <p class="project-status">OC </p>
                                                        <?
                                                    }
                                                    ?>
                                                  </td>
                                                  <td>                                                            
                                                  <?
                                                  $manager=$ci->m_user->get_user_by_login_name($value->project_cs);
                                                  if ($value->pass) {
                                                    ?>
                                                    <a href="<? echo site_url('project/edit_oc/'.$value->project_id)?>" class=""><?=$manager->nickname?><i class=" icon-pencil"></i></a><br>
                                                    <?
                                                  }else{
                                                    ?>
                                                    <a href="<? echo site_url('project/edit/'.$value->project_id)?>" class=""><?=$manager->nickname?><i class=" icon-pencil"></i></a><br>
                                                    <?
                                                  }
                                                  
                                                  $check_hod_assign=$ci->m_project->check_hod_assign_resource($value->project_id);
                                                  if ($check_hod_assign&&$value->pass) {
                                                  
                                                  ?><a class="btn btn-info btn-xs" href="<?=site_url("cs/work_sheet/".$value->project_id)?>">Work Sheet</a><?
                                                  }
                                                  ?>                 <br>      
                                                    <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/project_note/".$value->project_id)?>">
                                                      <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/project_note.png')?>">
                                                    </a>
                                                    <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/finan_note/".$value->project_id)?>">
                                                      <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/finance_note.png')?>">
                                                    </a>
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
        });
        function archive_job(project_id){
            if (confirm("Archive This Job")) {
                    window.open("<?echo site_url("cs/project_archive");?>/"+project_id,"_self");
                }
        }
        </script>