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
                            <div class="muted pull-left"> Add PCE#</div>
                        </div>
                        <div class="block-content collapse in">
                            <div class="span3 no-margin-left">
                            </div>
                            <div id="before_add_pce" class="span6 no-margin-left">
                                <table class="table table-noborder">
                                    <tr>
                                        <td class="first-ta">PCE#</td>
                                        <td>
                                            <input class="form-control" type="text" id="pce_no" style="max-width:90%">
                                            <input type="hidden" id="f_path">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-ta">
                                        </td>
                                        <td>
                                            <div id="progress" class="progress" style="min-width:50%;max-width:70%;display:inline-block">
                                                <div class="progress-bar progress-bar-success"><font id="pce_tmp_file_name" style="color:#FFFFFF"></font></div>
                                            </div>&nbsp;&nbsp;
                                            <div class="control-group" style="display:inline-block">
                                                <span class="btn btn-success fileinput-button">
                                                            <i class="glyphicon glyphicon-plus"></i>
                                                            <span>เลือกไฟล์ PDF</span>
                                                <!-- The file input field used as target for the file upload widget -->
                                                <input id="fileupload" type="file" name="files">
                                                <input id="temp_f_name" type="hidden">
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-ta">
                                            Description
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="pce_des" style="max-width:90%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="first-ta">
                                            Amount
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="pce_amount" style="max-width:90%">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align:center">
                                            <a id="add_pce" href="javascript:;" class="btn btn-success">Add</a>
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
        $(document).on("click", "#add_pce", function() {
            var pce_no = $("#pce_no").val();
            var pce_des = $("#pce_des").val();
            var pce_amount = $("#pce_amount").val();
            var pce_file = $("#temp_f_name").val();
            if (pce_no == "" || pce_des == "" || pce_amount == "" || pce_file == "") {
                alert("กรอกข้อมูลให้ครบทุกช่อง พร้อม upload file");
            } else {
                $.ajax({
                        method: "POST",
                        url: "<?php echo site_url("project/ajax_add_pce_html"); ?>",
                        data: {
                            "pce_no": pce_no,
                            "pce_des": pce_des,
                            "pce_amount": pce_amount,
                            "pce_file": pce_file
                        }
                    })
                    .done(function(data) {
                        parent.$("#pce_start").after(data);
                        parent.$.fancybox.close();
                    });
            }

        });
        $(function() {
            'use strict';
            // Change this to the location of your server-side upload handler:
            var url = '<?php echo site_url('upload_handler/pdf'); ?>';
            $('#fileupload').fileupload({
                    previewThumbnail: false,
                    url: url,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#progress .progress-bar').css(
                            'width',
                            '10%'
                        );
                        //$("#pce_tmp_file_name").html($('#fileupload').val())
                    },
                    done: function(e, data) {
                        //console.log(data);

                        $.each(data.result.files, function(index, file) {
                            //console.log(file);
                            $("#pce_tmp_file_name").html(file.name)
                            if (file.error == "File is too big") {
                                alert("File is too big exceed 100 MB");
                                $("#temp_f_name").val("");
                                $('#progress .progress-bar').css(
                                    'width',
                                    '0%'
                                );
                            } else if (file.error == "Filetype not allowed") {
                                alert("Filetype not allowed");
                                $("#temp_f_name").val("");
                                $('#progress .progress-bar').css(
                                    'width',
                                    '0%'
                                );
                            } else {
                                alert("Upload Complete file " + file.name);
                                $("#temp_f_name").val(file.name);
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
