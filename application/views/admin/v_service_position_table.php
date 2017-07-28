<?
$ci =& get_instance();
//https://jsplumbtoolkit.com/demos.html
?>
<style type="text/css">
  .table tr td{
    text-align: center;
    cursor: pointer;
    padding:3px;
    vertical-align: middle;
  }
  .table tr td.head{
    text-align: center;
    cursor: pointer;
    /*white-space: nowrap;*/
    padding:10px;
    font-weight: bolder;
    border-top: 1px solid #ddd!important;
    border: 1px solid #ddd;
  }
  .table tr td.headleft{
    text-align: center;
    cursor: pointer;
    white-space: nowrap;
    padding:10px;
    font-weight: bolder;
    border: 1px solid #ddd!important;
  }
  .table tr td.bu{
    text-align: center;
    cursor: pointer;
  }
  .table{
    cursor: pointer;
    border: none;
    border-collapse: separate;
  }
  .user-list{
    border: none;
  }
  .block-content{
    /*overflow: hidden;*/
  }
  .re-ok{
    background-color: green;
  }
  .re-ok.hiligh{
    background-color: blue;
  }
  .re-ok.hiligh2{
    background-color: blue;
  }
  .re-remove{
    background-color: gray;
  }
  .re-remove.hiligh{
    background-color: black;
  }
  .re-remove.hiligh2{
    background-color: black;
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
                                    <table id="draggable" cellpadding="0" cellspacing="50" border="0" class="table table-bordered">
                                    <tr>
                                    <td class="headleft bu">BU</td>
                                    <td class="headleft bu">Department</td>
                                      <td class="headleft">Position</td>
                                      <?
                                      foreach ($hour_rate_list as $key => $value) {
                                        ?>
                                        <td class="head" at-col="<?=$value->id?>">
                                          <?=$value->name?>
                                        </td>
                                        <?
                                      }
                                      ?>
                                    </tr>
                                    <?
                                    foreach ($position_list as $key => $value) {
                                      ?>
                                      <tr>
                                          <td class="head bu" ><?=$value->department->bu->name?></td>
                                          <td class="head bu" ><?=$value->department->name?></td>
                                           <td id="key1_<?=$value->id?>" at-row="<?=$value->id?>" class="head" ><?=$value->name?></td>
                                         
                                         
                                           <?
                                           foreach ($hour_rate_list as $key2 => $value2) {
                                            if (isset($value->service_list[$value2->id])) {
                                              ?>
                                              <td at-row="<?=$value->id?>" at-col="<?=$value2->id?>" class="re-ok"><i class="icon-ok icon-white"></i></td>
                                              <?
                                            }else{
                                              ?>
                                              <td at-row="<?=$value->id?>" at-col="<?=$value2->id?>" class="re-remove">-</td>
                                              <?
                                            }
                                           }
                                           ?>
                                         </tr>
                                    
                                      <?
                                    }
                                    ?>
                                    <tr>
                                      <td class="headleft bu">BU</td>
                                    <td class="headleft bu">Department</td>
                                      <td class="headleft">Position</td>
                                      <?
                                      foreach ($hour_rate_list as $key => $value) {
                                        ?>
                                        <td class="head" at-col="<?=$value->id?>">
                                          <?=$value->name?>
                                        </td>
                                        <?
                                      }
                                      ?>
                                    </tr>
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
    $(document).on("click", "[at-row]", function() {
        var current_ele=$(this);
        $("[at-row]").removeClass("hiligh");
        $("[at-row="+current_ele.attr("at-row")+"]").addClass("hiligh");
        

    });
    $(document).on("click", "[at-col]", function() {
        var current_ele=$(this);
        $("[at-col]").removeClass("hiligh2");
        $("[at-col="+current_ele.attr("at-col")+"]").addClass("hiligh2");
        

    });
    $(function() {
        $( "#chart_contain" ).draggable();

    });   


</script>