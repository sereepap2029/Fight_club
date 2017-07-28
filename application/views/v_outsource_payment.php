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
                        <div class="muted pull-left">Payment term | Amount : <?=$out->qt_cost?></div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                <thead>
                                    <tr>
                                        <th>Payment Date.</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bill_t_body">
                                    <?
                                    foreach ($out_bill as $key => $value) {
                                      
                                    ?>
                                    <tr>
                                        <td>
                                            <input class="datepicker" type="text" name="time_old[]" value="<?=$ci->m_time->unix_to_datepicker($value->time)?>">
                                            <input class="" type="hidden" name="id[]" value="<?=$value->id?>">
                                        </td>
                                        <td>
                                            <input class="" type="text" name="amount_old[]" value="<?=$value->amount?>">
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

  var pay_id=$("input[name='id[]']").serialize()
  var time_old=$("input[name='time_old[]']").serialize()
  var amount_old=$("input[name='amount_old[]']").serialize()
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/outsource_payment"); ?>",
                data: "save=<?=$out_id?>&"+send_dat_time+"&"+send_dat_amount+"&"+amount_old+"&"+pay_id+"&"+time_old
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
                url: "<?php echo site_url("project/check_pay_outsource_date_ready/"); ?>",
                data: "save=<?=$out_id?>"
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
                url: "<?php echo site_url("project/outsource_payment"); ?>",
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
                url: "<?php echo site_url("project/outsource_payment"); ?>",
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
