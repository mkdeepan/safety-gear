<?php defined('ABSPATH') or die("Cannot access pages directly."); ?>

<script type="text/javascript">
    if(typeof wpDataTablesCharts == 'undefined'){
        var wpDataTablesCharts = [];
    }
    wpDataTablesCharts['<?php echo $tableId?>'] = <?php echo $chartProperties ?>;
</script>