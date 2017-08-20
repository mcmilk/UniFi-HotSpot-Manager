<?php

/**
 * Copyright (c) 2017 Tino Reichardt
 * All rights reserved.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (!defined('HOTSPOT')) { exit; }

/**
 * delete vouchers
 */

$delete_ids = explode(',', $_POST['DeleteIDs']);

$note_len = strlen($note_prefix);
$list = $unifidata->stat_voucher();
foreach ($list as $entry) {
  // check, if we have access to that ticket (note_prefix)
  if (strncmp($entry->note, $note_prefix, $note_len) != 0) continue;

  $id = $entry->_id;
  if (in_array($id, $delete_ids)) {
    $time_start = microtime(true);
    $unifidata->revoke_voucher($id);
    $time_needed = number_format(microtime(true) - $time_start, 4);
    log_msg("time=$time_needed | revoke_voucher($id), user=$username [$note_prefix]");
  }
}
?>
