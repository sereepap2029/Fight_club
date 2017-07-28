<? $ci=& get_instance(); ?>
<style type="text/css">
.row-fluid .no-margin-left {
    margin-left: 0px;
}
</style>
<link rel="stylesheet" href="<?echo site_url();?>css/jquery.fileupload.css">
<link rel="stylesheet" href="<?echo site_url();?>css/style.css">
<script src="<?echo site_url();?>js/angular_controller/app.js"></script> 
<script src="<?echo site_url();?>js/angular_controller/admin_controller.js"></script> 
<div class="container-fluid" ng-app="adminApp">
    <div class="row-fluid" ng-controller="swap_hour_rate_pos">
        <div class="span12" id="content">
            <div class="row-fluid">
                <!-- block -->
                <div class="block">
                    <div class="navbar navbar-inner block-header">
                        <div class="muted pull-left">Add Position </div>
                    </div>
                    <div class="block-content collapse in" style="min-height:600px;">
                        <div class="span12">
                            <h5> <?if (isset($err_msg)) {
                                        echo "*******".$err_msg."*******";
                                    }?></h5>
                            <form class="form-horizontal" method="post" action="<? if(isset($edit)){echo site_url('admin/position_edit/'.$position->id);}else{echo site_url('admin/position_add');}?>">
                                <fieldset>
                                <input type="hidden" id="init_hour" link="<?echo site_url('admin/hour_rate_ajax');?>">
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Position Name</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <input class="focused" id="pos_name" pid="" type="text" name="name">
                                            <? }else{ ?>
                                            <input class="focused" id="pos_name" pid="<?echo $position->id;?>" type="text" name="name" value="<?echo $position->name;?>">
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="role">Department</label>
                                        <div class="controls">
                                            <select id="department_id" class="chzn-select" name="department_id">
                                                <option value="no">-----please select-----</option>
                                                <? foreach ($department_list as $key=> $value) { ?>
                                                <option value="<?=$value->id?>"><?=$value->bu->name.":".$value->name?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <? if (isset($edit)) { ?>
                                        <script type="text/javascript">
                                        $("#department_id").val("<?echo $position->department_id;?>")
                                        </script>
                                        <? } ?>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Description</label>
                                        <div class="controls">
                                            <? if (!isset($edit)) { ?>
                                            <textarea class="focused" name="description" style="width:80%;height:100px"></textarea>
                                            <? }else{ ?>
                                            <textarea class="focused" id="" type="text" name="description" style="width:80%;height:200px"><?echo $position->description;?></textarea>
                                            <? } ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="focusedInput">Non Productive</label>
                                        <div class="controls">
                                            <select id="non_productive" class="chzn-select" name="non_productive">
                                                <option value="n">No</option>
                                                <option value="y">Yes</option>
                                            </select>
                                        </div>
                                        <? if (isset($edit)) { ?>
                                        <script type="text/javascript">
                                        $("#non_productive").val("<?echo $position->non_productive;?>")
                                        </script>
                                        <? } ?>
                                    </div>








                                    <div class="span12 no-margin-left">
                                                  <div class="span4 no-margin-left">
                                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" >
                                                        <thead>
                                                            <tr>
                                                              <th>Selected Service Item</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                             <tr ng-repeat="(key, value) in selected_rate ">
                                                                  <td ng-click="select_left(key,$event)">{{value.name}}<input type="hidden" name="work[]" value="{{value.id}}"></td>
                                                              </tr>                                                                    
                                                        </tbody>
                                                    </table>
                                                  </div>
                                                  <div class="span4 no-margin-left">
                                                    <div class="span12 no-margin-left">
                                                      <div class="span4 no-margin-left"></div>
                                                        <div class="span4 no-margin-left">
                                                          <button class="span12" type="button" ng-click="move_left()" class="btn"><--</button>
                                                        </div>
                                                      <div class="span4 no-margin-left"></div>
                                                    </div>
                                                    <div class="span12 no-margin-left">
                                                      <div class="span4 no-margin-left"></div>
                                                        <div class="span4 no-margin-left">
                                                          <button class="span12" type="button" ng-click="move_right()" class="btn">--></button>
                                                        </div>
                                                      <div class="span4 no-margin-left"></div>
                                                    </div>
                                                  </div>
                                                  <div class="span4 no-margin-left">
                                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" >
                                                        <thead>
                                                            <tr>
                                                              <th>Service Item</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                              <tr ng-repeat="(key, value) in init_hour_rate">
                                                                <td ng-click="select_right(key,$event)">{{value.name}}</td>
                                                              </tr>                                                                    
                                                        </tbody>
                                                    </table>
                                                  </div>
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
        changeYear: true,
    });
    $('.datetimepicker').datetimepicker();
    $(".chzn-select").chosen({
        width: "75%"
    });
});
</script>
