<?
$ci =& get_instance();
?>
<style type="text/css">
  .green-f{
    color: green;
  }
  table.make-middle tr td{
    vertical-align: middle;
  }
</style>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">PO Outsource</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">       
                                   <b><font style="color:red"> จะ Confirm ได้ก็ต่อเมื่อเวลาได้ผ่านวันที่จ่ายแล้วเท่านั้น</font></b>                              
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered make-middle" id="example2">
                                        <thead>
                                            <tr>
                                              <th>#</th>
                                              <th>QT</th>
                                              <th>Description</th>
                                              <th>Due date</th>
                                              <th>INV</th>
                                              <th>Amount</th>
                                              <th>paid detail</th>                                           
                                          </tr>
                                        </thead>
                                        <tbody>
                                           <?
                                           //print_r($pce);
                                            $num=0;
                                            $total=0;
                                            foreach ($pce as $key => $value) {
                                              foreach ($value->outsource as $bkey => $bvalue) {
                                                foreach ($bvalue->bill as $ckey => $cvalue) {
                                                   $num+=1;
                                                   $total+=(int)$cvalue->amount;
                                                   if ($cvalue->paid_date<time()-(60*60*24*360)) {
                                                     $cvalue->paid_date=time();
                                                   }
                                                   ?>
                                                   <tr>
                                                        <td><? echo $num; ?></td>
                                                        <td><?=$bvalue->qt_no?><input type="hidden" name="bill_id[]" value="<?=$cvalue->id?>"></td>
                                                        <td><?=$bvalue->qt_des?></td>
                                                        <td><?=$ci->m_time->unix_to_datepicker($cvalue->time)?></td>
                                                        <td><input style="width:100px" type="text" name="inv[]" value="<?=$cvalue->inv?>"></td>                                                        
                                                        <td class="<?if($cvalue->paid=="y"){echo "green-f";}?>"><b><?=number_format($cvalue->amount, 2, '.', ',')?></b></td>
                                                        <td>
                                                        <a href="javascript:add_paid('<?=$cvalue->id?>');" class="btn btn-info">Add</a><br>
                                                             <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                      <th>PV</th>
                                                                      <th>amount</th>
                                                                      <th>วันที่บันทึก</th>
                                                                      <th>วันที่จ่าย</th>
                                                                      <th>paid comfirm</th>
                                                                      <th>Paid Type</th>                                      
                                                                  </tr>
                                                                </thead>
                                                                <tbody id="append_<?=$cvalue->id?>">
                                                                <?
                                                                foreach ($cvalue->paid_obj as $paid_obj_key => $paid_obj_value) {
                                                                  ?>
                                                                  <tr>
                                                                    <td><input style="width:100px" class="pv" type="text" name="pv[<?=$cvalue->id?>][]" value="<?=$paid_obj_value->pv?>"></td>
                                                                    <td><input style="width:100px" class="amount" type="text" name="amount[<?=$cvalue->id?>][]" value="<?=$paid_obj_value->amount?>"></td>
                                                                    <td><input style="width:100px" class="datepicker save_date" type="text" name="save_date[<?=$cvalue->id?>][]" value="<?=$ci->m_time->unix_to_datepicker($paid_obj_value->save_date)?>"></td>
                                                                    <td><input style="width:100px" class="datepicker date" type="text" name="date[<?=$cvalue->id?>][]" value="<?=$ci->m_time->unix_to_datepicker($paid_obj_value->date)?>"></td>
                                                                    <td><select style="width:100px" class="paid" name="paid[<?=$cvalue->id?>][]">
                                                                        <option value="n">no</option>
                                                                        <option value="y" <?if($paid_obj_value->paid=="y"){echo "selected";}?>>yes</option>
                                                                      </select></td>
                                                                      <td><select style="width:100px" class="paid_type" name="paid_type[<?=$cvalue->id?>][]">
                                                                        <option value="ไม่ระบุ">ไม่ระบุ</option>
                                                                        <option value="มัดจำ" <?if($paid_obj_value->paid_type=="มัดจำ"){echo "selected";}?>>มัดจำ</option>
                                                                        <option value="ค่าใช้จ่ายอื่น" <?if($paid_obj_value->paid_type=="ค่าใช้จ่ายอื่น"){echo "selected";}?>>ค่าใช้จ่ายอื่น</option>
                                                                      </select></td>
                                                                  </tr>
                                                                  <?
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                   <?
                                                }
                                                 
                                              }
                                             
                                            }
                                            ?>     
                                            <tr>
                                                      <td></td>
                                                      <td></td>
                                                      <td></td>
                                                      <td>Total</td>
                                                      <td><b><?=number_format($total, 2, '.', ',')?></b></td>
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
              width       : '70%',
              height      : '70%',
              autoSize    : false,
              closeClick  : false,
              openEffect  : 'none',
              closeEffect : 'none'
          });
        });
        </script>

<script type="text/javascript">
function testse(){
  var as=$(".inp").serialize()
  console.log(as);
}
function add_paid(id){// working here
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("account/poqp"); ?>",
                data: {
                    add: id,
                }
            })
            .done(function(data) {
                $("#append_"+id).append(data);
                $(".datepicker").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "dd/mm/yy"
                });
            });
}
function close_fancy() {
  $("#close_but").html("saving.....!!");
  var inv=$("input[name='inv[]']").serialize()
  var pv=$(".pv").serialize()
  var colllect=$(".paid").serialize()
  var colllect_type=$(".paid_type").serialize()
  var paid_amount=$(".amount").serialize()
  var bill_id=$("input[name='bill_id[]']").serialize()
  var paid_date=$(".date").serialize()
  var save_date=$(".save_date").serialize()
  if (bill_id=="") {
    parent.$.fancybox.close();
  }else{
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("account/poqp/".$project_id); ?>",
                data: bill_id+"&"+inv+"&"+pv+"&"+colllect+"&"+paid_amount+"&"+paid_date+"&"+save_date+"&"+colllect_type
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                //parent.window.open('<?php echo site_url("account/"); ?>',"_self");
                parent.$.fancybox.close();
              }else{
                alert(data['flag']);
                console.log(data)
                $("#close_but").html("OK");
              }
            });
          }
    
}
$(document).on("change", ".save_date", function() {
  var cur_ele=$(this);
    $.ajax({
                method: "POST",
                url: "<?php echo site_url("account/cal_date_credit_term/"); ?>",
                data: {paid_date:cur_ele.val(),credit_term:"30"}
            })
            .done(function(data) {
              cur_ele.parent().parent().find(".date").val(data['dat']);
            });
});
</script>