<? $ci=& get_instance(); 
$prem_flag=(isset($user_data->prem['csd'])
 ||isset($user_data->prem['hod'])
 ||isset($user_data->prem['fc']));
     $status_arr = array('y' => "Approve",'n' => "reject" ,'ns' => "not sign");
     $ty_arr = array('csd' => "CSD",'fc' => "FC" ,'hod' => "HOD");
?>

<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<style type="text/css">
    .img-region{
            margin: 5px;
            display: inline-block;
            max-width: 200px;
            position: relative;
    }
    .img-region .del_img_but{
            position: absolute;
            right: -5px;
            top: -5px;
    }
    .ui-state-highlight{
        margin: 5px;
        display: inline-block;
        width: 200px;
        height: 200px;
        position: relative;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left"> Task <?=$work->work_name?> Note</div>
                    </div>
                    <div class="block-content collapse in">
                        
                                <div class="span12 no-margin-left">
                                    <div class="span12">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Task Note</label>
                                            <p><?=htmlspecialchars($work->comment)?></p>
                                        </div>
                                    </div>
                                </div>                   
                                <div id="img_hold" class="span12 no-margin-left" style="margin-bottom:20px;">
                                 <?
                                 foreach ($work_photo as $key => $value) {
                                     ?>
                                     <div class="img-region" id="<?=$value->id?>">
                                        <a class="fancybox" rel="gallery1" href="<?=site_url("media/work_sheet_comment_photo/".$value->filename)?>" title="">
                                            <img src="<?=site_url("media/work_sheet_comment_photo/".$value->filename)?>" alt="" />
                                        </a>
                                        <input type="hidden" name="photo_id_list[]" value="<?=$value->id?>">                                        
                                    </div>
                                     <?
                                 }
                                 ?>                                        
                                    </div>                                         
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
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="<?echo site_url();?>js/upload/vendor/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?echo site_url();?>js/upload/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?echo site_url();?>js/upload/jquery.fileupload.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(".fancybox").fancybox({
        openEffect  : 'none',
        closeEffect : 'none'
    });
});
function close_fancy() {
 parent.$.fancybox.close();    
}
</script>
