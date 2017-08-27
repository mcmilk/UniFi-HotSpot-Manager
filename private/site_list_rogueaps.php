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

if ($action === "list_guest_aps") {
    $head = __("Guest WLANs");
    $text = __("This is where wireless networks are displayed, which, according to the MAC address, also have tickets.");
} else {
    $head = __("Wireless networks nearby");
    $text = __("List of wireless networks nearby.");
}

?>

<form method="POST" action="">

<div class="row">
  <div class="col-md-6">
    <h2><?php echo $head; ?></h2>
    <p><?php echo $text;  ?></p>
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
        <th class="text-center">ESSID</th>
        <th class="text-center"><?php echo __("Security"); ?></th>
        <th class="text-center">OUI</th>
        <th class="text-center">BSSID</th>
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
  myTable = $('.table').DataTable({
    <?php echo dataTablesDefaults(); ?>
    "ajax": {
      "url": "?get_data=list_rogueaps",
      "type": "POST",
    },
    "columns": [
      { "data": "essid", "className": "text-center input-filter" },
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
    add_table_search();
  }

  $("#reload").click(function(){
    myTable.ajax.reload(fnInitComplete, false);
  });

});
</script>
