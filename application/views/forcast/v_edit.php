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
</style>
<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<link rel="stylesheet" href="<?echo site_url();?>css/style.css">
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
    <script src="<?echo site_url();?>js/upload/vendor/jquery.ui.widget.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="<?echo site_url();?>js/upload/jquery.iframe-transport.js"></script>
    <!-- The basic File Upload plugin -->
    <script src="<?echo site_url();?>js/upload/jquery.fileupload.js"></script>
<div class="container-fluid">
    <? $ci=& get_instance(); ?>
    <style type="text/css">
    .row-fluid .no-margin-left {
        margin-left: 0px;
    }
    </style>
    <link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
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
        </style>
        <div class="row-fluid">
            <div class="span12">
                <!-- block -->
                <div class="block">
                    <style type="text/css">
                    td[ng-click] {
                        cursor: pointer;
                    }
                    </style>
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">
                            <div class="control-group">
                                <h3>Edit Forcast</h3>
                            </div>
                        </div>
                        <div class="pull-right">
                        </div>
                    </div>
                    <div class="block-content collapse in" style="min-height:600px;">
                        <form id="pro_add_form" class="form-horizontal" method="post" action="<?echo site_url('forcast/edit');?>">
                            <fieldset>
                                <div class="span12 no-margin-left">
                                    <div class="span2">
                                    <input id="project_id" class="" type="hidden" name="project_id" value="<?=$dat->project_id?>">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Client</label>
                                            <select id="company_id" class="chzn-select" name="project_client">
                                                <option value="no">---please select ------</option>
                                                <?
                                                foreach ($company as $key => $value) {
                                                    ?>
                                                    <option value="<?=$value->id?>"><?=$value->name?></option>
                                                    <?
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="span3 no-margin-left">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">BU</label>
                                            <select id="bu_id" name="project_bu">
                                                <option value="no">please select company</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="span2 no-margin-left">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Start Date</label>
                                            <input id="project_start" class="form-control datepicker" type="text" name="project_start" value="<?=$ci->m_time->unix_to_datepicker($dat->project_start)?>">
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">End Date</label>
                                            <input id="project_end" class="form-control datepicker" type="text" name="project_end" value="<?=$ci->m_time->unix_to_datepicker($dat->project_end)?>">
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Business Unit</label>
                                            <select id="business_unit_id" class="chzn-select" name="business_unit_id">
                                                <option value="no">---please select ------</option>
                                                <?
                                                foreach ($business_list as $key => $value) {
                                                    ?>
                                                    <option value="<?=$value->id?>"><?=$value->name?></option>
                                                    <?
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="span3 no-margin-left">
                                    <div class="control-group">
                                        <label class="control-label-new" for="focusedInput">Project name</label>
                                        <input id="project_name" class="form-control" type="text" name="project_name" value="<?=$dat->project_name?>">
                                    </div>
                                </div>                              
                                <div class="span3">
                                    <div class="control-group">
                                        <label class="control-label-new" for="focusedInput">Project Value</label>
                                        <input id="project_value" class="form-control" type="text" name="project_value" value="<?=$dat->project_value?>">
                                    </div>
                                </div>   
                                <div class="span3">
                                    <div class="control-group">
                                        <label class="control-label-new" for="focusedInput">Estimated Outsource</label>
                                        <input id="outsource_value" class="form-control" type="text" name="outsource_value" value="<?=$dat->outsource_value?>">
                                    </div>
                                </div>   


                                
                                <div class="span12 no-margin-left">
                                    <a href="javascript:save_n();" class="btn btn-info">Save</a>
                                    <a href="javascript:save_submit();" class="btn btn-info">Submit</a>
                                    <a href="javascript:convert_to_project();" class="btn btn-danger">Convert to Project</a>
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
        $(".fancybox").fancybox({
            fitToView   : false,
            width       : '90%',
            height      : '90%',
            autoSize    : false,
            closeClick  : false,
            openEffect  : 'none',
            closeEffect : 'none'
        });
        $( "a" ).tooltip({});
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy"
        });
        $("#company_id").val("<?=$dat->project_client?>");
        $("#business_unit_id").val("<?=$dat->business_unit_id?>");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/ajax_bu_html"); ?>",
                data: {
                    company_id: $("#company_id").val()
                }
            })
            .done(function(data) {
                $("#bu_id").html(data);
                $("#bu_id").val("<?=$dat->project_bu?>"); 
            });

        $('.datetimepicker').datetimepicker();
        $(".chzn-select").chosen({
            width: "75%"
        });
    });
    $(document).on("change", "#company_id", function() {
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/ajax_bu_html"); ?>",
                data: {
                    company_id: $("#company_id").val()
                }
            })
            .done(function(data) {
                $("#bu_id").html(data);
            });
    });
function save_n(){
    var company_id=$("#company_id").val();
        var bu_id=$("#bu_id").val();
        var project_start=$("#project_start").val();
        var project_end=$("#project_end").val();
        var business_unit_id=$("#business_unit_id").val();
        var project_name=$("#project_name").val();
        if (project_name==""||company_id=="no"||bu_id=="no"||project_start==""||project_end==""||business_unit_id=="no") {

            alert("กรอกข้อมูลให้ครบทุกช่อง");

        }else{
            document.getElementById("pro_add_form").submit();
        }
    
}
function save_submit(){
    var company_id=$("#company_id").val();
        var bu_id=$("#bu_id").val();
        var project_start=$("#project_start").val();
        var project_end=$("#project_end").val();
        var business_unit_id=$("#business_unit_id").val();
        var project_name=$("#project_name").val();
        if (project_name==""||company_id=="no"||bu_id=="no"||project_start==""||project_end==""||business_unit_id=="no") {

            alert("กรอกข้อมูลให้ครบทุกช่อง");
        }else{
            $("#pro_add_form").append('<input type="hidden" name="submit_job" value="yes">');
            document.getElementById("pro_add_form").submit();
        }
    
}
function convert_to_project(){
    if (confirm("เปลี่ยน Forcast นี้ให้กลายเป็น Project ??")) {
            window.open("<?=site_url('forcast/forcast_to_project/'.$dat->project_id)?>","_self")
        }
    
}
    </script>

