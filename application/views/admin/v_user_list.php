<?
$ci =& get_instance();
?>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">User list</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">
                                      <div class="btn-group">
                                         <a href="<? echo site_url('admin/user_add')?>"><button class="btn btn-success">Add New <i class="icon-plus icon-white"></i></button></a>                                         
                                         
                                      </div>  
                                      <div class="btn-group">
                                         
                                         <a href="<? echo site_url('admin/print_user_to_excel')?>" target="_blank"><button class="btn btn-info">User Report</button></a>
                                      </div>                                      
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example2">
                                        <thead>
                                            <tr>
                                              <th>#</th>
                                              <th>username</th>
                                              <th>ชื่อ</th>
                                              <th>นามสกุล</th>
                                              <th>เข้าใช้งานล่าสุด</th>
                                              <th>Action</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                           <?
                                            foreach ($user_list as $key => $value) {
                                             ?>
                                             <tr>
                                                  <td><? echo $key+1; ?></td>
                                                  <td><? echo $value->username; ?></td>
                                                  <td><? echo $value->firstname; ?></td>
                                                  <td><? echo $value->lastname; ?></td>
                                                  <td><? echo date("d M Y",$value->last_access); ?> </td>
                                                  <td>                                                                                           
                                                    <a href="<? echo site_url('admin/user_edit/'.$value->username)?>" class="btn btn-info btn-xs">แก้ใข</a>
                                                    <a href="javascript:approvol('<? echo site_url('admin/delete_user/'.$value->username)?>')" class="btn btn-danger btn-xs">ลบ</a>
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
    function approvol(link){
        if (confirm("Confirm Delete")) {
            window.open(link,"_self")
        };
    }
</script>