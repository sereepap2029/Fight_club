<? $ci=& get_instance(); ?>
<!DOCTYPE html>
<html class="no-js">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>popup</title>
    <!-- Bootstrap -->
    <link href="<?echo site_url();?>bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/DT_bootstrap.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/jquery.mCustomScrollbar.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/css/jquery-ui.css" rel="stylesheet" />
    <link href="<?echo site_url();?>assets/css/jquery.datetimepicker.css" rel="stylesheet" />
    <link href="<?echo site_url();?>css/jquery.fancybox.css" rel="stylesheet" />
    <link href="<?echo site_url();?>assets/styles.css" rel="stylesheet" media="screen">
    <link href="<?echo site_url();?>assets/styles_atom.css" rel="stylesheet" media="screen">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    <script src="<?echo site_url();?>vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    <script src="<?echo site_url();?>assets/js/jquery-1.10.2.js"></script>
    <script src="<?echo site_url();?>assets/js/jquery-ui.js"></script>
    <script src="<?echo site_url();?>assets/js/jquery.datetimepicker.js"></script>
    <script src="<?echo site_url();?>js/jquery.mCustomScrollbar.js"></script>
    <script src="<?echo site_url();?>js/jquery.fancybox.js"></script>
    <link href="<?echo site_url();?>vendors/chosen.min.css" rel="stylesheet" media="screen">
    <script src="<?echo site_url();?>vendors/chosen.jquery.min.js"></script>
    <style type="text/css">
    /*.navbar-inner div{
            background-color:#3366FF;
            color: #FFFFFF;
        }*/
    
    .white-nav-bar {
        background-image: -moz-linear-gradient(top, #fff, #f2f2f2);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#fff), to(#f2f2f2));
        background-image: -webkit-linear-gradient(top, #fff, #f2f2f2);
        background-image: -o-linear-gradient(top, #fff, #f2f2f2);
        background-image: linear-gradient(to bottom, #fff, #f2f2f2);
    }
    body{
        padding-top: 10px;
    }
    </style>
    <script type="text/javascript">
    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    function get_int_for_gif(){
        return getRandomInt(1, 13);
    }
    </script>
</head>

<body>
