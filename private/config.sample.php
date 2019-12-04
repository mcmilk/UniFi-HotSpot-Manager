<?php

/**
 * Copyright (c) 2017 Tino Reichardt
 * All rights reserved.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

// the user name for access to the UniFi Controller
$controller_user     = 'username';

// the password for access to the UniFi Controller
$controller_password = 'password';

// full url to the UniFi Controller
$controller_url      = 'https://192.168.1.3:8443';

// the site name of the UniFi Controller
$controller_siteid   = 'default';

// time of inactivity in seconds, after which the PHP session cookie will be refreshed
$cookietimeout       = '300';

// default language to use (is used, when browser is not detected)
$language            = 'en';

// your default theme of choice, pick one from the list below:
// bootstrap, cerulean, cosmo, cyborg, darkly, flatly, journal, lumen, paper
// readable, sandstone, simplex, slate, spacelab, superhero, united, yeti
$theme               = 'bootstrap';

// directory, which must have read write permission to the http user or group
// - relative to $PATH_PRIVATE
$datadir             = "db";

// default caching time, for responses from the unifi controller, -1 disables caching
$cachetime           = 30;

// enable SimpleSAMLphp
// set to true to enable SAML support
$use_saml            = false;

?>
