<? $ci=& get_instance(); 
$prem_flag=(isset($user_data->prem['fc']));
     $status_arr = array('y' => "Approve",'n' => "reject" ,'ns' => "not sign");
?>

<head>
</head>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left"> Permit User</div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                    foreach ($prem_usn_list as $key => $value) {

                                        ?>
                                        <tr>
                                        <td>
                                            <?
                                            echo $value->firstname." ".$value->lastname;
                                            ?>
                                        </td>
                                        </tr>
                                        <?
                                    }
                                    ?>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left"> FC Approve OC#</div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                <thead>
                                    <tr>
                                        <th>Date.</th>
                                        <th>Approver</th>
                                        <th>status</th>
                                        <th>comment</th>
                                    </tr>
                                </thead>
                                <tbody id="bill_t_body">
                                        <tr>
                                            <td><?
                                            if ($prem_flag) {
                                                ?>
                                                <input type="text" id="time" class="datetimepicker" value="<? if ($oc->fc_sign_time<100000) {
                                                    echo $ci->m_time->unix_to_datetimepicker(time());
                                                }else{
                                                    echo $ci->m_time->unix_to_datetimepicker($oc->fc_sign_time);
                                                }?>">
                                                <?
                                            }else{
                                                if ($oc->fc_sign_time<100000) {
                                                    echo $ci->m_time->unix_to_datetimepicker(time());
                                                }else{
                                                    echo $ci->m_time->unix_to_datetimepicker($oc->fc_sign_time);
                                                }
                                            }?></td>
                                    
                                            <td><?=$oc->fc_sign?></td>
                                      
                                            <td><?=$status_arr[$oc->status]?></td>
                                        
                                            <td><?
                                            if ($prem_flag) {
                                                ?>
                                                <input type="text" id="comment" value="<?=$oc->comment?>">
                                                <?
                                            }else{
                                                echo $oc->comment;
                                            }
                                            ?></td>
                                        </tr>
                                </tbody>
                            </table>
                            <?                            
                            if ($prem_flag) {
                                ?>
                                <a href="javascript:close_fancy('reject');" class="btn btn-danger close_but"><i class="icon-remove icon-white"></i>Reject</a>
                            <a id="close_but" href="javascript:close_fancy('approve');" class="btn btn-success close_but">Approve</a>
                                <?
                            }else{
                                ?>                                
                                <a id="close_but" href="javascript:close_fancy('');" class="btn btn-info">OK</a>
                                <?
                            }
                            ?>
                            
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
  
  var comment=$("#comment").val()
  var time=$("#time").val();
  if (iden=="approve") {
    approve_oc('<?=$oc->id?>',comment,time);
  }else if(iden=="reject"){
    reject_oc('<?=$oc->id?>',comment,time);
  }
    
}
function approve_oc(oc_id,comment,time){
    if (confirm(" Approve OC#")) {
        $(".close_but").html("saving.....!!");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/approve_oc"); ?>",
                data: {
                    "oc_id": oc_id,
                    "status": "y",
                    "time": time,
                    "comment": comment
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    parent.$("#fc_oc_"+oc_id).html('<i class="icon-ok icon-white"></i>FC');
                    parent.$("#fc_oc_"+oc_id).attr("class","btn btn-inverse fancybox");
                    parent.$.fancybox.close();
                }else{
                    alert(data['flag']);
                }
                //$("#last_tr").before(data);
            });
    }
}
function reject_oc(oc_id,comment,time){
    if (confirm(" Reject OC#")) {
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/approve_oc"); ?>",
                data: {
                    "oc_id": oc_id,
                    "status": "n",
                    "time": time,
                    "comment": comment
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    parent.$("#fc_oc_"+oc_id).html('<i class="icon-remove icon-white"></i>FC ');
                    parent.$("#fc_oc_"+oc_id).attr("class","btn btn-danger fancybox");
                    parent.$.fancybox.close();
                }else{
                    alert(data['flag']);
                }
                //$("#last_tr").before(data);
            });
    }
}
</script>
