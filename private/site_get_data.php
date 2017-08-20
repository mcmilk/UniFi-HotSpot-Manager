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
 * This file generates JSON output for ajax requests.
 */

if (!defined('HOTSPOT')) { exit; }

/**
 * only users can get data
 * - and get_data must be set
 */
if (!$is_user) {
  echo "404 Data not found :/";
  exit;
}

/**
 * check for valid prefix access
 * - used for filtering valid access to voucher/device infos
 */
function is_prefix(&$prefix) {
  global $note_prefix, $prefix_len;
  if (strncmp($prefix, $note_prefix, $prefix_len) == 0) {
    /* remove prefix, add it to list */
    $prefix = substr($prefix, $prefix_len);
    return true;
  }

  return false;
}

$time_start = microtime(true);

/**
 * ?get_data=function&p1=parameter1&p2=x2&...
 */
$func = $_GET["get_data"];
$data = array();
switch ($func) {

// no expired entries
case "list_guests_expired":
  // fall through
case "list_guests":
  /* list_guests($within = 8760) */
  $guests = unifi_cmd("list_guests");
  $users = unifi_cmd("list_users");
  foreach ($guests as $entry) {
    if (!isset($entry->name)) continue;
    if (!isset($entry->user_id)) continue;
    if (!isset($entry->expired)) continue;
    if ($func === "list_guests" && $entry->expired) continue;
    // remove entries, with other prefix
    if (!is_prefix($entry->name)) continue;
    $entry->username = "";
    $entry->usernote = "";
    $entry->blocked = "";
    foreach ($users as $u) {
      if ($u->_id === $entry->user_id) {
        if (isset($u->name)) $entry->username = $u->name;
        if (isset($u->note)) $entry->usernote = $u->note;
        if (isset($u->blocked)) $entry->blocked = $u->blocked;
      }
    }
    $data[] = $entry;
  }
  break;

// list online clients
case "list_clients":
  // 1) list_guests
  $data = unifi_cmd("list_guests");
  $gids = array();
  foreach ($data as $entry) {
    if (!isset($entry->name)) continue;
    if (!isset($entry->expired)) continue;
    if ($entry->expired) continue;
    // remove entries, with other prefix
    if (!is_prefix($entry->name)) continue;
    $gids[] = $entry->_id;
  }
  // 2) list_guests
  $data = unifi_cmd("list_clients");
  $a = array();
  foreach ($data as $entry) {
    // remove ubi-ck device
    if (!isset($entry->idletime)) continue;
    // check if the guest_id has the prefix
    if (!isset($entry->guest_id)) continue;
    if (in_array($entry->guest_id, $gids)) {
      $a[] = $entry;
    }
  }
  $x = count($a);
  //log_msg("number of guests filtered: $x!");
  $data = $a;
  break;

case "list_guest_aps":
  // 1a) via list_users
  $users = unifi_cmd("list_users");
  $macs = array();
  foreach ($users as $entry) {
    $macs[] = $entry->mac;
  }

  // 1b) list_guests (mit prefix!)
  $guests = unifi_cmd("list_guests");
  foreach ($guests as $entry) {
    if (!isset($entry->name)) continue;
    if (!isset($entry->expired)) continue;
    if ($entry->expired) continue;
    // remove entries, with other prefix
    if (!is_prefix($entry->name)) continue;
    $macs[] = $entry->mac;
  }

  $aps = unifi_cmd("list_rogueaps", $cachetime, 48);
  foreach ($aps as $entry) {
    if (!$entry->bssid) continue;
    if (in_array($entry->bssid, $macs)) {
      $data[] = $entry;
    }
  }
  break;

case "list_rogueaps":
  /* list_rogueaps($within = '24') */
  $data = unifi_cmd("list_rogueaps", $cachetime, 48);
  break;

case "stat_voucher":
  $stat = unifi_cmd("stat_voucher");
  foreach ($stat as $entry) {
    if (!is_prefix($entry->note)) continue;
    $data[] = $entry;
  }
  break;

case "list_devices":
  if (!$is_admin) break;
  /*  list_devices($device_mac = null) */
  //$p1 = get_getparam("p1", null);
  $data = unifi_cmd("list_devices");
  break;

case "list_users":
  if (!$is_admin) break;
  $data = unifi_cmd("list_users");
  break;

// unauthorize_guest with mac (expire the voucher)
case "unauthorize_guest":
  // unauthorize_guest($mac)
  $mac = get_postparam("mac", "");
  if (empty($mac)) break;
  $data = $unifidata->unauthorize_guest($mac);
  unifi_uncache("list_guests");
  break;

// block client with mac
case "block_sta":
  // block_sta($mac)
  $mac = get_postparam("mac", "");
  if (empty($mac)) break;
  $data = $unifidata->block_sta($mac);
  unifi_uncache("list_users");
  break;

// unblock client with mac
case "unblock_sta":
  // unblock_sta($mac)
  $mac = get_postparam("mac", "");
  if (empty($mac)) break;
  $data = $unifidata->unblock_sta($mac);
  unifi_uncache("list_users");
  break;

// reconnect client with mac
case "reconnect_sta":
  $mac = get_postparam("mac", "");
  if (empty($mac)) break;
  // reconnect_sta($mac)
  $data = $unifidata->reconnect_sta($mac);
  unifi_uncache("list_users");
  break;

// set station name
case "set_sta_name":
  // set_sta_name($user_id, $name = null)
  $userid = get_postparam("pk", "");
  if (empty($userid)) break;
  $name = get_postparam("value", "");
  $data = $unifidata->set_sta_name($userid, $name);
  unifi_uncache("list_users");
  break;

// set station note
case "set_sta_note":
  // set_sta_note($user_id, $note = null)
  $userid = get_postparam("pk", "");
  if (empty($userid)) break;
  $note = get_postparam("value", "");
  $data = $unifidata->set_sta_note($userid, $note);
  unifi_uncache("list_users");
  break;

case "stat_sta_sessions_latest":
  // stat_sta_sessions_latest($mac, $limit = null)
  $mac = get_postparam("mac", "");
  if (empty($mac)) break;
  $data = $unifidata->stat_sta_sessions_latest($mac, 10);
  break;

// languages
case "i18n_langlist":
  if (!$is_admin) break;
  $data = i18n_langlist();
  break;

case "i18n_langmod":
  if (!$is_admin) break;
  $data = i18n_langmod();
  break;

case "i18n_langadd":
  if (!$is_admin) break;
  $data = i18n_langadd();
  break;

// translations
case "i18n_tlist":
  if (!$is_admin) break;
  $data = i18n_tlist();
  break;

case "i18n_tmod":
  if (!$is_admin) break;
  $data = i18n_tmod();
  break;

// userdb
case "hotspot_userlist":
  if (!$is_admin) break;
  $data = hotspot_userlist();
  break;

case "hotspot_usermod":
  if (!$is_admin) break;
  $data = hotspot_usermod();
  break;

case "hotspot_useradd":
  if (!$is_admin) break;
  $data = hotspot_useradd();
  break;

case "hotspot_userdel":
  if (!$is_admin) break;
  $data = hotspot_userdel();
  break;

default:
  log_msg("Invalid get_data() command: $func ?");
  exit;
}

$count = count($data);

// debugging
//$echo = json_encode(array('draw' => 1, 'recordsTotal' => $count, 'recordsFiltered' => $count, 'data' => $data));
//trigger_error($echo);
header( 'Content-Type: application/json; charset=UTF-8' );
echo json_encode(array('draw' => 1, 'recordsTotal' => $count, 'recordsFiltered' => $count, 'data' => $data));
ob_end_flush();

$time_needed = number_format(microtime(true) - $time_start, 4);
log_msg("time=$time_needed | $func()=$count, user=$username [filter=$note_prefix]");

?>
