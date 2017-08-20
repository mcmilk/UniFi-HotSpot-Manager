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

/**
 * session.php
 * - create a session
 * - show login form and verify users
 * - login to unifi controller
 * - check what has to be included, created, deleted ...
 */

/* init session */
session_start();

/**
 * send it gzipped!
 * - saves a lot bandwith: 4175 bytes, instead of 86708...
 */
ob_start("ob_gzhandler");
header('Content-Encoding: gzip');

/* forget old session data, when requested */
if (isset($_GET['logout'])) {
  if (isset($_SESSION['unificookie'])) {
    /* logout from unifi controller, when user has some cookie */
    $unifidata = new UnifiApi($controller_user, $controller_password, $controller_url, $controller_siteid);
    $unifidata->logout();
  }
  session_destroy();
  $_SESSION = array();
  header('Location: ./');
  exit;
}

/**
 * theme switching (auth not needed)
 */
if (isset($_GET['theme'])) {
  $theme = $_GET['theme'];
  $_SESSION['theme'] = $theme;
}

if (isset($_SESSION['theme'])) {
  $theme = $_SESSION['theme'];
}

if ($theme === 'bootstrap') {
  $css_url = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css';
} else {
  $css_url = 'https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/' . trim($theme) . '/bootstrap.min.css';
}

/**
 * setup i18n variables
 * 1) object $i18n
 * 2) array $languages
 */
$i18n = null;
$languages = array();
$_SESSION['lang'] = i18n_init();

/**
 * setup $action variable
 */
if (isset($_GET['action'])) {
  $action = $_GET['action'];
} else if (isset($_POST['action'])) {
  $action = $_POST['action'];
} else if (isset($_SESSION['action'])) {
  $action = $_SESSION['action'];
} else {
  /* stat_voucher ist erstmal default */
  $action = "stat_voucher";
}
$_SESSION['action'] = $action;

/**
 * global variables
 */
$is_user = false;
$is_admin = false;
$username = "";
$headline = "";
$note_prefix = "";

/**
 * check POST data (login form)
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["loginform"]) && isset($_POST["username"]) && isset($_POST["password"])) {
  hotspot_login($_POST["username"], $_POST["password"]);
  $info = "";
  if (isset($_SERVER['REMOTE_ADDR'])) $info .= " ip=" . $_SERVER['REMOTE_ADDR'];
  if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) $info .= " encodings=[" . $_SERVER['HTTP_ACCEPT_ENCODING'] . "]";
  if (isset($_SERVER['HTTP_USER_AGENT'])) $info .= " browser=[" . $_SERVER['HTTP_USER_AGENT'] . "]";
  log_msg("LOGIN for user=" . $_SESSION['username'] . $info);
  $info = "";
}

if (isset($_SESSION['is_user'])) { $is_user = $_SESSION['is_user']; }
if (isset($_SESSION['is_admin'])) { $is_admin = $_SESSION['is_admin']; }
if (isset($_SESSION['username'])) { $username = $_SESSION['username']; }
if (isset($_SESSION['headline'])) { $headline = $_SESSION['headline']; }
if (isset($_SESSION['note_prefix'])) { $note_prefix = $_SESSION['note_prefix']; }
$prefix_len = strlen($note_prefix);

if ($is_user) {
  $unifidata = unifi_login();
  $includefile = "site_stat_voucher.php";
} else {
  $includefile = "form_login.php";
}

require_once("unifi_cache.php");

/**
 * check POST data (misc forms)
 */
if ($is_user) {

  if (isset($_GET["get_form"])) {
    switch ($_GET["get_form"]) {
    case "useradd":
      include("form_useradd.php");
      break;
    case "userpass":
      include("form_userpass.php");
      break;
    case "langadd":
      include("form_langadd.php");
      break;
    case "about":
      include("form_about.php");
      break;
    }
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* action: create voucher */
    if ($action === "stat_voucher" && isset($_POST["count"]) && isset($_POST["duration"]) && isset($_POST["dmulti"]) && isset($_POST["limit"]) && isset($_POST["note"])) {
      include("data_create_vouchers.php");
    }

    /* action: print voucher */
    if ($action === "stat_voucher" && isset($_POST["PrintIDs"]) && strlen($_POST["PrintIDs"])) {
      include("data_print_vouchers.php");
      exit;
    }

    /* action: delete voucher */
    if ($action === "stat_voucher" && isset($_POST["DeleteIDs"]) && strlen($_POST["DeleteIDs"])) {
      include("data_delete_vouchers.php");
    }

    /* action: usermgnt */
    if ($action === "usermgnt" && isset($_POST["DeleteIDs"]) && strlen($_POST["DeleteIDs"])) {
      include("data_delete_vouchers.php");
    }

    if (isset($_GET["get_data"])) {
      include("site_get_data.php");
      exit;
    }
  }

  switch ($action) {

  /* menu1: vouchers */
  case 'create_voucher':
    $includefile = "site_create_voucher.php";
    break;

  /* menu2: guests */
  case 'list_guests':
    $includefile = "site_list_guests.php";
    break;
  case 'list_clients':
    $includefile = "site_list_clients.php";
    break;
  case 'list_guest_aps':
    $todo = "list_guest_aps";
    $includefile = "site_list_rogueaps.php";
    break;

  /* menu3: statistic */
  case 'list_rogueaps':
    $todo = "list_rogueaps";
    $includefile = "site_list_rogueaps.php";
    break;
  }

  /* menu4: admin stuff */
  if ($is_admin) {
    switch ($action) {
    case 'list_rogueaps':
      $includefile = "site_list_rogueaps.php";
      break;
    case 'list_users':
      $includefile = "site_list_users.php";
      break;
    case 'usermgnt':
      $includefile = "site_usermgnt.php";
      break;
    case 'translator':
      $includefile = "site_translator.php";
      break;
    case 'languages':
      $includefile = "site_languages.php";
      break;
    }
  }
}

/**
 * debugging
echo "<pre>";
echo "<hr>server\n";
var_dump($_SERVER);
echo "</pre>";


echo "<hr>server\n";
var_dump($_SERVER);
echo "<hr>get\n";
var_dump($_GET);
echo "<pre>";
echo "<hr>session\n";
var_dump($_SESSION);
echo "<hr>post\n";
var_dump($_POST);
echo "</pre>";
 */

?>
