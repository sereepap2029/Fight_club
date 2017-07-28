<?public function ajax_add_pce_html(){ 
$cur_time=time(); ?>
    <div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left">
        <table class="table table-noborder">
            <tr>
                <td> <a class="btn btn-info fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/cs_set_sign_time/ ".$cur_time);?>"><i class="icon-pencil icon-white"></i></a>
                    <?
                $have_rewrite=$ci->m_pce->get_pce_rewrite_child_by_id($pce->id);
                if (isset($have_rewrite->id)) {
                    ?>
                        <a class="btn btn-warning fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/rewrite_pce_view/ ".$cur_time);?>"><i class="icon-list icon-white"></i></a>
                        <?
                }
                ?>
                </td>
                <td></td>
                <td style="text-align:right;"><a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-warning pce_rewrite">Revise</a></td>
            </tr>
            <tr>
                <td class="first-ta">PCE#</td>
                <td colspan="2" style="text-align: left;">
                    <a href="<?echo site_url(" media/temp/ ".$_POST['pce_file'])?>" target="_blank">
                        <?=$_POST['pce_no']?>&nbsp;&nbsp;<img src="<?echo site_url(" img/pdf_img.png ")?>"></a>
                    <input type="hidden" name="pce_filename[<?echo $cur_time;?>]" value="<?=$_POST['pce_file']?>">
                    <input type="hidden" name="pce_no[<?echo $cur_time;?>]" value="<?=$_POST['pce_no']?>">
                </td>
            </tr>
            <tr>
                <td class="first-ta">Description</td>
                <td colspan="2" style="text-align: left;">
                    <?=$_POST['pce_des']?>
                        <input type="hidden" name="pce_des[<?echo $cur_time;?>]" value="<?=$_POST['pce_des']?>">
                </td>
            </tr>
            <tr>
                <td class="first-ta">Amount</td>
                <td colspan="2" style="text-align: left;">
                    <?=number_format($_POST['pce_amount'], 2, '.', ',')?>
                        <input type="hidden" name="pce_amount[<?echo $cur_time;?>]" value="<?=$_POST['pce_amount']?>">
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center;">
                    <?
                     $hod_all_approve=true;
                     $hod_reject_flag=false;
                     foreach ($pce->hod_list as $hlistkey => $hlistvalue) {
                         if($hlistvalue->approve=="ns"){
                             $hod_all_approve=false;
                         }
                         if($hlistvalue->approve=="n"){
                             $hod_reject_flag=true;
                         }
                     }
                     if ($pce->csd_sign_status=="y") {                                                    
                         ?>
                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/csd ");?>"><i class="icon-ok icon-white"></i>CSD </a>
                        <?
                    }else if($pce->csd_sign_status=="n"){
                        ?>
                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/csd ");?>"><i class="icon-remove icon-white"></i>CSD </a>
                        <?
                    }else{
                        ?>
                        <a id="csd_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/csd ");?>">CSD </a>
                        <?
                    }
                    if($hod_reject_flag){
                    ?>
                    <a id="hod_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/hod ");?>"><i class="icon-remove icon-white"></i>HOD </a>
                    <?
                    }else if (isset($pce->hod_list[$user_data->username])&&$pce->hod_list[$user_data->username]->approve=="y") {
                        ?>
                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/hod ");?>"><i class="icon-ok icon-white"></i>HOD </a>
                        <?
                    }else if($hod_all_approve){
                        ?>
                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/hod ");?>"><i class="icon-ok icon-white"></i>HOD </a>
                        <?
                    }else{
                        ?>
                        <a id="hod_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/hod ");?>">HOD </a>
                        <?
                    }
                    if ($pce->fc_sign_status=="y") {                                                    
                        ?>
                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/fc ");?>"><i class="icon-ok icon-white"></i>FC </a>
                        <?
                    }else if($pce->fc_sign_status=="n"){
                        ?>
                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/fc ");?>"><i class="icon-remove icon-white"></i>FC </a>
                        <?
                    }else{
                        ?>
                            <a id="fc_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url(" project/approve_pce_view/ ".$cur_time."/fc ");?>">FC </a>
                            <?
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left">
        <table class="table table-noborder">
            <tr>
                <td style="text-align:center">
                    <a id="" iden="<?=$cur_time?>" togStat="hide" href="javascript:;" class="btn btn-large btn-block toggle_outsource">Out source</a>
                </td>
            </tr>
        </table>
    </div>
    <div id="outsource_<?echo $cur_time;?>" class="span12 no-margin-left" style="display:none">
        <div class="span12 no-margin-left">
            <h5>Out Source</h5>
            <h5>PCE# <?=$_POST['pce_no']?></h5>
            <div>
                <?=$_POST['pce_des']?>
            </div>
        </div>
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
            <thead>
                <tr>
                    <th>QT#</th>
                    <th></th>
                    <th>description</th>
                    <th>Cost</th>
                    <th>Charge</th>
                    <th>Margin</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="table_out_<?=$cur_time?>">
                <tr id="be_out_<?=$cur_time?>">
                    <td></td>
                    <td></td>
                    <td>Grand Total</td>
                    <td class="total_cost"></td>
                    <td class="total_charge"></td>
                    <td class="total_margin"></td>
                    <td>
                        <a iden="<?=$cur_time?>" href="javascript:;" class="btn btn-success add_out_list"><i class="icon-plus icon-white"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="del-but_<?=$cur_time?>" class="span12 no-margin-left">
        <table class="table table-noborder">
            <tr>
                <td style="text-align:center">
                    <a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-danger pce_delete">DELETE</a>
                </td>
            </tr>
        </table>
    </div>
    <hr id="hr_<?echo $cur_time;?>">
    <?

}
    ?>
