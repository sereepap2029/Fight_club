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
                                <h3>Create new Project</h3>
                            </div>
                        </div>
                        <div class="pull-right">
                        </div>
                    </div>
                    <div class="block-content collapse in">
                        <form id="pro_add_form" class="form-horizontal" method="post" action="<?echo site_url('project/add_project_action');?>">
                            <fieldset>
                                <div class="span12 no-margin-left">
                                    <div class="span2">
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
                                            <input id="project_start" class="form-control datepicker" type="text" name="project_start">
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">End Date</label>
                                            <input id="project_end" class="form-control datepicker" type="text" name="project_end">
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
                                        <label class="control-label-new" for="focusedInput">Job name</label>
                                        <input id="project_name" class="form-control" type="text" name="project_name">
                                    </div>
                                </div>
                                <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Accounting Unit</label>
                                            <select id="account_unit_id" class="chzn-select" name="account_unit_id">
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
                                <div class="span3 no-margin-left">
                                    <div class="control-group">
                                        <label class="control-label-new" for="focusedInput">งานด่วน(Pre-OC)</label>
                                        <select id="pre_oc_important" class="chzn-select" name="pre_oc_important">
                                                <option value="n">No</option>
                                                <option value="y">Yes</option>
                                            </select>
                                    </div>
                                </div>    
                                


                                <div class="span12 no-margin-left">
                                    <h3>Resource Sheet</h3>
                                </div>
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                    <thead>
                                        <tr>
                                            <th >No.</th>
                                            <th >Task</th>
                                            <th >Type</th>
                                            <th >Approved Budget (HR)</th>
                                            <th class="ch-hr">Charge/Hr</th>
                                            <th class="tt-ch">Total Charge</th>
                                            <th >Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="res_t_body">
                                        
                                        <tr id="last_tr">
                                            <td></td>
                                            <td></td>
                                            <td>Grand Total</td>
                                            <td class="total"></td>
                                            <td></td>
                                            <td class="total"></td>
                                            <td>
                                            <a id="add_res" href="javascript:;" class="btn btn-success"><i class="icon-plus icon-white"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="span12 no-margin-left">
                                    <h3><a class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/pop_add_pce/nothing");?>"  ><i class="icon-plus icon-white"></i></a>PCE</h3>
                                </div>
                                <div id="pce_start" class="span12 no-margin-left">
                                    <hr>

                                </div>
                                
                                <div id="before_add_pce" class="span12 no-margin-left">                                    
                                </div>
                                <div class="span12 no-margin-left">
                                    <a href="javascript:save_n();" class="btn btn-info">Save</a>
                                    <a href="javascript:save_submit();" class="btn btn-info">Submit</a>
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
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy"
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
    $(document).on("change", ".type_change", function() {
        var cur_row=$(this).parent().parent();
        var cur_select=cur_row.find("select.type_change");
        h_rate=parseInt(cur_select.find("option#"+cur_select.val()).attr("data"));
        a_buged=parseInt(cur_row.find("input.type_change").val());
        var chagre_hr=cur_row.find("td").eq(4);
        var chagre_total=cur_row.find("td").eq(5);
        chagre_hr.html(h_rate+"");
        chagre_total.html((h_rate*a_buged)+"");
        cal_rSheet();

    });
    function cal_rSheet(){
        var all_row=$("#res_t_body").find("tr");
        var sum_h=0;
        var sum_p=0;
        for (var i = 0; i <all_row.length-1; i++) {
            var cur_coll=all_row.eq(i).find("td");
            //cur_coll.eq(0).html((i+1)+"");
            h_p=parseInt(cur_coll.eq(5).html());
            hr=parseInt(cur_coll.eq(3).find("input.type_change").val());
            sum_h+=hr;
            sum_p+=h_p;
            //console.log(sum_h+" -"+sum_p );
        };
        var last_row=all_row.eq(all_row.length-1).find("td");
        last_row.eq(3).html(""+sum_h);
        last_row.eq(5).html(""+sum_p);

    }

    $(document).on("change", ".out_change", function() {
        var cur_row=$(this).parent().parent();
        var cur_cost=cur_row.find("input.out_change").eq(0);
        var cur_charge=cur_row.find("input.out_change").eq(1);
        var cost=parseInt(cur_cost.val());
        var charge=parseInt(cur_charge.val());
        var margin=(charge-cost)/charge*100;
        cur_row.find(".out_margin").html(""+margin.toFixed(2)+"%");
        var id=cur_row.attr("iden");
        cal_outSource(id);

    });
    /*function cal_outSource(id){
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
        last_row.eq(3).html(""+sum_cost);
        last_row.eq(4).html(""+sum_charge);
        var margin=(sum_charge-sum_cost)/sum_cost*100;
        last_row.eq(5).html(""+margin.toFixed(2)+"%");

    }*/
    $(document).on("click", "#add_res", function() {
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/ajax_add_res_html"); ?>",
                data: {
                    no: "no"
                }
            })
            .done(function(data) {
                $("#last_tr").before(data);
            });
        
    });
    $(document).on("click", ".add_out_list", function() {
        var id=$(this).attr("iden");
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("project/ajax_add_outlist_html"); ?>",
                data: {
                    "cur_time": id
                }
            })
            .done(function(data) {
                $("#be_out_"+id).before(data);
            });
        
    });
    $(document).on("click", "#add_pce", function() {
        var pce_no=$("#pce_no").val();
        var pce_des=$("#pce_des").val();
        var pce_amount=$("#pce_amount").val();
        var pce_file=$("#temp_f_name").val();
        if (pce_no==""||pce_des==""||pce_amount==""||pce_file=="") {
            alert("กรอกข้อมูลให้ครบทุกช่อง พร้อม upload file");
        }else{
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
                    $("#before_add_pce").before(data);
                    $("#pce_no").val("");
                    $("#pce_des").val("");
                    $("#pce_amount").val("");
                    $("#temp_f_name").val("");
                    $('#progress .progress-bar').css(
                            'width',
                            '0%'
                        );
                });
        }
        
    });
    $(document).on("click", ".del_res", function() {
        $(this).parent().parent().fadeOut(300,function(){
            $(this).remove();
        });
    });
    $(document).on("click", ".del_outlist", function() {
        $(this).parent().parent().fadeOut(300,function(){
            $(this).remove();
        });
    });
    $(document).on("click", ".pce_delete", function() {
        if (confirm("Confirm del")) {
            var id=$(this).attr("iden");
            $("#del-but_"+id).fadeOut(300,function(){
                $(this).remove();
            });
            $("#outsource-but_"+id).fadeOut(300,function(){
                $(this).remove();
            });
            $("#pce_cur_"+id).fadeOut(300,function(){
                $(this).remove();
            });
            $("#outsource_"+id).fadeOut(300,function(){
                $(this).remove();
            });
            $("#hr_"+id).fadeOut(300,function(){
                $(this).remove();
            });
        };
    });
    $(document).on("click", ".toggle_outsource", function() {
        var id=$(this).attr("iden");
        if ($(this).attr("togStat")=="hide") {
            $("#outsource_"+id).show("slow");
            $(this).attr("togStat","show");
        }else{
            
            $("#outsource_"+id).slideUp();
            $(this).attr("togStat","hide");
        }

    });
