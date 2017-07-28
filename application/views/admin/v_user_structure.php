<?
$ci =& get_instance();
//https://jsplumbtoolkit.com/demos.html
?>
<style type="text/css">
  .table tr td{
    text-align: center;
    cursor: pointer;
    border: none;
    margin:0px 10px;
    white-space: nowrap;
    padding:10px;
  }
  .table tr td.bu{
    text-align: center;
    cursor: pointer;
  }
  .table{
    cursor: pointer;
    border: none;
    border-spacing: 10px 80px;
    border-collapse: separate;
  }
  .user-list{
    border: none;
  }
  .block-content{
    overflow: hidden;
  }
</style>
<script src="<?echo site_url();?>js/jsPlumb-2.0.6.js"></script>
<div class="container-fluid">
            <div class="row-fluid">                
                <div class="span12" id="content">

                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Business Structure</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                    <div id="chart_contain" class="span12 no-margin-left">
                                      <table id="draggable" cellpadding="0" cellspacing="50" border="0" class="table">
                                        <tbody>
                                            <tr>
                                           <?
                                           $key1_colspan=0;
                                           $key2_colspan=0;
                                           $key3_colspan=0;
                                           // print Business
                                            foreach ($bu_struct as $key => $value) {
                                             ?>
                                             
                                                  <td id="key1_<?=$value->id?>" class="bu"><? echo $value->name; ?></td>
                                              
                                             <?
                                            }
                                            ?>    
                                            </tr>
                                            <tr>
                                              <?
                                              // pritn department
                                              foreach ($bu_struct as $key => $value) {
                                                if (count($value->depart)==0) {
                                                  ?>
                                                  <td >No Department</td>
                                                  <?
                                                  
                                                }
                                                foreach ($value->depart as $key2 => $value2) {
                                                  
                                                  ?>
                                                  <td id="key2_<?=$value2->id?>"><? echo $value2->name; ?></td>
                                                  <?
                                                }
                                                
                                                
                                              }
                                              ?>

                                            </tr>    
                                            <tr>
                                              <?
                                              // pritn position
                                              foreach ($bu_struct as $key => $value) {
                                                if (count($value->depart)==0) {
                                                  ?>
                                                  <td >No Position</td>
                                                  <?
                                                  
                                                }
                                                foreach ($value->depart as $key2 => $value2) {
                                                  if (count($value2->pos)==0) {
                                                    ?>
                                                    <td >No Position</td>
                                                    <?
                                                    
                                                  }
                                                  foreach ($value2->pos as $key3 => $value3) {
                                                    $key2_colspan+=1;
                                                    $key1_colspan+=1;
                                                    ?>
                                                    <td id="key3_<?=$value3->id?>"><? echo $value3->name; ?>
                                                    <br>
                                                    <br>
                                                    <ul class="user-list"><?
                                                    foreach ($value3->user as $key4 => $value4) {
                                                      ?>
                                                      
                                                        <li><?=$value4->nickname?></li>
                                                      
                                                      <?
                                                    }
                                                    ?>
                                                    </ul>
                                                    </td>
                                                    <?

                                                  }
                                                  ?>
                                                <script type="text/javascript">
                                                $("#key2_<?=$value2->id?>").attr("colspan","<?=$key2_colspan?>");
                                                </script>
                                                <?
                                                  $key2_colspan=0;
                                                }
                                                ?>
                                                <script type="text/javascript">
                                                $("#key1_<?=$value->id?>").attr("colspan","<?=$key1_colspan?>");
                                                </script>
                                                <?
                                                $key1_colspan=0;
                                              }
                                              ?>
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
    function approvol(link){
        if (confirm("Confirm Delete")) {
            window.open(link,"_self")
        };
    }
    $(function() {
        $( "#chart_contain" ).draggable();

    });
    




   jsPlumb.ready(function() {

    var firstInstance = jsPlumb.getInstance();
      firstInstance.importDefaults({
          Connector : [ "Flowchart", { cornerRadius: 1 } ],
          Anchors : [ "BottomCenter", "TopCenter" ],
          ConnectionsDetachable   : false,
          PaintStyle : { lineWidth : 4, strokeStyle : "#456" },
        });

      <?
         // pritn department
         foreach ($bu_struct as $key => $value) {
           foreach ($value->depart as $key2 => $value2) {
            ?>
            firstInstance.connect({
              source:"key1_<?=$value->id?>",
              target:"key2_<?=$value2->id?>",
              endpoint:"Dot",
              paintStyle:{lineWidth:5,strokeStyle:'rgb(103, 127, 179)'},
  endpointStyle:{radius:5}
              
            });
            <?
           }       
           
         }
         foreach ($bu_struct as $key => $value) {
           foreach ($value->depart as $key2 => $value2) {
            foreach ($value2->pos as $key3 => $value3) {
              ?>
              firstInstance.connect({
                source:"key2_<?=$value2->id?>",
                target:"key3_<?=$value3->id?>",
                endpoint:"Dot",
                paintStyle:{lineWidth:5,strokeStyle:'rgb(103, 127, 179)'},
                endpointStyle:{radius:5}
                
              });
              <?
            }
            
           }       
           
         }
         ?>

        
           
        });

</script>