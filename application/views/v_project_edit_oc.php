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
</style>
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
                                <h3><?=$project->project_name?> </h3>
                            </div>
                        </div>
                        <div class="pull-right">
                        <div id="complete_region" class="control-group">
                            <font class="head-pull-right"><?=$project->status?> </font>
                            <?
                            if ($project->status!="Done"&&$project->status!="Cancel"&&$project->status!="Archive") {
                                
                                //$check_hod_assign=$ci->m_project->check_hod_assign_resource($project->project_id);
                                $check_all_oc_done=$ci->m_oc->check_all_done_oc_by_project_id($project->project_id);
                                if ($check_all_oc_done) {
                                    ?>
                                    <a href="javascript:done_job();" class="btn">Complete</a>

                                    <?
                                }
                            
                            ?>
                            <a href="javascript:cancel_job();" class="btn">Cancel</a>
                            <?
                            }else if($project->status=="Done"){
                                ?>
                                <a href="javascript:archive_job();" class="btn">Archive</a>
                                <?
                            }
                        ?>
                        </div>
                        </div>
                    </div>
                    <div class="block-content collapse in">
                        <form id="pro_add_form" class="form-horizontal" method="post" action="<?echo site_url('project/edit_project_action');?>">
                            <fieldset>
                                <div class="span12 no-margin-left">
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Client</label>
                                            <select id="company_id" class="chzn-select" name="project_client">
                                                <option value="no">---please select ------</option>
                                                <?
                                                foreach ($company as $key => $value) {
                                                    ?>
                                                    <option value="<?=$value->id?>"><?=$value->name?></option>
                                                    <?
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="span3 no-margin-left">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">BU</label>
                                            <select id="bu_id" name="project_bu">
                                                <option value="no">please select company</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="span2 no-margin-left">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Start Date</label>
                                            <input class="form-control datepicker" type="text" name="project_start" value="<?=$ci->m_time->unix_to_datepicker($project->project_start)?>">
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">End Date</label>
                                            <input class="form-control datepicker" type="text" name="project_end" value="<?=$ci->m_time->unix_to_datepicker($project->project_end)?>">
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Internal Unit</label>
                                            <select id="business_unit_id" class="chzn-select" name="business_unit_id">
                                                <option value="no">---please select ------</option>
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
                                
                                <div class="span3 no-margin-left">
                                    <div class="control-group">
                                        <label class="control-label-new" for="focusedInput">Job name</label>
                                        <input class="form-control" type="text" name="project_name" value="<?=$project->project_name?>">
                                        <input class="form-control" type="hidden" name="project_id" value="<?=$project->project_id?>">
                                    </div>
                                </div>
                                <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Accounting Unit</label>
                                            <select id="account_unit_id" class="chzn-select" name="account_unit_id">
                                                <option value="no">---please select ------</option>
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
                                <div class="span3">
                                    <div class="control-group">
                                        <label class="control-label-new" for="focusedInput">Project Value</label>
                                        <p id='project_value' class="p-view-only"></p>
                                    </div>
                                </div>
                                <div class="span3">
                                    <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/project_note/".$project->project_id)?>">
                                      <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/project_note.png')?>">
                                    </a>
                                    <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/finan_note/".$project->project_id)?>">
                                      <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/finance_note.png')?>">
                                    </a>
                                </div>

                                <div class="span12 no-margin-left">
                                    <h3>Resource Sheet <?
                                    $check_hod_assign=$ci->m_project->check_hod_assign_resource($project->project_id);
                                    if ($check_hod_assign) {
                                    
                                    ?><a class="btn" href="<?=site_url("cs/work_sheet/".$project->project_id)?>">Work Sheet</a><?
                                    }
                                    ?> </h3>
                                </div>
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered orange lowfont">
                                    <thead>
                                        <tr>
                                            <th >No.</th>
                                            <th >Task</th>
                                            <th >Type</th>
                                            <th >Approved Budget (HR)</th>
                                            <th class="ch-hr">Charge/Hr</th>
                                            <th class="tt-ch">Total Charge</th>
                                            <th >Assign Resource</th>
                                            <th >Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="res_t_body">
                                        <?
                                        $hour_rate_list = $ci->m_hour_rate->get_all_hour_rate();
                                        foreach ($r_sheet as $t_key => $task) {
                                            $no_strint="".$task->sort_order;
                                        ?>
                                        <tr>
                                            <td><input class="r-no-input" type="text" name="sort_order[<?=$task->r_id?>]" value="<?=(float)$no_strint?>"></td>
                                            <td>
                                                <input class="r-task-input" type="text" name="task_old[<?=$task->r_id?>]" value="<?=$task->task?>">
                                                <input class="" type="hidden" name="r_id_old[<?=$task->r_id?>]" value="<?=$task->r_id?>">
                                            </td>
                                            <td>
                                                <select disabled class="type_change" name="type_old[<?=$task->r_id?>]">
                                                <?
                                                $rate_selected=0;
                                                foreach ($hour_rate_list as $key => $value) {
                                                    ?>
                                                    <option id="<?=$value->id?>" data="<?=$value->hour_rate?>" value="<?=$value->id?>" <?if($value->id==$task->type){echo "selected";$rate_selected=$value->hour_rate;}?>><?=$value->name?></option>
                                                    <?
                                                }
                                                ?>                                                    
                                                </select>
                                                <input type="hidden" name="type_old[<?=$task->r_id?>]" value="<?=$task->type?>">
                                            </td>
                                            <td class="ap_budget"><input class="type_change" type="text" name="approve_budget_old[<?=$task->r_id?>]" value="<?=$task->approve_budget?>"></td>
                                            <td><?echo $rate_selected;?></td>
                                            <td><?echo $task->approve_budget*$rate_selected;?></td>
                                            <td>
                                                <table class="no-border">
                                                <?
                                                $allow_list=$ci->m_Rsheet->get_allow_list_by_r_id($task->r_id);
                                                $al_num=count($allow_list);
                                                foreach ($allow_list as $akey => $avalue) {
                                                   $auser=$this->m_user->get_user_by_login_name($avalue->resource_usn);
                                                   if ($al_num==$akey+1) {
                                                       echo $auser->nickname;
                                                   }else{
                                                        echo $auser->nickname.",";
                                                    }
                                                    /*?>
                                                    <tr >
                                                        <td>
                                                            <?
                                                            echo $auser->nickname;
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <?*/
                                                }
                                                ?>
                                                
                                            </table>
                                            </td>
                                            <td>
                                                <a iden="<?=$task->r_id?>" href="javascript:;" class="btn btn-danger del_res"><i class="icon-remove icon-white"></i></a>
                                                                                            
                                            </td>
                                        </tr>
                                        <?
                                        }
                                        ?>
                                        <tr id="last_tr">
                                            <td></td>
                                            <td><a id="add_res" href="javascript:;" class="btn btn-success">Add Task<i class="icon-plus icon-white"></i></a></td>
                                            <td>Grand Total</td>
                                            <td class="total"></td>
                                            <td></td>
                                            <td class="total"></td>
                                            <td>
                                            
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="span12 no-margin-left">
                                <div class="span6">
                                    <h3><a class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/pop_add_pce/".$project->project_id);?>"  ><i class="icon-plus icon-white"></i></a>PCE</h3>
                                </div>
                                <div class="span6">
                                    <h3>OC/IOC</h3>
                                    <input type="hidden" id="is_oc_change" name="is_oc_change" value="n">
                                </div>
                                <div id="pce_start" class="span12 no-margin-left">
                                    <hr>

                                </div>
                                <?
                                $project_value=0;
                                foreach ($pce_doc as $key => $pce) {
                                    $cur_time=$pce->id;
                                    $oc_1_list=$ci->m_oc->get_all_oc_by_pce_id($pce->id);
                                    $project_value+=$pce->pce_amount;
                                    ?>

                                        <div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left pce-hold">
                                            <div class="span6" id="pce_inner_<?echo $cur_time;?>">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td> <a class="btn btn-info fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/cs_set_sign_time/".$cur_time);?>"  ><i class="icon-pencil icon-white"></i></a>
                                                    <?
                                                    $have_rewrite=$ci->m_pce->get_pce_rewrite_child_by_id($pce->id);
                                                    if (isset($have_rewrite->id)) {
                                                        ?>
                                                         <a class="btn btn-warning fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/rewrite_pce_view/".$cur_time);?>"  ><i class="icon-list icon-white"></i></a>
                                                        <?
                                                    }
                                                    ?> </td>
                                                    <td></td><td style="text-align:right;"><a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn-atom btn-atom-warning pce_rewrite">Revise</a></td>
                                                </tr>
                                                <tr><td class="first-ta">PCE#</td><td colspan="2" style="text-align: left;">
                                                <a href="<?echo site_url("project/view_sign_pce/".$pce->id)?>" target="_blank"><?=$pce->pce_no?>&nbsp;&nbsp;<img src="<?echo site_url("img/pdf_img.png")?>"></a>
                                                <input type="hidden" name="pce_filename[<?echo $cur_time;?>]" value="old__<?=$pce->filename?>">
                                                <input type="hidden" name="pce_no[<?echo $cur_time;?>]" value="<?=$pce->pce_no?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Description</td><td colspan="2" style="text-align: left;">
                                                     <?=$pce->pce_des?>
                                                    <input type="hidden" name="pce_des[<?echo $cur_time;?>]" value="<?=$pce->pce_des?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Amount</td><td colspan="2" style="text-align: left;">
                                                    <?=number_format($pce->pce_amount, 2, '.', ',')?>
                                                <input type="hidden" name="pce_amount[<?echo $cur_time;?>]" value="<?=$pce->pce_amount?>">
                                                </td></tr>
                                                <td></td><td colspan="2" style="text-align: left;">
                                                    <?
                                                    $hod_all_approve=true;
                                                    $hod_reject_flag=false;
                                                    foreach ($pce->hod_list as $hlistkey => $hlistvalue) {
                                                        if($hlistvalue->approve=="ns"){
                                                            $hod_all_approve=false;
                                                        }
                                                        if($hlistvalue->approve=="n"){
                                                            $hod_reject_flag=true;
                                                        }
                                                    }
                                                   if ($pce->csd_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-ok icon-white"></i>CSD </a>
                                                        <?
                                                    }else if($pce->csd_sign_status=="n"){
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-remove icon-white"></i>CSD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>" >CSD </a>
                                                        <?
                                                    }
                                                    if($hod_reject_flag){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-remove icon-white"></i>HOD </a>
                                                        <?
                                                    }else if (isset($pce->hod_list[$user_data->username])&&$pce->hod_list[$user_data->username]->approve=="y") {
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else if($hod_all_approve){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>">HOD </a>
                                                        <?
                                                    }
                                                    
                                                    if ($pce->fc_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-ok icon-white"></i>FC </a>
                                                        <?
                                                    }else if($pce->fc_sign_status=="n"){
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-remove icon-white"></i>FC </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>" >FC </a>
                                                        <?
                                                    }
                                                    ?>
                                                </td></tr>
                                            </table>
                                            </div>
                                            <div id="oc_region_<?=$cur_time?>" class="span6 oc_hold_special">
                                            <?
                                            foreach ($oc_1_list as $key => $oc) {
                                                $oc_id=$oc->id
                                                ?>

                                                    <div id="oc_cur_<?echo $oc_id;?>" class="span12 no-margin-left">
                                                    <table class="table table-noborder">
                                                        <tr>
                                                            <td colspan="2">
                                                                <a class="btn-atom btn-atom-success fancybox" data-fancybox-type="iframe" href="<?=site_url("project/oc_payment/".$oc_id)?>">Billing due <i class="icon-plus icon-white"></i></a>
                                                                <?
                                                                if ($oc->is_done=="n") {
                                                                    ?>
                                                                    <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Done" class="btn-atom btn-atom-info oc_done">Not Done</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Undone" class="btn-atom btn-atom-info oc_done">Done</a>
                                                                    <?
                                                                }
                                                                ?>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <a id="" href="javascript:;" iden="<?=$oc_id?>" class="btn-atom btn-atom-danger oc_delete">DELETE</a>
                                                            </td>
                                                            </tr>
                                                    </table>
                                                    <table class="table table-noborder">

                                                        
                                                            <tr><td class="first-ta">OC#</td><td colspan="2" style="text-align: left;">
                                                            <a href="<?echo site_url("project/view_sign_oc/".$oc->id)?>" target="_blank"><?=$oc->oc_no?></a>&nbsp;|&nbsp;
                                                            <a href="<?echo site_url("media/real_pdf/".$oc->filename_pce)?>" target="_blank"><?=$oc->pce->pce_no?></a>   
                                                            </td></tr>
                                                            <tr><td class="first-ta">Description#</td><td colspan="2" style="text-align: left;">
                                                            <?=$oc->oc_des?>
                                                            </td></tr>
                                                            <tr><td class="first-ta">Amount#</td><td colspan="2" style="text-align: left;">
                                                            <?=number_format($oc->oc_amount, 2, '.', ',')?>
                                                            </td></tr>
                                                            <td></td><td colspan="2" style="text-align: left;">
                                                                <?
                                                                    if ($oc->status=="y") {
                                                                           
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe"><i class="icon-ok icon-white"></i>FC </a>
                                                                            <?
                                                                        }else if($oc->status=="n"){
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-danger fancybox" data-fancybox-type="iframe"><i class="icon-remove icon-white"></i>FC </a>
                                                                            <?
                                                                        }else{
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn fancybox" data-fancybox-type="iframe">FC </a>                 
                                                                            <?
                                                                        }
                                                                    ?>
                                                            </td></tr>
                                                        <tr>
                                                            <td colspan="3"><hr id="hr_<?echo $oc_id;?>"></td>
                                                        </tr>
                                                    </table>    
                                                    </div>                                                    
                                                    
                                                <?
                                            }
                                            ?>
                                            <div class="span12 no-margin-left" id="before_add_oc_but_<?=$cur_time?>">
                                                <table class="table table-noborder">
                                                    <tr>
                                                        <td style="text-align:center">
                                                           <a class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/pop_add_oc/".$pce->id);?>"  >Add OC/IOC</a>  
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left outsource-cls">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="out_but_<?=$cur_time?>" iden="<?=$cur_time?>" togStat="hide" href="javascript:;" class="btn btn-large btn-block toggle_outsource">Out source</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div id="outsource_<?echo $cur_time;?>" class="span12 no-margin-left" style="display:none">
                                            <div class="span12 no-margin-left">
                                                <h5>Out Source</h5>
                                                <h5>PCE# <?=$pce->pce_no?></h5>
                                                <div class="span4">
                                                    <?=$pce->pce_des?>
                                                </div>
                                                <div class="span4">
                                                    Revise &nbsp;<input class="pay_revise" type="checkbox" style="width:20px;height:20px;">
                                                </div>
                                            </div>
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                                <thead>
                                                    <tr>
                                                        <th>QT#</th>
                                                        <th></th>
                                                        <th>description</th>
                                                        <th>Cost</th>
                                                        <th>Charge</th>
                                                        <th>Margin</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_out_<?=$cur_time?>">
                                                <?
                                                foreach ($pce->outsource as $outsource_key => $outsource) {
                                                    $up_time=$outsource->id;
                                                    $sum_bill_paid=0;
                                                    foreach ($outsource->bill_paid as $bkey => $bvalue) {
                                                        $sum_bill_paid+=$bvalue->amount;
                                                    }
                                                    ?>

                                                    <tr class="out_<?=$cur_time?>" iden="<?=$cur_time?>">
                                                        <td><input class="" type="text" name="qt_no[<?=$cur_time?>][]" value="<?=$outsource->qt_no?>">
                                                        <input class="" type="hidden" name="qt_id[<?=$cur_time?>][]" value="<?=$outsource->id?>">
                                                        </td>
                                                        <td>
                                                            <div class="control-group">
                                                                <?
                                                                if ($outsource->filename!="no_file") {
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="<?echo site_url("media/real_pdf/".$outsource->filename)?>" target="_blank">ดูไฟล์</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="" target="_blank"></a>
                                                                    <?
                                                                }
                                                                ?>
                                                                
                                                                <span class="btn btn-success fileinput-button">
                                                                    
                                                                    <i class="glyphicon glyphicon-plus"></i>
                                                                    <span id="but_upload_<?=$cur_time?>">เลือกไฟล์ PDF</span>
                                                                     <!-- The file input field used as target for the file upload widget -->
                                                                    <input id="qtupload_<?=$up_time?>" type="file">
                                                                    <input id="qt_file_<?=$up_time?>" type="hidden" name="qt_filename[<?=$cur_time?>][]" value="old__<?=$outsource->filename?>">
                                                                </span>
                                                                <script type="text/javascript">
                                                                    $('#qtupload_<?=$up_time?>').fileupload({
                                                                            previewThumbnail: false,
                                                                            url: '<?php echo site_url('upload_handler/pdf '); ?>',
                                                                            dataType: 'json',
                                                                            beforeSend: function() {
                                                                                $('#but_upload_<?=$up_time?>').html("Please wait");
                                                                                $('#qtupload_<?=$up_time?>').attr("disabled","");
                                                                            },
                                                                            done: function(e, data) {
                                                                                //console.log(data);

                                                                                $.each(data.result.files, function(index, file) {
                                                                                    //console.log(file);
                                                                                    if (file.error == "File is too big") {
                                                                                        alert("File is too big exceed 100 MB");
                                                                                        $("#qt_file_<?=$up_time?>").val("");
                                                                                    }else if (file.error == "Filetype not allowed") {
                                                                                        alert("Filetype not allowed");
                                                                                       $("#qt_file_<?=$up_time?>").val("");
                                                                                    } else {
                                                                                        alert("Upload Complete file " + file.name);
                                                                                        $("#qt_file_<?=$up_time?>").val(file.name);
                                                                                        $('#qt_view_<?=$up_time?>').attr("href",'<?echo site_url("media/temp/")?>/'+file.name);
                                                                                        $('#qt_view_<?=$up_time?>').html("ดูไฟล์");
                                                                                    }
                                                                                    $('#but_upload_<?=$up_time?>').html("เลือกไฟล์ PDF");

                                                                                    $('#qtupload_<?=$up_time?>').removeAttr("disabled");
                                                                                });

                                                                            },
                                                                            progressall: function(e, data) {
                                                                            }
                                                                        }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                </script>
                                                            </div>
                                                        </td>
                                                        <td><input class="" type="text" name="qt_des[<?=$cur_time?>][]" value="<?=$outsource->qt_des?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_cost[<?=$cur_time?>][]" value="<?=$outsource->qt_cost?>"><br><?="ใช้ไปแล้ว : ".number_format($sum_bill_paid)?></td>
                                                        <td><input class="out_change" type="text" name="qt_charge[<?=$cur_time?>][]" value="<?=$outsource->qt_charge?>"></td>
                                                        <td class="out_margin"><?echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_charge*100,2);?>%</td>
                                                        <td>
                                                            <a iden="<?=$up_time?>" href="javascript:;" class="btn btn-danger del_outlist"><i class="icon-remove icon-white"></i></a>                                                                 
                                                            <a class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?=site_url("project/outsource_payment/".$up_time)?>"><i class="icon-plus icon-white"></i>Pay date</a>               
                                                        </td>
                                                    </tr>
                                                    <?
                                                }
                                                ?>
                                                    <tr id="be_out_<?=$cur_time?>">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Grand Total</td>
                                                        <td class="total_cost"></td>
                                                        <td class="total_charge"></td>
                                                        <td class="total_margin"></td>
                                                        <td>
                                                            <a iden="<?=$cur_time?>" href="javascript:;" class="btn btn-success add_out_list"><i class="icon-plus icon-white"></i></a>                                                            
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="del-but_<?=$cur_time?>" class="span12 no-margin-left">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-danger pce_delete">DELETE PCE</a>   
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <hr id="hr_<?echo $cur_time;?>">
                                        <script type="text/javascript">
                                        cal_outSource('<?=$cur_time?>');
                                        </script>
                                    <?
                                }
                                ?>
                                <div id="before_add_pce" class="span12 no-margin-left">
                                </div>
                                 <div id="before_add_oc" class="span12 no-margin-left">
                                </div>
                                </div>

                                <div class="span12 no-margin-left">
                                    <input type="hidden" id="is_rewrite" value="0">
                                    <input type="hidden" id="is_rewrite_complete" value="0">
                                    <?
                                    if ($project->status!="Done"&&$project->status!="Cancel"&&$project->status!="Archive") {
                                        ?>
                                        <a href="javascript:save_submit();" class="btn btn-info">Submit</a>
                                        <?
                                    }
                                    ?>
                                    <a href="<?=site_url("cs")?>" class="btn btn-info">BACK</a>
                                    <a href="javascript:save_dup();" class="btn btn-info">Duplicate</a>
                                </div>
                            </fieldset>
                        </form>
                        
                    </div>
                </div>
                <!-- /block -->
            </div>
        </div>
    </div>
    <!--/.fluid-container-->
    
    <script>
    /*jslint unparam: true */
    /*global window, $ */
    $(function() {
        $( "#res_t_body" ).sortable({
          placeholder: "ui-state-highlight",
        });
        $(".fancybox").fancybox({
            fitToView   : false,
            width       : '90%',
            height      : '90%',
            autoSize    : false,
            closeClick  : false,
            openEffect  : 'none',
            closeEffect : 'none'
        });
        $( "a" ).tooltip({});
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy"
        });
        $("#company_id").val("<?=$project->project_client?>");
        $("#business_unit_id").val("<?=$project->business_unit_id?>");
        $("#account_unit_id").val("<?=$project->account_unit_id?>");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/ajax_bu_html"); ?>",
                data: {
                    company_id: $("#company_id").val()
                }
            })
            .done(function(data) {
                $("#bu_id").html(data);
                $("#bu_id").val("<?=$project->project_bu?>"); 
            });

        $('.datetimepicker').datetimepicker();
        $(".chzn-select").chosen({
            width: "75%"
        });
        cal_rSheet();
        $("#project_value").html("<?=number_format($project_value)?>")
    });
    $(document).on("change", "#company_id", function() {
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/ajax_bu_html"); ?>",
                data: {
                    company_id: $("#company_id").val()
                }
            })
            .done(function(data) {
                $("#bu_id").html(data);
            });
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
        var all_row=$("#res_t_body").children("tr");
        var sum_h=0;
        var sum_p=0;
        for (var i = 0; i <all_row.length-1; i++) {
            var cur_coll=all_row.eq(i).children("td");
            //cur_coll.eq(0).html((i+1)+"");
            h_p=parseInt(cur_coll.eq(5).html());
            hr=parseInt(cur_coll.eq(3).children("input.type_change").val());
            sum_h+=hr;
            sum_p+=h_p;
            //console.log(sum_h+" -"+sum_p );
        };
        var last_row=all_row.eq(all_row.length-1).children("td");
        last_row.eq(3).html(""+sum_h);
        last_row.eq(5).html(""+sum_p);

    }

    $(document).on("change", ".out_change", function() {
        var cur_row=$(this).parent().parent();
        var cur_cost=cur_row.find("input.out_change").eq(0);
        var cur_charge=cur_row.find("input.out_change").eq(1);
        var cost=parseInt(cur_cost.val());
        var charge=parseInt(cur_charge.val());
        var margin=(charge-cost)/charge*100;
        cur_row.find(".out_margin").html(""+margin.toFixed(2)+"%");
        var id=cur_row.attr("iden");
        cal_outSource(id);

    });
    
    $(document).on("click", "#add_res", function() {
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/ajax_add_res_html"); ?>",
                data: {
                    no: "no"
                }
            })
            .done(function(data) {
                $("#last_tr").before(data);
            });
        
    });
    $(document).on("click", ".add_out_list", function() {
        var id=$(this).attr("iden");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/ajax_add_outlist_html"); ?>",
                data: {
                    "cur_time": id
                }
            })
            .done(function(data) {
                $("#be_out_"+id).before(data);
            });
        
    });
    $(document).on("click", ".pce_rewrite", function() {
        var pce_id=$(this).attr("iden");
            $.ajax({
                    method: "POST",
                    url: "<?php echo site_url("project/ajax_rewrite_pce_html"); ?>",
                    data: {
                        "pce_id": pce_id,
                        "from_oc": pce_id,
                    }
                })
                .done(function(data) {
                    $("#del-but_"+pce_id).remove();
                    $("#outsource-but_"+pce_id).remove();
                    $("#outsource_"+pce_id).remove();
                    $("#pce_cur_"+pce_id).remove();
                    $("#hr_"+pce_id).before(data);
                    var num_revise=parseInt($("#is_rewrite").val());
                    num_revise+=1;
                    $("#is_rewrite").val(""+num_revise);
                });
        
    });
    $(document).on("click", ".pce_rewrite_cancel", function() {
        var pce_id=$(this).attr("iden");
            $.ajax({
                    method: "POST",
                    url: "<?php echo site_url("project/ajax_rewrite_pce_cancel_html"); ?>",
                    data: {
                        "pce_id": pce_id,
                        "from_oc": pce_id,
                    }
                })
                .done(function(data) {
                    $("#outsource-but_"+pce_id).remove();
                    $("#outsource_"+pce_id).remove();
                    $("#pce_cur_"+pce_id).remove();
                    $("#hr_"+pce_id).before(data);
                    var num_revise=parseInt($("#is_rewrite").val());
                    num_revise-=1;
                    $("#is_rewrite").val(""+num_revise);
                });
        
    });
    $(document).on("click", ".pce_rewrite_ok", function() {

        var pce_id=$(this).attr("iden");
        var pce_no=$("#pce_no_"+pce_id).val();
        var pce_file=$("#temp_f_name_rewrite_"+pce_id).val();
        var pce_des=$("#pce_des_"+pce_id).val();
        var pce_amount=$("#pce_amount_"+pce_id).val();

        var qt_no=$("input[name='qt_no["+pce_id+"][]']").serialize();
        var qt_id=$("input[name='qt_id["+pce_id+"][]']").serialize();
        var qt_des=$("input[name='qt_des["+pce_id+"][]']").serialize();
        var qt_cost=$("input[name='qt_cost["+pce_id+"][]']").serialize();
        var qt_charge=$("input[name='qt_charge["+pce_id+"][]']").serialize();
        var qt_filename=$("input[name='qt_filename["+pce_id+"][]']").serialize();
        if (pce_file!="old") {
            $.ajax({
                    method: "POST",
                    url: "<?php echo site_url("project/ajax_rewrite_pce_save"); ?>",
                    data: "pce_id="+pce_id+"&project_id=<?=$project->project_id?>&"+
                    "pce_no="+pce_no+"&"+
                    "pce_file="+pce_file+"&"+
                    "pce_des="+pce_des+"&"+
                    "pce_amount="+pce_amount+"&"+
                    "from_oc=yes&"+
                    qt_no+"&"+
                    qt_id+"&"+
                    qt_des+"&"+
                    qt_cost+"&"+
                    qt_charge+"&"+
                    qt_filename
                })
                .done(function(data) {
                    $("#outsource-but_"+pce_id).remove();
                    $("#outsource_"+pce_id).remove();
                    $("#pce_cur_"+pce_id).remove();
                    $("#hr_"+pce_id).before(data);
                    $("#hr_"+pce_id).remove();
                    //$("option[value='"+pce_id+"']").remove();
                    var num_revise=parseInt($("#is_rewrite").val());
                    num_revise-=1;
                    $("#is_rewrite").val(""+num_revise);
                    $("#is_rewrite_complete").val("yes");
                });
            }else{
                alert("กรุณาอัพโหลดไฟล์");
            }
        
    });
    $(document).on("click", ".del_res", function() {
        $("#before_add_pce").before('<input class="form-control" type="hidden" name="task_del[]" value="'+$(this).attr("iden")+'">');
        $(this).parent().parent().fadeOut(300,function(){
            $(this).remove();
            cal_rSheet();
        });

    });
    $(document).on("click", ".del_outlist", function() {
        $("#before_add_pce").before('<input class="form-control" type="hidden" name="outsource_del_list[]" value="'+$(this).attr("iden")+'">');
        $(this).parent().parent().fadeOut(300,function(){
            $(this).remove();
        });
        
    });
    $(document).on("click", ".pce_delete", function() {
        if (confirm("Confirm DELETE PCE")) {
            var id=$(this).attr("iden");
            $.ajax({
                    method: "POST",
                    url: "<?php echo site_url("project/check_pce_paid_billed"); ?>",
                    data: "pce_id="+id
                })
                .done(function(data) {
                    if (data['flag']=="OK") {
                        $("#del-but_"+id).fadeOut(300,function(){
                            $(this).remove();
                        });
                        $("#outsource-but_"+id).fadeOut(300,function(){
                            $(this).remove();
                        });
                        $("#pce_cur_"+id).fadeOut(300,function(){
                            $(this).remove();
                        });
                        $("#outsource_"+id).fadeOut(300,function(){
                            $(this).remove();
                        });
                        $("#hr_"+id).fadeOut(300,function(){
                            $(this).remove();
                        });
                        $("#before_add_pce").before('<input class="form-control" type="hidden" name="pce_del_list[]" value="'+id+'">');
                    }else{
                        alert(data['flag']);
                    }
                });
            
        };
    });
    $(document).on("click", ".oc_delete", function() {
        if (confirm("Confirm del")) {
            var id=$(this).attr("iden");
            $("#oc_cur_"+id).fadeOut(300,function(){
                $(this).remove();
            });
            $("#hr_"+id).fadeOut(300,function(){
                $(this).remove();
            });
            $("#before_add_oc").before('<input class="form-control" type="hidden" name="oc_del_list[]" value="'+id+'">');
            $("#is_oc_change").val("y");
        };
    });
    $(document).on("click", ".oc_done", function() {
        if (confirm($(this).attr("stat")+" This OC#")) {
            var cur=$(this);
            var id=$(this).attr("iden");
            $.ajax({
                    method: "POST",
                    url: "<?php echo site_url("project/done_oc"); ?>",
                    data: {
                        "id": id,
                    }
                })
                .done(function(data) {
                    if (data['flag']=="OK") {
                        cur.html(data['stat']);
                        cur.attr("stat",data['stat2']);
                    }else{
                        alert(data['flag']);

                    }
                    check_get_complete_button();
                });
        };
    });
    function check_get_complete_button(){
        $.ajax({
                    method: "POST",
                    url: "<?php echo site_url("project/check_get_complete_button"); ?>",
                    data: {
                        "id": "<?=$project->project_id?>",
                    }
                })
                .done(function(data) {
                    $("#complete_region").html(data);
                });
    }
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


