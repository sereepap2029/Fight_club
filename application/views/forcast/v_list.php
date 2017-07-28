<?
$ci =& get_instance();
?>
<style type="text/css">
  .green-f{
    color: green;
    font-weight: bolder;
  }
</style>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">All Forcast</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">
                                      <div class="btn-group">
                                         <a href="<? echo site_url('forcast/add')?>"><button class="btn btn-success">Create New Forcast <i class="icon-plus icon-white"></i></button></a>
                                      </div>                                      
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="example2">
                                        <thead>
                                            <tr>
                                              <th>#</th>
                                              <th>Job Name</th>
                                              <th>Duration</th>
                                              <th>Project Owner</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                              <?
                                              $countnum=0;
                                              foreach ($dat as $key => $value) {
                                                $countnum+=1;
                                                $company=$ci->m_company->get_company_by_id($value->project_client);
                                                $company_bu=$ci->m_company->get_bu_by_id($value->project_bu);
                                                ?>
                                                <tr>
                                                  <td><? echo $countnum; ?></td>
                                                  <td style="word-wrap: normal;min-width:400px;">
                                                                <a href="<? echo site_url('forcast/edit/'.$value->project_id)?>" class=""><?=$value->project_name?><i class=" icon-pencil"></i></a>
                                                                <br>
                                                              <? echo $company->name." ".$company_bu->bu_name;
                                                              ?><br>
                                                              <table class="just-table">
                                                                <tr>
                                                                  <td><?
                                                                  echo number_format($value->project_value, 0, '.', ',');
                                                                  ?></td>
                                                                  <td style="text-align:right">
                                                                    
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                              
                                                              </td>
                                                  <td><? echo $ci->m_time->unix_to_datepicker($value->project_start)." - ".$ci->m_time->unix_to_datepicker($value->project_end); ?>
                                                    <br>
                                                    <?
                                                      $lenght_time=(int)(($value->project_end-$value->project_start)/(60*60*24));
                                                      $until_now_time=(int)((time()-$value->project_start)/(60*60*24));
                                                      $cur_lenght=0;
                                                      if ($until_now_time>=$lenght_time) {
                                                        $cur_lenght=100;
                                                      }else{
                                                        $cur_lenght=(int)(($until_now_time/$lenght_time)*100);
                                                      }                                                    
                                                      echo $lenght_time." Days ";
                                                      ?>
                                                      <div class="progress">
                                                        <div style="width: <?=$cur_lenght?>%;" class="bar"><?=$cur_lenght?>%</div>
                                                      </div>
                                                  </td>
                                                  <td>
                                                    <?
                                                    $manager=$ci->m_user->get_user_by_login_name($value->project_cs);
                                                      ?>
                                                      <a href="<? echo site_url('forcast/edit/'.$value->project_id)?>" class=""><?=$manager->nickname?><i class=" icon-pencil"></i></a><br> 
                                                      <a href="<? echo site_url('forcast/delete_forcast/'.$value->project_id)?>" class="btn btn-danger">Delete<i class=" icon-remove icon-white"></i></a><br> 
                                                  </td>
                                                </tr>
                                                <?
                                              }
                                              ?>                                                              
                                        </tbody>
                                    </table>                                   
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
          $(".fancybox").fancybox({
              maxWidth    : 800,
              maxHeight   : 600,
              fitToView   : false,
              width       : '70%',
              height      : '70%',
              autoSize    : false,
              closeClick  : false,
              openEffect  : 'none',
              closeEffect : 'none'
          });
        });
        </script>