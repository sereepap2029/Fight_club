<? $ci=& get_instance(); ?>
<link rel="stylesheet" href="<?echo site_url();?>assets/work_sheet.css">
<style type="text/css">
.row-fluid .no-margin-left {
    margin-left: 0px;
}
hr{
    min-height: 2px;
    background-color: #CCCCCC;
    width: 100%;
}
.range_task{
    background-color: #cccccc;
}
.w-wrap{
    word-wrap: break-word;
}
.carlendar-res{
    overflow-y:auto;
}
.show_click{
    cursor: pointer;
}

.normal_allocate{
    background-color: #CCCC00;
}
.over_allocate{
    background-color: #E68A00;
}

.holiday_td{
    background-color: rgb(10,15,76);
    color: white;
}
.s_holiday_td {
    color: white;
    background-color: blue;
    text-align: center;
    font-weight: bolder;
}
.current_day{
    background-color: #51ABDB;
}
.job_d_click{
    cursor: pointer;
}
.outer_tr .head_td{
   white-space:nowrap;
   vertical-align: middle;
}
thead tr .head_th{
   white-space:nowrap;
   vertical-align: middle;
}
tr .nowrap{
   white-space:nowrap;
   vertical-align: middle;
}

.gray-bg{
    position: relative;
    background: rgba(46,43,46,1);
background: -moz-linear-gradient(left, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
background: -webkit-gradient(left top, right top, color-stop(0%, rgba(46,43,46,1)), color-stop(35%, rgba(89,89,89,1)), color-stop(100%, rgba(71,71,71,1)));
background: -webkit-linear-gradient(left, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
background: -o-linear-gradient(left, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
background: -ms-linear-gradient(left, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
background: linear-gradient(to right, rgba(46,43,46,1) 0%, rgba(89,89,89,1) 35%, rgba(71,71,71,1) 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#2e2b2e', endColorstr='#474747', GradientType=1 );
    text-align: center;
}
.text_in_bar{
    color: white;
    font-weight: bolder;
    text-align: center;
    position: absolute;
    height: 100%;
    width: 100%;
}
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">Resource Manager</div>
                    </div>
                    <div class="block-content collapse in ">
                        <div class="span12">
                            <!-- Tab -->
                            
                                <div class="well">
                                    <div id="myTabContent" class="tab-content">
                                        <div class="tab-pane active in" id="tab_one">
                                            <div class="span12 no-margin-left carlendar-res" >
                                                <div class="span12 no-margin-left">
                                                    <h4>Resource Name : <?=$child_user->nickname?></h4>
                                                </div>
                                                <div class="span12 no-margin-left">
                                                    <div class="control-group">
                                                        <label class="control-label-new" for="focusedInput">Project</label>
                                                        <select id="project_id" name="project_id">
                                                          <?
                                                          foreach ($project_wip as $key => $value) {
                                                            ?>
                                                            <option value="<?=$value->project_id?>"><?=$value->project_name?></option>
                                                            <?
                                                          }
                                                          ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <h5 id="project_name_reg"></h5>
                                                <?
                                                //if ($child_user->username!=$ci->user_data->username||isset($ci->user_data->prem['hod'])) {
                                                    ?>
                                                <a id="add_task_reg" class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?echo site_url("res/add_task_work_sheet/".$value->project_id."/".$child_user->username);?>"><i class="icon-plus icon-white"></i></a>
                                                <?
                                                //}
                                                ?>
                                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                                                                <thead>
                                                                     <tr>    
                                                                     <th class="head_th" rowspan="2">Task type</th>                                                 
                                                                     <th class="head_th" rowspan="2" >Task Name</th>
                                                                     <th class="head_th" rowspan="2" >Action</th>
                                                                    <?
                                                                    $current_time=$start_car_time;
                                                                        $cur_month=1000;
                                                                        while ($current_time<=$end_car_time) {
                                                                            if ($cur_month!=date("n",$current_time)) {
                                                                                $cur_month=date("n",$current_time);
                                                                                $cur_day=date("j",$current_time);
                                                                                $numday=date("t",$current_time);
                                                                                $colspan1=0;
                                                                                if ($cur_month==date("n",$end_car_time)) {
                                                                                    $numday=date("j",$end_car_time);
                                                                                    $colspan1=(int)$numday-(int)$cur_day+1;
                                                                                }else{
                                                                                    $colspan1=(int)$numday-(int)$cur_day+1;
                                                                                }
                                                                                
                                                                                ?>
                                                                                <th colspan="<?=$colspan1?>" style="text-align: center;"><?=date("M",$current_time)?></th>
                                                                                <?
                                                                            }                                                        
                                                                           $current_time+=(60*60*24);
                                                                        }
                                                                    ?>
                                                                    </tr>
                                                                    <tr>                                      
                                                                    <?
                                                                    
                                                                    $current_time=$start_car_time;
                                                                        while ($current_time<=$end_car_time) {
                                                                            $holiday="";
                                                                            if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                                                $holiday='class="holiday_td"';
                                                                            }
                                                                           ?>
                                                                           <th <?=$holiday?>><?=date("d",$current_time)?></th>
                                                                           <?
                                                                           $current_time+=(60*60*24);
                                                                        }
                                                                    ?>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="table-body" is-init="0" id="render_region">
                                                                    
                                                                </tbody>
                                                        </table>
                                                        <br>
                                                        <br>
                                                
                                                
                                            </div>
                                    </div>

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
<script>
    /*jslint unparam: true */
    /*global window, $ */
    $(function() {
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
        //make_render();
        //$(window).on('scroll', function() {
            //make_render();
        //});
        <?
        foreach ($project_wip as $key2 => $value2) {
            ?>
            render_table_project('<?=$value2->project_id?>','<?=$start_car_time?>','<?=$end_car_time?>');
            $("#project_name_reg").html($('#project_id').find(":selected").text()+"&nbsp;&nbsp;&nbsp;");
            $("#add_task_reg").attr("href","<?echo site_url("res/add_task_work_sheet/".$value2->project_id."/".$child_user->username);?>");
            <?
            break 1;
        }
        ?>
        
    });
$(document).on("change", "#project_id", function() {
    $("#project_name_reg").html($('#project_id').find(":selected").text()+"&nbsp;&nbsp;&nbsp;");
    $("#add_task_reg").attr("href","<?echo site_url("res/add_task_work_sheet");?>/"+$(this).val()+"/<?=$child_user->username?>");
        render_table_project($(this).val(),'<?=$start_car_time?>','<?=$end_car_time?>');
});    
function make_render(){
    var scrollTop = $(window).scrollTop();

            $('.table-body').each(function() {
                var hT = $(this).offset().top,
                   hH = $(this).outerHeight(),
                   wH = $(window).height();
                   if (scrollTop > (hT+hH-wH)){
                       if ($(this).attr("is-init")==0) {
                            $(this).attr("is-init","1");
                            render_table_project($(this).attr("id"),'<?=$start_car_time?>','<?=$end_car_time?>');
                            //console.log( $(this).attr("id") + ' was scrolled to the top' );
                        }
                   }
            });
}    
function render_table_project(project_id,start,end) {
  $("#"+project_id).html('<img src="<?=site_url()?>img/loading_gif/loading_'+get_int_for_gif()+'.gif" width="100%">')
  start = typeof start !== 'undefined' ? start : '<?=$start_car_time?>';
  end = typeof end !== 'undefined' ? end : '<?=$end_car_time?>';
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/print_project_child_control"); ?>",
                data: {
                    "child_username": '<?=$child_user->username?>',
                    "project_id": project_id,
                    "start": start,
                    "end": end,
                }
            })
            .done(function(data) {
                
                $("#render_region").html(data);
                $( ".input-assign" ).tooltip({});
            });    
    
}
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
                url: "<?php echo site_url("res/assign_action"); ?>",
                data: {
                    "save": current_val.attr("workid"),
                    "time": current_val.attr("time"),
                    "usn": current_val.attr("usn"),
                    "hour": current_val.val()
                }
            })
            .done(function(data) {
                 console.log(data);
                 if (data['flag']=="OK") {
                     /*if (data['status']=="over"&&current_val.val()!="0") {
                        current_val.parent().parent().addClass("range_task_over");
                     }else{
                        current_val.parent().parent().removeClass("range_task_over");
                     }
                     if (data['status']=="interfere"&&current_val.val()!="0") {
                        current_val.parent().parent().addClass("range_task_interfere");
                     }else{
                        current_val.parent().parent().removeClass("range_task_interfere");
                     }*/
                 }else{
                    alert(data['flag']);
                    current_val.val(old_val);
                 }
                    
            });
        }else{
            alert("Allow value is 0.5 and 0 to 8");
            current_val.val(old_val);
        }

    });
    $(document).on("click", ".del_work", function() {
        var current_val=$(this);
        if (confirm("Confirm del")) {
             $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/del_work"); ?>",
                data: {
                    "project_id": current_val.attr("project-id"),
                    "id": current_val.attr("iden")
                }
            })
            .done(function(data) {
                 $("#"+current_val.attr("iden")).parent().parent().fadeOut(300,function(){
                        $(this).remove();
                    });
                 render_table_project(current_val.attr("project-id"),'<?=$start_car_time?>','<?=$end_car_time?>');
                 
            });
           
        }

    });
    </script>