</div>
        <script src="<?echo site_url();?>bootstrap/js/bootstrap.min.js"></script>
        <script src="<?echo site_url();?>vendors/easypiechart/jquery.easy-pie-chart.js"></script>
        <script src="<?echo site_url();?>assets/scripts.js"></script>
        <script>
        $('.dropdown-toggle').click(function(e) {
          e.preventDefault();
          setTimeout($.proxy(function() {
            if ('ontouchstart' in document.documentElement) {
              $(this).siblings('.dropdown-backdrop').off().remove();
            }
          }, this), 0);
        });
        $(function() {
            // Easy pie charts
            $('.chart').easyPieChart({animate: 1000});
        });
        </script>
    <?
    if (isset($table)) {
    	?>
    	<!-- DATA TABLE SCRIPTS -->
        <script src="<?echo site_url();?>assets/js/dataTables/jquery.dataTables.js"></script>
        <script src="<?echo site_url();?>assets/DT_bootstrap.js"></script>
    	<?
    }
    ?>
   
</body>
</html>