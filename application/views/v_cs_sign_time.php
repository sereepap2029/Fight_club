<? $ci=& get_instance(); 
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
                        <div class="muted pull-left"> <?=$pce_doc->pce_no?></div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                <thead>
                                    <tr>
                                        <th>Date.</th>
                                        <th>File</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="bill_t_body">
                                        <tr>
                                            <td>
                                                <input type="text" id="time" class="datetimepicker" value="<?if ($pce_doc->cs_sign_time<100000) {
                                                    echo $ci->m_time->unix_to_datetimepicker(time());
                                                }else{
                                                    echo $ci->m_time->unix_to_datetimepicker($pce_doc->cs_sign_time);
                                                }?>"></td>
                                    
                                            <td><a href="<?echo site_url("project/view_sign_pce/".$pce_doc->id)?>" target="_blank"><?=$pce_doc->pce_no?></a></td>
                                      
                                            <td><?=$pce_doc->pce_des?></td>
                                        
                                            <td><?=number_format($pce_doc->pce_amount, 2, '.', ',')?></td>
                                        </tr>
                                </tbody>
                            </table>                            
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
    $(".datetimepicker").datetimepicker();
});
function close_fancy(iden) {  
  var time=$("#time").val();
  $(".close_but").html("saving.....!!");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/cs_set_sign_time"); ?>",
                data: {
                    "save": '<?=$pce_doc->id?>',
                    "time": time,
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    parent.$.fancybox.close();
                }else{
                    alert(data['flag']);
                }
            });
    
}

</script>
