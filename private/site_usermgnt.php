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
 * HotSpot User Management
 */

if (!defined('HOTSPOT')) { exit; }

?>

<form data-toggle="validator" method="POST" action="">

<div class="row">
  <div class="col-xs-12 col-md-6">
    <h2><?php echo __("User management"); ?></h2>
    <p><?php echo __("Lets you create, modify, or delete the Hotspot Manager users."); ?></p>
  </div>
  <div class="col-xs-12 col-md-6 text-right">
    <h2></h2>
    <button type="button" id="useradd" class="btn btn-primary"><i class="fa fa-user-plus"></i> <?php echo __("New user"); ?></button>
    <button type="button" id="reload" class="btn btn-primary"><i class="fa fa-refresh"></i> <?php echo __("Reload Now"); ?></button>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
  <table id="myTable" class="table table-hover table-responsive table-bordered" width="100%">
    <thead>
      <tr class="info">
        <th class="text-center"><?php echo __("Username"); ?></th>
        <th class="text-center"><?php echo __("Voucher prefix"); ?></th>
        <th class="text-center"><?php echo __("Permissions"); ?></th>
        <th class="text-center"><?php echo __("Voucher headline"); ?></th>
        <th class="text-center"><?php echo __("Actions"); ?></th>
      </tr>
    </thead>
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
      "url": "?get_data=hotspot_userlist",
      "type": "POST"
    },
    "columns": [
      { "data": "username", "className": "text-center", "render": local_username },
      { "data": "prefix",   "className": "text-center", "render": local_prefix },
      { "data": "options",  "className": "text-center", "defaultContent": "d", "render": local_options },
      { "data": "headline", "className": "text-center", "render": local_headline },
      { "data": null, "className": "text-center", "render": local_actions, "orderable": false }
    ]
  });

  function local_username(val, type, row) {
    if (type === 'display') {
      var html = '<a href="#" class="edit-user" data-name="username" data-value="'+val+'" data-type="text" data-pk="'+row.username+'">'+val+'</a>';
      return html;
    }
    return val;
  }

  function local_prefix(val, type, row) {
    if (type === 'display') {
      var html = '<a href="#" class="edit-user" data-name="prefix" data-value="'+val+'" data-type="text" data-pk="'+row.username+'">'+val+'</a>';
      return html;
    }
    return val;
  }

  function local_options(val, type, row) {
    if (type === 'display') {
      var option = "";
      switch (row.option) {
      case "d": option = "Deaktiviert";
      case "u": option = "Benutzer";
      case "a": option = "Administrator";
      }
      var html = '<a href="#" class="edit-options" data-name="options" data-value="'+val+'" data-type="select" data-pk="'+row.username+'">'+option+'</a>';
      return html;
    }
    return val;
  }

  function local_headline(val, type, row) {
    if (type === 'display') {
      var html = '<a href="#" class="edit-user" data-name="headline" data-value="'+val+'" data-type="text" data-pk="'+row.username+'">'+val+'</a>';
      return html;
    }
    return val;
  }

  function local_actions(val, type, row) {
    if (type === 'display') {
      var font = "";
      var html = "";

      font = '<label class="btn btn-primary btn-sm"><i class="fa fa-key"></i> '+"<?php echo __("Change password"); ?>"+'</label>';
      html += '<a href="#" class="action" data-action="userpass" data-html="true" data-pk="'+row.username+'">'+font+'</a> ';

      font = '<label class="btn btn-danger btn-sm"><i class="fa fa-user-times"></i> '+"<?php echo __("Delete user"); ?>"+'</label>';
      html += '<a href="#" class="action" data-action="userdel" data-html="true" data-pk="'+row.username+'">'+font+'</a> ';
      return html;
    }
    return val;
  }

  function CheckActions() {
    var font = '<label class="btn btn-default btn-sm"><i class="fa fa-pencil"></i></span></label>';

    // catch the editing fields
    $.fn.editable.defaults.emptytext = font;
    $.fn.editable.defaults.mode = 'inline';

    $('.edit-user').editable({
      url: '?get_data=hotspot_usermod'
    });

    $('.edit-options').editable({
      url: '?get_data=hotspot_usermod',
      source: [
       { value: "a", text: "<?php echo __("Administrator"); ?>"},
       { value: "u", text: "<?php echo __("Username"); ?>"},
       { value: "d", text: "<?php echo __("Disabled"); ?>"}
      ]
    });

    $('.action').off("click").on("click", function(){
      var btn = $(this);
      var action = btn.attr("data-action");
      var pk = btn.attr("data-pk");

      if (action === "userpass") {
        userpassDialog.realize();
        var m = userpassDialog.getMessage();
        m.find('input').first().val(pk);
        userpassDialog.setMessage(m);
        BootstrapDialog.closeAll();
        userpassDialog.open();
        return;
      }

      if (action === "userdel") {
        userdelDialog.realize();
        var m = userdelDialog.getMessage();
        m.find('input').first().val(pk);
        userdelDialog.setMessage(m);
        userdelDialog.open();
        return;
      }
    });
  }


  function fnInitComplete(row, data, index) {
    myTable.on('draw', function(e, datatable, columns) {
      CheckActions();
      return;
    });
    myTable.on('responsive-display', function(e, datatable, columns) {
      CheckActions();
      return;
    });
    CheckActions();
  }

  $("#useradd").click(function(){
    useraddDialog.open();
  });

  /* https://datatables.net/reference/api/ajax.reload() */
  $("#reload").click(function(){
    myTable.ajax.reload(fnInitComplete, false);
  });

  var userpassDialog = new BootstrapDialog({
    title: "<?php echo __("Change password"); ?>",
    message: $('<div></div>').load('?get_form=userpass'),
    onshown: function(me){
      $("form").validator('update');
      me.getButton('btn-1').disable();
      $("form").on('validate.bs.validator', function (e) {
        if ($("form").find('.has-success').length == 2) {
          me.getButton('btn-1').enable();
        } else {
          me.getButton('btn-1').disable();
        }
      });
    },
    buttons: [{
      label: "<?php echo __("Cancel"); ?>",
      action: function(me) {
       me.close();
      }
    }, {
      id: 'btn-1',
      label: '<i class="fa fa-key"></i> '+"<?php echo __("Change password"); ?>",
      cssClass: 'btn-primary',
      action: function(me) {
        var username = $("input[name=username]").val();
        var password = $("input[name=password]").val();
        $.ajax({
         url: "?get_data=hotspot_usermod",
         method: "POST",
         data: { "name":"password", "pk":username, "value":password },
        }).done(function(data){
          me.close();
          $("#reload").click();
        });
      }
    }]
  });

  var userdelDialog = new BootstrapDialog({
    title: "<?php echo __("Delete user"); ?>",
    message: $('<form><div><?php echo __("Are you sure you want to delete the user?"); ?></div><input type="hidden" name="username" value=""></form>'),
    buttons: [{
      label: "<?php echo __("Cancel"); ?>",
      action: function(me) { me.close(); }
    }, {
      label: '<i class="fa fa-user-times"></i> '+"<?php echo __("Delete user"); ?>",
      cssClass: 'btn-danger',
      action: function(me) {
        var username = $("input[name=username]").val();
        $.ajax({
         url: "?get_data=hotspot_userdel",
         method: "POST",
         data: { "username":username },
        }).done(function(data){
          me.close();
          $("#reload").click();
        });
      }
    }]
  });

  var useraddDialog = new BootstrapDialog({
    title: "<?php echo __("Add user"); ?>",
    message: $('<div></div>').load('?get_form=useradd'),
    onshown: function(me){
      $("form").validator('update');
      me.getButton('btn-1').disable();
      $("form").on('validate.bs.validator', function (e) {
        if ($("form").find('.has-success').length == 5) {
          me.getButton('btn-1').enable();
        } else {
          me.getButton('btn-1').disable();
        }
      });
    },
    buttons: [{
      label: "<?php echo __("Cancel"); ?>",
      action: function(me) { me.close(); }
    }, {
      id: 'btn-1',
      label: '<i class="fa fa-user-plus"></i> '+"<?php echo __("Add user"); ?>",
      cssClass: 'btn-primary',
      action: function(me) {
        var username = $("input[name=username]").val();
        var password = $("input[name=password]").val();
        var prefix   = $("input[name=prefix]").val();
        var options  = $("select[name=options]").val();
        var headline = $("input[name=headline]").val();
        $.ajax({
         url: "?get_data=hotspot_useradd",
         method: "POST",
         data: { "username":username, "password":password, "prefix":prefix, "options":options, "headline":headline },
        }).done(function(data){
          me.close();
          $("#reload").click();
        });
      }
    }]
  });

});

</script>
