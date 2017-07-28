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
            .no-border td{
            border: 0px!important;
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
                                    <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/project_note/".$project->project_id)?>">
                                      <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/project_note.png')?>">
                                    </a>
                                </div>
                                
                                <div class="span12 no-margin-left">
                                    <h3>Resource Sheet</h3>
                                </div>
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered orange lowfont">
                                    <thead>
                                        <tr>
                                            <th >No.</th>
                                            <th >Task</th>
                                            <th >Type</th>
                                            <th class="ch-hr">Approved Budget (HR)</th>
                                            <th ></th>
                                            <th ></th>
                                            <th >Assign</th>
                                            
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
                                                    
                                                    <?=$task->type_obj->name?>
                                                    <?$approve_budget=$r_sheet[$t_key]->approve_budget;?> 
                                                    <?$total_hr+=$approve_budget;?>
                                                    <?$total_chg+=$approve_budget*$r_sheet[$t_key]->type_obj->hour_rate;?>
                                                    
                                            </td>
                                            <td><p> <?=$r_sheet[$t_key]->approve_budget?></p></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                            <table id="<?=$task->r_id?>" class="no-border">
                                                <tr class="resource_show">
                                                    <td>
                                                        AAAAAAAAAAAAA
                                                    </td>
                                                    <td>
                                                        BBBBBBBBBBB
                                                    </td>
                                                </tr>
                                                <tr id="before_ares">
                                                    
                                                    <?
                                                    if (isset($ci->user_data->prem['hod'])) {                                                    
                                                        ?>
                                                        <td><a class="btn fancybox" data-fancybox-type="iframe" href="<?=site_url($ci->uri->segment(1,'')."/assign_action/".$task->r_id."/".$task->type)?>">Assign</a></td>
                                                        <td><a class="btn fancybox" data-fancybox-type="iframe" href="<?=site_url("hod/delegate_action/".$task->r_id."/".$task->type."/".$project->project_id)?>">Delegate</a></td>
                                                        <?
                                                    }else if(isset($delegate_r[$task->r_id])){
                                                        ?>
                                                        <td><a class="btn fancybox" data-fancybox-type="iframe" href="<?=site_url($ci->uri->segment(1,'')."/assign_action/".$task->r_id."/".$task->type)?>">Assign</a></td>
                                                        <?
                                                    }
                                                    ?>
                                                </tr>
                                            </table>

                                            </td>
                                        </tr>
                                        <script type="text/javascript">
                                        $(function() {
                                            show_allow_resource('<?=$task->r_id?>');
                                        });
                                        </script>
                                        <?
                                        }
                                        ?>
                                        <tr id="last_tr">
                                            <td></td>
                                            <td></td>
                                            <td>Grand Total</td>
                                            <td class="total"><?echo $total_hr;?></td>
                                            <td></td>
                                            <td class="total"></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>

                                </fieldset>
                                  <div class="span12 no-margin-left">
                                     <hr>
                                  </div>
                                   <div class="span12 no-margin-left">
                                  <a href="<?=site_url("gate")?>" class="btn btn-info">BACK</a>
                                  <a href="<?=site_url("delegate/complete_assign/".$project->project_id)?>" class="btn btn-success">Resource Assign Complete</a>
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
function show_allow_resource(r_id){
    $("#"+r_id+" .resource_show").remove();
    $.ajax({
                method: "POST",
                url: "<?php echo site_url($ci->uri->segment(1,'')."/show_allow_resource"); ?>",
                data: {
                    "r_id": r_id
                }
            })
            .done(function(data) {
                $("#"+r_id+" #before_ares").before(data);
            });

}
    </script>



