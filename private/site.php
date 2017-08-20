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
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="A HotSpot Management solution for Ubiquiti Hardware ;)">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Tino Reichardt">

  <title>UniFi HotSpot Manager</title>

  <!--  jquery + bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo $css_url; ?>">
  <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

  <!--  jquery dataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css"/>
  <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>

  <!--  jquery dataTables responsive -->
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css"/>
  <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>

  <!--  bootstrap editable -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

  <!-- bootstrap form validator -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script>

  <!-- bootstrap dialog -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

  <!-- moment datetime (en) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

  <!--  own stuff -->
  <script src="js/hotspot.js"></script>

  <!-- custom CSS styling -->
  <style>
    body { padding-top: 60px; }
    .navbar.navbar-default { padding-right: 2em; }
    .table > tbody > tr > td { vertical-align: middle; white-space: nowrap; }
    .tooltip-inner { max-width: none; white-space: nowrap; text-align: left; }
  </style>
  <!-- /custom CSS styling -->
</head>

<body>

<!-- top navbar -->
<nav id="navbar" class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#navbar-main">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand hidden-sm hidden-md" href=""><i class="fa fa-id-card fa-fw fa-lg" aria-hidden="true"></i> HotSpot Manager</a>
    </div>
    <div id="navbar-main" class="collapse navbar-collapse">
      <?php if ($is_user) { ?>
      <ul class="nav navbar-nav navbar-left">
        <li id="user-menu" class="dropdown">
            <a id="user-menu" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <?php echo __("Vouchers"); ?><span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo __("Voucher menu"); ?></li>
                <li><a href="?action=stat_voucher"><?php echo __("Overview"); ?></a></li>
                <li role="separator" class="divider"></li>
                <li><a href="?action=create_voucher"><?php echo __("Create vouchers"); ?></a></li>
            </ul>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-left">
      </ul>
      <ul class="nav navbar-nav navbar-left">
        <li id="user-menu" class="dropdown">
            <a id="user-menu" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <?php echo __("Guests"); ?><span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo __("Activated vouchers"); ?></li>
                <li><a href="?action=list_guests"><?php echo __("Overview"); ?></a></li>
                <li role="separator" class="divider"></li>
                <li><a href="?action=list_clients"><?php echo __("Guests online"); ?></a></li>
                <li><a href="?action=list_guest_aps"><?php echo __("Guests APs"); ?></a></li>
            </ul>
        </li>
      </ul>
      <?php } ?>

      <?php if ($is_admin) { ?>
      <ul class="nav navbar-nav navbar-left">
        <li id="user-menu" class="dropdown">
            <a id="user-menu" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <?php echo __("Admin"); ?><span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo __("Admin menu"); ?></li>
                <li><a href="?action=list_users"><?php echo __("Wireless user"); ?></a></li>
                <li><a href="?action=list_rogueaps"><?php echo __("Other wireless networks"); ?></a></li>
                <li role="separator" class="divider"></li>
                <li><a href="?action=usermgnt"><?php echo __("User management"); ?></a></li>
            </ul>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-left">
        <li id="user-menu" class="dropdown">
            <a id="user-menu" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <?php echo __("Translation"); ?><span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li class="dropdown-header"><?php echo __("Translation Tools"); ?></li>
                <li><a href="?action=languages"><?php echo __("Languages"); ?></a></li>
                <li><a href="?action=translator"><?php echo __("Translations"); ?></a></li>
            </ul>
        </li>
      </ul>
      <?php } ?>
      <?php if ($is_user) { ?>
      <ul class="nav navbar-nav navbar-right">
        <li id="logout" data-toggle="tooltip" data-placement="top"><a href="?logout=true"><i class="fa fa-sign-out"></i> <?php echo __("Logout") . " [" . $username . "]"; ?></a></li>
      </ul>
      <?php } ?>
      <ul class="nav navbar-nav navbar-right">
        <li id="about" data-toggle="tooltip" data-placement="top"><a href="#"><i class="fa fa-info-circle"></i> <?php echo __("About"); ?></a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li id="user-menu" class="dropdown">
          <a id="user-menu" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo __("Themes"); ?><span class="caret"></span></a>
          <ul class="dropdown-menu">
            <?php navbar_themes(); ?>
          </ul>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li id="user-menu" class="dropdown">
          <a id="user-menu" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-globe"></i> <?php echo __("Language"); ?><span class="caret"></span></a>
          <ul class="dropdown-menu">
          <?php i18n_menu_lang(); ?>
          </ul>
        </li>
      </ul>
    </div><!-- /.nav-collapse -->
  </div><!-- /.container-fluid -->
</nav><!-- /top navbar -->

<div class="container-fluid">
<?php include($includefile); ?>
</div><!-- /.container-fluid -->

<script>
  $("#about").click(function(){
    BootstrapDialog.show({
      title:    'HotSpot Manager Version 0.4a (2017-08-20)',
      message:  $('<div></div>').load('?get_form=about')
    });
  });
</script>

</body>
</html>