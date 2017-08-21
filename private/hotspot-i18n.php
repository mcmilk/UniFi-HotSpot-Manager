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
 * I18n functions that are needed for the HotSpot system.
 */

if (!defined('HOTSPOT')) { exit; }

use Gettext\GettextTranslator;
use Gettext\Translations;
use Gettext\Translator;
use Gettext\Merge;

/**
 * check what browser has (rfc2616) _and_ what we have
 */
function i18n_getbrowser() {
  global $languages, $language;

  // ["HTTP_ACCEPT_LANGUAGE"]=> string(116) "de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4,fr;q=0.2,it;q=0.2,ru;q=0.2,pt;q=0.2,ja;q=0.2,es;q=0.2,gl;q=0.2,pl;q=0.2,fa;q=0.2"
  $browserlang = explode(',', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
  $result = array();
  foreach ($browserlang as $language) {
    $lang = explode(';q=', $language);
    $result[$lang[0]] = isset($lang[1]) ? floatval($lang[1]) : 1;
  }

  arsort($result);
  //print_r($result);
  foreach(array_keys($result) as $lang_user) {
    if (!is_string($lang_user) || strlen($lang_user) < 2) continue;
    $l1 = substr($lang_user, 0, 2);

    foreach($languages as $lang) {
      $browsertag = explode(',', strtolower($lang->browser));

      // 1) perfect matches
      foreach ($browsertag as $b) {
        if ($lang_user === $b) return $lang->id;
      }

      // 2) first two chars match?
      foreach ($browsertag as $b) {
        if (strlen($b) < 2) continue;
        $l2 = substr($b, 0, 2);
        if ($l1 === $l2) return $lang->id;
      }

    }
  }

  // default
  return $language;
}

/**
 * initialize i18n
 */
function i18n_init() {
  global $PATH_PRIVATE, $datadir, $i18ndir;
  global $languages, $language, $i18n;

  // create mini json language definition
  $file = "$datadir/languages.json";
  if (!is_file($file)) {
    $a = [];
    $a[] = array("id" => "ca", "name" => "Català",     "browser" => "ca");
    $a[] = array("id" => "cs", "name" => "Čeština",    "browser" => "cs");
    $a[] = array("id" => "de", "name" => "Deutsch",    "browser" => "de,de-de,de-at");
    $a[] = array("id" => "en", "name" => "English",    "browser" => "en,en-us,en-gb");
    $a[] = array("id" => "es", "name" => "Español",    "browser" => "es,es-es");
    $a[] = array("id" => "hr", "name" => "Hrvatski",   "browser" => "hr");
    $a[] = array("id" => "it", "name" => "Italiano",   "browser" => "it");
    $a[] = array("id" => "nl", "name" => "Nederlands", "browser" => "nl");
    $a[] = array("id" => "pl", "name" => "Polski",     "browser" => "pl");
    $a[] = array("id" => "pt", "name" => "Português",  "browser" => "pt,pt-pt");
    $a[] = array("id" => "ru", "name" => "Русский",    "browser" => "ru");
    $a[] = array("id" => "sv", "name" => "Svenska",    "browser" => "sv");
    $a[] = array("id" => "tr", "name" => "Türkçe",     "browser" => "tr");
    $a[] = array("id" => "zh-cn", "name" => "中文",    "browser" => "zh-cn");
    file_put_contents($file, json_encode($a));
  }

  // read our lang database
  $languages = json_decode(file_get_contents($file));
  i18n_update_json();

  // default from configuration
  $userlang = $language;

  if (isset($_GET['lang']) && is_string($_GET['lang'])) {
    $userlang = $_GET['lang'];
  } else if (isset($_SESSION['lang']) && is_string($_SESSION['lang'])) {
    $userlang = $_SESSION['lang'];
  } else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $userlang = i18n_getbrowser();
  }

  // can happen, if session has wrong lang set, or get is wrong
  $file = $userlang . '.json';
  if (!is_file("$i18ndir/$file")) {
    $userlang = $language;
  }

  $i18n = new Translator();
  $tl = Gettext\Translations::fromJsonFile("$i18ndir/$userlang" . '.json');
  $i18n->loadTranslations($tl);
  $i18n->register();

  if (isset($_GET['destlang']) && is_string($_GET['destlang'])) {
    $destlang = $_GET['destlang'];
  } else if (isset($_SESSION['destlang']) && is_string($_SESSION['destlang'])) {
    $destlang = $_SESSION['destlang'];
  } else {
    $destlang = $userlang;
  }
  $_SESSION['destlang'] = $destlang;

  return $userlang;
}

/**
 * update all json files, needed when some code changed
 */
function i18n_update_json() {
  global $PATH_PRIVATE, $languages, $i18ndir, $datadir;

  // get template from source code
  $files = glob('*.php');
  $tlc = Translations::fromPhpCodeFile($files);
  $tlc->toPoFile("$datadir/source.po");

  // write json for each defined language
  foreach($languages as $lang) {
    $sfile = "$datadir/source.po";
    $tlc = Translations::fromPoFile($sfile);

    $file = "$datadir/" . $lang->id . '.po';
    if (is_file($file)) {
      $tlx = Translations::fromPoFile($file);
      $tlc->mergeWith($tlx, Merge::REFERENCES_THEIRS);
      unlink($file);
    }

    $file = "$i18ndir/" . $lang->id . '.json';
    if (is_file($file)) {
      $tlx = Translations::fromJsonFile($file);
      $tlc->mergeWith($tlx, Merge::REFERENCES_OURS);
    }
    $tlc->toJsonFile($file);
  }
}

/**
 * build <li>'s for the destlang dropdown
 */
