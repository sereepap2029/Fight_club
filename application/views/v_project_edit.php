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
                                <h3><?=$project->project_name?></h3>
                            </div>
                        </div>
                        <div class="pull-right">
                        <div class="control-group">
                            <font class="head-pull-right"><?=$project->status?> </font>
                            <a href="javascript:cancel_job();" class="btn">Cancel</a>
                        <a href="javascript:save_submit();" class="btn btn-info">Submit</a>
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
                                    <h3>Resource Sheet<?/*
                                    $check_hod_assign=$ci->m_project->check_hod_assign_resource($project->project_id);
                                    if ($check_hod_assign) {
                                    
                                    ?><a class="btn" href="<?=site_url("cs/work_sheet/".$project->project_id)?>">Work Sheet</a><?
                                    }*/
                                    ?> </h3>
                                </div>
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                    <thead>
                                        <tr>
                                            <th >No.</th>
                                            <th >Task</th>
                                            <th >Type</th>
                                            <th >Approved Budget (HR)</th>
                                            <th class="ch-hr">Charge/Hr</th>
                                            <th class="tt-ch">Total Charge</th>
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
                                                <select class="type_change" name="type_old[<?=$task->r_id?>]">
                                                <?
                                                $rate_selected=0;
                                                foreach ($hour_rate_list as $key => $value) {
                                                    ?>
                                                    <option id="<?=$value->id?>" data="<?=$value->hour_rate?>" value="<?=$value->id?>" <?if($value->id==$task->type){echo "selected";$rate_selected=$value->hour_rate;}?>><?=$value->name?></option>
                                                    <?
                                                }
                                                ?>
                                                    
                                                </select>
                                                <?/*<input type="hidden" name="type_old[<?=$task->r_id?>]" value="<?=$task->type?>">*/?>
                                            </td>
                                            <td class="ap_budget"><input class="type_change" type="text" name="approve_budget_old[<?=$task->r_id?>]" value="<?=$task->approve_budget?>"></td>
                                            <td><?echo $rate_selected;?></td>
                                            <td><?echo $task->approve_budget*$rate_selected;?></td>
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
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="span12 no-margin-left">
                                    <h3><a class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/pop_add_pce/".$project->project_id);?>"  ><i class="icon-plus icon-white"></i></a>PCE</h3>
                                </div>
                                <div id="pce_start" class="span12 no-margin-left">
                                    <hr>

                                </div>
                                <?
                                $project_value=0;
                                foreach ($pce_doc as $key => $pce) {
                                    $cur_time=$pce->id;
                                    $project_value+=$pce->pce_amount;
                                    ?>
                                    <div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left pce-hold">
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
                                                <tr><td></td><td colspan="2" style="text-align: left;">
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
                                        <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left">
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
                                                <div>
                                                    <?=$pce->pce_des?>
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
                                <div class="span12 no-margin-left">
                                <input type="hidden" id="is_rewrite" value="0">
                                    <a href="javascript:save_n();" class="btn btn-info">Save</a>
                                    <a href="javascript:save_submit();" class="btn btn-info">Submit</a>
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
        var all_row=$("#res_t_body").find("tr");
        var sum_h=0;
        var sum_p=0;
        for (var i = 0; i <all_row.length-1; i++) {
            var cur_coll=all_row.eq(i).find("td");
            //cur_coll.eq(0).html((i+1)+"");
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
    $(document).on("click", "#add_pce", function() {
        var pce_no=$("#pce_no").val();
        var pce_des=$("#pce_des").val();
        var pce_amount=$("#pce_amount").val();
        var pce_file=$("#temp_f_name").val();
        if (pce_no==""||pce_des==""||pce_amount==""||pce_file=="") {
            alert("กรอกข้อมูลให้ครบทุกช่อง พร้อม upload file");
        }else{
            $.ajax({
                    method: "POST",
                    url: "<?php echo site_url("project/ajax_add_pce_html"); ?>",
                    data: {
                        "pce_no": pce_no,
                        "pce_des": pce_des,
                        "pce_amount": pce_amount,
                        "pce_file": pce_file
                    }
                })
                .done(function(data) {
                    $("#before_add_pce").before(data);
                    $("#pce_no").val("");
                    $("#pce_des").val("");
                    $("#pce_amount").val("");
                    $("#temp_f_name").val("");
                    $('#progress .progress-bar').css(
                            'width',
                            '0%'
                        );
                });
        }
        
    });

    $(document).on("click", ".pce_rewrite", function() {
        var pce_id=$(this).attr("iden");
            $.ajax({
                    method: "POST",
                    url: "<?php echo site_url("project/ajax_rewrite_pce_html"); ?>",
                    data: {
                        "pce_id": pce_id,
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
                    var num_revise=parseInt($("#is_rewrite").val());
                    num_revise-=1;
                    $("#is_rewrite").val(""+num_revise);
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

$(function() {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = '<?php echo site_url('upload_handler/pdf '); ?>';
    $('#fileupload').fileupload({
            previewThumbnail: false,
            url: url,
            dataType: 'json',
            beforeSend: function() {
                $('#progress .progress-bar').css(
                    'width',
                    '10%'
                );
            },
            done: function(e, data) {
                //console.log(data);

                $.each(data.result.files, function(index, file) {
                    //console.log(file);
                    if (file.error == "File is too big") {
                        alert("File is too big exceed 100 MB");
                        $("#temp_f_name").val("");
                        $('#progress .progress-bar').css(
                            'width',
                            '0%'
                        );
                    }else if (file.error == "Filetype not allowed") {
                        alert("Filetype not allowed");
                       $("#temp_f_name").val("");
                       $('#progress .progress-bar').css(
                            'width',
                            '0%'
                        );
                    } else {
                        alert("Upload Complete file " + file.name);
                        $("#temp_f_name").val(file.name);
                    }
                });

            },
            progressall: function(e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            }
        }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
        
});
function save_n(){
    if (parseInt($("#is_rewrite").val())<=0) {
        document.getElementById("pro_add_form").submit();
    }else{
        alert("ยังมี PCE# ที่ยังอยู่ระหว่าง Revise");
    }
}
function save_submit(){
    if (parseInt($("#is_rewrite").val())<=0) {
        $("#pro_add_form").append('<input type="hidden" name="submit_job" value="yes">');
        document.getElementById("pro_add_form").submit();
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
function cancel_job(){
    if (confirm("Cancel This Job")) {
            window.open("<?echo site_url("cs/project_cancel/".$project->project_id);?>","_self");
        }
}
    </script>


