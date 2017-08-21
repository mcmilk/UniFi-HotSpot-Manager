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
 * HotSpot i18n Management
 */

if (!defined('HOTSPOT')) { exit; }

?>

<form data-toggle="validator" method="POST" action="">

<div class="row">
  <div class="col-xs-12 col-md-6">
    <h2><?php echo __("Translation Management"); ?></h2>
    <p><?php echo __("If you want, you can help translate the entire HotSpot Manager into your native language."); ?></p>
  </div>

  <div class="col-xs-12 col-md-6 text-right">
    <h2></h2>

<div class="dropdown">
    <button type="button" id="reload" class="btn btn-primary"><i class="fa fa-refresh"></i> <?php echo __("Reload Now"); ?></button>
  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?php echo __("Target language"); ?>
  <span class="caret"></span></button>
  <ul class="dropdown-menu dropdown-menu-right">
      <?php i18n_menu_destlang(); ?>
  </ul>
</div>

  </div>
</div>

<div class="row">
  <div class="col-md-12">
  <table id="myTable" class="table table-hover table-responsive table-bordered" width="100%">
    <thead>
      <tr class="info">
        <th class="text-center"><?php echo __("References"); ?></th>
        <th class="text-center"><?php echo __("Source"); ?></th>
        <th class="text-center"><?php echo __("Target Language"); i18n_table_destlang(); ?></th>
      </tr>
    </thead>
    <tfoot>
      <tr class="info">
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
      "url": "?get_data=i18n_tlist",
      "type": "POST"
    },
    "columns": [
      { "data": "references", "width": "2%", "className": "text-center input-filter" },
      { "data": "source",     "width": "49%", "className": "text-left input-filter" },
      { "data": "destlang",   "width": "49%", "className": "text-left input-filter", "render": render_destlang }
    ],
    "order": [[ 0, "desc" ]]
  });

  function render_destlang(val, type, row) {
    if (type === 'display') {
      var html = '<a href="#" class="edit-user" data-type="text" data-name="destlang" data-value="'+val+'" data-pk="'+row.source+'">'+val+'</a>';
      return html;
    }
    return val;
  }

  function CheckActions() {
    // catch the editing fields
    var font = "<?php echo __("empty"); ?>";
    $.fn.editable.defaults.emptytext = font;
    $.fn.editable.defaults.mode = 'inline';
    $('.edit-user').editable({
      url: '?get_data=i18n_tmod'
    });
  }

  function fnInitComplete(row, data, index) {

    $('#myTable tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="<?php echo __("Search"); ?> '+title+'" />' );
    });

    myTable.columns('.input-filter').every(function() {
        var that = this;
        $('input', this.footer() ).on( 'keyup change', function(){
            if (that.search() !== this.value) {
                that
                    .search(this.value)
                    .draw();
            }
        });
    });

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

  /* https://datatables.net/reference/api/ajax.reload() */
  $("#reload").click(function(){
    myTable.ajax.reload(fnInitComplete, false);
  });
});

</script>
