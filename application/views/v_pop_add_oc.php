<? $ci=& get_instance(); 
?>
    <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
    <script src="<?echo site_url();?>js/upload/vendor/jquery.ui.widget.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="<?echo site_url();?>js/upload/jquery.iframe-transport.js"></script>
    <!-- The basic File Upload plugin -->
    <script src="<?echo site_url();?>js/upload/jquery.fileupload.js"></script>
    <link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
    <link rel="stylesheet" href="<?echo site_url();?>css/style.css">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12" id="content">
                <div class="row-fluid">
                    <!-- block -->
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left"> Add OC#</div>
                        </div>
                        <div class="block-content collapse in">
                            <div class="span3 no-margin-left">
                            </div>
                            <div id="before_add_oc" class="span6 no-margin-left">
                                <table class="table table-noborder">
                                    <tr>
                                        <td class="first-ta">OC/IOC#</td>
                                        <td>
                                            <input class="form-control" type="text" id="oc_no" style="max-width:90%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-ta">
                                        </td>
                                        <td>
                                            <div id="progress_oc" class="progress" style="min-width:50%;max-width:70%;display:inline-block">
                                                <div class="progress-bar progress-bar-success"><font id="oc_tmp_file_name" style="color:#FFFFFF"></font></div>
                                            </div>&nbsp;&nbsp;
                                            <div class="control-group" style="display:inline-block">
                                                <span class="btn btn-success fileinput-button">
                                                            <i class="glyphicon glyphicon-plus"></i>
                                                            <span>OC#</span>
                                                <!-- The file input field used as target for the file upload widget -->
                                                <input id="fileupload_oc" type="file" name="files">
                                                <input id="oc_temp_f_name" type="hidden">
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-ta">
                                        </td>
                                        <td>
                                            <div id="progress_pce_final" class="progress" style="min-width:50%;max-width:70%;display:inline-block">
                                                <div class="progress-bar progress-bar-success"><font id="pce_tmp_file_name" style="color:#FFFFFF"></font></div>
                                            </div>&nbsp;&nbsp;
                                            <div class="control-group" style="display:inline-block">
                                                <span class="btn btn-success fileinput-button">
                                                                <i class="glyphicon glyphicon-plus"></i>
                                                                <span>Client Approved PCE</span>
                                                <!-- The file input field used as target for the file upload widget -->
                                                <input id="fileupload_pce_final" type="file" name="files">
                                                <input id="oc_pce_temp_f_name" type="hidden">
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-ta">
                                            PCE#
                                        </td>
                                        <td>
                                            <?=$pce_doc->pce_no?>
                                                <input type="hidden" name="oc_pce" id="oc_pce" value="<?=$pce_doc->id?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-ta">
                                            Description
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="oc_des" style="max-width:90%" value="<?=$pce_doc->pce_des?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-ta">
                                            Amount
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="oc_amount" style="max-width:90%" value="<?=$pce_doc->pce_amount?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align:center">
                                            <a id="add_oc" href="javascript:;" class="btn btn-success">Add</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="span3 no-margin-left">
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
        $(document).on("click", "#add_oc", function() {
            var oc_no = $("#oc_no").val();
            var oc_des = $("#oc_des").val();
            var oc_amount = $("#oc_amount").val();
            var oc_pce = $("#oc_pce").val();
            var oc_temp_f_name = $("#oc_temp_f_name").val();
            var oc_pce_temp_f_name = $("#oc_pce_temp_f_name").val();
            if (oc_no == "" || oc_des == "" || oc_amount == "" || oc_temp_f_name == "" || oc_pce_temp_f_name == "" || oc_pce == "no") {
                alert("กรอกข้อมูลให้ครบทุกช่อง พร้อม upload file");
            } else {
                $.ajax({
                        method: "POST",
                        url: "<?php echo site_url("project/ajax_add_oc_html"); ?>",
                        data: {
                            "oc_no": oc_no,
                            "oc_des": oc_des,
                            "oc_amount": oc_amount,
                            "oc_pce": oc_pce,
                            "oc_file": oc_temp_f_name,
                            "oc_pce_file": oc_pce_temp_f_name
                        }
                    })
                    .done(function(data) {
                        parent.$("#before_add_oc_but_" + oc_pce).before(data);
                        parent.$("#is_oc_change").val("y");
                        parent.$.fancybox.close();
                    });
            }

        });
        $(function() {
            'use strict';
            // for OC
            var url = '<?php echo site_url('upload_handler/pdf'); ?>';
            $('#fileupload_oc').fileupload({
                    previewThumbnail: false,
                    url: url,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#progress_oc .progress-bar').css(
                            'width',
                            '10%'
                        );
                    },
                    done: function(e, data) {
                        //console.log(data);

                        $.each(data.result.files, function(index, file) {
                            //console.log(file);
                            $("#oc_tmp_file_name").html(file.name)
                            if (file.error == "File is too big") {
                                alert("File is too big exceed 100 MB");
                                $("#oc_temp_f_name").val("");
                                $('#progress_oc .progress-bar').css(
                                    'width',
                                    '0%'
                                );
                            } else if (file.error == "Filetype not allowed") {
                                alert("Filetype not allowed");
                                $("#oc_temp_f_name").val("");
                                $('#progress_oc .progress-bar').css(
                                    'width',
                                    '0%'
                                );
                            } else {
                                alert("Upload Complete file " + file.name);
                                $("#oc_temp_f_name").val(file.name);
                            }
                        });

                    },
                    progressall: function(e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#progress_oc .progress-bar').css(
                            'width',
                            progress + '%'
                        );
                    }
                }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');

        });

        $(function() {
            'use strict';
            // for OC
            var url = '<?php echo site_url('upload_handler/pdf'); ?>';
            $('#fileupload_pce_final').fileupload({
                    previewThumbnail: false,
                    url: url,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#progress_pce_final .progress-bar').css(
                            'width',
                            '10%'
                        );
                    },
                    done: function(e, data) {
                        //console.log(data);

                        $.each(data.result.files, function(index, file) {
                            //console.log(file);
                            $("#pce_tmp_file_name").html(file.name)
                            if (file.error == "File is too big") {
                                alert("File is too big exceed 100 MB");
                                $("#oc_pce_temp_f_name").val("");
                                $('#progress_pce_final .progress-bar').css(
                                    'width',
                                    '0%'
                                );
                            } else if (file.error == "Filetype not allowed") {
                                alert("Filetype not allowed");
                                $("#oc_pce_temp_f_name").val("");
                                $('#progress_pce_final .progress-bar').css(
                                    'width',
                                    '0%'
                                );
                            } else {
                                alert("Upload Complete file " + file.name);
                                $("#oc_pce_temp_f_name").val(file.name);
                            }
                        });

                    },
                    progressall: function(e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#progress_pce_final .progress-bar').css(
                            'width',
                            progress + '%'
                        );
                    }
                }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');

        });
        </script>
