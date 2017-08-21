<?php

/**
 * UniFi HotSpot Manager
 *
 * Copyright (c) 2017 Tino Reichardt
 * All rights reserved.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (!defined('HOTSPOT')) { exit; }

?>

<form method="POST" action="">

<div class="row">
  <div class="col-md-6">
    <h2><?php echo __("Wireless networks nearby"); ?></h2>
    <p><?php echo __("Here, wireless networks are displayed, which according to MAC address also have vouchers."); ?></p>
  </div>
  <div class="col-md-6 text-right">
    <h2></h2><p>
    <button type="button" id="reload" class="btn btn-primary"><i class="fa fa-refresh"></i> <?php echo __("Reload Now"); ?></button>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
  <table id="myTable" class="table table-hover table-responsive table-bordered" width="100%">
    <thead>
      <tr class="info">
        <th class="text-center"><?php echo __("ESSID"); ?></th>
        <th class="text-center"><?php echo __("Security"); ?></th>
        <th class="text-center"><?php echo __("OUI"); ?></th>
        <th class="text-center"><?php echo __("BSSID"); ?></th>
        <th class="text-center"><?php echo __("Last Seen"); ?></th>
        <th class="text-center"><?php echo __("Report time"); ?></th>
        <th class="text-center"><?php echo __("Age"); ?></th>
      </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
      <tr class="info">
        <th class="text-center"></th>
        <th class="text-center"></th>
        <th class="text-center"></th>
        <th class="text-center"></th>
        <th class="text-center"></th>
        <th class="text-center"></th>
        <th class="text-center"></th>
      </tr>
    </tfoot>
  </table>
  </div>
</div>
</form>

<script>
$(document).ready(function(){
  var myTable = $('.table').DataTable({
    <?php echo dataTablesDefaults(); ?>
    "ajax": {
      "url": "?get_data=<?php echo $todo; ?>",
      "type": "POST",
    },
    "columns": [
      { "data": "essid", "className": "text-center select-filter" },
      { "data": "security", "className": "text-center select-filter" },
      { "data": "oui", "className": "text-center select-filter" },
      { "data": "bssid", "className": "text-center" },
      { "data": "last_seen", "className": "text-center", "render": fmt_datetime },
      { "data": "report_time", "className": "text-center", "render": fmt_datetime },
      { "data": "age", "className": "text-center" }
     ],
  });

  /* add select fields under each column */
  function fnInitComplete(settings, json) {
    myTable.columns('.select-filter').every(function(){
      var column = this;
      var select = $('<select><option value=""></option></select>')
        .appendTo($(column.footer()).empty())
        .on( 'change', function(){
            var val = $.fn.dataTable.util.escapeRegex(
                $(this).val()
            );

            column
                .search( val ? '^'+val+'$' : '', true, false )
                .draw();
        });

      column.cache('search').unique().sort().each(function(d, j) {
          select.append( '<option value="'+d+'">'+d+'</option>' )
      });
    });
  }

  $("#reload").click(function(){
    myTable.ajax.reload(fnInitComplete, false);
  });

});
</script>
