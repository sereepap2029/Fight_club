<?
$ci =& get_instance();
$direct_to_dash=false;
if (count($user_data->prem)==1&&!isset($havede)) {
  $direct_to_dash=true;
}
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
                                <div class="muted pull-left">Gate List</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">
                                      <ul class="btn-group_gate">

                                        <li>
                                          <?/* if (isset($user_data->prem['csd'])) { 
                                            if ($direct_to_dash) {
                                              ?>
                                              <script type="text/javascript">
                                              window.open("<? echo site_url('csd')?>","_self");
                                              </script>
                                              <?
                                            }
                                            ?>
                                            <a class="btn btn-success" href="<? echo site_url('csd')?>">List - CSD <i class="icon-plus icon-white"></i>
                                            </a>
                                          <?}?>
                                        </li>

                                         <li>
                                          <? if (isset($user_data->prem['hod'])) { 
                                            if ($direct_to_dash) {
                                              ?>
                                              <script type="text/javascript">
                                              window.open("<? echo site_url('hod')?>","_self");
                                              </script>
                                              <?
                                            }
                                            ?>
                                            <a class="btn btn-success" href="<? echo site_url('hod')?>">List - HOD <i class="icon-plus icon-white"></i>
                                        </a>
                                          <?}?>
                                        </li>
                                        
                                         <li>
                                          <? if (isset($user_data->prem['fc'])) { 
                                            if ($direct_to_dash) {
                                              ?>
                                              <script type="text/javascript">
                                              window.open("<? echo site_url('fc')?>","_self");
                                              </script>
                                              <?
                                            }
                                            ?>
                                            <a class="btn btn-success" href="<? echo site_url('fc')?>">
                                              List - FC <i class="icon-plus icon-white"></i>
                                            </a>
                                          <?}?>
                                        </li>
                                        <li>
                                          <? if (isset($user_data->prem['cs'])) { 
                                            if ($direct_to_dash) {
                                              ?>
                                              <script type="text/javascript">
                                              window.open("<? echo site_url('cs')?>","_self");
                                              </script>
                                              <?
                                            }
                                            ?>
                                            <a class="btn btn-success" href="<? echo site_url('cs')?>">
                                              List - CS <i class="icon-plus icon-white"></i>
                                            </a>
                                          <?}?>
                                        </li>
                                        <li>
                                          <? if (isset($user_data->prem['resource'])) { 
                                            if ($direct_to_dash) {
                                              ?>
                                              <script type="text/javascript">
                                              window.open("<? echo site_url('res')?>","_self");
                                              </script>
                                              <?
                                            }
                                            ?>
                                            <a class="btn btn-success" href="<? echo site_url('res')?>">
                                              List - RES <i class="icon-plus icon-white"></i>
                                            </a>
                                          <?}?>
                                        </li>
                                        <li>
                                          <? if (isset($user_data->prem['account'])) { 
                                            if ($direct_to_dash) {
                                              ?>
                                              <script type="text/javascript">
                                              window.open("<? echo site_url('account')?>","_self");
                                              </script>
                                              <?
                                            }
                                            ?>
                                            <a class="btn btn-success" href="<? echo site_url('account')?>">
                                              List - Account <i class="icon-plus icon-white"></i>
                                            </a>
                                          <?}?>
                                        </li>
                                        <li>
                                          <?*/ ?>
                                            <a class="btn btn-success" href="<? echo site_url('hr')?>">
                                              ปฏิทินลงซ้อม <i class="icon-plus icon-white"></i>
                                            </a>
                                        </li>
                                      </ul>
                                         
                                                                         
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