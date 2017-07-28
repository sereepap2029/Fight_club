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
                        <div class="muted pull-left">เพิ่ม ศิลปะการต่อสู้ </div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <h5> <?if (isset($err_msg)) {
                                        echo "*******".$err_msg."*******";
                                    }?></h5>
                            <form class="form-horizontal" method="post" action="<? if(isset($edit)){echo site_url('admin/hour_rate_edit/'.$hour_rate->id);}else{echo site_url('admin/hour_rate_add');}?>">
                                <fieldset>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">ศิลปะการต่อสู้</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="name">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="name" value="<?echo $hour_rate->name;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Rate</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="hour_rate">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="hour_rate" value="<?echo $hour_rate->hour_rate;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">รายละเอียด</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <textarea class="focused" name="description" style="width:80%;height:200px"></textarea>
                                            <? }else{ ?>
                                            <textarea class="focused" id="" type="text" name="description" style="width:80%;height:200px"><?echo $hour_rate->description;?></textarea>
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
</script>
