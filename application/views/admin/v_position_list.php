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
                                <div class="muted pull-left">Position list</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">
                                      <div class="btn-group">
                                         <a href="<? echo site_url('admin/position_add')?>"><button class="btn btn-success">Add New Position <i class="icon-plus icon-white"></i></button></a>
                                      </div>                                      
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example2">
                                        <thead>
                                            <tr>
                                              <th>#</th>
                                              <th>position</th>
                                              <th>Department</th>
                                              <th>Business Unit</th>
                                              <th>Action</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                           <?
                                            foreach ($position_list as $key => $value) {
                                             ?>
                                             <tr>
                                                  <td><? echo $key+1; ?></td>
                                                  <td><? echo $value->name; ?></td>
                                                  <td><? echo $value->department->name; ?></td>
                                                  <td><? echo $value->department->bu->name; ?></td>
                                                  <td>                                                                                           
                                                    <a href="<? echo site_url('admin/position_edit/'.$value->id)?>" class="btn btn-info btn-xs">แก้ใข</a>
                                                    <a href="javascript:approvol('<? echo site_url('admin/delete_position/'.$value->id)?>')" class="btn btn-danger btn-xs">ลบ</a>
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