function save_n(){
    document.getElementById("pro_add_form").submit();
}
$(document).on("change", ".out_change", function() {
    ch=$(".pay_revise");
    for (var i = 0; i < ch.length; i++) {
        ch[i].checked=true;
    };
});
function check_is_pay_revise(){
    ch=$(".pay_revise");
    var revise=false;
    for (var i = 0; i < ch.length; i++) {
        if(ch[i].checked){
            revise=true;
        }
    };
    return revise;
}
function save_submit(){
    var check_pay_revise=check_is_pay_revise();
    if (parseInt($("#is_rewrite").val())<=0) {
        if ($("#is_oc_change").val()=="y") {
            var oc_del_list=$("input[name='oc_del_list[]']").serialize()
            if (confirm("OC have change")) {
                if ($("#is_rewrite_complete").val()=="yes"||check_pay_revise) {
                    $("#pro_add_form").append('<input type="hidden" name="submit_job" value="yes">');
                        document.getElementById("pro_add_form").submit();
                }else{
                    $.ajax({
                        method: "POST",
                        url: "<?php echo site_url("project/check_project_bill_date_ready/"); ?>",
                        data: "save=<?=$project->project_id?>&"+oc_del_list
                    })
                    .done(function(data) {
                      if (data['flag']=="OK") {
                        $("#pro_add_form").append('<input type="hidden" name="submit_job" value="yes">');
                        document.getElementById("pro_add_form").submit();
                      }else{
                        alert(data['flag']);
                        //console.log(data)
                      }
                    });
                }
            }
        }else{
            if (confirm("confirm submit")) {
                if ($("#is_rewrite_complete").val()=="yes"||check_pay_revise) {
                    $("#pro_add_form").append('<input type="hidden" name="submit_job" value="yes">');
                        document.getElementById("pro_add_form").submit();
                }else{
                    $.ajax({
                        method: "POST",
                        url: "<?php echo site_url("project/check_project_bill_date_ready/"); ?>",
                        data: "save=<?=$project->project_id?>"
                    })
                    .done(function(data) {
                      if (data['flag']=="OK") {
                        $("#pro_add_form").append('<input type="hidden" name="submit_job" value="yes">');
                        document.getElementById("pro_add_form").submit();
                      }else{
                        alert(data['flag']);
                        //console.log(data)
                      }
                    });
                }
                
            }
        };
    }else{
        alert("ยังมี PCE# ที่ยังอยู่ระหว่าง Revise");
    }
    
}
function save_dup(){
            if (confirm("Duplicate this project")) {
                    $("#pro_add_form").attr('action','<?echo site_url('project/dup_project_action');?>');
                        document.getElementById("pro_add_form").submit();
                
            }
    
}
function done_job(){
    if (confirm("Complete This Job")) {
            window.open("<?echo site_url("cs/project_done/".$project->project_id);?>","_self");
        }
}
function archive_job(){
    if (confirm("Archive This Job")) {
            window.open("<?echo site_url("cs/project_archive/".$project->project_id);?>","_self");
        }
}
function cancel_job(){
    if (confirm("Cancel This Job")) {
            window.open("<?echo site_url("cs/project_cancel/".$project->project_id);?>","_self");
        }
}
    </script>


