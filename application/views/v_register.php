<? $ci=& get_instance(); ?>
<!DOCTYPE html>
<html class="no-js">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Home Page</title>
    <!-- Bootstrap -->
    <link href="<?echo site_url();?>bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/DT_bootstrap.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/jquery.mCustomScrollbar.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/css/jquery-ui.css" rel="stylesheet" />
    <link href="<?echo site_url();?>assets/css/jquery.datetimepicker.css" rel="stylesheet" />
    <link href="<?echo site_url();?>css/jquery.fancybox.css" rel="stylesheet" />
    <link href="<?echo site_url();?>assets/styles.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/styles_atom.css" rel="stylesheet" media="screen">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    <script src="<?echo site_url();?>vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    <script src="<?echo site_url();?>assets/js/jquery-1.10.2.js"></script>
    <script src="<?echo site_url();?>assets/js/jquery-ui.js"></script>
    <script src="<?echo site_url();?>assets/js/jquery.datetimepicker.js"></script>
    <script src="<?echo site_url();?>js/jquery.mCustomScrollbar.js"></script>
    <script src="<?echo site_url();?>js/jquery.fancybox.js"></script>
    <link href="<?echo site_url();?>vendors/chosen.min.css" rel="stylesheet" media="screen">
    <script src="<?echo site_url();?>vendors/chosen.jquery.min.js"></script>
    <script type="text/javascript">
    /**
     * Number.prototype.format(n, x, s, c)
     * 
     * @param integer n: length of decimal
     * @param integer x: length of whole part
     * @param mixed   s: sections delimiter
     * @param mixed   c: decimal delimiter
     */
    Number.prototype.format = function(n, x, s, c) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = this.toFixed(Math.max(0, ~~n));

        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
    };
    function cal_outSource(id){
        var all_row=$("#table_out_"+id).find("tr");
        var sum_cost=0;
        var sum_charge=0;
        console.log(" -"+id );
        for (var i = 0; i <all_row.length-1; i++) {
            var cur_coll=all_row.eq(i).find("td");
            cost=parseInt(cur_coll.eq(3).find("input.out_change").val());
            charge=parseInt(cur_coll.eq(4).find("input.out_change").val());
            sum_cost+=cost;
            sum_charge+=charge;
            console.log(" -"+id );
        };
        var last_row=all_row.eq(all_row.length-1).find("td");
        last_row.eq(3).html(""+sum_cost.format(2, 3, ',', '.'));
        last_row.eq(4).html(""+sum_charge.format(2, 3, ',', '.'));
        var margin=(sum_charge-sum_cost)/sum_charge*100;
        last_row.eq(5).html(""+margin.format(2, 3, ',', '.')+"%");

        $("#out_but_"+id).html("Out source("+sum_cost.format(2, 3, ',', '.')+")");

    }
    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    function get_int_for_gif(){
        return getRandomInt(1, 13);
    }
    </script>
    <style type="text/css">
    /*.navbar-inner div{
            background-color:#3366FF;
            color: #FFFFFF;
        }*/
    
    .white-nav-bar {
        background-image: -moz-linear-gradient(top, #fff, #f2f2f2);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#fff), to(#f2f2f2));
        background-image: -webkit-linear-gradient(top, #fff, #f2f2f2);
        background-image: -o-linear-gradient(top, #fff, #f2f2f2);
        background-image: linear-gradient(to bottom, #fff, #f2f2f2);
    }
    </style>
    <link rel="stylesheet" href="<?echo site_url();?>css/style.css">
</head>
<body>
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
                        <div class="muted pull-left">สมัคร </div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <h5> <?if (isset($err_msg)) {
                                        echo "*******".$err_msg."*******";
                                    }?></h5>
                            <form class="form-horizontal" method="post" action="<? echo site_url('main/register');?>">
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
                                        <label class="control-label" for="role">Class</label>
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
                                        <a href="<?=site_url()?>" class="btn">ยกเลิก</a>
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
