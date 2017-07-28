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
  .table tr td.head{
    text-align: center;
    cursor: pointer;
    border: none;
    margin:0px 10px;
    white-space: nowrap;
    padding:10px;
    font-weight: bolder;
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
                                <div class="muted pull-left">Service to Position Structure</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                    <div id="chart_contain" class="span12 no-margin-left">
                                    <table id="draggable" cellpadding="0" cellspacing="50" border="0" class="table">
                                    <?
                                    foreach ($hour_rate_list as $key => $value) {
                                      ?>
                                      
                                       <tbody>
                                        <tr>
                                           <td id="key1_<?=$value->id?>" class="head" colspan="<?=count($value->pos)?>"><?=$value->name?></td>
                                         </tr>
                                         <tr>
                                           <?
                                           foreach ($value->pos as $key2 => $value2) {
                                             ?>
                                             <td id="key2_<?=$value->id?>_<?=$value2->position_id?>"><?
                                             echo $position_list[$value2->position_id]->name;
                                             ?></td>
                                             <?
                                           }
                                           if (count($value->pos)==0) {
                                             ?>
                                             <td id="key2_<?=$value->id?>_000">No Service</td>
                                             <?
                                           }
                                           ?>
                                         </tr>
                                       </tbody>
                                    
                                      <?
                                    }
                                    ?>
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
      jsPlumb.importDefaults({
          Connector : [ "Flowchart", { cornerRadius: 1 } ],
          Anchors : [ "BottomCenter", "TopCenter" ],
          ConnectionsDetachable   : false,
          PaintStyle : { lineWidth : 4, strokeStyle : "#456" },
        });

      <?
         // pritn department
         foreach ($hour_rate_list as $key => $value) {
           foreach ($value->pos as $key2 => $value2) {
            ?>
            jsPlumb.connect({
              source:"key1_<?=$value->id?>",
              target:"key2_<?=$value->id?>_<?=$value2->position_id?>",
              endpoint:"Dot",
              paintStyle:{lineWidth:5,strokeStyle:'rgb(103, 127, 179)'},
  endpointStyle:{radius:5}
              
            });
            <?
           }       
           if (count($value->pos)==0) {
             ?>
            jsPlumb.connect({
              source:"key1_<?=$value->id?>",
              target:"key2_<?=$value->id?>_000",
              endpoint:"Dot",
              paintStyle:{lineWidth:5,strokeStyle:'rgb(103, 127, 179)'},
  endpointStyle:{radius:5}
              
            });
            <?
           }
           
         }
         ?>

        
           
        });

</script>