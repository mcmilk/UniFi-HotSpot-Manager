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
 * UserDB functions that are needed for the HotSpot system.
 */

if (!defined('HOTSPOT')) { exit; }

/**
 * username: the name of some user
 * password: hash of user password (PASSWORD_BCRYPT)
 * prefix:   voucher prefix, used for filtering
 * options:  a=admin u=user d=deactivated
 * headline: headline on the vouchers (different grouping)
 */

/**
 * password database - init
 */
function userdb_init() {
  global $datadir;

  $file = "$datadir/userdb.json";
  if (!is_file($file)) {
    $pwtemp = random_str(10);
    $pwhash = password_hash($pwtemp, PASSWORD_BCRYPT);
    $a = [];
    $a[] = array("username" => "admin", "password" => "$pwhash", "prefix" => "", "options" => "a", "headline" => "pass = [$pwtemp]");
    $r = file_put_contents($file, json_encode($a, JSON_PRETTY_PRINT));
    if ($r == false) trigger_error('hotspot: writing $filename has failed!');
  }

  // read database
  $userdb = json_decode(file_get_contents($file));
  if (!$userdb) return HeaderDie("HTTP/1.0 400 userdb not found!");
  return $userdb;
}

/**
 * flush password database
 */
function userdb_flush(&$array) {
  global $datadir;

  $file = "$datadir/userdb.json";
  file_put_contents($file, json_encode($array, JSON_PRETTY_PRINT));
  http_204();
}

/**
 * hotspot_login()
 * - check for valid username and password
 * - setup session (is_user, is_admin, ...)
 */
function hotspot_login($username, $password, $use_saml) {

  // defaults
  $is_admin = false;
  $is_user = false;
  $headline = "";
  $note_prefix = "";

  $userdb = userdb_init();
  foreach($userdb as $user) {
    if ($user->username !== $username) continue;

    // skip password check with SAML
    if (empty($use_saml) || ($use_saml === false)) {
      // return without setting session, when password is wrong   
      if (!password_verify($password, $user->password)) return;
    }

    // username ok + password ok
    if ($user->options === "a") {
      $is_user = true;
      $is_admin = true;
    } else if ($user->options === "u") {
      $is_user = true;
    }

    // setup session
    $_SESSION['is_user'] = $is_user;
    $_SESSION['is_admin'] = $is_admin;
    $_SESSION['username'] = $user->username;
    $_SESSION['headline'] = $user->headline;
    $_SESSION['note_prefix'] = $user->prefix;
    return;
  }
}

/**
 * hotspot_userlist() - return userdb array
 * - array of: username, prefix, options, headline
 */
function hotspot_userlist() {
  $r = [];
  $userdb = userdb_init();
  foreach($userdb as $user) {
    $e = array('username' => $user->username, 'prefix' => $user->prefix, 'options' => $user->options, 'headline' => $user->headline);
    $r[] = $e;
  }

  return $r;
}

/**
 * hotspot_usermod()
 * - update settings in userdb.txt file
 */
function hotspot_usermod() {

  /**
   * POST(name)  -> username|password|prefix|options|headline|delete
   * POST(value) -> value
   * POST(pk)    -> username
   *
   * sample, setting admin password to 123: (which is bad!)
   * POST(name)  -> password
   * POST(value) -> 123
   * POST(pk)    -> admin
   */

  if (!isset($_POST['name']) || !isset($_POST['value']) || !isset($_POST['pk']))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $pk    = get_postparam("pk", "");
  $name  = get_postparam("name", "");
  $value = get_postparam("value", "");

  if (!strlen($pk) || !strlen($name))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $r = [];
  $userdb = userdb_init();
  foreach($userdb as $user) {
    if ($user->username !== $pk) {
      $r[] = $user;
      continue;
    }

    // username | password | prefix | options | headline | delete
    switch ($name) {
    case "username":
      $user->username = $value;
      $r[] = $user;
      break;
    case "password":
      $user->password = password_hash($value, PASSWORD_BCRYPT);
      $r[] = $user;
      break;
    case "prefix":
      $user->prefix = $value;
      $r[] = $user;
      break;
    case "options":
      $user->options = $value;
      $r[] = $user;
      break;
    case "headline":
      $user->headline = $value;
      $r[] = $user;
      break;
    }

    // if some value of current user has changed, update the session
    if ($_SESSION['username'] == $pk) {
      if ($user->options === "a") {
        $is_user = true;
        $is_admin = true;
      } else if ($user->options === "u") {
        $is_user = true;
      }

      $_SESSION['is_user'] = $is_user;
      $_SESSION['is_admin'] = $is_admin;
      $_SESSION['username'] = $user->username;
      $_SESSION['headline'] = $user->headline;
      $_SESSION['note_prefix'] = $user->prefix;
      log_msg("anpassen der session!");
    }
  }

  userdb_flush($r);
}

/**
 * hotspot_useradd()
 * - add user to userdb.txt file
 */
function hotspot_useradd() {

  /**
   * POST(username)  -> username
   * POST(password)  -> password
   * POST(prefix)    -> prefix
   * POST(options)   -> options
   * POST(headline)  -> headline
   */

  if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['prefix']) || !isset($_POST['options']) || !isset($_POST['headline']))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $username = get_postparam("username", "");
  $password = get_postparam("password", "");
  $prefix   = get_postparam("prefix", "");
  $options  = get_postparam("options", "");
  $headline = get_postparam("headline", "");

  if (!strlen($username) || !strlen($password) || !strlen($options))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $r = [];
  $userdb = userdb_init();
  foreach($userdb as $user) {
    if ($user->username !== $username) {
      $r[] = $user;
      continue;
    }
  }

  // username | password | prefix | options | headline
  $hash = password_hash($password, PASSWORD_BCRYPT);
  $r[] = array('username' => $username, 'password' => $hash, 'prefix' => $prefix, 'options' => $options, 'headline' => $headline);

  userdb_flush($r);
}

?>
