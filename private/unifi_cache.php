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
 * A bit of caching with files for the responses of the controller.
 */

if (!defined('HOTSPOT')) { exit; }

/* read response from server, no cache here */
function unifi_read($command, $p1 = null, $p2 = null, $p3 = null, $p4 = null, $p5 = null, $p6 = null) {
  global $unifidata;

  $data = "";
  switch ($command) {
  case "list_clients":
    // list_clients($client_mac = null)
    $data = $unifidata->list_clients($p1);
    break;
  case "list_guests":
    // list_guests($within = 8760)
    if (empty($p1)) { $p1 = 8760; }
    $data = $unifidata->list_guests($p1);
    break;
  case "list_users":
    // list_users()
    $data = $unifidata->list_users();
    break;
  case "list_devices":
    // list_devices($device_mac = null
    $data = $unifidata->list_devices($p1);
    break;
  case "list_rogueaps":
    // list_rogueaps($within = '24')
    if (empty($p1)) { $p1 = 24; }
    $data = $unifidata->list_rogueaps($p1);
    break;
  case "stat_voucher":
    // stat_voucher($create_time = null)
    $data = $unifidata->stat_voucher($p1);
    break;
  case "stat_auths":
    // stat_auths($start = null, $end = null)
    $data = $unifidata->stat_auths($p1, $p2);
    break;
  case "stat_sessions":
    // stat_sessions($start = null, $end = null, $mac = null)
    $data = $unifidata->stat_sessions($p1, $p2, $p3);
    break;
  case "stat_allusers":
    // stat_allusers($historyhours = 8760)
    if (empty($p1)) { $p1 = 8760; }
    $data = $unifidata->stat_allusers($p1);
    break;
  }

  // this is a error fatal
  if (empty($data)) {
    log_msg("Fatal Error: $command has no resonse!");
    HeaderDie("HTTP/1.0 404 Unifi not Found :/");
  }

  return $data;
}

/* remove cache for some command */
function unifi_uncache($command) {
  global $cachedir;
  $filename = "$cachedir/$command.dump";

  if (is_file($filename)) {
    unlink($filename);
  }
}

/* read cached data */
function unifi_cmd($command, $seconds = 15, $p1 = null, $p2 = null, $p3 = null, $p4 = null, $p5 = null, $p6 = null) {
  global $cachedir;
  $filename = "$cachedir/$command.dump";

  if (is_file($filename)) {
    $ts_cache = filemtime($filename);
    $ts_now   = time();
    if ($ts_cache + $seconds > $ts_now) {
      return unserialize(file_get_contents($filename));
    }
  }

  $resp = unifi_read($command, $p1, $p2, $p3, $p4, $p5, $p6);
  file_put_contents($filename, serialize($resp));

  // extra infos, XXX, debugging
  $filename2 = "$cachedir/$command.txt";
  file_put_contents($filename2, print_r($resp, true));

  return $resp;
}

?>
