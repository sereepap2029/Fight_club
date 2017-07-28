<? $ci=& get_instance(); 
$prem_flag=(isset($user_data->prem['csd'])
 ||isset($user_data->prem['hod'])
 ||isset($user_data->prem['fc']));
     $status_arr = array('y' => "Approve",'n' => "reject" ,'ns' => "not sign");
     $ty_arr = array('csd' => "CSD",'fc' => "FC" ,'hod' => "HOD");
?>

<head>
</head>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left"> Add Task : <?=$project->project_name?></div>
                    </div>
                    <div class="block-content collapse in">
                        <div class="span12 no-margin-left">
                        <div class="span12 no-margin-left">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered orange lowfont">
                                    <thead>
                                        <tr>
                                            <th style="min-width:250px">Type</th>
                                            <th style="min-width:450px">Task</th>
                                            
                                            <th >Approved Budget (HR)</th>
                                            <th >Allocate Budget (HR)</th>
                                            <th >Remain Budget (HR)</th>
                                            <th >Assign Resource</th>
                                        </tr>
                                    </thead>
                                    <tbody id="res_t_body">                                        
                                    </tbody>
                                </table>
                        </div>
                         <div class="span12 no-margin-left">
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Task Type</label>
                                            <select id="task_type" >
                                                <option value="no">---please select ------</option>
                                                <?
                                                foreach ($task_type_list as $key => $value) {
                                                  ?>
                                                  <option value="<?=$value->id?>"><?=$value->name?></option>
                                                  <?
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">Task</label>
                                            <input class="form-control" type="text" id="task">
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label-new" for="focusedInput">&nbsp;</label>
                                            <a id="add_task" href="javascript:add_task();" iden="ready" class="btn btn-success add_task">Add Task <i class="icon-plus icon-white"></i></a>
                                        </div>
                                    </div>
                                </div>      
                            
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
$(function() {
    $("a").tooltip({});
    $(".datepicker").datepicker();
    print_table();
});
function add_task(){
        var task_name=$("#task").val();
        var task_type=$("#task_type").val();
        if (task_name==""||task_type=="no") {
            alert("กรอกข้อมูลให้ครบทุกช่อง");
        }else if($("#add_task").attr("iden")!="ready"){
            alert("Adding please wait");
        }else{
            $("#add_task").attr("iden","adding");
         $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/add_work_task"); ?>",
                data: {
                    "task_name": task_name,
                    "task_type": task_type,
                    "resource": '<?=$child_username?>',
                    "project_id": "<?=$project->project_id?>",
                }
            })
            .done(function(data) {
                if (data['flag']=="OK") {
                    parent.render_table_project("<?=$project->project_id?>");   
                    parent.$.fancybox.close();
                }else{
                    alert(data['flag'])
                }
                
                
            });
        }
    }
function print_table(){
        $("#res_t_body").html('<img src="<?=site_url()?>img/loading_gif/loading_'+get_int_for_gif()+'.gif" width="100%">')
        $.ajax({
                method: "POST",
                url: "<?php echo site_url("res/print_table_sum_work"); ?>",
                data: {
                    "project_id": '<?=$project->project_id?>'
                }
            })
            .done(function(data) {
                $("#res_t_body").html(data);
            });
    }
</script>
