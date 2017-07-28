<? $ci=& get_instance(); ?>
<style type="text/css">
.row-fluid .no-margin-left {
    margin-left: 0px;
}
hr{
    min-height: 2px;
    background-color: #CCCCCC;
    width: 100%;
}
.w-wrap{
    word-wrap: break-word;
}
</style>
<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<link rel="stylesheet" href="<?echo site_url();?>css/style.css">
<link rel="stylesheet" href="<?echo site_url();?>assets/work_sheet.css">
<div class="container-fluid">
    <? $ci=& get_instance(); ?>
    <style type="text/css">
    .row-fluid .no-margin-left {
        margin-left: 0px;
    }
    </style>
    <link rel="stylesheet" href="<?echo site_url();?>css/style.css">
    <div class="container-fluid">
        <style type="text/css">
        th {
            cursor: pointer;
        }
        
        .lowfont {
            font-size: 12px
        }
        
        .no-margin-left {
            margin-left: 0px!important;
        }
        .input-assign{
            width:60%;
            height: 60%;
        }
        .table-nopadding td{
            padding: 0px;
            vertical-align: middle;
            text-align: center;
        }
        </style>
        <div class="row-fluid">
            <div class="span12">
                <!-- block -->
                <div class="block" style="min-height:600px;">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">
                            <div class="control-group">
                                <h3><?=$project->project_name?> </h3>
                            </div>
                        </div>
                        <div class="pull-right">
                        <div class="control-group">
                            <font class="head-pull-right"><?=$project->status?> </font>
                        </div>
                        </div>
                    </div>
                    <div class="block-content collapse in" style="min-height:600px;">
                        <form id="pro_add_form" class="form-horizontal" method="post">
                            <fieldset>
                                <div class="span12 no-margin-left">
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">company</label>
                                            <p class="p-view-only"><?=$company->name?></p>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">BU</label>
                                            <p class="p-view-only"><?=$bu->bu_name?></p>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Start Date</label>
                                            <p class="p-view-only"><?=$ci->m_time->unix_to_datepicker($project->project_start)?></p>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">End Date</label>
                                            <p class="p-view-only"><?=$ci->m_time->unix_to_datepicker($project->project_end)?></p>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Internal Unit</label>
                                                <?
                                                foreach ($business_list as $key => $value) {
                                                    if ($project->business_unit_id==$value->id) {
                                                        ?>
                                                        <p class="p-view-only"><?=$value->name?></p>
                                                        <?
                                                        break;
                                                    }
                                                    
                                                }
                                                ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="span3 no-margin-left">
                                    <div class="control-group">
                                        <label class="control-label-new" for="focusedInput">Job name</label>
                                        <p class="p-view-only"><?=$project->project_name?></p>
                                        
                                    </div>
                                </div>


                                <div class="span12 no-margin-left">
                                    <h3>Work sheet</h3>
                                </div>                                
                                <div class="span12 no-margin-left">
                                    <div id="t_list" class="span6 no-margin-left">
                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                <th colspan="4" style="text-align: center;">Month -></th>
                                                </tr>
                                                <tr>
                                                <th>#</th>
                                                    <th style="width:300px;">Task</th>
                                                    <th>Note</th>
                                                    <th>Resource</th>
                                                    <th>TAH</th>
                                                </tr>
                                            </thead>
                                            <tbody id="task_table_body">

                                            </tbody>

                                        </table>                                        
                                    </div>

                                    <div id="t_carlendar" class="span6 no-margin-left work-sheet-carlendar">
                                        
                                    </div>


                                </div>
                                <div class="span6 no-margin-left">
                                    <a href="javascript:close_fancy();" class="btn btn-info">OK</a>
                                </div>
                            </fieldset>
                            
                        </form>
                        
                    </div>
                </div>
                <!-- /block -->
            </div>
        </div>
    </div>
    <!--/.fluid-container-->
    
    <script>
    /*jslint unparam: true */
    /*global window, $ */
    $(function() {        
        $( "a" ).tooltip({});
        $(".fancybox").fancybox({
            fitToView   : false,
            width       : '98%',
            height      : '98%',
            autoSize    : false,
            closeClick  : false,
            openEffect  : 'none',
            closeEffect : 'none'
        });
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy"
        });
        
        $('.datetimepicker').datetimepicker();
        $(".chzn-select").chosen({
            width: "75%"
        });
        print_task_left("<?=$project->project_id?>");
        print_task_right("<?=$project->project_id?>");
        set_valid_element_carlendar();
    });
    function close_fancy(){
        parent.$.fancybox.close();
    }
    function draw_left_right(){
        print_task_left("<?=$project->project_id?>");
        print_task_right("<?=$project->project_id?>");
    }
    function print_task_left(project_id){
        $("#task_table_body").html('<img src="<?=site_url("img/loading-circle.gif")?>" width="100%">')
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/print_work_task_left"); ?>",
                data: {
                    "project_id": project_id
                }
            })
            .done(function(data) {
                $("#task_table_body").html(data);
                set_valid_element_carlendar();
            });
    }
    function print_task_right(project_id){
        $("#t_carlendar").html('<img src="<?=site_url("img/loading-circle.gif")?>" width="100%">')
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/print_work_task_right"); ?>",
                data: {
                    "project_id": project_id
                }
            })
            .done(function(data) {
                $("#t_carlendar").html(data);
                set_valid_element_carlendar();
            });
    }
    function set_valid_element_carlendar(){
        $("#t_carlendar").height( $("#t_list").height()+20);
        $(".allow_tr_con").height($("#"+$(this).attr("iden")).height());
        for (var i = 0; i <$(".allow_tr_con").length; i++) {
            var new_height=$("#"+$(".allow_tr_con").eq(i).attr("iden")).height();
            $(".allow_tr_con").eq(i).height(new_height);
        };
        for (var i = 0; i <$(".outer_tr").length; i++) {
            var new_height=$("#"+$(".outer_tr").eq(i).attr("iden")).height();
            $(".outer_tr").eq(i).height(new_height);
        };
    }
    </script>


