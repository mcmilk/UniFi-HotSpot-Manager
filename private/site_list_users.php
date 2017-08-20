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
    <h2><?php echo __("Userlist"); ?></h2>
    <p><?php echo __("List of all user, which are / were connected to our wireless network."); ?></p>
  </div>
  <div class="col-md-6 text-right">
    <h2></h2>
    <button type="button" id="reload" class="btn btn-primary"><i class="fa fa-refresh"></i> <?php echo __("Refresh now"); ?></button>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
  <table id="myTable" class="table table-hover table-responsive table-bordered" width="100%">
    <thead>
      <tr class="info">
        <th class="text-center"><?php echo __("OUI"); ?></th>
        <th class="text-center"><?php echo __("Hostname"); ?></th>
        <th class="text-center"><?php echo __("MAC address"); ?></th>
        <th class="text-center"><?php echo __("First Seen"); ?></th>
        <th class="text-center"><?php echo __("Last Seen"); ?></th>
        <th class="text-center"><?php echo __("Connection"); ?></th>
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
      "url": "?get_data=list_users",
      "type": "POST",
    },
    "columns": [
      { "data": "oui", "className": "text-center select-filter" },
      { "data": "hostname", "defaultContent": "<i>No hostname</i>", "className": "text-center" },
      { "data": "mac", "className": "text-center" },
      { "data": "first_seen", "render": fmt_datetime, "className": "text-center" },
      { "data": "last_seen", "render": fmt_datetime, "className": "text-center" },
      { "data": null, "render": fmt_list_users_connection }
     ],
    "order": [[ 4, "desc" ]]
  });

/**
 * [blocked] => 1
 * [is_guest] => 1
 * [is_wired] => 
 */
function fmt_list_users_connection(val, type, row) {
  var icons = "";
  if (typeof row.is_guest !== 'undefined') {
    if (row.is_guest == 1) icons += '<i class="fa fa-wifi"> ';
  }

  if (typeof row.is_wired !== 'undefined') {
    if (row.is_wired == 1) icons += '<i class="fa fa-plug"> ';
  }

  if (typeof row.blocked !== 'undefined') {
    if (row.blocked == 1) icons += '<span class="fa-stack fa-lg"><i class="fa fa-user fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x"></i></span>';
  }
  return icons;
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
