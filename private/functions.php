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
 * Misc functions that are needed for the HotSpot system.
 */

if (!defined('HOTSPOT')) { exit; }

/**
 * format numbers human readable (14567 -> 14.22 kiB)
 */
function fmt_human($bytes, $dec = 2) {
  $size = array('B', 'kiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$dec}f ", $bytes / pow(1024, $factor)) . @$size[$factor];
}

/**
 * format seconds to german units
 * eg: 10 Tage 21 Stunden 2 Minuten
 */
function fmt_duration($seconds) {
  $years = intval($seconds/60/60/24/365);
  $seconds -= $years * 60 * 60 * 24 * 365;
  $weeks = intval($seconds/60/60/24/7);
  $seconds -= $weeks * 60 * 60 * 24 * 7;
  $days = intval($seconds/60/60/24);
  $seconds -= $days * 60 * 60 * 24;
  $hours = intval($seconds/60/60);
  $seconds -= $hours * 60 * 60;
  $minutes = intval($seconds/60);
  $seconds -= $minutes * 60;
  $r = "";
  if ($years)   { if ($years == 1)   { $r .= "1 ".__("year")   ." "; } else { $r .= "$years "  .__("years")   ." "; } }
  if ($weeks)   { if ($weeks == 1)   { $r .= "1 ".__("week")   ." "; } else { $r .= "$weeks "  .__("weeks")   ." "; } }
  if ($days)    { if ($days == 1)    { $r .= "1 ".__("day")    ." "; } else { $r .= "$days "   .__("days")    ." "; } }
  if ($hours)   { if ($hours == 1)   { $r .= "1 ".__("hour")   ." "; } else { $r .= "$hours "  .__("hours")   ." "; } }
  if ($minutes) { if ($minutes == 1) { $r .= "1 ".__("minute") ." "; } else { $r .= "$minutes ".__("minutes") ." "; } }
  if ($seconds) { if ($seconds == 1) { $r .= "1 ".__("second") ." "; } else { $r .= "$seconds ".__("seconds") ." "; } }
  return $r;
}

function get_duration() {
  $x1 = array(__("minutes"), __("hours"), __("days"), __("weeks"), __("years"));
  $x2 = array(1, 60, 60*24, 60*24*7, 60*24*365);
  for ($i = 0; isset($x1[$i]); $i++) {
    $v1 = $x1[$i];
    $v2 = $x2[$i];
    echo "<option value=\"$v2\">$v1</option>";
  }
}

/**
 * - check some GET parameter, if not set, take default
 */
function get_getparam($name, $default) {
  if (isset($_GET["$name"])) return $_GET["$name"];
  return $default;
}

/**
 * - check some POST parameter, if not set, take default
 */
function get_postparam($name, $default) {
  if (isset($_POST["$name"])) return $_POST["$name"];
  return $default;
}

/**
 * wifi code: from "1234567890" to "12345-67890"
 */
function fmt_wificode($code) {
  return substr($code, 0, 5) . "-" . substr($code, 5, 5);
}

/**
 * validate_input()
 * - check for bad stuff in input variables
 */
function validate_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  $data = str_replace(":", "", $data);
  return $data;
}

/**
 * unifi_login()
 */
function unifi_login() {
  global $controller_user, $controller_password, $controller_url, $controller_siteid;

  $unifidata = new UnifiApi($controller_user, $controller_password, $controller_url, $controller_siteid);
  if ($unifidata->login() == false) {
    HeaderDie("HTTP/1.0 504 No connection to UniFi controller :/");
  } else {
    /* remember cookie in session */
    $_SESSION['unificookie'] = $unifidata->getcookie();
  }

  return $unifidata;
}

/*
 * DIN-A4 notes:
 * - 210 Millimeter breit -> 192/4  -> 48
 * - 297 Millimeter hoch  -> 280/10 -> 10
 *
 * Write(float h , string txt [, mixed link])
 * Text(float x , float y , string txt)
 * SetFont(string family [, string style] [, float size])
 * Rect(float x , float y , float w , float h [, string style])
 * Image(string file , float x , float y [, float w] [, float h] [, string type] [, mixed link])
 * Cell(float w [, float h] [, string txt] [, mixed border] [, integer ln] [, string align] [, integer fill] [, mixed link])
 *
 * /TR 2017-07-21
 */
