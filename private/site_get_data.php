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
 * ?get_data=function
 */
$func = $_GET["get_data"];
$data = array();
switch ($func) {

// list online clients
case "list_online":
  $hours = 1;
  $guests = unifi_cmd("list_guests", $cachetime, $hours);
  $online = unifi_cmd("list_clients");
  $users = unifi_cmd("list_users");
  foreach ($guests as $guest) {
    if (!isset($guest->name)) continue;
    if (!isset($guest->user_id)) continue;
    if (!isset($guest->expired)) continue;
    if ($guest->expired) continue;
    // remove entries, with other prefix
    if (!is_prefix($guest->name)) continue;
    foreach ($users as $u) {
      if ($u->_id != $guest->user_id) continue;
      foreach ($online as $o) {
        // remove ubi-ck device
        if (!isset($o->idletime)) continue;
        if (!isset($o->guest_id)) continue;
        if ($o->guest_id != $guest->{'_id'}) continue;
        $new = $guest;
        $new->idletime = $o->idletime;
        $new->bandwidth = $o->{'bytes-r'} * 8;
        $new->username = "";
        $new->usernote = "";
        $new->blocked = "";
        if (isset($u->name)) $new->username = $u->name;
        if (isset($u->note)) $new->usernote = $u->note;
        if (isset($u->blocked)) $new->blocked = $u->blocked;
        $data[] = $new;
      }
    }
  }
  break;

// no expired entries
case "list_guests_expired":
  // fall through
case "list_guests":
  /* list_guests($within = 8760) */
  $hours = 24 * 7;
  $guests = unifi_cmd("list_guests", $cachetime, $hours);
  $users = unifi_cmd("list_users");
  foreach ($guests as $guest) {
    if (!isset($guest->name)) continue;
    if (!isset($guest->user_id)) continue;
    if (!isset($guest->expired)) continue;
    if ($func === "list_guests" && $guest->expired) continue;
    // remove entries, with other prefix
    //if (!is_prefix($guest->name)) continue;
    $guest->username = "";
    $guest->usernote = "";
    $guest->blocked = "";
    foreach ($users as $u) {
      if ($u->_id === $guest->user_id) {
        if (isset($u->name)) $guest->username = $u->name;
        if (isset($u->note)) $guest->usernote = $u->note;
        if (isset($u->blocked)) $guest->blocked = $u->blocked;
        $data[] = $guest;
      }
    }
  }
  break;

case "list_rogueaps":
  // 1a) via list_users
  $users = unifi_cmd("list_users");
  $macs = array();
  foreach ($users as $user) {
    $macs[] = $user->mac;
  }

  // 1b) list_guests (mit prefix!)
  $hours = 24 * 2;
  $guests = unifi_cmd("list_guests", $cachetime, $hours);
  foreach ($guests as $guest) {
    if (!isset($guest->name)) continue;
    if (!isset($guest->expired)) continue;
    if ($guest->expired) continue;
    // remove entries, with other prefix
    if (!is_prefix($guest->name)) continue;
    $macs[] = $guest->mac;
  }

  $aps = unifi_cmd("list_rogueaps", $cachetime, 48);
  foreach ($aps as $ap) {
    if (!$ap->bssid) continue;
    if (in_array($ap->bssid, $macs)) {
      $data[] = $ap;
    }
  }
  break;

case "stat_voucher":
  $vouchers = unifi_cmd("stat_voucher");
  foreach ($vouchers as $voucher) {
    if (!is_prefix($voucher->note)) continue;
    $data[] = $voucher;
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
