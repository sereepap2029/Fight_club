<?
$ci =& get_instance();
$day_name_arr = array(1 => "จ.",2 => "อ.",3 => "พ.",4 => "พฤ.",5 => "ศ.",6 => "ส.",7 => "อา.", );
$month_name_arr = array(
    1 => "มกราคม",
    2 => "กุมภาพันธ์",
    3 => "มีนาคม",
    4 => "เมษายน",
    5 => "พฤษภาคม",
    6 => "มิถุนายน",
    7 => "กรกฎาคม",
    8 => "สิงหาคม",
    9 => "กันยายน",
    10 => "ตุลาคม",
    11 => "พฤศจิกายน",
    12 => "ธันวาคม",
     );
?>
<style type="text/css">
    .table th,.table td{
        text-align: center;
        cursor: default;
    }
    td.set_holiday{
        cursor: pointer;
    }
    td.set_holiday:hover{
        background-color: #55CCC1;
    }
    .holiday{
        background-color: green;
    }
    .weekend{
        background-color: gray;
    }
</style>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12" id="content">
                <div class="row-fluid">
                    <!-- block -->
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Year carlendar</div>
                        </div>
                        <div class="block-content collapse in">
                            <div class="span12">
                                <div class="table-toolbar">
                                    <form method="post" action="<?=site_url("hr/holiday_carlendar")?>">
                                        <fieldset>
                                            <div class="span12 no-margin-left">
                                                <div class="span3">
                                                    <div class="control-group">
                                                        <label class="control-label-new" for="focusedInput">YEAR</label>
                                                        <select id="year" class="chzn-select" name="year">
                                                            <?
                                                            $start_year=(int)date("Y")-10;
                                                        for ($i=$start_year; $i <$start_year+20 ; $i++) { 
                                                            ?>
                                                            <option value="<?=$i?>" <?if($i==(int)$_POST['year']){echo "selected";}?>><?=$i?></option>
                                                            <?
                                                        }
                                                        ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="span1 ">
                                                    <div class="control-group">
                                                    <label class="control-label-new" for="focusedInput">&nbsp</label>
                                                        <input type="submit" class="btn btn-info" value="submit">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                                <div class="span12 no-margin-left">
                                    <?
                                    for ($i=1; $i <= 12; $i++) { 
                                        ?>
                                        <div class="span4 no-margin-left">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                                                <?
                                                // determine start day
                                                $mark_time=mktime(0,0,1,$i,1,(int)$_POST['year']);
                                                $num_day_in_month=date("t",$mark_time);
                                                $first_day_in_week=date("N",$mark_time);
                                                $days=1;
                                                ?>
                                                <tr>
                                                    <th colspan="7"><?=$month_name_arr[$i]?></th>
                                                </tr>
                                                <tr>
                                                    <?
                                                    for ($j=1; $j <=7 ; $j++) { 
                                                        ?>
                                                        <th>
                                                            <?=$day_name_arr[$j]?>
                                                        </th>
                                                        <?
                                                    }
                                                    ?>
                                                </tr>
                                                <?
                                                for ($j=0; $j <=$num_day_in_month+7 ; $j+=7) { 
                                                    if ($j==0) {
                                                        ?>
                                                        <tr>
                                                        <?
                                                        $num_skipday=$first_day_in_week-1;
                                                        for ($l=1; $l <= $num_skipday; $l++) { 
                                                            ?>
                                                            <td>-</td>
                                                            <?
                                                        }
                                                        for ($m=$first_day_in_week; $m <=7 ; $m++) { 
                                                            $cur_time=mktime(0,0,1,$i,$days,(int)$_POST['year']);
                                                            $holi_class="";
                                                            $weekend="";
                                                            $title="";
                                                            if (isset($holiday[$cur_time])&&$holiday[$cur_time]->is_holiday=="y") {
                                                                $holi_class="holiday";
                                                                $title=$holiday[$cur_time]->comment;
                                                            }
                                                            if ($m==6||$m==7) {
                                                                $weekend="weekend";
                                                            }else{
                                                                $weekend="set_holiday";
                                                            }
                                                            ?>
                                                            <td class="<?=$holi_class?> <?=$weekend?>" unix-time="<?=$cur_time?>" title="<? echo $title; ?>"><?=$days?></td>
                                                            <?
                                                            $days+=1;
                                                        }
                                                        ?>
                                                        </tr>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <tr>
                                                            <?
                                                            for ($k=1; $k <=7 ; $k++) { 
                                                                if ($days<=$num_day_in_month) {
                                                                    $cur_time=mktime(0,0,1,$i,$days,(int)$_POST['year']);
                                                                    $holi_class="";
                                                                    $weekend="";
                                                                    $title="";
                                                                    if (isset($holiday[$cur_time])&&$holiday[$cur_time]->is_holiday=="y") {
                                                                        $holi_class="holiday";
                                                                        $title=$holiday[$cur_time]->comment;
                                                                    }
                                                                    if ($k==6||$k==7) {
                                                                        $weekend="weekend";
                                                                    }else{
                                                                        $weekend="set_holiday";
                                                                    }
                                                                    ?>
                                                                    <td class="<?=$holi_class?> <?=$weekend?>" unix-time="<?=$cur_time?>" title="<? echo $title; ?>"><?=$days?></td>
                                                                    <?
                                                                    $days+=1;
                                                                }else{
                                                                    ?>
                                                                    <td>-</td>
                                                                    <?
                                                                }
                                                                
                                                            }
                                                            ?>
                                                        </tr>
                                                        <?
                                                    }
                                                }

                                                ?>
                                            </table>
                                        </div>
                                        <?
                                    }
                                    ?>
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
        <a class="fancybox" style="display:none;" id="c_add_holiday" data-fancybox-type="iframe" href="<?=site_url("hod/assign_action/1123")?>"></a>
    </div>
    <!--/.fluid-container-->
    <script type="text/javascript">
    $( "td" ).tooltip({});
    $(".fancybox").fancybox({
            fitToView   : false,
            width       : '100%',
            height      : '90%',
            autoSize    : false,
            closeClick  : false,
            openEffect  : 'none',
            closeEffect : 'none'
        });
    $(document).on("click", ".set_holiday", function() {
        var current_ele=$(this);
        $("#c_add_holiday").attr("href","<?=site_url('hr/pop_holiday')?>/"+current_ele.attr("unix-time"));
        $("#c_add_holiday").trigger('click'); 
        

    });
    </script>
