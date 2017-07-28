<? $ci=& get_instance(); 
$prem_flag=(isset($user_data->prem['csd'])
 ||isset($user_data->prem['hod'])
 ||isset($user_data->prem['fc']));
     $status_arr = array('y' => "Approve",'n' => "reject" ,'ns' => "not sign");
     $ty_arr = array('csd' => "CSD",'fc' => "FC" ,'hod' => "HOD");
?>

<head>
</head>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left"> Task : <?=$work->work_name?> | Type : <?=$hour_rate_list[$work->task_type]->name?></div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12 no-margin-left">
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
                        </div>
                                <div class="span12 no-margin-left">
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Task Start</label>
                                            <input class="form-control datepicker" type="text" id="task_start"
                                            <?
                                            if ($work->start<100000) {
                                               ?>
                                               value="<?=$ci->m_time->unix_to_datepicker(time())?>"
                                               <?
                                            }else{
                                                ?>
                                                value="<?=$ci->m_time->unix_to_datepicker($work->start)?>"
                                                <?
                                            }
                                            ?>
                                            >
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Task End</label>
                                            <input class="form-control datepicker" type="text" id="task_end"
                                            <?
                                            if ($work->end<100000) {
                                               ?>
                                               value="<?=$ci->m_time->unix_to_datepicker(time())?>"
                                               <?
                                            }else{
                                                ?>
                                                value="<?=$ci->m_time->unix_to_datepicker($work->end)?>"
                                                <?
                                            }
                                            ?>
                                            >
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Hour</label>
                                            <input class="form-control" type="text" id="hour" value="<?=$work->hour_limit?>">
                                        </div>
                                    </div>
                                </div>                            
                                <a id="close_but" href="javascript:close_fancy(0);" class="btn btn-info">Manual</a>
                                <?/*<a id="close_but" href="javascript:close_fancy(1);" class="btn btn-info">Auto</a>*/?>
                            
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
    $("a").tooltip({});
    $(".datepicker").datepicker();
    print_table();
});
function close_fancy(auto) {
  var start_date=$("#task_start").val();
var end_date=$("#task_end").val();
var hour=$("#hour").val();
  $(".close_but").html("saving.....!!");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/ajax_edit_peroid_work_sheet"); ?>",
                data: {
                    "id": '<?=$work_id?>',
                    "start_date": start_date,
                    "end_date": end_date,
                    "hour": hour,
                    "auto": auto,
                    "project_id": '<?=$work->project_id?>',
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    parent.render_table_project("<?=$work->project_id?>");   
                    parent.$.fancybox.close();

                }else{
                    alert(data['flag']);
                }
            });    
    
}
function print_table(){
        $("#res_t_body").html('<img src="<?=site_url()?>img/loading_gif/loading_'+get_int_for_gif()+'.gif" width="100%">')
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/print_table_sum_work"); ?>",
                data: {
                    "project_id": '<?=$work->project_id?>'
                }
            })
            .done(function(data) {
                $("#res_t_body").html(data);
            });
    }
</script>
