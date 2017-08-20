<?php

/**
 * UniFi HotSpot Manager
 *
 * Copyright (c) 2017 Tino Reichardt
 * All rights reserved.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (!defined('HOTSPOT')) { exit; }

/* switch to private directory */
chdir($PATH_PRIVATE);

/* include unifi and pdf classes */
require_once("class.unifi.php");
require_once("class.pdf.php");

/* gettext */
require_once("gettext/autoloader.php");

/* config + libs */
require_once("config.php");
require_once("hotspot-core.php");
require_once("hotspot-i18n.php");

/* misc functions */
require_once("functions.php");

/* userdb functions */
require_once("userdb.php");

/* check user auth */
require_once("session.php");

/* show site */
require_once("site.php");

?>
