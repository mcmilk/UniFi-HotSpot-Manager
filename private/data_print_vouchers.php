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
 * create vouchers via pdf export
 */

$print_ids = explode(',', $_POST['PrintIDs']);

// create pdf
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetTitle("HotSpot Manager");
$pdf->SetSubject($headline);
$pdf->SetAuthor("HotSpot Manager, <?php echo __("Username"); ?> " . $username);
$pdf->SetCreator("HotSpot Manager, (C) 2017 Tino Reichardt");

$note_len = strlen($note_prefix);
$list = unifi_cmd("stat_voucher");
foreach ($list as $entry) {
  // check, if ticket is owned
  if (strncmp($entry->note, $note_prefix, $note_len) != 0) continue;

  $id = $entry->_id;
  $code  = fmt_wificode($entry->code);
  $duration  = fmt_duration($entry->duration * 60);
  $note  = substr($entry->note, strlen($note_prefix));

  if (isset($entry->qos_usage_quota)) {
    $limit = $entry->qos_usage_quota . " MB";
  }

  // if in the printing list, add it to pdf file
  if (in_array($id, $print_ids)) {
    add_ticket($pdf, $headline, $code, $duration, $limit, $note, "", "");
  }
}

// send pdf file to client
$pdf->Output('tickets.pdf', 'D');
ob_end_flush();
?>
