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
    <p><button type="button" id="reload" class="btn btn-primary"><i class="fa fa-refresh"></i> <?php echo __("Reload Now"); ?></button>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
  <table id="myTable" class="table table-hover table-responsive table-bordered" width="100%">
    <thead>
      <tr class="info">
        <th class="text-center"><?php echo __("Code"); ?></th>
        <th class="text-center"></th>
        <th class="text-center"><?php echo __("Voucher comment"); ?></th>
        <th class="text-center"><?php echo __("Hostname"); ?></th>
        <th class="text-center"></th>
        <th class="text-center"><?php echo __("Bandwidth"); ?></th>
        <th class="text-center"><?php echo __("Idle"); ?></th>
        <th class="text-center"><?php echo __("Username"); ?></th>
        <th class="text-center"><?php echo __("Comment"); ?></th>
        <th class="text-center"><?php echo __("Traffic"); ?></th>
        <th class="text-center"><?php echo __("Valid until"); ?></th>
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
      "url": "?get_data=list_online",
      "type": "POST",
    },
    "columns": [
      { "data": "voucher_code", "className": "text-center", "render": local_wificode, "orderable": false },
      { "data": null, "render": local_banguest, "orderable": false },
      { "data": "name", "className": "text-center select-filter" },
      { "data": "hostname", "className": "text-center select-filter", "render": local_hostname, "defaultContent": "<i>No hostname</i>" },
      { "data": null, "render": local_banuser, "orderable": false },
      { "data": "bandwidth", "className": "text-center", "defaultContent": "0", "render": fmt_human },
      { "data": "idletime", "className": "text-center", "defaultContent": "0" },
      { "data": "username", "className": "text-center select-filter", "defaultContent": "", "render": local_username },
      { "data": "usernote", "className": "text-center select-filter", "defaultContent": "", "render": local_usernote },
      { "data": "bytes", "className": "text-center", "defaultContent": "0", "render": fmt_human },
      { "data": "end", "className": "text-center", "render": fmt_datetime }
    ],
    "order": [[ 5, "desc" ]]
  });

  function local_wificode(val, type, row) {
    if (!val) return "-";
    if (type === 'display') {
      var code = fmt_wificode(val, type, row);
      var ttip = "";
      ttip += "<p><b><?php echo __("Validity"); ?></b>";
      ttip += "<br>seit: " + fmt_datetime(row.start, type);
      ttip += "<br>bis:  " + fmt_datetime(row.end, type);
      if (row.qos_overwrite) {
        ttip += "<p><b><?php echo __("Limits"); ?></b>";
        if (row.qos_usage_quota > 0) ttip += "<br><?php echo __("Limit"); ?>: " + fmt_human_mb(row.qos_usage_quota, type);
        if (row.qos_rate_max_up > 0) ttip += "<br><?php echo __("Upload"); ?>: " + fmt_human(row.qos_rate_max_up, type);
        if (row.qos_rate_max_down > 0) ttip += "<br><?php echo __("Download"); ?>: " + fmt_human(row.qos_rate_max_down, type);
      }
      var html = '<a href="#" rel="tooltip" data-html="true" title="'+ttip+'">'+code+'</a>';
      return html;
    }
    return val;
  }

  function local_hostname(val, type, row) {
    if (type === 'display') {
      var ttip = "";
      ttip += "<p><b><?php echo __("Client information"); ?></b>";
      ttip += "<br><?php echo __("IP address"); ?>: " + row.ip;
      ttip += "<br><?php echo __("MAC address"); ?>: " + row.mac;
      ttip += "<br><?php echo __("Browser"); ?>: " + row.user_agent;
      ttip += "<p><b><?php echo __("Data Traffic"); ?></b>";
      ttip += "<br><?php echo __("Data Sent"); ?>: " + fmt_human(row.tx_bytes, type);
      ttip += "<br><?php echo __("Data Received"); ?>: " + fmt_human(row.rx_bytes, type);
      ttip += "<br><?php echo __("Data Total"); ?>: " + fmt_human(row.bytes, type);
      if (row.radio) {
        ttip += "<p><b><?php echo __("Wireless information"); ?></b>";
        ttip += "<br><?php echo __("Last access point"); ?>: " + row.ap_mac;
        ttip += "<br><?php echo __("Channel"); ?>: " + row.channel;
        ttip += "<br><?php echo __("Radio"); ?>: " + row.radio;
        ttip += "<br><?php echo __("Roaming"); ?>: " + row.roam_count;
      }
      var html = '<a href="#" rel="tooltip" data-html="true" title="'+ttip+'">'+val+'</a> ';
      return html;
    }
    return val;
  }

  function local_banguest(val, type, row) {
    if (type === 'display') {
      var html = "";
      if (row.expired != 1) {
        var font = '<label class="btn btn-default btn-xs"><span class="fa-stack"><i class="fa fa-wifi fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span></label>';
        html = ' <a href="#" rel="tooltip" class="action" data-action="unauthorize_guest" data-mac='+row.mac+' data-html="true" title="<?php echo __("Discard voucher"); ?>">'+font+'</a>';
      }
      return html;
    }
    return val;
  }

  function local_banuser(val, type, row) {
    if (type === 'display') {
      var html = "";
      var font = "";
      var text = "";

      if (row.blocked == 1) {
        font = '<label class="btn btn-default btn-xs"><span class="fa-stack"><i class="fa fa-user fa-stack-2x"></i><i class="fa fa-check fa-stack-2x text-success"></i></span></label>';
        text = "<?php echo __("Enable user"); ?>";
        html = '<a href="#" rel="tooltip" class="action" data-action="unblock_sta" data-mac='+row.mac+' data-html="true" title="'+text+'">'+font+'</a> ';
      } else {
        font = '<label class="btn btn-default btn-xs"><span class="fa-stack"><i class="fa fa-user fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span></label>';
        text = "<?php echo __("Block user"); ?>";
        html = '<a href="#" rel="tooltip" class="action" data-action="block_sta" data-mac='+row.mac+' data-html="true" title="'+text+'">'+font+'</a> ';
      }
      text = "<?php echo __("Reset user device"); ?>"
      font = '<label class="btn btn-default btn-xs"><span class="fa-stack"><i class="fa fa-user fa-stack-1x"></i><i class="fa fa-repeat fa-stack-2x text-danger"></i></span></label>';
      html += '<a href="#" rel="tooltip" class="action" data-action="reconnect_sta" data-mac='+row.mac+' data-html="true" title="'+text+'">'+font+'</a> ';
      return html;
    }
    return val;
  }

  function local_username(val, type, row) {
    if (type === 'display') {
      var html = '<a href="#" class="edit-name" data-value="'+val+'" data-type="text" data-pk="'+row.user_id+'">'+val+'</a>';
      return html;
    }
    return val;
  }

  function local_usernote(val, type, row) {
    if (type === 'display') {
      var html = '<a href="#" class="edit-note" data-value="'+val+'" data-type="textarea" data-pk="'+row.user_id+'">'+val+'</a>';
      return html;
    }
    return val;
  }

  function CheckActions() {
    //log("CheckActions()");
    var font = '<label class="btn btn-default btn-sm"><i class="fa fa-pencil"></i></span></label>';

    // catch the editing fields
    $.fn.editable.defaults.emptytext = font;
    $.fn.editable.defaults.mode = 'popup';
    $('.edit-name').editable({
      url: '?get_data=' + "set_sta_name"
    });
    $('.edit-note').editable({
      url: '?get_data=' + "set_sta_note"
    });

    // catch the font awesome buttons
    $('.action').off("click").on("click", function(){
      var btn = $(this);
      var mac = btn.attr("data-mac");
      var action = btn.attr("data-action");
      $.ajax({
       url: "?get_data=" + action,
       method: "POST",
       data: { "mac": mac },
      }).done(function(data){
        if (action == 'unauthorize_guest') btn.remove();
        if (action == 'block_sta' || action == 'unblock_sta') $("#reload").click();
      });
    });
  }

  /* add select fields under each column */
  function fnInitComplete(settings, json) {
    add_table_search();

    myTable.on('draw', function(e, datatable, columns) {
      CheckActions();
    });
    myTable.on('responsive-display', function(e, datatable, columns) {
      CheckActions();
    });
    CheckActions();
  }

  /* https://datatables.net/reference/api/ajax.reload() */
  $("#reload").click(function(){
    myTable.ajax.reload(fnInitComplete, false);
  });

  // render html tooltips
  $('body').tooltip({
    selector: "a[rel=tooltip]"
  })

});
</script>
