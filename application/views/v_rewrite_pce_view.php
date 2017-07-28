<? $ci=& get_instance(); 
?>

<?
//print_r($pce_doc);
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left"> PCE History </div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                <thead>
                                    <tr>
                                        <th>PCE No.</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Stat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                    foreach ($pce_doc as $key => $value) {
                                        if ($value->rewrite_stat=="n") {
                                            ?>
                                            <tr>
                                            <td>
                                                <a href="<?echo site_url("project/view_sign_pce/".$value->id)?>" target="_blank"><?=$value->pce_no?></a>
                                            </td>
                                            <td>
                                                <?echo $value->pce_des;?>
                                            </td>
                                            <td>
                                                
                                                <?=number_format($value->pce_amount, 2, '.', ',')?>
                                                
                                            </td>
                                            <td>
                                                Current
                                            </td>
                                            </tr>
                                            <?
                                            break;
                                        }
                                    }
                                    ?>
                                    <?
                                    foreach ($pce_doc as $key => $value) {
                                        if ($value->rewrite_stat=="y") {
                                            ?>
                                            <tr>
                                            <td>
                                                <a href="<?echo site_url("project/view_sign_pce/".$value->id)?>" target="_blank"><?=$value->pce_no?></a>
                                            </td>
                                            <td>
                                                <?echo $value->pce_des;?>
                                            </td>
                                            <td>
                                                
                                                <?=number_format($value->pce_amount, 2, '.', ',')?>
                                                
                                            </td>
                                            <td>
                                                Rewrite
                                            </td>
                                            </tr>
                                            <?
                                        }
                                    }
                                    ?>
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
function close_fancy() {  
    parent.$.fancybox.close();
    
}
</script>
