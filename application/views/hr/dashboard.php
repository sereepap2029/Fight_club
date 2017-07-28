<?
$ci =& get_instance();
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
                                <div class="muted pull-left">HR Dashboard</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">
                                      <ul class="btn-group_gate">

                                        <li>
                                            <a class="btn btn-success" href="<? echo site_url('hr/holiday_carlendar')?>">ปฏิทินวันหยุด</i>
                                            </a>
                                        </li>

                                         <li>
                                            <a class="btn btn-success" href="<? echo site_url('hr/user_leave_carlendar')?>">ปฏิทินวันลา</i>
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