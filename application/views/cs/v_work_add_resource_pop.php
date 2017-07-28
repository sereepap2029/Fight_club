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
                        <div class="muted pull-left">Task Type : <?=$hour_rate->name?></div>
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
                                    $sum_hour=0;
                                    foreach ($res_list as $key => $value) {
                                    ?>
                                    <input class="" type="hidden" name="id_old[]" value="<?=$value->id?>">
                                    <tr>
                                        <td>
                                            <div class="control-group">
                                                <select class="chzn-select change_usn" name="resource_usn_old[]">
                                                    <option value="no">---please select ------</option>
                                                    <?
                                                    $have_u_dat=false;
                                                    foreach ($user as $key2 => $value2) {
                                                                            ?>
                                                        <option value="<?=$value2->user_dat->username?>" <?if($value->username==$value2->user_dat->username){echo "selected"; $have_u_dat=true;}?>><?=$value2->user_dat->nickname?></option>
                                                        <?
                                                    }
                                                    if (!$have_u_dat) {
                                                        $us_tmp=$ci->m_user->m_user->get_user_by_login_name($value->username);
                                                        ?>
                                                        <option value="<?=$us_tmp->username?>" selected><?=$us_tmp->nickname?></option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                            </div>
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
  var resource_usn_old=$("select[name='resource_usn_old[]']").serialize();
  var id_old=$("input[name='id_old[]']").serialize();
  var del_list=$("input[name='del_list[]']").serialize();
  $.ajax({
                method: "POST",
                url: "<?=site_url('cs/work_add_resource/'.$work_id)?>",
                data: "save=<?=$work_id?>&t_type=<?=$hour_rate->id?>&project_id=<?=$project_id?>&"+resource_usn+"&"+resource_usn_old+"&"+id_old+"&"+del_list
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                parent.draw_left_right();
                parent.print_table("<?=$project_id?>");
                if (data['msg']!="NO") {
                    alert(data['msg']);
                };
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
$(document).on("click", ".del_allow", function() {        
    $( "#bill_t_body" ).append('<input type="hidden" name="del_list[]" value="'+$( this ).attr("iden")+'">');
        $(this).parent().parent().fadeOut(300,function(){
            $(this).remove();
        });
        
    });
$(document).on("click", ".add_allow", function() {
        $.ajax({
                method: "POST",
                url: "<?=site_url('cs/work_add_resource/'.$work_id)?>",
                data: {
                    "add_bill": "add_bill",
                }
            })
            .done(function(data) {
                $("#bill_t_body").append(data);
                call_datepicker();
            });
        
    });
call_datepicker();
</script>
