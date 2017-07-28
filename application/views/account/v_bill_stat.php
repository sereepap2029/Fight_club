<?
$ci =& get_instance();
//print_r($bill_stat);
?>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Billing Status Date : <?=date("d / m / Y")?></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">
                                      <div class="btn-group">
                                         
                                      </div>                                      
                                   </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" >
                                        <thead>
                                            <tr>
                                              <th>Business Unit</th>
                                              <th>Billing MTD</th>
                                              <th>Outstanding Billing this Month</th>
                                              <th>Overdue billing</th>
                                              <th>Billing Next Month</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            <?
                                            $total_mtd=0;
                                            $total_out=0;
                                            $total_over=0;
                                            $total_next=0;
                                            foreach ($bill_stat as $key => $value) {
                                              $total_mtd+=$value->bill_mtd;
                                              $total_out+=$value->outstanding;
                                              $total_over+=$value->overdue;
                                              $total_next+=$value->bill_nextmonth;
                                              ?>
                                              <tr>
                                                <td><?=$value->name?></td>
                                                <td><?=number_format($value->bill_mtd)?></td>
                                                <td><?=number_format($value->outstanding)?></td>
                                                <td><?=number_format($value->overdue)?></td>
                                                <td><?=number_format($value->bill_nextmonth)?></td>
                                              </tr>
                                              <?
                                            }
                                            ?>            
                                            <tr>
                                              <td>Total</td>
                                                <td><?=number_format($total_mtd)?></td>
                                                <td><?=number_format($total_out)?></td>
                                                <td><?=number_format($total_over)?></td>
                                                <td><?=number_format($total_next)?></td>
                                            </tr>                                                       
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
              fitToView   : false,
              width       : '95%',
              height      : '90%',
              autoSize    : false,
              closeClick  : false,
              openEffect  : 'none',
              closeEffect : 'none'
          });
        });
        </script>