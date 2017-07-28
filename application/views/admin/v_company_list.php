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
                                <div class="muted pull-left">Client list</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">
                                      <div class="btn-group">
                                         <a href="<? echo site_url('admin/company_add')?>"><button class="btn btn-success">Add New Company <i class="icon-plus icon-white"></i></button></a>
                                      </div>                                      
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example2">
                                        <thead>
                                            <tr>
                                              <th>#</th>
                                              <th>Name</th>
                                              <th>Client ID</th>
                                              <th>Action</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                           <?
                                            foreach ($company_list as $key => $value) {
                                             ?>
                                             <tr>
                                                  <td><? echo $key+1; ?></td>
                                                  <td><? echo $value->name; ?></td>
                                                  <td><? echo $value->client_id; ?></td>
                                                  <td>                                                                                           
                                                    <a href="<? echo site_url('admin/company_edit/'.$value->id)?>" class="btn btn-info btn-xs">แก้ใข</a>
                                                    <?
                                                    if ($value->is_use=="n") {
                                                      ?>
                                                      <a href="javascript:approvol('<? echo site_url('admin/delete_company/'.$value->id)?>')" class="btn btn-danger btn-xs">ลบ</a>
                                                      <?
                                                    }
                                                    ?>
                                                    
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