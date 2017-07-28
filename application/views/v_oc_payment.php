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
                        <div class="muted pull-left">OC Payment term | Amount : <?=$oc->oc_amount?></div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                <thead>
                                    <tr>
                                        <th>Billing Date.</th>
                                        <th>Amount</th>
                                        <th>Comment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bill_t_body">
                                    <?
                                    foreach ($oc_bill as $key => $value) {
                                      
                                    ?>
                                    <tr>
                                        <td>
                                            <input class="datepicker" type="text" name="time_old[]" value="<?=$ci->m_time->unix_to_datepicker($value->time)?>">
                                            <input class="" type="hidden" name="bil_id[]" value="<?=$value->id?>">
                                        </td>
                                        <td>
                                            <input class="" type="text" name="amount_old[]" value="<?=$value->amount?>">
                                        </td>
                                        <td>
                                            <input class="" type="text" name="comment_old[]" value="<?=$value->comment?>">
                                        </td>
                                        <td>
                                          <a href="javascript:;" class="btn btn-danger del_bill_old" id="<?=$value->id?>"><i class="icon-remove icon-white"></i></a>
                                        </td>
                                    </tr>
                                    <?
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <a href="javascript:;" class="btn btn-success add_bill"><i class="icon-plus icon-white"></i></a>
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
  var send_dat_time=$("input[name='time[]']").serialize()
  var send_dat_amount=$("input[name='amount[]']").serialize()
  var send_dat_comment=$("input[name='comment[]']").serialize()

  var bil_id=$("input[name='bil_id[]']").serialize()
  var send_dat_time_old=$("input[name='time_old[]']").serialize()
  var send_dat_amount_old=$("input[name='amount_old[]']").serialize()
  var send_dat_comment_old=$("input[name='comment_old[]']").serialize()
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/oc_payment/".$oc->id); ?>",
                data: "save=<?=$oc_id?>&"+send_dat_time+"&"+send_dat_amount+"&"+send_dat_comment+"&"+bil_id+"&"+send_dat_time_old+"&"+send_dat_amount_old+"&"+send_dat_comment_old
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                //parent.$.fancybox.close();
                check_bill_ready()
              }else{
                alert("error happen");
                console.log(data)
                $("#close_but").html("OK");
              }
            });
    
}
function check_bill_ready(){
    $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/check_oc_bill_date_ready/"); ?>",
                data: "save=<?=$oc_id?>"
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                parent.$.fancybox.close();
              }else{
                alert(data['flag']);
                $("#close_but").html("OK");
                //console.log(data)
              }
            });
}
function call_datepicker(){
  $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd/mm/yy"
    });
}
$(function() {
    $("a").tooltip({});
    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd/mm/yy"
    });
});
$(document).on("click", ".del_bill", function() {        
        $(this).parent().parent().fadeOut(300,function(){
            $(this).remove();
        });
        
    });
$(document).on("click", ".del_bill_old", function() {      
    var cur_ele=$(this)
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/oc_payment"); ?>",
                data: "del="+cur_ele.attr("id"),
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                 cur_ele.parent().parent().fadeOut(300,function(){
                        $(this).remove();
                    });
              }else{
                alert(data['flag']);
              }
            });  
       
        
    });
$(document).on("click", ".add_bill", function() {
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/oc_payment/".$oc->id); ?>",
                data: {
                    "add_bill": "add_bill"
                }
            })
            .done(function(data) {
                $("#bill_t_body").append(data);
                call_datepicker();
            });
        
    });
</script>
