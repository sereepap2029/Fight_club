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
.current_day{
        border-left: 1px solid #000!important;
        border-right: 1px solid #000;
}
th.current_day{
    background-color: #000000;
    color: #FFF;
}
.job_d_click{
    cursor: pointer;
}
.outer_tr .head_td{
   white-space:nowrap;
   vertical-align: middle;
}
.spend-input{
    width: 30px;
    margin-bottom:0px;
}
.comment-input{
    width: 200px;
    margin-bottom:0px;
}
.table-atom{
    width: 100%;
}
.table-atom th{
    padding: 5px;
    border-bottom-width: 1px!important;
    border-bottom: solid;
    border-color: #000000;
    text-align: left;
}
.table-atom td{
    padding: 2px;
    text-align: left;
}
.table-atom tr:last-child td {
    border-bottom-width: 1px!important;
    border-bottom: solid;
    border-color: #000000;
    text-align: left;
}
.table tr td.hiligh-border-bot {
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #000;
}
</style>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">Resource Dashboard</div>
                    </div>
                    <div class="block-content collapse in ">
                        <div class="span12">
                            <!-- Tab -->
                             <input id="cur_time" type="hidden" value="">
                              <div class="table-toolbar">
                                        <div class="btn-group">
                                         <h3 id="t_day">TODAY</h3>
                                      </div>  
                                      <div class="btn-group">
                                         <a href="javascript:update_spend();"><button class="btn btn-info">Update</button></a>
                                      </div>                                      
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table-atom">
                                        <thead>
                                            <tr>
                                            <th style="min-width:30px;">#</th>
                                              <th style="min-width:300px;">Project</th>
                                              <th style="min-width:200px;">Task</th>
                                              <th style="width:30px;">Allocation</th>
                                              <th style="width:30px;">Spend</th>
                                              <th >comment</th>
                                          </tr>
                                        </thead>
                                        <tbody id="table_detail">
                                        <tr>
                                            <td>
                                                
                                            </td>
                                        </tr>                                                                     
                                        </tbody>
                                    </table>
                            <div class="span12 no-margin-left" >
                            <br>
                            <br>
                            </div>
                            <div class="span12 no-margin-left carlendar-res" >
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                                            <thead>
                                                 <tr>
                                                 <th colspan="2" style="text-align: center;">Month -></th>
                                                <?
                                                $current_day_time=$ci->m_time->datepicker_to_unix(date("d/m/Y"));
                                                $start_car_time=$current_day_time-(60*60*24*7);
                                                $end_car_time=$current_day_time+(60*60*24*60);
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
                                                            <th colspan="<?=$colspan1?>" style="text-align: center;"><?=date("F",$current_time)?></th>
                                                            <?
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?>
                                                </tr>
                                                <tr>
                                                <th style="min-width:250px;">Project</th>
                                                <th style="min-width:250px;">Task</th>
                                                <?
                                                
                                                $current_time=$start_car_time;
                                                    while ($current_time<=$end_car_time) {
                                                        $holiday="";
                                                        $current_cls="";
                                                        if ($current_time==$current_day_time) {
                                                                        $current_cls="current_day";
                                                                    }
                                                        if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                            $holiday='holiday_td';
                                                        }
                                                        if (isset($holiday_obj[$current_time])&&$holiday_obj[$current_time]->is_holiday=="y") {
                                                                $holiday='s_holiday_td';
                                                        }
                                                       ?>
                                                       <th class="<?=$holiday?> <?=$current_cls?>"><?=date("d",$current_time)?></th>
                                                       <?
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               
                                                <?
                                                $project_name_flag=true;
                                                foreach ($project_wip as $key => $value) {
                                                    $num_row_span=count($value->work_sheet);
                                                    $num_border=1;
                                                    foreach ($value->work_sheet as $wkey => $wvalue) {
                                                        $row_hiligh="";
                                                    if ($num_border==$num_row_span) {
                                                            $row_hiligh="hiligh-border-bot";
                                                        }                                                      
                                                        ?>
                                                        <tr class="outer_tr">
                                                        <?
                                                        if ($project_name_flag) {
                                                            ?>
                                                            <td class="head_td hiligh-border-bot" rowspan="<?=$num_row_span?>">
                                                                <?=$value->project_name?>
                                                            </td>
                                                            <?
                                                            $project_name_flag=false;
                                                        }
                                                        ?>
                                                            
                                                            <td class="head_td <?=$row_hiligh?>">
                                                                <?=$wvalue->work_name?>
                                                                <?
                                                                if ($wvalue->comment!="") {
                                                                    ?>
                                                                    <a class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("res/view_task_note/".$wvalue->id);?>"><i class="icon-file icon-white"></i></a>
                                                                    <?
                                                                }
                                                                ?>
                                                            </td>
                                                        <?
                                                            $current_time=$start_car_time;
                                                                while ($current_time<=$end_car_time) {
                                                                    $holiday="";
                                                                    $in_range="";
                                                                    $day_click="";
                                                                    $over_cls="";
                                                                    $current_cls="";
                                                                    if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                                        $holiday='holiday_td';
                                                                    }
                                                                    $sum_al_hour=0;            
                                                                    $sum_spent_hour=0;
                                                                    $sum_spent_hour_str="";
                                                                    if (isset($wvalue->assign_obj->list[$current_time])) { 
                                                                            $sum_al_hour+=$wvalue->assign_obj->list[$current_time]->hour;
                                                                            $sum_spent_hour+=$wvalue->assign_obj->list[$current_time]->spend;
                                                                    }
                                                                    $sum_spent_hour_str=$sum_spent_hour."/";
                                                                    if ($sum_al_hour>=8) {
                                                                        $over_cls="over_allocate";
                                                                    }else if($sum_al_hour==0){
                                                                        $over_cls="";
                                                                        $sum_al_hour="";
                                                                        $sum_spent_hour_str="";
                                                                    }else{
                                                                        $over_cls="normal_allocate";
                                                                    }
                                                                    if ($current_time==$current_day_time) {
                                                                        $current_cls="current_day";
                                                                    }
                                                                    if ($wvalue->start<=$current_time&&$wvalue->end>=$current_time&&$holiday=="") {
                                                                        $in_range='range_task';
                                                                    }
                                                                    if (isset($holiday_obj[$current_time])&&$holiday_obj[$current_time]->is_holiday=="y") {
                                                                        $holiday='s_holiday_td';
                                                                        $sum_al_hour="H";
                                                                    }
                                                                    if (isset($user_leave_obj[$current_time][$user_data->username])) {
                                                                        $holiday='leave_td';
                                                                        $sum_al_hour="L";
                                                                    }
                                                                   ?>
                                                                   <td class="<?=$current_cls?> <?=$holiday?> show_click <?=$over_cls?> <?=$in_range?> <?=$row_hiligh?>" time="<?=$current_time?>">
                                                                   <?=$sum_spent_hour_str.$sum_al_hour?>
                                                                   </td>
                                                                   <?
                                                                   $current_time+=(60*60*24);
                                                                }// end while
                                                            ?>

                                                            </tr>
                                                            <?
                                                            $num_border+=1;
                                                           
                                                    }// end foreach work sheet
                                                    $project_name_flag=true;
                                                }// end project WIP
                                                ?>

                                                <tr>
                                                <th colspan="2" style="text-align: center;"> Day -></th>
                                                <?
                                                
                                                $current_time=$start_car_time;
                                                    while ($current_time<=$end_car_time) {
                                                        $holiday="";
                                                        $current_cls="";
                                                        if ($current_time==$current_day_time) {
                                                                        $current_cls="current_day";
                                                                    }
                                                        if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                            $holiday='holiday_td';
                                                        }
                                                        if (isset($holiday_obj[$current_time])&&$holiday_obj[$current_time]->is_holiday=="y") {
                                                                $holiday='s_holiday_td';
                                                        }
                                                       ?>
                                                       <th class="<?=$holiday?> <?=$current_cls?>"><?=date("d",$current_time)?></th>
                                                       <?
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?>
                                                </tr>
                                                <tr>
                                                 <th colspan="2" style="text-align: center;">Month -></th>
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
                                                            <th colspan="<?=$colspan1?>" style="text-align: center;"><?=date("F",$current_time)?></th>
                                                            <?
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?>
                                                </tr>
                                            </tbody>
                                    </table>
                            </div>
                            
                           

                                    <div class="table-toolbar">
                                        <div class="btn-group">
                                         <h3>Work in progress</h3>
                                      </div>                                      
                                    </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table-atom">
                                        <thead>
                                            <tr>
                                              <th style="width:30px;">#</th>
                                              <th>Project</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            <?
                                            $count=1;
                                            foreach ($project_wip as $key => $value) {
                                                ?>
                                                <tr>
                                                    <td><?=$count?></td>
                                                    <td class="job_d_click" iden="<?=$value->project_id?>">
                                                        <?=$value->project_name?>
                                                    </td>
                                                </tr>
                                                <?
                                                $count+=1;
                                            }
                                            ?>                                                                
                                        </tbody>
                                    </table>                                    
                                    <a class="fancybox" style="display:none;" data-fancybox-type="iframe" href=""></a>
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
});
    $(document).on("click", ".show_click", function() {
        var current=$(this);
        $("#cur_time").val(current.attr("time"));
        update_table(current.attr("time"));

    });
    function update_table(time){
        $("#table_detail").html("<tr id='loading-gif'><td colspan='3'><img src='<?=site_url("img/meme-loading.gif")?>' width='400'></td></tr>");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/table_detail"); ?>",
                data: {
                    "usn": '<?=$ci->user_data->username?>',
                    "time": time,
                }
            })
            .done(function(data) {
                
                $("#table_detail").html(data);
                $("#t_day").html($("#current_datepick").val());
                $("#cur_time").val(""+time);
            });
    }
     function add_interfere(){
        $("#table_detail").append("<tr id='loading-gif'><td colspan='3'><img src='<?=site_url()?>img/loading_gif/loading_"+get_int_for_gif()+".gif' width='400'></td></tr>");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/insert_hour"); ?>",
                data: {
                    "usn": '<?=$ci->user_data->username?>',
                    "date":$("#t_day").html(),
                }
            })
            .done(function(data) {                
                $("#table_detail").append(data);
                $("#loading-gif").fadeOut(300,function(){
                    $(this).remove();
                });
            });
    }
