<?php

/**
 * UniFi HotSpot Manager
 *
 * Copyright (c) 2017 Tino Reichardt
 * All rights reserved.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Display and manage unused vouchers.
 */

if (!defined('HOTSPOT')) { exit; }
?>

<form method="POST" action="">

<input type="text" name="PrintIDs" id="idPrint" value="" style="display:none;" />
<input type="text" name="DeleteIDs" id="idDelete" value="" style="display:none;" />

<div class="row">
  <div class="col-xs-12 col-md-6">
    <h2><?php echo __("Available vouchers"); ?></h2>
    <p><?php echo __("Overview of all vouchers that have not been activated at the moment."); ?></p>
  </div>
  <div class="col-xs-12 col-md-6 text-right">
    <h2></h2>
    <button type="submit" id="print"  class="btn btn-primary"><i class="fa fa-print"></i> <?php echo __("Print selection"); ?></button>
    <button type="submit" id="delete" class="btn btn-primary"><i class="fa fa-trash"></i> <?php echo __("Delete selection"); ?></button>
    <button type="button" id="reload" class="btn btn-primary"><i class="fa fa-refresh"></i> <?php echo __("Reload Now"); ?></button>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
  <table id="myTable" class="table table-hover table-responsive table-bordered" width="100%">
    <thead>
      <tr class="info">
        <th class="text-center"><?php echo __("Code"); ?></th>
        <th class="text-center"><?php echo __("Voucher comment"); ?></th>
        <th class="text-center"><?php echo __("Created on"); ?></th>
        <th class="text-center"><?php echo __("Validity"); ?></th>
        <th class="text-center"><?php echo __("Limit"); ?></th>
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
      "url": "?get_data=stat_voucher",
      "type": "POST",
    },
    "columns": [
      { "data": "code", "render": fmt_wificode, "className": "text-center input-filter" },
      { "data": "note", "className": "select-filter" },
      { "data": "create_time", "render": fmt_datetime, "className": "text-center select-filter" },
      { "data": "duration", "render": fmt_duration, "className": "text-center select-filter" },
      { "data": "qos_usage_quota", "render": fmt_human_mb, "className": "text-center select-filter", "defaultContent": "0" }
    ],
    "order": [[ 2, "asc" ]]
  });

  /* add select fields under each column */
  function fnInitComplete(settings, json) {
    add_table_search();
  }

 /**
  * printing button
  * - done via hidden input + form with action=post
  */
  $("#print").click(function(){
    /* get id's we want to print */
    var ids = [];
    myTable.rows({filter: 'applied'}).every(function() {
      ids.push(this.data()._id);
    });
    /* store to value field of idPrint */
    $("#idDelete").val("");
    $("#idPrint").val(ids);
  });

 /**
  * printing button
  * - done via hidden input + form with action=post
  * - should be done via ajax ... will be faster i think, cause we need to make a lot single calls...
  * - we could inform then the user about the state ...
  * - will required single row deletion from time to time ... on ajax success callback...
  */
  $("#delete").click(function(){
    var ids = [];
    myTable.rows({filter: 'applied'}).every(function() {
      ids.push(this.data()._id);
    });
    $("#idPrint").val("");
    $("#idDelete").val(ids);

    BootstrapDialog.show({
      title:    "<?php echo __("Working. Please wait."); ?>",
      message:  '<p><i class="fa fa-spinner fa-spin fa-5x"></i>',
    });
  });

  /* https://datatables.net/reference/api/ajax.reload() */
  $("#reload").click(function(){
    myTable.ajax.reload(fnInitComplete, false);
  });
});

</script>
