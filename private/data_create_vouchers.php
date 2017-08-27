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
 * create vouchers, todo: check for correct input
 */
$count    = get_postparam("count", "1");
$duration = get_postparam("duration", "1");
$dmulti   = get_postparam("dmulti", "1");
$note     = get_postparam("note", null);
$up       = get_postparam("up", null);
$down     = get_postparam("down", null);
$limit    = get_postparam("limit", null);

/**
 * public function create_voucher($minutes, $count = 1, $quota = '1', $note = null, $up = null, $down = null, $MBytes = null)
 *
 * Create voucher(s)
 * -----------------
 * returns an array of voucher codes (without the dash "-" in the middle) by calling the stat_voucher method
 * required parameter <minutes> = minutes the voucher is valid after activation
 * optional parameter <count>   = number of vouchers to create, default value is 1
 * optional parameter <quota>   = single-use or multi-use vouchers, string value '0' is for multi-use, '1' is for single-use,
 *                                "n" is for multi-use n times
 * optional parameter <note>    = note text to add to voucher when printing
 * optional parameter <up>      = upload speed limit in kbps
 * optional parameter <down>    = download speed limit in kbps
 * optional parameter <MBytes>  = data transfer limit in MB
 */

$duration *= $dmulti;
$unifidata->create_voucher($duration, $count, 1, $note_prefix . $note, $up, $down, $limit);

?>
