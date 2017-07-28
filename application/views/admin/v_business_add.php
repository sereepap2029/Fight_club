<? $ci=& get_instance(); ?>
<style type="text/css">
.row-fluid .no-margin-left {
    margin-left: 0px;
}
</style>
<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<link rel="stylesheet" href="<?echo site_url();?>css/style.css">
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">Add Business </div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <h5> <?if (isset($err_msg)) {
                                        echo "*******".$err_msg."*******";
                                    }?></h5>
                            <form class="form-horizontal" method="post" action="<? if(isset($edit)){echo site_url('admin/business_edit/'.$business->id);}else{echo site_url('admin/business_add');}?>">
                                <fieldset>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Business Name</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="name">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="name" value="<?echo $business->name;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <button type="submit" class="btn btn-primary">บันทึก</button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /block -->
            </div>
        </div>
    </div>
</div>
<!--/.fluid-container-->
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="<?echo site_url();?>js/upload/vendor/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?echo site_url();?>js/upload/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?echo site_url();?>js/upload/jquery.fileupload.js"></script>
<script>
/*jslint unparam: true */
/*global window, $ */
$(function() {
    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        beforeShow: function() {
            if ($(this).val() != "") {
                var arrayDate = $(this).val().split("/");
                arrayDate[2] = parseInt(arrayDate[2]) - 543;
                $(this).val(arrayDate[0] + "/" + arrayDate[1] + "/" + arrayDate[2]);
            }
            setTimeout(function() {
                $.each($(".ui-datepicker-year option"), function(j, k) {
                    var textYear = parseInt($(".ui-datepicker-year option").eq(j).val()) + 543;
                    $(".ui-datepicker-year option").eq(j).text(textYear);
                });
            }, 50);
        }
    });
    $('.datetimepicker').datetimepicker();
    $(".chzn-select").chosen({
        width: "75%"
    });
});
$( document ).on( "click", "#after_bu", function() {
 $( this ).before('<div class="control-group"><label class="control-label" for="focusedInput">BU</label><div class="controls"><input class="focused" id="" type="text" name="bu[]"><a id="del_bu" href="javascript:;" class="btn btn-danger">del</i></a></div></div>')
});
$( document ).on( "click", "#del_bu", function() {
 $( this ).parent().parent().fadeOut(200,function(){
    $(this).remove();
 })
});
</script>
