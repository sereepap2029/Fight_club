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
                        <div class="muted pull-left">เพิ่มทีม</div>
                    </div>
                    <div class="block-content collapse in" style="min-height:600px;">
                        <div class="span12">
                            <h5> <?if (isset($err_msg)) {
                                        echo "*******".$err_msg."*******";
                                    }?></h5>
                            <form class="form-horizontal" method="post" action="<? if(isset($edit)){echo site_url('admin/department_edit/'.$department->id);}else{echo site_url('admin/department_add');}?>">
                                <fieldset>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">ชื่อทีม</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="name">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="name" value="<?echo $department->name;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="role">ชื่อค่าย</label>
                                        <div class="controls">
                                            <select id="business_id" class="chzn-select" name="business_id">
                                                <option value="no">-----please select-----</option>
                                                <? foreach ($business_list as $key=> $value) { ?>
                                                <option value="<?=$value->id?>"><?=$value->name?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <? if (isset($edit)) { ?>
                                        <script type="text/javascript">
                                        $("#business_id").val("<?echo $department->business_id;?>")
                                        </script>
                                        <? } ?>
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
    });
    $('.datetimepicker').datetimepicker();
    $(".chzn-select").chosen({
        width: "75%"
    });
});
</script>
