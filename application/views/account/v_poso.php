<?
$ci =& get_instance();
?>
<style type="text/css">
  .green-f{
    color: green;
  }
</style>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">POBL</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar"> 
                                   <b><font style="color:red"> จะ Confirm ได้ก็ต่อเมื่อเวลาได้ผ่านวันที่วางบิลแล้วเท่านั้น</font></b>
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example2">
                                        <thead>
                                            <tr>
                                              <th>#</th>
                                              <th>OC</th>
                                              <th>Description</th>
                                              <th>Due date</th>
                                              <th>PO</th>
                                              <th>BL</th>
                                              <th>Amount</th>
                                              <th>Receive Amount</th>
                                              <th>Billing Date</th>
                                              <th>Confirm Bill</th>                                              
                                              <th>Receive Check date</th>
                                              <th>Confirm Check</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                           <?
                                            $num=0;
                                            $total=0;
                                            foreach ($oc as $key => $value) {
                                              foreach ($value->oc_bill as $bkey => $bvalue) {
                                                 $num+=1;
                                                 $total+=(int)$bvalue->amount;
                                                 if ($bvalue->paid_date<time()-(60*60*24*360)) {
                                                   $bvalue->paid_date=time();
                                                 }
                                                 ?>
                                                 <tr>
                                                      <td><? echo $num; ?></td>
                                                      <td><?=$value->oc_no?><input type="hidden" name="bill_id[]" value="<?=$bvalue->id?>"></td>
                                                      <td><?=$value->oc_des?></td>
                                                      <td><?=$ci->m_time->unix_to_datepicker($bvalue->time)?></td>
                                                      <td><input style="width:100px" type="text" name="po[]" value="<?=$bvalue->po?>"></td>
                                                      <td><input style="width:100px" type="text" name="so[]" value="<?=$bvalue->so?>"></td>
                                                      <td class="<?if($bvalue->collected=="y"){echo "green-f";}?>"><b><?=number_format($bvalue->amount, 2, '.', ',')?></b></td>
                                                      <td><input style="width:100px" type="text" name="paid_amount[]" value="<?=$bvalue->paid_amount?>"></td>
                                                      <td>
                                                      <?
                                                      $paid_date_str=$ci->m_time->unix_to_datepicker($bvalue->paid_date);
                                                      ?>
                                                          <input style="width:100px" class="datepicker c_event" type="text" name="paid_date[]" value="<?=$paid_date_str?>">
                                                      </td>
                                                      <td><select style="width:100px" name="colllect[]">
                                                        <option value="n">no</option>
                                                        <option value="y" <?if($bvalue->collected=="y"){echo "selected";}?>>yes</option>
                                                      </select></td>
                                                      <td>
                                                      <?
                                                      $re_date_show=$ci->m_time->unix_to_datepicker($bvalue->receive_check_date);
                                                      if ($bvalue->receive_check_date<=1000000) {
                                                        $cur_paid_date=$ci->m_time->datepicker_to_unix($paid_date_str);
                                                        $re_date_show=$ci->m_time->unix_to_datepicker($cur_paid_date+(60*60*24*(int)$credit_term));
                                                      }
                                                      ?>
                                                          <input style="width:100px" class="datepicker check_date" type="text" name="receive_check_date[]" value="<?=$re_date_show?>">
                                                      </td>
                                                      <td><select style="width:100px" name="receive_check_colllect[]">
                                                        <option value="n">no</option>
                                                        <option value="y" <?if($bvalue->receive_check_colllect=="y"){echo "selected";}?>>yes</option>
                                                      </select></td>
                                                  </tr>
                                                 <?
                                              }
                                             
                                            }
                                            ?>     
                                            <tr>
                                                      <td></td>
                                                      <td></td>
                                                      <td></td>
                                                      <td></td>
                                                      <td></td>
                                                      <td>Total</td>
                                                      <td><b><?=number_format($total, 2, '.', ',')?></b></td>
                                                      <td></td>
                                                      <td></td>
                                                      <td></td>
                                                      <td></td>
                                                      <td><a id="close_but" href="javascript:close_fancy();" class="btn btn-info">OK</a></td>
                                                  </tr>                                                                    
                                        </tbody>
                                    </table>
                                </div>
                                <div class="span6">
                                  
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
          $(".datepicker").datepicker({
              changeMonth: true,
              changeYear: true,
              dateFormat: "dd/mm/yy"
          });
          $(".fancybox").fancybox({
              maxWidth    : 800,
              maxHeight   : 600,
              fitToView   : false,
              width       : '90%',
              height      : '90%',
              autoSize    : false,
              closeClick  : false,
              openEffect  : 'none',
              closeEffect : 'none'
          });
        });
        </script>

<script type="text/javascript">
function close_fancy() {
  $("#close_but").html("saving.....!!");
  var po=$("input[name='po[]']").serialize()
  var so=$("input[name='so[]']").serialize()
  var paid_amount=$("input[name='paid_amount[]']").serialize()
  var paid_date=$("input[name='paid_date[]']").serialize()
  var colllect=$("select[name='colllect[]']").serialize()
  var bill_id=$("input[name='bill_id[]']").serialize()
  var receive_check_colllect=$("select[name='receive_check_colllect[]']").serialize()
  var receive_check_date=$("input[name='receive_check_date[]']").serialize()
  if (bill_id=="") {
    parent.$.fancybox.close();
  }else{
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("account/poso/".$project_id); ?>",
                data: bill_id+"&"+po+"&"+so+"&"+colllect+"&"+paid_amount+"&"+paid_date+"&"+receive_check_colllect+"&"+receive_check_date
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                parent.window.open('<?php echo site_url("account/"); ?>',"_self");
                parent.$.fancybox.close();

              }else{
                alert(data['flag']);
                $("#close_but").html("OK");
                //console.log(data)
              }
            });
          }
    
}
$(document).on("change", ".c_event", function() {
  var cur_ele=$(this);
    $.ajax({
                method: "POST",
                url: "<?php echo site_url("account/cal_date_credit_term/"); ?>",
                data: {paid_date:cur_ele.val(),credit_term:"<?=$credit_term?>"}
            })
            .done(function(data) {
              cur_ele.parent().parent().find(".check_date").val(data['dat']);
            });
});
</script>