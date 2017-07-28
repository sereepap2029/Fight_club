<? $ci=& get_instance(); ?>
<style type="text/css">
.row-fluid .no-margin-left {
    margin-left: 0px;
}
</style>
<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<link rel="stylesheet" href="<?echo site_url();?>css/style.css">
<?/*
<script src="<?echo site_url();?>js/angular_controller/app.js"></script> 
<script src="<?echo site_url();?>js/angular_controller/admin_controller.js"></script> 
<div class="container-fluid" ng-app="adminApp">
    <div class="row-fluid" ng-controller="swap_hour_rate">*/?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">Add admin Account </div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <h5> <?if (isset($err_msg)) {
                                        echo "*******".$err_msg."*******";
                                    }?></h5>
                            <form class="form-horizontal" method="post" action="<? if(isset($edit)){echo site_url('admin/user_edit/'.$user->username);}else{echo site_url('admin/user_add');}?>">
                                <fieldset>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Username</label>
                                        <div class="controls">
                                            <input type="hidden" id="init_hour" link="<?echo site_url('admin/hour_rate_ajax');?>">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="username" type="text" name="username" link="">
                                            <? }else{ ?>
                                            <input class="focused" id="username" type="text" link="<?echo site_url('admin/hour_rate_ajax');?>" name="username" value="<?echo $user->username;?>" disabled>
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Password</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="password">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="password" value="<?echo $user->password;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Confirm Password</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="confirm_password">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="confirm_password" value="<?echo $user->password;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">ชื่อ</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="firstname">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="firstname" value="<?echo $user->firstname;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">นามสกุล</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="lastname">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="lastname" value="<?echo $user->lastname;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">ชื่อเล่น</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="nickname">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="nickname" value="<?echo $user->nickname;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">น้ำหนัก</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="weight">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="weight" value="<?echo $user->weight;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">เบอร์โทร</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="" type="text" name="phone">
                                            <? }else{ ?>
                                            <input class="focused" id="" type="text" name="phone" value="<?echo $user->phone;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="role">Style</label>
                                        <div class="controls">
                                            <select id="position" class="chzn-select" name="position">
                                                <option value="no">-----please select-----</option>
                                                <? foreach ($position_list as $key=> $value) { ?>
                                                <option value="<?=$value->id?>"><?=$value->department->bu->name." : ".$value->department->name." : ".$value->name?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <? if (isset($edit)) { 
                                            if ($user->position==""||$user->position==null) {
                                                $user->position="no";
                                            }
                                            ?>
                                        <script type="text/javascript">
                                        $("#position").val("<?echo $user->position;?>")
                                        </script>
                                        <? } ?>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Join Date</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused datepicker" id="" type="text" name="join_date" value="<?echo $ci->m_time->unix_to_datepicker(time());?>">
                                            <? }else{ ?>
                                            <input class="focused datepicker" id="" type="text" name="join_date" value="<?echo $ci->m_time->unix_to_datepicker($user->join_date);?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label" for="role">พี่เลี้ยง</label>
                                        <div class="controls">
                                            <select id="supervisor" class="chzn-select" name="supervisor">
                                                <option value="no">-----please select-----</option>
                                                <? foreach ($user_list as $key=> $value) { ?>
                                                <option value="<?=$value->username?>"><?=$value->firstname." ".$value->lastname." ".$value->nickname?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <? if (isset($edit)) {
                                            if ($user->supervisor==""||$user->supervisor==null) {
                                                $user->supervisor="no";
                                            }
                                         ?>
                                        <script type="text/javascript">
                                        $("#supervisor").val("<?echo $user->supervisor;?>")
                                        </script>
                                        <? } ?>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="role">Role</label>
                                        <div class="controls">
                                            <select id="g_prem_id" class="chzn-select" name="g_prem_id">
                                                <option value="no">-----please select-----</option>
                                                <? foreach ($group_list as $key=> $value) { ?>
                                                <option value="<?=$value->g_id?>"><?=$value->g_name?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <? if (isset($edit)) { 
                                            if ($user->g_prem_id==""||$user->g_prem_id==null) {
                                                $user->g_prem_id="no";
                                            }
                                            ?>
                                        <script type="text/javascript">
                                        $("#g_prem_id").val("<?echo $user->g_prem_id;?>")
                                        </script>
                                        <? } ?>
                                    </div>







                                                







                                    <div class="control-group">
                                        <span class="btn btn-success fileinput-button">
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                        <span>เลือกไฟล์</span>
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
                                    <?
                                    if (isset($edit)) {
                                      ?>
                                      <img src="<?php echo site_url('media/sign_photo/'.$user->sign_filename); ?>" id="file_tmp" class="span4">
                                      <?
                                    }else{
                                      ?>
                                      <img src="" id="file_tmp" class="span4">
                                      <?
                                    }
                                    ?>
                                        
                                        <input type="hidden" id="file_path" name="file_path">
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
        changeYear: true
    });
    $('.datetimepicker').datetimepicker();
    $(".chzn-select").chosen({
        width: "75%"
    });
});
$(function() {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = '<?php echo site_url('upload_handler/attachment '); ?>';
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
                    //console.log(file);
                    if (file.error == "File is too big") {
                        $("#file_tmp").attr("alt", "ไฟล์ขนาดไหญ่เกินไป");
                        $("#file_tmp").attr("src", "");
                    } else {
                        $("#file_tmp").attr("alt", "Upload Complete file " + file.name);
                        $("#file_path").val(file.name);
                        $("#file_tmp").attr("src", '<?php echo site_url('media/temp '); ?>/' + file.name);
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
