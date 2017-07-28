<? $ci=& get_instance(); ?>

<head>
</head>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">Task Type:<?=$hour_rate->name?> || Advailable Hour <font id="ad_hour"></font></div>
                    </div>
                    <div class="block-content collapse in" style="min-height:500px">
                        <div class="span12">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                <thead>
                                    <tr>
                                        <th>Resource</th>
                                        <th>Hour</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bill_t_body">
                                    <?
                                    $sum_hour=0;
                                    foreach ($res_list as $key => $value) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="control-group">
                                                <select class="chzn-select change_usn" name="resource_usn_old[]">
                                                    <option value="no">---please select ------</option>
                                                    <?
                                                    foreach ($user as $key2 => $value2) {
                                                                            ?>
                                                        <option value="<?=$value2->user_dat->username?>" <?if($value->user_dat->username==$value2->user_dat->username){echo "selected";}?>><?=$value2->user_dat->nickname?></option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="control-group">
                                                <select class="chzn-select" name="allow_hour_old[]">
                                                    <?
                                                    for ($i=1; $i < 9; $i++) { 
                                                                            ?>
                                                        <option value="<?=$i?>" <?if($i==$value->hour){echo "selected";}?>><?=$i?></option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <input class="" type="hidden" name="id_old[]" value="<?=$value->id?>">
                                            <input class="" type="hidden" name="hour_old[]" value="<?=$value->hour?>">
                                        </td>
                                        <td>
                                          <a href="javascript:;" iden="<?=$value->id?>" class="btn btn-danger del_allow"><i class="icon-remove icon-white"></i></a>
                                        </td>
                                    </tr>
                                    <?
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <a href="javascript:;" class="btn btn-success add_allow"><i class="icon-plus icon-white"></i></a>
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
function close_fancy() {
  $("#close_but").html("saving.....!!");
  var resource_usn=$("select[name='resource_usn[]']").serialize();
  var allow_hour=$("select[name='allow_hour[]']").serialize();
  var allow_hour_old=$("select[name='allow_hour_old[]']").serialize();
  var resource_usn_old=$("select[name='resource_usn_old[]']").serialize();
  var id_old=$("input[name='id_old[]']").serialize();
  var hour_old=$("input[name='hour_old[]']").serialize();
  var del_list=$("input[name='del_list[]']").serialize();
  $.ajax({
                method: "POST",
                url: "<?=site_url('cs/assign_action/'.$project_id.'/'.$work_id.'/'.$time)?>",
                data: "save=<?=$work_id?>&time=<?=$time?>&t_type=<?=$hour_rate->id?>&project_id=<?=$project_id?>&"+hour_old+"&"+resource_usn+"&"+allow_hour+"&"+allow_hour_old+"&"+resource_usn_old+"&"+id_old+"&"+del_list
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                parent.draw_left_right();
                parent.$.fancybox.close();
              }else{
                alert(data['flag']);
                console.log(data)
                $("#close_but").html("OK");
                $("#allocate_hour").html(""+data['hour']);
              }
            });
    
}
function call_datepicker(){
  $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd/mm/yy"
    });
  $(".chzn-select").chosen({
            width: "75%"
        });
  
}
$(function() {
    $("a").tooltip({});
    call_datepicker();
    $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/get_advailable_hour"); ?>",
                data: {
                    "project_id": "<?=$project_id?>",
                    "work_id": "<?=$work_id?>",
                    "t_type": "<?=$hour_rate->id?>"
                }
            })
            .done(function(data) {
                $("#ad_hour").html(data['hour']);
            });
});
$(document).on("click", ".del_allow", function() {        
    $( "#bill_t_body" ).append('<input type="hidden" name="del_list[]" value="'+$( this ).attr("iden")+'">');
        $(this).parent().parent().fadeOut(300,function(){
            $(this).remove();
        });
        
    });
$(document).on("click", ".add_allow", function() {
        $.ajax({
                method: "POST",
                url: "<?=site_url('cs/assign_action/'.$project_id.'/'.$work_id.'/'.$time)?>",
                data: {
                    "add_bill": "add_bill",
                }
            })
            .done(function(data) {
                $("#bill_t_body").append(data);
                call_datepicker();
            });
        
    });
$(document).on("change", ".change_usn", function() {
    var current=$(this);
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/get_advailable_hour"); ?>",
                data: {
                    "project_id": "<?=$project_id?>",
                    "work_id": "<?=$work_id?>",
                    "usn": ""+$(this).val(),
                    "t_type": "<?=$hour_rate->id?>"
                }
            })
            .done(function(data) {
                $("#ad_hour").html(data['hour']);
                call_datepicker();
            });
        
    });
</script>
