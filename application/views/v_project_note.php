<? $ci=& get_instance(); ?>

<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<style type="text/css">
    .img-region{
            margin: 5px;
            padding: 10px;
            display: block;
            max-width: 100%;
            position: relative;
    }
    .img-region .del_img_but{
            position: absolute;
            right: -5px;
            top: -5px;
    }
    .ui-state-highlight{
        margin: 5px;
        padding: 10px;
        display: block;
        width: 20px;
        height: 20px;
        position: relative;
    }
</style>
<style type="text/css">
    .finan_note{
        min-height: 40px;
        padding-right: 20px;
        padding-left: 20px;
        background-color: #fafafa;
        background-image: -moz-linear-gradient(top,#F18383,#DA0B0B);
        background-image: -webkit-gradient(linear,0 0,0 100%,from(#F18383),to(#DA0B0B));
        background-image: -webkit-linear-gradient(top,#F18383,#DA0B0B);
        background-image: -o-linear-gradient(top,#F18383,#DA0B0B);
        background-image: linear-gradient(to bottom,#F18383,#DA0B0B);
        background-repeat: repeat-x;
        border: 1px solid #d4d4d4;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff',endColorstr='#fff2f2f2',GradientType=0);
        -webkit-box-shadow: 0 1px 4px rgba(0,0,0,0.065);
        -moz-box-shadow: 0 1px 4px rgba(0,0,0,0.065);
        box-shadow: 0 1px 4px rgba(0,0,0,0.065);
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header <?=$func_note?>">
                        <div class="muted pull-left"><?=$note_name?></div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12">
                            <div class="control-group">
                                        <label class="control-label" for="focusedInput"></label>
                                        <div class="controls">
                                           <textarea class="span12" rows="5" id="comment"><?=htmlspecialchars ( $project->{$func_note})?></textarea>
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
                                 foreach ($project_attachment as $key => $value) {
                                     ?>
                                     <div class="img-region" id="<?=$value->id?>">
                                        <a href="<?=site_url("media/project_attachment/".$value->filename)?>" title="" target="_blank">
                                           <?=$value->origin_filename?>
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
<script type="text/javascript">
function close_fancy() {
  $("#close_but").html("saving.....!!");
  var dat=$("#comment").val();
  $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/".$func_note."/".$project->project_id); ?>",
                data:{
                        "dat": dat,
                    }
            })
            .done(function(data) {
              if (data['flag']=="OK") {
                parent.$.fancybox.close();
              }else{
                alert("error happen");
                console.log(data)
              }
            });
    
}
</script>

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
                url: "<?php echo site_url("project/sort_attachment_file"); ?>",
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
            },
            done: function(e, data) {
                //console.log(data);

                $.each(data.result.files, function(index, file) {
                    //console.log(file);
                    if (file.error != ""&&file.error != undefined) {
                        alert(file.error);
                    } else {
                        $.ajax({
                            method: "POST",
                            url: "<?php echo site_url("project/add_project_attachment_file"); ?>",
                            data: {
                                "file_path": file.name,
                                "project_id": '<?=$project->project_id?>',
                                "type": '<?=$a_type?>',
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
                            url: "<?php echo site_url("project/del_attachment_file"); ?>",
                            data: {
                                "file_id": cur_ele.attr("id-dat"),
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