function i18n_menu_destlang() {
  global $languages, $i18ndir;

  foreach($languages as $lang) {
    $file = "$i18ndir/".$lang->id.".json";
    if (!is_file($file)) continue;

    $tls = Translations::fromJsonFile($file);
    $tl = $tls->getTranslations();
    $tlc = $tls->getTranslationsCount();
    $num = count($tls->getTranslations());

    if ($_SESSION['destlang'] === $lang->id) {
      echo '<li class="active"><a href="?destlang=' . $lang->id . '">'.$lang->name . ' ['.$tlc.'/'.$num.']</a></li>';
    } else {
      echo '<li><a href="?destlang=' . $lang->id . '">'.$lang->name . ' ('.$tlc.'/'.$num.')</a></li>';
    }
  }
}

function i18n_table_destlang() {
  global $languages;


  foreach($languages as $lang) {
    if ($_SESSION['destlang'] === $lang->id) {
      echo " [" . $lang->name . "]";
    }
  }
}

/**
 * build <li>'s for the navbar menu
 */
function i18n_menu_lang() {
  global $languages;

  foreach($languages as $lang) {
    if ($_SESSION['lang'] === $lang->id) {
      echo '<li class="active"><a href="?lang=' . $lang->id . '">'.$lang->name . '</a></li>';
    } else {
      echo '<li><a href="?lang=' . $lang->id . '">'.$lang->name . '</a></li>';
    }
  }
}

/**
 * return array with all defined languages
 */
function i18n_langlist() {
  global $languages;

  $r = [];
  foreach($languages as $lang) {
    $e = array("id" => $lang->id, "name" => $lang->name, "browser" => $lang->browser);
    $r[] = $e;
  }

  return $languages;
}

/**
 * update language in json database
 */
function i18n_langmod() {
  global $languages, $datadir, $i18ndir;

  /**
   * POST(name)  -> id|name|browser|delete
   * POST(value) -> value
   * POST(pk)    -> id
   *
   * POST(name)  -> browser
   * POST(value) -> de,de-de ...
   * POST(pk)    -> de
   */

  if (!isset($_POST['name']) || !isset($_POST['pk']) || !isset($_POST['value']))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $name  = $_POST['name'];
  $key   = $_POST['pk'];
  $value = $_POST['value'];

  $r = [];
  foreach ($languages as $lang) {
    if ($lang->id !== $key) {
      $r[] = array("id" => $lang->id, "name" => $lang->name, "browser" => $lang->browser);
      continue;
    }

    switch ($name) {
    case "id":
      $r[] = array("id" => $value, "name" => $lang->name, "browser" => $lang->browser);
      break;
    case "name":
      $r[] = array("id" => $lang->id, "name" => $value, "browser" => $lang->browser);
      break;
    case "browser":
      $r[] = array("id" => $lang->id, "name" => $lang->name, "browser" => $value);
      break;
    case "delete":
      $file = $lang->id . '.json';
      if (is_file("$i18ndir/$file")) {
        unlink("$i18ndir/$file");
      }
      break;
    }
  }

  file_put_contents("$datadir/languages.json", json_encode($r));
  http_204();
}

/**
 * add language to json database
 */
function i18n_langadd() {
  global $languages, $datadir;

  /**
   * POST(id)      -> id
   * POST(name)    -> name
   * POST(browser) -> browser
   */

  if (!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['browser']))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $id      = $_POST['id'];
  $name    = $_POST['name'];
  $browser = $_POST['browser'];

  $found = false;
  $r = [];
  foreach ($languages as $lang) {
    if ($lang->id === $id) {
      // replace old entry
      $r[] = array("id" => $id, "name" => $name, "browser" => $browser);
      $found = true;
    } else {
      $r[] = array("id" => $lang->id, "name" => $lang->name, "browser" => $lang->browser);
    }
  }

  // add new entry
  if (!$found) {
    $r[] = array("id" => $id, "name" => $name, "browser" => $browser);
  }

  file_put_contents("$datadir/languages.json", json_encode($r));
  http_204();
}

/**
 * return array with different languages for translating
 */
function i18n_tlist() {
  global $datadir, $i18ndir, $language;

  $tls = Translations::fromPoFile("$datadir/source.po");
  $tls_o = $tls->getOriginals();
  $tls_r = $tls->getReferences();

  if (!isset($_SESSION['destlang']))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $file = "$i18ndir/".$_SESSION['destlang'].".json";
  if (!is_file($file))
    HeaderDie("HTTP/1.0 400 incorrect SESSION!");

  $tls = Translations::fromJsonFile($file);
  $td = $tls->getTranslations();

  $r = [];
  for ($i = 0; isset($tls_o[$i]); $i++) {
    $r[] = array("source" => $tls_o[$i], "references" => count($tls_r[$i]), "destlang" => $td[$i]);
  }

  return $r;
}

/**
 * update translation in json database
 */
function i18n_tmod() {
  global $languages, $datadir, $i18ndir;

  /**
   * POST(name)  -> destlang (static word)
   * POST(value) -> newvalue
   * POST(pk)    -> original
   */

  if (!isset($_POST['name']) || !isset($_POST['pk']) || !isset($_POST['value']))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  if (!isset($_SESSION['destlang']))
    return HeaderDie("HTTP/1.0 400 incorrect SESSION values!");

  $file = "$i18ndir/".$_SESSION['destlang'].".json";
  if (!is_file($file))
    HeaderDie("HTTP/1.0 400 incorrect SESSION!");

  $key   = $_POST['pk'];
  $value = $_POST['value'];
  $tle = Translations::fromJsonFile($file);
  $te = $tle->find(null, $key);
  if ($te) {
    $te->setTranslation($value);
  }
  $tle->toJsonFile($file);
  http_204();
}

?>
