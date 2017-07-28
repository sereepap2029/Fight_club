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
                                <div class="muted pull-left">วันที่ <?=$ci->m_time->unix_to_datepicker($holiday->time)?></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar"> 
                                   
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example2">
                                        <thead>
                                            <tr>
                                              <th>Holiday</th>
                                              <th>Comment</th>                                          
                                          </tr>
                                        </thead>
                                        <tbody>
                                                 <tr>
                                                      <td><select style="" name="is_holiday">
                                                        <option value="n">no</option>
                                                        <option value="y" <?if($holiday->is_holiday=="y"){echo "selected";}?>>yes</option>
                                                      </select></td>
                                                      <td>
                                                          <input style="" type="text" name="comment" value="<?=$holiday->comment?>">
                                                      </td>
                                                  </tr>  
                                            <tr>
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
        });
        </script>

<script type="text/javascript">
function close_fancy() {
  $("#close_but").html("saving.....!!");
  var comment_dat=$("input[name='comment']").val()
  var is_holiday_dat=$("select[name='is_holiday']").val();
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("hr/pop_holiday"); ?>",
                data: {
                    time : '<?=$holiday->time?>',
                    comment:comment_dat,
                    is_holiday:is_holiday_dat,
                }
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                if (is_holiday_dat=="y") {
                    parent.$("td[unix-time='<?=$holiday->time?>']").addClass("holiday");
                    parent.$("td[unix-time='<?=$holiday->time?>']").attr("title",comment_dat);
                }else{
                    parent.$("td[unix-time='<?=$holiday->time?>']").removeClass("holiday");
                    parent.$("td[unix-time='<?=$holiday->time?>']").attr("title","");
                }
                parent.$.fancybox.close();
              }else{
                alert("error happen");
                console.log(data)
              }
            });
    
}
</script>