function add_ticket($pdf, $headline, $code, $time = "", $limit = "", $comment = "", $download = "", $upload = "") {
 static $id_x = 0; /* 0..3 */
 static $id_y = 0; /* 0..9 */

 /* new page */
 if ($id_x == 0 && $id_y == 0) {
   $pdf->AddPage();
   $pdf->SetAutoPageBreak(false);
   $pdf->SetLineWidth(0.1);
   $pdf->SetDrawColor(0, 0, 0);
 }

 /* real x/y offset */
 $x = $id_x * 48; /* 0..3 */
 $y = $id_y * 28; /* 0..9 */

 $x1 = 9 + $x;
 $y1 = 9 + $y;
 $font = 'Arial';

 // Rahmen drum herum
 $pdf->Rect($x1, $y1, 48, 28, "LTRB");

 // 74x74 png mit WLAN SSID usw!
 // qrencode -s2 -lL -t png -o qr-code.png 'WIFI:S:SSID;T:WPA;P:pass;;'
 if (is_file('qr-code.png')) {
   $pdf->Image('qr-code.png', $x1+1, $y1+5);
 }

 // Font für Headline
 if (strlen($headline)) {
   $pdf->SetFont($font, '', 13);
   $pdf->SetXY($x1+1, $y1+4);
   $pdf->CellFitScale(46, 0, $headline, 0, 0, "C");
 }

 // Font für Code
 if (strlen($code)) {
   $pdf->SetFont($font, 'B', 12);
   $pdf->Text($x1+20, $y1+10, $code);
 }

 // Font für den Rest
 $pdf->SetFont($font, '', 8);

 if (strlen($download)) {
   $pdf->Text($x1+20, $y1+14, __("Download"));
   $pdf->SetXY($x1+34, $y1+13);
   $pdf->CellFitScale(46, 0, $download);
 }

 if (strlen($upload)) {
   $pdf->Text($x1+20, $y1+17, __("Upload"));
   $pdf->SetXY($x1+34, $y1+16);
   $pdf->CellFitScale(19, 0, $upload);
 }

 if (strlen($time)) {
   $pdf->Text($x1+20, $y1+20, __("Time"));
   $pdf->SetXY($x1+27, $y1+19);
   $pdf->CellFitScale(19, 0, $time);
 }

 if (strlen($limit)) {
   $pdf->Text($x1+20, $y1+23, __("Limit"));
   $pdf->SetXY($x1+27, $y1+22);
   $pdf->CellFitScale(19, 0, $limit);
 }

 if (strlen($comment)) {
   $pdf->SetFont($font, '', 9);
   $pdf->SetXY($x1+1, $y1+26);
   $pdf->CellFitScale(46, 0, $comment, 0, 0, "C");
 }

 /* next cell for next ticket */
 if ($id_x == 3) {
   $id_x = 0;
   if ($id_y == 9) {
     $id_y = 0;
    } else {
      $id_y += 1;
    }
 } else {
   $id_x += 1;
 }
}

/**
 * build <li>'s for the navbar menu
 */
function navbar_themes() {
  global $theme;
  $themes = array("Bootstrap", "Cerulean", "Cosmo", "Cyborg", "Darkly",
  "Flatly", "Journal", "Lumen", "Paper", "Readable", "Sandstone", "Simplex",
  "Slate", "Solar", "Spacelab", "Superhero", "United", "Yeti");

  foreach($themes as $T) {
    $t = strtolower($T);
    if ($theme === $t) {
      echo '<li class="active"><a href="?theme=' . $t . '">'. $T . '</a></li>';
    } else {
      echo '<li><a href="?theme=' . $t . '">'. $T . '</a></li>';
    }
  }
}

function dataTablesDefaults() {
  $sEmptyTable     = __("No data available");
  $sInfo           = __("_START_ to _END_ of _TOTAL_ entries");
  $sInfoEmpty      = __("0 to 0 of 0 entries");
  $sInfoFiltered   = __("filtered of _MAX_ entries");
  $sLengthMenu     = __("_MENU_ entries per page");
  $sLoadingRecords = __("Downloading");
  $sProcessing     = __("Working. Please wait.");
  $sSearch         = __("Search");
  $sZeroRecords    = __("No entries available.");
  $sFirst          = __("First");
  $sPrevious       = __("Prev");
  $sNext           = __("Next");
  $sLast           = __("Last");
  $sAll            = __("All");

  $text = <<<EOT
    "initComplete": fnInitComplete,
    "language": {
      "sEmptyTable":     "$sEmptyTable",
      "sInfo":           "$sInfo",
      "sInfoEmpty":      "$sInfoEmpty",
      "sInfoFiltered":   "$sInfoFiltered",
      "sInfoPostFix":    "",
      "sInfoThousands":  ".",
      "sLengthMenu":     "$sLengthMenu",
      "sLoadingRecords": "$sLoadingRecords",
      "sProcessing":     "$sProcessing",
      "sSearch":         "$sSearch",
      "sZeroRecords":    "$sZeroRecords",
      "oPaginate": {
        "sFirst":        "$sFirst",
        "sPrevious":     "$sPrevious",
        "sNext":         "$sNext",
        "sLast":         "$sLast"
      }
    },
    "processing": true,
    "responsive": true,
    "stateSave": true,
    "lengthMenu": [ [ 8, 16, 40, -1], [ 8, 16, 40, "$sAll" ] ],
EOT;
  return $text;
}

?>
