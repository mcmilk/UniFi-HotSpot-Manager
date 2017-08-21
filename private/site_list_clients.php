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
<input type="text" name="MacIDs" id="idMac" value="" style="display:none;" />

<div class="row">
  <div class="col-md-6">
    <h2><?php echo __("List of online clients"); ?></h2>
    <p><?php echo __("Displays all currently active devices or users on the network."); ?></p>
  </div>
  <div class="col-md-6 text-right">
    <h2></h2>
    <button type="button" id="reload" class="btn btn-primary"><i class="fa fa-refresh"></i> <?php echo __("Reload Now"); ?></button>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
  <table id="myTable" class="table table-hover table-responsive table-bordered" width="100%">
    <thead>
      <tr class="info">
        <th class="text-center"><?php echo __("Hostname"); ?></th>
        <th class="text-center"><?php echo __("Idle"); ?></th>
        <th class="text-center"><?php echo __("First Seen"); ?></th>
        <th class="text-center"><?php echo __("Last Seen"); ?></th>
        <th class="text-center"><?php echo __("Bandwidth"); ?></th>
        <th class="text-center"><?php echo __("Download"); ?></th>
        <th class="text-center"><?php echo __("Upload"); ?></th>
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
      "url": "?get_data=list_clients",
      "type": "POST",
    },
    "columns": [
      { "data": "hostname", "defaultContent": "<i><?php echo __("No hostname"); ?></i>", "className": "text-center select-filter" },
      { "data": "idletime", "defaultContent": -1, "className": "text-center" },
      { "data": "first_seen", "render": fmt_datetime, "className": "text-center" },
      { "data": "last_seen", "render": fmt_datetime, "className": "text-center" },
      { "data": null, "defaultContent": 0, "render": fmt_rate, "className": "text-center" },
      { "data": "tx_bytes", "defaultContent": 0, "render": fmt_human, "className": "text-center" },
      { "data": "rx_bytes", "defaultContent": 0, "render": fmt_human, "className": "text-center" },
     ],
    "order": [[ 4, "desc" ]]
  });

  // Code		Ticket Kommentar	Hostname		Benutzer	Kommentar	Datenverkehr	Gültig bis
  // Hostname	Idle	Zuerst gesehen	Zuletzt gesehen	Bandbreite	Download	Upload

  /* render function */
  function fmt_rate(val, type, row) {
    var rate = 0;
    if (typeof row["bytes-r"] !== undefined) rate = row["bytes-r"];
    return fmt_human(rate * 8, type);
  }

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

  /* https://datatables.net/reference/api/ajax.reload() */
  $("#reload").click(function(){
    myTable.ajax.reload(fnInitComplete, false);
  });

});
</script>
