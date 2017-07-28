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
                                <h3><?=$project->project_name?></h3>
                            </div>
                        </div>
                        <div class="pull-right">
                        <div class="control-group">
                            <font class="head-pull-right"><?=$project->status?> </font>
                        </div>
                        </div>
                    </div>
                    <div class="block-content collapse in">
                        <form id="pro_add_form" class="form-horizontal" method="post" action="<?echo site_url('project/edit_project_action');?>">
                           
                                <div class="span12 no-margin-left">
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">company</label>
                                            <p class="p-view-only"><?=$company->name?></p>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">BU</label>
                                            <p class="p-view-only"><?=$bu->bu_name?></p>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Start Date</label>
                                            <p class="p-view-only"><?=$ci->m_time->unix_to_datepicker($project->project_start)?></p>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">End Date</label>
                                            <p class="p-view-only"><?=$ci->m_time->unix_to_datepicker($project->project_end)?></p>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Internal Unit</label>
                                                <?
                                                foreach ($business_list as $key => $value) {
                                                    if ($project->business_unit_id==$value->id) {
                                                        ?>
                                                        <p class="p-view-only"><?=$value->name?></p>
                                                        <?
                                                        break;
                                                    }
                                                    
                                                }
                                                ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="span3 no-margin-left">
                                    <div class="control-group">
                                        <label class="control-label-new" for="focusedInput">Job name</label>
                                        <p class="p-view-only"><?=$project->project_name?></p>
                                        
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
                                    <h3>Resource Sheet</h3>
                                </div>
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                    <thead>
                                        <tr>
                                            <th >No.</th>
                                            <th >Task</th>
                                            <th >Type</th>
                                            <th class="ch-hr">Approved Budget (HR)</th>
                                            <th class="ch-hr">Charge/Hr</th>
                                            <th class="tt-ch">Total Charge</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="res_t_body">
                                        <?
                                        $total_hr = 0;
                                        $total_chg = 0;
                                       $countnum=0;
                                        foreach ($r_sheet as $t_key => $task) {
                                        $no_strint="".$task->sort_order;
                                        ?>
                                        <tr>
                                            <td><?=(float)$no_strint?> </td>
                                            <td>
                                                <p><?=$task->task?></p>
                                                <input class="" type="hidden" name="r_id[]" value="<?=$task->r_id?>">
                                            </td>
                                            <td>
                                                    
                                                    <?=$r_sheet[$t_key]->type_obj->name?>
                                                    <?$approve_budget=$r_sheet[$t_key]->approve_budget;?> 
                                                    <?$total_hr+=$approve_budget;?>
                                                    <?$total_chg+=$approve_budget*$r_sheet[$t_key]->type_obj->hour_rate;?>
                                                    
                                            </td>
                                            <td><p> <?=$r_sheet[$t_key]->approve_budget?></p></td>
                                            <td><?echo $r_sheet[$t_key]->type_obj->hour_rate;?></td>
                                            <td><?echo $approve_budget*$r_sheet[$t_key]->type_obj->hour_rate;?></td>
                                        </tr>
                                        <?
                                        }
                                        ?>
                                        <tr id="last_tr">
                                            <td></td>
                                            <td></td>
                                            <td>Grand Total</td>
                                            <td class="total"><?echo $total_hr;?></td>
                                            <td></td>
                                            <td class="total"><?echo $total_chg;?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                </fieldset>
                                <div class="span12 no-margin-left">
                                    <h3>PCE</h3>
                                </div>
                                <div class="span12 no-margin-left">
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
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn fancybox " data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>" >CSD </a>
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
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn fancybox btn-warning" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>">Waiting for HOD Approve</a>
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
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn fancybox " data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>" >FC </a>
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
                                                    </tr>
                                                </thead>
                                                <tbody id="table_out_<?=$cur_time?>">
                                                <?
                                                foreach ($pce->outsource as $outsource_key => $outsource) {
                                                    $up_time=$outsource->id;
                                                    ?>

                                                    <tr class="out_<?=$cur_time?>" iden="<?=$cur_time?>">
                                                        <td><?=$outsource->qt_no?>
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
                                                            </div>
                                                        </td>
                                                        <td><?=$outsource->qt_des?></td>
                                                        <td><?=number_format($outsource->qt_cost)?>
                                                        <input class="out_change" type="hidden" value="<?=$outsource->qt_cost?>">
                                                        </td>
                                                        <td><?=number_format($outsource->qt_charge)?>
                                                        <input class="out_change" type="hidden" value="<?=$outsource->qt_charge?>">
                                                        </td>
                                                        <td class="out_margin"><?echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_charge*100,2);?>%</td>
                                                      
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
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr id="hr_<?echo $cur_time;?>">
                                        <script type="text/javascript">
                                        cal_outSource('<?=$cur_time?>');
                                        </script>
                                        
                                    <?
                                }
                                ?>
                                  <div class="span12 no-margin-left">
                                     <hr>
                                  </div>
                                <div class="span12 no-margin-left">
                                  <a href="<?=site_url("hod")?>" class="btn btn-info">BACK</a>
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
        $( "a" ).tooltip({});

        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy",
            beforeShow: function() {
                if ($(this).val() != "") {
                    var arrayDate = $(this).val().split("/");
                    arrayDate[2] = parseInt(arrayDate[2]) - 543;
                    $(this).val(arrayDate[0] + "/" + arrayDate[1] + "/" + arrayDate[2]);
                }
                setTimeout(function() {
                    $.each($(".ui-datepicker-year option"), function(j, k) {
                        var textYear = parseInt($(".ui-datepicker-year option").eq(j).val()) + 543;
                        $(".ui-datepicker-year option").eq(j).text(textYear);
                    });
                }, 50);
            }
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
    $(document).on("click", ".del_res", function() {
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
        if (confirm("Confirm del")) {
            var id=$(this).attr("iden");
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
                    console.log(file);
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
    document.getElementById("pro_add_form").submit();
}
function save_submit(){
    $("#pro_add_form").append('<input type="hidden" name="submit_job" value="yes">');
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
    </script>


