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
 * - we have currently no locking on the file
 * - xxx, use json db
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
 * hotspot_login()
 * - check for valid username and password
 * - setup session parameters (is_user, is_admin, ...)
 */
function hotspot_login($user, $pass) {
  global $PATH_PRIVATE;

  $userdb = $PATH_PRIVATE . "/userdb.txt";
  if (!file_exists($userdb))
    return;

  $user = validate_input($user);
  $pass = validate_input($pass);

  $is_admin = false;
  $is_user = false;
  $headline = "";
  $note_prefix = "";

  $f = file($userdb);
  for ($i = 0; isset($f[$i]); $i++) {
    $e = $f[$i];
    if ($e[0] == '#') continue;

    $pwe = explode(":", $e);
    if (!isset($pwe[4])) continue;
    $username = validate_input($pwe[0]);
    $fhash    = validate_input($pwe[1]);
    $fprefix  = validate_input($pwe[2]);
    $options  = validate_input($pwe[3]);
    $headline = validate_input($pwe[4]);

    if ($user === $username) {
      if (password_verify($pass, $fhash)) {
        if ($options === "a") {
          $is_user = true;
          $is_admin = true;
        } else if ($options === "u") {
          $is_user = true;
        }
        /* valid user/pass found */
        $note_prefix = $fprefix;
        break;
      }
    }
  }

  // setup session variables
  $_SESSION['is_user'] = $is_user;
  $_SESSION['is_admin'] = $is_admin;
  $_SESSION['headline'] = $headline;
  $_SESSION['username'] = $username;
  $_SESSION['note_prefix'] = $note_prefix;
}

/**
 * hotspot_userlist()
 * - return array of valid usernames and options
 * - array of: username, prefix, rights, headline
 */
function hotspot_userlist() {
  global $PATH_PRIVATE;

  $userdb = $PATH_PRIVATE . "/userdb.txt";
  if (!file_exists($userdb))
    return;

  $f = file($userdb);
  $r = [];
  for ($i = 0; isset($f[$i]); $i++) {
    $e = $f[$i];
    if ($e[0] == '#') continue;

    $e = explode(":", $e);
    if (!isset($e[4])) continue;
    $e = array('username' => $e[0], 'prefix' => $e[2], 'options' => $e[3], 'headline' => $e[4]);
    $r[] = $e;
  }

  return $r;
}

/**
 * hotspot_usermod()
 * - update settings in userdb.txt file
 */
function hotspot_usermod() {
  global $PATH_PRIVATE;

  $userdb = $PATH_PRIVATE . "/userdb.txt";
  if (!file_exists($userdb))
    return HeaderDie("HTTP/1.0 400 DB not found!");

  /**
   * POST(name)  -> options
   * POST(value) -> u
   * POST(pk)    -> IV (username)
   */

  if (!isset($_POST['name']) || !isset($_POST['value']) || !isset($_POST['pk']))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $pk    = validate_input($_POST['pk']);
  $name  = validate_input($_POST['name']);
  $value = validate_input($_POST['value']);

  $f = file($userdb);
  $fh = fopen($userdb, "w");
  for ($i = 0; isset($f[$i]); $i++) {
    $e = $f[$i];
    if ($e[0] == '#') {
      fwrite($fh, $e);
      continue;
    }

    $pwe = explode(":", $e);
    if (!isset($pwe[4])) {
      fwrite($fh, $e);
      continue;
    }

    $username = validate_input($pwe[0]);
    $x = "";
    if ($username === $pk) {
      // username | password | prefix | options | headline
      switch ($name) {
      case "username":
        $x = $value . ":" . $pwe[1] . ":" . $pwe[2] . ":" . $pwe[3] . ":" . $pwe[4];
        break;
      case "password":
        $x = $pwe[0] . ":" . password_hash($value, PASSWORD_BCRYPT) . ":" . $pwe[2] . ":" . $pwe[3] . ":" . $pwe[4];
        break;
      case "prefix":
        $x = $pwe[0] . ":" . $pwe[1] . ":" . $value . ":" . $pwe[3] . ":" . $pwe[4];
        break;
      case "options":
        $x = $pwe[0] . ":" . $pwe[1] . ":" . $pwe[2] . ":" . $value . ":" . $pwe[4];
        break;
      case "headline":
        $x = $pwe[0] . ":" . $pwe[1] . ":" . $pwe[2] . ":" . $pwe[3] . ":" . $value . "\n";
        break;
      }
    }

    if (empty($x)) $x = $e;
    fwrite($fh, $x);
    //$line = trim($x);
    //log_msg("line=[$line]");
  }
  fclose($fh);
  http_204();
}

/**
 * hotspot_useradd()
 * - add user to userdb.txt file
 */
function hotspot_useradd() {
  global $PATH_PRIVATE;

  $userdb = $PATH_PRIVATE . "/userdb.txt";
  if (!file_exists($userdb))
    return HeaderDie("HTTP/1.0 400 DB not found!");

  /**
   * POST(username)  -> username
   * POST(password)  -> password
   * POST(prefix)    -> prefix
   * POST(options)   -> options
   * POST(headline)  -> headline
   */

  if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['prefix']) || !isset($_POST['options']) || !isset($_POST['headline']))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $username = validate_input($_POST['username']);
  $password = validate_input($_POST['password']);
  $prefix   = validate_input($_POST['prefix']);
  $options  = validate_input($_POST['options']);
  $headline = validate_input($_POST['headline']);
  $hash = password_hash($password, PASSWORD_BCRYPT);

  $f = file($userdb);
  $fh = fopen($userdb, "w");
  $found = 0;
  for ($i = 0; isset($f[$i]); $i++) {
    $e = $f[$i];
    if ($e[0] == '#') {
      fwrite($fh, $e);
      continue;
    }

    $pwe = explode(":", $e);
    if (!isset($pwe[4])) {
      fwrite($fh, $e);
      continue;
    }

    // username | password | prefix | options | headline
    $x = "";
    if ($username === $pwe[0]) {
      // replace old user with same name
      $x = $username . ":" . $hash . ":" . $prefix . ":" . $options . ":" . $headline . "\n";
      $found = 1;
    } else {
      $x = $e;
    }
    fwrite($fh, $x);
  }

  if ($found == 0) {
    // append new (unknown) user
    $x = $username . ":" . $hash . ":" . $prefix . ":" . $options . ":" . $headline . "\n";
    fwrite($fh, $x);
  }
  fclose($fh);
  http_204();
}

/**
 * hotspot_userdel()
 * - delete user from userdb.txt file
 */
function hotspot_userdel() {
  global $PATH_PRIVATE;

  $userdb = $PATH_PRIVATE . "/userdb.txt";
  if (!file_exists($userdb))
    return HeaderDie("HTTP/1.0 400 DB not found!");

  /**
   * POST(username)  -> username
   */

  if (!isset($_POST['username']))
    return HeaderDie("HTTP/1.0 400 incorrect POST values!");

  $username = validate_input($_POST['username']);

  $f = file($userdb);
  $fh = fopen($userdb, "w");
  for ($i = 0; isset($f[$i]); $i++) {
    $e = $f[$i];
    if ($e[0] == '#') {
      fwrite($fh, $e);
      continue;
    }

    $pwe = explode(":", $e);
    if (!isset($pwe[4])) {
      fwrite($fh, $e);
      continue;
    }

    // username | password | prefix | options | headline
    if ($username === $pwe[0]) continue;
    fwrite($fh, $e);
  }
  fclose($fh);
  http_204();
}

?>
