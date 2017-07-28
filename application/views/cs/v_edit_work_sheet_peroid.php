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
                        <div class="muted pull-left"> Task <?=$work->work_name?> Peroid</div>
                    </div>
                    <div class="block-content collapse in">
                        
                                <div class="span12 no-margin-left">
                                    <div class="span4">
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
                                    <div class="span4">
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
                                </div>                            
                                <a id="close_but" href="javascript:close_fancy();" class="btn btn-info">OK</a>
                            
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
});
function close_fancy() {
  var start_date=$("#task_start").val();
var end_date=$("#task_end").val();
  $(".close_but").html("saving.....!!");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/ajax_edit_peroid_work_sheet"); ?>",
                data: {
                    "id": '<?=$work_id?>',
                    "start_date": start_date,
                    "end_date": end_date,
                    "project_id": '<?=$work->project_id?>',
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    parent.draw_left_right();
                    parent.$.fancybox.close();
                    
                }else{
                    alert(data['flag']);
                }
            });    
    
}
</script>