function save_n(){
    var company_id=$("#company_id").val();
        var bu_id=$("#bu_id").val();
        var project_start=$("#project_start").val();
        var project_end=$("#project_end").val();
        var business_unit_id=$("#business_unit_id").val();
        var project_name=$("#project_name").val();
        var approve_budget=$("input[name='approve_budget[]']");
        var task=$("input[name='task[]']");
        var ap_flag=false;
        var task_flag=false;
        for (var i = 0; i < approve_budget.length; i++) {
            ap_val=approve_budget.eq(i).val();
            if (ap_val==""||!$.isNumeric( ap_val)) {
                ap_flag=true;
            };
        };
        for (var i = 0; i < task.length; i++) {
            ap_val=task.eq(i).val();
            if (ap_val=="") {
                task_flag=true;
            };
        };
        if (company_id=="no"||bu_id=="no"||project_start==""||project_end==""||business_unit_id=="no"||task_flag||ap_flag) {

            alert("กรอกข้อมูลให้ครบทุกช่อง");
            if (task_flag) {
                alert("Task ไม่ถูกต้อง")
            };
            if (ap_flag) {
                alert("Approved Budget ไม่ถูกต้อง")
            };

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
        var approve_budget=$("input[name='approve_budget[]']");
        var task=$("input[name='task[]']");
        var ap_flag=false;
        var task_flag=false;
        for (var i = 0; i < approve_budget.length; i++) {
            ap_val=approve_budget.eq(i).val();
            if (ap_val==""||!$.isNumeric( ap_val)) {
                ap_flag=true;
            };
        };
        for (var i = 0; i < task.length; i++) {
            ap_val=task.eq(i).val();
            if (ap_val=="") {
                task_flag=true;
            };
        };
        if (company_id=="no"||bu_id=="no"||project_start==""||project_end==""||business_unit_id=="no"||task_flag||ap_flag) {

            alert("กรอกข้อมูลให้ครบทุกช่อง");
            if (task_flag) {
                alert("Task ไม่ถูกต้อง")
            };
            if (ap_flag) {
                alert("Approved Budget ไม่ถูกต้อง")
            };

        }else{
            $("#pro_add_form").append('<input type="hidden" name="submit_job" value="yes">');
            document.getElementById("pro_add_form").submit();
        }
    
}
    </script>

