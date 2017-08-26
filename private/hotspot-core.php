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
 * Library core functions that are needed for the HotSpot system.
 */

if (!defined('HOTSPOT')) { exit; }

/**
 * some debug logging to logfile
 */
function log_msg($msg) {
  global $datadir;
  static $rv = TRUE;

  $logfile = "$datadir/unifi.log";
  if (!is_writeable($logfile)) return;
  if (!$logfile) return;
  if ($rv == FALSE) return;

  // when writing fails, we will not try again later
  $t = date("[Y-m-d H:i:s] ");
  $rv = file_put_contents($logfile, $t . $msg . "\n", FILE_APPEND);
  return;
}

/**
 * HeaderDie() - die with some special HTTP response code
 */
function HeaderDie($msg) {
  header($msg);
  die;
}

/**
 * http_204() - return request okay response and die
 */
function http_204() {
  HeaderDie("HTTP/1.0 204 Yes! My Lord!");
}

?>
