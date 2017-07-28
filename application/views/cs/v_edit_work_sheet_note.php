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
                                            <textarea id="comment" name="comment" style="min-width:600px;min-height:200px;"><?=htmlspecialchars($work->comment)?></textarea>
                                        </div>
                                    </div>
                                </div>           
                                <div class="control-group">
                                        <span class="btn btn-success fileinput-button">
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                        <span>เลือกไฟล์</span>
                                        <!-- The file input field used as target for the file upload widget -->
                                        <input id="fileupload" type="file" name="files[]" multiple>
                                        </span>
                                        <br>
                                        <br>
                                        <!-- The global progress bar -->
                                        <div id="progress" class="progress">
                                            <div class="progress-bar progress-bar-success"></div>
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
                                        <a href="javascript:;" id-dat="<?=$value->id?>" class="btn btn-danger del_img_but"><i class="icon-remove icon-white"></i></a>
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

$(function() {
    $(".datepicker").datepicker();
    $( "#img_hold" ).sortable({
          update: function( event, ui ) {
            var photo_id_list=$("input[name='photo_id_list[]']").serialize()
            $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/sort_task_photo"); ?>",
                data: "save=yes&"+photo_id_list
            })
            .done(function(data) {
              if (data['flag']=="OK") {
              }else{
                alert("error happen");
                console.log(data)
              }
            });
          },
          placeholder: "ui-state-highlight"
        });

});
$(document).ready(function() {
    $(".fancybox").fancybox({
        openEffect  : 'none',
        closeEffect : 'none'
    });
});
function close_fancy() {
  var comment=$("#comment").val();
  $(".close_but").html("saving.....!!");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("cs/update_task_note"); ?>",
                data: {
                    "work_id": '<?=$work_id?>',
                    "dat": comment,
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    parent.$.fancybox.close();
                    
                }else{
                    alert(data['flag']);
                }
            });    
    
}
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
                        alert("ไฟล์ขนาดไหญ่เกินไป");
                    } else {
                        $.ajax({
                            method: "POST",
                            url: "<?php echo site_url("cs/insert_task_Photo"); ?>",
                            data: {
                                "file_path": file.name,
                                "work_id": '<?=$work_id?>',
                            }
                        })
                        .done(function(data) {
                            if (data['flag']=="OK") {
                                $("#img_hold").append(data['html']);
                            }else{
                                alert(data['flag']);
                            }
                        });    
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
$(document).on("click", ".del_img_but", function() {
    var cur_ele=$(this);
    if (confirm("Confirm del")) {    
                    $.ajax({
                            method: "POST",
                            url: "<?php echo site_url("cs/del_task_photo"); ?>",
                            data: {
                                "photo_id": cur_ele.attr("id-dat"),
                            }
                        })
                        .done(function(data) {
                            if (data['flag']=="OK") {
                                $("#"+cur_ele.attr("id-dat")).fadeOut(500,function(){
                                    $(this).remove();
                                })
                            }else{
                                alert(data['flag']);
                            }
                        }); 
                    };
});
</script>
