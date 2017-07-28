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
                        <div class="muted pull-left">Allow Resource Task:<?=$task->task?>  || Available Budget:<?=$task->approve_budget?> </div>
                    </div>
                    <div class="block-content collapse in" style="min-height:500px">
                        <div class="span12">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                <thead>
                                    <tr>
                                        <th>Resource</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bill_t_body">
                                    <?
                                    foreach ($allow_list as $key => $value) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="control-group">
                                                <select id="resource_usn_old" class="chzn-select" name="resource_usn_old[]">
                                                    <option value="no">---please select ------</option>
                                                    <?
                                                    $flag_have_name=false;
                                                    foreach ($user as $key2 => $value2) {
                                                                            ?>
                                                        <option value="<?=$value2->username?>" <?if($value->resource_usn==$value2->username){echo "selected";$flag_have_name=true;}?>><?=$value2->nickname?></option>
                                                        <?
                                                    }
                                                    if (!$flag_have_name) {
                                                        $tmp_usn=$ci->m_user->get_user_by_login_name ($value->resource_usn);
                                                        ?>
                                                        <option value="<?=$value->resource_usn?>" <? echo "selected";?>><?=$tmp_usn->nickname?></option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                            <input class="" type="hidden" name="id_old[]" value="<?=$value->id?>">
                                        <td>
                                        <?
                                        if ($flag_have_name) {
                                            ?>
                                            <a href="javascript:;" iden="<?=$value->id?>" class="btn btn-danger del_allow"><i class="icon-remove icon-white"></i></a>
                                            <?
                                        }
                                        ?>                                          
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
  // var allow_hour=$("input[name='allow_hour[]']").serialize();
  // var allow_hour_old=$("input[name='allow_hour_old[]']").serialize();
  var resource_usn_old=$("select[name='resource_usn_old[]']").serialize();
  var id_old=$("input[name='id_old[]']").serialize();
  var del_list=$("input[name='del_list[]']").serialize();
  $.ajax({
                method: "POST",
                url: "<?php echo site_url($ci->uri->segment(1,'')."/assign_action"); ?>",
                data: "save=<?=$task->r_id?>&"+resource_usn+"&"+resource_usn_old+"&"+id_old+"&"+del_list
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                parent.show_allow_resource('<?=$task->r_id?>');
                parent.$.fancybox.close();
              }else{
                alert(data['flag']);
                console.log(data)
                $("#close_but").html("OK");
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
                url: "<?php echo site_url($ci->uri->segment(1,'')."/assign_action"); ?>",
                data: {
                    "add_bill": "add_bill",
                    "t_type": "<?=$t_type?>"
                }
            })
            .done(function(data) {
                $("#bill_t_body").append(data);
                call_datepicker();
            });
        
    });
</script>
