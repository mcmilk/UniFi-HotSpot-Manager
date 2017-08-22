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
 * HotSpot language management
 */

if (!defined('HOTSPOT')) { exit; }

?>

<form data-toggle="validator" method="POST" action="">

<div class="row">
  <div class="col-xs-12 col-md-6">
    <h2><?php echo __("Language Management"); ?></h2>
    <p><?php echo __("You can define here new languages for the HotSpot Manager."); ?></p>
  </div>
  <div class="col-xs-12 col-md-6 text-right">
    <h2></h2>
    <button type="button" id="langadd" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __("Add language"); ?></button>
    <button type="button" id="reload" class="btn btn-primary"><i class="fa fa-refresh"></i> <?php echo __("Reload Now"); ?></button>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
  <table id="myTable" class="table table-hover table-responsive table-bordered" width="100%">
    <thead>
      <tr class="info">
        <th class="text-center"><?php echo __("Language ID"); ?></th>
        <th class="text-center"><?php echo __("Name in menu"); ?></th>
        <th class="text-center"><?php echo __("Browser language"); ?></th>
        <th class="text-center"><?php echo __("Action"); ?></th>
      </tr>
    </thead>
    <tfoot>
      <tr class="info">
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
      "url": "?get_data=i18n_langlist",
      "type": "POST"
    },
    "columns": [
      { "data": "id", "className": "text-center", "render": render_id },
      { "data": "name",   "className": "text-center", "render": render_name },
      { "data": "browser", "className": "text-center", "render": render_browser },
      { "data": null, "className": "text-center", "render": render_action, "orderable": false }
    ]
  });

  function local_edit(val, type, row, name) {
    if (type === 'display') {
      var html = '<a href="#" class="edit-user" data-type="text" data-name="'+name+'" data-value="'+val+'" data-pk="'+row.id+'">'+val+'</a>';
      return html;
    }
    return val;
  }

  function render_id(val, type, row) {
    return local_edit(val, type, row, "id");
  }

  function render_name(val, type, row) {
    return local_edit(val, type, row, "name");
  }

  function render_browser(val, type, row) {
    return local_edit(val, type, row, "browser");
  }

  function render_action(val, type, row) {
    if (type === 'display') {
      var font = "";
      var html = "";

      font = '<label class="btn btn-danger btn-sm"><i class="fa fa-minus"></i> <?php echo __("Delete language"); ?></label>';
      html += '<a href="#" class="action" data-action="langdel" data-name="delete" data-pk="'+row.id+'">'+font+'</a> ';
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
      url: '?get_data=i18n_langmod'
    });

    // catch the font awesome buttons
    $('.action').off("click").on("click", function(){
      var btn = $(this);
      var pk = btn.attr("data-pk");
      var action = btn.attr("data-action");

      if (action === "langdel") {
        BootstrapDialog.confirm({
          draggable: true,
          title: "<?php echo __("Delete language"); ?>",
          message: "<?php echo __("Warning, do you really want to delete this translation?"); ?>",
          btnCancelLabel: "<?php echo __("Cancel"); ?>",
          btnOKLabel: "<?php echo __("Delete language"); ?>",
          btnOKClass: 'btn-danger',
          callback: function(result) {
            if (result) {
              $.ajax({
               url: "?get_data=i18n_langmod",
               method: "POST",
               data: { "name":"delete", "pk":pk, "value":"nix ;)" },
              }).done(function(data){
                $("#reload").click();
              });
            }
          }
        });
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

  $("#langadd").click(function(){
    langaddDialog.open();
  });

  /* https://datatables.net/reference/api/ajax.reload() */
  $("#reload").click(function(){
    myTable.ajax.reload(fnInitComplete, false);
  });

  var langaddDialog = new BootstrapDialog({
    title: "<?php echo __("Add language"); ?>",
    message: $('<div></div>').load('?get_form=langadd'),
    onshown: function(){
      $("form").validator('update');
    },
    buttons: [{
      label: "<?php echo __("Cancel"); ?>",
      action: function(me) { me.close(); }
    }, {
      label: '<i class="fa fa-plus"></i> <?php echo __("Add language"); ?>',
      cssClass: 'btn-primary',
      action: function(me) {
        var id      = $("input[name=id]").val();
        var name    = $("input[name=name]").val();
        var browser = $("input[name=browser]").val();
        $.ajax({
         url: "?get_data=i18n_langadd",
         method: "POST",
         data: { "id":id, "name":name, "browser":browser },
        }).done(function(data){
          me.close();
          $("#reload").click();
        });
      }
    }]
  });

});

</script>