function update_spend() {
  var spend=$("input[name*='spend']").serialize();
  var comment=$("input[name*='comment']").serialize();
  var work_sheet_id=$("select[name='work_sheet_id[]']").serialize();
  var inter_hour=$("input[name='inter_hour[]']").serialize();
  var com_inter=$("input[name='com_inter[]']").serialize();
  //console.log(spend);
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/update_spend"); ?>",
                data: "cur_time="+$("#cur_time").val()+"&usn=<?=$ci->user_data->username?>&"+spend+"&"+comment+"&"+work_sheet_id+"&"+inter_hour+"&"+com_inter
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    alert("Update success");
                    update_table($("#cur_time").val());
                }else{
                    alert(data['flag']);
                    update_table($("#cur_time").val());
                }
                window.open("<?php echo site_url("res"); ?>","_self")
            });
    
}
$(document).on("click", ".job_d_click", function() {
        var current_ele=$(this);
        var project_id=current_ele.attr("iden");
        $(".fancybox").attr("href","<?=site_url('res/project_task')?>/"+project_id);
        $(".fancybox").trigger('click'); 

    });
$(document).on("change", ".change_pro_id", function() {
        var current_ele=$(this);
        var parent_ele=current_ele.parent().parent().parent();
        var tar_change_work=parent_ele.find(".change_work_id");
        $.ajax({
                method: "POST",
                url: "<?=site_url('res/get_interfere_work_id')?>",
                data: {
                    "project_id": current_ele.val(),
                    "time": $("#cur_time").val(),
                }
            })
            .done(function(data) {
                tar_change_work.html(data);
            });
        
    });
update_table('<?=$current_day_time?>');
</script>