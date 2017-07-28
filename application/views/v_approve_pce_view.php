<? $ci=& get_instance(); 
$prem_flag=(isset($user_data->prem['csd'])
 ||isset($user_data->prem['hod'])
 ||isset($user_data->prem['fc']));
     $status_arr = array('y' => "Approve",'n' => "reject" ,'ns' => "not sign");
     $ty_arr = array('csd' => "CSD",'fc' => "FC" ,'hod' => "HOD");
     $assign_status = array('y' => "Complete Assign",'n' => "Not Assign");
?>

<head>
</head>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
            <?
            if ($type!="hod") {
            ?>
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
                <?
            }
                ?>
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left"> <?=$ty_arr[$type]?> Approve PCE#</div>
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
                                    <?
                                    $hod_can_approve=false;
                                    if ($type=="csd") {
                                        ?>
                                        <tr>
                                            <td><?
                                            if ($prem_flag) {
                                                ?>
                                                <input type="text" id="time" class="datetimepicker" value="<?if ($pce_doc->csd_sign_time<100000) {
                                                    echo $ci->m_time->unix_to_datetimepicker(time());
                                                }else{
                                                    echo $ci->m_time->unix_to_datetimepicker($pce_doc->csd_sign_time);
                                                }?>">
                                                <?
                                            }else{
                                                if ($pce_doc->csd_sign_time<100000) {
                                                    echo $ci->m_time->unix_to_datetimepicker(time());
                                                }else{
                                                    echo $ci->m_time->unix_to_datetimepicker($pce_doc->csd_sign_time);
                                                }
                                            }?></td>
                                    
                                            <td><?=$pce_doc->csd_sign_name?></td>
                                      
                                            <td><?=$status_arr[$pce_doc->csd_sign_status]?></td>
                                        
                                            <td><?
                                            if ($prem_flag) {
                                                ?>
                                                <input type="text" id="comment" value="<?=$pce_doc->csd_comment?>">
                                                <?
                                            }else{
                                                echo $pce_doc->csd_comment;
                                            }
                                            ?></td>
                                        </tr>
                                        <?
                                    }else if($type=="fc") {
                                        ?>
                                        <tr>
                                            <td><?
                                            if ($prem_flag) {
                                                ?>
                                                <input type="text" id="time" class="datetimepicker" value="<? 
                                                if ($pce_doc->fc_sign_time<100000) {
                                                    echo $ci->m_time->unix_to_datetimepicker(time());
                                                }else{
                                                    echo $ci->m_time->unix_to_datetimepicker($pce_doc->fc_sign_time);
                                                }?>">
                                                <?
                                            }else{
                                                if ($pce_doc->fc_sign_time<100000) {
                                                    echo $ci->m_time->unix_to_datetimepicker(time());
                                                }else{
                                                    echo $ci->m_time->unix_to_datetimepicker($pce_doc->fc_sign_time);
                                                }
                                            }?></td>
                                    
                                            <td><?=$pce_doc->fc_sign_name?></td>
                                      
                                            <td><?=$status_arr[$pce_doc->fc_sign_status]?></td>
                                        
                                            <td><?
                                            if ($prem_flag) {
                                                ?>
                                                <input type="text" id="comment" value="<?=$pce_doc->fc_comment?>">
                                                <?
                                            }else{
                                                echo $pce_doc->fc_comment;
                                            }
                                            ?></td>
                                        </tr>
                                        <?
                                    }else if($type=="hod"){
                                        foreach ($pce_doc->hod_list as $key => $value) {
                                            $hod_sign=$this->m_user->get_user_by_login_name($value->hod_usn);
                                            $hod_sign_name="";
                                            if (!isset($hod_sign->firstname)) {
                                                $hod_sign_name="Hod Has been deleted";
                                            }else{
                                                $hod_sign_name=$hod_sign->firstname." ".$hod_sign->lastname;
                                            }
                                            
                                            ?>
                                                <tr>
                                                    <td><?
                                                    if ($prem_flag&&$value->hod_usn==$user_data->username) {
                                                        $hod_can_approve=true;
                                                        ?>
                                                        <input type="text" id="time" class="datetimepicker" value="<?if ($value->approve_time<100000) {
                                                            echo $ci->m_time->unix_to_datetimepicker(time());
                                                        }else{
                                                            echo $ci->m_time->unix_to_datetimepicker($value->approve_time);
                                                        }?>">
                                                        <?
                                                    }else{
                                                        if ($value->approve_time<100000) {
                                                            echo $ci->m_time->unix_to_datetimepicker(time());
                                                        }else{
                                                            echo $ci->m_time->unix_to_datetimepicker($value->approve_time);
                                                        }
                                                    }?></td>
                                            
                                                    <td><?=$hod_sign_name?></td>
                                              
                                                    <td><?=$status_arr[$value->approve]." : ".$assign_status[$value->complete_assign]?></td>
                                                
                                                    <td><?
                                                    if ($prem_flag&&$value->hod_usn==$user_data->username) {
                                                        ?>
                                                        <input type="text" id="comment" value="<?=$value->comment?>">
                                                        <?
                                                    }else{
                                                        echo $value->comment;
                                                    }
                                                    ?></td>
                                                </tr>
                                            <?
                                        }

                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?                            
                            if ($prem_flag&&!(($hod_can_approve&&!$type=='hod')||(!$hod_can_approve&&$type=='hod'))) {
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
  
  var comment=$("#comment").val();
  var time=$("#time").val();
  if (iden=="approve") {
    approve_pce('<?=$type?>','<?=$pce_doc->id?>',comment,time);
  }else if(iden=="reject"){
    reject_pce('<?=$type?>','<?=$pce_doc->id?>',comment,time);
  }else{
    parent.$.fancybox.close();
  }
    
}
function approve_pce(type,pce_id,comment,time){
    if (confirm(type+" Approve PCE#")) {
        $(".close_but").html("saving.....!!");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/approve_pce"); ?>",
                data: {
                    "type": type,
                    "pce_id": pce_id,
                    "status": "y",
                    "time": time,
                    "project_id": "<?=$pce_doc->project_id?>",
                    "comment": comment
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    parent.$("#"+type+"_a_"+pce_id).html('<i class="icon-ok icon-white"></i><?=$ty_arr[$type]?>');
                    parent.$("#"+type+"_a_"+pce_id).attr("class","btn btn-inverse fancybox");
                    parent.window.open("<?=site_url("project/view_sign_pce/".$pce_doc->id)?>","_blank");
                    parent.$.fancybox.close();
                }else{
                    alert(data['flag']);
                }
                //$("#last_tr").before(data);
            });
    }
}
function reject_pce(type,pce_id,comment,time){
    if (confirm(type+" Reject PCE#")) {
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/approve_pce"); ?>",
                data: {
                    "type": type,
                    "pce_id": pce_id,
                    "status": "n",
                    "project_id": "<?=$pce_doc->project_id?>",
                    "time": time,
                    "comment": comment
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    parent.$("#"+type+"_a_"+pce_id).html('<i class="icon-remove icon-white"></i><?=$ty_arr[$type]?> ');
                    parent.$("#"+type+"_a_"+pce_id).attr("class","btn btn-danger fancybox");
                    parent.$.fancybox.close();
                }else{
                    alert(data['flag']);
                }
                //$("#last_tr").before(data);
            });
    }
}
</script>
