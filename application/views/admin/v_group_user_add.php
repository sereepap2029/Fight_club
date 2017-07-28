<?
$ci =& get_instance();
?>
<style type="text/css">
.row-fluid fieldset .no-margin-left{margin-left: 0px;}
.row-fluid .no-margin-left{margin-left: 0px;}
</style>
<script src="<?echo site_url();?>js/angular_controller/app.js"></script> 
<script src="<?echo site_url();?>js/angular_controller/admin_controller.js"></script> 
<div class="container-fluid" ng-app="adminApp">
            <div class="row-fluid" ng-controller="swap_prem">                
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
                                   <form class="form-horizontal" method="post" action="<? if(isset($edit)){echo site_url('admin/edit_prem_group/'.$group->g_id);}else{echo site_url('admin/add_prem_group');}?>">
                                        <fieldset>
                                          
                                                <div class="control-group">
                                                  <label class="control-label" for="focusedInput">ชื่อ Role</label>
                                                  <div class="controls">
                                                    <?
                                                    if (!isset($edit)) {
                                                      ?>
                                                      <input class="focused" id="" type="text" name="g_name">
                                                      <input type="hidden" name="g_id" id="g_id" value="" link="<?echo site_url('admin/edit_prem_group');?>">

                                                      <?
                                                    }else{
                                                      ?>
                                                      <input class="focused" id="" type="text" name="g_name" value="<?echo $group->g_name;?>">
                                                      <input type="hidden" name="g_id" id="g_id" value="<?echo $group->g_id;?>" link="<?echo site_url('admin/edit_prem_group/'.$group->g_id);?>">
                                                      
                                                      <?
                                                    }
                                                    ?>
                                                  </div>
                                                </div>
                                                <div class="span12 no-margin-left">
                                                  <div class="span4 no-margin-left">
                                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" >
                                                        <thead>
                                                            <tr>
                                                              <th>Selected Permission</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                             <tr ng-repeat="(key, value) in selected_prem">
                                                                  <td ng-click="select_left(key,$event)">{{value.name}}<input type="hidden" name="prem[]" value="{{value.prem}}"></td>
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
                                                              <th>Permission {{index_pre}}</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                              <tr ng-repeat="(key, value) in init_prem">
                                                                <td ng-click="select_right(key,$event)">{{value.name}}</td>
                                                              </tr>                                                                    
                                                        </tbody>
                                                    </table>
                                                  </div>
                                                </div>





                                                <div class="span12 no-margin-left">
                                                  <div class="span4 no-margin-left">
                                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" >
                                                        <thead>
                                                            <tr>
                                                              <th>Selected User</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                             <tr ng-repeat="(key, value) in selected_user ">
                                                                  <td ng-click="select_user_left(key,$event)">{{value.firstname+" "+value.lastname}}
                                                                  <input type="hidden" name="user[]" value="{{value.username}}"></td>
                                                              </tr>                                                                    
                                                        </tbody>
                                                    </table>
                                                  </div>
                                                  <div class="span4 no-margin-left">
                                                    <div class="span12 no-margin-left">
                                                      <div class="span4 no-margin-left"></div>
                                                        <div class="span4 no-margin-left">
                                                          <button class="span12" type="button" ng-click="move_user_left()" class="btn"><--</button>
                                                        </div>
                                                      <div class="span4 no-margin-left"></div>
                                                    </div>
                                                    <div class="span12 no-margin-left">
                                                      <div class="span4 no-margin-left"></div>
                                                        <div class="span4 no-margin-left">
                                                          <button class="span12" type="button" ng-click="move_user_right()" class="btn">--></button>
                                                        </div>
                                                      <div class="span4 no-margin-left"></div>
                                                    </div>
                                                  </div>
                                                  <div class="span4 no-margin-left">
                                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" >
                                                        <thead>
                                                            <tr>
                                                              <th>User</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                              <tr ng-repeat="(key, value) in init_user">
                                                                <td ng-click="select_user_right(key,$event)">{{value.firstname+" "+value.lastname}}</td>
                                                                <input type="hidden" name="init_user[]" value="{{value.username}}"></td>
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
        <script type="text/javascript">
                                           $(function() {

                                            });
                                           </script>


                                          

            