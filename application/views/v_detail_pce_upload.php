<? $ci=& get_instance(); ?>
<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<link rel="stylesheet" href="<?echo site_url();?>css/style.css">
<div class="container-fluid">
    <style type="text/css">
    th {
        cursor: pointer;
    }
    </style>
    <style type="text/css">
    .row-fluid .no-margin-left {
        margin-left: 0px;
    }
    
    .progress-bar-success {
        background-color: #5cb85c;
    }
    
    .progress-bar {
        float: left;
        width: 0;
        height: 100%;
        font-size: 12px;
        line-height: 20px;
        color: #fff;
        text-align: center;
        background-color: #428bca;
        -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
        -webkit-transition: width .6s ease;
        transition: width .6s ease;
    }
    </style>
    <link rel="stylesheet" href="css/jquery.fileupload.css">
    <link rel="stylesheet" href="css/style.css">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block" ng-include="project_header">
                </div>
                <!-- /block -->
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">Upload PCE#</div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <form class="form-horizontal" id="submit_form" method="post" action="<? echo site_url('project/pce_upload')?>">
                                <fieldset>
                                    <div class="span12 no-margin-left">
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">PCE#</label>
                                        <div class="controls">
                                            <input class="input-xlarge focused" id="pce_no" type="text" name="pce_no">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <span class="btn btn-success fileinput-button">
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                        <span>เลือกไฟล์ PDF</span>
                                        <!-- The file input field used as target for the file upload widget -->
                                        <input id="fileupload" type="file" name="files[]">
                                        </span>
                                        <br>
                                        <br>
                                        <!-- The global progress bar -->
                                        <div id="progress" class="progress">
                                            <div class="progress-bar progress-bar-success"></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="span12 no-margin-left" style="margin-bottom:20px;">
                                        <img src="" id="file_tmp" class="span10">
                                        <input type="hidden" id="file_path" ng-model="file_path" name="file_path">
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
$(function() {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = '<?php echo site_url('upload_handler/pdf '); ?>';
    $('#fileupload').fileupload({
            previewThumbnail: false,
            url: url,
            dataType: 'json',
            beforeSend: function() {
                $('#progress .progress-bar').css(
                    'width',
                    '10%'
                );
            },
            done: function(e, data) {
                //console.log(data);

                $.each(data.result.files, function(index, file) {
                    console.log(file);
                    if (file.error == "File is too big") {
                        $("#file_tmp").attr("alt", "ไฟล์ขนาดไหญ่เกินไป");
                        $("#file_tmp").attr("src", "");
                    }else if (file.error == "Filetype not allowed") {
                        $("#file_tmp").attr("alt", "Filetype not allowed");
                        $("#file_tmp").attr("src", "");
                    } else {
                        $("#file_tmp").attr("alt", "Upload Complete file " + file.name);
                        $("#file_path").val(file.name);
                        //$("#file_tmp").attr("src", '<?php echo site_url('media/temp '); ?>/' + file.name);
                    }
                });

            },
            progressall: function(e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            }
        }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
</script>
