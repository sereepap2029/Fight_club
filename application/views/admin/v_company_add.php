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
                        <div class="muted pull-left">Add Client </div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <h5> <?if (isset($err_msg)) {
                                        echo "*******".$err_msg."*******";
                                    }?></h5>
                            <form class="form-horizontal" method="post" action="<? if(isset($edit)){echo site_url('admin/company_edit/'.$company->id);}else{echo site_url('admin/company_add');}?>">
                                <fieldset>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Client Name</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="name">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="name" value="<?echo $company->name;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Client ID</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="client_id">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="client_id" value="<?echo $company->client_id;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="span12 no-margin-left">
                                    <h5>BU list</h5>
                                    <?
                                    if (isset($edit)) {
                                        foreach ($company->bu as $key => $value) {
                                            ?>
                                            <div class="control-group">
                                                <label class="control-label" for="focusedInput">BU </label>
                                                
                                                <div class="controls">
                                                    <input type="hidden" name="id_old[]" value="<?echo $value->id;?>">
                                                    <input class="focused" id="" type="text" name="bu_old[]" value="<?echo $value->bu_name;?>">
                                                    <?
                                                    if ($value->is_use=="n") {
                                                        ?>
                                                        <a id="del_bu" iden="<?echo $value->id;?>" href="javascript:;" class="btn btn-danger">del</i></a>
                                                        <?
                                                    }
                                                    ?>
                                                    
                                                    
                                                </div>
                                                <label class="control-label" for="focusedInput">Credit Term </label>
                                                
                                                <div class="controls">
                                                    <input class="focused" id="" type="text" name="credit_term_old[]" value="<?echo $value->credit_term;?>">                                                    
                                                </div>
                                            </div>
                                            <?
                                        }
                                    }
                                    ?>
                                        <a id="after_bu" href="javascript:;" class="btn btn-success">Add BU <i class="icon-plus icon-white"></i></a>
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
 $( this ).before('<div class="control-group"><label class="control-label" for="focusedInput">BU</label><div class="controls"><input class="focused" id="" type="text" name="bu[]"><a id="del_bu" href="javascript:;" class="btn btn-danger">del</i></a></div><label class="control-label" for="focusedInput">Credit Term </label> <div class="controls"><input class="focused" id="" type="text" name="credit_term[]"></div></div>')
});
$( document ).on( "click", "#del_bu", function() {
    $( "#after_bu" ).before('<input type="hidden" name="del_list[]" value="'+$( this ).attr("iden")+'">');
 $( this ).parent().parent().fadeOut(200,function(){
    $(this).remove();
 })
});
</script>
