<? $ci=& get_instance(); ?>
<style type="text/css">
.row-fluid .no-margin-left {
    margin-left: 0px;
}
</style>
<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<link rel="stylesheet" href="<?echo site_url();?>css/style.css">
<link rel="stylesheet" href="<?echo site_url();?>assets/work_sheet.css">
<div class="container-fluid">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <!-- block -->
                <div class="block" style="min-height:600px;">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">
                            <div class="control-group">
                                <h3><?=$project->project_name?> </h3>
                            </div>
                        </div>
                        <div class="pull-right">
                        <div class="control-group">
                            <font class="head-pull-right"><?=$project->status?> </font>
                            <?
                            if ($project->status!="Done"&&$project->status!="Cancel") {
                                
                                $check_hod_assign=$ci->m_project->check_hod_assign_resource($project->project_id);
                                $check_all_oc_done=$ci->m_oc->check_all_done_oc_by_project_id($project->project_id);
                                if ($check_hod_assign&&$check_all_oc_done) {
                                    ?>
                                    <a href="javascript:done_job();" class="btn">Complete</a>

                                    <?
                                }
                            
                            ?>
                            <a href="javascript:cancel_job();" class="btn">Cancel</a>
                            <?
                        }
                        ?>
                        </div>
                        </div>
                    </div>
                    <div class="block-content collapse in" style="min-height:600px;">
                        <form id="pro_add_form" class="form-horizontal" method="post">
                            <fieldset>
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

                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered orange lowfont">
                                    <thead>
                                        <tr>
                                            <th style="min-width:250px">Type</th>
                                            <th style="min-width:450px">Task</th>
                                            
                                            <th >Approved Budget (HR)</th>
                                            <th >Allocate Budget (HR)</th>
                                            <th >Remain Budget (HR)</th>
                                            <th >Assign Resource</th>
                                        </tr>
                                    </thead>
                                    <tbody id="res_t_body">                                        
                                    </tbody>
                                </table>


                                <div class="span12 no-margin-left">
                                    <h3>Work sheet</h3>&nbsp;<a id="reverse_allocation" href="javascript:reverse_allocation();" class="btn btn-warning add_task">Reverse Allocation</a>
                                </div>
                                <div class="span12 no-margin-left">
                                    <div id="t_list" class="span6 no-margin-left">
                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                <th colspan="4" style="text-align: center;">Month -></th>
                                                </tr>
                                                <tr>
                                                <th>#</th>
                                                    <th style="min-width:250px;">Task</th>
                                                    <th>Note</th>
                                                    <th>Peroid</th>
                                                    <th>Resource</th>
                                                    <th>TAH</th>
                                                </tr>
                                                
                                            </thead>
                                            <tbody id="task_table_body">

                                            </tbody>

                                        </table>                                        
                                    </div>

                                    <div id="t_carlendar" class="span6 no-margin-left work-sheet-carlendar">
                                        
                                    </div>


                                </div>
                                <div class="span12 no-margin-left">
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Task Type</label>
                                            <select id="task_type" >
                                                <option value="no">---please select ------</option>
                                                <?
                                                foreach ($task_type_list as $key => $value) {
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
                                            <label class="control-label-new" for="focusedInput">Task</label>
                                            <input class="form-control" type="text" id="task">
                                        </div>
                                    </div>
                                   <!-- <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Task Start</label>
                                            <input class="form-control datepicker" type="text" id="task_start">
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Task End</label>
                                            <input class="form-control datepicker" type="text" id="task_end">
                                        </div>
                                    </div>-->
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">&nbsp;</label>
                                            <a id="add_task" href="javascript:add_task();" iden="ready" class="btn btn-success add_task">Add Task <i class="icon-plus icon-white"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="span6 no-margin-left">
                                    <a href="<? echo site_url('project/edit_oc/'.$project->project_id)?>" class="btn btn-info">OK</a>
                                </div>
                                <div class="span6 no-margin-left">
                                    <a href="javascript:;" class="btn btn-info">GEN GANT</a>
                                </div>
                            </fieldset>
                            <a class="fancybox" style="display:none;" id="c_add_res_fancy" data-fancybox-type="iframe" href="<?=site_url("hod/assign_action/1123")?>"></a>
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
        $( "#task_table_body" ).sortable({
          update: function( event, ui ) {
            var work_id_list=$("input[name='work_id_list[]']").serialize()
            $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/force_sort_work_sheet"); ?>",
                data: "save=yes&"+work_id_list
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                draw_left_right();
              }else{
                alert("error happen");
                console.log(data)
              }
            });
          },
          placeholder: "ui-state-highlight"
        });
        $( "#task_table_body" ).disableSelection();
        $(".fancybox").fancybox({
            fitToView   : false,
            width       : '98%',
            height      : '98%',
            autoSize    : false,
            closeClick  : false,
            openEffect  : 'none',
            closeEffect : 'none'
        });
        //$(".fancybox").trigger('click');         
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy"
        });
        
        $('.datetimepicker').datetimepicker();
        $(".chzn-select").chosen({
            width: "75%"
        });
        print_task_left("<?=$project->project_id?>");
        print_task_right("<?=$project->project_id?>");
        print_table("<?=$project->project_id?>");
        
    });
    function draw_left_right(){
        print_task_left("<?=$project->project_id?>");
        print_task_right("<?=$project->project_id?>");
    }
    function add_task(){
        var task_name=$("#task").val();
        var task_type=$("#task_type").val();
        if (task_name==""||task_type=="no") {
            alert("กรอกข้อมูลให้ครบทุกช่อง");
        }else if($("#add_task").attr("iden")!="ready"){
            alert("Adding please wait");
        }else{
            $("#add_task").attr("iden","adding");
         $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/add_work_task"); ?>",
                data: {
                    "task_name": task_name,
                    "task_type": task_type,
                    "project_id": "<?=$project->project_id?>",
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    print_task_left("<?=$project->project_id?>");
                    print_task_right("<?=$project->project_id?>");
                    $("#add_task").attr("iden","ready");
                    $("#task").val("");
                    $("#task_type").val("no");
                    $("#task_start").val("");
                    $("#task_end").val("");
                }else{
                    alert(data['flag'])
                }
                
                
            });
        }
    }
    function print_task_left(project_id){
        $("#task_table_body").html('<img src="<?=site_url()?>img/loading_gif/loading_'+get_int_for_gif()+'.gif" width="100%">')
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/print_work_task_left"); ?>",
                data: {
                    "project_id": project_id
                }
            })
            .done(function(data) {
                $("#task_table_body").html(data);
            });
    }
    function print_task_right(project_id){
        $("#t_carlendar").html('<img src="<?=site_url()?>img/loading_gif/loading_'+get_int_for_gif()+'.gif" width="100%">')
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/print_work_task_right"); ?>",
                data: {
                    "project_id": project_id
                }
            })
            .done(function(data) {
                $("#t_carlendar").html(data);
                setTimeout(set_valid_element_carlendar,200);
            });
    }
    function print_table(project_id){
        $("#res_t_body").html('<img src="<?=site_url()?>img/loading_gif/loading_'+get_int_for_gif()+'.gif" width="100%">')
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/print_table_sum_work"); ?>",
                data: {
                    "project_id": project_id
                }
            })
            .done(function(data) {
                $("#res_t_body").html(data);
            });
    }
    function reverse_allocation(){
        if(confirm("Are you sure to reverse Allocation")){
            $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/reverse_allocate"); ?>",
                data: {
                    "project_id": "<?=$project->project_id?>"
                }
            })
            .done(function(data) {
                draw_left_right();
                print_table("<?=$project->project_id?>");
            });
        }
    }
    function set_valid_element_carlendar(){
        $("#t_carlendar").height( $("#t_list").height()+20);
        //$(".allow_tr_con").height($("#"+$(this).attr("iden")).height());
        for (var i = 0; i <$(".allow_tr_con").length; i++) {
            var new_height=$("#"+$(".allow_tr_con").eq(i).attr("iden")).height();
            $(".allow_tr_con").eq(i).height(new_height);
        };
        for (var i = 0; i <$(".outer_tr").length; i++) {
            var new_height=$("#"+$(".outer_tr").eq(i).attr("iden")).height();
            $(".outer_tr").eq(i).height(new_height);
        };
        $( ".input-assign" ).tooltip({});
        $( ".div-info" ).tooltip({});
    }
    $(document).on("click", ".del_work", function() {
        var current_val=$(this);
        if (confirm("Confirm del")) {
             $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/del_work"); ?>",
                data: {
                    "project_id": "<?=$project->project_id?>",
                    "id": current_val.attr("iden")
                }
            })
            .done(function(data) {
                 $("#"+current_val.attr("iden")).fadeOut(300,function(){
                        $(this).remove();
                    });
                 $(".outer_tr[iden='"+current_val.attr("iden")+"']").fadeOut(300,function(){
                        $(this).remove();
                    });
                 draw_left_right();
                 
            });
           
        }

    });
    $(document).on("click", ".click_add_resource", function() {
        var current_ele=$(this);
        var work_id=current_ele.attr("workid");
        $("#c_add_res_fancy").attr("href","<?=site_url('cs/work_add_resource')?>/"+work_id);
        $("#c_add_res_fancy").trigger('click'); 
        

    });
    var old_val=0;
    $(document).on("focusin", ".input-assign", function() {
        var current_val=$(this);
        old_val=current_val.val();
    });
    $(document).on("focusout", ".input-assign", function() {
        var allow = {"0":"0","1":"1", "0.5":"0.5", "2":"2", "3":"3", "4":"4", "5":"5", "6":"6", "7":"7", "8":"8"};
        var current_val=$(this);
        if (typeof allow[''+current_val.val()] != 'undefined') {  
            $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/assign_action"); ?>",
                data: {
                    "save": current_val.attr("workid"),
                    "time": current_val.attr("time"),
                    "usn": current_val.attr("usn"),
                    "hour": current_val.val()
                }
            })
            .done(function(data) {
                 console.log(data);
                 print_task_left("<?=$project->project_id?>");
                 if (data['flag']=="OK") {
                     if (data['status']=="over"&&current_val.val()!="0") {
                        current_val.parent().parent().addClass("range_task_over");
                     }else{
                        current_val.parent().parent().removeClass("range_task_over");
                     }
                     if (data['status']=="interfere"&&current_val.val()!="0") {
                        current_val.parent().parent().addClass("range_task_interfere");
                     }else{
                        current_val.parent().parent().removeClass("range_task_interfere");
                     }
                 }else{
                    alert(data['flag']);
                    current_val.val(data['val']);
                 }
                 setTimeout(set_valid_element_carlendar,1000);
                 print_table("<?=$project->project_id?>");
                    
            });
        }else{
            alert("Allow value is 0.5 and 0 to 8");
            current_val.val(old_val);
        }

    });
    function done_job(){
    if (confirm("Complete This Job")) {
            window.open("<?echo site_url("cs/project_done/".$project->project_id);?>","_self");
        }
}
function cancel_job(){
    if (confirm("Cancel This Job")) {
            window.open("<?echo site_url("cs/project_cancel/".$project->project_id);?>","_self");
        }
}
    </script>


