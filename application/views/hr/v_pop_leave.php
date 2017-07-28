<?
$ci =& get_instance();
?>
<style type="text/css">
  .green-f{
    color: green;
  }
  .datetimepicker{
    width:100px;
  }
</style>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block" >
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">วันที่ <?=$ci->m_time->unix_to_datepicker($time)?></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12" style="min-height:300px;">
                                   <div class="table-toolbar"> 
                                   
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example2">
                                        <thead>
                                            <tr>
                                              <th>ชื่อ-นามสกุล</th>
                                              <th>เริ่ม</th>
                                              <th>สิ้นสุด</th>
                                          </tr>
                                        </thead>
                                        <tbody id="table_body">
                                         <?
                                         foreach ($leave_list as $key => $value) {
                                           ?>
                                           <tr>
                                             <td>
                                               <div class="control-group">
                                                <select id="usn" class="chzn-select" name="usn[]">
                                                    <option value="no">---please select ------</option>
                                                    <?
                                                    foreach ($user_list as $key2 => $value2) {
                                                                            ?>
                                                        <option value="<?=$value2->username?>" <?if($value->usn==$value2->username){echo "selected";}?>><?=$value2->nickname?></option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                             </td>
                                             <td>
                                               <input type="text" class="datetimepicker" name="time_start[]" value="<?=date("H:i",$value->time_start)?>">
                                             </td>
                                             <td>
                                               <input type="text" class="datetimepicker" name="time_end[]" value="<?=date("H:i",$value->time_end)?>">
                                             </td>
                                           </tr>
                                           <?
                                         }
                                         ?>
                                            <tr id="before_action">
                                                      <td></td>
                                                      <td><a href="javascript:add_record();" class="btn btn-info">Add</a></td>
                                                      <td><a id="close_but" href="javascript:close_fancy();" class="btn btn-success">OK</a></td>
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
        function call_datepicker(){
          $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "dd/mm/yy"
            });
          $(".chzn-select").chosen({
                    width: "75%"
                });
          jQuery('.datetimepicker').datetimepicker({
            datepicker:false,
            format:'H:i'
          });
        }
        $(function() {
          call_datepicker();
          
        });
        </script>

<script type="text/javascript">
function add_record() {
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("hr/pop_user_leave"); ?>",
                data: {
                    add : 'add',
                }
            })
            .done(function(data) {
              $("#before_action").before(data);
              call_datepicker();
            });
}
function close_fancy() {
  $("#close_but").html("saving.....!!");
  var resource_usn=$("select[name='usn[]']").serialize();
  var time_start=$("input[name='time_start[]']").serialize();
  var time_end=$("input[name='time_end[]']").serialize();
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("hr/pop_user_leave"); ?>",
                data: "save=<?=$time?>&"+resource_usn+"&"+time_start+"&"+time_end
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                if (data['have_leave']=="y") {
                    parent.$("td[unix-time='<?=$time?>']").addClass("holiday");
                }else{
                    parent.$("td[unix-time='<?=$time?>']").removeClass("holiday");
                }
                parent.$.fancybox.close();
              }else{
                alert("error happen");
                console.log(data)
              }
            });
    
}
</script>